<?php
    require_once('includes/config.php');
    require_once('includes/function.php');

    // --------------------------------------------------------------------------
    // AJAX Endpoints for Dynamic 4-Step Cascading Dropdowns
    // --------------------------------------------------------------------------
    if (isset($_GET['ajax_states'])) {
        header('Content-Type: application/json');
        header('Cache-Control: public, max-age=86400');
        $bank = trim($_GET['bank'] ?? '');
        if (empty($bank)) {
            echo json_encode(['status' => 'error', 'data' => []]);
            exit;
        }

        $cache_dir = __DIR__ . '/tmp/bank_cache';
        $cache_key = md5('st_' . strtolower($bank));
        $cache_file = "$cache_dir/$cache_key.json";
        if (file_exists($cache_file) && (time() - filemtime($cache_file) < 604800)) { // 7 days
            $raw = @file_get_contents($cache_file);
            if ($raw) {
                echo json_encode(['status' => 'success', 'data' => json_decode($raw, true) ?: []]);
                exit;
            }
        }

        $safe_bank = mysqli_real_escape_string($con, $bank);
        $res = mysqli_query($con, "SELECT DISTINCT state FROM bank_ifsc WHERE bank = '$safe_bank' AND state IS NOT NULL AND state != '' ORDER BY state ASC");
        $states = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $states[] = $row['state'];
            }
            mysqli_free_result($res);
        }

        if (!is_dir($cache_dir)) {
            @mkdir($cache_dir, 0755, true);
        }
        @file_put_contents($cache_file, json_encode($states));

        echo json_encode(['status' => 'success', 'data' => $states]);
        exit;
    }

    if (isset($_GET['ajax_districts'])) {
        header('Content-Type: application/json');
        header('Cache-Control: public, max-age=86400');
        $bank = trim($_GET['bank'] ?? '');
        $state = trim($_GET['state'] ?? '');
        if (empty($bank) || empty($state)) {
            echo json_encode(['status' => 'error', 'data' => []]);
            exit;
        }

        $cache_dir = __DIR__ . '/tmp/bank_cache';
        $cache_key = md5('dst_' . strtolower($bank . '_' . $state));
        $cache_file = "$cache_dir/$cache_key.json";
        if (file_exists($cache_file) && (time() - filemtime($cache_file) < 604800)) { // 7 days
            $raw = @file_get_contents($cache_file);
            if ($raw) {
                echo json_encode(['status' => 'success', 'data' => json_decode($raw, true) ?: []]);
                exit;
            }
        }

        $safe_bank = mysqli_real_escape_string($con, $bank);
        $safe_state = mysqli_real_escape_string($con, $state);
        $res = mysqli_query($con, "SELECT DISTINCT city2 FROM bank_ifsc WHERE bank = '$safe_bank' AND state = '$safe_state' AND city2 IS NOT NULL AND city2 != '' ORDER BY city2 ASC");
        $districts = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $districts[] = $row['city2'];
            }
            mysqli_free_result($res);
        }

        if (!is_dir($cache_dir)) {
            @mkdir($cache_dir, 0755, true);
        }
        @file_put_contents($cache_file, json_encode($districts));

        echo json_encode(['status' => 'success', 'data' => $districts]);
        exit;
    }

    if (isset($_GET['ajax_branches'])) {
        header('Content-Type: application/json');
        header('Cache-Control: public, max-age=86400');
        $bank = trim($_GET['bank'] ?? '');
        $state = trim($_GET['state'] ?? '');
        $district = trim($_GET['district'] ?? '');
        if (empty($bank) || empty($state) || empty($district)) {
            echo json_encode(['status' => 'error', 'data' => []]);
            exit;
        }

        $cache_dir = __DIR__ . '/tmp/bank_cache';
        $cache_key = md5('br_' . strtolower($bank . '_' . $state . '_' . $district));
        $cache_file = "$cache_dir/$cache_key.json";
        if (file_exists($cache_file) && (time() - filemtime($cache_file) < 604800)) { // 7 days
            $raw = @file_get_contents($cache_file);
            if ($raw) {
                echo json_encode(['status' => 'success', 'data' => json_decode($raw, true) ?: []]);
                exit;
            }
        }

        $safe_bank = mysqli_real_escape_string($con, $bank);
        $safe_state = mysqli_real_escape_string($con, $state);
        $safe_district = mysqli_real_escape_string($con, $district);
        $res = mysqli_query($con, "SELECT DISTINCT branch FROM bank_ifsc WHERE bank = '$safe_bank' AND state = '$safe_state' AND city2 = '$safe_district' AND branch IS NOT NULL AND branch != '' ORDER BY branch ASC");
        $branches = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $branches[] = $row['branch'];
            }
            mysqli_free_result($res);
        }

        if (!is_dir($cache_dir)) {
            @mkdir($cache_dir, 0755, true);
        }
        @file_put_contents($cache_file, json_encode($branches));

        echo json_encode(['status' => 'success', 'data' => $branches]);
        exit;
    }

    // --------------------------------------------------------------------------
    // Request Parameters
    // --------------------------------------------------------------------------
    $selected_bank = isset($_GET['bank']) ? trim($_GET['bank']) : (isset($_POST['bank']) ? trim($_POST['bank']) : '');
    $selected_state = isset($_GET['state']) ? trim($_GET['state']) : (isset($_POST['state']) ? trim($_POST['state']) : '');
    $selected_district = isset($_GET['district']) ? trim($_GET['district']) : (isset($_POST['district']) ? trim($_POST['district']) : '');
    $selected_branch = isset($_GET['branch']) ? trim($_GET['branch']) : (isset($_POST['branch']) ? trim($_POST['branch']) : '');

    // Fetch All Banks for Step 1 Dropdown (Cached in tmp/ to eliminate 180k-row DISTINCT scan on every load)
    $banks_cache_file = __DIR__ . '/tmp/bank_list.json';
    $all_banks = [];
    if (file_exists($banks_cache_file) && (time() - filemtime($banks_cache_file) < 604800)) {
        $raw_banks = @file_get_contents($banks_cache_file);
        if ($raw_banks) {
            $all_banks = json_decode($raw_banks, true) ?: [];
        }
    }
    if (empty($all_banks)) {
        $banks_res = mysqli_query($con, "SELECT DISTINCT bank FROM bank_ifsc WHERE bank IS NOT NULL AND bank != '' ORDER BY bank ASC");
        if ($banks_res) {
            while ($bRow = mysqli_fetch_assoc($banks_res)) {
                $all_banks[] = $bRow['bank'];
            }
            mysqli_free_result($banks_res);
            if (!is_dir(__DIR__ . '/tmp')) {
                @mkdir(__DIR__ . '/tmp', 0755, true);
            }
            @file_put_contents($banks_cache_file, json_encode($all_banks));
        }
    }

    // Fetch States if Bank is selected
    $states_for_bank = [];
    if (!empty($selected_bank)) {
        $safe_bank = mysqli_real_escape_string($con, $selected_bank);
        $s_res = mysqli_query($con, "SELECT DISTINCT state FROM bank_ifsc WHERE bank = '$safe_bank' AND state IS NOT NULL AND state != '' ORDER BY state ASC");
        if ($s_res) {
            while ($sRow = mysqli_fetch_assoc($s_res)) {
                $states_for_bank[] = $sRow['state'];
            }
        }
    }

    // Fetch Districts if Bank & State are selected
    $districts_for_state = [];
    if (!empty($selected_bank) && !empty($selected_state)) {
        $safe_bank = mysqli_real_escape_string($con, $selected_bank);
        $safe_state = mysqli_real_escape_string($con, $selected_state);
        $d_res = mysqli_query($con, "SELECT DISTINCT city2 FROM bank_ifsc WHERE bank = '$safe_bank' AND state = '$safe_state' AND city2 IS NOT NULL AND city2 != '' ORDER BY city2 ASC");
        if ($d_res) {
            while ($dRow = mysqli_fetch_assoc($d_res)) {
                $districts_for_state[] = $dRow['city2'];
            }
        }
    }

    // Fetch Branches if Bank, State & District are selected
    $branches_for_district = [];
    if (!empty($selected_bank) && !empty($selected_state) && !empty($selected_district)) {
        $safe_bank = mysqli_real_escape_string($con, $selected_bank);
        $safe_state = mysqli_real_escape_string($con, $selected_state);
        $safe_dist = mysqli_real_escape_string($con, $selected_district);
        $br_res = mysqli_query($con, "SELECT DISTINCT branch FROM bank_ifsc WHERE bank = '$safe_bank' AND state = '$safe_state' AND city2 = '$safe_dist' AND branch IS NOT NULL AND branch != '' ORDER BY branch ASC");
        if ($br_res) {
            while ($brRow = mysqli_fetch_assoc($br_res)) {
                $branches_for_district[] = $brRow['branch'];
            }
        }
    }

    // --------------------------------------------------------------------------
    // Execute Search Query when Filter is Applied (with Pagination)
    // --------------------------------------------------------------------------
    $has_search = (!empty($selected_bank));
    $results = ['count' => 0, 'data' => []];
    $total_records = 0;
    $per_page = isset($_GET['per_page']) ? max(5, min(200, intval($_GET['per_page']))) : 20;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $total_pages = 1;
    $total_unique_ifsc_count = 0;
    $total_unique_branches_count = 0;
    $total_unique_districts_count = 0;

    if ($has_search) {
        $where_clauses = [];
        $safe_bank = mysqli_real_escape_string($con, $selected_bank);
        $where_clauses[] = "bank = '$safe_bank'";

        if (!empty($selected_state)) {
            $safe_state = mysqli_real_escape_string($con, $selected_state);
            $where_clauses[] = "state = '$safe_state'";
        }

        if (!empty($selected_district)) {
            $safe_dist = mysqli_real_escape_string($con, $selected_district);
            $where_clauses[] = "city2 = '$safe_dist'";
        }

        if (!empty($selected_branch)) {
            $safe_br = mysqli_real_escape_string($con, $selected_branch);
            $where_clauses[] = "branch = '$safe_br'";
        }

        $where_sql = implode(' AND ', $where_clauses);

        // Get total count
        $count_res = mysqli_query($con, "SELECT COUNT(*) as total, COUNT(DISTINCT ifsc) as total_ifscs, COUNT(DISTINCT branch) as total_branches, COUNT(DISTINCT city2) as total_districts FROM bank_ifsc WHERE $where_sql");
        if ($count_res && ($cRow = mysqli_fetch_assoc($count_res))) {
            $total_records = (int)$cRow['total'];
            $total_unique_ifsc_count = (int)$cRow['total_ifscs'];
            $total_unique_branches_count = (int)$cRow['total_branches'];
            $total_unique_districts_count = (int)$cRow['total_districts'];
        }

        $total_pages = max(1, (int)ceil($total_records / $per_page));
        if ($page > $total_pages) {
            $page = $total_pages;
        }
        $offset = ($page - 1) * $per_page;

        // Fetch paginated results
        $results = direct_sql("SELECT * FROM bank_ifsc WHERE $where_sql ORDER BY state ASC, city2 ASC, branch ASC LIMIT $offset, $per_page");
    }

    // --------------------------------------------------------------------------
    // Dynamic SEO Metadata & Schema.org Graphs
    // --------------------------------------------------------------------------
    $schema_graphs = [];
    $faq_items = [];

    $canonical_params = [];
    if (!empty($selected_bank)) $canonical_params['bank'] = $selected_bank;
    if (!empty($selected_state)) $canonical_params['state'] = $selected_state;
    if (!empty($selected_district)) $canonical_params['district'] = $selected_district;
    if (!empty($selected_branch)) $canonical_params['branch'] = $selected_branch;

    $canonical_url = $base_url . "bank-finder" . (!empty($canonical_params) ? '?' . http_build_query($canonical_params) : '');

    if (!empty($selected_branch) && !empty($selected_district) && !empty($selected_bank)) {
        $page_title = "$selected_bank $selected_branch Branch, $selected_district IFSC & MICR Code | Online Law";
        $meta_desc = "Get verified IFSC code, MICR code, and branch address for $selected_bank $selected_branch branch in $selected_district, $selected_state. RTGS, NEFT & IMPS enabled.";
        $meta_keywords = "$selected_bank $selected_branch IFSC, $selected_bank $selected_district IFSC Code, $selected_branch MICR, $selected_bank $selected_state IFSC";
    } elseif (!empty($selected_district) && !empty($selected_bank)) {
        $page_title = "$selected_bank Branches in $selected_district ($selected_state) - IFSC & MICR Codes (2026) | Online Law";
        $meta_desc = "Find all " . number_format($total_records) . " $selected_bank branch IFSC & MICR codes in $selected_district district, $selected_state. Complete directory with addresses and branch codes.";
        $meta_keywords = "$selected_bank $selected_district IFSC, $selected_bank branches in $selected_district, $selected_bank $selected_state branches, IFSC finder $selected_bank";
    } elseif (!empty($selected_state) && !empty($selected_bank)) {
        $page_title = "$selected_bank Branches in $selected_state - District & Branch IFSC Finder (2026) | Online Law";
        $meta_desc = "Browse all $selected_bank branches across $selected_state. Search by district and branch to find verified RBI IFSC and MICR codes.";
        $meta_keywords = "$selected_bank $selected_state branches, $selected_bank IFSC $selected_state, All branches of $selected_bank in $selected_state";
    } elseif (!empty($selected_bank)) {
        $page_title = "$selected_bank IFSC & MICR Code Directory (All Branches in India) | Online Law";
        $meta_desc = "Search all " . number_format($total_records) . " branches of $selected_bank across all states and districts in India. Verified IFSC, MICR, and branch addresses.";
        $meta_keywords = "$selected_bank IFSC Code, $selected_bank Branch Finder, $selected_bank MICR Codes, All $selected_bank Branches";
    } else {
        $page_title = "Find Bank IFSC & MICR Code by Bank, State, District & Branch | Bank Finder (2026) - Online Law";
        $meta_desc = "Search and verify RBI IFSC & MICR codes for all Indian banks by selecting Bank Name, State, District/City, and Branch. 1.8+ lakh branches directory for NEFT, RTGS, IMPS money transfers.";
        $meta_keywords = "Bank Finder, IFSC Code Finder, Find Bank by State District Branch, Bank Branch Directory, All Bank IFSC Codes, RBI IFSC MICR Search";
    }

    // BreadcrumbList Schema
    $breadcrumb_items = [
        ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
        ["@type" => "ListItem", "position" => 2, "name" => "Bank Finder", "item" => $base_url . "bank-finder"]
    ];
    if (!empty($selected_bank)) {
        $breadcrumb_items[] = ["@type" => "ListItem", "position" => 3, "name" => $selected_bank, "item" => $base_url . "bank-finder?bank=" . urlencode($selected_bank)];
    }
    if (!empty($selected_state)) {
        $breadcrumb_items[] = ["@type" => "ListItem", "position" => 4, "name" => $selected_state, "item" => $base_url . "bank-finder?bank=" . urlencode($selected_bank) . "&state=" . urlencode($selected_state)];
    }
    if (!empty($selected_district)) {
        $breadcrumb_items[] = ["@type" => "ListItem", "position" => 5, "name" => $selected_district, "item" => $canonical_url];
    }
    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => $breadcrumb_items
    ];

    // BankOrCreditUnion / FinancialService Schema
    if (!empty($selected_bank)) {
        $bank_schema = [
            "@context" => "https://schema.org",
            "@type" => "FinancialService",
            "name" => $selected_bank,
            "url" => $canonical_url,
            "description" => "Official branch directory, IFSC codes, MICR codes, and RTGS/NEFT settlement details for $selected_bank."
        ];
        if (!empty($results['data'][0])) {
            $first_b = $results['data'][0];
            $bank_schema["address"] = [
                "@type" => "PostalAddress",
                "streetAddress" => $first_b['address'] ?? '',
                "addressLocality" => $first_b['city2'] ?? ($first_b['city1'] ?? ''),
                "addressRegion" => $first_b['state'] ?? '',
                "addressCountry" => "IN"
            ];
        }
        $schema_graphs[] = $bank_schema;
    }

    // FAQ items
    if (!empty($selected_bank) && !empty($selected_district)) {
        $faq_items = [
            [
                "q" => "How many branches does $selected_bank have in $selected_district, $selected_state?",
                "a" => "According to official RBI data, $selected_bank has " . number_format($total_records) . " active branch(es) operating in $selected_district district, $selected_state."
            ],
            [
                "q" => "Are $selected_bank branches in $selected_district enabled for NEFT, RTGS, and IMPS?",
                "a" => "Yes, all verified $selected_bank branches listed here are fully enabled for real-time electronic fund transfers including NEFT, 24x7 RTGS, and instant IMPS transfers."
            ],
            [
                "q" => "How do I format an IFSC code for $selected_bank?",
                "a" => "The IFSC is an 11-character alphanumeric code. The first 4 characters represent the bank name, the 5th character is always '0' (reserved for future use), and the last 6 characters represent the specific branch."
            ]
        ];
    } else {
        $faq_items = [
            [
                "q" => "How does the 4-step Bank IFSC Finder work?",
                "a" => "Select your Bank name first, which instantly loads the active States for that bank. Then select your State and District/City to view all branch listings, verified IFSC codes, MICR numbers, and contact details."
            ],
            [
                "q" => "What is the difference between an IFSC Code and a MICR Code?",
                "a" => "An IFSC (Indian Financial System Code) is an 11-character code used for online funds transfer (NEFT, RTGS, IMPS). A MICR (Magnetic Ink Character Recognition) is a 9-digit numerical code printed on cheque leaves used for automated cheque clearance."
            ],
            [
                "q" => "Are all bank branch details up-to-date with RBI records?",
                "a" => "Yes, our directory indexes over 1.8 lakh bank branches across India directly synced with the Reserve Bank of India (RBI) master database."
            ]
        ];
    }

    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => array_map(function($faq) {
            return [
                "@type" => "Question",
                "name" => $faq['q'],
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => $faq['a']
                ]
            ];
        }, $faq_items)
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Basic Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">

    <!-- Open Graph Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:site_name" content="Online Law">
    <meta property="og:image" content="<?php echo $base_url; ?>images/logo/logo.png">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta name="twitter:image" content="<?php echo $base_url; ?>images/logo/logo.png">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo $base_url; ?>images/favicon.ico">

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- JSON-LD Structured Data Schema -->
    <?php foreach ($schema_graphs as $schema): ?>
        <script type="application/ld+json">
        <?php echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>
        </script>
    <?php endforeach; ?>
