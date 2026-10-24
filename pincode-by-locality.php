<?php
/**
 * Online Law - Find PIN Code by Locality, Village or City
 * Fully updated for 2026 India Post Directory
 */
require_once('includes/config.php');
require_once('includes/function.php');

// Retrieve locality search query (support GET ?locality=..., ?q=..., and POST)
$locality_search = "";
if (isset($_GET['locality'])) {
    $locality_search = trim($_GET['locality']);
} elseif (isset($_GET['q'])) {
    $locality_search = trim($_GET['q']);
} elseif (isset($_POST['locality'])) {
    $locality_search = trim($_POST['locality']);
}

$res = ['count' => 0, 'data' => []];
$ct = 0;
$schema_graphs = [];
$faq_items = [];
$pincodes_unique = [];
$offices_unique = [];

if (!empty($locality_search)) {
    $safe_query = mysqli_real_escape_string($con, $locality_search);
    $limit = 250;

    // 1. Check File Cache for Frequently Requested Searches
    $cache_dir = __DIR__ . '/tmp/pincode_search';
    $cache_key = md5(strtolower(trim($locality_search)));
    $cache_file = "$cache_dir/$cache_key.json";
    $cached_rows = null;

    if (file_exists($cache_file) && (time() - filemtime($cache_file) < 259200)) { // 3 days
        $raw_cache = @file_get_contents($cache_file);
        if ($raw_cache) {
            $cached_rows = json_decode($raw_cache, true);
        }
    }

    if (is_array($cached_rows)) {
        $res = ['count' => count($cached_rows), 'data' => $cached_rows];
    } else {
        $found_rows = [];

        // 2. Strategy 1: FULLTEXT Boolean Search (Instant < 5ms via ft_pincode_search)
        $clean_words = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $locality_search);
        $words = array_filter(explode(' ', trim($clean_words)));
        
        if (!empty($words)) {
            $boolean_tokens = [];
            foreach ($words as $w) {
                if (mb_strlen($w) >= 2) {
                    $boolean_tokens[] = '+' . mysqli_real_escape_string($con, $w) . '*';
                }
            }
            if (!empty($boolean_tokens)) {
                $ft_query = implode(' ', $boolean_tokens);
                $ft_sql = "SELECT * FROM pincode WHERE MATCH(locality_name, office_name, sub_district_name, district_name) AGAINST('$ft_query' IN BOOLEAN MODE) LIMIT $limit";
                $ft_res = mysqli_query($con, $ft_sql);
                if ($ft_res && mysqli_num_rows($ft_res) > 0) {
                    while ($r = mysqli_fetch_assoc($ft_res)) {
                        $found_rows[] = $r;
                    }
                    mysqli_free_result($ft_res);
                }
            }
        }

        // 3. Strategy 2: Fast B-Tree Prefix Match if Fulltext returned 0 (e.g. rare short token)
        if (empty($found_rows)) {
            $pre_sql = "SELECT * FROM pincode WHERE locality_name LIKE '$safe_query%' OR office_name LIKE '$safe_query%' OR district_name LIKE '$safe_query%' LIMIT $limit";
            $pre_res = mysqli_query($con, $pre_sql);
            if ($pre_res && mysqli_num_rows($pre_res) > 0) {
                while ($r = mysqli_fetch_assoc($pre_res)) {
                    $found_rows[] = $r;
                }
                mysqli_free_result($pre_res);
            }
        }

        // 4. Strategy 3: Substring Fallback with Strict Limit
        if (empty($found_rows) && mb_strlen($safe_query) >= 3) {
            $sub_sql = "SELECT * FROM pincode WHERE locality_name LIKE '%$safe_query%' OR office_name LIKE '%$safe_query%' OR district_name LIKE '%$safe_query%' LIMIT 50";
            $sub_res = mysqli_query($con, $sub_sql);
            if ($sub_res && mysqli_num_rows($sub_res) > 0) {
                while ($r = mysqli_fetch_assoc($sub_res)) {
                    $found_rows[] = $r;
                }
                mysqli_free_result($sub_res);
            }
        }

        // Sort in PHP memory in < 1ms to eliminate heavy MySQL filesort
        if (!empty($found_rows)) {
            usort($found_rows, function($a, $b) {
                $cmpState = strcmp($a['state_name'] ?? '', $b['state_name'] ?? '');
                if ($cmpState !== 0) return $cmpState;
                $cmpDist = strcmp($a['district_name'] ?? '', $b['district_name'] ?? '');
                if ($cmpDist !== 0) return $cmpDist;
                return strcmp($a['locality_name'] ?? '', $b['locality_name'] ?? '');
            });

            if (!is_dir($cache_dir)) {
                @mkdir($cache_dir, 0755, true);
            }
            @file_put_contents($cache_file, json_encode($found_rows));
        }

        $res = ['count' => count($found_rows), 'data' => $found_rows];
    }
    $ct = $res['count'];

    $canonical_url = $base_url . "pincode-by-locality?locality=" . urlencode($locality_search);

    if ($ct > 0) {
        $dist = $res['data'][0]['district_name'] ?? '';
        $st = $res['data'][0]['state_name'] ?? '';
        
        // Collect unique PINs and post offices
        $pincodes = [];
        $offices = [];
        $localities = [];
        foreach ($res['data'] as $r) {
            if (!empty($r['pincode'])) $pincodes[] = $r['pincode'];
            if (!empty($r['office_name'])) $offices[] = $r['office_name'];
            if (!empty($r['locality_name'])) $localities[] = $r['locality_name'];
        }
        $pincodes_unique = array_values(array_unique($pincodes));
        $offices_unique = array_values(array_unique($offices));
        $localities_unique = array_values(array_unique($localities));
        $pin_sample = implode(', ', array_slice($pincodes_unique, 0, 4));

        $page_title = "PIN Code of $locality_search - " . (!empty($dist) ? "$dist, $st " : "") . "Postal Codes (2026) | Online Law";
        $meta_desc = "Find official 6-digit Indian PIN Code for $locality_search in $dist ($st). List of all post offices, delivery status, taluk, and postal codes (" . (!empty($pin_sample) ? $pin_sample : "$dist PINs") . "). Verified India Post Directory 2026.";
        $meta_keywords = "$locality_search PIN Code, PIN Code for $locality_search, $locality_search Post Office, Postal Code $locality_search, $locality_search $dist Pincode, Village Pincode $locality_search, Speed Post $locality_search";

        // BreadcrumbList Schema
        $schema_graphs[] = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
                ["@type" => "ListItem", "position" => 2, "name" => "PIN Code Directory", "item" => $base_url . "pincode-finder"],
                ["@type" => "ListItem", "position" => 3, "name" => "Locality Search", "item" => $base_url . "pincode-by-locality"],
                ["@type" => "ListItem", "position" => 4, "name" => "$locality_search ($dist)", "item" => $canonical_url]
            ]
        ];

        // FAQ Items
        $faq_items = [
            [
                "q" => "What is the official PIN Code for $locality_search?",
                "a" => "The primary PIN code(s) serving $locality_search in $dist, $st include: " . implode(', ', array_slice($pincodes_unique, 0, 6)) . "."
            ],
            [
                "q" => "Which post offices cater to $locality_search?",
                "a" => "$locality_search is covered by " . count($offices_unique) . " post office(s): " . implode(', ', array_slice($offices_unique, 0, 8)) . " in $dist district, $st."
            ],
            [
                "q" => "How to write the postal address for $locality_search for speed post delivery?",
                "a" => "Format: [Recipient Name], [House/Street/Colony], $locality_search, $dist, $st - " . (!empty($pincodes_unique[0]) ? $pincodes_unique[0] : "PIN Code") . ", India."
            ],
            [
                "q" => "Are parcels and Speed Post delivered to $locality_search?",
                "a" => "Yes, India Post and major courier carriers provide regular delivery coverage to $locality_search using the designated 6-digit postal code."
            ]
        ];
    } else {
        $page_title = "PIN Code Search for '$locality_search' | India Postal Directory (2026) - Online Law";
        $meta_desc = "Search results and postal lookup for '$locality_search' on Online Law Indian Postal Index Directory 2026.";
        $meta_keywords = "$locality_search PIN Code, Village PIN Code Search, Post Office Lookup $locality_search";

        $schema_graphs[] = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
                ["@type" => "ListItem", "position" => 2, "name" => "Locality Search", "item" => $canonical_url]
            ]
        ];
    }
} else {
    $canonical_url = $base_url . "pincode-by-locality";
    $page_title = "Find PIN Code by Locality, Village or City Name (2026) | Online Law";
    $meta_desc = "Search for any Indian PIN Code by entering a locality, city, town, or village name. Fast, comprehensive postal index directory with 150,000+ post offices across India.";
    $meta_keywords = "PIN Code by Locality, Village PIN Code Search, City Postal Codes India, Post Office Name Search, Find PIN by Area, Village Post Office Lookup India";

    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "PIN Code by Locality", "item" => $canonical_url]
        ]
    ];

    $faq_items = [
        [
            "q" => "How can I find a PIN code by entering a village or area name?",
            "a" => "Simply type the name of the village, colony, sector, city, or post office into the search field above and click 'Search'. Our system will instantly find all matching post offices and their exact 6-digit PIN codes."
        ],
        [
            "q" => "What if multiple PIN codes match my locality name?",
            "a" => "Many cities and villages share common names across different states and districts (e.g. Rampur, Gandhi Nagar, Indirapuram). Check the District and State columns in the search results table to identify your exact location."
        ],
        [
            "q" => "Are rural village branch post offices (BO) included in this directory?",
            "a" => "Yes, our database indexes all categories of post offices in India: Head Post Offices (HO), Sub Post Offices (SO), and rural Branch Post Offices (BO)."
        ],
        [
            "q" => "Can I lookup PIN code details by digit code directly?",
            "a" => "Yes, you can use our 'PIN Code Search by 6-Digit Code' tool to lookup any postal code directly."
        ]
    ];
}

