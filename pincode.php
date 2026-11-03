<?php
/**
 * Online Law - PIN Code Search by 6-Digit Code
 * Fully updated for 2026 India Post Directory
 */
require_once('includes/config.php');
require_once('includes/function.php');

$pincode = isset($_REQUEST['pincode']) ? trim($_REQUEST['pincode']) : '';
$res = ['count' => 0, 'data' => []];
$ct = 0;
$schema_graphs = [];
$faq_items = [];

$postal_zones = [
    '1' => 'Northern Zone 1 (Delhi, Haryana, Punjab, Himachal Pradesh, J&K, Chandigarh, Ladakh)',
    '2' => 'Northern Zone 2 (Uttar Pradesh, Uttarakhand)',
    '3' => 'Western Zone 1 (Rajasthan, Gujarat, Daman & Diu, Dadra & Nagar Haveli)',
    '4' => 'Western Zone 2 (Maharashtra, Goa, Madhya Pradesh, Chhattisgarh)',
    '5' => 'Southern Zone 1 (Andhra Pradesh, Telangana, Karnataka)',
    '6' => 'Southern Zone 2 (Tamil Nadu, Kerala, Puducherry, Lakshadweep)',
    '7' => 'Eastern Zone 1 (West Bengal, Odisha, Assam, Sikkim, North Eastern States)',
    '8' => 'Eastern Zone 2 (Bihar, Jharkhand)',
    '9' => 'Army Postal Service (APS / Field Post Offices)'
];