</head>
<body>

<style>
    :root {
        --bank-primary: #047857;
        --bank-primary-hover: #065f46;
        --bank-accent: #10b981;
        --bank-dark: #064e3b;
        --bank-navy: #0f172a;
        --bank-surface: #f8fafc;
        --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body {
        font-family: var(--font-main) !important;
        background-color: var(--bank-surface);
        color: #1e293b;
    }

    /* Hero Banner */
    .hero-bank-banner {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #0f172a 100%);
        padding: 56px 0 64px 0;
        position: relative;
        overflow: hidden;
    }
    .bank-glow-orb-1 {
        position: absolute;
        top: -20%;
        left: -10%;
        width: 450px;
        height: 450px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, transparent 70%);
        border-radius: 50%;
        filter: blur(50px);
        pointer-events: none;
    }
    .bank-glow-orb-2 {
        position: absolute;
        bottom: -25%;
        right: -10%;
        width: 480px;
        height: 480px;
        background: radial-gradient(circle, rgba(52, 211, 153, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        filter: blur(60px);
        pointer-events: none;
    }

    /* Modern Card Container */
    .bank-console-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.8);
        position: relative;
        margin-top: -36px;
        z-index: 20;
        transition: box-shadow 0.3s ease;
    }
    .bank-console-card:hover {
        box-shadow: 0 25px 50px -12px rgba(4, 120, 87, 0.12), 0 0 0 1px rgba(203, 213, 225, 0.9);
    }

    /* Select Styles */
    .form-select-bank {
        height: 52px;
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
        padding: 10px 16px;
        font-size: 14.5px;
        font-weight: 500;
        color: #1e293b;
        background-color: #f8fafc;
        transition: all 0.2s ease-in-out;
    }
    .form-select-bank:focus {
        border-color: #10b981;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
        outline: none;
    }
    .form-select-bank:disabled {
        background-color: #f1f5f9;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Step indicator badges */
    .bank-step-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #ecfdf5;
        color: #047857;
        font-size: 11.5px;
        font-weight: 800;
        margin-right: 6px;
        border: 1px solid #a7f3d0;
    }

    /* Result spec summary tile */
    .bank-spec-tile {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 16px;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .bank-spec-tile:hover {
        transform: translateY(-2px);
        border-color: #cbd5e1;
        background: #ffffff;
    }

    /* Quick bank pill button */
    .bank-quick-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 30px;
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
        text-decoration: none !important;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .bank-quick-pill:hover {
        background: #ecfdf5;
        border-color: #10b981;
        color: #047857;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.15);
    }
    .bank-quick-pill.active {
        background: #047857;
        border-color: #047857;
        color: #ffffff;
    }

    /* Mobile Branch Card */
    .bank-card-mobile {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 14px;
        box-shadow: 0 2px 6px -1px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
    }
    .bank-card-mobile:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    /* FAQ Card */
    .bank-faq-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        margin-bottom: 12px;
        overflow: hidden;
        transition: all 0.2s ease;
    }
    .bank-faq-card .faq-header-btn {
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
    .bank-faq-card .faq-header-btn:hover {
        background: #f8fafc;
        color: var(--bank-primary);
    }
    .bank-faq-card .faq-body-text {
        padding: 0 20px 16px 20px;
        font-size: 14px;
        line-height: 1.6;
        color: #475569;
    }

    /* Pagination styles */
    .bank-pagination .page-item .page-link {
        color: #334155;
        border: 1px solid #e2e8f0;
        border-radius: 8px !important;
        margin: 0 3px;
        padding: 6px 12px;
        font-weight: 600;
        font-size: 13.5px;
        transition: all 0.2s ease;
    }
    .bank-pagination .page-item.active .page-link {
        background-color: var(--bank-primary);
        border-color: var(--bank-primary);
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(4, 120, 87, 0.25);
    }
    .bank-pagination .page-item .page-link:hover:not(.active) {
        background-color: #ecfdf5;
        color: var(--bank-primary);
        border-color: #a7f3d0;
    }
    .bank-pagination .page-item.disabled .page-link {
        color: #94a3b8;
        background-color: #f8fafc;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }

    /* Toast */
    .bank-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #064e3b;
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
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .bank-toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    @media (max-width: 767px) {
        .bank-console-card {
            margin-top: -24px;
            padding: 16px 14px !important;
            border-radius: 18px !important;
        }
        .desktop-only { display: none !important; }
        .mobile-only { display: block !important; }
        .form-select-bank {
            height: 48px !important;
            font-size: 13.5px !important;
        }
        .bank-toast {
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
<section class="hero-bank-banner">
    <div class="bank-glow-orb-1"></div>
    <div class="bank-glow-orb-2"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-12 text-center">

                <!-- Breadcrumbs -->
                <div class="d-flex justify-content-center">
                    <nav class="d-flex align-items-center flex-wrap gap-2 mb-3" style="font-size: 13.5px; color: #94a3b8;" aria-label="breadcrumb">
                        <a href="<?php echo $base_url; ?>index" style="color: #cbd5e1; text-decoration: none;"><i class="fas fa-home mr-1"></i> Home</a>
                        <span class="mx-1">/</span>
                        <a href="<?php echo $base_url; ?>bank-finder" style="color: #cbd5e1; text-decoration: none;">Bank Finder</a>
                        <?php if (!empty($selected_bank)): ?>
                            <span class="mx-1">/</span>
                            <span class="text-white font-weight-bold"><?php echo htmlspecialchars($selected_bank); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($selected_state)): ?>
                            <span class="mx-1">/</span>
                            <span class="text-white font-weight-bold"><?php echo htmlspecialchars($selected_state); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($selected_district)): ?>
                            <span class="mx-1">/</span>
                            <span class="text-white font-weight-bold"><?php echo htmlspecialchars($selected_district); ?></span>
                        <?php endif; ?>
                    </nav>
                </div>

                <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill mb-3" style="background: rgba(6, 78, 59, 0.85); border: 1px solid rgba(255, 255, 255, 0.2); color: #86efac; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">
                    <i class="fas fa-university mr-1.5"></i> RBI MASTER BANK &amp; IFSC DIRECTORY 2026
                </div>

                <h1 class="text-white font-weight-bold mb-2" style="font-size: clamp(24px, 3.8vw, 36px); letter-spacing: -0.5px; line-height: 1.25;">
                    Find Your <span style="background: linear-gradient(135deg, #86efac 0%, #38bdf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Bank &amp; IFSC Code</span>
                </h1>
                <p class="text-light mb-0" style="color: #cbd5e1 !important; font-size: 15px; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Select your <strong>Bank</strong>, <strong>State</strong>, <strong>District / City</strong>, and <strong>Branch</strong> to instantly uncover official RBI IFSC, MICR, addresses, and settlement details.
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

        <!-- 4-Step Cascading Dropdown Console Card -->
        <div class="bank-console-card p-4 p-md-4 mb-4">
            <form id="bankFinderForm" action="<?php echo $base_url; ?>bank-finder" method="GET">
                <div class="row align-items-end">

                    <!-- Step 1: Select Bank -->
                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-dark mb-1.5 d-flex align-items-center">
                            <span class="bank-step-num">1</span>
                            <span>Select Bank</span>
                        </label>
                        <select name="bank" id="bankSelect" class="form-control form-select-bank" required onchange="onBankChange();">
                            <option value="">-- Choose Bank --</option>
                            <?php foreach ($all_banks as $b): ?>
                                <option value="<?php echo htmlspecialchars($b); ?>" <?php echo ($selected_bank === $b) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($b); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Step 2: Select State -->
                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-dark mb-1.5 d-flex align-items-center">
                            <span class="bank-step-num">2</span>
                            <span>Select State</span>
                        </label>
                        <select name="state" id="stateSelect" class="form-control form-select-bank" onchange="onStateChange();" <?php echo empty($selected_bank) ? 'disabled' : ''; ?>>
                            <option value="">-- Choose State --</option>
                            <?php if (!empty($states_for_bank)): ?>
                                <?php foreach ($states_for_bank as $st): ?>
                                    <option value="<?php echo htmlspecialchars($st); ?>" <?php echo ($selected_state === $st) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($st); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Step 3: Select District / City -->
                    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                        <label class="small font-weight-bold text-dark mb-1.5 d-flex align-items-center">
                            <span class="bank-step-num">3</span>
                            <span>Select District / City</span>
                        </label>
                        <select name="district" id="districtSelect" class="form-control form-select-bank" onchange="onDistrictChange();" <?php echo empty($selected_state) ? 'disabled' : ''; ?>>
                            <option value="">-- Choose District / City --</option>
                            <?php if (!empty($districts_for_state)): ?>
                                <?php foreach ($districts_for_state as $dst): ?>
                                    <option value="<?php echo htmlspecialchars($dst); ?>" <?php echo ($selected_district === $dst) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dst); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Step 4: Select Branch & Submit Button -->
                    <div class="col-lg-3 col-md-6">
                        <label class="small font-weight-bold text-dark mb-1.5 d-flex align-items-center">
                            <span class="bank-step-num">4</span>
                            <span>Select Branch (Optional)</span>
                        </label>
                        <div class="d-flex gap-2">
                            <select name="branch" id="branchSelect" class="form-control form-select-bank mr-2" <?php echo empty($selected_district) ? 'disabled' : ''; ?>>
                                <option value="">-- All Branches --</option>
                                <?php if (!empty($branches_for_district)): ?>
                                    <?php foreach ($branches_for_district as $br): ?>
                                        <option value="<?php echo htmlspecialchars($br); ?>" <?php echo ($selected_branch === $br) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($br); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <button type="submit" class="btn btn-success font-weight-bold px-3 d-flex align-items-center justify-content-center shadow-sm" style="height: 52px; border-radius: 14px; min-width: 54px; background: #047857; border-color: #047857;" title="Search Bank">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </form>

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-3 border-top" style="border-color: #f1f5f9 !important;">
                <div class="small text-muted font-weight-medium">
                    <i class="fas fa-info-circle text-success mr-1"></i> Direct lookup: 
                    <a href="<?php echo $base_url; ?>bank-ifsc" class="text-success font-weight-bold ml-1">Search by IFSC Code</a> &bull; 
                    <a href="<?php echo $base_url; ?>bank-micr" class="text-success font-weight-bold ml-1">Search by MICR Code</a> &bull;
                    <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name" class="text-success font-weight-bold ml-1">Search by Branch Name</a>
                </div>
                <?php if ($has_search): ?>
                    <a href="<?php echo $base_url; ?>bank-finder" class="btn btn-sm btn-outline-danger font-weight-bold px-3 py-1" style="border-radius: 20px; font-size: 12px;">
                        <i class="fas fa-times mr-1"></i> Reset Filters
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- ======================================================================
             SEARCH RESULTS SECTION
             ====================================================================== -->
        <?php if ($has_search): ?>

            <?php if ($results['count'] > 0): ?>

                <!-- Dynamic Results Overview Header Card -->
                <div class="p-4 rounded-xl mb-4 bg-white shadow-sm border" style="border-radius: 20px;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <span class="badge px-3 py-1 rounded-pill font-weight-bold text-uppercase mb-1" style="background: #ecfdf5; color: #047857; font-size: 11px; border: 1px solid #a7f3d0;">
                                <i class="fas fa-check-circle mr-1"></i> RBI Verified Banking Results
                            </span>
                            <h2 class="font-weight-bold text-dark mb-0" style="font-size: clamp(20px, 2.8vw, 26px); letter-spacing: -0.5px;">
                                <?php 
                                    $heading_parts = [];
                                    if (!empty($selected_branch)) $heading_parts[] = $selected_branch;
                                    if (!empty($selected_district)) $heading_parts[] = $selected_district;
                                    if (!empty($selected_state)) $heading_parts[] = $selected_state;
                                    $heading_parts[] = $selected_bank;
                                    echo htmlspecialchars(implode(', ', $heading_parts));
                                ?>
                            </h2>
                        </div>
                        <span class="badge badge-success px-3 py-2 rounded-pill font-weight-bold mt-2 mt-sm-0" style="font-size: 13px; background: #047857;">
                            <?php echo number_format($total_records); ?> Branch Record(s) Found
                        </span>
                    </div>

                    <!-- 4 Summary Tiles -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                            <div class="bank-spec-tile">
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">Bank</small>
                                <strong class="text-dark text-truncate d-block" style="font-size: 14px;" title="<?php echo htmlspecialchars($selected_bank); ?>"><?php echo htmlspecialchars($selected_bank); ?></strong>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                            <div class="bank-spec-tile">
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">State</small>
                                <strong class="text-dark" style="font-size: 14.5px;"><?php echo htmlspecialchars($selected_state ?: 'All States'); ?></strong>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                            <div class="bank-spec-tile">
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">District / City</small>
                                <strong class="text-dark" style="font-size: 14.5px;"><?php echo htmlspecialchars($selected_district ?: 'All Districts'); ?></strong>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-6 mb-3">
                            <div class="bank-spec-tile">
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">Unique Branches</small>
                                <strong class="text-success" style="font-size: 14.5px;"><?php echo number_format($total_unique_branches_count); ?> Branch(es)</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop DataTable View -->
                <div class="bg-white rounded-xl shadow-sm border mb-4 p-4 desktop-only" style="border-radius: 20px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="small text-muted font-weight-medium">
                            Showing <strong class="text-dark"><?php echo number_format($offset + 1); ?></strong> to <strong class="text-dark"><?php echo number_format(min($offset + $per_page, $total_records)); ?></strong> of <strong class="text-dark"><?php echo number_format($total_records); ?></strong> branches
                        </div>
                        <div class="small text-muted">
                            Page <strong class="text-success"><?php echo $page; ?></strong> of <?php echo $total_pages; ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="width: 100%;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted">No</th>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted">Branch Name</th>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted">IFSC Code</th>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted">MICR Code</th>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted">City / District</th>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted">State</th>
                                    <th class="border-0 px-3 py-3 small text-uppercase font-weight-bold text-muted text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $idx = $offset + 1;
                                foreach ($results['data'] as $r): 
                                ?>
                                    <tr>
                                        <td class="px-3 py-3 align-middle text-muted small"><?php echo $idx++; ?></td>
                                        <td class="px-3 py-3 align-middle font-weight-bold text-dark">
                                            <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo urlencode($r['ifsc']); ?>" class="text-dark font-weight-bold text-decoration-none">
                                                <?php echo htmlspecialchars($r['branch']); ?>
                                            </a>
                                            <small class="d-block text-muted font-weight-normal text-truncate" style="max-width: 280px;" title="<?php echo htmlspecialchars($r['address']); ?>">
                                                <?php echo htmlspecialchars($r['address']); ?>
                                            </small>
                                        </td>
                                        <td class="px-3 py-3 align-middle text-nowrap">
                                            <span class="badge px-2.5 py-1.5 font-weight-bold" style="background: #ecfdf5; color: #047857; font-size: 13px; border: 1px solid #a7f3d0; font-family: monospace;">
                                                <?php echo htmlspecialchars($r['ifsc']); ?>
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 align-middle text-muted small text-nowrap">
                                            <?php if (!empty($r['micr']) && $r['micr'] !== 'NA' && $r['micr'] !== '-'): ?>
                                                <a href="<?php echo $base_url; ?>bank-micr?micr=<?php echo urlencode($r['micr']); ?>" class="text-muted font-weight-medium">
                                                    <?php echo htmlspecialchars($r['micr']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">NA</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-3 py-3 align-middle small text-muted"><?php echo htmlspecialchars($r['city2'] ?: $r['city1']); ?></td>
                                        <td class="px-3 py-3 align-middle small text-muted"><?php echo htmlspecialchars($r['state']); ?></td>
                                        <td class="px-3 py-3 align-middle text-right text-nowrap">
                                            <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo urlencode($r['ifsc']); ?>" class="btn btn-sm btn-outline-success px-2.5 py-1 font-weight-bold mr-1" style="border-radius: 8px; font-size: 12px;">
                                                Details &rarr;
                                            </a>
                                            <button type="button" class="btn btn-sm btn-light border text-muted px-2 py-1" onclick="copyFinderText('<?php echo htmlspecialchars($r['ifsc']); ?>');" title="Copy IFSC Code">
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
                        <span class="badge badge-success font-weight-bold" style="background: #047857;">
                            Page <?php echo $page; ?> / <?php echo $total_pages; ?>
                        </span>
                    </div>

                    <?php 
                    $mIndex = $offset + 1;
                    foreach ($results['data'] as $r): 
                    ?>
                        <div class="bank-card-mobile">
                            <div class="d-flex justify-content-between align-items-start mb-2.5">
                                <div>
                                    <span class="badge badge-light border text-muted font-weight-bold small mb-1">#<?php echo $mIndex++; ?></span>
                                    <h5 class="mb-0 font-weight-bold text-dark" style="font-size: 16px;"><?php echo htmlspecialchars($r['branch']); ?></h5>
                                    <small class="text-muted font-weight-medium"><?php echo htmlspecialchars($r['bank']); ?></small>
                                </div>
                                <span class="badge px-2 py-1 font-weight-bold" style="background: #ecfdf5; color: #047857; font-size: 12.5px; border: 1px solid #a7f3d0; font-family: monospace;">
                                    <?php echo htmlspecialchars($r['ifsc']); ?>
                                </span>
                            </div>

                            <p class="small text-muted mb-2.5" style="line-height: 1.4;">
                                <i class="fas fa-map-marker-alt text-danger mr-1"></i> <?php echo htmlspecialchars($r['address']); ?>
                            </p>

                            <div class="row no-gutters mb-2">
                                <div class="col-6 mb-2">
                                    <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">District / City</small>
                                    <p class="mb-0 text-dark" style="font-size: 13px; font-weight: 500;"><?php echo htmlspecialchars($r['city2'] ?: $r['city1']); ?></p>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">State</small>
                                    <p class="mb-0 text-dark" style="font-size: 13px; font-weight: 500;"><?php echo htmlspecialchars($r['state']); ?></p>
                                </div>
                                <div class="col-12">
                                    <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">MICR Code</small>
                                    <p class="mb-0 text-dark font-weight-medium" style="font-size: 13px;"><?php echo htmlspecialchars($r['micr'] ?: 'NA'); ?></p>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo urlencode($r['ifsc']); ?>" class="btn btn-xs btn-outline-success font-weight-bold px-3 py-1" style="border-radius: 8px; font-size: 12px;">
                                    Full Details &rarr;
                                </a>
                                <button type="button" class="btn btn-xs btn-light border text-muted px-2.5 py-1" style="border-radius: 8px; font-size: 12px;" onclick="copyFinderText('<?php echo htmlspecialchars($r['ifsc']); ?>');">
                                    <i class="far fa-copy mr-1"></i> Copy IFSC
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Server-Side Pagination Bar -->
                <?php if ($total_pages > 1): ?>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 p-3.5 bg-white rounded-xl shadow-sm border" style="border-radius: 18px;">
                        <div class="text-muted small mb-3 mb-md-0 font-weight-medium">
                            Showing <strong class="text-dark"><?php echo number_format($offset + 1); ?></strong> &ndash; <strong class="text-dark"><?php echo number_format(min($offset + $per_page, $total_records)); ?></strong> of <strong class="text-dark"><?php echo number_format($total_records); ?></strong> branches 
                            <span class="badge badge-light border ml-1 px-2 py-1 text-muted">Page <?php echo $page; ?> / <?php echo $total_pages; ?></span>
                        </div>
                        
                        <nav aria-label="Bank Finder Page navigation">
                            <ul class="pagination pagination-sm bank-pagination mb-0 flex-wrap justify-content-center">
                                <?php
                                    $p_params = [];
                                    if (!empty($selected_bank)) $p_params['bank'] = $selected_bank;
                                    if (!empty($selected_state)) $p_params['state'] = $selected_state;
                                    if (!empty($selected_district)) $p_params['district'] = $selected_district;
                                    if (!empty($selected_branch)) $p_params['branch'] = $selected_branch;
                                    if ($per_page != 20) $p_params['per_page'] = $per_page;

                                    $make_bank_p_url = function($pNum) use ($base_url, $p_params) {
                                        $arr = $p_params;
                                        $arr['page'] = $pNum;
                                        return $base_url . 'bank-finder?' . http_build_query($arr);
                                    };

                                    // Prev
                                    if ($page > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($make_bank_p_url($page - 1)) . '">&laquo; Prev</a></li>';
                                    } else {
                                        echo '<li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>';
                                    }

                                    // Window
                                    $p_window = 2;
                                    $start_p = max(1, $page - $p_window);
                                    $end_p = min($total_pages, $page + $p_window);

                                    if ($start_p > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($make_bank_p_url(1)) . '">1</a></li>';
                                        if ($start_p > 2) {
                                            echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                                        }
                                    }

                                    for ($i = $start_p; $i <= $end_p; $i++) {
                                        if ($i == $page) {
                                            echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                                        } else {
                                            echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($make_bank_p_url($i)) . '">' . $i . '</a></li>';
                                        }
                                    }

                                    if ($end_p < $total_pages) {
                                        if ($end_p < $total_pages - 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                                        }
                                        echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($make_bank_p_url($total_pages)) . '">' . $total_pages . '</a></li>';
                                    }

                                    // Next
                                    if ($page < $total_pages) {
                                        echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($make_bank_p_url($page + 1)) . '">Next &raquo;</a></li>';
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
                            <i class="fas fa-university fa-2x"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2">No Branches Found</h4>
                        <p class="text-muted mb-4" style="max-width: 480px; margin: 0 auto;">No bank branches matched your selected Bank/State/District filters. Try selecting a different location or reset filters.</p>
                        <a href="<?php echo $base_url; ?>bank-finder" class="btn btn-success font-weight-bold px-4" style="border-radius: 12px; background: #047857; border-color: #047857;">Reset Filters</a>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>

            <!-- ==================================================================
                 DEFAULT VIEW: 4-STEP VISUAL HOW IT WORKS & POPULAR BANKS
                 ================================================================== -->
            <div class="row mb-5">
                <div class="col-lg-3 col-md-6 col-12 mb-3">
                    <div class="p-4 bg-white rounded-xl border h-100 shadow-sm" style="border-radius: 18px;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle font-weight-bold text-white mr-2.5" style="width: 34px; height: 34px; background: #047857; font-size: 14px;">1</span>
                            <h5 class="font-weight-bold text-dark mb-0" style="font-size: 16px;">Select Bank</h5>
                        </div>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            Choose from over 200+ public, private, and co-operative banks operating across India.
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-3">
                    <div class="p-4 bg-white rounded-xl border h-100 shadow-sm" style="border-radius: 18px;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle font-weight-bold text-white mr-2.5" style="width: 34px; height: 34px; background: #0284c7; font-size: 14px;">2</span>
                            <h5 class="font-weight-bold text-dark mb-0" style="font-size: 16px;">Select State</h5>
                        </div>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            Pick the state or union territory where the bank branch is situated.
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-3">
                    <div class="p-4 bg-white rounded-xl border h-100 shadow-sm" style="border-radius: 18px;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle font-weight-bold text-white mr-2.5" style="width: 34px; height: 34px; background: #7c3aed; font-size: 14px;">3</span>
                            <h5 class="font-weight-bold text-dark mb-0" style="font-size: 16px;">Select District</h5>
                        </div>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            Select the administrative district or city to filter branch locations.
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-3">
                    <div class="p-4 bg-white rounded-xl border h-100 shadow-sm" style="border-radius: 18px;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle font-weight-bold text-white mr-2.5" style="width: 34px; height: 34px; background: #d97706; font-size: 14px;">4</span>
                            <h5 class="font-weight-bold text-dark mb-0" style="font-size: 16px;">Get IFSC &amp; MICR</h5>
                        </div>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            Get verified 11-digit IFSC, MICR, street address, and fund transfer verification.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Popular Indian Banks Quick Navigation -->
            <div class="p-4 bg-white rounded-xl border shadow-sm mb-5" style="border-radius: 22px;">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1" style="font-size: 19px;">
                            <i class="fas fa-university text-success mr-1.5"></i> Popular Banks in India
                        </h4>
                        <p class="text-muted small mb-0">Click any major bank to start finding branch IFSC and MICR codes.</p>
                    </div>
                </div>
                <div class="row">
                    <?php 
                    $popular_banks = [
                        'STATE BANK OF INDIA', 'PUNJAB NATIONAL BANK', 'CANARA BANK', 'UNION BANK OF INDIA',
                        'HDFC BANK', 'BANK OF BARODA', 'ICICI BANK LIMITED', 'BANK OF INDIA',
                        'AXIS BANK', 'INDIAN BANK', 'CENTRAL BANK OF INDIA', 'INDIAN OVERSEAS BANK',
                        'IDBI BANK', 'UCO BANK', 'YES BANK', 'KOTAK MAHINDRA BANK LIMITED'
                    ];
                    foreach ($popular_banks as $pb): 
                    ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-2.5">
                            <a href="<?php echo $base_url; ?>bank-finder?bank=<?php echo urlencode($pb); ?>" class="bank-quick-pill w-100 justify-content-between text-truncate">
                                <span class="text-truncate"><i class="fas fa-building text-success mr-1"></i> <?php echo htmlspecialchars($pb); ?></span>
                                <i class="fas fa-chevron-right small text-muted"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php endif; ?>

        <!-- ======================================================================
             SEO CONTENT & ACCORDION FAQ SECTION
             ====================================================================== -->
        <div class="p-4 p-md-5 bg-white rounded-xl border shadow-sm mb-4" style="border-radius: 22px;">
            <div class="row">
                <div class="col-lg-7 col-12 mb-4 mb-lg-0">
                    <h3 class="font-weight-bold text-dark mb-3" style="font-size: 22px;">
                        Complete Indian Banking &amp; IFSC Code Directory
                    </h3>
                    <p class="text-muted" style="line-height: 1.7; font-size: 14.5px;">
                        The <strong>Indian Financial System Code (IFSC)</strong> is an 11-character alphanumeric code assigned by the <strong>Reserve Bank of India (RBI)</strong> to identify each bank branch uniquely across the country. It is indispensable for performing seamless and secure electronic remittances via <strong>NEFT (National Electronic Funds Transfer)</strong>, <strong>RTGS (Real-Time Gross Settlement)</strong>, and <strong>IMPS (Immediate Payment Service)</strong>.
                    </p>
                    <p class="text-muted" style="line-height: 1.7; font-size: 14.5px;">
                        Our <strong>Bank Finder</strong> allows customers, accountants, lawyers, and business owners to filter through more than 1.8 lakh bank branches with cascading State, District, and Branch menus.
                    </p>

                    <div class="row mt-4">
                        <div class="col-sm-6 mb-3">
                            <div class="p-3 rounded-lg border bg-light">
                                <h6 class="font-weight-bold text-dark mb-1"><i class="fas fa-bolt text-warning mr-1"></i> RTGS (24x7x365)</h6>
                                <p class="text-muted small mb-0">High-value fund transfers (min. ₹2 Lakh) settled in real-time round the clock.</p>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="p-3 rounded-lg border bg-light">
                                <h6 class="font-weight-bold text-dark mb-1"><i class="fas fa-sync-alt text-primary mr-1"></i> NEFT &amp; IMPS</h6>
                                <p class="text-muted small mb-0">Instant retail money transfers with zero minimum threshold across all banks.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-12">
                    <h4 class="font-weight-bold text-dark mb-3" style="font-size: 19px;">
                        <i class="fas fa-question-circle text-success mr-1.5"></i> Frequently Asked Questions
                    </h4>

                    <div class="accordion" id="bankFaqAccordion">
                        <?php foreach ($faq_items as $fIndex => $faq): ?>
                            <div class="bank-faq-card">
                                <button class="faq-header-btn collapsed" type="button" data-toggle="collapse" data-target="#bankFaqCollapse<?php echo $fIndex; ?>" aria-expanded="false" aria-controls="bankFaqCollapse<?php echo $fIndex; ?>">
                                    <span><?php echo htmlspecialchars($faq['q']); ?></span>
                                    <i class="fas fa-chevron-down text-muted"></i>
                                </button>
                                <div id="bankFaqCollapse<?php echo $fIndex; ?>" class="collapse" data-parent="#bankFaqAccordion">
                                    <div class="faq-body-text">
                                        <?php echo htmlspecialchars($faq['a']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Copy Toast Notification -->
<div id="bankFinderToast" class="bank-toast">
    <i class="fas fa-check-circle text-success" style="font-size: 18px;"></i>
    <span id="bankFinderToastMsg">IFSC Code copied to clipboard!</span>
</div>

<?php require_once('includes/footer.php'); ?>

<!-- ==========================================================================
     DYNAMIC CASCADING JAVASCRIPT LOGIC
     ========================================================================== -->
<script>
const BASE_URL = '<?php echo $base_url; ?>';

function showToast(msg) {
    const toast = document.getElementById('bankFinderToast');
    const toastMsg = document.getElementById('bankFinderToastMsg');
    if (!toast) return;
    toastMsg.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2800);
}

function copyFinderText(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Copied: ' + text);
        }).catch(() => {
            fallbackCopy(text);
        });
    } else {
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    try {
        document.execCommand('copy');
        showToast('Copied: ' + text);
    } catch (e) {
        prompt('Copy code:', text);
    }
    document.body.removeChild(ta);
}

// 1. When Bank Changes -> Fetch States
// Client-side Memory Cache for Zero Duplicate Requests
const bankStatesCache = {};
const bankDistrictsCache = {};
const bankBranchesCache = {};

// 1. When Bank Changes -> Fetch States
function onBankChange() {
    const bankSelect = document.getElementById('bankSelect');
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');
    const branchSelect = document.getElementById('branchSelect');

    const bank = bankSelect.value.trim();

    // Reset downstream selects
    stateSelect.innerHTML = '<option value="">-- Choose State --</option>';
    districtSelect.innerHTML = '<option value="">-- Choose District / City --</option>';
    branchSelect.innerHTML = '<option value="">-- All Branches --</option>';

    districtSelect.disabled = true;
    branchSelect.disabled = true;

    if (!bank) {
        stateSelect.disabled = true;
        return;
    }

    if (bankStatesCache[bank]) {
        renderBankStates(bankStatesCache[bank]);
        return;
    }

    stateSelect.disabled = true;
    stateSelect.innerHTML = '<option value="">Loading States...</option>';

    fetch(`${BASE_URL}bank-finder?ajax_states=1&bank=${encodeURIComponent(bank)}`)
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success' && res.data && res.data.length > 0) {
                bankStatesCache[bank] = res.data;
                renderBankStates(res.data);
            } else {
                stateSelect.disabled = false;
                stateSelect.innerHTML = '<option value="">-- Choose State --</option>';
            }
        })
        .catch(err => {
            console.error('Error fetching states:', err);
            stateSelect.disabled = false;
            stateSelect.innerHTML = '<option value="">-- Choose State --</option>';
        });

    function renderBankStates(stList) {
        stateSelect.disabled = false;
        stateSelect.innerHTML = '<option value="">-- Choose State --</option>';
        stList.forEach(st => {
            const opt = document.createElement('option');
            opt.value = st;
            opt.textContent = st;
            stateSelect.appendChild(opt);
        });
    }
}

