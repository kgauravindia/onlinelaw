<?php
    require_once('includes/config.php');
    require_once('includes/function.php');
    require_once('includes/captcha.php');

    // --------------------------------------------------------------------------
    // AJAX Endpoints for Dynamic Cascading Dropdowns
    // --------------------------------------------------------------------------
    if (isset($_GET['ajax_districts'])) {
        header('Content-Type: application/json');
        header('Cache-Control: public, max-age=86400'); // 24hr browser cache
        $st = trim($_GET['state'] ?? '');
        if (empty($st)) {
            echo json_encode(['status' => 'error', 'data' => []]);
            exit;
        }

        $safe_st = mysqli_real_escape_string($con, $st);
        $res = mysqli_query($con, "SELECT DISTINCT district_name FROM pincode WHERE state_name = '$safe_st' AND district_name != '' ORDER BY district_name ASC");
        $districts = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $districts[] = $row['district_name'];
            }
            mysqli_free_result($res);
        }

        echo json_encode(['status' => 'success', 'data' => $districts]);
        exit;
    }

    if (isset($_GET['ajax_regions'])) {
        header('Content-Type: application/json');
        header('Cache-Control: public, max-age=86400');
        $st = trim($_GET['state'] ?? '');
        $dist = trim($_GET['district'] ?? '');
        if (empty($st) || empty($dist)) {
            echo json_encode(['status' => 'error', 'data' => []]);
            exit;
        }

        $safe_st = mysqli_real_escape_string($con, $st);
        $safe_dist = mysqli_real_escape_string($con, $dist);
        $res = mysqli_query($con, "SELECT DISTINCT sub_district_name FROM pincode WHERE state_name = '$safe_st' AND district_name = '$safe_dist' AND sub_district_name != '' ORDER BY sub_district_name ASC");
        $regions = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $regions[] = $row['sub_district_name'];
            }
            mysqli_free_result($res);
        }

        echo json_encode(['status' => 'success', 'data' => $regions]);
        exit;
    }

    // --------------------------------------------------------------------------
    // Request Parameters
    // --------------------------------------------------------------------------
    $selected_state = isset($_GET['state']) ? trim($_GET['state']) : (isset($_POST['state']) ? trim($_POST['state']) : '');
    $selected_district = isset($_GET['district']) ? trim($_GET['district']) : (isset($_POST['district']) ? trim($_POST['district']) : '');
    $selected_region = isset($_GET['region']) ? trim($_GET['region']) : (isset($_POST['region']) ? trim($_POST['region']) : '');

    // Fetch All States for Initial Dropdown
    $all_states = $_SESSION['all_pincode_states'] ?? [];
    if (empty($all_states)) {
        $states_res = mysqli_query($con, "SELECT DISTINCT state_name FROM pincode WHERE state_name != '' ORDER BY state_name ASC");
        if ($states_res) {
            while ($sRow = mysqli_fetch_assoc($states_res)) {
                $all_states[] = $sRow['state_name'];
            }
            mysqli_free_result($states_res);
            $_SESSION['all_pincode_states'] = $all_states;
        }
    }

    // Fetch Districts if State is selected
    $districts_for_state = [];
    if (!empty($selected_state)) {
        $safe_st = mysqli_real_escape_string($con, $selected_state);
        $d_res = mysqli_query($con, "SELECT DISTINCT district_name FROM pincode WHERE state_name = '$safe_st' AND district_name != '' ORDER BY district_name ASC");
        if ($d_res) {
            while ($dRow = mysqli_fetch_assoc($d_res)) {
                $districts_for_state[] = $dRow['district_name'];
            }
        }
    }

    // Fetch Regions if District is selected
    $regions_for_district = [];
    if (!empty($selected_state) && !empty($selected_district)) {
        $safe_st = mysqli_real_escape_string($con, $selected_state);
        $safe_dist = mysqli_real_escape_string($con, $selected_district);
        $r_res = mysqli_query($con, "SELECT DISTINCT sub_district_name FROM pincode WHERE state_name = '$safe_st' AND district_name = '$safe_dist' AND sub_district_name != '' ORDER BY sub_district_name ASC");
        if ($r_res) {
            while ($rRow = mysqli_fetch_assoc($r_res)) {
                $regions_for_district[] = $rRow['sub_district_name'];
            }
        }
    }

    // --------------------------------------------------------------------------
    // Execute Search Query when Filter is Applied with Pagination
    // --------------------------------------------------------------------------
    $has_search = (!empty($selected_state));
    $results = ['count' => 0, 'data' => []];
    $total_records = 0;
    $per_page = isset($_GET['per_page']) ? max(5, min(200, intval($_GET['per_page']))) : 20;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $total_pages = 1;
    $unique_pincodes = [];
    $unique_offices = [];
    $total_unique_pincodes_count = 0;
    $total_unique_offices_count = 0;
    $captcha_error = false;

    if ($has_search) {
        if (!olaw_validate_search_captcha()) {
            $captcha_error = true;
        }

        if (!$captcha_error) {
            $where_clauses = [];
            $safe_st = mysqli_real_escape_string($con, $selected_state);
            $where_clauses[] = "state_name = '$safe_st'";

            if (!empty($selected_district)) {
                $safe_dist = mysqli_real_escape_string($con, $selected_district);
                $where_clauses[] = "district_name = '$safe_dist'";
            }

            if (!empty($selected_region)) {
                $safe_reg = mysqli_real_escape_string($con, $selected_region);
                $where_clauses[] = "(sub_district_name = '$safe_reg' OR locality_name LIKE '%$safe_reg%')";
            }

            $where_sql = implode(' AND ', $where_clauses);

            // Get total count
            $count_res = mysqli_query($con, "SELECT COUNT(*) as total, COUNT(DISTINCT pincode) as total_pincodes, COUNT(DISTINCT office_name) as total_offices FROM pincode WHERE $where_sql");
            if ($count_res && ($cRow = mysqli_fetch_assoc($count_res))) {
                $total_records = (int)$cRow['total'];
                $total_unique_pincodes_count = (int)$cRow['total_pincodes'];
                $total_unique_offices_count = (int)$cRow['total_offices'];
            }

            $total_pages = max(1, (int)ceil($total_records / $per_page));
            if ($page > $total_pages) {
                $page = $total_pages;
            }
            $offset = ($page - 1) * $per_page;

            // Fetch paginated results
            $results = direct_sql("SELECT * FROM pincode WHERE $where_sql ORDER BY district_name ASC, sub_district_name ASC, locality_name ASC LIMIT $offset, $per_page");

            // Fetch top unique pincodes for quick badge pills
            $p_res = mysqli_query($con, "SELECT DISTINCT pincode FROM pincode WHERE $where_sql ORDER BY pincode ASC LIMIT 24");
            if ($p_res) {
                while ($pRow = mysqli_fetch_assoc($p_res)) {
                    $unique_pincodes[] = $pRow['pincode'];
                }
            }
        }
    }

    // --------------------------------------------------------------------------
    // Dynamic SEO Metadata & Schema.org Graphs
    // --------------------------------------------------------------------------
    $schema_graphs = [];
    $faq_items = [];

    $canonical_params = [];
    if (!empty($selected_state)) $canonical_params['state'] = $selected_state;
    if (!empty($selected_district)) $canonical_params['district'] = $selected_district;
    if (!empty($selected_region)) $canonical_params['region'] = $selected_region;

    $canonical_url = $base_url . "pincode-finder" . (!empty($canonical_params) ? '?' . http_build_query($canonical_params) : '');

    if (!empty($selected_region) && !empty($selected_district)) {
        $page_title = "PIN Codes in $selected_region, $selected_district ($selected_state) - Post Office Directory (2026) | Online Law";
        $meta_desc = "Find all PIN codes and post offices for $selected_region region in $selected_district district, $selected_state. Complete postal details with delivery status. Updated 2026.";
        $meta_keywords = "$selected_region PIN Code, $selected_district Pincode, $selected_region Post Office, $selected_state Postal Codes, India Post $selected_region";
    } elseif (!empty($selected_district)) {
        $page_title = "PIN Codes in $selected_district District ($selected_state) - All Post Offices (2026) | Online Law";
        $meta_desc = "Explore all " . count($unique_pincodes) . " PIN codes and " . count($unique_offices) . " post offices in $selected_district district, $selected_state. Full directory with localities, talukas and postal zones.";
        $meta_keywords = "$selected_district PIN Code, $selected_district Post Offices, $selected_state $selected_district Pincodes, All Pincodes in $selected_district";
    } elseif (!empty($selected_state)) {
        $page_title = "PIN Codes in $selected_state - District & Post Office Finder (2026) | Online Law";
        $meta_desc = "Find PIN codes for all districts in $selected_state. Browse post offices, sorting districts, taluks and delivery codes on Online Law India Postal Directory.";
        $meta_keywords = "$selected_state PIN Code, $selected_state Postal Codes, All Pincodes in $selected_state, $selected_state Post Office Directory";
    } else {
        $page_title = "Find PIN Code by State, District & Region | Indian Postal Directory (2026) - Online Law";
        $meta_desc = "Select your State, District, and Region to instantly find 6-digit Indian PIN codes and post office details across all 28 states and 8 UTs. Real-time 2026 directory.";
        $meta_keywords = "PIN Code Finder, Find PIN Code by State, District Pincode Search, Region Postal Code Finder, Select State Select District Select Region Pincode, India Post Lookup";
    }

    // BreadcrumbList Schema
    $breadcrumb_items = [
        ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
        ["@type" => "ListItem", "position" => 2, "name" => "PIN Code Finder", "item" => $base_url . "pincode-finder"]
    ];
    if (!empty($selected_state)) {
        $breadcrumb_items[] = ["@type" => "ListItem", "position" => 3, "name" => $selected_state, "item" => $base_url . "pincode-finder?state=" . urlencode($selected_state)];
    }
    if (!empty($selected_district)) {
        $breadcrumb_items[] = ["@type" => "ListItem", "position" => 4, "name" => $selected_district, "item" => $canonical_url];
    }
    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => $breadcrumb_items
    ];

    // FAQ items
    if (!empty($selected_district)) {
        $faq_items = [
            [
                "q" => "How many PIN codes and post offices are there in $selected_district, $selected_state?",
                "a" => "There are " . count($unique_pincodes) . " unique 6-digit PIN code(s) and " . count($unique_offices) . " post office(s) indexed in $selected_district district, $selected_state."
            ],
            [
                "q" => "What is the primary PIN code for $selected_district district headquarter?",
                "a" => "The primary PIN codes serving $selected_district include: " . implode(', ', array_slice($unique_pincodes, 0, 5)) . "."
            ],
            [
                "q" => "How do I find a specific village or colony PIN code in $selected_district?",
                "a" => "Select the specific Region / Sub-District from the dropdown above, or type the village/colony name in our Locality Search tool."
            ]
        ];
    } else {
        $faq_items = [
            [
                "q" => "How does the 3-step State & District PIN Code Finder work?",
                "a" => "Select your State first. The District dropdown will automatically populate with all districts in that state. Then select your District to view all matching sub-districts, post offices, and 6-digit PIN codes."
            ],
            [
                "q" => "Can I filter down to a specific sub-district or region?",
                "a" => "Yes! Once you pick a State and District, the Region / Sub-District dropdown lets you narrow down results to a specific taluka, tehsil, or urban zone."
            ],
            [
                "q" => "Are both urban and rural post offices covered in this directory?",
                "a" => "Yes, our database indexes all categories of India Post offices: Head Post Offices (HO), Sub Post Offices (SO), and rural Branch Post Offices (BO)."
            ]
        ];
    }

    if (!empty($faq_items)) {
        $faq_schema = [
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => []
        ];
        foreach ($faq_items as $item) {
            $faq_schema["mainEntity"][] = [
                "@type" => "Question",
                "name" => $item["q"],
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => $item["a"]
                ]
            ];
        }
        $schema_graphs[] = $faq_schema;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo htmlspecialchars($meta_desc); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