if (!empty($pincode) && is_numeric($pincode)) {
    $safe_pincode = mysqli_real_escape_string($con, $pincode);
    $sql = "SELECT * FROM pincode WHERE pincode = '$safe_pincode' ORDER BY locality_name ASC";
    $res = direct_sql($sql);
    $ct = $res['count'];

    $canonical_url = $base_url . "pincode?pincode=" . urlencode($pincode);
    $zone_digit = substr($pincode, 0, 1);
    $zone_name = $postal_zones[$zone_digit] ?? 'India Postal Circle';

    if ($ct > 0) {
        $dist = $res['data'][0]['district_name'] ?? '';
        $st = $res['data'][0]['state_name'] ?? '';
        $sub_dist = $res['data'][0]['sub_district_name'] ?? '';
        
        // Collect unique post offices and localities
        $offices = [];
        $localities = [];
        foreach ($res['data'] as $r) {
            if (!empty($r['office_name'])) $offices[] = $r['office_name'];
            if (!empty($r['locality_name'])) $localities[] = $r['locality_name'];
        }
        $offices_unique = array_values(array_unique($offices));
        $localities_unique = array_values(array_unique($localities));
        $offices_sample = implode(', ', array_slice($offices_unique, 0, 4));

        $page_title = "PIN Code $pincode - " . (!empty($dist) ? "$dist, $st " : "") . "Post Office & Locality Details (2026) | Online Law";
        $meta_desc = "Get official post office details for PIN Code $pincode in $dist ($st). List of all post offices (" . (!empty($offices_sample) ? $offices_sample : "$dist Area") . "), localities, taluka, and delivery status. Verified India Post Directory 2026.";
        $meta_keywords = "PIN Code $pincode, $pincode Post Office, $pincode Postal Code, $pincode PIN Code Details, $dist Pincode $pincode, Post Offices in $pincode, Speed Post $pincode, India Post $pincode";

        // BreadcrumbList Schema
        $schema_graphs[] = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
                ["@type" => "ListItem", "position" => 2, "name" => "PIN Code Directory", "item" => $base_url . "pincode-finder"],
                ["@type" => "ListItem", "position" => 3, "name" => "PIN Code $pincode ($dist)", "item" => $canonical_url]
            ]
        ];

        // PostalAddress Schema
        $schema_graphs[] = [
            "@context" => "https://schema.org",
            "@type" => "PostalAddress",
            "postalCode" => $pincode,
            "addressLocality" => !empty($dist) ? $dist : $sub_dist,
            "addressRegion" => $st,
            "addressCountry" => "IN"
        ];

        // FAQ Schema for this PIN Code
        $faq_items = [
            [
                "q" => "What district and state does PIN Code $pincode belong to?",
                "a" => "PIN Code $pincode belongs to $dist district in the state/UT of $st, India."
            ],
            [
                "q" => "Which post offices are covered under PIN Code $pincode?",
                "a" => "PIN Code $pincode covers " . count($offices_unique) . " post office location(s) including: " . implode(', ', array_slice($offices_unique, 0, 8)) . "."
            ],
            [
                "q" => "How is the 6-digit PIN code $pincode structured?",
                "a" => "The first digit '" . substr($pincode, 0, 1) . "' represents the postal zone ($zone_name), the first two digits '" . substr($pincode, 0, 2) . "' represent the sub-zone, the first three digits '" . substr($pincode, 0, 3) . "' represent the sorting district ($dist), and the last three digits '" . substr($pincode, 3, 3) . "' indicate the specific delivery post office."
            ],
            [
                "q" => "Can I use PIN Code $pincode for Speed Post and courier parcels?",
                "a" => "Yes, PIN Code $pincode is the official postal index number recognized by India Post and all domestic & international courier services (Blue Dart, Delhivery, DTDC, FedEx) for parcel and letter routing."
            ]
        ];
    } else {
        $canonical_url = $base_url . "pincode?pincode=" . urlencode($pincode);
        $page_title = "PIN Code $pincode - Not Found | India Post Office Lookup (2026) - Online Law";
        $meta_desc = "No post office record found for PIN Code $pincode. Search across 150,000+ Indian post offices on Online Law Postal Directory 2026.";
        $meta_keywords = "PIN Code $pincode, $pincode Post Office, Postal Index Number $pincode, India Post Finder";

        $schema_graphs[] = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
                ["@type" => "ListItem", "position" => 2, "name" => "PIN Code Search", "item" => $canonical_url]
            ]
        ];
    }
} else {
    $canonical_url = $base_url . "pincode";
    $page_title = "PIN Code Search by 6-Digit Code | Indian Postal Directory 2026 - Online Law";
    $meta_desc = "Search any Indian PIN Code (Postal Index Number) by 6-digit code. Instant post office directory covering 150,000+ locations across 28 states & 8 UTs. Real-time 2026 data.";
    $meta_keywords = "PIN Code Finder, Indian Post Office Search, Postal Index Number India, Pin Code Search by Digit, Post Office Locality Finder, Pincode List India, Speed Post Pincode Lookup";

    // BreadcrumbList Schema for Root Page
    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "PIN Code Finder", "item" => $canonical_url]
        ]
    ];

    $faq_items = [
        [
            "q" => "What is a PIN Code (Postal Index Number) in India?",
            "a" => "A PIN Code is a 6-digit numeric code introduced by the Indian Postal Service on 15 August 1972 to simplify the sorting and delivery of mail across India."
        ],
        [
            "q" => "How many postal zones are there in India?",
            "a" => "India is divided into 9 postal zones: 8 regional zones covering all states and Union Territories (Zones 1-8) and 1 functional zone (Zone 9) dedicated to the Army Postal Service (APS)."
        ],
        [
            "q" => "What is the difference between Head Post Office (HO), Sub Post Office (SO), and Branch Post Office (BO)?",
            "a" => "A Head Post Office (HO) is the main accounting and administrative hub in a postal division. A Sub Post Office (SO) is located in urban and semi-urban areas with complete delivery and financial counters. A Branch Post Office (BO) operates primarily in rural villages providing essential postal and banking services."
        ],
        [
            "q" => "How can I find the correct PIN Code for an Indian address?",
            "a" => "You can enter any 6-digit numeric code in the search tool on this page to view all associated post offices, or use our 'Search by Locality' tool to find PIN codes by entering a city, town, or village name."
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
            --pin-primary: #ff671f;
            --pin-primary-hover: #ea580c;
            --pin-accent: #046a38;
            --pin-dark: #071329;
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
            background-color: #071329 !important;
            background: radial-gradient(135% 100% at 50% 0%, #071329 0%, #0b1e3d 55%, #071329 100%) !important;
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
            background: radial-gradient(circle, rgba(255, 103, 31, 0.28) 0%, rgba(255, 103, 31, 0) 70%);
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
            background: radial-gradient(circle, rgba(4, 106, 56, 0.22) 0%, rgba(4, 106, 56, 0) 70%);
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
            background: #ff671f;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(255, 103, 31, 0.25);
        }

        /* Search input bar */
        .search-input-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        .search-input-field {
            height: 56px;
            padding-left: 20px;
            padding-right: 20px;
            border-radius: 14px;
            border: 2px solid #e2e8f0;
            font-family: var(--font-code);
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.2s ease;
        }
        .search-input-field:focus {
            border-color: #ff671f;
            box-shadow: 0 0 0 4px rgba(255, 103, 31, 0.15);
            outline: none;
        }
        .search-btn-primary {
            height: 56px;
            border-radius: 14px;
            background: #ff671f;
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(255, 103, 31, 0.25);
        }
        .search-btn-primary:hover {
            background: #ea580c;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(255, 103, 31, 0.35);
        }

        /* Result summary hero block */
        .pin-result-hero {
            background: linear-gradient(135deg, #fff7ed 0%, #ffffff 50%, #f0fdf4 100%);
            border: 1.5px solid #fed7aa;
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
            border-color: #fdba74;
        }

        /* PIN Code Badges */
        .pin-code-badge {
            font-family: var(--font-code);
            font-weight: 700;
            letter-spacing: 0.5px;
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
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
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-copy-code:hover {
            background: #fff7ed;
            color: #c2410c;
            border-color: #fdba74;
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
            background: #fff7ed;
            color: #ff671f;
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
            .pin-result-hero {
                padding: 20px 16px;
            }
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
                        <span class="text-white font-weight-bold">PIN Search</span>
                        <?php if (!empty($pincode)): ?>
                            <span class="mx-1">/</span>
                            <span class="text-primary font-weight-bold" style="color: #fdba74 !important;"><?php echo htmlspecialchars($pincode); ?></span>
                        <?php endif; ?>
                    </nav>
                </div>

                <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill mb-3" style="background: rgba(255, 103, 31, 0.15); border: 1px solid rgba(255, 103, 31, 0.35); color: #fed7aa; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">
                    <i class="fas fa-mail-bulk mr-1.5" style="color: #ff8800;"></i> INDIA POST OFFICIAL DIRECTORY 2026
                </div>

                <h1 class="text-white font-weight-bold mb-2" style="font-size: clamp(24px, 3.8vw, 36px); letter-spacing: -0.5px; line-height: 1.25;">
                    Indian PIN Code <span style="background: linear-gradient(135deg, #ffa500 0%, #ff8800 40%, #ffffff 70%, #34d399 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Search by Digit</span>
                </h1>
                <p class="text-light mb-0" style="color: #cbd5e1 !important; font-size: 15px; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Search 6-digit Postal Index Numbers to uncover post offices, talukas, districts, postal zones, and delivery status across 150,000+ Indian postal locations.
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
                <a href="<?php echo $base_url; ?>pincode" class="tool-switcher-pill active">
                    <i class="fas fa-search-location"></i> Search by PIN Code
                </a>
                <a href="<?php echo $base_url; ?>pincode-by-locality" class="tool-switcher-pill">
                    <i class="fas fa-map-marked-alt"></i> Search by Locality / City
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="<?php echo $base_url; ?>pincode" class="mb-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-8 mb-3 mb-md-0">
                        <div class="search-input-group">
                            <input type="text" name="pincode" class="form-control search-input-field" placeholder="Enter 6-digit PIN Code (e.g. 110001, 400001, 560001, 800001)" maxlength="6" minlength="6" value="<?php echo htmlspecialchars($pincode); ?>" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <button type="submit" class="btn btn-block search-btn-primary d-flex align-items-center justify-content-center">
                            <i class="fas fa-search mr-2"></i> Search PIN Code
                        </button>
                    </div>
                </div>
            </form>

            <!-- Quick Suggestions -->
            <div class="d-flex align-items-center flex-wrap justify-content-center gap-2 pt-2 border-top">
                <span class="text-muted small font-weight-bold mr-2"><i class="fas fa-bolt text-warning mr-1"></i> Popular PIN Codes:</span>
                <a href="<?php echo $base_url; ?>pincode?pincode=110001" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">110001 (Connaught Place)</a>
                <a href="<?php echo $base_url; ?>pincode?pincode=400001" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">400001 (Mumbai GPO)</a>
                <a href="<?php echo $base_url; ?>pincode?pincode=560001" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">560001 (Bengaluru GPO)</a>
                <a href="<?php echo $base_url; ?>pincode?pincode=700001" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">700001 (Kolkata GPO)</a>
                <a href="<?php echo $base_url; ?>pincode?pincode=600001" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">600001 (Chennai GPO)</a>
                <a href="<?php echo $base_url; ?>pincode?pincode=800001" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">800001 (Patna GPO)</a>
            </div>
        </div>

        <!-- ======================================================================
             SEARCH RESULTS DISPLAY
             ====================================================================== -->
        <?php if (!empty($pincode)): ?>
            <?php if ($ct > 0): ?>
                
                <!-- Postal Summary Hero Card -->
                <div class="pin-result-hero shadow-sm">
                    <div class="row align-items-center">
                        <div class="col-lg-8 mb-3 mb-lg-0">
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                <span class="badge badge-success px-2.5 py-1 font-weight-bold" style="background: #046a38; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">
                                    <i class="fas fa-check-circle mr-1"></i> India Post Verified
                                </span>
                                <span class="text-muted small">Updated August 2026</span>
                            </div>
                            <h2 class="font-weight-bold text-dark mb-1" style="font-size: clamp(20px, 2.4vw, 26px); line-height: 1.3;">
                                PIN Code <?php echo htmlspecialchars($pincode); ?>
                            </h2>
                            <p class="text-muted font-weight-bold mb-3" style="font-size: 15px;">
                                <i class="fas fa-map-marker-alt text-danger mr-1.5"></i> <?php echo htmlspecialchars($dist); ?> District &bull; <?php echo htmlspecialchars($st); ?>
                            </p>

                            <!-- PIN Code Box -->
                            <div class="d-inline-flex align-items-center flex-wrap gap-2 p-2.5 px-3 rounded-lg" style="background: #ffffff; border: 1.5px solid #bfdbfe; border-radius: 12px;">
                                <div>
                                    <div class="text-muted" style="font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Postal Index Number (PIN)</div>
                                    <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 20px; color: #1d4ed8 !important; letter-spacing: 1px;">
                                        <?php echo htmlspecialchars($pincode); ?>
                                    </div>
                                </div>
                                <button type="button" class="btn-copy-code ml-2 py-1.5 px-3" onclick="copyText('<?php echo htmlspecialchars($pincode); ?>', 'PIN Code copied!')">
                                    <i class="far fa-copy mr-1"></i> Copy PIN
                                </button>
                            </div>

                            <span class="text-muted small ml-md-3 mt-2 mt-md-0 d-inline-block font-weight-semibold">
                                <i class="fas fa-building text-primary mr-1"></i> <?php echo count($offices_unique); ?> Post Office(s) Registered
                            </span>
                        </div>

                        <!-- Postal Spec Badges -->
                        <div class="col-lg-4 text-lg-right">
                            <div class="p-3 bg-white rounded-lg border" style="border-radius: 14px;">
                                <div class="font-weight-bold text-dark mb-2 small text-left text-lg-right">Postal Network Coverage:</div>
                                <div class="d-flex flex-wrap justify-content-start justify-content-lg-end gap-1.5" style="gap: 6px;">
                                    <span class="badge badge-primary px-2.5 py-1.5 font-weight-bold" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 6px; font-size: 11px;"><i class="fas fa-truck-fast mr-1"></i> Speed Post</span>
                                    <span class="badge badge-success px-2.5 py-1.5 font-weight-bold" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; border-radius: 6px; font-size: 11px;"><i class="fas fa-box mr-1"></i> Registered Parcel</span>
                                    <span class="badge badge-info px-2.5 py-1.5 font-weight-bold" style="border-radius: 6px; font-size: 11px;"><i class="fas fa-money-bill-wave mr-1"></i> e-Money Order</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Spec Grid -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 col-6 mb-2.5">
                        <div class="pin-spec-tile">
                            <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">District</small>
                            <strong class="text-dark" style="font-size: 14px;"><?php echo htmlspecialchars($dist ?: 'N/A'); ?></strong>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-6 mb-2.5">
                        <div class="pin-spec-tile">
                            <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">State / UT</small>
                            <strong class="text-dark" style="font-size: 14px;"><?php echo htmlspecialchars($st ?: 'N/A'); ?></strong>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-6 mb-2.5">
                        <div class="pin-spec-tile">
                            <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">Sub-District / Taluk</small>
                            <strong class="text-dark" style="font-size: 14px;"><?php echo htmlspecialchars($sub_dist ?: 'N/A'); ?></strong>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-6 mb-2.5">
                        <div class="pin-spec-tile">
                            <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10.5px;">Postal Zone</small>
                            <strong class="text-primary" style="font-size: 13.5px;" title="<?php echo htmlspecialchars($zone_name); ?>">
                                Zone <?php echo htmlspecialchars($zone_digit); ?> (<?php echo htmlspecialchars($zone_digit <= 2 ? 'Northern' : ($zone_digit <= 4 ? 'Western' : ($zone_digit <= 6 ? 'Southern' : ($zone_digit <= 8 ? 'Eastern' : 'APS')))); ?>)
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Results Table (Desktop) -->
                <div class="desktop-only mb-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden;">
                        <div class="card-header bg-white py-3 px-4 border-bottom">
                            <h4 class="font-weight-bold text-dark mb-0" style="font-size: 16px;">
                                <i class="fas fa-list-ul text-primary mr-2"></i> Post Offices &amp; Localities under PIN Code <?php echo htmlspecialchars($pincode); ?>
                            </h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="pincode_table">
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
                                                    <span class="pin-code-badge mr-1"><?php echo htmlspecialchars($row['pincode']); ?></span>
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
                                    <span class="pin-code-badge mr-1" style="font-size: 12px;"><?php echo htmlspecialchars($row['pincode']); ?></span>
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
                                <a href="<?php echo $base_url; ?>pincode-finder?state=<?php echo urlencode($row['state_name']); ?>&district=<?php echo urlencode($row['district_name']); ?>" class="btn btn-sm btn-light border text-dark font-weight-bold" style="font-size: 11.5px; border-radius: 8px;">
                                    <i class="fas fa-landmark text-primary mr-1"></i> More in <?php echo htmlspecialchars($row['district_name']); ?>
                                </a>
                                <a href="<?php echo $base_url; ?>pincode-by-locality?locality=<?php echo urlencode($row['locality_name']); ?>" class="btn btn-sm btn-light border text-dark font-weight-bold" style="font-size: 11.5px; border-radius: 8px;">
                                    <i class="fas fa-map-pin text-danger mr-1"></i> <?php echo htmlspecialchars($row['locality_name']); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- ==============================================================
                     NEARBY PIN CODES
                     ============================================================== -->
                <?php
                    if (strlen($pincode) >= 3 && is_numeric($pincode)) {
                        $prefix3 = substr($pincode, 0, 3);
                        $prefix4 = (strlen($pincode) >= 4) ? substr($pincode, 0, 4) : $prefix3;
                        $safe_cur_pin = intval($pincode);

                        // Very Near (4-digit match using fast indexed numeric range scan)
                        $p4_int = intval($prefix4);
                        $min4 = $p4_int * 100;
                        $max4 = $min4 + 99;
                        $very_near_res = direct_sql("SELECT DISTINCT pincode, district_name FROM pincode WHERE pincode >= $min4 AND pincode <= $max4 AND pincode != $safe_cur_pin ORDER BY pincode ASC LIMIT 8");

                        // Nearby (3-digit match, excluding 4-digit pool, using fast indexed numeric range scan)
                        $p3_int = intval($prefix3);
                        $min3 = $p3_int * 1000;
                        $max3 = $min3 + 999;
                        $nearby_res = direct_sql("SELECT DISTINCT pincode, district_name FROM pincode WHERE pincode >= $min3 AND pincode <= $max3 AND (pincode < $min4 OR pincode > $max4) AND pincode != $safe_cur_pin ORDER BY pincode ASC LIMIT 8");
                    }
                ?>

                <?php if (!empty($very_near_res['count']) || !empty($nearby_res['count'])): ?>
                    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px; background: #ffffff;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3" style="width: 42px; height: 42px; border-radius: 12px; background: #fff7ed; color: #ff671f; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                <i class="fas fa-map-location-dot"></i>
                            </div>
                            <div>
                                <h3 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Nearest PIN Codes in this Region</h3>
                                <p class="text-muted small mb-0">Discover other PIN codes operating in <?php echo htmlspecialchars($dist); ?> and adjoining sorting districts.</p>
                            </div>
                        </div>

                        <?php if (!empty($very_near_res['count'])): ?>
                            <h6 class="text-uppercase text-muted font-weight-bold mb-2.5 mt-2" style="font-size: 11px; letter-spacing: 0.8px;">
                                <i class="fas fa-map-pin text-primary mr-1"></i> Very Near (<?php echo htmlspecialchars($prefix4); ?>...):
                            </h6>
                            <div class="row mb-3">
                                <?php foreach ($very_near_res['data'] as $nr): ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-2.5">
                                        <a href="<?php echo $base_url; ?>pincode?pincode=<?php echo htmlspecialchars($nr['pincode']); ?>" class="d-block p-2.5 rounded-lg border text-decoration-none bg-light" style="border-radius: 10px; transition: all 0.2s ease;">
                                            <div class="font-weight-bold text-primary" style="font-family: var(--font-code); font-size: 14.5px;"><?php echo htmlspecialchars($nr['pincode']); ?></div>
                                            <div class="text-muted small text-truncate" style="font-size: 12px;"><?php echo htmlspecialchars($nr['district_name']); ?></div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($nearby_res['count'])): ?>
                            <h6 class="text-uppercase text-muted font-weight-bold mb-2.5 mt-2" style="font-size: 11px; letter-spacing: 0.8px;">
                                <i class="fas fa-compass text-info mr-1"></i> Nearby Sorting Area (<?php echo htmlspecialchars($prefix3); ?>...):
                            </h6>
                            <div class="row">
                                <?php foreach ($nearby_res['data'] as $nr): ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-2.5">
                                        <a href="<?php echo $base_url; ?>pincode?pincode=<?php echo htmlspecialchars($nr['pincode']); ?>" class="d-block p-2.5 rounded-lg border text-decoration-none bg-light" style="border-radius: 10px; transition: all 0.2s ease;">
                                            <div class="font-weight-bold text-dark" style="font-family: var(--font-code); font-size: 14.5px;"><?php echo htmlspecialchars($nr['pincode']); ?></div>
                                            <div class="text-muted small text-truncate" style="font-size: 12px;"><?php echo htmlspecialchars($nr['district_name']); ?></div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- ==============================================================
                     FAQ SECTION
                     ============================================================== -->
                <?php if (!empty($faq_items)): ?>
                    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px; background: #ffffff;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3" style="width: 42px; height: 42px; border-radius: 12px; background: #fff7ed; color: #ff671f; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div>
                                <h3 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Frequently Asked Questions</h3>
                                <p class="text-muted small mb-0">Learn more about PIN Code <?php echo htmlspecialchars($pincode); ?> and postal delivery in <?php echo htmlspecialchars($dist); ?>.</p>
                            </div>
                        </div>

                        <div class="accordion" id="pincodeFaqAccordion">
                            <?php foreach ($faq_items as $index => $faq): ?>
                                <div class="pin-faq-card">
                                    <div class="pin-faq-header" data-toggle="collapse" data-target="#faqCollapse<?php echo $index; ?>" aria-expanded="<?php echo ($index === 0) ? 'true' : 'false'; ?>">
                                        <span><i class="fas fa-circle-question text-primary mr-2"></i> <?php echo htmlspecialchars($faq['q']); ?></span>
                                        <i class="fas fa-chevron-down text-muted" style="font-size: 12px;"></i>
                                    </div>
                                    <div id="faqCollapse<?php echo $index; ?>" class="collapse <?php echo ($index === 0) ? 'show' : ''; ?>" data-parent="#pincodeFaqAccordion">
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
                        <h4 class="font-weight-bold mb-0" style="font-size: 17px;">No Record Found for PIN Code: <?php echo htmlspecialchars($pincode); ?></h4>
                    </div>
                    <p class="mb-3 small" style="line-height: 1.6;">
                        Please verify that the 6-digit numeric PIN code was typed correctly. You can also search by entering a locality, village, or town name.
                    </p>
                    <div class="d-flex flex-wrap gap-2" style="gap: 8px;">
                        <a href="<?php echo $base_url; ?>pincode-by-locality" class="btn btn-sm btn-danger font-weight-bold px-3 py-2" style="border-radius: 10px;">
                            <i class="fas fa-map-marker-alt mr-1.5"></i> Search by Locality / Village
                        </a>
                        <a href="<?php echo $base_url; ?>pincode-finder" class="btn btn-sm btn-outline-danger font-weight-bold px-3 py-2" style="border-radius: 10px;">
                            <i class="fas fa-landmark mr-1.5"></i> Cascading PIN Finder
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- ======================================================================
             EDUCATIONAL GUIDE & 6-DIGIT BREAKDOWN
             ====================================================================== -->
        <div class="card border-0 shadow-sm p-4 p-md-5 mb-4" style="border-radius: 24px; background: #ffffff;">
            <h3 class="font-weight-bold text-dark mb-2" style="font-size: 20px;">
                <i class="fas fa-info-circle text-primary mr-2"></i> How Does an Indian 6-Digit PIN Code Work?
            </h3>
            <p class="text-muted small mb-4" style="line-height: 1.65;">
                A PIN (Postal Index Number) is a 6-digit code introduced by Shriram Bhikaji Velankar, additional secretary in the Union Ministry of Communications, on 15 August 1972. Here is the structural breakdown:
            </p>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-primary mb-1" style="font-size: 15px;">
                            <i class="fas fa-1 mr-1"></i> 1st Digit (Postal Zone)
                        </div>
                        <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 14px;">e.g. 1 (North), 4 (West), 5 (South)</div>
                        <div class="text-muted small mt-1">Indicates the broad geographical postal zone out of the 9 total zones in India.</div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-info mb-1" style="font-size: 15px;">
                            <i class="fas fa-2 mr-1"></i> 2nd &amp; 3rd Digits (Sub-Zone &amp; Sorting District)
                        </div>
                        <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 14px;">e.g. 11 (Delhi), 40 (Mumbai)</div>
                        <div class="text-muted small mt-1">Combines with the 1st digit to pinpoint the specific revenue and sorting district.</div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-success mb-1" style="font-size: 15px;">
                            <i class="fas fa-3 mr-1"></i> Last 3 Digits (Delivery Post Office)
                        </div>
                        <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 14px;">e.g. 001 (GPO / Main Delivery PO)</div>
                        <div class="text-muted small mt-1">Identifies the exact physical delivery post office responsible for last-mile mail delivery.</div>
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
        if ($('#pincode_table').length) {
            $('#pincode_table').DataTable({
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