// Add FAQ Schema graph if FAQ items exist
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
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    <meta name="author" content="Online Law">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Online Law">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta property="og:image" content="<?php echo $base_url; ?>images/favicon.png">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta name="twitter:image" content="<?php echo $base_url; ?>images/favicon.png">

    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Schema.org JSON-LD Structured Data -->
    <?php if (!empty($schema_graphs)): ?>
    <script type="application/ld+json">
    <?php echo json_encode(count($schema_graphs) === 1 ? $schema_graphs[0] : ["@context" => "https://schema.org", "@graph" => $schema_graphs], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>
    <?php endif; ?>

    <style>
        :root {
            --pin-primary: #2563eb;
            --pin-primary-hover: #1d4ed8;
            --pin-accent: #38bdf8;
            --pin-dark: #0f172a;
            --pin-surface: #f8fafc;
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-code: 'JetBrains Mono', monospace;
        }

        body {
            font-family: var(--font-main) !important;
            background-color: var(--pin-surface);
            color: #1e293b;
        }

        /* Hero Banner */
        .hero-pin-banner {
            background-color: #0b1120 !important;
            background: radial-gradient(135% 100% at 50% 0%, #0b1120 0%, #1e1b4b 55%, #0f172a 100%) !important;
            color: #ffffff !important;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 56px 0 64px 0;
        }
        .hero-pin-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.7;
            pointer-events: none;
        }
        .pin-glow-orb-1 {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, rgba(99, 102, 241, 0) 70%);
            top: -120px;
            left: -100px;
            pointer-events: none;
            filter: blur(50px);
        }
        .pin-glow-orb-2 {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.2) 0%, rgba(16, 185, 129, 0) 70%);
            bottom: -140px;
            right: -80px;
            pointer-events: none;
            filter: blur(60px);
        }

        /* Modern Card Console */
        .pin-console-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.8);
            position: relative;
            margin-top: -36px;
            z-index: 20;
            transition: box-shadow 0.3s ease;
        }

        /* Quick Tool Switcher Pills */
        .tool-switcher-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding-bottom: 20px;
            margin-bottom: 24px;
            border-bottom: 1px solid #e2e8f0;
        }
        .tool-switcher-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            background: #f1f5f9;
            text-decoration: none !important;
            transition: all 0.2s ease;
        }
        .tool-switcher-pill:hover {
            background: #e2e8f0;
            color: #0f172a;
            transform: translateY(-1px);
        }
        .tool-switcher-pill.active {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        /* Search input bar */
        .search-input-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        .search-input-group i.search-icon {
            position: absolute;
            left: 20px;
            color: #94a3b8;
            font-size: 18px;
            z-index: 5;
        }
        .search-input-field {
            height: 56px;
            padding-left: 54px;
            padding-right: 20px;
            border-radius: 14px;
            border: 2px solid #e2e8f0;
            font-size: 15.5px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .search-input-field:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
            outline: none;
        }
        .search-btn-primary {
            height: 56px;
            border-radius: 14px;
            background: #2563eb;
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        .search-btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
        }

        /* Result summary hero block */
        .pin-result-hero {
            background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
            border: 1.5px solid #bfdbfe;
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 28px;
        }

        .pin-spec-tile {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            height: 100%;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .pin-spec-tile:hover {
            transform: translateY(-2px);
            border-color: #93c5fd;
        }

        /* PIN Code Badges */
        .pin-code-badge {
            font-family: var(--font-code);
            font-weight: 700;
            letter-spacing: 0.5px;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 4px 10px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none !important;
        }

        /* Copy Button */
        .btn-copy-code {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-copy-code:hover {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #60a5fa;
        }

        /* Mobile Card */
        .pin-card-mobile {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 6px -1px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
        }
        .pin-card-mobile:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        /* FAQ Card */
        .pin-faq-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .pin-faq-header {
            padding: 18px 22px;
            background: #ffffff;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.2s ease;
            text-decoration: none !important;
        }
        .pin-faq-header:hover {
            background: #f8fafc;
            color: #2563eb;
        }
        .pin-faq-body {
            padding: 0 22px 18px 22px;
            color: #475569;
            font-size: 14px;
            line-height: 1.65;
        }

        /* Toast */
        .pin-toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #0f172a;
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
        .pin-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 767px) {
            .pin-console-card {
                margin-top: -24px;
                padding: 18px 16px !important;
                border-radius: 18px !important;
            }
            .desktop-only { display: none !important; }
            .mobile-only { display: block !important; }
        }
        @media (min-width: 768px) {
            .mobile-only { display: none !important; }
            .desktop-only { display: block !important; }
        }
    </style>
</head>
<body>

<?php require_once('includes/header.php'); ?>

<!-- ==========================================================================
     HERO BANNER
     ========================================================================== -->
<section class="hero-pin-banner">
    <div class="pin-glow-orb-1"></div>
    <div class="pin-glow-orb-2"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-12 text-center">

                <!-- Breadcrumbs -->
                <div class="d-flex justify-content-center">
                    <nav class="d-flex align-items-center flex-wrap gap-2 mb-3" style="font-size: 13.5px; color: #94a3b8;" aria-label="breadcrumb">
                        <a href="<?php echo $base_url; ?>index" style="color: #cbd5e1; text-decoration: none;"><i class="fas fa-home mr-1"></i> Home</a>
                        <span class="mx-1">/</span>
                        <a href="<?php echo $base_url; ?>pincode-finder" style="color: #cbd5e1; text-decoration: none;">PIN Code Finder</a>
                        <span class="mx-1">/</span>
                        <span class="text-white font-weight-bold">Locality Search</span>
                        <?php if (!empty($locality_search)): ?>
                            <span class="mx-1">/</span>
                            <span class="text-primary font-weight-bold" style="color: #60a5fa !important;"><?php echo htmlspecialchars($locality_search); ?></span>
                        <?php endif; ?>
                    </nav>
                </div>

                <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill mb-3" style="background: rgba(30, 27, 75, 0.85); border: 1px solid rgba(255, 255, 255, 0.2); color: #93c5fd; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">
                    <i class="fas fa-map-marked-alt mr-1.5"></i> AREA, VILLAGE &amp; CITY POSTAL DIRECTORY 2026
                </div>

                <h1 class="text-white font-weight-bold mb-2" style="font-size: clamp(24px, 3.8vw, 36px); letter-spacing: -0.5px; line-height: 1.25;">
                    Find PIN Code by <span style="background: linear-gradient(135deg, #93c5fd 0%, #38bdf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Locality, Village or City</span>
                </h1>
                <p class="text-light mb-0" style="color: #cbd5e1 !important; font-size: 15px; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Search any Indian town, sector, colony, or village name to uncover official 6-digit PIN codes, delivery status, and post office addresses.
                </p>

            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     MAIN BODY & SEARCH CONSOLE
     ========================================================================== -->
<div class="full-row py-4" style="background-color: #f8fafc; position: relative; z-index: 10;">
    <div class="container">

        <!-- Console Card -->
        <div class="pin-console-card p-4 p-md-4 mb-4">

            <!-- Tool Switcher Bar -->
            <div class="tool-switcher-bar">
                <a href="<?php echo $base_url; ?>pincode-finder" class="tool-switcher-pill">
                    <i class="fas fa-landmark text-primary"></i> Cascading PIN Finder
                </a>
                <a href="<?php echo $base_url; ?>pincode" class="tool-switcher-pill">
                    <i class="fas fa-search-location"></i> Search by PIN Code
                </a>
                <a href="<?php echo $base_url; ?>pincode-by-locality" class="tool-switcher-pill active">
                    <i class="fas fa-map-marked-alt"></i> Search by Locality / City
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="<?php echo $base_url; ?>pincode-by-locality" class="mb-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-8 mb-3 mb-md-0">
                        <div class="search-input-group">
                            <i class="fas fa-map-marker-alt search-icon"></i>
                            <input type="text" name="locality" class="form-control search-input-field" placeholder="Enter Locality, Village, Sector or City (e.g. Connaught Place, Andheri East, Indirapuram, Whitefield)" minlength="3" maxlength="60" value="<?php echo htmlspecialchars($locality_search); ?>" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <button type="submit" class="btn btn-block search-btn-primary d-flex align-items-center justify-content-center">
                            <i class="fas fa-search mr-2"></i> Search Locality
                        </button>
                    </div>
                </div>
            </form>

            <!-- Quick Suggestions -->
            <div class="d-flex align-items-center flex-wrap justify-content-center gap-2 pt-2 border-top">
                <span class="text-muted small font-weight-bold mr-2"><i class="fas fa-bolt text-warning mr-1"></i> Popular Localities:</span>
                <a href="<?php echo $base_url; ?>pincode-by-locality?locality=Connaught+Place" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-size: 11.5px;">Connaught Place</a>
                <a href="<?php echo $base_url; ?>pincode-by-locality?locality=Andheri+East" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-size: 11.5px;">Andheri East</a>
                <a href="<?php echo $base_url; ?>pincode-by-locality?locality=Indirapuram" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-size: 11.5px;">Indirapuram</a>
                <a href="<?php echo $base_url; ?>pincode-by-locality?locality=Whitefield" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-size: 11.5px;">Whitefield</a>
                <a href="<?php echo $base_url; ?>pincode-by-locality?locality=Salt+Lake" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-size: 11.5px;">Salt Lake</a>
                <a href="<?php echo $base_url; ?>pincode-by-locality?locality=Kankarbagh" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-size: 11.5px;">Kankarbagh</a>
            </div>
        </div>

        <!-- ======================================================================
             SEARCH RESULTS DISPLAY
             ====================================================================== -->
        <?php if (!empty($locality_search)): ?>
            <?php if ($ct > 0): ?>
                
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                    <h3 class="font-weight-bold text-dark mb-0" style="font-size: 19px;">
                        Found <span class="text-primary"><?php echo number_format($ct); ?></span> Post Office record(s) matching "<span class="text-dark"><?php echo htmlspecialchars($locality_search); ?></span>"
                    </h3>
                    <div class="text-muted small">
                        Showing verified India Post data
                    </div>
                </div>

                <!-- Locality Summary Spec Box -->
                <div class="p-3 p-md-4 rounded-xl mb-4" style="background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); border: 1.5px solid #bfdbfe; border-radius: 18px;">
                    <div class="row align-items-center">
                        <div class="col-lg-4 col-md-6 col-6 mb-2.5 mb-lg-0">
                            <div class="pin-spec-tile">
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">Primary District</small>
                                <strong class="text-dark" style="font-size: 14px;"><?php echo htmlspecialchars($dist ?: 'N/A'); ?></strong>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-6 mb-2.5 mb-lg-0">
                            <div class="pin-spec-tile">
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">State / UT</small>
                                <strong class="text-dark" style="font-size: 14px;"><?php echo htmlspecialchars($st ?: 'N/A'); ?></strong>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12 col-12">
                            <div class="pin-spec-tile">
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">Matching PIN Codes</small>
                                <div class="d-flex flex-wrap gap-1.5 mt-1" style="gap: 6px;">
                                    <?php foreach(array_slice($pincodes_unique, 0, 5) as $puk): ?>
                                        <a href="<?php echo $base_url; ?>pincode?pincode=<?php echo urlencode($puk); ?>" class="badge badge-primary font-weight-bold px-2 py-1" style="font-family: var(--font-code); font-size: 12px; text-decoration: none;">
                                            <?php echo htmlspecialchars($puk); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Table (Desktop) -->
                <div class="desktop-only mb-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden;">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="locality_table">
                                <thead style="background: #f8fafc; font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #475569;">
                                    <tr>
                                        <th class="py-3 px-3">#</th>
                                        <th class="py-3 px-3">Locality / Village</th>
                                        <th class="py-3 px-3">Post Office Name</th>
                                        <th class="py-3 px-3">Sub District</th>
                                        <th class="py-3 px-3">District</th>
                                        <th class="py-3 px-3">State / UT</th>
                                        <th class="py-3 px-3">PIN Code</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 13.5px;">
                                    <?php $idx = 1; foreach ($res['data'] as $row): ?>
                                        <tr>
                                            <td class="py-3 px-3 text-muted"><?php echo $idx++; ?></td>
                                            <td class="py-3 px-3 font-weight-bold text-dark"><?php echo htmlspecialchars($row['locality_name']); ?></td>
                                            <td class="py-3 px-3"><?php echo htmlspecialchars($row['office_name']); ?></td>
                                            <td class="py-3 px-3"><?php echo htmlspecialchars($row['sub_district_name']); ?></td>
                                            <td class="py-3 px-3"><?php echo htmlspecialchars($row['district_name']); ?></td>
                                            <td class="py-3 px-3"><?php echo htmlspecialchars($row['state_name']); ?></td>
                                            <td class="py-3 px-3">
                                                <div class="d-flex align-items-center">
                                                    <a href="<?php echo $base_url; ?>pincode?pincode=<?php echo urlencode($row['pincode']); ?>" class="pin-code-badge mr-1">
                                                        <?php echo htmlspecialchars($row['pincode']); ?>
                                                    </a>
                                                    <button type="button" class="btn-copy-code" onclick="copyText('<?php echo htmlspecialchars($row['pincode']); ?>', 'PIN Code Copied!')" title="Copy PIN">
                                                        <i class="far fa-copy"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Mobile Card List View -->
                <div class="mobile-only mb-4">
                    <?php $mi = 1; foreach ($res['data'] as $row): ?>
                        <div class="pin-card-mobile">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="font-weight-bold text-dark mb-0" style="font-size: 16px;">
                                    <?php echo htmlspecialchars($row['locality_name']); ?>
                                </h5>
                                <div class="d-flex align-items-center">
                                    <a href="<?php echo $base_url; ?>pincode?pincode=<?php echo urlencode($row['pincode']); ?>" class="pin-code-badge mr-1" style="font-size: 12px;">
                                        <?php echo htmlspecialchars($row['pincode']); ?>
                                    </a>
                                    <button type="button" class="btn-copy-code" onclick="copyText('<?php echo htmlspecialchars($row['pincode']); ?>', 'PIN Code Copied!')">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="p-2.5 rounded-lg mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="row no-gutters">
                                    <div class="col-6 mb-2">
                                        <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">Post Office</small>
                                        <div class="text-dark font-weight-semibold" style="font-size: 12.5px;"><?php echo htmlspecialchars($row['office_name']); ?></div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">District</small>
                                        <div class="text-dark font-weight-semibold" style="font-size: 12.5px;"><?php echo htmlspecialchars($row['district_name']); ?></div>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">State / UT</small>
                                        <div class="text-dark font-weight-semibold" style="font-size: 12.5px;"><?php echo htmlspecialchars($row['state_name']); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 pt-2 border-top" style="gap: 6px;">
                                <a href="<?php echo $base_url; ?>pincode?pincode=<?php echo urlencode($row['pincode']); ?>" class="btn btn-sm btn-light border text-dark font-weight-bold" style="font-size: 11.5px; border-radius: 8px;">
                                    <i class="fas fa-barcode text-primary mr-1"></i> PIN Details
                                </a>
                                <a href="<?php echo $base_url; ?>pincode-finder?state=<?php echo urlencode($row['state_name']); ?>&district=<?php echo urlencode($row['district_name']); ?>" class="btn btn-sm btn-light border text-dark font-weight-bold" style="font-size: 11.5px; border-radius: 8px;">
                                    <i class="fas fa-landmark text-success mr-1"></i> All in <?php echo htmlspecialchars($row['district_name']); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- ==============================================================
                     FAQ SECTION
                     ============================================================== -->
                <?php if (!empty($faq_items)): ?>
                    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px; background: #ffffff;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3" style="width: 42px; height: 42px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div>
                                <h3 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Frequently Asked Questions</h3>
                                <p class="text-muted small mb-0">Learn how to address mail and find postal codes for <?php echo htmlspecialchars($locality_search); ?>.</p>
                            </div>
                        </div>

                        <div class="accordion" id="localityFaqAccordion">
                            <?php foreach ($faq_items as $index => $faq): ?>
                                <div class="pin-faq-card">
                                    <div class="pin-faq-header" data-toggle="collapse" data-target="#locFaqCollapse<?php echo $index; ?>" aria-expanded="<?php echo ($index === 0) ? 'true' : 'false'; ?>">
                                        <span><i class="fas fa-circle-question text-primary mr-2"></i> <?php echo htmlspecialchars($faq['q']); ?></span>
                                        <i class="fas fa-chevron-down text-muted" style="font-size: 12px;"></i>
                                    </div>
                                    <div id="locFaqCollapse<?php echo $index; ?>" class="collapse <?php echo ($index === 0) ? 'show' : ''; ?>" data-parent="#localityFaqAccordion">
                                        <div class="pin-faq-body">
                                            <?php echo htmlspecialchars($faq['a']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- Not Found Alert -->
                <div class="alert p-4 mb-4" style="background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b; border-radius: 18px;">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-circle-exclamation mr-2" style="font-size: 22px;"></i>
                        <h4 class="font-weight-bold mb-0" style="font-size: 17px;">No Postal Records Found for "<?php echo htmlspecialchars($locality_search); ?>"</h4>
                    </div>
                    <p class="mb-3 small" style="line-height: 1.6;">
                        Try searching with a shorter keyword or check spelling (e.g., try "Delhi" instead of "New Delhi Central"). You can also explore by State and District using our Cascading PIN Finder.
                    </p>
                    <div class="d-flex flex-wrap gap-2" style="gap: 8px;">
                        <a href="<?php echo $base_url; ?>pincode-finder" class="btn btn-sm btn-danger font-weight-bold px-3 py-2" style="border-radius: 10px;">
                            <i class="fas fa-landmark mr-1.5"></i> Launch Cascading PIN Finder
                        </a>
                        <a href="<?php echo $base_url; ?>pincode" class="btn btn-sm btn-outline-danger font-weight-bold px-3 py-2" style="border-radius: 10px;">
                            <i class="fas fa-search-location mr-1.5"></i> Search by 6-Digit PIN
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- ======================================================================
             SEARCH TIPS & HELPFUL GUIDE
             ====================================================================== -->
        <div class="card border-0 shadow-sm p-4 p-md-5 mb-4" style="border-radius: 24px; background: #ffffff;">
            <h3 class="font-weight-bold text-dark mb-2" style="font-size: 20px;">
                <i class="fas fa-lightbulb text-warning mr-2"></i> Tips for Locating Postal Codes
            </h3>
            <p class="text-muted small mb-4" style="line-height: 1.65;">
                Finding the right PIN code ensures your Speed Post letters and eCommerce parcels are routed without delay.
            </p>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-primary mb-1" style="font-size: 15px;">
                            <i class="fas fa-city mr-1"></i> Search by Town or Colony
                        </div>
                        <div class="text-dark font-weight-bold" style="font-size: 14px;">e.g. Indirapuram, Bandra West</div>
                        <div class="text-muted small mt-1">Typing a sub-locality or residential colony helps quickly isolate urban post offices.</div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-success mb-1" style="font-size: 15px;">
                            <i class="fas fa-tree mr-1"></i> Search by Village Name
                        </div>
                        <div class="text-dark font-weight-bold" style="font-size: 14px;">e.g. Rampur, Shivpur</div>
                        <div class="text-muted small mt-1">Searches rural Branch Post Offices (BO) linked to their designated Sub Post Office (SO).</div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-info mb-1" style="font-size: 15px;">
                            <i class="fas fa-landmark mr-1"></i> Search by District
                        </div>
                        <div class="text-dark font-weight-bold" style="font-size: 14px;">e.g. Patna, Pune, Jaipur</div>
                        <div class="text-muted small mt-1">Displays all major post offices operating within that sorting and delivery division.</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Toast Notification -->
<div id="pinToast" class="pin-toast">
    <i class="fas fa-check-circle text-primary" style="font-size: 18px; color: #60a5fa !important;"></i>
    <span id="pinToastMsg">Copied to clipboard!</span>
</div>

<?php require_once('includes/footer.php'); ?>

<script>
    $(document).ready(function() {
        if ($('#locality_table').length) {
            $('#locality_table').DataTable({
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 15,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Filter post offices..."
                }
            });
        }
    });

    function copyText(text, msg) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function() {
                showToast(msg || 'Copied to clipboard!');
            });
        } else {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                showToast(msg || 'Copied to clipboard!');
            } catch (err) {
                console.error('Copy failed', err);
            }
            document.body.removeChild(textArea);
        }
    }

    function showToast(msg) {
        var toast = document.getElementById('pinToast');
        var msgEl = document.getElementById('pinToastMsg');
        if (toast && msgEl) {
            msgEl.innerText = msg;
            toast.classList.add('show');
            setTimeout(function() {
                toast.classList.remove('show');
            }, 2500);
        }
    }
</script>
</body>
</html>