<meta name="author" content="OfferPlant">
<title><?php echo htmlspecialchars($page_title); ?></title>
<link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">

<!-- Open Graph / Social Meta -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="Online Law">
<meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($meta_desc); ?>">
<meta property="og:image" content="<?php echo $base_url; ?>images/favicon.png">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($meta_desc); ?>">
<meta name="twitter:image" content="<?php echo $base_url; ?>images/favicon.png">

<!-- Schema.org JSON-LD Structured Data -->
<?php if (!empty($schema_graphs)): ?>
<script type="application/ld+json">
<?php echo json_encode(count($schema_graphs) === 1 ? $schema_graphs[0] : ["@context" => "https://schema.org", "@graph" => $schema_graphs], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>
<?php endif; ?>

<style>
    /* Ultra-Modern Design System */
    .hero-finder-banner {
        background-color: #ffffff !important;
        background: linear-gradient(180deg, #ffffff 0%, #fafbfc 55%, #f1f5f9 100%) !important;
        color: #0f172a !important;
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid #e2e8f0;
        padding: clamp(36px, 5vw, 65px) 0 clamp(48px, 6vw, 75px);
    }
    .hero-finder-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(15, 23, 42, 0.05) 1px, transparent 1px);
        background-size: 24px 24px;
        opacity: 0.7;
        pointer-events: none;
    }
    .finder-glow-orb-1 {
        position: absolute;
        width: 450px;
        height: 450px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 103, 31, 0.08) 0%, rgba(255, 103, 31, 0) 70%);
        top: -120px;
        left: -100px;
        pointer-events: none;
    }
    .finder-glow-orb-2 {
        position: absolute;
        width: 450px;
        height: 450px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(4, 106, 56, 0.07) 0%, rgba(4, 106, 56, 0) 70%);
        bottom: -140px;
        right: -80px;
        pointer-events: none;
    }

    .finder-console-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.12), 0 4px 12px rgba(15, 23, 42, 0.04);
        margin-top: -45px;
        position: relative;
        z-index: 20;
    }

    .form-select-custom {
        height: 52px;
        border-radius: 14px;
        border: 1.5px solid #cbd5e1;
        font-size: 14.5px;
        font-weight: 600;
        color: #1e293b;
        background-color: #f8fafc;
        transition: all 0.2s ease;
        padding: 0 14px;
    }
    .form-select-custom:focus {
        background-color: #ffffff;
        border-color: #ff671f;
        box-shadow: 0 0 0 4px rgba(255, 103, 31, 0.14);
    }

    .step-badge-num {
        width: 24px;
        height: 24px;
        background: #fff7ed;
        color: #ff671f;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
        margin-right: 6px;
    }

    .result-spec-tile {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 18px;
        transition: all 0.2s ease;
        height: 100%;
    }
    .result-spec-tile:hover {
        border-color: #93c5fd;
        background: #f8fafc;
        transform: translateY(-2px);
    }

    .pincode-pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #fff7ed;
        color: #c2410c;
        border: 1.5px solid #ffedd5;
        border-radius: 8px;
        padding: 5px 12px;
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }
    .pincode-pill-badge:hover {
        background: #ff671f;
        color: #ffffff !important;
        border-color: #ff671f;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(255, 103, 31, 0.25);
    }

    .pincode-card-mobile {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 14px;
        box-shadow: 0 2px 6px -1px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
    }
    .pincode-card-mobile:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    /* FAQ Card */
    .faq-card-seo {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        margin-bottom: 12px;
        overflow: hidden;
        transition: all 0.2s ease;
    }
    .faq-card-seo .faq-header-btn {
        width: 100%;
        padding: 16px 20px;
        text-align: left;
        background: #ffffff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 700;
        font-size: 15px;
        color: #1e293b;
        cursor: pointer;
    }
    .faq-card-seo .faq-header-btn:hover {
        background: #fff7ed;
        color: #ff671f;
    }
    .faq-card-seo .faq-body-text {
        padding: 0 20px 16px 20px;
        font-size: 14px;
        line-height: 1.6;
        color: #475569;
    }

    /* Toast */
    .law-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #071329;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        box-shadow: 0 14px 30px -5px rgba(0, 0, 0, 0.4);
        z-index: 999999;
        display: flex;
        align-items: center;
        gap: 10px;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
        border: 1px solid rgba(255, 255, 255, 0.18);
    }
    .law-toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    /* Pagination styles */
    .finder-pagination .page-item .page-link {
        color: #334155;
        border: 1px solid #e2e8f0;
        border-radius: 8px !important;
        margin: 0 3px;
        padding: 6px 12px;
        font-weight: 600;
        font-size: 13.5px;
        transition: all 0.2s ease;
    }
    .finder-pagination .page-item.active .page-link {
        background-color: #ff671f;
        border-color: #ff671f;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(255, 103, 31, 0.25);
    }
    .finder-pagination .page-item .page-link:hover:not(.active) {
        background-color: #fff7ed;
        color: #ea580c;
        border-color: #fdba74;
    }
    .finder-pagination .page-item.disabled .page-link {
        color: #94a3b8;
        background-color: #f8fafc;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }

    @media (max-width: 767px) {
        .finder-console-card {
            margin-top: -24px;
            padding: 16px 14px !important;
            border-radius: 18px !important;
        }
        .desktop-only { display: none !important; }
        .mobile-only { display: block !important; }
        .form-select-custom {
            height: 48px !important;
            font-size: 13.5px !important;
        }
        .law-toast {
            left: 14px;
            right: 14px;
            bottom: 14px;
        }
    }
    @media (min-width: 768px) {
        .mobile-only { display: none !important; }
        .desktop-only { display: block !important; }
    }
</style>

<?php require_once('includes/header.php'); ?>

<!-- ==========================================================================
     HERO MODERN BANNER SECTION
     ========================================================================== -->
<section class="hero-finder-banner">
    <div class="finder-glow-orb-1"></div>
    <div class="finder-glow-orb-2"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-12 text-center">

                <!-- Breadcrumbs -->
                <div class="d-flex justify-content-center">
                    <nav class="d-flex align-items-center flex-wrap gap-2 mb-3" style="font-size: 13.5px; color: #64748b;" aria-label="breadcrumb">
                        <a href="<?php echo $base_url; ?>index" style="color: #475569; text-decoration: none;"><i class="fas fa-home mr-1"></i> Home</a>
                        <span class="mx-1">/</span>
                        <a href="<?php echo $base_url; ?>pincode-finder" style="color: #475569; text-decoration: none;">PIN Code Finder</a>
                        <?php if (!empty($selected_state)): ?>
                            <span class="mx-1">/</span>
                            <span class="text-dark font-weight-bold"><?php echo htmlspecialchars($selected_state); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($selected_district)): ?>
                            <span class="mx-1">/</span>
                            <span class="text-dark font-weight-bold"><?php echo htmlspecialchars($selected_district); ?></span>
                        <?php endif; ?>
                    </nav>
                </div>

                <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill mb-3" style="background: #ffffff; border: 1.5px solid #fed7aa; color: #c2410c; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; box-shadow: 0 4px 14px rgba(255, 103, 31, 0.08);">
                    <i class="fas fa-map-marked-alt mr-1.5" style="color: #ff671f;"></i> CASCADING PIN CODE DIRECTORY 2026
                </div>

                <h1 class="text-dark font-weight-bold mb-2" style="font-size: clamp(24px, 3.8vw, 36px); letter-spacing: -0.5px; line-height: 1.25;">
                    Find Your <span style="background: linear-gradient(135deg, #ff671f 0%, #ea580c 45%, #046a38 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">PIN Code</span>
                </h1>
                <p class="mb-0" style="color: #475569 !important; font-size: 15px; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Select your <strong>State</strong>, <strong>District</strong>, and <strong>Region</strong> to instantly uncover official 6-digit postal index numbers and post office details.
                </p>

            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     MAIN BODY & CASCADING FILTER CONSOLE
     ========================================================================== -->
