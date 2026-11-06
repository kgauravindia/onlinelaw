<?php
require_once('includes/config.php');
require_once('includes/function.php');
require_once('includes/captcha.php');

$code_param = isset($_GET['code']) ? trim($_GET['code']) : '';
$q_param = isset($_GET['q']) ? trim($_GET['q']) : $code_param;
$level_param = isset($_GET['level']) ? strtoupper(trim($_GET['level'])) : '';
$section_param = isset($_GET['section']) ? strtoupper(trim($_GET['section'])) : '';
$division_param = isset($_GET['division']) ? trim($_GET['division']) : '';
$view_mode = isset($_GET['view']) && $_GET['view'] === 'table' ? 'table' : 'grid';
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 30;
$offset = ($page - 1) * $limit;

$has_search = (!empty($q_param) || !empty($level_param) || !empty($section_param) || !empty($division_param) || !empty($code_param));

// Single Code Detailed View
$single_nic = null;
$single_hierarchy = null;
$child_records = [];

if (!empty($code_param)) {
    $safe_code = mysqli_real_escape_string($con, $code_param);
    $res_single = mysqli_query($con, "SELECT * FROM `nic` WHERE code = '$safe_code' ORDER BY FIELD(level, 'SUBCLASS', 'CLASS', 'GROUP', 'DIVISION', 'SECTION') LIMIT 1");
    if ($res_single && mysqli_num_rows($res_single) > 0) {
        $single_nic = mysqli_fetch_assoc($res_single);
        $single_hierarchy = get_nic_details($single_nic['code']);

        // Fetch direct child codes if any
        $cur_code = mysqli_real_escape_string($con, $single_nic['code']);
        $qChildren = mysqli_query($con, "SELECT * FROM `nic` WHERE parent_code = '$cur_code' ORDER BY code ASC LIMIT 100");
        if ($qChildren) {
            while ($cr = mysqli_fetch_assoc($qChildren)) {
                $child_records[] = $cr;
            }
        }
    }
}

// SEO Meta Setup
if ($single_nic) {
    $page_title = "NIC Code " . htmlspecialchars($single_nic['code']) . " - " . htmlspecialchars($single_nic['title']) . " (" . $single_nic['level'] . ") | NIC 2025 India - Online Law";
    $meta_desc = "Official MoSPI NIC 2025 Activity Code " . $single_nic['code'] . " (" . $single_nic['level'] . "): " . substr(strip_tags($single_nic['title']), 0, 160) . ". View full classification hierarchy and registered companies.";
} elseif (!empty($q_param)) {
    $page_title = "NIC Code Search for \"" . htmlspecialchars($q_param) . "\" | National Industrial Classification 2025 - Online Law";
    $meta_desc = "Search results for \"" . htmlspecialchars($q_param) . "\" in the official National Industrial Classification (NIC 2025) directory. Find 2-digit to 6-digit industry activity codes.";
} else {
    $page_title = "NIC Code Finder 2025 | National Industrial Classification Directory & Search - Online Law";
    $meta_desc = "Official MoSPI National Industrial Classification (NIC 2025) Code Finder. Search 2,715 official activity codes for Company Registration, MSME Udyam, GST, and MCA CIN.";
}

// Search Results Query
$results_list = [];
$total_results = 0;
$total_pages = 0;
$captcha_error = false;

if ($has_search && !$single_nic) {
    if (!empty($q_param) && !olaw_validate_search_captcha()) {
        $captcha_error = true;
    }

    if (!$captcha_error) {
        $where = ["status = 'ACTIVE'"];

        if (!empty($q_param)) {
            $sq = mysqli_real_escape_string($con, $q_param);
            $where[] = "(code LIKE '%$sq%' OR code_5digit LIKE '%$sq%' OR title LIKE '%$sq%' OR description LIKE '%$sq%')";
        }
        if (!empty($level_param) && in_array($level_param, ['SECTION', 'DIVISION', 'GROUP', 'CLASS', 'SUBCLASS'])) {
            $where[] = "level = '$level_param'";
        }
        if (!empty($section_param)) {
            $sec_esc = mysqli_real_escape_string($con, $section_param);
            $where[] = "(section_code = '$sec_esc' OR (level = 'SECTION' AND code = '$sec_esc'))";
        }
        if (!empty($division_param)) {
            $div_esc = mysqli_real_escape_string($con, $division_param);
            $div_pad = str_pad($div_esc, 2, '0', STR_PAD_LEFT);
            $where[] = "(division_code = '$div_esc' OR division_code = '$div_pad' OR (level = 'DIVISION' AND code = '$div_pad'))";
        }

        $where_sql = implode(' AND ', $where);

        $count_res = mysqli_query($con, "SELECT COUNT(*) as total FROM `nic` WHERE $where_sql");
        if ($count_res) {
            $r_cnt = mysqli_fetch_assoc($count_res);
            $total_results = (int)$r_cnt['total'];
            $total_pages = ceil($total_results / $limit);
        }

        $data_res = mysqli_query($con, "SELECT * FROM `nic` WHERE $where_sql ORDER BY CHAR_LENGTH(code) ASC, code ASC LIMIT $offset, $limit");
        if ($data_res) {
            while ($row = mysqli_fetch_assoc($data_res)) {
                $results_list[] = $row;
            }
        }
    }
}

// Total Stats for Landing
$nic_counts = ['SECTION' => 21, 'DIVISION' => 87, 'GROUP' => 257, 'CLASS' => 463, 'SUBCLASS' => 1886, 'TOTAL' => 2714];
try {
    if ($con) {
        $stats_query = @mysqli_query($con, "SELECT level, count(*) as cnt FROM `nic` WHERE status = 'ACTIVE' GROUP BY level");
        if ($stats_query) {
            $nic_counts['TOTAL'] = 0;
            while ($sq_row = mysqli_fetch_assoc($stats_query)) {
                $nic_counts[$sq_row['level']] = (int)$sq_row['cnt'];
                $nic_counts['TOTAL'] += (int)$sq_row['cnt'];
            }
            mysqli_free_result($stats_query);
        }
    }
} catch (Throwable $e) {}