// 2. When State Changes -> Fetch Districts
function onStateChange() {
    const bankSelect = document.getElementById('bankSelect');
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');
    const branchSelect = document.getElementById('branchSelect');

    const bank = bankSelect.value.trim();
    const state = stateSelect.value.trim();

    districtSelect.innerHTML = '<option value="">-- Choose District / City --</option>';
    branchSelect.innerHTML = '<option value="">-- All Branches --</option>';
    branchSelect.disabled = true;

    if (!bank || !state) {
        districtSelect.disabled = true;
        return;
    }

    const cacheKey = bank + '||' + state;
    if (bankDistrictsCache[cacheKey]) {
        renderBankDistricts(bankDistrictsCache[cacheKey]);
        return;
    }

    districtSelect.disabled = true;
    districtSelect.innerHTML = '<option value="">Loading Districts...</option>';

    fetch(`${BASE_URL}bank-finder?ajax_districts=1&bank=${encodeURIComponent(bank)}&state=${encodeURIComponent(state)}`)
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success' && res.data && res.data.length > 0) {
                bankDistrictsCache[cacheKey] = res.data;
                renderBankDistricts(res.data);
            } else {
                districtSelect.disabled = false;
                districtSelect.innerHTML = '<option value="">-- Choose District / City --</option>';
            }
        })
        .catch(err => {
            console.error('Error fetching districts:', err);
            districtSelect.disabled = false;
            districtSelect.innerHTML = '<option value="">-- Choose District / City --</option>';
        });

    function renderBankDistricts(distList) {
        districtSelect.disabled = false;
        districtSelect.innerHTML = '<option value="">-- Choose District / City --</option>';
        distList.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d;
            opt.textContent = d;
            districtSelect.appendChild(opt);
        });
    }
}