<div class="full-row py-4" style="background-color: #f8fafc; position: relative; z-index: 10;">
    <div class="container">

        <!-- 3-Step Cascading Dropdown Console Card -->
        <div class="finder-console-card p-4 p-md-4 mb-4">
            <form id="pincodeFinderForm" action="<?php echo $base_url; ?>pincode-finder" method="GET">
                <div class="row align-items-end">

                    <!-- Step 1: Select State -->
                    <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-dark mb-1.5 d-flex align-items-center">
                            <span class="step-badge-num">1</span>
                            <span>Select State</span>
                        </label>
                        <select name="state" id="stateSelect" class="form-control form-select-custom" required onchange="onStateChange();">
                            <option value="">-- Choose State / UT --</option>
                            <?php foreach ($all_states as $st): ?>
                                <option value="<?php echo htmlspecialchars($st); ?>" <?php echo ($selected_state === $st) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($st); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Step 2: Select District -->
                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-dark mb-1.5 d-flex align-items-center">
                            <span class="step-badge-num">2</span>
                            <span>Select District</span>
                        </label>
                        <select name="district" id="districtSelect" class="form-control form-select-custom" onchange="onDistrictChange();" <?php echo empty($selected_state) ? 'disabled' : ''; ?>>
                            <option value="">-- Choose District --</option>
                            <?php if (!empty($districts_for_state)): ?>
                                <?php foreach ($districts_for_state as $dist): ?>
                                    <option value="<?php echo htmlspecialchars($dist); ?>" <?php echo ($selected_district === $dist) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dist); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Step 3: Select Region -->
                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-dark mb-1.5 d-flex align-items-center">
                            <span class="step-badge-num">3</span>
                            <span>Select Region / Taluk</span>
                        </label>
                        <select name="region" id="regionSelect" class="form-control form-select-custom" <?php echo empty($selected_district) ? 'disabled' : ''; ?>>
                            <option value="">-- All Regions / Taluks --</option>
                            <?php if (!empty($regions_for_district)): ?>
                                <?php foreach ($regions_for_district as $reg): ?>
                                    <option value="<?php echo htmlspecialchars($reg); ?>" <?php echo ($selected_region === $reg) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($reg); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-lg-2 col-md-6">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold d-flex align-items-center justify-content-center shadow-sm" style="height: 52px; border-radius: 14px; font-size: 15px;">
                            <i class="fas fa-search mr-2"></i> Find PIN
                        </button>
                    </div>

                    <div class="col-12 mt-3 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap" style="border-color: #f1f5f9 !important; gap: 8px;">
                        <div class="d-inline-flex align-items-center flex-wrap" style="gap: 8px;">
                            <span class="small font-weight-bold text-muted"><i class="fas fa-shield-alt text-primary mr-1"></i> Security Code:</span>
                            <?php echo olaw_render_search_captcha_widget('_pinfinder'); ?>
                        </div>
                        <span class="small text-muted"><i class="fas fa-lock mr-1"></i> Quick session verification for seamless browsing.</span>
                    </div>

                </div>
            </form>

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-3 border-top" style="border-color: #f1f5f9 !important;">
                <div class="small text-muted font-weight-medium">
                    <i class="fas fa-info-circle text-primary mr-1"></i> Quick links: 
                    <a href="<?php echo $base_url; ?>pincode" class="text-primary font-weight-bold ml-1">Search by 6-digit Code</a> &bull; 
                    <a href="<?php echo $base_url; ?>pincode-by-locality" class="text-primary font-weight-bold ml-1">Search by Locality / Village</a>
                </div>
                <?php if ($has_search): ?>

            <?php if (!empty($captcha_error)): ?>
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" style="border-radius: 14px; background: #fffbeb; color: #b45309; border-left: 5px solid #f59e0b !important; padding: 18px 24px;">
                <i class="fas fa-shield-alt fa-2x mr-3 text-warning"></i>
                <div>
                    <h6 class="font-weight-bold mb-1" style="color: #92400e;">Security Verification Required</h6>
                    <p class="small mb-0 text-muted">Please enter the security verification code displayed above to view postal records.</p>
                </div>
            </div>
            <?php endif; ?>
                    <a href="<?php echo $base_url; ?>pincode-finder" class="btn btn-sm btn-outline-danger font-weight-bold px-3 py-1" style="border-radius: 20px; font-size: 12px;">
                        <i class="fas fa-times mr-1"></i> Reset Filters
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- ======================================================================
             SEARCH RESULTS SECTION
             ====================================================================== -->
        <?php if ($has_search): ?>

            <?php if (!empty($captcha_error)): ?>
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" style="border-radius: 14px; background: #fffbeb; color: #b45309; border-left: 5px solid #f59e0b !important; padding: 18px 24px;">
                <i class="fas fa-shield-alt fa-2x mr-3 text-warning"></i>
                <div>
                    <h6 class="font-weight-bold mb-1" style="color: #92400e;">Security Verification Required</h6>
                    <p class="small mb-0 text-muted">Please enter the security verification code displayed above to view postal records.</p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($results['count'] > 0): ?>

                <!-- Dynamic Results Overview Header Card -->
                <div class="p-4 rounded-xl mb-4 bg-white shadow-sm border" style="border-radius: 20px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <span class="badge badge-primary px-3 py-1 rounded-pill font-weight-bold text-uppercase mb-1" style="font-size: 11px;">
                                <i class="fas fa-check-circle mr-1"></i> Official Postal Results
                            </span>
                            <h2 class="font-weight-bold text-dark mb-0" style="font-size: clamp(20px, 2.8vw, 26px); letter-spacing: -0.5px;">
                                <?php 
                                    $heading_parts = [];
                                    if (!empty($selected_region)) $heading_parts[] = $selected_region;
                                    if (!empty($selected_district)) $heading_parts[] = $selected_district;
                                    if (!empty($selected_state)) $heading_parts[] = $selected_state;
                                    echo htmlspecialchars(implode(', ', $heading_parts));
                                ?>
                            </h2>
                        </div>
                        <span class="badge badge-success px-3 py-2 rounded-pill font-weight-bold mt-2 mt-sm-0" style="font-size: 13px;">
                            <?php echo number_format($total_records); ?> Location Record(s) Found
                        </span>
                    </div>

                    <!-- 4 Summary Tiles -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                            <div class="result-spec-tile">
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">State / UT</small>
                                <strong class="text-dark" style="font-size: 14.5px;"><?php echo htmlspecialchars($selected_state); ?></strong>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                            <div class="result-spec-tile">
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">District</small>
                                <strong class="text-dark" style="font-size: 14.5px;"><?php echo htmlspecialchars($selected_district ?: 'All Districts'); ?></strong>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                            <div class="result-spec-tile">
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">Post Offices</small>
                                <strong class="text-primary" style="font-size: 14.5px;"><?php echo number_format($total_unique_offices_count); ?> Post Office(s)</strong>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                            <div class="result-spec-tile">
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">Unique PIN Codes</small>
                                <strong class="text-success" style="font-size: 14.5px;"><?php echo number_format($total_unique_pincodes_count); ?> PIN Code(s)</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Clickable Unique PIN Code Badges -->
                    <?php if (!empty($unique_pincodes)): ?>
                        <div class="mt-2 pt-3 border-top">
                            <span class="small font-weight-bold text-muted text-uppercase mr-2 d-inline-block mb-1">
                                <i class="fas fa-tags text-primary mr-1"></i> Area PIN Codes (Sample):
                            </span>
                            <div class="d-inline-flex flex-wrap gap-1.5" style="gap: 6px;">
                                <?php foreach ($unique_pincodes as $pCode): ?>
                                    <a href="<?php echo $base_url; ?>pincode?pincode=<?php echo $pCode; ?>" class="pincode-pill-badge" title="View details for PIN Code <?php echo $pCode; ?>">
                                        <span><?php echo htmlspecialchars($pCode); ?></span>
                                        <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Desktop DataTable View -->
                <div class="bg-white rounded-xl shadow-sm border mb-4 p-4 desktop-only" style="border-radius: 20px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="small text-muted font-weight-medium">
                            Showing <strong class="text-dark"><?php echo number_format($offset + 1); ?></strong> to <strong class="text-dark"><?php echo number_format(min($offset + $per_page, $total_records)); ?></strong> of <strong class="text-dark"><?php echo number_format($total_records); ?></strong> records
                        </div>
                        <div class="small text-muted">
                            Page <strong class="text-primary"><?php echo $page; ?></strong> of <?php echo $total_pages; ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="finderDataTable" style="width: 100%;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted">No</th>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted">Locality / Village</th>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted">Post Office</th>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted">Sub-District / Taluk</th>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted">District</th>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted">State</th>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted text-right">PIN Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $idx = $offset + 1;
                                foreach ($results['data'] as $r): 
                                ?>
                                    <tr>
                                        <td class="px-3 py-3 align-middle text-muted small"><?php echo $idx++; ?></td>
                                        <td class="px-3 py-3 align-middle font-weight-bold text-dark"><?php echo htmlspecialchars($r['locality_name']); ?></td>
                                        <td class="px-3 py-3 align-middle"><?php echo htmlspecialchars($r['office_name']); ?></td>
                                        <td class="px-3 py-3 align-middle small text-muted"><?php echo htmlspecialchars($r['sub_district_name'] ?: '-'); ?></td>
                                        <td class="px-3 py-3 align-middle small text-muted"><?php echo htmlspecialchars($r['district_name']); ?></td>
                                        <td class="px-3 py-3 align-middle small text-muted"><?php echo htmlspecialchars($r['state_name']); ?></td>
                                        <td class="px-3 py-3 align-middle text-right text-nowrap">
                                            <a href="<?php echo $base_url; ?>pincode?pincode=<?php echo $r['pincode']; ?>" class="badge badge-primary px-2.5 py-1.5 font-weight-bold mr-1" style="font-size: 13px; text-decoration: none;">
                                                <?php echo htmlspecialchars($r['pincode']); ?> &rarr;
                                            </a>
                                            <button type="button" class="btn btn-sm btn-light border text-muted px-2 py-1" onclick="copyFinderText('<?php echo htmlspecialchars($r['pincode']); ?>');" title="Copy PIN Code">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card List View -->
                <div class="mobile-only">
                    <div class="p-3 mb-3 bg-white rounded-xl border shadow-sm d-flex justify-content-between align-items-center" style="border-radius: 14px;">
                        <span class="small font-weight-bold text-dark">
                            Showing <?php echo number_format($offset + 1); ?>&ndash;<?php echo number_format(min($offset + $per_page, $total_records)); ?> of <?php echo number_format($total_records); ?>
                        </span>
                        <span class="badge badge-primary font-weight-bold">
                            Page <?php echo $page; ?> / <?php echo $total_pages; ?>
                        </span>
                    </div>

                    <?php 
                    $mIndex = $offset + 1;
                    foreach ($results['data'] as $r): 
                    ?>
                        <div class="pincode-card-mobile">
                            <div class="d-flex justify-content-between align-items-start mb-2.5">
                                <div class="d-flex align-items-center">
                                    <span class="mr-2 text-muted font-weight-bold small">#<?php echo $mIndex++; ?></span>
                                    <h5 class="mb-0 font-weight-bold text-dark" style="font-size: 16px;"><?php echo htmlspecialchars($r['locality_name']); ?></h5>
                                </div>
                                <a href="<?php echo $base_url; ?>pincode?pincode=<?php echo $r['pincode']; ?>" class="badge badge-primary px-2.5 py-1.5 font-weight-bold" style="font-size: 13px; text-decoration: none;">
                                    <?php echo htmlspecialchars($r['pincode']); ?>
                                </a>
                            </div>

                            <div class="row no-gutters mb-2">
                                <div class="col-6 mb-2">
                                    <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">Post Office</small>
                                    <p class="mb-0 text-dark" style="font-size: 13px; font-weight: 500;"><?php echo htmlspecialchars($r['office_name']); ?></p>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">District</small>
                                    <p class="mb-0 text-dark" style="font-size: 13px; font-weight: 500;"><?php echo htmlspecialchars($r['district_name']); ?></p>
                                </div>
                                <div class="col-6">
                                    <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">Taluk / Region</small>
                                    <p class="mb-0 text-dark" style="font-size: 13px;"><?php echo htmlspecialchars($r['sub_district_name'] ?: '-'); ?></p>
                                </div>
                                <div class="col-6">
                                    <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">State</small>
                                    <p class="mb-0 text-dark" style="font-size: 13px;"><?php echo htmlspecialchars($r['state_name']); ?></p>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <a href="<?php echo $base_url; ?>pincode?pincode=<?php echo $r['pincode']; ?>" class="btn btn-xs btn-outline-primary font-weight-bold px-3 py-1" style="border-radius: 8px; font-size: 12px;">
                                    View Full Details &rarr;
                                </a>
                                <button type="button" class="btn btn-xs btn-light border text-muted px-2.5 py-1" style="border-radius: 8px; font-size: 12px;" onclick="copyFinderText('<?php echo htmlspecialchars($r['pincode']); ?>');">
                                    <i class="far fa-copy mr-1"></i> Copy PIN
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Server-Side Pagination Bar -->
                <?php if ($total_pages > 1): ?>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 p-3.5 bg-white rounded-xl shadow-sm border" style="border-radius: 18px;">
                        <div class="text-muted small mb-3 mb-md-0 font-weight-medium">
                            Showing <strong class="text-dark"><?php echo number_format($offset + 1); ?></strong> &ndash; <strong class="text-dark"><?php echo number_format(min($offset + $per_page, $total_records)); ?></strong> of <strong class="text-dark"><?php echo number_format($total_records); ?></strong> locations 
                            <span class="badge badge-light border ml-1 px-2 py-1 text-muted">Page <?php echo $page; ?> / <?php echo $total_pages; ?></span>
                        </div>
                        
                        <nav aria-label="PIN Code Finder Page navigation">
                            <ul class="pagination pagination-sm finder-pagination mb-0 flex-wrap justify-content-center">
                                <?php
                                    $p_params = [];
                                    if (!empty($selected_state)) $p_params['state'] = $selected_state;
                                    if (!empty($selected_district)) $p_params['district'] = $selected_district;
                                    if (!empty($selected_region)) $p_params['region'] = $selected_region;
                                    if ($per_page != 20) $p_params['per_page'] = $per_page;

                                    $make_p_url = function($pNum) use ($base_url, $p_params) {
                                        $arr = $p_params;
                                        $arr['page'] = $pNum;
                                        return $base_url . 'pincode-finder?' . http_build_query($arr);
                                    };

                                    // Prev
                                    if ($page > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($make_p_url($page - 1)) . '">&laquo; Prev</a></li>';
                                    } else {
                                        echo '<li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>';
                                    }

                                    // Window
                                    $p_window = 2;
                                    $start_p = max(1, $page - $p_window);
                                    $end_p = min($total_pages, $page + $p_window);

                                    if ($start_p > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($make_p_url(1)) . '">1</a></li>';
                                        if ($start_p > 2) {
                                            echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                                        }
                                    }

                                    for ($i = $start_p; $i <= $end_p; $i++) {
                                        if ($i == $page) {
                                            echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                                        } else {
                                            echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($make_p_url($i)) . '">' . $i . '</a></li>';
                                        }
                                    }

                                    if ($end_p < $total_pages) {
                                        if ($end_p < $total_pages - 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                                        }
                                        echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($make_p_url($total_pages)) . '">' . $total_pages . '</a></li>';
                                    }

                                    // Next
                                    if ($page < $total_pages) {
                                        echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($make_p_url($page + 1)) . '">Next &raquo;</a></li>';
                                    } else {
                                        echo '<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>';
                                    }
                                ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="bg-white rounded-xl shadow-sm border py-5 text-center mb-4" style="border-radius: 20px;">
                    <div class="card-body">
                        <div class="mx-auto mb-3 text-danger" style="width: 54px; height: 54px; background: #fef2f2; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2">No PIN Codes Found</h4>
                        <p class="text-muted mb-4" style="max-width: 480px; margin: 0 auto;">No post offices found for the selected State/District/Region combination. Try selecting a different district or reset filters.</p>
                        <a href="<?php echo $base_url; ?>pincode-finder" class="btn btn-primary font-weight-bold px-4" style="border-radius: 12px;">Reset Filters</a>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>

            <!-- ==================================================================
                 DEFAULT VIEW: 3-STEP VISUAL HOW IT WORKS & POPULAR STATES
                 ================================================================== -->
            <div class="row mb-5">
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="p-4 bg-white rounded-xl border h-100 shadow-sm" style="border-radius: 18px;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle font-weight-bold text-white mr-2.5" style="width: 34px; height: 34px; background: #ff671f; font-size: 14px;">1</span>
                            <h5 class="font-weight-bold text-dark mb-0" style="font-size: 17px;">Select Your State</h5>
                        </div>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            Pick any of India's 28 States or 8 Union Territories from the dropdown above. The district list will load instantly.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="p-4 bg-white rounded-xl border h-100 shadow-sm" style="border-radius: 18px;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle font-weight-bold text-white mr-2.5" style="width: 34px; height: 34px; background: #002b7f; font-size: 14px;">2</span>
                            <h5 class="font-weight-bold text-dark mb-0" style="font-size: 17px;">Select Your District</h5>
                        </div>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            Choose your administrative district to view all postal divisions and sorting hubs operating in that jurisdiction.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-12 mb-3">
                    <div class="p-4 bg-white rounded-xl border h-100 shadow-sm" style="border-radius: 18px;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle font-weight-bold text-white mr-2.5" style="width: 34px; height: 34px; background: #046a38; font-size: 14px;">3</span>
                            <h5 class="font-weight-bold text-dark mb-0" style="font-size: 17px;">Select Region &amp; Get PIN</h5>
                        </div>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            Filter down to your specific sub-district, tehsil, or taluk to get the exact 6-digit postal code for accurate mail routing.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Popular Indian States Quick Navigation -->
            <div class="p-4 bg-white rounded-xl border shadow-sm mb-5" style="border-radius: 22px;">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1" style="font-size: 19px;">
                            <i class="fas fa-map-marked text-primary mr-1.5"></i> Browse PIN Codes by State
                        </h4>
                        <p class="text-muted small mb-0">Click any state to quickly load its districts and postal index directories.</p>
                    </div>
                </div>
                <div class="row">
                    <?php 
                    $popular_states = [
                        'MAHARASHTRA', 'DELHI', 'UTTAR PRADESH', 'KARNATAKA',
                        'TAMIL NADU', 'GUJARAT', 'WEST BENGAL', 'TELANGANA',
                        'RAJASTHAN', 'KERALA', 'BIHAR', 'MADHYA PRADESH',
                        'HARYANA', 'PUNJAB', 'ANDHRA PRADESH', 'ODISHA'
                    ];
                    foreach ($popular_states as $pst): 
                    ?>
                        <div class="col-lg-3 col-md-4 col-6 mb-2.5">
                            <a href="<?php echo $base_url; ?>pincode-finder?state=<?php echo urlencode($pst); ?>" class="p-2.5 rounded border d-flex align-items-center justify-content-between text-decoration-none text-dark bg-light" style="border-radius: 12px; transition: all 0.2s ease;" onmouseover="this.style.background='#fff7ed'; this.style.color='#ff671f'; this.style.borderColor='#fdba74';" onmouseout="this.style.background='#f8fafc'; this.style.color='#1e293b'; this.style.borderColor='#e2e8f0';">
                                <span class="font-weight-bold small"><?php echo htmlspecialchars($pst); ?></span>
                                <i class="fas fa-chevron-right text-muted" style="font-size: 11px;"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php endif; ?>

        <!-- ======================================================================
             SEO FAQ ACCORDION SECTION
             ====================================================================== -->
        <?php if (!empty($faq_items)): ?>
            <div class="faq-seo-section mt-4 pt-4 border-top">
                <div class="d-flex align-items-center mb-4">
                    <div style="width: 40px; height: 40px; background: #fdf2f8; color: #db2777; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div>
                        <h4 class="mb-0" style="font-family: var(--font-heading); font-weight: 700; color: #1e293b; font-size: 20px;">Frequently Asked Questions</h4>
                        <p class="mb-0 text-muted" style="font-size: 13px;">Frequently asked questions about finding PIN codes by state, district, and region</p>
                    </div>
                </div>

                <div class="accordion" id="finderFaqAccordion">
                    <?php foreach ($faq_items as $fIndex => $faq): ?>
                        <div class="faq-card-seo">
                            <button class="faq-header-btn" type="button" data-toggle="collapse" data-target="#finderFaqCollapse<?php echo $fIndex; ?>" aria-expanded="<?php echo $fIndex === 0 ? 'true' : 'false'; ?>" aria-controls="finderFaqCollapse<?php echo $fIndex; ?>">
                                <span><?php echo htmlspecialchars($faq['q']); ?></span>
                                <i class="fas fa-chevron-down text-muted" style="font-size: 12px;"></i>
                            </button>
                            <div id="finderFaqCollapse<?php echo $fIndex; ?>" class="collapse <?php echo $fIndex === 0 ? 'show' : ''; ?>" data-parent="#finderFaqAccordion">
                                <div class="faq-body-text">
                                    <?php echo htmlspecialchars($faq['a']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Interactive Scripts & AJAX Cascading Dropdowns -->
<script>
// Client-side Memory Cache for Zero Duplicate Requests
const pincodeDistrictCache = {};
const pincodeRegionCache = {};

function onStateChange() {
    const stateVal = document.getElementById('stateSelect').value;
    const districtSelect = document.getElementById('districtSelect');
    const regionSelect = document.getElementById('regionSelect');

    districtSelect.innerHTML = '<option value="">Loading districts...</option>';
    districtSelect.disabled = true;
    regionSelect.innerHTML = '<option value="">-- All Regions / Taluks --</option>';
    regionSelect.disabled = true;

    if (!stateVal) {
        districtSelect.innerHTML = '<option value="">-- Choose District --</option>';
        return;
    }

    // Check Client-side Cache
    if (pincodeDistrictCache[stateVal]) {
        renderDistrictOptions(pincodeDistrictCache[stateVal]);
        return;
    }

    fetch('<?php echo $base_url; ?>pincode-finder?ajax_districts=1&state=' + encodeURIComponent(stateVal))
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.data) {
                pincodeDistrictCache[stateVal] = data.data;
                renderDistrictOptions(data.data);
            } else {
                districtSelect.innerHTML = '<option value="">No districts found</option>';
            }
        })
        .catch(err => {
            districtSelect.innerHTML = '<option value="">Error loading districts</option>';
        });

    function renderDistrictOptions(distList) {
        let opts = '<option value="">-- Choose District --</option>';
        distList.forEach(d => {
            opts += '<option value="' + d + '">' + d + '</option>';
        });
        districtSelect.innerHTML = opts;
        districtSelect.disabled = false;
    }
}