// All Sections for filter dropdown & landing grid
$sections_list = [];
try {
    if ($con) {
        $all_sections_res = @mysqli_query($con, "SELECT code, title FROM `nic` WHERE level = 'SECTION' ORDER BY code ASC");
        if ($all_sections_res) {
            while ($sec_r = mysqli_fetch_assoc($all_sections_res)) {
                $sections_list[] = $sec_r;
            }
            mysqli_free_result($all_sections_res);
        }
    }
} catch (Throwable $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="NIC Code Finder 2025, National Industrial Classification, NIC Activity Code, MoSPI NIC Codes, 5 Digit NIC Code, 2 Digit Division, MSME Udyam Activity Code, Company Activity Code, <?php echo htmlspecialchars($q_param); ?>">
<meta name="author" content="OfferPlant">
<title><?php echo $page_title; ?></title>

<!-- Schema.org JSON-LD -->
<?php
$nic_schema = [
  "@context" => "https://schema.org",
  "@type" => "WebPage",
  "name" => $page_title,
  "description" => $meta_desc,
  "breadcrumb" => [
    "@type" => "BreadcrumbList",
    "itemListElement" => [
      [
        "@type" => "ListItem",
        "position" => 1,
        "name" => "Home",
        "item" => $base_url
      ],
      [
        "@type" => "ListItem",
        "position" => 2,
        "name" => "NIC Code Directory",
        "item" => $base_url . "nic"
      ]
    ]
  ]
];
?>
<script type="application/ld+json">
<?php echo json_encode($nic_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- Google Fonts & FontAwesome -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* =========================================================
       NIC CODE FINDER - CUSTOM MODERN STYLES
       ========================================================= */
    :root {
        --nic-hero-bg: linear-gradient(180deg, #ffffff 0%, #fafbfc 55%, #f1f5f9 100%);
        --nic-section-amber: #d97706;
        --nic-section-amber-bg: #fef3c7;
        --nic-division-blue: #ff671f;
        --nic-division-blue-bg: #fff7ed;
        --nic-group-indigo: #4f46e5;
        --nic-group-indigo-bg: #e0e7ff;
        --nic-class-purple: #7c3aed;
        --nic-class-purple-bg: #f3e8ff;
        --nic-subclass-emerald: #059669;
        --nic-subclass-emerald-bg: #ecfdf5;
        --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        --font-code: 'JetBrains Mono', monospace;
    }

    body {
        font-family: var(--font-main) !important;
    }

    /* Hero Banner with Grid Pattern */
    .nic-hero-banner {
        background: var(--nic-hero-bg);
        border-bottom: 1px solid #e2e8f0;
        padding: clamp(45px, 6vw, 75px) 0 clamp(65px, 8vw, 95px);
        position: relative;
        overflow: hidden;
    }
    .nic-hero-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(15, 23, 42, 0.05) 1px, transparent 1px);
        background-size: 24px 24px;
        opacity: 0.7;
        pointer-events: none;
    }
    .nic-glow-orb-1 {
        position: absolute;
        width: 550px;
        height: 550px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 103, 31, 0.08) 0%, rgba(255, 103, 31, 0) 70%);
        top: -140px;
        left: -120px;
        pointer-events: none;
        animation: pulseOrb 8s ease-in-out infinite alternate;
    }
    .nic-glow-orb-2 {
        position: absolute;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(4, 106, 56, 0.07) 0%, rgba(4, 106, 56, 0) 70%);
        bottom: -160px;
        right: -100px;
        pointer-events: none;
        animation: pulseOrb 10s ease-in-out infinite alternate-reverse;
    }
    @keyframes pulseOrb {
        0% { transform: scale(1) translate(0, 0); }
        100% { transform: scale(1.15) translate(20px, 15px); }
    }

    .nic-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1.5px solid #fed7aa;
        color: #c2410c;
        padding: 6px 16px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 14px rgba(255, 103, 31, 0.08);
    }
    .nic-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: nicPulse 2s infinite;
    }
    @keyframes nicPulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* Code Badges */
    .nic-code-badge {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-weight: 800;
        letter-spacing: 0.5px;
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s ease;
    }
    .nic-level-section { background: var(--nic-section-amber-bg); color: #92400e; border: 1px solid #fde68a; }
    .nic-level-division { background: var(--nic-division-blue-bg); color: #1e40af; border: 1px solid #bfdbfe; }
    .nic-level-group { background: var(--nic-group-indigo-bg); color: #3730a3; border: 1px solid #c7d2fe; }
    .nic-level-class { background: var(--nic-class-purple-bg); color: #6b21a8; border: 1px solid #e9d5ff; }
    .nic-level-subclass { background: var(--nic-subclass-emerald-bg); color: #065f46; border: 1px solid #a7f3d0; }

    /* Search Console Card */
    .nic-search-console {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.12), 0 4px 12px rgba(15, 23, 42, 0.04);
        border: 1px solid #e2e8f0;
        padding: clamp(20px, 3vw, 32px);
        margin-top: -55px;
        position: relative;
        z-index: 20;
    }
    .nic-input-wrap {
        position: relative;
    }
    .nic-input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 16px;
        pointer-events: none;
    }
    .nic-input-control {
        height: 52px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        padding-left: 16px;
        padding-right: 16px;
        font-size: 14.5px;
        color: #0f172a;
        background-color: #ffffff;
        transition: all 0.2s ease;
        font-family: inherit;
    }
    .nic-input-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        outline: none;
    }
    .nic-select-control {
        height: 52px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        padding-left: 42px;
        padding-right: 32px;
        font-size: 14px;
        color: #0f172a;
        font-weight: 500;
        background-color: #ffffff;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .nic-select-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        outline: none;
    }

    /* Sector Quick Pills */
    .sector-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 13px;
        border-radius: 9999px;
        font-size: 12.5px;
        font-weight: 600;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }
    .sector-pill:hover {
        background: #eff6ff;
        color: var(--primary-color);
        border-color: #bfdbfe;
        transform: translateY(-1px);
    }
    .sector-pill.active {
        background: var(--primary-color);
        color: #ffffff;
        border-color: var(--primary-color);
    }

    /* Result Cards */
    .nic-card-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        position: relative;
    }
    .nic-card-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 28px -6px rgba(15, 23, 42, 0.09), 0 4px 10px rgba(15, 23, 42, 0.04);
        border-color: #cbd5e1;
    }
    .nic-card-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 20px;
        right: 20px;
        height: 3px;
        border-radius: 3px 3px 0 0;
        background: transparent;
        transition: background 0.25s ease;
    }
    .nic-card-item:hover::before {
        background: var(--primary-gradient);
    }

    /* View Switch Buttons */
    .view-switch-btn {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        transition: all 0.2s;
        text-decoration: none !important;
        font-size: 15px;
    }
    .view-switch-btn.active, .view-switch-btn:hover {
        background: var(--primary-color);
        color: #ffffff;
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    /* Hierarchy Stepper Tree */
    .hierarchy-stepper-box {
        display: flex;
        flex-direction: column;
        gap: 12px;
        position: relative;
    }
    .hierarchy-step-node {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 18px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }
    .hierarchy-step-node:hover {
        background: #ffffff;
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transform: translateX(4px);
    }
    .hierarchy-step-node.active {
        background: #eff6ff;
        border-color: #3b82f6;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.12);
    }
    .hierarchy-dot-line {
        position: absolute;
        left: 28px;
        top: 30px;
        bottom: 30px;
        width: 2px;
        background: #e2e8f0;
        z-index: 1;
    }

    /* Interactive Stat Cards */
    .nic-stat-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 20px 18px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .nic-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -6px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
    }
    .nic-stat-icon-wrap {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-bottom: 12px;
    }

    /* Popular Sector Cards */
    .popular-sector-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 22px 20px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none !important;
    }
    .popular-sector-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 28px -6px rgba(15, 23, 42, 0.09);
        border-color: #93c5fd;
    }

    /* Section A-U Card */
    .section-grid-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        height: 100%;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }
    .section-grid-card:hover {
        background: #f8fafc;
        border-color: var(--primary-color);
        box-shadow: 0 8px 18px -4px rgba(15, 23, 42, 0.08);
        transform: translateY(-2px);
    }

    /* CIN Anatomy Segment */
    .cin-segment-card {
        background: #0f172a;
        color: #ffffff;
        border-radius: 20px;
        padding: clamp(24px, 4vw, 36px);
        position: relative;
        overflow: hidden;
    }
    .cin-part {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-weight: 800;
        font-size: clamp(16px, 2.5vw, 22px);
        letter-spacing: 1px;
        padding: 8px 12px;
        border-radius: 8px;
        display: inline-block;
        margin: 3px 2px;
        text-align: center;
    }

    /* Custom Toast for Copy Action */
    .copy-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #0f172a;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        z-index: 9999;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .copy-toast.show {
        opacity: 1;
        transform: translateY(0);
    }
</style>

<?php require_once('includes/header.php'); ?>

<!-- Banner Section Start -->
<section class="nic-hero-banner">
    <div class="nic-glow-orb-1"></div>
    <div class="nic-glow-orb-2"></div>
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10 col-12">
                <div class="nic-badge-pill mb-3">
                    <span class="nic-pulse-dot"></span>
                    <span>OFFICIAL MOSPI STANDARDS &bull; NIC 2025 REVISED EDITION</span>
                </div>
                <h1 class="font-weight-bold mb-3" style="font-family: var(--font-heading); font-size: clamp(28px, 4.2vw, 44px); line-height: 1.22; letter-spacing: -0.5px;">
                    <span style="color: #0f172a; display: inline-block;">National Industrial Classification</span> <br class="d-none d-sm-inline">
                    <span style="background: linear-gradient(135deg, #ff671f 0%, #ea580c 45%, #046a38 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">(NIC) Finder</span>
                </h1>
                <p class="lead mb-3" style="color: #475569 !important; font-size: clamp(15px, 2vw, 17.5px); max-width: 820px; margin: 0 auto; line-height: 1.6;">
                    Explore <strong>2,715 official activity codes</strong> across 21 broad sections &amp; 87 divisions. Indispensable for <strong class="text-dark">MCA SPICe+ Incorporation</strong>, <strong class="text-dark">MSME Udyam Registration</strong>, and <strong class="text-dark">GST HSN/SAC Mapping</strong>.
                </p>
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-2 pt-1" style="gap: 8px;">
                    <span class="badge px-3 py-1.5 rounded-pill font-weight-bold" style="font-size: 12.5px; color: #0369a1; background: #f0f9ff; border: 1px solid #bae6fd; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
                        <i class="fas fa-check-circle mr-1" style="color: #0284c7;"></i> MCA SPICe+ Compliant
                    </span>
                    <span class="badge px-3 py-1.5 rounded-pill font-weight-bold" style="font-size: 12.5px; color: #15803d; background: #f0fdf4; border: 1px solid #bbf7d0; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
                        <i class="fas fa-check-circle mr-1" style="color: #16a34a;"></i> MSME Udyam 2/4/5 Digit Key
                    </span>
                    <span class="badge px-3 py-1.5 rounded-pill font-weight-bold" style="font-size: 12.5px; color: #b45309; background: #fffbeb; border: 1px solid #fde68a; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
                        <i class="fas fa-check-circle mr-1" style="color: #d97706;"></i> 21-Digit CIN Industry Segment
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->

