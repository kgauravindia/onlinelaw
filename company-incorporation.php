<?php
    require_once('includes/config.php');
    require_once('includes/function.php');

    // AJAX Endpoint for Live NIC Search
    if (isset($_GET['ajax_nic_search'])) {
        header('Content-Type: application/json');
        $kw = trim($_GET['ajax_nic_search']);
        if (empty($kw)) {
            echo json_encode(['status' => 'error', 'message' => 'Query is empty']);
            exit;
        }
        $safe_kw = mysqli_real_escape_string($con, $kw);
        $res = mysqli_query($con, "SELECT code, level, title, description, division_code FROM `nic` WHERE (code LIKE '%$safe_kw%' OR title LIKE '%$safe_kw%' OR description LIKE '%$safe_kw%') ORDER BY CHAR_LENGTH(code) ASC, code ASC LIMIT 20");
        $items = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $items[] = $row;
            }
        }
        echo json_encode(['status' => 'success', 'data' => $items]);
        exit;
    }

    $cin_param = isset($_GET['cin']) ? trim($_GET['cin']) : '';
    $q_param = isset($_GET['q']) ? trim($_GET['q']) : '';
    $type_param = isset($_GET['type']) ? strtoupper(trim($_GET['type'])) : (isset($_GET['entity_type']) ? strtoupper(trim($_GET['entity_type'])) : '');
    $category_param = isset($_GET['category']) ? strtoupper(trim($_GET['category'])) : '';
    $activity_param = isset($_GET['activity']) ? trim($_GET['activity']) : '';
    $date_param = isset($_GET['date']) ? trim($_GET['date']) : '';
    $month_param = isset($_GET['month']) ? trim($_GET['month']) : '';
    $view_mode = isset($_GET['view']) && $_GET['view'] === 'table' ? 'table' : 'grid';
    $page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
    $limit = 30;
    $offset = ($page - 1) * $limit;
    $is_admin = (check_admin_auth(false) !== false);

    // Normalize type and category if aliased
    if (empty($type_param) && !empty($category_param)) {
        if (in_array($category_param, ['PVT_LTD', 'PVT-LTD', 'PTC', 'PUBLIC_LTD', 'PUBLIC-LTD', 'PLC', 'OPC', 'SECTION_8', 'SECTION-8', 'SEC8', 'NPL'])) {
            $type_param = $category_param;
            $category_param = '';
        }
    }

    $single_company = null;
    $canonical_url = $base_url . "company-incorporation";
    $page_title = "New Company Incorporations in India | Pvt Ltd, Public Ltd, OPC, Section 8, LLPs - Online Law";
    $meta_desc = "Search newly registered companies in India: Private Limited (Pvt Ltd), Public Limited, One Person Companies (OPC), Section 8 Non-profits, LLPs & Foreign Entities from official MCA records.";

    if (!empty($cin_param)) {
        $single_company = resolve_company_incorporation($cin_param);
        if ($single_company) {
            $canonical_url = company_url($single_company);
            $page_title = htmlspecialchars($single_company['company_name']) . " (" . $single_company['cin'] . ") - Registration & Incorporation Details";
            $meta_desc = "Registration details for " . $single_company['company_name'] . " (" . $single_company['cin'] . "). Category: " . $single_company['category'] . ", Class: " . $single_company['class'] . ", Type: " . $single_company['company_type'] . ", Registered on " . ($single_company['registration_date'] ? date('d F Y', strtotime($single_company['registration_date'])) : 'N/A') . ".";
        }
    }

    $has_search = (!empty($q_param) || !empty($type_param) || !empty($category_param) || !empty($activity_param) || !empty($date_param) || !empty($month_param));

    // Fetch related companies when viewing a single company without separate search filters
    $related_companies = ['count' => 0, 'data' => []];
    if ($single_company && !$has_search) {
        $r_where = [$is_admin ? "1=1" : "(status = 'ACTIVE' OR status IS NULL OR status = '')", "id != " . intval($single_company['id'])];
        if (!empty($single_company['activity_code'])) {
            $r_act = mysqli_real_escape_string($con, $single_company['activity_code']);
            $r_where[] = "activity_code = '$r_act'";
        } else {
            $r_cat = mysqli_real_escape_string($con, $single_company['category']);
            $r_where[] = "category = '$r_cat'";
        }
        $r_where_sql = implode(' AND ', $r_where);
        $related_companies = direct_sql("SELECT * FROM companies_incorporated WHERE $r_where_sql ORDER BY registration_date DESC, id DESC LIMIT 6");
    }

    $companies_list = ['count' => 0, 'data' => []];
    $total_companies = 0;
    $total_pages = 0;

    // Query database for search results when a query or filter is active
    if ($has_search) {
        $where = [$is_admin ? "1=1" : "(status = 'ACTIVE' OR status IS NULL OR status = '')"];
        if (!empty($q_param)) {
            $sq = mysqli_real_escape_string($con, $q_param);
            $sub_clauses = ["cin LIKE '%$sq%'", "company_name LIKE '%$sq%'", "activity_code = '$sq'"];

            // Check if search keyword matches any NIC activity title or description in table nic
            $nic_kw_res = mysqli_query($con, "SELECT DISTINCT division_code FROM `nic` WHERE (title LIKE '%$sq%' OR description LIKE '%$sq%') AND division_code != '' LIMIT 15");
            if ($nic_kw_res && mysqli_num_rows($nic_kw_res) > 0) {
                $matched_divs = [];
                while ($ndr = mysqli_fetch_assoc($nic_kw_res)) {
                    $matched_divs[] = "'" . mysqli_real_escape_string($con, $ndr['division_code']) . "'";
                    if (str_starts_with($ndr['division_code'], '0')) {
                        $matched_divs[] = "'" . (int)$ndr['division_code'] . "'";
                    }
                }
                if (!empty($matched_divs)) {
                    $sub_clauses[] = "activity_code IN (" . implode(',', array_unique($matched_divs)) . ")";
                }
            }
            $where[] = "(" . implode(' OR ', $sub_clauses) . ")";
        }
        if (!empty($activity_param)) {
            $s_act = mysqli_real_escape_string($con, $activity_param);
            $act_pad = str_pad($s_act, 2, '0', STR_PAD_LEFT);
            $where[] = "(activity_code = '$s_act' OR activity_code = '$act_pad')";
        }
        if (!empty($type_param)) {
            switch ($type_param) {
                case 'PVT_LTD':
                case 'PVT-LTD':
                case 'PTC':
                    $where[] = "(cin LIKE '%PTC%' OR (company_name LIKE '%PRIVATE LIMITED%' AND company_name NOT LIKE '%(OPC)%'))";
                    break;
                case 'PUBLIC_LTD':
                case 'PUBLIC-LTD':
                case 'PLC':
                    $where[] = "(cin LIKE '%PLC%' OR (company_name LIKE '%LIMITED' AND company_name NOT LIKE '%PRIVATE LIMITED%'))";
                    break;
                case 'OPC':
                    $where[] = "(cin LIKE '%OPC%' OR company_name LIKE '%(OPC)%' OR company_name LIKE '% OPC %')";
                    break;
                case 'SECTION_8':
                case 'SECTION-8':
                case 'SEC8':
                case 'NPL':
                    $where[] = "(cin LIKE '%NPL%' OR cin LIKE '%GAP%' OR cin LIKE '%GPL%' OR company_name LIKE '%SECTION 8%' OR company_name LIKE '%SECTION-8%' OR company_name LIKE '%FOUNDATION%' OR company_name LIKE '%ASSOCIATION%')";
                    break;
                case 'LLP':
                    $where[] = "(category = 'LLP' OR cin LIKE '%LLP%' OR company_name LIKE '%LLP%')";
                    break;
                case 'FOREIGN':
                    $where[] = "(category = 'FOREIGN' OR cin LIKE 'F%')";
                    break;
            }
        }
        if (!empty($category_param) && in_array($category_param, ['INDIAN', 'LLP', 'FOREIGN'])) {
            $where[] = "category = '$category_param'";
        }
        if (!empty($date_param)) {
            $sd = mysqli_real_escape_string($con, $date_param);
            $where[] = "registration_date = '$sd'";
        }
        if (!empty($month_param)) {
            $sm = mysqli_real_escape_string($con, $month_param);
            $where[] = "report_month = '$sm'";
        }

        $where_sql = implode(' AND ', $where);

        $count_res = direct_sql("SELECT COUNT(*) as total FROM companies_incorporated WHERE $where_sql");
        $total_companies = isset($count_res['data'][0]['total']) ? (int)$count_res['data'][0]['total'] : 0;
        $total_pages = ceil($total_companies / $limit);

        $companies_list = direct_sql("SELECT * FROM companies_incorporated WHERE $where_sql ORDER BY registration_date DESC, id DESC LIMIT $offset, $limit");
    }

    // Stats breakdown (Cached for 24 hours to eliminate 4-second full-table scan across 1M rows)
    $cached_stats_raw = function_exists('get_setting') ? get_setting('company_stats_cache', '') : '';
    $stats = null;
    if (!empty($cached_stats_raw)) {
        $decoded = json_decode($cached_stats_raw, true);
        if ($decoded && isset($decoded['time']) && (time() - $decoded['time'] < 86400)) {
            $stats = $decoded['data'];
        }
    }

    if (!$stats) {
        $stats = [
            'INDIAN' => 698172,
            'LLP' => 265278,
            'FOREIGN' => 12951,
            'TOTAL' => 976401,
            'PVT_LTD' => 615190,
            'PUBLIC_LTD' => 16882,
            'OPC' => 43131,
            'SECTION_8' => 45580
        ];
        $stats_res = direct_sql("SELECT category, COUNT(*) as cnt FROM companies_incorporated WHERE (status = 'ACTIVE' OR status IS NULL OR status = '') GROUP BY category");
        if ($stats_res['count'] > 0) {
            $tot = 0;
            foreach ($stats_res['data'] as $s) {
                $cat = strtoupper($s['category']);
                if (isset($stats[$cat])) {
                    $stats[$cat] = (int)$s['cnt'];
                }
                $tot += (int)$s['cnt'];
            }
            $stats['TOTAL'] = $tot;
        }
        if (function_exists('set_setting')) {
            set_setting('company_stats_cache', json_encode(['time' => time(), 'data' => $stats]));
        }
    }

    // Helper: Determine exact entity type and visual styling for any company record
    function get_company_entity_type_info($comp) {
        $cin = strtoupper(trim($comp['cin'] ?? ''));
        $name = strtoupper(trim($comp['company_name'] ?? ''));
        $cat = strtoupper(trim($comp['category'] ?? ''));
        $class = strtoupper(trim($comp['class'] ?? ''));
        
        // 1. Foreign Company
        if ($cat === 'FOREIGN' || str_starts_with($cin, 'F')) {
            return [
                'type_key' => 'FOREIGN',
                'label' => 'Foreign Company (FCIN)',
                'short_label' => 'Foreign Entity',
                'badge_class' => 'company-badge-foreign',
                'badge_style' => 'background: #fffbeb; color: #b45309; border: 1px solid #fde68a;',
                'card_class' => 'card-foreign',
                'icon' => 'fas fa-globe'
            ];
        }
        
        // 2. LLP (Limited Liability Partnership)
        if ($cat === 'LLP' || str_contains($cin, 'LLP') || str_contains($name, 'LLP') || str_contains($name, 'LIMITED LIABILITY PARTNERSHIP')) {
            return [
                'type_key' => 'LLP',
                'label' => 'Limited Liability Partnership (LLPIN)',
                'short_label' => 'LLP',
                'badge_class' => 'company-badge-llp',
                'badge_style' => 'background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;',
                'card_class' => 'card-llp',
                'icon' => 'fas fa-handshake'
            ];
        }
        
        // 3. One Person Company (OPC)
        if (str_contains($cin, 'OPC') || str_contains($name, '(OPC)') || str_contains($name, ' OPC ') || str_contains($class, 'OPC') || str_contains($class, 'ONE PERSON')) {
            return [
                'type_key' => 'OPC',
                'label' => 'One Person Company (OPC)',
                'short_label' => 'OPC (One Person)',
                'badge_class' => 'company-badge-opc',
                'badge_style' => 'background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;',
                'card_class' => 'card-opc',
                'icon' => 'fas fa-user-tie'
            ];
        }
        
        // 4. Section 8 Company (Non-profit / NGO / Foundation)
        if (str_contains($cin, 'NPL') || str_contains($cin, 'GAP') || str_contains($cin, 'GPL') || str_contains($name, 'SECTION 8') || str_contains($name, 'SECTION-8') || str_contains($name, 'FOUNDATION') || str_contains($class, 'SECTION 8') || str_contains($class, 'NON-PROFIT')) {
            return [
                'type_key' => 'SECTION_8',
                'label' => 'Section 8 Company (NGO / Non-Profit)',
                'short_label' => 'Section 8 (NGO)',
                'badge_class' => 'company-badge-sec8',
                'badge_style' => 'background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8;',
                'card_class' => 'card-sec8',
                'icon' => 'fas fa-hand-holding-heart'
            ];
        }
        
        // 5. Public Limited Company (PLC / Ltd)
        if (str_contains($cin, 'PLC') || (str_ends_with($name, 'LIMITED') && !str_contains($name, 'PRIVATE LIMITED')) || str_contains($class, 'PUBLIC')) {
            return [
                'type_key' => 'PUBLIC_LTD',
                'label' => 'Public Limited Company (PLC)',
                'short_label' => 'Public Ltd',
                'badge_class' => 'company-badge-public',
                'badge_style' => 'background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe;',
                'card_class' => 'card-public',
                'icon' => 'fas fa-landmark'
            ];
        }
        
        // 6. Private Limited Company (PTC / Pvt Ltd - Default for Indian Companies)
        return [
            'type_key' => 'PVT_LTD',
            'label' => 'Private Limited Company (Pvt Ltd)',
            'short_label' => 'Pvt Ltd',
            'badge_class' => 'company-badge-indian',
            'badge_style' => 'background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;',
            'card_class' => 'card-pvt',
            'icon' => 'fas fa-building'
        ];
    }

    // State / ROC Code Map
    $state_roc_map = [
        'AP' => 'Andhra Pradesh (Vijayawada)',
        'AR' => 'Arunachal Pradesh',
        'AS' => 'Assam (Shillong/Guwahati)',
        'BR' => 'Bihar (Patna)',
        'CT' => 'Chhattisgarh (Bilaspur)',
        'CH' => 'Chandigarh',
        'DC' => 'Delhi (NCT)',
        'DL' => 'Delhi (NCT)',
        'DN' => 'Dadra & Nagar Haveli',
        'GA' => 'Goa',
        'GJ' => 'Gujarat (Ahmedabad)',
        'HR' => 'Haryana (Delhi/Chandigarh)',
        'HP' => 'Himachal Pradesh',
        'JH' => 'Jharkhand (Ranchi)',
        'JK' => 'Jammu & Kashmir',
        'KA' => 'Karnataka (Bengaluru)',
        'KL' => 'Kerala (Ernakulam)',
        'LD' => 'Lakshadweep',
        'MH' => 'Maharashtra (Mumbai/Pune)',
        'MP' => 'Madhya Pradesh (Gwalior)',
        'MN' => 'Manipur',
        'ML' => 'Meghalaya',
        'MZ' => 'Mizoram',
        'NL' => 'Nagaland',
        'OR' => 'Odisha (Cuttack)',
        'PB' => 'Punjab (Chandigarh)',
        'PY' => 'Puducherry',
        'RJ' => 'Rajasthan (Jaipur)',
        'SK' => 'Sikkim',
        'TN' => 'Tamil Nadu (Chennai/Coimbatore)',
        'TG' => 'Telangana (Hyderabad)',
        'TR' => 'Tripura',
        'UP' => 'Uttar Pradesh (Kanpur)',
        'UT' => 'Uttarakhand (Dehradun)',
        'WB' => 'West Bengal (Kolkata)'
    ];

    // Company Class Map
    $class_map = [
        'PTC' => 'Private Limited Company (Pvt Ltd)',
        'PLC' => 'Public Limited Company (Public Ltd)',
        'FTC' => 'Subsidiary of a Foreign Company',
        'GAP' => 'General Association (Private Sec 8)',
        'GPL' => 'General Association (Public Sec 8)',
        'GOI' => 'Government of India Company',
        'SGC' => 'State Government Company',
        'ULL' => 'Unlimited Liability (Private)',
        'ULT' => 'Unlimited Liability (Public)'
    ];

    // Helper: Parse CIN
    function parseCINDetails($cin, $state_roc_map, $class_map) {
        $cin = strtoupper(trim($cin));
        if (strlen($cin) === 21) {
            $stCode = substr($cin, 6, 2);
            $classCode = substr($cin, 12, 3);
            return [
                'type' => 'CIN',
                'listing' => substr($cin, 0, 1) === 'L' ? 'Listed on Stock Exchange' : 'Unlisted Company',
                'listing_code' => substr($cin, 0, 1),
                'industry' => substr($cin, 1, 5),
                'state_code' => $stCode,
                'state_name' => isset($state_roc_map[$stCode]) ? $state_roc_map[$stCode] : $stCode,
                'year' => substr($cin, 8, 4),
                'class_code' => $classCode,
                'class_name' => isset($class_map[$classCode]) ? $class_map[$classCode] : $classCode,
                'serial' => substr($cin, 15, 6),
            ];
        }
        return null;
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
<meta name="keywords" content="Company Incorporation India, New Company Registration, MCA Registered Companies, Search CIN, LLP Registration, Foreign Company FCIN, Daily Registered Companies, ROC Verification">
<meta name="author" content="OfferPlant">
<title><?php echo $page_title; ?></title>
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

<!-- Schema.org Structured Data -->
<?php if ($single_company): 
$company_schema = [
  "@context" => "https://schema.org",
  "@type" => "Organization",
  "name" => $single_company['company_name'],
  "identifier" => $single_company['cin'],
  "foundingDate" => $single_company['registration_date'],
  "additionalType" => $single_company['company_type'],
  "description" => "Incorporated under MCA Ministry of Corporate Affairs, India. Class: " . $single_company['class']
];
?>
<script type="application/ld+json">
<?php echo json_encode($company_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>
<?php endif; ?>

<style>
    /* ==========================================================================
       MODERN ULTRA-PREMIUM DESIGN SYSTEM FOR COMPANY INCORPORATIONS
       ========================================================================== */
    :root {
        --incorp-primary: #2563eb;
        --incorp-primary-hover: #1d4ed8;
        --incorp-primary-light: #eff6ff;
        --incorp-dark: #0b1329;
        --incorp-slate: #1e293b;
        --incorp-emerald: #059669;
        --incorp-amber: #d97706;
        --incorp-rose: #e11d48;
        --incorp-purple: #7c3aed;
        --incorp-teal: #0d9488;
        --incorp-border: #e2e8f0;
        --incorp-border-light: #f1f5f9;
        --incorp-card-bg: #ffffff;
        --incorp-radius: 18px;
        --incorp-shadow-sm: 0 2px 6px rgba(15, 23, 42, 0.04);
        --incorp-shadow-md: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 4px 6px -2px rgba(15, 23, 42, 0.03);
        --incorp-shadow-lg: 0 20px 40px -12px rgba(15, 23, 42, 0.12);
    }

    /* Entity Type Badges */
    .company-badge-indian { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 700; border-radius: 8px; }
    .company-badge-pvt { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 700; border-radius: 8px; }
    .company-badge-public { background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; font-weight: 700; border-radius: 8px; }
    .company-badge-opc { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-weight: 700; border-radius: 8px; }
    .company-badge-sec8 { background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; font-weight: 700; border-radius: 8px; }
    .company-badge-llp { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-weight: 700; border-radius: 8px; }
    .company-badge-foreign { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-weight: 700; border-radius: 8px; }
    
    /* Modern Hero Banner */
    .hero-incorp-banner {
        background-color: #0b1120 !important;
        background: radial-gradient(135% 100% at 50% 0%, #0b1120 0%, #1e1b4b 55%, #0f172a 100%) !important;
        color: #ffffff !important;
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: clamp(45px, 6vw, 75px) 0 clamp(65px, 8vw, 95px);
    }
    .hero-incorp-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
        background-size: 24px 24px;
        opacity: 0.7;
        pointer-events: none;
    }
    .incorp-glow-orb-1 {
        position: absolute;
        width: 550px;
        height: 550px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.28) 0%, rgba(99, 102, 241, 0) 70%);
        top: -140px;
        left: -120px;
        pointer-events: none;
        animation: pulseOrbIncorp 8s ease-in-out infinite alternate;
    }
    .incorp-glow-orb-2 {
        position: absolute;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.22) 0%, rgba(14, 165, 233, 0) 70%);
        bottom: -160px;
        right: -100px;
        pointer-events: none;
        animation: pulseOrbIncorp 10s ease-in-out infinite alternate-reverse;
    }
    @keyframes pulseOrbIncorp {
        0% { transform: scale(1) translate(0, 0); }
        100% { transform: scale(1.15) translate(20px, 15px); }
    }

    .hero-pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(30, 41, 59, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.22);
        color: #93c5fd;
        padding: 7px 18px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
        margin-bottom: 14px;
    }
    .hero-pulse-dot {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse-green 2s infinite cubic-bezier(0.66, 0, 0, 1);
    }
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .hero-incorp-headline {
        color: #ffffff;
        font-size: clamp(28px, 4.2vw, 42px);
        font-weight: 800;
        letter-spacing: -0.6px;
        line-height: 1.25;
        margin-bottom: 12px;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }
    .hero-incorp-headline .headline-highlight {
        background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-incorp-subtitle {
        max-width: 760px;
        margin: 0 auto 24px auto;
        font-size: 15.5px;
        line-height: 1.6;
        color: #cbd5e1 !important;
    }
    .hero-incorp-subtitle strong {
        color: #ffffff !important;
    }

    /* Stats Quick Ribbon in Hero */
    .hero-stats-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 12px;
        max-width: 1040px;
        margin: 0 auto;
    }
    .hero-stat-card {
        background: rgba(15, 23, 42, 0.85);
        border: 1.5px solid rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 16px;
        padding: 12px 14px;
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
    }
    .hero-stat-card::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3.5px;
        background: transparent;
        transition: background 0.2s ease;
    }
    .hero-stat-card:hover {
        background: rgba(30, 41, 59, 0.95);
        transform: translateY(-3px);
        border-color: rgba(255, 255, 255, 0.35);
        box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.5);
    }
    .hero-stat-card.stat-all:hover::before { background: #60a5fa; }
    .hero-stat-card.stat-pvt:hover::before { background: #3b82f6; }
    .hero-stat-card.stat-public:hover::before { background: #a855f7; }
    .hero-stat-card.stat-opc:hover::before { background: #22c55e; }
    .hero-stat-card.stat-sec8:hover::before { background: #ec4899; }
    .hero-stat-card.stat-llp:hover::before { background: #14b8a6; }
    .hero-stat-card.stat-foreign:hover::before { background: #f59e0b; }

    .hero-stat-card .stat-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: block;
    }
    .hero-stat-card .stat-value {
        color: #ffffff !important;
        font-size: 16.5px;
        font-weight: 800;
        margin-top: 4px;
        line-height: 1.2;
    }

    /* Elevated Search Console */
    .search-console-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.12), 0 4px 12px rgba(15, 23, 42, 0.04);
        margin-top: -55px;
        position: relative;
        z-index: 20;
        transition: all 0.3s ease;
    }
    .search-console-card:hover {
        border-color: #cbd5e1;
    }

    .form-control-custom {
        height: 50px;
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
        font-size: 14px;
        font-weight: 500;
        color: #1e293b;
        background-color: #f8fafc;
        transition: all 0.2s ease;
    }
    .form-control-custom:focus {
        background-color: #ffffff;
        border-color: var(--incorp-primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.14);
    }

    /* Quick Filter Chips */
    .quick-chips-container {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        padding-bottom: 4px;
    }
    .quick-chips-container::-webkit-scrollbar {
        display: none;
    }
    .quick-chip {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        background: #f1f5f9;
        color: #334155;
        border: 1.5px solid #e2e8f0;
        border-radius: 30px;
        padding: 6px 14px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        flex-shrink: 0;
    }
    .quick-chip:hover {
        background: #eff6ff;
        color: #2563eb;
        border-color: #93c5fd;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.1);
    }
    .quick-chip.active {
        background: #2563eb;
        color: #ffffff !important;
        border-color: #2563eb;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    /* Company Card Elevated (Grid View) */
    .company-card-elevated {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: var(--incorp-shadow-sm);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .company-card-elevated::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2563eb, #60a5fa);
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    .company-card-elevated.card-pvt::before { background: linear-gradient(90deg, #2563eb, #60a5fa); }
    .company-card-elevated.card-public::before { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
    .company-card-elevated.card-opc::before { background: linear-gradient(90deg, #16a34a, #4ade80); }
    .company-card-elevated.card-sec8::before { background: linear-gradient(90deg, #db2777, #f472b6); }
    .company-card-elevated.card-llp::before { background: linear-gradient(90deg, #0d9488, #2dd4bf); }
    .company-card-elevated.card-foreign::before { background: linear-gradient(90deg, #d97706, #fbbf24); }
    
    .company-card-elevated:hover {
        transform: translateY(-6px);
        box-shadow: var(--incorp-shadow-lg);
        border-color: #cbd5e1;
    }
    .company-card-elevated:hover::before {
        opacity: 1;
    }

    /* Spec Tiles for Details View */
    .spec-tile {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 18px 20px;
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .spec-tile:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }

    /* Nav Pills Modern */
    .nav-pills-modern {
        gap: 6px;
    }
    .nav-pills-modern .nav-link {
        color: #475569;
        font-family: inherit;
        font-weight: 600;
        font-size: 13.5px;
        border-radius: 30px;
        padding: 8px 18px;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .nav-pills-modern .nav-link:hover {
        color: var(--incorp-primary);
        border-color: var(--incorp-primary);
        background: #eff6ff;
        transform: translateY(-1px);
    }
    .nav-pills-modern .nav-link.active {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff !important;
        border-color: transparent;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
    }

    /* View Switcher */
    .view-switch-btn {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }
    .view-switch-btn.active, .view-switch-btn:hover {
        background: #eff6ff;
        color: #2563eb;
        border-color: #2563eb;
    }

    /* Infographics */
    .infographic-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        padding: 28px 24px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .infographic-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 30px -8px rgba(15, 23, 42, 0.1);
        border-color: #cbd5e1;
    }

    /* CIN Anatomy Visual Diagram */
    .cin-anatomy-diagram {
        background: radial-gradient(ellipse at top, #1e293b 0%, #0b1329 100%);
        color: #ffffff;
        border-radius: 24px;
        padding: 36px 30px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 25px 45px -12px rgba(15, 23, 42, 0.5);
    }
    .cin-segment-pill {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 10px;
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-weight: 800;
        font-size: 19px;
        letter-spacing: 1.5px;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: default;
    }
    .cin-segment-pill:hover {
        transform: translateY(-3px) scale(1.06);
    }

    /* Toast notification */
    .law-toast {
        position: fixed;
        bottom: 28px;
        right: 28px;
        background: #0b1329;
        color: #ffffff;
        padding: 14px 22px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 14px 30px -5px rgba(0, 0, 0, 0.4);
        z-index: 999999;
        display: flex;
        align-items: center;
        gap: 12px;
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

    /* Breadcrumbs Modern */
    .incorp-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px 8px;
        font-size: 13.5px;
        color: #94a3b8;
        margin-bottom: 16px;
    }
    .incorp-breadcrumb a {
        color: #cbd5e1;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }
    .incorp-breadcrumb a:hover {
        color: #ffffff;
        text-decoration: underline;
    }
    .incorp-breadcrumb span.current-page {
        color: #ffffff;
        font-weight: 600;
        max-width: 280px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: inline-block;
        vertical-align: middle;
    }

    /* Pagination Modern */
    .pagination-modern .page-link {
        border-radius: 12px;
        margin: 0 4px;
        border: 1.5px solid #e2e8f0;
        color: #334155;
        font-weight: 600;
        padding: 10px 18px;
        transition: all 0.2s ease;
    }
    .pagination-modern .page-item.active .page-link {
        background: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
        box-shadow: 0 6px 14px rgba(37, 99, 235, 0.35);
    }
    .pagination-modern .page-link:hover:not(.active) {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #2563eb;
    }

    /* ==========================================================================
       MOBILE RESPONSIVENESS & TOUCH OPTIMIZATIONS
       ========================================================================== */
    @media (max-width: 991px) {
        .hero-incorp-banner {
            padding: 38px 0 52px 0 !important;
        }
        .search-console-card {
            margin-top: -32px;
            padding: 20px !important;
        }
    }

    @media (max-width: 767px) {
        .hero-incorp-banner {
            padding: 28px 0 42px 0 !important;
        }
        .hero-incorp-headline {
            font-size: 23px !important;
            letter-spacing: -0.3px;
        }
        .hero-incorp-subtitle {
            font-size: 13.5px !important;
            line-height: 1.55;
            margin-bottom: 16px !important;
        }
        .hero-pill-badge {
            font-size: 10px !important;
            padding: 5px 12px !important;
            letter-spacing: 0.5px !important;
        }
        
        /* Stats Ribbon: Horizontal Native App Touch Scroll on Mobile */
        .hero-stats-wrapper {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            scroll-snap-type: x mandatory;
            gap: 8px !important;
            padding: 4px 12px 14px 12px !important;
            margin: 0 -15px !important;
            scrollbar-width: none;
        }
        .hero-stats-wrapper::-webkit-scrollbar {
            display: none;
        }
        .hero-stat-card {
            min-width: 118px !important;
            flex: 0 0 auto !important;
            scroll-snap-align: start;
            padding: 10px 10px !important;
            border-radius: 14px !important;
        }
        .hero-stat-card .stat-label {
            font-size: 9.5px !important;
        }
        .hero-stat-card .stat-value {
            font-size: 14.5px !important;
        }

        /* Search Console Card */
        .search-console-card {
            margin-top: -24px;
            padding: 16px 14px !important;
            border-radius: 18px !important;
        }
        .form-control-custom {
            height: 46px !important;
            font-size: 13.5px !important;
        }

        /* Quick Filter Chips */
        .quick-chips-container {
            padding-bottom: 6px !important;
            margin: 0 -10px !important;
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
        .quick-chip {
            padding: 5px 12px !important;
            font-size: 12px !important;
        }

        /* Nav Pills Filter */
        .nav-pills-modern {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px !important;
            margin: 0 -10px !important;
            padding-left: 10px !important;
            padding-right: 10px !important;
            scrollbar-width: none;
        }
        .nav-pills-modern::-webkit-scrollbar {
            display: none;
        }
        .nav-pills-modern .nav-link {
            white-space: nowrap !important;
            font-size: 12px !important;
            padding: 7px 14px !important;
            flex-shrink: 0;
        }

        /* Action Buttons on Single Company Detail */
        .w-100-mobile {
            width: 100% !important;
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 8px !important;
        }
        .w-100-mobile .btn {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 13px !important;
            padding: 9px 8px !important;
            border-radius: 10px !important;
        }

        /* Spec Tiles (Single Company) */
        .spec-tile {
            padding: 12px 12px !important;
            border-radius: 14px !important;
        }
        .spec-tile strong, .spec-tile span {
            font-size: 12.5px !important;
        }

        /* CIN Anatomy & Segments */
        .cin-anatomy-diagram {
            padding: 20px 14px !important;
            border-radius: 18px !important;
        }
        .cin-segment-pill {
            padding: 5px 8px !important;
            font-size: 14px !important;
            letter-spacing: 0.8px !important;
        }

        /* Infographic metrics cards */
        .infographic-card {
            padding: 18px 12px !important;
            border-radius: 16px !important;
        }
        .infographic-card .h4 {
            font-size: 18px !important;
        }

        /* Toast notification */
        .law-toast {
            left: 14px;
            right: 14px;
            bottom: 14px;
            font-size: 12.5px;
            padding: 10px 14px;
        }

        /* Pagination Modern */
        .pagination-modern .page-link {
            padding: 8px 12px !important;
            font-size: 13px !important;
            margin: 0 2px !important;
        }
    }

    @media (max-width: 576px) {
        .company-card-elevated {
            padding: 16px !important;
            border-radius: 16px !important;
        }
        .company-card-elevated h5 {
            font-size: 15px !important;
            min-height: auto !important;
        }
        .incorp-breadcrumb span.current-page {
            max-width: 180px;
        }
    }
</style>

<?php require_once('includes/header.php'); ?>

<!-- ==========================================================================
     HERO MODERN BANNER SECTION
     ========================================================================== -->
<section class="hero-incorp-banner">
    <!-- Animated Glow Orbs -->
    <div class="incorp-glow-orb-1"></div>
    <div class="incorp-glow-orb-2"></div>

	<div class="container position-relative" style="z-index: 2;">
		<div class="row justify-content-center">
			<div class="col-lg-11 col-12 text-center">
                
                <!-- Breadcrumbs -->
                <div class="d-flex justify-content-center">
                    <nav class="incorp-breadcrumb" aria-label="breadcrumb">
                        <a href="<?php echo $base_url; ?>index"><i class="fas fa-home mr-1"></i> Home</a>
                        <span>/</span>
                        <?php if ($single_company): ?>
                            <a href="<?php echo $base_url; ?>company-incorporation">Company Incorporations</a>
                            <span>/</span>
                            <span class="current-page"><?php echo htmlspecialchars(mb_strimwidth($single_company['company_name'], 0, 32, '...')); ?></span>
                        <?php else: ?>
                            <span class="current-page">Company Incorporations</span>
                        <?php endif; ?>
                    </nav>
                </div>

				<div class="hero-pill-badge">
					<span class="hero-pulse-dot"></span>
					<span>OFFICIAL MCA CORPORATE REGISTRY • REAL-TIME 2026</span>
				</div>

				<h1 class="hero-incorp-headline">
					Indian Corporate Registry &amp; <span class="headline-highlight">Incorporations</span>
				</h1>
				<p class="hero-incorp-subtitle">
					Direct search and legal due diligence for <strong>Private Limited</strong>, <strong>Public Limited</strong>, <strong>OPC</strong>, <strong>Section 8</strong>, <strong>LLPs</strong> &amp; <strong>Foreign Companies</strong> registered with the Ministry of Corporate Affairs (MCA).
				</p>

                <!-- Quick Hero Stats Ribbon (7 Responsive Modular Cards) -->
                <div class="hero-stats-wrapper mt-4">
                    <a href="<?php echo $base_url; ?>company-incorporation" class="hero-stat-card stat-all">
                        <span class="stat-label text-white-50">TOTAL REGISTRY</span>
                        <span class="stat-value text-white"><?php echo number_format($stats['TOTAL']); ?>+</span>
                    </a>
                    <a href="<?php echo $base_url; ?>company-incorporation?type=pvt-ltd" class="hero-stat-card stat-pvt">
                        <span class="stat-label" style="color: #93c5fd;"><i class="fas fa-building mr-1"></i> PVT LTD</span>
                        <span class="stat-value"><?php echo number_format($stats['PVT_LTD']); ?></span>
                    </a>
                    <a href="<?php echo $base_url; ?>company-incorporation?type=public-ltd" class="hero-stat-card stat-public">
                        <span class="stat-label" style="color: #c4b5fd;"><i class="fas fa-landmark mr-1"></i> PUBLIC LTD</span>
                        <span class="stat-value"><?php echo number_format($stats['PUBLIC_LTD']); ?></span>
                    </a>
                    <a href="<?php echo $base_url; ?>company-incorporation?type=opc" class="hero-stat-card stat-opc">
                        <span class="stat-label" style="color: #86efac;"><i class="fas fa-user-tie mr-1"></i> OPC</span>
                        <span class="stat-value"><?php echo number_format($stats['OPC']); ?></span>
                    </a>
                    <a href="<?php echo $base_url; ?>company-incorporation?type=section-8" class="hero-stat-card stat-sec8">
                        <span class="stat-label" style="color: #f472b6;"><i class="fas fa-hand-holding-heart mr-1"></i> SECTION 8</span>
                        <span class="stat-value"><?php echo number_format($stats['SECTION_8']); ?></span>
                    </a>
                    <a href="<?php echo $base_url; ?>company-incorporation?type=llp" class="hero-stat-card stat-llp">
                        <span class="stat-label" style="color: #5eead4;"><i class="fas fa-handshake mr-1"></i> LLPs</span>
                        <span class="stat-value"><?php echo number_format($stats['LLP']); ?></span>
                    </a>
                    <a href="<?php echo $base_url; ?>company-incorporation?type=foreign" class="hero-stat-card stat-foreign">
                        <span class="stat-label" style="color: #fde047;"><i class="fas fa-globe mr-1"></i> FOREIGN</span>
                        <span class="stat-value"><?php echo number_format($stats['FOREIGN']); ?></span>
                    </a>
                </div>

			</div>
		</div>
	</div>
</section>
<!-- Hero Modern Banner End -->

<!-- ==========================================================================
     MAIN BODY & SEARCH CONSOLE
     ========================================================================== -->
<div class="full-row py-4" style="background-color: #f8fafc; position: relative; z-index: 10;">
	<div class="container">

        <!-- Unified Search & Filter Console -->
        <div class="search-console-card p-4 p-md-4 mb-4">
            <form action="<?php echo $base_url; ?>company-incorporation" method="GET">
                <div class="row align-items-end">
                    <div class="col-lg-4 col-md-12 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-muted mb-1.5"><i class="fas fa-search mr-1 text-primary"></i> Company Name, CIN, or Keyword</label>
                        <div class="position-relative">
                            <input type="text" name="q" class="form-control form-control-custom" placeholder="e.g. Tata, Infosys, U47713DC2026PTC474566..." value="<?php echo htmlspecialchars($q_param); ?>">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-muted mb-1.5"><i class="fas fa-industry mr-1 text-warning"></i> NIC 2025 Industry Sector</label>
                        <select name="activity" class="form-control form-control-custom">
                            <option value="">All Industry Sectors</option>
                            <?php
                            $top_nic_sectors = [
                                '73' => '73 - Advertising & Market Research',
                                '01' => '01 - Agriculture & Crop Production',
                                '71' => '71 - Architecture & Engineering',
                                '41' => '41 - Building Construction & Development',
                                '20' => '20 - Chemical & Chemical Products',
                                '42' => '42 - Civil Engineering & Infra',
                                '63' => '63 - Data Processing, Hosting & Portals',
                                '85' => '85 - Education & Skill Training',
                                '35' => '35 - Electricity, Solar & Renewable Power',
                                '78' => '78 - Employment & Placement Agencies',
                                '66' => '66 - Financial Services Support',
                                '10' => '10 - Food Manufacturing & Processing',
                                '86' => '86 - Hospitals & Healthcare',
                                '55' => '55 - Hotels & Accommodation',
                                '70' => '70 - Management Consulting & HQ',
                                '21' => '21 - Pharmaceuticals & Drugs',
                                '74' => '74 - Professional, Scientific & Technical',
                                '68' => '68 - Real Estate Activities',
                                '56' => '56 - Restaurants & Food Service',
                                '47' => '47 - Retail Trade & E-Commerce',
                                '88' => '88 - Social Work & Foundations',
                                '62' => '62 - Software, IT & Consultancy',
                                '43' => '43 - Specialized Construction',
                                '79' => '79 - Travel & Tour Operations',
                                '52' => '52 - Warehousing & Logistics',
                                '46' => '46 - Wholesale Trade (B2B)',
                            ];
                            foreach ($top_nic_sectors as $act_code => $act_name) {
                                $sel = ($activity_param == $act_code || $activity_param == (int)$act_code) ? 'selected' : '';
                                echo "<option value=\"$act_code\" $sel>$act_name</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-muted mb-1.5"><i class="fas fa-layer-group mr-1 text-primary"></i> Entity Type</label>
                        <select name="type" class="form-control form-control-custom">
                            <option value="">All Entity Types</option>
                            <option value="pvt-ltd" <?php echo ($type_param === 'PVT_LTD' || $type_param === 'PVT-LTD' || $type_param === 'PTC') ? 'selected' : ''; ?>>Private Limited (Pvt Ltd)</option>
                            <option value="public-ltd" <?php echo ($type_param === 'PUBLIC_LTD' || $type_param === 'PUBLIC-LTD' || $type_param === 'PLC') ? 'selected' : ''; ?>>Public Limited (Public Ltd)</option>
                            <option value="opc" <?php echo ($type_param === 'OPC') ? 'selected' : ''; ?>>One Person Company (OPC)</option>
                            <option value="section-8" <?php echo ($type_param === 'SECTION_8' || $type_param === 'SECTION-8' || $type_param === 'SEC8' || $type_param === 'NPL') ? 'selected' : ''; ?>>Section 8 (NGO / Non-Profit)</option>
                            <option value="llp" <?php echo ($type_param === 'LLP' || $category_param === 'LLP') ? 'selected' : ''; ?>>LLP Entities</option>
                            <option value="foreign" <?php echo ($type_param === 'FOREIGN' || $category_param === 'FOREIGN') ? 'selected' : ''; ?>>Foreign Companies</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-muted mb-1.5"><i class="far fa-calendar-alt mr-1 text-primary"></i> Reg. Date</label>
                        <input type="date" name="date" class="form-control form-control-custom" value="<?php echo htmlspecialchars($date_param); ?>">
                    </div>
                    <div class="col-lg-1 col-md-12">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold p-0 d-flex align-items-center justify-content-center shadow-sm" style="height: 50px; border-radius: 14px;" title="Search Records">
                            <i class="fas fa-search fa-lg mr-2 mr-lg-0"></i>
                            <span class="d-inline d-lg-none">Search Incorporations</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Quick Filter Chips Horizontal Bar -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-3 border-top" style="border-color: #f1f5f9 !important;">
                <div class="quick-chips-container flex-grow-1">
                    <span class="small text-muted font-weight-bold mr-1 d-inline-flex align-items-center"><i class="fas fa-bolt text-warning mr-1"></i> Quick Filters:</span>
                    <a href="<?php echo $base_url; ?>company-incorporation?type=pvt-ltd" class="quick-chip <?php echo ($type_param === 'PVT_LTD' || $type_param === 'PVT-LTD' || $type_param === 'PTC') ? 'active' : ''; ?>"><i class="fas fa-building text-primary mr-1.5"></i> Pvt Ltd</a>
                    <a href="<?php echo $base_url; ?>company-incorporation?type=public-ltd" class="quick-chip <?php echo ($type_param === 'PUBLIC_LTD' || $type_param === 'PUBLIC-LTD' || $type_param === 'PLC') ? 'active' : ''; ?>"><i class="fas fa-landmark mr-1.5" style="color: #7c3aed;"></i> Public Ltd</a>
                    <a href="<?php echo $base_url; ?>company-incorporation?type=opc" class="quick-chip <?php echo ($type_param === 'OPC') ? 'active' : ''; ?>"><i class="fas fa-user-tie text-success mr-1.5"></i> OPC</a>
                    <a href="<?php echo $base_url; ?>company-incorporation?type=section-8" class="quick-chip <?php echo ($type_param === 'SECTION_8' || $type_param === 'SECTION-8' || $type_param === 'SEC8' || $type_param === 'NPL') ? 'active' : ''; ?>"><i class="fas fa-hand-holding-heart mr-1.5" style="color: #db2777;"></i> Section 8 (NGO)</a>
                    <a href="<?php echo $base_url; ?>company-incorporation?type=llp" class="quick-chip <?php echo ($type_param === 'LLP' || $category_param === 'LLP') ? 'active' : ''; ?>"><i class="fas fa-handshake text-teal mr-1.5" style="color: #0d9488;"></i> LLPs</a>
                    <a href="<?php echo $base_url; ?>company-incorporation?type=foreign" class="quick-chip <?php echo ($type_param === 'FOREIGN' || $category_param === 'FOREIGN') ? 'active' : ''; ?>"><i class="fas fa-globe text-warning mr-1.5"></i> Foreign</a>
                    <a href="<?php echo $base_url; ?>company-incorporation?activity=62" class="quick-chip <?php echo ($activity_param == '62') ? 'active' : ''; ?>"><i class="fas fa-laptop-code text-primary mr-1.5"></i> Software &amp; IT (62)</a>
                    <a href="<?php echo $base_url; ?>company-incorporation?activity=47" class="quick-chip <?php echo ($activity_param == '47') ? 'active' : ''; ?>"><i class="fas fa-shopping-cart text-danger mr-1.5"></i> Retail &amp; E-Com (47)</a>
                    <a href="<?php echo $base_url; ?>company-incorporation?activity=86" class="quick-chip <?php echo ($activity_param == '86') ? 'active' : ''; ?>"><i class="fas fa-heartbeat text-danger mr-1.5"></i> Healthcare (86)</a>
                </div>
                
                <?php if ($has_search): ?>
                <a href="<?php echo $base_url; ?>company-incorporation" class="btn btn-sm btn-outline-danger font-weight-bold px-3 py-1.5 text-nowrap" style="border-radius: 20px; font-size: 12px;">
                    <i class="fas fa-times mr-1"></i> Reset Filters
                </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($has_search || $single_company !== null || (!empty($cin_param) && !$single_company)): ?>

            <!-- Not Found State -->
            <?php if (!empty($cin_param) && !$single_company): ?>
            <div class="bg-white rounded-xl shadow-sm border py-5 text-center mb-5" style="border-radius: 20px;">
                <div class="card-body">
                    <div class="tool-icon-squircle mx-auto mb-3" style="background: #fef2f2; color: #ef4444; width: 64px; height: 64px; display: inline-flex; align-items: center; justify-content: center; border-radius: 16px;">
                        <i class="fas fa-exclamation-circle fa-2x"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-2">Company Record Not Found</h4>
                    <p class="text-muted mb-4" style="max-width: 520px; margin: 0 auto;">We could not locate any incorporation record matching "<strong><?php echo htmlspecialchars($cin_param); ?></strong>". The company may not be registered yet, or the CIN/slug may be incorrect.</p>
                    <a href="<?php echo $base_url; ?>company-incorporation" class="btn btn-primary font-weight-bold px-4" style="border-radius: 12px;">Browse All Companies</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- =================================================================
                 SINGLE COMPANY DETAIL VIEW
                 ================================================================= -->
            <?php if ($single_company): ?>
            <?php 
                $parsedCin = parseCINDetails($single_company['cin'], $state_roc_map, $class_map);
                $typeInfo = get_company_entity_type_info($single_company);
                $badgeClass = $typeInfo['badge_class'];
                $badgeIcon = $typeInfo['icon'];

                // Resolve full NIC 2025 classification
                $nic_info = get_nic_details($single_company['cin'] ?: $single_company['activity_code']);
            ?>

            <?php if ($is_admin): ?>
            <div class="alert alert-dark d-flex flex-wrap justify-content-between align-items-center mb-3 py-2.5 px-3.5 shadow-sm border-0" style="border-radius: 16px; background: linear-gradient(135deg, #0b1329 0%, #1e293b 100%); color: #f8fafc;">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="badge badge-warning text-dark font-weight-bold px-2.5 py-1" style="border-radius: 8px;"><i class="fas fa-user-shield mr-1"></i> Admin View</span>
                    <span class="small text-light">Status: <strong class="<?php echo (strtoupper($single_company['status'] ?? '') === 'BLOCK') ? 'text-danger' : 'text-success'; ?>"><?php echo htmlspecialchars($single_company['status'] ?: 'ACTIVE'); ?></strong></span>
                    <span class="small text-muted">&bull;</span>
                    <span class="small text-muted font-monospace">ID #<?php echo $single_company['id']; ?></span>
                </div>
                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0" style="gap: 8px;">
                    <a href="<?php echo $base_url; ?>admin/edit_company.php?id=<?php echo $single_company['id']; ?>" target="_blank" class="btn btn-sm btn-warning font-weight-bold text-dark px-3 shadow-sm" style="border-radius: 20px; background: #f59e0b; border: none;">
                        <i class="fas fa-edit mr-1"></i> Edit Company
                    </a>
                    <a href="<?php echo $base_url; ?>admin/manage_companies.php?q=<?php echo urlencode($single_company['cin']); ?>" target="_blank" class="btn btn-sm btn-outline-light px-3" style="border-radius: 20px;">
                        <i class="fas fa-list mr-1"></i> Manage
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-md border mb-4 p-4 p-md-5" style="border-radius: 24px;">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 pb-4 border-bottom mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2.5">
                            <span class="badge <?php echo $badgeClass; ?> px-3 py-1.5 text-uppercase rounded-pill d-inline-flex align-items-center" style="font-size: 12px; font-weight: 700;">
                                <i class="<?php echo $badgeIcon; ?> mr-1.5"></i> <?php echo htmlspecialchars($typeInfo['label']); ?>
                            </span>
                            <?php if (strtoupper($single_company['status'] ?? '') === 'BLOCK'): ?>
                                <span class="badge badge-danger px-3 py-1.5 rounded-pill text-uppercase font-weight-bold" style="background: #fef2f2; color: #b91c1c; font-size: 11.5px;">
                                    <i class="fas fa-ban mr-1"></i> BLOCKED (Hidden from Public)
                                </span>
                            <?php elseif (strtoupper($single_company['status'] ?? '') === 'STRUCK_OFF' || strtoupper($single_company['status'] ?? '') === 'STRUCK OFF'): ?>
                                <span class="badge badge-warning px-3 py-1.5 rounded-pill text-uppercase font-weight-bold" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-size: 11.5px;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> STRUCK OFF / DISSOLVED
                                </span>
                            <?php else: ?>
                                <span class="badge badge-success px-3 py-1.5 rounded-pill text-uppercase font-weight-bold" style="background: #ecfdf5; color: #047857; font-size: 11.5px;">
                                    <i class="fas fa-check-circle mr-1"></i> <?php echo htmlspecialchars($single_company['status'] ?: 'ACTIVE'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <h2 class="font-weight-bold text-dark mb-2" style="font-size: clamp(22px, 3.5vw, 32px); letter-spacing: -0.5px; line-height: 1.3;">
                            <?php echo htmlspecialchars($single_company['company_name']); ?>
                        </h2>
                        <div class="text-muted small d-flex align-items-center flex-wrap gap-2">
                            <span>Corporate Identification Number:</span> 
                            <span class="d-inline-flex align-items-center px-3 py-1 rounded-pill" style="background: #eff6ff; border: 1.5px solid #bfdbfe;">
                                <code class="font-weight-bold text-primary font-monospace fs-6 mr-2" style="font-size: 14px;"><?php echo htmlspecialchars($single_company['cin']); ?></code>
                                <button type="button" class="btn btn-sm btn-link p-0 text-primary font-weight-semibold" onclick="copyToClipboard('<?php echo htmlspecialchars($single_company['cin']); ?>', 'CIN');" title="Copy CIN to Clipboard">
                                    <i class="far fa-copy"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3 mt-lg-0 w-100-mobile">
                        <button type="button" class="btn btn-primary font-weight-bold px-3.5" onclick="copyToClipboard('<?php echo htmlspecialchars($single_company['cin']); ?>', 'CIN');" style="border-radius: 12px;">
                            <i class="far fa-copy mr-1"></i> Copy CIN
                        </button>
                        <button type="button" class="btn btn-light border font-weight-bold px-3" onclick="window.print();" style="border-radius: 12px;">
                            <i class="fas fa-print mr-1"></i> Print / PDF
                        </button>
                        <a href="<?php echo $base_url; ?>contact?type=removal&cin=<?php echo urlencode($single_company['cin']); ?>&name=<?php echo urlencode($single_company['company_name']); ?>" class="btn btn-outline-danger font-weight-bold px-3" style="border-radius: 12px;" title="File a complaint or data removal request">
                            <i class="fas fa-flag mr-1"></i> Complain
                        </a>
                        <a href="<?php echo $base_url; ?>company-incorporation" class="btn btn-outline-secondary font-weight-bold px-3" style="border-radius: 12px;">
                            <i class="fas fa-arrow-left mr-1"></i> All Records
                        </a>
                    </div>
                </div>

                <?php if (strtoupper($single_company['status'] ?? '') === 'STRUCK_OFF' || strtoupper($single_company['status'] ?? '') === 'STRUCK OFF'): ?>
                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4 p-3" style="background: #fffbeb; border-left: 4px solid #f59e0b !important; border-radius: 14px; color: #92400e;">
                        <i class="fas fa-exclamation-triangle fa-2x mr-3 text-warning"></i>
                        <div>
                            <strong class="d-block mb-1" style="font-size: 15px;">Statutory Notice: Company Struck Off by ROC / MCA</strong>
                            <span class="small" style="line-height: 1.5; display: block;">This corporate entity has been published under the official MCA Struck Off report and has been struck off / dissolved from the Register of Companies under Section 248 of the Companies Act, 2013.</span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Clean 6-Tile Key-Value Data Specs Grid -->
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-6 mb-3">
                        <div class="spec-tile">
                            <div class="d-flex align-items-center gap-2 mb-1.5">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-primary" style="width: 28px; height: 28px; background: #eff6ff;"><i class="fas fa-layer-group" style="font-size: 13px;"></i></span>
                                <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Class</small>
                            </div>
                            <strong class="text-dark" style="font-size: 14.5px;"><?php echo htmlspecialchars($single_company['class'] ?: 'N/A'); ?></strong>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-6 mb-3">
                        <div class="spec-tile">
                            <div class="d-flex align-items-center gap-2 mb-1.5">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 28px; height: 28px; background: #f5f3ff; color: #7c3aed;"><i class="fas fa-tag" style="font-size: 13px;"></i></span>
                                <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Type</small>
                            </div>
                            <strong class="text-dark" style="font-size: 14.5px;"><?php echo htmlspecialchars($single_company['company_type'] ?: 'N/A'); ?></strong>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-6 mb-3">
                        <div class="spec-tile">
                            <div class="d-flex align-items-center gap-2 mb-1.5">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-danger" style="width: 28px; height: 28px; background: #fff1f2;"><i class="fas fa-map-marker-alt" style="font-size: 13px;"></i></span>
                                <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">RoC Jurisdiction</small>
                            </div>
                            <strong class="text-dark" style="font-size: 14.5px;"><?php echo $parsedCin ? htmlspecialchars($parsedCin['state_name']) : 'RoC'; ?></strong>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-6 mb-3">
                        <div class="spec-tile">
                            <div class="d-flex align-items-center gap-2 mb-1.5">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-warning" style="width: 28px; height: 28px; background: #fefce8;"><i class="fas fa-industry" style="font-size: 13px;"></i></span>
                                <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Activity / NIC</small>
                            </div>
                            <strong class="text-dark" style="font-size: 14.5px;">
                                Div <?php echo htmlspecialchars($single_company['activity_code'] ?: 'N/A'); ?>
                                <?php if (!empty($nic_info['division']['title'])): ?>
                                    <span class="text-primary font-weight-normal small ml-1">&bull; <?php echo htmlspecialchars(mb_strimwidth($nic_info['division']['title'], 0, 24, '...')); ?></span>
                                <?php endif; ?>
                            </strong>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-6 mb-3">
                        <div class="spec-tile">
                            <div class="d-flex align-items-center gap-2 mb-1.5">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-secondary" style="width: 28px; height: 28px; background: #f1f5f9;"><i class="fas fa-archive" style="font-size: 13px;"></i></span>
                                <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Batch Month</small>
                            </div>
                            <strong class="text-dark" style="font-size: 14.5px;"><?php echo htmlspecialchars($single_company['report_month'] ?: 'N/A'); ?></strong>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-6 mb-3">
                        <div class="spec-tile">
                            <div class="d-flex align-items-center gap-2 mb-1.5">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-success" style="width: 28px; height: 28px; background: #ecfdf5;"><i class="fas fa-shield-alt" style="font-size: 13px;"></i></span>
                                <small class="text-muted font-weight-bold text-uppercase" style="font-size: 11px;">Status</small>
                            </div>
                            <span class="text-success font-weight-bold" style="font-size: 14.5px;"><i class="fas fa-check-circle mr-1"></i> Active</span>
                        </div>
                    </div>
                </div>

                <!-- National Industrial Classification (NIC 2025) Official Record -->
                <?php if (!empty($nic_info['division']) || !empty($nic_info['class']) || !empty($nic_info['subclass'])): ?>
                <div class="mt-4 pt-4 border-top">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="small font-weight-bold text-uppercase text-muted">
                            <i class="fas fa-sitemap text-primary mr-1"></i> Official National Industrial Classification (NIC 2025) Hierarchy
                        </span>
                        <span class="badge badge-light border text-muted small">MoSPI NIC 2025 Standard</span>
                    </div>

                    <div class="p-4 rounded-xl border bg-light" style="border-radius: 16px;">
                        <div class="row">
                            <?php if (!empty($nic_info['section'])): ?>
                            <div class="col-md-6 mb-2.5">
                                <small class="text-muted d-block font-weight-bold">SECTION <?php echo htmlspecialchars($nic_info['section']['code']); ?>:</small>
                                <span class="font-weight-semibold text-dark"><?php echo htmlspecialchars($nic_info['section']['title']); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($nic_info['division'])): ?>
                            <div class="col-md-6 mb-2.5">
                                <small class="text-muted d-block font-weight-bold">DIVISION <?php echo htmlspecialchars($nic_info['division']['code']); ?>:</small>
                                <span class="font-weight-semibold text-primary"><?php echo htmlspecialchars($nic_info['division']['title']); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($nic_info['group'])): ?>
                            <div class="col-md-6 mb-2.5">
                                <small class="text-muted d-block font-weight-bold">GROUP <?php echo htmlspecialchars($nic_info['group']['code']); ?>:</small>
                                <span class="font-weight-semibold text-dark"><?php echo htmlspecialchars($nic_info['group']['title']); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($nic_info['class'])): ?>
                            <div class="col-md-6 mb-2.5">
                                <small class="text-muted d-block font-weight-bold">CLASS <?php echo htmlspecialchars($nic_info['class']['code']); ?>:</small>
                                <span class="font-weight-semibold text-dark"><?php echo htmlspecialchars($nic_info['class']['title']); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($nic_info['subclass'])): ?>
                            <div class="col-12 mt-2 pt-2 border-top">
                                <small class="text-muted d-block font-weight-bold">SUB-CLASS <?php echo htmlspecialchars($nic_info['subclass']['code']); ?> (Exact Operation):</small>
                                <span class="font-weight-bold text-success"><?php echo htmlspecialchars($nic_info['subclass']['title']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($nic_info['class']['description'])): ?>
                        <div class="mt-2 pt-2 border-top small text-muted">
                            <strong>Official Scope &amp; Notes:</strong> <?php echo nl2br(htmlspecialchars($nic_info['class']['description'])); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Clean Inline CIN Breakdown -->
                <?php if ($parsedCin): ?>
                <div class="mt-4 pt-3 border-top">
                    <div class="small font-weight-bold text-muted text-uppercase mb-2">
                        <i class="fas fa-info-circle text-primary mr-1"></i> CIN Architecture Breakdown (21 Digits)
                    </div>
                    <p class="small text-muted mb-0" style="line-height: 1.7;">
                        <strong>Listing:</strong> <?php echo $parsedCin['listing_code']; ?> (<?php echo $parsedCin['listing']; ?>) &bull;
                        <strong>NIC Activity:</strong> <?php echo $parsedCin['industry']; ?> &bull;
                        <strong>State ROC:</strong> <?php echo $parsedCin['state_code']; ?> (<?php echo $parsedCin['state_name']; ?>) &bull;
                        <strong>Year:</strong> <?php echo $parsedCin['year']; ?> &bull;
                        <strong>Class:</strong> <?php echo $parsedCin['class_code']; ?> (<?php echo $parsedCin['class_name']; ?>) &bull;
                        <strong>ROC Serial:</strong> <?php echo $parsedCin['serial']; ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- MCA Disclaimer Note -->
                <div class="mt-4 pt-3 border-top">
                    <div class="alert alert-light border mb-0 p-3.5" style="background: #f8fafc; border-color: #e2e8f0; border-radius: 16px;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fas fa-shield-alt text-secondary mt-1 mr-2" style="font-size: 1.1rem;"></i>
                            <div class="small text-muted" style="line-height: 1.6; width: 100%;">
                                <strong class="text-dark">Disclaimer: Regarding MCA (Ministry of Corporate Affairs)</strong> &mdash; 
                                The corporate data shown above is compiled from periodic incorporation reports published by the Ministry of Corporate Affairs (MCA), Government of India. Online Law is an independent informational directory and is not affiliated, associated, or endorsed by the MCA, Registrar of Companies (ROC), or any government body. Company status, capital, and director filings are subject to subsequent updates. For official compliance and certified records, please verify directly on <a href="https://www.mca.gov.in" target="_blank" rel="noopener noreferrer" class="text-primary font-weight-bold">www.mca.gov.in</a>.
                                <div class="mt-2.5 pt-2 border-top d-flex flex-wrap align-items-center justify-content-between gap-2" style="border-color: #e2e8f0 !important;">
                                    <span class="text-dark">Are you an authorized representative of <strong><?php echo htmlspecialchars($single_company['company_name']); ?></strong> wishing to request record deletion or report an issue?</span>
                                    <a href="<?php echo $base_url; ?>contact?type=removal&cin=<?php echo urlencode($single_company['cin']); ?>&name=<?php echo urlencode($single_company['company_name']); ?>" class="btn btn-sm btn-outline-danger font-weight-bold px-3 py-1 text-nowrap" style="border-radius: 20px;">
                                        <i class="fas fa-flag mr-1"></i> Complain (Delete Data)
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- =================================================================
                 SEARCH RESULTS VIEW (GRID / TABLE)
                 ================================================================= -->
            <?php if ($has_search): ?>
            <!-- Category & Entity Nav Pills Filter & View Switcher -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <ul class="nav nav-pills nav-pills-modern m-0 p-0 flex-wrap">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (empty($category_param) && empty($type_param)) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>company-incorporation?q=<?php echo urlencode($q_param); ?><?php echo !empty($activity_param) ? '&activity='.$activity_param : ''; ?><?php echo !empty($date_param) ? '&date='.$date_param : ''; ?>">
                            <i class="fas fa-th-large mr-1"></i> All Results <span class="badge badge-light ml-1" style="font-size: 11px;"><?php echo number_format($total_companies); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($type_param === 'PVT_LTD' || $type_param === 'PVT-LTD' || $type_param === 'PTC') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>company-incorporation?type=pvt-ltd&q=<?php echo urlencode($q_param); ?><?php echo !empty($activity_param) ? '&activity='.$activity_param : ''; ?><?php echo !empty($date_param) ? '&date='.$date_param : ''; ?>">
                            <i class="fas fa-building mr-1 text-primary"></i> Pvt Ltd
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($type_param === 'PUBLIC_LTD' || $type_param === 'PUBLIC-LTD' || $type_param === 'PLC') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>company-incorporation?type=public-ltd&q=<?php echo urlencode($q_param); ?><?php echo !empty($activity_param) ? '&activity='.$activity_param : ''; ?><?php echo !empty($date_param) ? '&date='.$date_param : ''; ?>">
                            <i class="fas fa-landmark mr-1" style="color: #7c3aed;"></i> Public Ltd
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($type_param === 'OPC') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>company-incorporation?type=opc&q=<?php echo urlencode($q_param); ?><?php echo !empty($activity_param) ? '&activity='.$activity_param : ''; ?><?php echo !empty($date_param) ? '&date='.$date_param : ''; ?>">
                            <i class="fas fa-user-tie mr-1 text-success"></i> OPC
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($type_param === 'SECTION_8' || $type_param === 'SECTION-8' || $type_param === 'SEC8' || $type_param === 'NPL') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>company-incorporation?type=section-8&q=<?php echo urlencode($q_param); ?><?php echo !empty($activity_param) ? '&activity='.$activity_param : ''; ?><?php echo !empty($date_param) ? '&date='.$date_param : ''; ?>">
                            <i class="fas fa-hand-holding-heart mr-1" style="color: #db2777;"></i> Section 8
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($type_param === 'LLP' || $category_param === 'LLP') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>company-incorporation?type=llp&q=<?php echo urlencode($q_param); ?><?php echo !empty($activity_param) ? '&activity='.$activity_param : ''; ?><?php echo !empty($date_param) ? '&date='.$date_param : ''; ?>">
                            <i class="fas fa-handshake mr-1" style="color: #0d9488;"></i> LLPs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($type_param === 'FOREIGN' || $category_param === 'FOREIGN') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>company-incorporation?type=foreign&q=<?php echo urlencode($q_param); ?><?php echo !empty($activity_param) ? '&activity='.$activity_param : ''; ?><?php echo !empty($date_param) ? '&date='.$date_param : ''; ?>">
                            <i class="fas fa-globe mr-1 text-warning"></i> Foreign
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <div class="d-none d-md-flex align-items-center gap-1">
                        <span class="small text-muted mr-2 font-weight-semibold">Layout:</span>
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
            </div>

            <!-- Companies Grid / List -->
            <?php if ($companies_list['count'] > 0): ?>

                <?php if ($view_mode === 'table'): ?>
                    <!-- Table View Mode -->
                    <div class="bg-white rounded-xl shadow-sm border mb-4 overflow-hidden" style="border-radius: 20px;">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted">Entity Type</th>
                                        <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted">Company Name</th>
                                        <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted">CIN / LLPIN</th>
                                        <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted">Class &amp; Type</th>
                                        <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted">NIC Activity</th>
                                        <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted">Reg. Date</th>
                                        <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($companies_list['data'] as $comp): ?>
                                        <?php
                                        $typeInfo = get_company_entity_type_info($comp);
                                        $badgeClass = $typeInfo['badge_class'];
                                        $badgeIcon = $typeInfo['icon'];

                                        $card_nic = get_nic_details($comp['cin'] ?: $comp['activity_code']);
                                        $act_title = !empty($card_nic['primary_title']) ? $card_nic['primary_title'] : '';
                                        $comp_href = company_url($comp);
                                        ?>
                                        <tr>
                                            <td class="px-4 py-3.5 align-middle">
                                                <span class="badge <?php echo $badgeClass; ?> px-2.5 py-1 text-uppercase d-inline-flex align-items-center" style="font-size: 11px;">
                                                    <i class="<?php echo $badgeIcon; ?> mr-1"></i> <?php echo htmlspecialchars($typeInfo['short_label']); ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3.5 align-middle font-weight-bold">
                                                <a href="<?php echo htmlspecialchars($comp_href); ?>" class="text-dark text-decoration-none" style="transition: color 0.2s;" onmouseover="this.style.color='var(--incorp-primary)';" onmouseout="this.style.color='#0f172a';">
                                                    <?php echo htmlspecialchars($comp['company_name']); ?>
                                                </a>
                                            </td>
                                            <td class="px-4 py-3.5 align-middle">
                                                <span class="d-inline-flex align-items-center">
                                                    <code class="font-weight-bold text-primary font-monospace mr-1.5"><?php echo htmlspecialchars($comp['cin']); ?></code>
                                                    <button type="button" class="btn btn-sm btn-link p-0 text-muted" onclick="copyToClipboard('<?php echo htmlspecialchars($comp['cin']); ?>', 'CIN');" title="Copy CIN">
                                                        <i class="far fa-copy"></i>
                                                    </button>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3.5 align-middle small text-muted">
                                                <?php echo htmlspecialchars($comp['class'] ?: '-'); ?>
                                            </td>
                                            <td class="px-4 py-3.5 align-middle small">
                                                <?php if (!empty($comp['activity_code'])): ?>
                                                    <span class="badge badge-light border text-dark" title="<?php echo htmlspecialchars($act_title); ?>">
                                                        NIC <?php echo htmlspecialchars($comp['activity_code']); ?>
                                                        <?php if ($act_title): ?>
                                                             &bull; <?php echo htmlspecialchars(mb_strimwidth($act_title, 0, 20, '...')); ?>
                                                        <?php endif; ?>
                                                    </span>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-3.5 align-middle small text-muted font-weight-medium">
                                                <?php echo !empty($comp['registration_date']) ? date('d M Y', strtotime($comp['registration_date'])) : '-'; ?>
                                            </td>
                                            <td class="px-4 py-3.5 align-middle text-right text-nowrap">
                                                <a href="<?php echo htmlspecialchars($comp_href); ?>" class="btn btn-sm btn-outline-primary font-weight-bold px-3" style="border-radius: 10px;">
                                                    View &rarr;
                                                </a>
                                                <?php if ($is_admin): ?>
                                                    <a href="<?php echo $base_url; ?>admin/edit_company.php?id=<?php echo $comp['id']; ?>" target="_blank" class="btn btn-sm btn-warning font-weight-bold ml-1 px-2 text-dark" style="background: #f59e0b; border: none; border-radius: 10px;" title="Edit in Admin">
                                                        <i class="fas fa-edit mr-1"></i> Edit
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Grid View Mode -->
                    <div class="row">
                        <?php foreach ($companies_list['data'] as $comp): ?>
                            <?php
                            $typeInfo = get_company_entity_type_info($comp);
                            $badgeClass = $typeInfo['badge_class'];
                            $badgeIcon = $typeInfo['icon'];
                            $cardExtraClass = $typeInfo['card_class'];

                            $card_nic = get_nic_details($comp['cin'] ?: $comp['activity_code']);
                            $act_title = !empty($card_nic['primary_title']) ? $card_nic['primary_title'] : '';
                            $comp_href = company_url($comp);
                            ?>
                            <div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
                                <div class="company-card-elevated <?php echo $cardExtraClass; ?> p-4 w-100">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2.5">
                                            <span class="badge <?php echo $badgeClass; ?> px-2.5 py-1 text-uppercase d-inline-flex align-items-center" style="font-size: 11px;">
                                                <i class="<?php echo $badgeIcon; ?> mr-1"></i> <?php echo htmlspecialchars($typeInfo['short_label']); ?>
                                            </span>
                                            <span class="small text-muted font-weight-medium">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                <?php echo !empty($comp['registration_date']) ? date('d M Y', strtotime($comp['registration_date'])) : '-'; ?>
                                            </span>
                                        </div>
                                        
                                        <h5 class="font-weight-bold mb-2" style="font-size: 16px; line-height: 1.4; min-height: 44px;">
                                            <a href="<?php echo htmlspecialchars($comp_href); ?>" class="text-dark text-decoration-none" style="transition: color 0.2s;" onmouseover="this.style.color='var(--incorp-primary)';" onmouseout="this.style.color='#0f172a';">
                                                <?php echo htmlspecialchars($comp['company_name']); ?>
                                            </a>
                                        </h5>

                                        <div class="d-flex justify-content-between align-items-center mb-2.5 p-2 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <small class="text-muted font-weight-bold" style="font-size: 11px;">CIN / ID:</small>
                                            <code class="font-weight-bold text-primary font-monospace" style="font-size: 12.5px;"><?php echo htmlspecialchars($comp['cin']); ?></code>
                                        </div>

                                        <div class="small text-muted mb-2">
                                            <?php if (!empty($comp['class'])): ?>
                                                <div class="mb-1"><i class="fas fa-tag text-primary mr-1"></i> <?php echo htmlspecialchars($comp['class']); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($comp['activity_code'])): ?>
                                                <div class="mt-1" title="<?php echo htmlspecialchars($act_title); ?>">
                                                    <i class="fas fa-industry text-warning mr-1"></i> 
                                                    <strong>NIC <?php echo htmlspecialchars($comp['activity_code']); ?>:</strong> 
                                                    <span class="text-dark"><?php echo htmlspecialchars(mb_strimwidth($act_title ?: 'Industrial Classification', 0, 26, '...')); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="pt-3 border-top mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div class="d-flex align-items-center" style="gap: 6px;">
                                            <a href="<?php echo htmlspecialchars($comp_href); ?>" class="btn btn-sm btn-outline-primary font-weight-bold px-3" style="border-radius: 10px;">
                                                View Details &rarr;
                                            </a>
                                            <?php if ($is_admin): ?>
                                                <a href="<?php echo $base_url; ?>admin/edit_company.php?id=<?php echo $comp['id']; ?>" target="_blank" class="btn btn-sm btn-warning font-weight-bold text-dark px-2" style="background: #f59e0b; border: none; border-radius: 10px;" title="Edit in Admin">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light text-muted border rounded-circle" style="width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;" onclick="copyToClipboard('<?php echo htmlspecialchars($comp['cin']); ?>', 'CIN');" title="Copy CIN">
                                            <i class="far fa-copy"></i>
                                        </button>
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
                        <ul class="pagination pagination-modern">
                            <?php
                            $queryParams = $_GET;
                            // Prev
                            if ($page > 1) {
                                $queryParams['p'] = $page - 1;
                                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParams) . '">&laquo; Prev</a></li>';
                            } else {
                                echo '<li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>';
                            }

                            $startPage = max(1, $page - 2);
                            $endPage = min($total_pages, $page + 2);

                            for ($i = $startPage; $i <= $endPage; $i++) {
                                $queryParams['p'] = $i;
                                $active = ($i == $page) ? 'active' : '';
                                echo '<li class="page-item ' . $active . '"><a class="page-link" href="?' . http_build_query($queryParams) . '">' . $i . '</a></li>';
                            }

                            // Next
                            if ($page < $total_pages) {
                                $queryParams['p'] = $page + 1;
                                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParams) . '">Next &raquo;</a></li>';
                            } else {
                                echo '<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>';
                            }
                            ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="bg-white rounded-xl shadow-sm border py-5 text-center" style="border-radius: 20px;">
                    <div class="card-body">
                        <div class="tool-icon-squircle mx-auto mb-3" style="background: #f1f5f9; color: #64748b; width: 64px; height: 64px; display: inline-flex; align-items: center; justify-content: center; border-radius: 16px;">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2">No Registered Companies Found</h4>
                        <p class="text-muted mb-4" style="max-width: 480px; margin: 0 auto;">No companies matched your query "<strong><?php echo htmlspecialchars($q_param); ?></strong>". Try searching with a different keyword, CIN, or category.</p>
                        <a href="<?php echo $base_url; ?>company-incorporation" class="btn btn-primary font-weight-bold px-4" style="border-radius: 12px;">Reset Search</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php endif; // End of has_search ?>

            <?php if ($single_company && !$has_search && $related_companies['count'] > 0): ?>
            <!-- Related Companies in Same Industry / Activity -->
            <div class="mt-5 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-layer-group text-primary mr-2"></i> Other Recent Incorporations <?php echo !empty($single_company['activity_code']) ? '(NIC '.$single_company['activity_code'].')' : 'in '.$single_company['category']; ?>
                        </h4>
                        <p class="text-muted small mb-0">Newly registered companies and entities in similar activity and sector.</p>
                    </div>
                    <a href="<?php echo $base_url; ?>company-incorporation?category=<?php echo urlencode($single_company['category']); ?><?php echo !empty($single_company['activity_code']) ? '&activity='.urlencode($single_company['activity_code']) : ''; ?>" class="btn btn-sm btn-outline-primary font-weight-bold px-3" style="border-radius: 10px;">
                        Browse All &rarr;
                    </a>
                </div>
                <div class="row">
                    <?php foreach ($related_companies['data'] as $comp): ?>
                        <?php
                        $typeInfo = get_company_entity_type_info($comp);
                        $badgeClass = $typeInfo['badge_class'];
                        $badgeIcon = $typeInfo['icon'];
                        $cardExtraClass = $typeInfo['card_class'];

                        $card_nic = get_nic_details($comp['cin'] ?: $comp['activity_code']);
                        $act_title = !empty($card_nic['primary_title']) ? $card_nic['primary_title'] : '';
                        $comp_href = company_url($comp);
                        ?>
                        <div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
                            <div class="company-card-elevated <?php echo $cardExtraClass; ?> p-4 w-100">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2.5">
                                        <span class="badge <?php echo $badgeClass; ?> px-2.5 py-1 text-uppercase d-inline-flex align-items-center" style="font-size: 11px;">
                                            <i class="<?php echo $badgeIcon; ?> mr-1"></i> <?php echo htmlspecialchars($typeInfo['short_label']); ?>
                                        </span>
                                        <span class="small text-muted font-weight-medium">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            <?php echo !empty($comp['registration_date']) ? date('d M Y', strtotime($comp['registration_date'])) : '-'; ?>
                                        </span>
                                    </div>
                                    
                                    <h5 class="font-weight-bold mb-2" style="font-size: 16px; line-height: 1.4; min-height: 44px;">
                                        <a href="<?php echo htmlspecialchars($comp_href); ?>" class="text-dark text-decoration-none" style="transition: color 0.2s;" onmouseover="this.style.color='var(--incorp-primary)';" onmouseout="this.style.color='#0f172a';">
                                            <?php echo htmlspecialchars($comp['company_name']); ?>
                                        </a>
                                    </h5>

                                    <div class="d-flex justify-content-between align-items-center mb-2.5 p-2 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <small class="text-muted font-weight-bold" style="font-size: 11px;">CIN / ID:</small>
                                        <code class="font-weight-bold text-primary font-monospace" style="font-size: 12.5px;"><?php echo htmlspecialchars($comp['cin']); ?></code>
                                    </div>

                                    <div class="small text-muted mb-2">
                                        <?php if (!empty($comp['class'])): ?>
                                            <div class="mb-1"><i class="fas fa-tag text-primary mr-1"></i> <?php echo htmlspecialchars($comp['class']); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($comp['activity_code'])): ?>
                                            <div class="mt-1" title="<?php echo htmlspecialchars($act_title); ?>">
                                                <i class="fas fa-industry text-warning mr-1"></i> 
                                                <strong>NIC <?php echo htmlspecialchars($comp['activity_code']); ?>:</strong> 
                                                <span class="text-dark"><?php echo htmlspecialchars(mb_strimwidth($act_title ?: 'Industrial Classification', 0, 26, '...')); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="pt-3 border-top mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="d-flex align-items-center" style="gap: 6px;">
                                        <a href="<?php echo htmlspecialchars($comp_href); ?>" class="btn btn-sm btn-outline-primary font-weight-bold px-3" style="border-radius: 10px;">
                                            View Details &rarr;
                                        </a>
                                        <?php if ($is_admin): ?>
                                            <a href="<?php echo $base_url; ?>admin/edit_company.php?id=<?php echo $comp['id']; ?>" target="_blank" class="btn btn-sm btn-warning font-weight-bold text-dark px-2" style="background: #f59e0b; border: none; border-radius: 10px;" title="Edit in Admin">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-light text-muted border rounded-circle" style="width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;" onclick="copyToClipboard('<?php echo htmlspecialchars($comp['cin']); ?>', 'CIN');" title="Copy CIN">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        <?php else: ?>

            <!-- ================================================================= -->
            <!-- DEFAULT INFOGRAPHICS & VISUAL GUIDE (WHEN NO SEARCH IS PERFORMED) -->
            <!-- ================================================================= -->

            <!-- Infographic 1: Visual CIN Anatomy Breakdown Diagram -->
            <div class="cin-anatomy-diagram mb-5 shadow-xl">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
                    <div>
                        <span class="badge badge-warning text-dark font-weight-bold px-3 py-1 rounded-pill text-uppercase mb-1" style="font-size: 11px;">
                            INFOGRAPHIC ARCHITECTURE
                        </span>
                        <h3 class="font-weight-bold text-white mb-1" style="font-size: clamp(20px, 3vw, 26px);">
                            How Indian Corporate Identification Numbers (CIN) Work
                        </h3>
                        <p class="text-white-50 small mb-0">Every registered company in India carries a distinct 21-digit alphanumeric identification code containing 6 vital regulatory parameters.</p>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <span class="badge badge-primary px-3 py-2 rounded-pill font-weight-bold" style="background: rgba(37, 99, 235, 0.4); border: 1px solid rgba(96, 165, 250, 0.5);">
                            21 ALPHANUMERIC CHARACTERS
                        </span>
                    </div>
                </div>

                <!-- Animated Visual CIN Example Banner -->
                <div class="text-center py-3 px-2 rounded-lg mb-4" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 14px;">
                    <div class="d-flex flex-wrap justify-content-center align-items-center" style="gap: 8px;">
                        <span class="cin-segment-pill" style="background: #2563eb; color: #fff;" title="Listing Status">U</span>
                        <span class="cin-segment-pill" style="background: #059669; color: #fff;" title="NIC Activity Code">47713</span>
                        <span class="cin-segment-pill" style="background: #d97706; color: #fff;" title="State ROC Jurisdiction">DC</span>
                        <span class="cin-segment-pill" style="background: #0284c7; color: #fff;" title="Incorporation Year">2026</span>
                        <span class="cin-segment-pill" style="background: #dc2626; color: #fff;" title="Company Classification">PTC</span>
                        <span class="cin-segment-pill" style="background: #7c3aed; color: #fff;" title="ROC Sequential Registration Number">474566</span>
                    </div>
                    <div class="text-white-50 small mt-2">Example: <code>U47713DC2026PTC474566</code> (Unlisted Specialized Retail Company incorporated in Delhi, 2026)</div>
                </div>

                <!-- 6 Explanatory Infographic Rows -->
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="d-flex align-items-start gap-3 p-3 rounded" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px;">
                            <span class="badge" style="background: #2563eb; color: #fff; font-size: 14px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px;">1</span>
                            <div>
                                <h6 class="text-white font-weight-bold mb-1">Listing Status (1 Char)</h6>
                                <p class="text-white-50 extra-small mb-0" style="font-size: 12.5px;"><strong>U</strong> for Unlisted Company or <strong>L</strong> for Companies listed on stock exchanges (NSE/BSE).</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="d-flex align-items-start gap-3 p-3 rounded" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px;">
                            <span class="badge" style="background: #059669; color: #fff; font-size: 14px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px;">2</span>
                            <div>
                                <h6 class="text-white font-weight-bold mb-1">NIC Activity Code (5 Digits)</h6>
                                <p class="text-white-50 extra-small mb-0" style="font-size: 12.5px;">National Industrial Classification code categorizing the primary business sector &amp; economic operations.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="d-flex align-items-start gap-3 p-3 rounded" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px;">
                            <span class="badge" style="background: #d97706; color: #fff; font-size: 14px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px;">3</span>
                            <div>
                                <h6 class="text-white font-weight-bold mb-1">State / ROC Code (2 Chars)</h6>
                                <p class="text-white-50 extra-small mb-0" style="font-size: 12.5px;">Two-letter state jurisdiction code of the registered office (e.g. DL/DC for Delhi, MH for Maharashtra, KA for Karnataka).</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="d-flex align-items-start gap-3 p-3 rounded" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px;">
                            <span class="badge" style="background: #0284c7; color: #fff; font-size: 14px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px;">4</span>
                            <div>
                                <h6 class="text-white font-weight-bold mb-1">Incorporation Year (4 Digits)</h6>
                                <p class="text-white-50 extra-small mb-0" style="font-size: 12.5px;">The 4-digit calendar year in which the legal Certificate of Incorporation was issued by the Registrar.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="d-flex align-items-start gap-3 p-3 rounded" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px;">
                            <span class="badge" style="background: #dc2626; color: #fff; font-size: 14px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px;">5</span>
                            <div>
                                <h6 class="text-white font-weight-bold mb-1">Ownership Class (3 Chars)</h6>
                                <p class="text-white-50 extra-small mb-0" style="font-size: 12.5px;"><strong>PTC</strong> (Private Limited), <strong>PLC</strong> (Public Limited), <strong>FTC</strong> (Foreign Subsidiary), <strong>GAP</strong> (Sec 8 Non-profit).</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="d-flex align-items-start gap-3 p-3 rounded" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px;">
                            <span class="badge" style="background: #7c3aed; color: #fff; font-size: 14px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px;">6</span>
                            <div>
                                <h6 class="text-white font-weight-bold mb-1">Registration Serial (6 Digits)</h6>
                                <p class="text-white-50 extra-small mb-0" style="font-size: 12.5px;">Unique sequential registration number allotted by the respective regional Registrar of Companies (ROC).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Infographic 2: Statistical Live Overview Metrics (6 Distinct Entity Types) -->
            <div class="row mb-5">
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <a href="<?php echo $base_url; ?>company-incorporation?type=pvt-ltd" class="infographic-card text-center text-decoration-none d-block">
                        <div class="tool-icon-squircle mx-auto mb-2.5" style="background: #eff6ff; color: #2563eb; width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 14px;">
                            <i class="fas fa-building fa-lg"></i>
                        </div>
                        <div class="h4 font-weight-bold text-primary mb-1"><?php echo number_format($stats['PVT_LTD']); ?></div>
                        <div class="text-muted small font-weight-bold text-uppercase" style="font-size: 11px;">Pvt Ltd (PTC)</div>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <a href="<?php echo $base_url; ?>company-incorporation?type=public-ltd" class="infographic-card text-center text-decoration-none d-block">
                        <div class="tool-icon-squircle mx-auto mb-2.5" style="background: #f5f3ff; color: #7c3aed; width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 14px;">
                            <i class="fas fa-landmark fa-lg"></i>
                        </div>
                        <div class="h4 font-weight-bold mb-1" style="color: #7c3aed;"><?php echo number_format($stats['PUBLIC_LTD']); ?></div>
                        <div class="text-muted small font-weight-bold text-uppercase" style="font-size: 11px;">Public Ltd (PLC)</div>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <a href="<?php echo $base_url; ?>company-incorporation?type=opc" class="infographic-card text-center text-decoration-none d-block">
                        <div class="tool-icon-squircle mx-auto mb-2.5" style="background: #f0fdf4; color: #16a34a; width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 14px;">
                            <i class="fas fa-user-tie fa-lg"></i>
                        </div>
                        <div class="h4 font-weight-bold text-success mb-1"><?php echo number_format($stats['OPC']); ?></div>
                        <div class="text-muted small font-weight-bold text-uppercase" style="font-size: 11px;">One Person (OPC)</div>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <a href="<?php echo $base_url; ?>company-incorporation?type=section-8" class="infographic-card text-center text-decoration-none d-block">
                        <div class="tool-icon-squircle mx-auto mb-2.5" style="background: #fdf2f8; color: #db2777; width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 14px;">
                            <i class="fas fa-hand-holding-heart fa-lg"></i>
                        </div>
                        <div class="h4 font-weight-bold mb-1" style="color: #db2777;"><?php echo number_format($stats['SECTION_8']); ?></div>
                        <div class="text-muted small font-weight-bold text-uppercase" style="font-size: 11px;">Section 8 (NGO)</div>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <a href="<?php echo $base_url; ?>company-incorporation?type=llp" class="infographic-card text-center text-decoration-none d-block">
                        <div class="tool-icon-squircle mx-auto mb-2.5" style="background: #ecfdf5; color: #0d9488; width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 14px;">
                            <i class="fas fa-handshake fa-lg"></i>
                        </div>
                        <div class="h4 font-weight-bold mb-1" style="color: #0d9488;"><?php echo number_format($stats['LLP']); ?></div>
                        <div class="text-muted small font-weight-bold text-uppercase" style="font-size: 11px;">LLPs (LLPIN)</div>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <a href="<?php echo $base_url; ?>company-incorporation?type=foreign" class="infographic-card text-center text-decoration-none d-block">
                        <div class="tool-icon-squircle mx-auto mb-2.5" style="background: #fffbeb; color: #d97706; width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 14px;">
                            <i class="fas fa-globe fa-lg"></i>
                        </div>
                        <div class="h4 font-weight-bold text-warning mb-1"><?php echo number_format($stats['FOREIGN']); ?></div>
                        <div class="text-muted small font-weight-bold text-uppercase" style="font-size: 11px;">Foreign (FCIN)</div>
                    </a>
                </div>
            </div>

            <!-- Infographic 3: NIC 2025 Industry Sectors & Activity Code Explorer -->
            <div class="bg-white rounded-xl shadow-sm border p-4 p-md-5 mb-5" style="border-radius: 22px;">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge badge-primary px-3 py-1 rounded-pill text-uppercase font-weight-bold" style="background: #eff6ff; color: #1d4ed8; font-size: 11px;">
                                <i class="fas fa-database mr-1"></i> NIC 2025 NATIONAL CLASSIFICATION
                            </span>
                            <span class="badge badge-success px-2.5 py-1 rounded-pill text-uppercase font-weight-bold" style="background: #ecfdf5; color: #047857; font-size: 11px;">
                                2,715 Official Codes
                            </span>
                        </div>
                        <h3 class="font-weight-bold text-dark mb-1" style="font-size: 22px;">
                            Browse Incorporations by NIC 2025 Industry Sectors
                        </h3>
                        <p class="text-muted small mb-0">Explore registered companies classified according to Ministry of Statistics &amp; Programme Implementation (MoSPI) standards.</p>
                    </div>
                </div>

                <!-- 8 Featured High-Volume Industry Sectors (Alphabetical) -->
                <div class="row mb-4">
                    <?php
                    $sector_cards = [
                        ['code' => '85', 'title' => 'Education & Skill Training', 'icon' => 'fa-graduation-cap', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'count' => '1,260+'],
                        ['code' => '86', 'title' => 'Human Health & Hospitals', 'icon' => 'fa-heartbeat', 'color' => '#e11d48', 'bg' => '#fff1f2', 'count' => '740+'],
                        ['code' => '70', 'title' => 'Management Consulting & HQ', 'icon' => 'fa-briefcase', 'color' => '#0284c7', 'bg' => '#f0f9ff', 'count' => '1,030+'],
                        ['code' => '68', 'title' => 'Real Estate Activities', 'icon' => 'fa-city', 'color' => '#d97706', 'bg' => '#fffbeb', 'count' => '1,950+'],
                        ['code' => '47', 'title' => 'Retail Trade & Commerce', 'icon' => 'fa-shopping-cart', 'color' => '#dc2626', 'bg' => '#fef2f2', 'count' => '1,740+'],
                        ['code' => '62', 'title' => 'Software & Computer Services', 'icon' => 'fa-laptop-code', 'color' => '#2563eb', 'bg' => '#eff6ff', 'count' => '3,830+'],
                        ['code' => '35', 'title' => 'Solar, Gas & Renewable Power', 'icon' => 'fa-bolt', 'color' => '#ca8a04', 'bg' => '#fefce8', 'count' => '670+'],
                        ['code' => '46', 'title' => 'Wholesale Trade & B2B', 'icon' => 'fa-boxes', 'color' => '#059669', 'bg' => '#ecfdf5', 'count' => '3,150+'],
                    ];
                    foreach ($sector_cards as $sc): ?>
                    <div class="col-lg-3 col-md-6 col-12 mb-3 d-flex">
                        <a href="<?php echo $base_url; ?>company-incorporation?activity=<?php echo $sc['code']; ?>" class="p-3 rounded-lg border w-100 text-decoration-none d-flex flex-column justify-content-between" style="background: #ffffff; transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); border-color: #e2e8f0; border-radius: 16px;" onmouseover="this.style.borderColor='<?php echo $sc['color']; ?>'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.06)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-lg" style="width: 38px; height: 38px; background: <?php echo $sc['bg']; ?>; color: <?php echo $sc['color']; ?>; font-size: 16px; border-radius: 10px;">
                                    <i class="fas <?php echo $sc['icon']; ?>"></i>
                                </span>
                                <div>
                                    <span class="badge badge-light border text-muted" style="font-size: 10px;">Division <?php echo $sc['code']; ?></span>
                                    <div class="font-weight-bold text-dark" style="font-size: 13.5px; line-height: 1.3;"><?php echo $sc['title']; ?></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top" style="border-color: #f1f5f9 !important;">
                                <span class="small font-weight-bold text-primary"><?php echo $sc['count']; ?> Companies</span>
                                <span class="small text-muted font-weight-bold">View &rarr;</span>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Instant NIC 2025 Activity Code Finder Widget -->
                <div class="p-4 rounded-xl" style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 18px;">
                    <div class="row align-items-center mb-3">
                        <div class="col-lg-7 col-md-12 mb-2 mb-lg-0">
                            <h5 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">
                                <i class="fas fa-search text-primary mr-1"></i> Instant NIC 2025 Activity Code Finder
                            </h5>
                            <p class="text-muted small mb-0">Type any business activity (e.g. <em>Artificial Intelligence, Electric Vehicle, Solar, Hospital, Dairy</em>) to find official 2-digit to 6-digit codes.</p>
                        </div>
                        <div class="col-lg-5 col-md-12">
                            <div class="input-group">
                                <input type="text" id="nicLiveSearchInput" class="form-control form-control-custom" placeholder="Search 2,715 NIC activities..." style="height: 44px;">
                                <div class="input-group-append">
                                    <button class="btn btn-primary px-3 font-weight-bold" style="border-radius: 0 12px 12px 0;" type="button" onclick="runNicLiveSearch();">
                                        <i class="fas fa-search"></i> Find
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="nicSearchResultsBox" style="display: none;" class="mt-3 bg-white rounded-lg border p-3 shadow-sm" style="border-radius: 14px;">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="small font-weight-bold text-dark"><i class="fas fa-list mr-1 text-primary"></i> Matching NIC 2025 Codes:</span>
                            <button type="button" class="btn btn-sm btn-link text-muted p-0" onclick="document.getElementById('nicSearchResultsBox').style.display='none';"><i class="fas fa-times"></i> Close</button>
                        </div>
                        <div id="nicSearchResultsList" class="small" style="max-height: 240px; overflow-y: auto;"></div>
                    </div>
                </div>
            </div>

            <!-- Infographic 4: Comparative Corporate Classification Matrix -->
            <div class="bg-white rounded-xl shadow-sm border p-4 p-md-5 mb-5" style="border-radius: 22px;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="brand-icon-badge" style="width: 36px; height: 36px; font-size: 16px; background: #eff6ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px;">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <h3 class="font-weight-bold text-dark mb-0" style="font-size: 20px;">
                        Comparison of Registered Business Structures in India
                    </h3>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered mb-0" style="border-color: #e2e8f0; font-size: 13px;">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-dark font-weight-bold" style="min-width: 140px;">Feature</th>
                                <th class="font-weight-bold text-primary" style="min-width: 160px;"><i class="fas fa-building mr-1"></i> Private Limited</th>
                                <th class="font-weight-bold text-dark" style="min-width: 160px; color: #0284c7;"><i class="fas fa-landmark mr-1"></i> Public Limited</th>
                                <th class="font-weight-bold" style="min-width: 160px; color: #7c3aed;"><i class="fas fa-user-tie mr-1"></i> OPC</th>
                                <th class="font-weight-bold text-success" style="min-width: 160px;"><i class="fas fa-hands-helping mr-1"></i> Section 8 (NGO)</th>
                                <th class="font-weight-bold text-dark" style="min-width: 160px; color: #0d9488;"><i class="fas fa-handshake mr-1"></i> LLP</th>
                                <th class="font-weight-bold text-warning" style="min-width: 160px;"><i class="fas fa-globe mr-1"></i> Foreign Entity</th>
                            </tr>
                        </thead>
                        <tbody class="text-muted">
                            <tr>
                                <td class="font-weight-bold text-dark">CIN / Code Suffix</td>
                                <td><code>PTC</code></td>
                                <td><code>PLC</code></td>
                                <td><code>OPC</code> / <code>PTC</code></td>
                                <td><code>NPL</code> / <code>GAP</code> / <code>GPL</code></td>
                                <td>LLPIN (e.g. <code>AAA-1234</code>)</td>
                                <td><code>FTC</code> / FCIN</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-dark">Governing Law</td>
                                <td>Companies Act, 2013</td>
                                <td>Companies Act, 2013 &amp; SEBI (if listed)</td>
                                <td>Companies Act, 2013</td>
                                <td>Companies Act, 2013 (Sec 8)</td>
                                <td>LLP Act, 2008</td>
                                <td>Companies Act &amp; RBI FEMA</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-dark">Min. Members / Directors</td>
                                <td>2 Members / 2 Directors</td>
                                <td>7 Members / 3 Directors</td>
                                <td>1 Member / 1 Director</td>
                                <td>2 Members / 2 Directors</td>
                                <td>2 Partners</td>
                                <td>1 Indian Rep.</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-dark">Profit Distribution</td>
                                <td>Dividends permitted</td>
                                <td>Dividends permitted</td>
                                <td>Dividends permitted</td>
                                <td><span class="badge badge-danger">Strictly Prohibited</span></td>
                                <td>Profit share to partners</td>
                                <td>Repatriable subject to RBI</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-dark">Liability Protection</td>
                                <td>Limited to unpaid capital</td>
                                <td>Limited to share value</td>
                                <td>Limited to share capital</td>
                                <td>Limited by shares/guarantee</td>
                                <td>Limited to partner contribution</td>
                                <td>Parent company liability</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-dark">Annual MCA Filings</td>
                                <td>AOC-4, MGT-7, DIR-3 KYC</td>
                                <td>AOC-4, MGT-7, Secretarial Audit</td>
                                <td>AOC-4, MGT-7A (simplified)</td>
                                <td>AOC-4, MGT-7, Annual Returns</td>
                                <td>Form 8 &amp; Form 11</td>
                                <td>Form FC-3 &amp; Financials</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Infographic 5: How to Verify Company Status (3 Steps) -->
            <div class="bg-white rounded-xl shadow-sm border p-4 p-md-5" style="border-radius: 22px;">
                <div class="row text-center mb-4 justify-content-center">
                    <div class="col-lg-8 col-12">
                        <span class="badge badge-primary px-3 py-1 rounded-pill text-uppercase font-weight-bold mb-2" style="background: #eff6ff; color: #2563eb;">CITIZEN VERIFICATION</span>
                        <h4 class="font-weight-bold text-dark mb-1" style="font-size: 22px;">How to Verify Any Indian Business Entity</h4>
                        <p class="text-muted small mb-0">Follow these 3 simple steps to conduct legal due diligence on contractors, vendors, and companies.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                        <div class="p-3.5 rounded border bg-light h-100" style="border-radius: 14px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge badge-primary rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                <h6 class="font-weight-bold text-dark mb-0">Locate Identification No.</h6>
                            </div>
                            <p class="text-muted small mb-0">Check invoices, official letterheads, or company websites for the mandatory 21-digit CIN or LLPIN.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                        <div class="p-3.5 rounded border bg-light h-100" style="border-radius: 14px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge badge-success rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                <h6 class="font-weight-bold text-dark mb-0">Search on OnlineLaw</h6>
                            </div>
                            <p class="text-muted small mb-0">Enter the CIN, company name, or LLPIN in the search box above to instantly fetch official registration data.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                        <div class="p-3.5 rounded border bg-light h-100" style="border-radius: 14px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge badge-info rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                                <h6 class="font-weight-bold text-dark mb-0">Verify Compliance Status</h6>
                            </div>
                            <p class="text-muted small mb-0">Confirm the entity's status is ACTIVE and cross-reference the registered ROC state and authorized class.</p>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <!-- Disclaimer: Regarding MCA (Ministry of Corporate Affairs) -->
        <div class="p-4 rounded-xl border bg-white shadow-sm mt-5 mb-4" style="border-radius: 20px; border-left: 4px solid var(--incorp-primary) !important;">
            <div class="d-flex align-items-start">
                <div class="p-2 rounded-circle bg-light text-primary mr-3 d-none d-sm-inline-flex" style="width: 44px; height: 44px; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-balance-scale fa-lg"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold text-dark mb-2">
                        <i class="fas fa-shield-alt text-primary mr-1 d-inline d-sm-none"></i> Disclaimer: Regarding Ministry of Corporate Affairs (MCA)
                    </h5>
                    <p class="text-muted small mb-2" style="line-height: 1.7;">
                        <strong>Public Data Source:</strong> The corporate database, company incorporation records, Limited Liability Partnership (LLP) details, and Foreign Company listings available on <strong>Online Law</strong> are compiled from official reports publicly disseminated by the <strong>Ministry of Corporate Affairs (MCA), Government of India</strong> under public data standards.
                    </p>
                    <p class="text-muted small mb-2" style="line-height: 1.7;">
                        <strong>Independent Platform &amp; Non-Affiliation:</strong> <strong>Online Law</strong> (an initiative of <em>OfferPlant Technologies Private Limited</em>) is an independent private legal and regulatory information portal. We are <u>not affiliated, associated, authorized, endorsed by, or in any way officially connected with</u> the Ministry of Corporate Affairs (MCA), the Registrar of Companies (ROC), the Insolvency and Bankruptcy Board of India (IBBI), or any statutory department of the Government of India. Official government logos, trademarks, and statutory terminology remain the property of their respective owners.
                    </p>
                    <p class="text-muted small mb-2" style="line-height: 1.7;">
                        <strong>Verification &amp; Accuracy:</strong> While we endeavor to maintain up-to-date and accurate data, corporate statuses (including active, dormant, struck-off, amalgamated, or under liquidation), authorized/paid-up capital, registered office addresses, charges, and director appointments are subject to subsequent statutory filings and regulatory revisions on the MCA portal. We strongly advise users, investors, financial institutions, and legal professionals to independently verify real-time statutory filings and certified documents on the official government website: <a href="https://www.mca.gov.in" target="_blank" rel="noopener noreferrer" class="text-primary font-weight-bold">https://www.mca.gov.in</a>.
                    </p>
                    <p class="text-muted small mb-0" style="line-height: 1.7;">
                        <strong>No Legal or Corporate Advice:</strong> The contents and search tools provided on this portal are for general informational and educational purposes only and do not constitute formal legal, corporate governance, financial, or tax advice. For specific matters, please consult a qualified legal professional, Company Secretary (CS), or Chartered Accountant (CA).
                    </p>
                </div>
            </div>
        </div>

	</div>
</div>

<!-- ==========================================================================
     INTERACTIVE SCRIPTS & TOAST NOTIFICATION SYSTEM
     ========================================================================== -->
<script>
function copyToClipboard(text, label = 'CIN') {
    if (!text) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(showToast).catch(() => fallbackCopy(text));
    } else {
        fallbackCopy(text);
    }

    function showToast() {
        let toast = document.getElementById('lawCopyToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'lawCopyToast';
            toast.className = 'law-toast';
            document.body.appendChild(toast);
        }
        toast.innerHTML = '<i class="fas fa-check-circle text-success mr-2" style="font-size: 16px;"></i> <span>' + label + ' copied: <strong>' + text + '</strong></span>';
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 2800);
    }

    function fallbackCopy(str) {
        const temp = document.createElement('textarea');
        temp.value = str;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        showToast();
    }
}

function runNicLiveSearch() {
    const input = document.getElementById('nicLiveSearchInput');
    const box = document.getElementById('nicSearchResultsBox');
    const list = document.getElementById('nicSearchResultsList');
    if (!input || !box || !list) return;

    const query = input.value.trim();
    if (!query) {
        alert('Please enter a business keyword or NIC code to search.');
        return;
    }

    box.style.display = 'block';
    list.innerHTML = '<div class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Searching official NIC 2025 records...</div>';

    fetch('<?php echo $base_url; ?>company-incorporation?ajax_nic_search=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.data && data.data.length > 0) {
                let html = '<div class="list-group list-group-flush">';
                data.data.forEach(item => {
                    if (!item) return;
                    const code = item.code || '';
                    const level = item.level || '';
                    const title = item.title || '';
                    const divCode = item.division_code || (code.length >= 2 ? code.substring(0, 2) : code);

                    html += '<div class="list-group-item px-2 py-2.5 d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-color: #f1f5f9;">' +
                        '<div>' +
                            '<span class="badge badge-primary mr-2" style="font-size: 11px;">' + level + ' ' + code + '</span>' +
                            '<strong class="text-dark">' + title + '</strong>' +
                        '</div>' +
                        '<a href="<?php echo $base_url; ?>company-incorporation?activity=' + divCode + '" class="btn btn-xs btn-outline-primary font-weight-bold px-2.5 py-1" style="font-size: 11.5px; border-radius: 6px;">' +
                            'View Companies (Div ' + divCode + ') &rarr;' +
                        '</a>' +
                    '</div>';
                });
                html += '</div>';
                list.innerHTML = html;
            } else {
                list.innerHTML = '<div class="text-muted p-2">No matching NIC 2025 activity codes found for "<strong>' + encodeURIComponent(query) + '</strong>".</div>';
            }
        })
        .catch(err => {
            list.innerHTML = '<div class="text-danger p-2"><i class="fas fa-exclamation-triangle mr-1"></i> Error querying NIC database.</div>';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('nicLiveSearchInput');
    if (input) {
        input.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                runNicLiveSearch();
            }
        });
    }
});
</script>

<?php require_once('includes/footer.php'); ?>