function onDistrictChange() {
    const stateVal = document.getElementById('stateSelect').value;
    const districtVal = document.getElementById('districtSelect').value;
    const regionSelect = document.getElementById('regionSelect');

    regionSelect.innerHTML = '<option value="">Loading regions...</option>';
    regionSelect.disabled = true;

    if (!stateVal || !districtVal) {
        regionSelect.innerHTML = '<option value="">-- All Regions / Taluks --</option>';
        return;
    }

    const cacheKey = stateVal + '||' + districtVal;
    if (pincodeRegionCache[cacheKey]) {
        renderRegionOptions(pincodeRegionCache[cacheKey]);
        return;
    }

    fetch('<?php echo $base_url; ?>pincode-finder?ajax_regions=1&state=' + encodeURIComponent(stateVal) + '&district=' + encodeURIComponent(districtVal))
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.data) {
                pincodeRegionCache[cacheKey] = data.data;
                renderRegionOptions(data.data);
            } else {
                regionSelect.innerHTML = '<option value="">All Regions</option>';
            }
        })
        .catch(err => {
            regionSelect.innerHTML = '<option value="">-- All Regions / Taluks --</option>';
        });

    function renderRegionOptions(regList) {
        let opts = '<option value="">-- All Regions / Taluks --</option>';
        regList.forEach(r => {
            opts += '<option value="' + r + '">' + r + '</option>';
        });
        regionSelect.innerHTML = opts;
        regionSelect.disabled = false;
    }
}

function copyFinderText(text) {
    if (!text) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(showFinderToast).catch(() => fallbackFinderCopy(text));
    } else {
        fallbackFinderCopy(text);
    }

    function showFinderToast() {
        let toast = document.getElementById('lawFinderToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'lawFinderToast';
            toast.className = 'law-toast';
            document.body.appendChild(toast);
        }
        toast.innerHTML = '<i class="fas fa-check-circle text-success mr-2" style="font-size: 16px;"></i> <span>PIN Code copied: <strong>' + text + '</strong></span>';
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 2600);
    }

    function fallbackFinderCopy(str) {
        const temp = document.createElement('textarea');
        temp.value = str;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        showFinderToast();
    }
}

$(document).ready(function() {
    if ($('#finderDataTable').length) {
        $('#finderDataTable').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            pageLength: 20,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Filter locations..."
            }
        });
    }
});
</script>

<?php require_once('includes/footer.php'); ?>