<div class="full-row py-4" style="background-color: var(--bg-body); position: relative; z-index: 10;">
	<div class="container">

        <!-- Search Form Console Card -->
        <div class="nic-search-console mb-5">
            <form action="nic.php" method="GET" id="nicSearchForm">
                <div class="row align-items-end">
                    <div class="col-lg-4 col-md-12 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-dark mb-2 d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-search mr-1.5 text-primary"></i> Keyword, Activity Name or Code</span>
                            <?php if (!empty($q_param)): ?>
                                <a href="nic.php" class="text-muted small" style="text-decoration: underline;">Clear</a>
                            <?php endif; ?>
                        </label>
                        <div class="nic-input-wrap">
                            <input type="text" name="q" class="form-control nic-input-control" placeholder="e.g. Software, 62013, Hospital, Real Estate, Dairy..." value="<?php echo htmlspecialchars($q_param); ?>" autocomplete="off" autofocus>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-dark mb-2">
                            <i class="fas fa-layer-group mr-1.5 text-info"></i> Classification Level
                        </label>
                        <div class="nic-input-wrap">
                            <i class="fas fa-filter nic-input-icon" style="color: #0ea5e9;"></i>
                            <select name="level" class="form-control nic-select-control">
                                <option value="">All 5 Levels (Section to Sub-Class)</option>
                                <option value="SECTION" <?php echo ($level_param === 'SECTION') ? 'selected' : ''; ?>>SECTION (1-Letter Broad Sector)</option>
                                <option value="DIVISION" <?php echo ($level_param === 'DIVISION') ? 'selected' : ''; ?>>DIVISION (2-Digit Major Industry)</option>
                                <option value="GROUP" <?php echo ($level_param === 'GROUP') ? 'selected' : ''; ?>>GROUP (3-Digit Industry Group)</option>
                                <option value="CLASS" <?php echo ($level_param === 'CLASS') ? 'selected' : ''; ?>>CLASS (4-Digit Industry Class)</option>
                                <option value="SUBCLASS" <?php echo ($level_param === 'SUBCLASS') ? 'selected' : ''; ?>>SUBCLASS (5/6-Digit Operation)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-dark mb-2">
                            <i class="fas fa-sitemap mr-1.5 text-warning"></i> Broad Economic Section
                        </label>
                        <div class="nic-input-wrap">
                            <i class="fas fa-th-list nic-input-icon" style="color: #f59e0b;"></i>
                            <select name="section" class="form-control nic-select-control">
                                <option value="">All 21 Broad Sections (A to U)</option>
                                <?php foreach ($sections_list as $sec): ?>
                                    <option value="<?php echo $sec['code']; ?>" <?php echo ($section_param === $sec['code']) ? 'selected' : ''; ?>>
                                        Section <?php echo $sec['code']; ?>: <?php echo htmlspecialchars(mb_strimwidth($sec['title'], 0, 36, '...')); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold d-flex align-items-center justify-content-center shadow-sm" style="height: 52px; border-radius: 12px; font-size: 15px; letter-spacing: 0.3px;">
                            <i class="fas fa-search mr-2"></i> Search
                        </button>
                    </div>

                    <div class="col-12 mt-3 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap" style="border-color: #f1f5f9 !important; gap: 8px;">
                        <div class="d-inline-flex align-items-center flex-wrap" style="gap: 8px;">
                            <span class="small font-weight-bold text-muted"><i class="fas fa-shield-alt text-primary mr-1"></i> Security Code:</span>
                            <?php echo olaw_render_search_captcha_widget('_nic'); ?>
                        </div>
                        <span class="small text-muted"><i class="fas fa-lock mr-1"></i> Quick verification for seamless code searches.</span>
                    </div>
                </div>
            </form>

            <!-- Quick Filter Tags -->
            <div class="d-flex flex-wrap align-items-center gap-2 mt-4 pt-3 border-top">
                <span class="small text-muted font-weight-bold mr-1 d-flex align-items-center">
                    <i class="fas fa-bolt text-warning mr-1.5"></i> Quick Sectors:
                </span>
                <a href="nic.php?division=62" class="sector-pill <?php echo ($division_param === '62') ? 'active' : ''; ?>">
                    <i class="fas fa-laptop-code text-primary"></i> Software &amp; IT (62)
                </a>
                <a href="nic.php?division=46" class="sector-pill <?php echo ($division_param === '46') ? 'active' : ''; ?>">
                    <i class="fas fa-boxes text-success"></i> Wholesale Trade (46)
                </a>
                <a href="nic.php?division=47" class="sector-pill <?php echo ($division_param === '47') ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart text-danger"></i> Retail Trade (47)
                </a>
                <a href="nic.php?division=68" class="sector-pill <?php echo ($division_param === '68') ? 'active' : ''; ?>">
                    <i class="fas fa-city text-warning"></i> Real Estate (68)
                </a>
                <a href="nic.php?division=86" class="sector-pill <?php echo ($division_param === '86') ? 'active' : ''; ?>">
                    <i class="fas fa-heartbeat text-rose" style="color: #e11d48;"></i> Health &amp; Hospitals (86)
                </a>
                <a href="nic.php?division=85" class="sector-pill <?php echo ($division_param === '85') ? 'active' : ''; ?>">
                    <i class="fas fa-graduation-cap text-purple" style="color: #7c3aed;"></i> Education (85)
                </a>
                <a href="nic.php?division=35" class="sector-pill <?php echo ($division_param === '35') ? 'active' : ''; ?>">
                    <i class="fas fa-bolt text-amber" style="color: #ca8a04;"></i> Solar &amp; Power (35)
                </a>
                <a href="nic.php?division=70" class="sector-pill <?php echo ($division_param === '70') ? 'active' : ''; ?>">
                    <i class="fas fa-briefcase text-info"></i> Consulting &amp; HQ (70)
                </a>
                <a href="nic.php?division=10" class="sector-pill <?php echo ($division_param === '10') ? 'active' : ''; ?>">
                    <i class="fas fa-utensils text-secondary"></i> Food Mfg (10)
                </a>
                <?php if ($has_search): ?>
                <a href="nic.php" class="btn btn-sm btn-outline-danger font-weight-bold ml-auto d-inline-flex align-items-center" style="border-radius: 8px; padding: 5px 12px;">
                    <i class="fas fa-times-circle mr-1.5"></i> Clear Search Filters
                </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($single_nic): ?>

            <!-- =========================================================
                 1. SINGLE NIC CODE DETAILED INSPECTION VIEW
                 ========================================================= -->
            <?php 
                $lvl = strtolower($single_nic['level']);
                $badgeCls = 'nic-level-' . $lvl;
                $divToLink = !empty($single_nic['division_code']) ? $single_nic['division_code'] : (strlen($single_nic['code']) >= 2 ? substr($single_nic['code'], 0, 2) : $single_nic['code']);
            ?>

            <!-- Back Navigation Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb bg-transparent p-0 mb-0 align-items-center" style="font-size: 13.5px;">
                    <li class="breadcrumb-item"><a href="index.php" class="text-muted"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="nic.php" class="text-muted">NIC Directory</a></li>
                    <?php if (!empty($single_nic['section_code'])): ?>
                    <li class="breadcrumb-item"><a href="nic.php?section=<?php echo urlencode($single_nic['section_code']); ?>" class="text-muted">Section <?php echo htmlspecialchars($single_nic['section_code']); ?></a></li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active font-weight-bold text-primary" aria-current="page">Code <?php echo htmlspecialchars($single_nic['code']); ?></li>
                </ol>
            </nav>

            <div class="bg-white rounded-xl shadow-md border mb-4 p-4 p-md-5">
                <!-- Header Showcase -->
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 pb-4 border-bottom mb-4">
                    <div style="flex: 1; min-width: 280px;">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2.5">
                            <span class="nic-code-badge <?php echo $badgeCls; ?>" style="font-size: 14px; padding: 6px 14px;">
                                <i class="fas fa-tag mr-1"></i> <?php echo htmlspecialchars($single_nic['level']); ?> <?php echo htmlspecialchars($single_nic['code']); ?>
                            </span>
                            <span class="badge badge-success px-3 py-1.5 rounded-pill text-uppercase font-weight-bold" style="background: #ecfdf5; color: #047857; font-size: 12px;">
                                <i class="fas fa-check-circle mr-1"></i> Official MoSPI 2025 Standard
                            </span>
                            <?php if (!empty($single_nic['section_code'])): ?>
                            <span class="badge badge-light border text-muted px-2.5 py-1.5 rounded-pill font-weight-bold" style="font-size: 12px;">
                                Section <?php echo htmlspecialchars($single_nic['section_code']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <h2 class="font-weight-bold text-dark mb-2" style="font-size: clamp(22px, 3.2vw, 28px); font-family: var(--font-heading); line-height: 1.3;">
                            <?php echo htmlspecialchars($single_nic['title']); ?>
                        </h2>
                        <p class="text-muted small mb-0">
                            Classification Identifier: <strong class="text-dark"><?php echo htmlspecialchars($single_nic['code']); ?></strong> &bull; Tier: <strong class="text-dark"><?php echo htmlspecialchars($single_nic['level']); ?></strong>
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-secondary font-weight-bold d-inline-flex align-items-center" onclick="copyNicCode('<?php echo htmlspecialchars($single_nic['code']); ?>', '<?php echo addslashes($single_nic['title']); ?>')" title="Copy Code & Title">
                            <i class="fas fa-copy mr-1.5 text-primary"></i> Copy Code
                        </button>
                        <?php if (!empty($divToLink)): ?>
                        <a href="company-incorporation.php?activity=<?php echo urlencode($divToLink); ?>" class="btn btn-primary font-weight-bold d-inline-flex align-items-center shadow-sm">
                            <i class="fas fa-building mr-1.5"></i> View Registered Companies &rarr;
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column: Scope & Regulatory Info -->
                    <div class="col-lg-7 col-12 mb-4 mb-lg-0">
                        <!-- Official Scope / Description Card -->
                        <div class="p-4 rounded-xl border mb-4" style="background: #f8fafc;">
                            <h5 class="font-weight-bold text-dark mb-2.5 d-flex align-items-center" style="font-size: 16px;">
                                <i class="fas fa-info-circle text-primary mr-2"></i> Official Activity Scope, Inclusions &amp; Exclusions
                            </h5>
                            <?php if (!empty($single_nic['description'])): ?>
                                <div class="text-secondary" style="font-size: 14px; line-height: 1.75;">
                                    <?php echo nl2br(htmlspecialchars($single_nic['description'])); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small mb-0">
                                    Comprehensive operational scope defined under Division <?php echo htmlspecialchars($divToLink); ?> of the National Industrial Classification (NIC 2025). Refer to hierarchy tree for broader and subordinate classifications.
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Regulatory Applications Card -->
                        <div class="p-4 rounded-xl border" style="background: #ffffff;">
                            <h5 class="font-weight-bold text-dark mb-3" style="font-size: 16px;">
                                <i class="fas fa-balance-scale text-primary mr-2"></i> Regulatory Compliance &amp; Portal Mappings
                            </h5>
                            <div class="row">
                                <div class="col-md-6 col-12 mb-3">
                                    <div class="p-3 rounded-lg border bg-light h-100">
                                        <div class="small font-weight-bold text-primary mb-1">
                                            <i class="fas fa-file-signature mr-1"></i> MCA SPICe+ Incorporation
                                        </div>
                                        <p class="text-muted small mb-0" style="line-height: 1.5;">
                                            Use 5-digit code <strong class="text-dark"><?php echo htmlspecialchars(substr(str_pad($single_nic['code'], 5, '0', STR_PAD_RIGHT), 0, 5)); ?></strong> in Part-B of SPICe+ to auto-generate the 21-digit CIN.
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 mb-3">
                                    <div class="p-3 rounded-lg border bg-light h-100">
                                        <div class="small font-weight-bold text-success mb-1">
                                            <i class="fas fa-industry mr-1"></i> MSME Udyam Portal
                                        </div>
                                        <p class="text-muted small mb-0" style="line-height: 1.5;">
                                            Select 2-digit (Div <?php echo htmlspecialchars($divToLink); ?>), 4-digit class, and 5-digit activity code for subsidy priority.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: 5-Tier Hierarchy Stepper Visualizer -->
                    <div class="col-lg-5 col-12">
                        <div class="p-4 rounded-xl border bg-light h-100">
                            <h5 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">
                                <i class="fas fa-sitemap text-primary mr-2"></i> 5-Tier Lineage Breakdown
                            </h5>
                            <p class="text-muted small mb-3">Click any level to inspect its taxonomy</p>

                            <div class="hierarchy-stepper-box">
                                <?php if (!empty($single_hierarchy['section'])): ?>
                                    <a href="nic.php?section=<?php echo urlencode($single_hierarchy['section']['code']); ?>" class="hierarchy-step-node <?php echo ($single_nic['level'] === 'SECTION') ? 'active' : ''; ?>">
                                        <span class="nic-code-badge nic-level-section" style="font-size: 11px; min-width: 32px; text-align: center;">
                                            SEC <?php echo htmlspecialchars($single_hierarchy['section']['code']); ?>
                                        </span>
                                        <div>
                                            <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 11px;">1. Broad Section</div>
                                            <div class="font-weight-bold text-dark" style="font-size: 13.5px; line-height: 1.35;">
                                                <?php echo htmlspecialchars($single_hierarchy['section']['title']); ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($single_hierarchy['division'])): ?>
                                    <a href="nic.php?code=<?php echo urlencode($single_hierarchy['division']['code']); ?>" class="hierarchy-step-node <?php echo ($single_nic['level'] === 'DIVISION') ? 'active' : ''; ?>">
                                        <span class="nic-code-badge nic-level-division" style="font-size: 11px; min-width: 32px; text-align: center;">
                                            DIV <?php echo htmlspecialchars($single_hierarchy['division']['code']); ?>
                                        </span>
                                        <div>
                                            <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 11px;">2. Division (2 Digits)</div>
                                            <div class="font-weight-bold text-dark" style="font-size: 13.5px; line-height: 1.35;">
                                                <?php echo htmlspecialchars($single_hierarchy['division']['title']); ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($single_hierarchy['group'])): ?>
                                    <a href="nic.php?code=<?php echo urlencode($single_hierarchy['group']['code']); ?>" class="hierarchy-step-node <?php echo ($single_nic['level'] === 'GROUP') ? 'active' : ''; ?>">
                                        <span class="nic-code-badge nic-level-group" style="font-size: 11px; min-width: 32px; text-align: center;">
                                            GRP <?php echo htmlspecialchars($single_hierarchy['group']['code']); ?>
                                        </span>
                                        <div>
                                            <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 11px;">3. Group (3 Digits)</div>
                                            <div class="font-weight-bold text-dark" style="font-size: 13.5px; line-height: 1.35;">
                                                <?php echo htmlspecialchars($single_hierarchy['group']['title']); ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($single_hierarchy['class'])): ?>
                                    <a href="nic.php?code=<?php echo urlencode($single_hierarchy['class']['code']); ?>" class="hierarchy-step-node <?php echo ($single_nic['level'] === 'CLASS') ? 'active' : ''; ?>">
                                        <span class="nic-code-badge nic-level-class" style="font-size: 11px; min-width: 32px; text-align: center;">
                                            CLS <?php echo htmlspecialchars($single_hierarchy['class']['code']); ?>
                                        </span>
                                        <div>
                                            <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 11px;">4. Class (4 Digits)</div>
                                            <div class="font-weight-bold text-dark" style="font-size: 13.5px; line-height: 1.35;">
                                                <?php echo htmlspecialchars($single_hierarchy['class']['title']); ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($single_hierarchy['subclass']) && $single_nic['level'] === 'SUBCLASS'): ?>
                                    <div class="hierarchy-step-node active">
                                        <span class="nic-code-badge nic-level-subclass" style="font-size: 11px; min-width: 32px; text-align: center;">
                                            SUB <?php echo htmlspecialchars($single_hierarchy['subclass']['code']); ?>
                                        </span>
                                        <div>
                                            <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 11px;">5. Sub-Class (5/6 Digits)</div>
                                            <div class="font-weight-bold text-dark" style="font-size: 13.5px; line-height: 1.35;">
                                                <?php echo htmlspecialchars($single_hierarchy['subclass']['title']); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Child Sub-Classes / Class Breakdown -->
                <?php if (!empty($child_records)): ?>
                <div class="mt-5 pt-4 border-top">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px; font-family: var(--font-heading);">
                                <i class="fas fa-list-ol text-primary mr-1.5"></i> Subordinate Classifications under Code <?php echo htmlspecialchars($single_nic['code']); ?>
                            </h4>
                            <span class="text-muted small">Total <strong><?php echo count($child_records); ?></strong> subordinate activities found</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <input type="text" id="childSearchInput" class="form-control form-control-sm" placeholder="Filter subordinate activities..." style="height: 38px; border-radius: 8px; width: 260px;" onkeyup="filterChildTable()">
                        </div>
                    </div>

                    <div class="table-responsive rounded-xl border shadow-sm">
                        <table class="table table-hover mb-0" id="childNicTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted" style="width: 120px;">Code</th>
                                    <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted" style="width: 130px;">Level</th>
                                    <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted">Activity Title &amp; Scope</th>
                                    <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted text-right" style="width: 110px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($child_records as $child): ?>
                                    <?php 
                                        $c_lvl = strtolower($child['level']);
                                        $c_badge = 'nic-level-' . $c_lvl;
                                    ?>
                                    <tr class="child-row">
                                        <td class="px-4 py-3 align-middle font-weight-bold">
                                            <code class="text-primary font-weight-bold" style="font-size: 14px;"><?php echo htmlspecialchars($child['code']); ?></code>
                                        </td>
                                        <td class="px-4 py-3 align-middle">
                                            <span class="nic-code-badge <?php echo $c_badge; ?>" style="font-size: 11px;">
                                                <?php echo htmlspecialchars($child['level']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 align-middle">
                                            <a href="nic.php?code=<?php echo urlencode($child['code']); ?>" class="text-dark font-weight-semibold text-decoration-none child-title">
                                                <?php echo htmlspecialchars($child['title']); ?>
                                            </a>
                                            <?php if (!empty($child['description'])): ?>
                                                <div class="small text-muted mt-1 child-desc"><?php echo htmlspecialchars(mb_strimwidth($child['description'], 0, 120, '...')); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 align-middle text-right">
                                            <a href="nic.php?code=<?php echo urlencode($child['code']); ?>" class="btn btn-sm btn-outline-primary font-weight-bold px-3" style="border-radius: 6px;">
                                                Inspect
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        <?php elseif ($has_search && !$single_nic): ?>

            <?php if (!empty($captcha_error)): ?>
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" style="border-radius: 14px; background: #fffbeb; color: #b45309; border-left: 5px solid #f59e0b !important; padding: 18px 24px;">
                <i class="fas fa-shield-alt fa-2x mr-3 text-warning"></i>
                <div>
                    <h6 class="font-weight-bold mb-1" style="color: #92400e;">Security Verification Required</h6>
                    <p class="small mb-0 text-muted">Please enter the security verification code displayed above in the search console to view industry classification records.</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- =========================================================
                 2. SEARCH RESULTS VIEW (GRID / TABLE)
                 ========================================================= -->

            <!-- Search Results Header Bar -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h4 class="font-weight-bold text-dark mb-0" style="font-size: 22px; font-family: var(--font-heading);">
                            NIC Search Results
                        </h4>
                        <span class="badge badge-primary px-3 py-1 rounded-pill font-weight-bold" style="background: #eff6ff; color: var(--primary-color); font-size: 13px;">
                            <?php echo number_format($total_results); ?> Found
                        </span>
                    </div>
                    <span class="text-muted small">
                        Showing page <strong><?php echo $page; ?></strong> of <strong><?php echo max(1, $total_pages); ?></strong>
                        <?php if (!empty($q_param)): ?>
                            for query "<strong><?php echo htmlspecialchars($q_param); ?></strong>"
                        <?php endif; ?>
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <?php 
                        $gridQuery = $_GET; $gridQuery['view'] = 'grid';
                        $tableQuery = $_GET; $tableQuery['view'] = 'table';
                    ?>
                    <a href="?<?php echo http_build_query($gridQuery); ?>" class="view-switch-btn <?php echo $view_mode === 'grid' ? 'active' : ''; ?>" title="Grid View">
                        <i class="fas fa-th-large"></i>
                    </a>
                    <a href="?<?php echo http_build_query($tableQuery); ?>" class="view-switch-btn <?php echo $view_mode === 'table' ? 'active' : ''; ?>" title="Table View">
                        <i class="fas fa-list"></i>
                    </a>
                </div>
            </div>

            <!-- Search Results Content -->
            <?php if ($total_results > 0): ?>

                <?php if ($view_mode === 'table'): ?>
                    <!-- Table View -->
                    <div class="bg-white rounded-xl shadow-sm border mb-4 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 px-4 py-3.5 small text-uppercase font-weight-bold text-muted" style="width: 120px;">Code</th>
                                        <th class="border-0 px-4 py-3.5 small text-uppercase font-weight-bold text-muted" style="width: 130px;">Level</th>
                                        <th class="border-0 px-4 py-3.5 small text-uppercase font-weight-bold text-muted">Activity Title &amp; Scope</th>
                                        <th class="border-0 px-4 py-3.5 small text-uppercase font-weight-bold text-muted" style="width: 130px;">Section</th>
                                        <th class="border-0 px-4 py-3.5 small text-uppercase font-weight-bold text-muted text-right" style="width: 160px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results_list as $row): ?>
                                        <?php 
                                            $r_lvl = strtolower($row['level']);
                                            $r_badge = 'nic-level-' . $r_lvl;
                                            $divCode = $row['division_code'] ?: (strlen($row['code']) >= 2 ? substr($row['code'], 0, 2) : $row['code']);
                                        ?>
                                        <tr>
                                            <td class="px-4 py-3.5 align-middle">
                                                <code class="font-weight-bold text-primary" style="font-size: 14.5px;"><?php echo htmlspecialchars($row['code']); ?></code>
                                            </td>
                                            <td class="px-4 py-3.5 align-middle">
                                                <span class="nic-code-badge <?php echo $r_badge; ?>" style="font-size: 11px;">
                                                    <?php echo htmlspecialchars($row['level']); ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3.5 align-middle">
                                                <a href="nic.php?code=<?php echo urlencode($row['code']); ?>" class="text-dark font-weight-semibold text-decoration-none" style="font-size: 14.5px;">
                                                    <?php echo htmlspecialchars($row['title']); ?>
                                                </a>
                                                <?php if (!empty($row['description'])): ?>
                                                    <div class="small text-muted mt-1"><?php echo htmlspecialchars(mb_strimwidth($row['description'], 0, 130, '...')); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-3.5 align-middle small text-muted font-weight-medium">
                                                <span class="badge badge-light border px-2 py-1 rounded">
                                                    Sec <?php echo htmlspecialchars($row['section_code'] ?: '-'); ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3.5 align-middle text-right">
                                                <div class="d-inline-flex gap-1.5">
                                                    <a href="nic.php?code=<?php echo urlencode($row['code']); ?>" class="btn btn-sm btn-outline-primary font-weight-bold px-2.5" style="border-radius: 6px;" title="Inspect Code">
                                                        Inspect
                                                    </a>
                                                    <?php if (!empty($divCode)): ?>
                                                    <a href="company-incorporation.php?activity=<?php echo urlencode($divCode); ?>" class="btn btn-sm btn-light border text-muted px-2.5" style="border-radius: 6px;" title="View Registered Companies in MCA">
                                                        <i class="fas fa-building text-primary"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Grid View -->
                    <div class="row">
                        <?php foreach ($results_list as $row): ?>
                            <?php 
                                $r_lvl = strtolower($row['level']);
                                $r_badge = 'nic-level-' . $r_lvl;
                                $divCode = $row['division_code'] ?: (strlen($row['code']) >= 2 ? substr($row['code'], 0, 2) : $row['code']);
                            ?>
                            <div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
                                <div class="nic-card-item w-100">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="nic-code-badge <?php echo $r_badge; ?>">
                                                <i class="fas fa-layer-group mr-1" style="font-size: 10px;"></i> <?php echo htmlspecialchars($row['level']); ?> <?php echo htmlspecialchars($row['code']); ?>
                                            </span>
                                            <?php if (!empty($row['section_code'])): ?>
                                                <span class="badge badge-light border text-muted font-weight-bold" style="font-size: 11px;">
                                                    Sec <?php echo htmlspecialchars($row['section_code']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <h5 class="font-weight-bold text-dark mb-2" style="font-size: 15.5px; line-height: 1.4; min-height: 44px; font-family: var(--font-heading);">
                                            <a href="nic.php?code=<?php echo urlencode($row['code']); ?>" class="text-dark text-decoration-none" style="transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)';" onmouseout="this.style.color='#0f172a';">
                                                <?php echo htmlspecialchars($row['title']); ?>
                                            </a>
                                        </h5>

                                        <?php if (!empty($row['description'])): ?>
                                            <p class="small text-muted mb-3" style="line-height: 1.55; min-height: 38px;">
                                                <?php echo htmlspecialchars(mb_strimwidth($row['description'], 0, 115, '...')); ?>
                                            </p>
                                        <?php else: ?>
                                            <p class="small text-muted mb-3" style="line-height: 1.55; min-height: 38px;">
                                                Official National Industrial Classification standard under Division <?php echo htmlspecialchars($divCode); ?>.
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="pt-3 border-top mt-3 d-flex justify-content-between align-items-center">
                                        <a href="nic.php?code=<?php echo urlencode($row['code']); ?>" class="btn btn-sm btn-outline-primary font-weight-bold px-3" style="border-radius: 8px;">
                                            Inspect Hierarchy &rarr;
                                        </a>
                                        <?php if (!empty($divCode)): ?>
                                        <a href="company-incorporation.php?activity=<?php echo urlencode($divCode); ?>" class="btn btn-sm btn-light border text-dark font-weight-bold px-2.5" style="border-radius: 8px;" title="View Registered Companies under Division <?php echo $divCode; ?>">
                                            <i class="fas fa-building text-primary mr-1"></i> Companies
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination" style="gap: 5px;">
                            <?php
                            $queryParams = $_GET;
                            // Prev
                            if ($page > 1) {
                                $queryParams['p'] = $page - 1;
                                echo '<li class="page-item"><a class="page-link rounded-pill px-3.5 shadow-xs" href="?' . http_build_query($queryParams) . '">&laquo; Prev</a></li>';
                            } else {
                                echo '<li class="page-item disabled"><span class="page-link rounded-pill px-3.5 text-muted">&laquo; Prev</span></li>';
                            }

                            $startPage = max(1, $page - 2);
                            $endPage = min($total_pages, $page + 2);

                            for ($i = $startPage; $i <= $endPage; $i++) {
                                $queryParams['p'] = $i;
                                $active = ($i == $page) ? 'active' : '';
                                echo '<li class="page-item ' . $active . '"><a class="page-link rounded-pill px-3.5" href="?' . http_build_query($queryParams) . '">' . $i . '</a></li>';
                            }

                            // Next
                            if ($page < $total_pages) {
                                $queryParams['p'] = $page + 1;
                                echo '<li class="page-item"><a class="page-link rounded-pill px-3.5 shadow-xs" href="?' . http_build_query($queryParams) . '">Next &raquo;</a></li>';
                            } else {
                                echo '<li class="page-item disabled"><span class="page-link rounded-pill px-3.5 text-muted">Next &raquo;</span></li>';
                            }
                            ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- No Results Empty State -->
                <div class="bg-white rounded-xl shadow-sm border py-5 px-4 text-center mb-5">
                    <div class="mx-auto mb-3 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; background: #eff6ff; color: var(--primary-color); font-size: 28px;">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-2">No Matching Activity Codes Found</h4>
                    <p class="text-muted mb-4" style="max-width: 500px; margin: 0 auto;">
                        We couldn't find any NIC activity codes matching "<strong><?php echo htmlspecialchars($q_param); ?></strong>". Try checking the spelling, using broader sector keywords, or searching by a 2-digit division code.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="nic.php" class="btn btn-primary font-weight-bold px-4" style="border-radius: 10px;">
                            <i class="fas fa-th-large mr-1.5"></i> Browse All 21 Sections
                        </a>
                        <a href="nic.php?division=62" class="btn btn-outline-secondary font-weight-bold px-3" style="border-radius: 10px;">
                            Software (62)
                        </a>
                        <a href="nic.php?division=47" class="btn btn-outline-secondary font-weight-bold px-3" style="border-radius: 10px;">
                            Retail (47)
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        <?php elseif (!$single_nic): ?>

            <!-- =========================================================
                 3. DEFAULT DIRECTORY OVERVIEW & 21 SECTIONS BROWSER
                 ========================================================= -->

            <!-- Metric Counter Badges -->
            <div class="row mb-5">
                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <div class="nic-stat-card text-center">
                        <div class="nic-stat-icon-wrap mx-auto" style="background: #fff7ed; color: #ff671f;">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <span class="text-muted small text-uppercase font-weight-bold">Total Codes</span>
                        <div class="h3 font-weight-bold text-primary mb-0 mt-1"><?php echo number_format($nic_counts['TOTAL']); ?></div>
                        <small class="text-muted">MoSPI 2025 Standard</small>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <div class="nic-stat-card text-center">
                        <div class="nic-stat-icon-wrap mx-auto" style="background: #fef3c7; color: #d97706;">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <span class="text-muted small text-uppercase font-weight-bold">Sections</span>
                        <div class="h3 font-weight-bold text-dark mb-0 mt-1"><?php echo $nic_counts['SECTION']; ?></div>
                        <small class="text-muted">Letters A to U</small>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <div class="nic-stat-card text-center">
                        <div class="nic-stat-icon-wrap mx-auto" style="background: #eff6ff; color: #0284c7;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <span class="text-muted small text-uppercase font-weight-bold">Divisions</span>
                        <div class="h3 font-weight-bold text-info mb-0 mt-1"><?php echo $nic_counts['DIVISION']; ?></div>
                        <small class="text-muted">2-Digit Industries</small>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <div class="nic-stat-card text-center">
                        <div class="nic-stat-icon-wrap mx-auto" style="background: #e0e7ff; color: #4f46e5;">
                            <i class="fas fa-cubes"></i>
                        </div>
                        <span class="text-muted small text-uppercase font-weight-bold">Groups</span>
                        <div class="h3 font-weight-bold mb-0 mt-1" style="color: #4f46e5;"><?php echo $nic_counts['GROUP']; ?></div>
                        <small class="text-muted">3-Digit Clusters</small>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <div class="nic-stat-card text-center">
                        <div class="nic-stat-icon-wrap mx-auto" style="background: #f3e8ff; color: #7c3aed;">
                            <i class="fas fa-th-list"></i>
                        </div>
                        <span class="text-muted small text-uppercase font-weight-bold">Classes</span>
                        <div class="h3 font-weight-bold mb-0 mt-1" style="color: #7c3aed;"><?php echo $nic_counts['CLASS']; ?></div>
                        <small class="text-muted">4-Digit Classes</small>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <div class="nic-stat-card text-center">
                        <div class="nic-stat-icon-wrap mx-auto" style="background: #ecfdf5; color: #059669;">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <span class="text-muted small text-uppercase font-weight-bold">Sub-Classes</span>
                        <div class="h3 font-weight-bold text-success mb-0 mt-1"><?php echo number_format($nic_counts['SUBCLASS']); ?></div>
                        <small class="text-muted">5 &amp; 6-Digit Codes</small>
                    </div>
                </div>
            </div>

            <!-- Interactive 5-Level Architecture Guide -->
            <div class="bg-white rounded-xl shadow-sm border p-4 p-md-5 mb-5">
                <div class="row align-items-center mb-4 pb-2 border-bottom">
                    <div class="col-md-8 col-12">
                        <span class="badge badge-primary px-3 py-1 rounded-pill text-uppercase font-weight-bold mb-1.5" style="background: #eff6ff; color: var(--primary-color); font-size: 11px;">
                            TAXONOMY GUIDE
                        </span>
                        <h3 class="font-weight-bold text-dark mb-0" style="font-size: 22px; font-family: var(--font-heading);">
                            Understanding the 5-Tier NIC Structure
                        </h3>
                    </div>
                    <div class="col-md-4 col-12 text-md-right mt-2 mt-md-0">
                        <span class="small text-muted">Official Hierarchy Architecture</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-2 col-md-4 col-12 mb-3">
                        <div class="p-3.5 rounded-xl border h-100" style="background: #fffbeb;">
                            <span class="nic-code-badge nic-level-section mb-2">Section</span>
                            <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">1-Letter Broad Sector</h6>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.5;">Broad economic domain (A to U), e.g., <strong>Section J: Information &amp; Communication</strong>.</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-12 mb-3">
                        <div class="p-3.5 rounded-xl border h-100" style="background: #eff6ff;">
                            <span class="nic-code-badge nic-level-division mb-2">Division</span>
                            <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">2-Digit Major Industry</h6>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.5;">Major branch of economy, e.g., <strong>Div 62: Computer Programming</strong>.</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-12 mb-3">
                        <div class="p-3.5 rounded-xl border h-100" style="background: #e0e7ff;">
                            <span class="nic-code-badge nic-level-group mb-2">Group</span>
                            <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">3-Digit Industry Group</h6>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.5;">Homogeneous industry group, e.g., <strong>Grp 620: Software Consultancy</strong>.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 mb-3">
                        <div class="p-3.5 rounded-xl border h-100" style="background: #f3e8ff;">
                            <span class="nic-code-badge nic-level-class mb-2">Class</span>
                            <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">4-Digit Industry Class</h6>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.5;">Specific business activity, e.g., <strong>Class 6201: Software Development</strong>.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 mb-3">
                        <div class="p-3.5 rounded-xl border h-100" style="background: #ecfdf5;">
                            <span class="nic-code-badge nic-level-subclass mb-2">Sub-Class</span>
                            <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">5/6-Digit Operation Code</h6>
                            <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.5;">Exact product/service used on <strong>MCA SPICe+ and MSME Udyam</strong>, e.g., <strong>62013: Cloud &amp; Web Apps</strong>.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 8 High Volume Economic Sectors -->
            <div class="bg-white rounded-xl shadow-sm border p-4 p-md-5 mb-5">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-2 border-bottom">
                    <div>
                        <span class="badge badge-primary px-3 py-1 rounded-pill text-uppercase font-weight-bold mb-1.5" style="font-size: 11px; background: #eff6ff; color: #1d4ed8;">
                            POPULAR SECTORS
                        </span>
                        <h3 class="font-weight-bold text-dark mb-0" style="font-size: 22px; font-family: var(--font-heading);">
                            High-Demand Business Divisions
                        </h3>
                    </div>
                    <a href="company-incorporation.php" class="btn btn-sm btn-outline-primary font-weight-bold" style="border-radius: 8px;">
                        <i class="fas fa-building mr-1.5"></i> Browse Companies by Sector &rarr;
                    </a>
                </div>

                <div class="row">
                    <?php
                    $popular_divisions = [
                        ['code' => '62', 'title' => 'Computer Programming & Software Consultancy', 'icon' => 'fa-laptop-code', 'color' => '#2563eb', 'bg' => '#eff6ff', 'companies' => '3,830+ Companies'],
                        ['code' => '46', 'title' => 'Wholesale Trade (B2B Supply & Distribution)', 'icon' => 'fa-boxes', 'color' => '#059669', 'bg' => '#ecfdf5', 'companies' => '3,150+ Companies'],
                        ['code' => '68', 'title' => 'Real Estate Activities (Development & Leasing)', 'icon' => 'fa-city', 'color' => '#d97706', 'bg' => '#fffbeb', 'companies' => '1,950+ Companies'],
                        ['code' => '47', 'title' => 'Retail Trade & E-Commerce Operations', 'icon' => 'fa-shopping-cart', 'color' => '#dc2626', 'bg' => '#fef2f2', 'companies' => '1,740+ Companies'],
                        ['code' => '85', 'title' => 'Education, Higher Academies & Skill Training', 'icon' => 'fa-graduation-cap', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'companies' => '1,260+ Companies'],
                        ['code' => '70', 'title' => 'Management Consulting & Corporate Headquarters', 'icon' => 'fa-briefcase', 'color' => '#0284c7', 'bg' => '#f0f9ff', 'companies' => '1,030+ Companies'],
                        ['code' => '86', 'title' => 'Human Health, Hospitals & Medical Clinics', 'icon' => 'fa-heartbeat', 'color' => '#e11d48', 'bg' => '#fff1f2', 'companies' => '740+ Companies'],
                        ['code' => '35', 'title' => 'Electricity, Gas, Solar & Renewable Power Supply', 'icon' => 'fa-bolt', 'color' => '#ca8a04', 'bg' => '#fefce8', 'companies' => '670+ Companies'],
                    ];
                    foreach ($popular_divisions as $pdiv): ?>
                    <div class="col-lg-3 col-md-6 col-12 mb-3 d-flex">
                        <div class="popular-sector-card w-100">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2.5">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-lg" style="width: 38px; height: 38px; background: <?php echo $pdiv['bg']; ?>; color: <?php echo $pdiv['color']; ?>; font-size: 16px;">
                                        <i class="fas <?php echo $pdiv['icon']; ?>"></i>
                                    </span>
                                    <span class="nic-code-badge nic-level-division" style="font-size: 11px;">Div <?php echo $pdiv['code']; ?></span>
                                </div>
                                <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px; line-height: 1.35;">
                                    <a href="nic.php?code=<?php echo $pdiv['code']; ?>" class="text-dark text-decoration-none">
                                        <?php echo $pdiv['title']; ?>
                                    </a>
                                </h6>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2.5 mt-2.5 border-top">
                                <span class="small font-weight-bold text-primary"><?php echo $pdiv['companies']; ?></span>
                                <a href="nic.php?code=<?php echo $pdiv['code']; ?>" class="small font-weight-bold text-muted d-inline-flex align-items-center">
                                    Inspect &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- CIN Anatomy Infographic -->
            <div class="cin-segment-card mb-5 shadow-lg">
                <div class="row align-items-center">
                    <div class="col-lg-5 col-12 mb-4 mb-lg-0">
                        <span class="badge badge-warning px-3 py-1 rounded-pill text-uppercase font-weight-bold mb-2" style="font-size: 11px;">
                            CIN DECODER
                        </span>
                        <h3 class="font-weight-bold text-white mb-2" style="font-size: 24px; font-family: var(--font-heading);">
                            How NIC Codes Embed into MCA CIN Numbers
                        </h3>
                        <p class="text-white-50 small mb-0" style="line-height: 1.6;">
                            Every Indian company is assigned a 21-digit Corporate Identification Number (CIN). Digits 2 to 6 represent the official 5-digit NIC Activity Code, instantly categorizing its industry.
                        </p>
                    </div>
                    <div class="col-lg-7 col-12">
                        <div class="p-3.5 rounded-xl border border-secondary" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
                            <div class="text-muted small font-weight-bold mb-2 text-uppercase letter-spacing-1">Sample 21-Digit CIN Anatomy</div>
                            <div class="d-flex flex-wrap align-items-center gap-1 mb-3">
                                <span class="cin-part" style="background: #334155; color: #94a3b8;" title="Listing Status (U = Unlisted)">U</span>
                                <span class="cin-part" style="background: #ff671f; color: #ffffff; box-shadow: 0 0 12px rgba(255,103,31,0.6);" title="5-Digit NIC Code (72200 = Software)">72200</span>
                                <span class="cin-part" style="background: #334155; color: #94a3b8;" title="State Code (DL = Delhi)">DL</span>
                                <span class="cin-part" style="background: #334155; color: #94a3b8;" title="Year of Incorporation">2023</span>
                                <span class="cin-part" style="background: #334155; color: #94a3b8;" title="Company Class (PTC = Private Limited)">PTC</span>
                                <span class="cin-part" style="background: #334155; color: #94a3b8;" title="6-Digit Registration Sequential Number">123456</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-warning small font-weight-bold">
                                <i class="fas fa-arrow-up"></i> Digits 2 to 6 (<code class="text-white font-weight-bold">72200</code>) map directly to NIC Subclass for Software &amp; IT Services.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Complete 21 Sections Explorer Grid -->
            <div class="bg-white rounded-xl shadow-sm border p-4 p-md-5 mb-5">
                <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-lg text-white" style="width: 36px; height: 36px; font-size: 16px; background: var(--primary-color);">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <h3 class="font-weight-bold text-dark mb-0" style="font-size: 22px; font-family: var(--font-heading);">
                        All 21 Broad Economic Sections (NIC 2025: Sections A to U)
                    </h3>
                </div>

                <div class="row">
                    <?php foreach ($sections_list as $sitem): ?>
                        <div class="col-lg-4 col-md-6 col-12 mb-3 d-flex">
                            <a href="nic.php?section=<?php echo $sitem['code']; ?>" class="section-grid-card w-100">
                                <span class="nic-code-badge nic-level-section" style="font-size: 15px; min-width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;">
                                    <?php echo $sitem['code']; ?>
                                </span>
                                <div style="flex: 1;">
                                    <div class="font-weight-bold text-dark mb-1" style="font-size: 14px; line-height: 1.35;">
                                        Section <?php echo $sitem['code']; ?>: <?php echo htmlspecialchars($sitem['title']); ?>
                                    </div>
                                    <span class="small text-primary font-weight-semibold d-inline-flex align-items-center">
                                        Browse Divisions &rarr;
                                    </span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Educational Infographic: How NIC Codes are Used -->
            <div class="bg-white rounded-xl shadow-sm border p-4 p-md-5 mb-5">
                <div class="row text-center mb-4 justify-content-center">
                    <div class="col-lg-8 col-12">
                        <span class="badge badge-primary px-3 py-1 rounded-pill text-uppercase font-weight-bold mb-2" style="background: var(--primary-light); color: var(--primary-color);">REGULATORY APPLICATIONS</span>
                        <h4 class="font-weight-bold text-dark mb-1" style="font-size: 22px; font-family: var(--font-heading);">Where NIC Activity Codes are Mandatory in India</h4>
                        <p class="text-muted small mb-0">National Industrial Classification codes are the legal foundation for business registration, licensing, and compliance across Indian government portals.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-12 mb-3">
                        <div class="p-3.5 rounded-xl border bg-light h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge badge-primary rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px;">1</span>
                                <h6 class="font-weight-bold text-dark mb-0" style="font-size: 15px;">MCA Company CIN</h6>
                            </div>
                            <p class="text-muted small mb-0" style="line-height: 1.55;">The 2nd to 6th digits of every 21-digit Corporate Identification Number (CIN) contain the 5-digit NIC code.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 mb-3">
                        <div class="p-3.5 rounded-xl border bg-light h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge badge-success rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px;">2</span>
                                <h6 class="font-weight-bold text-dark mb-0" style="font-size: 15px;">MSME Udyam Portal</h6>
                            </div>
                            <p class="text-muted small mb-0" style="line-height: 1.55;">Every Micro, Small &amp; Medium Enterprise must specify 2-digit, 4-digit, and 5-digit NIC codes during Udyam registration.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 mb-3">
                        <div class="p-3.5 rounded-xl border bg-light h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge badge-warning rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px;">3</span>
                                <h6 class="font-weight-bold text-dark mb-0" style="font-size: 15px;">GST Registration</h6>
                            </div>
                            <p class="text-muted small mb-0" style="line-height: 1.55;">NIC codes map your core business operations with corresponding Goods and Services HSN/SAC codes for taxation.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 mb-3">
                        <div class="p-3.5 rounded-xl border bg-light h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge badge-info rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px;">4</span>
                                <h6 class="font-weight-bold text-dark mb-0" style="font-size: 15px;">Banking &amp; Priority Loans</h6>
                            </div>
                            <p class="text-muted small mb-0" style="line-height: 1.55;">Financial institutions use NIC codes to determine priority sector lending eligibility and industrial credit limits.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="bg-white rounded-xl shadow-sm border p-4 p-md-5">
                <h4 class="font-weight-bold text-dark mb-3" style="font-size: 22px; font-family: var(--font-heading);">
                    Frequently Asked Questions on NIC 2025 Codes
                </h4>
                <div class="accordion" id="nicFaqAccordion">
                    <div class="border-bottom py-3.5">
                        <h6 class="font-weight-bold text-dark mb-1.5" style="font-size: 15.5px;">What is the National Industrial Classification (NIC)?</h6>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">NIC is an essential statistical standard developed by the Ministry of Statistics and Programme Implementation (MoSPI), Government of India, to uniformly categorize all economic and commercial activities across India.</p>
                    </div>
                    <div class="border-bottom py-3.5">
                        <h6 class="font-weight-bold text-dark mb-1.5" style="font-size: 15.5px;">How do I choose the correct NIC code for my startup or company?</h6>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">Use the search bar above to type your core business activity (e.g., <em>Software Development, Cloud Kitchen, Retail Apparel, Renewable Energy</em>). The tool will display the exact 2-digit Division, 4-digit Class, and 5/6-digit Sub-class codes to use on the MCA SPICe+ form and MSME portal.</p>
                    </div>
                    <div class="border-bottom py-3.5">
                        <h6 class="font-weight-bold text-dark mb-1.5" style="font-size: 15.5px;">Can a company have more than one NIC code?</h6>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">Yes. While your primary CIN embeds your main business activity, companies can register multiple secondary NIC codes in MSME Udyam registration and corporate MoA (Memorandum of Association) main and ancillary objectives.</p>
                    </div>
                    <div class="py-3.5">
                        <h6 class="font-weight-bold text-dark mb-1.5" style="font-size: 15.5px;">What is the difference between NIC Code and HSN/SAC Code?</h6>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">A NIC Code classifies the <em>industry activity or nature of business</em> (e.g. IT services, manufacturing, wholesale), whereas HSN (Harmonized System of Nomenclature) and SAC (Services Accounting Code) classify the specific <em>goods and services transacted</em> for GST invoicing and tax rates.</p>
                    </div>
                </div>
            </div>

        <?php endif; ?>

	</div>
</div>

<!-- Copy Toast Notification -->
<div id="copyToast" class="copy-toast">
    <i class="fas fa-check-circle text-success" style="font-size: 16px;"></i>
    <span id="copyToastText">NIC Code copied to clipboard!</span>
</div>

<script>
function copyNicCode(code, title) {
    var textToCopy = "NIC Code " + code + " - " + title;
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(textToCopy).then(function() {
            showToast("Copied: NIC " + code);
        }).catch(function() {
            fallbackCopy(textToCopy);
        });
    } else {
        fallbackCopy(textToCopy);
    }
}

function fallbackCopy(text) {
    var textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-999999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        showToast("Copied to clipboard!");
    } catch (err) {
        prompt("Copy this code:", text);
    }
    document.body.removeChild(textArea);
}

function showToast(msg) {
    var toast = document.getElementById('copyToast');
    var toastText = document.getElementById('copyToastText');
    if (toast && toastText) {
        toastText.textContent = msg;
        toast.classList.add('show');
        setTimeout(function() {
            toast.classList.remove('show');
        }, 2500);
    }
}

function filterChildTable() {
    var input = document.getElementById('childSearchInput');
    if (!input) return;
    var filter = input.value.toLowerCase();
    var rows = document.querySelectorAll('#childNicTable tbody tr.child-row');
    rows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        if (text.indexOf(filter) > -1) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<?php require_once('includes/footer.php'); ?>