// 3. When District Changes -> Fetch Branches
function onDistrictChange() {
    const bankSelect = document.getElementById('bankSelect');
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');
    const branchSelect = document.getElementById('branchSelect');

    const bank = bankSelect.value.trim();
    const state = stateSelect.value.trim();
    const district = districtSelect.value.trim();

    branchSelect.innerHTML = '<option value="">-- All Branches --</option>';

    if (!bank || !state || !district) {
        branchSelect.disabled = true;
        return;
    }

    const cacheKey = bank + '||' + state + '||' + district;
    if (bankBranchesCache[cacheKey]) {
        renderBankBranches(bankBranchesCache[cacheKey]);
        return;
    }

    branchSelect.disabled = true;
    branchSelect.innerHTML = '<option value="">Loading Branches...</option>';

    fetch(`${BASE_URL}bank-finder?ajax_branches=1&bank=${encodeURIComponent(bank)}&state=${encodeURIComponent(state)}&district=${encodeURIComponent(district)}`)
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success' && res.data && res.data.length > 0) {
                bankBranchesCache[cacheKey] = res.data;
                renderBankBranches(res.data);
            } else {
                branchSelect.disabled = false;
                branchSelect.innerHTML = '<option value="">-- All Branches --</option>';
            }
        })
        .catch(err => {
            console.error('Error fetching branches:', err);
            branchSelect.disabled = false;
            branchSelect.innerHTML = '<option value="">-- All Branches --</option>';
        });

    function renderBankBranches(brList) {
        branchSelect.disabled = false;
        branchSelect.innerHTML = '<option value="">-- All Branches --</option>';
        brList.forEach(br => {
            const opt = document.createElement('option');
            opt.value = br;
            opt.textContent = br;
            branchSelect.appendChild(opt);
        });
    }
}
</script>

</body>
</html>
