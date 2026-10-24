<?php
/**
 * Online Law - Bank MICR Code Search Tool
 * Fully updated for 2026 RBI Master Database
 */
require_once('includes/config.php');
require_once('includes/function.php');

// Retrieve search parameter (support GET ?micr=..., ?micrcode=..., ?ifsc=..., and POST)
$micrcode = "";
$search_type = "micr";

if (isset($_GET['micr'])) {
    $micrcode = strtoupper(trim($_GET['micr']));
    $search_type = "micr";
} elseif (isset($_GET['micrcode'])) {
    $micrcode = strtoupper(trim($_GET['micrcode']));
    $search_type = "micr";
} elseif (isset($_GET['ifsc'])) {
    $micrcode = strtoupper(trim($_GET['ifsc']));
    $search_type = "ifsc";
} elseif (isset($_POST['micrcode'])) {
    $micrcode = strtoupper(trim($_POST['micrcode']));
    $search_type = "micr";
}

$schema_graphs = [];
$row_seo = null;
$faq_items = [];
$res_data = [];
$total_found = 0;

if (!empty($micrcode)) {
    $safe_code = mysqli_real_escape_string($con, $micrcode);
    if ($search_type === 'ifsc') {
        $sql = "SELECT * FROM bank_ifsc WHERE ifsc = '$safe_code' ORDER BY branch ASC";
    } else {
        $sql = "SELECT * FROM bank_ifsc WHERE micr = '$safe_code' ORDER BY branch ASC";
    }
    
    $res = direct_sql($sql);
    $total_found = $res['count'];
    $res_data = $res['data'];

    if ($total_found > 0) {
        $row_seo = $res_data[0];
        $bank_name = $row_seo['bank'];
        $branch_name = $row_seo['branch'];
        $city_name = !empty($row_seo['city2']) ? $row_seo['city2'] : $row_seo['city1'];
        $state_name = $row_seo['state'];
        $micr_val = (!empty($row_seo['micr']) && $row_seo['micr'] != 'NA' && $row_seo['micr'] != '-') ? $row_seo['micr'] : 'N/A';
        $ifsc_val = $row_seo['ifsc'];
        $address_val = $row_seo['address'];

        $canonical_url = $base_url . "bank-micr?micr=" . urlencode($micr_val);
        $page_title = "MICR Code $micr_val - $bank_name, $branch_name Branch ($city_name) | Online Law";
        $meta_desc = "Get verified MICR Code: $micr_val for $bank_name $branch_name branch in $city_name, $state_name. IFSC Code: $ifsc_val, Address: $address_val. 100% accurate for Cheque Clearance (CTS).";
        $meta_keywords = "$micr_val, MICR Code $micr_val, $bank_name $branch_name MICR, $bank_name Cheque Clearance Code, CTS Clearing MICR $micr_val, Online Law";

        // BreadcrumbList Schema
        $schema_graphs[] = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
                ["@type" => "ListItem", "position" => 2, "name" => "Bank Directory", "item" => $base_url . "bank/"],
                ["@type" => "ListItem", "position" => 3, "name" => "MICR Code Search", "item" => $base_url . "bank-micr"],
                ["@type" => "ListItem", "position" => 4, "name" => "MICR $micr_val ($bank_name)", "item" => $canonical_url]
            ]
        ];

        // Bank / Financial Entity Schema
        $bank_schema = [
            "@context" => "https://schema.org",
            "@type" => ["BankOrCreditUnion", "FinancialService"],
            "name" => "$bank_name, $branch_name Branch",
            "alternateName" => "MICR $micr_val Branch",
            "url" => $canonical_url,
            "branchOf" => [
                "@type" => "BankOrCreditUnion",
                "name" => $bank_name
            ],
            "identifier" => $micr_val,
            "address" => [
                "@type" => "PostalAddress",
                "streetAddress" => $address_val,
                "addressLocality" => $city_name,
                "addressRegion" => $state_name,
                "addressCountry" => "IN"
            ],
            "paymentAccepted" => ["Cheque", "CTS", "NEFT", "RTGS", "IMPS"],
            "currenciesAccepted" => "INR"
        ];
        $schema_graphs[] = $bank_schema;

        // FAQ Items
        $faq_items = [
            [
                "q" => "What is the MICR Code for $bank_name $branch_name branch?",
                "a" => ($micr_val !== 'N/A')
                    ? "The 9-digit MICR code for $bank_name $branch_name branch in $city_name, $state_name is $micr_val. It is used during cheque clearance in the RBI Cheque Truncation System (CTS)."
                    : "The MICR code for $bank_name $branch_name is currently non-MICR or unallocated in official records."
            ],
            [
                "q" => "What is the difference between IFSC and MICR Code?",
                "a" => "An IFSC code (11 characters) is used for electronic online fund transfers (NEFT, RTGS, IMPS, UPI). An MICR code (9 digits) is printed using magnetic ink at the bottom of bank cheques to speed up physical cheque processing and clearing."
            ],
            [
                "q" => "How is the 9-digit MICR code structured?",
                "a" => "The first 3 digits represent the City Code (aligned with PIN code first 3 digits), the middle 3 digits represent the Bank Code, and the final 3 digits represent the unique Branch Code."
            ],
            [
                "q" => "Where can I find the MICR code on my cheque leaf?",
                "a" => "The MICR code is the second set of numbers printed in magnetic ink at the bottom of your cheque leaf, right beside the 6-digit cheque number."
            ]
        ];

        $faq_schema = [
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => []
        ];
        foreach ($faq_items as $f) {
            $faq_schema["mainEntity"][] = [
                "@type" => "Question",
                "name" => $f["q"],
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => $f["a"]
                ]
            ];
        }
        $schema_graphs[] = $faq_schema;

    } else {
        $canonical_url = $base_url . "bank-micr";
        $page_title = "MICR Code $micrcode - Not Found | Online Law";
        $meta_desc = "No bank branch found for MICR Code $micrcode. Search across 181,900+ RBI bank branches across India.";
        $meta_keywords = "MICR Code $micrcode, Bank MICR Search, Online Law";

        $schema_graphs[] = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
                ["@type" => "ListItem", "position" => 2, "name" => "Bank Directory", "item" => $base_url . "bank/"],
                ["@type" => "ListItem", "position" => 3, "name" => "MICR Code Search", "item" => $canonical_url]
            ]
        ];
    }
} else {
    $canonical_url = $base_url . "bank-micr";
    $page_title = "Bank MICR Code Search 2026 | Find Any Bank Branch MICR in India - Online Law";
    $meta_desc = "Search 9-digit Magnetic Ink Character Recognition (MICR) codes for all bank branches in India. Verified CTS clearing codes, addresses, and IFSC details updated for 2026.";
    $meta_keywords = "MICR Code Search, Find Bank MICR, RBI MICR List 2026, Cheque Clearance Codes, CTS MICR Codes India, Bank Branch Finder, Online Law";

    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "Bank Directory", "item" => $base_url . "bank/"],
            ["@type" => "ListItem", "position" => 3, "name" => "MICR Code Search", "item" => $canonical_url]
        ]
    ];
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

    <!-- Schema.org JSON-LD -->
    <?php foreach ($schema_graphs as $schema): ?>
        <script type="application/ld+json">
        <?php echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>
        </script>
    <?php endforeach; ?>

    <style>
        :root {
            --bank-primary: #047857;
            --bank-primary-hover: #065f46;
            --bank-accent: #10b981;
            --bank-dark: #064e3b;
            --bank-navy: #0f172a;
            --bank-surface: #f8fafc;
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-code: 'JetBrains Mono', monospace;
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

        /* Modern Card Console */
        .bank-console-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.8);
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
            background: #047857;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(4, 120, 87, 0.25);
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
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
            outline: none;
        }
        .search-btn-primary {
            height: 56px;
            border-radius: 14px;
            background: #047857;
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(4, 120, 87, 0.25);
        }
        .search-btn-primary:hover {
            background: #065f46;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(4, 120, 87, 0.35);
        }

        /* Codes Badges */
        .ifsc-code-badge {
            font-family: var(--font-code);
            font-weight: 700;
            letter-spacing: 0.5px;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 4px 10px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none !important;
        }
        .micr-code-badge {
            font-family: var(--font-code);
            font-weight: 700;
            font-size: 13.5px;
            background: #f1f5f9;
            color: #1e293b;
            border: 1.5px solid #cbd5e1;
            padding: 4px 10px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none !important;
        }

        /* Detail hero block */
        .micr-result-hero {
            background: linear-gradient(135deg, #f8fafc 0%, #ecfdf5 100%);
            border: 1.5px solid #a7f3d0;
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 28px;
        }

        .bank-spec-tile {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            height: 100%;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .bank-spec-tile:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
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
            background: #ecfdf5;
            color: #047857;
            border-color: #10b981;
        }

        /* Mobile Card */
        .bank-card-mobile {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 6px -1px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
        }
        .bank-card-mobile:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        /* FAQ Card */
        .bank-faq-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .bank-faq-header {
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
        .bank-faq-header:hover {
            background: #f8fafc;
            color: #047857;
        }
        .bank-faq-body {
            padding: 0 22px 18px 22px;
            color: #475569;
            font-size: 14px;
            line-height: 1.65;
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
                padding: 18px 16px !important;
                border-radius: 18px !important;
            }
            .desktop-only { display: none !important; }
            .mobile-only { display: block !important; }
            .micr-result-hero {
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
                        <a href="<?php echo $base_url; ?>bank/" style="color: #cbd5e1; text-decoration: none;">Bank Directory</a>
                        <span class="mx-1">/</span>
                        <span class="text-white font-weight-bold">MICR Search</span>
                        <?php if (!empty($micrcode)): ?>
                            <span class="mx-1">/</span>
                            <span class="text-success font-weight-bold"><?php echo htmlspecialchars($micrcode); ?></span>
                        <?php endif; ?>
                    </nav>
                </div>

                <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill mb-3" style="background: rgba(6, 78, 59, 0.85); border: 1px solid rgba(255, 255, 255, 0.2); color: #86efac; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">
                    <i class="fas fa-money-check-alt mr-1.5"></i> RBI 9-DIGIT MICR CODE DIRECTORY 2026
                </div>

                <h1 class="text-white font-weight-bold mb-2" style="font-size: clamp(24px, 3.8vw, 36px); letter-spacing: -0.5px; line-height: 1.25;">
                    Bank MICR <span style="background: linear-gradient(135deg, #86efac 0%, #38bdf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Code Search</span>
                </h1>
                <p class="text-light mb-0" style="color: #cbd5e1 !important; font-size: 15px; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Search Magnetic Ink Character Recognition (MICR) codes for cheque clearance and CTS (Cheque Truncation System) operations across all Indian banks.
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
        <div class="bank-console-card p-4 p-md-4 mb-4">

            <!-- Tool Switcher Bar -->
            <div class="tool-switcher-bar">
                <a href="<?php echo $base_url; ?>bank-finder" class="tool-switcher-pill">
                    <i class="fas fa-landmark text-success"></i> Cascading Bank Finder
                </a>
                <a href="<?php echo $base_url; ?>bank-ifsc" class="tool-switcher-pill">
                    <i class="fas fa-barcode"></i> Search by IFSC
                </a>
                <a href="<?php echo $base_url; ?>bank-micr" class="tool-switcher-pill active">
                    <i class="fas fa-money-check-alt"></i> Search by MICR
                </a>
                <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name" class="tool-switcher-pill">
                    <i class="fas fa-building"></i> Search by Branch Name
                </a>
                <a href="<?php echo $base_url; ?>bank/contact-details" class="tool-switcher-pill">
                    <i class="fas fa-headset"></i> Bank Customer Care
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="<?php echo $base_url; ?>bank-micr" class="mb-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-8 mb-3 mb-md-0">
                        <div class="search-input-group">
                            <input type="text" name="micr" class="form-control search-input-field" placeholder="Enter 9-digit MICR Code (e.g. 110002005, 400024002, 700002014)" maxlength="9" minlength="9" value="<?php echo htmlspecialchars($micrcode); ?>" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <button type="submit" class="btn btn-block search-btn-primary d-flex align-items-center justify-content-center">
                            <i class="fas fa-search mr-2"></i> Search MICR
                        </button>
                    </div>
                </div>
            </form>

            <!-- Quick Suggestions -->
            <div class="d-flex align-items-center flex-wrap justify-content-center gap-2 pt-2 border-top">
                <span class="text-muted small font-weight-bold mr-2"><i class="fas fa-bolt text-warning mr-1"></i> Popular MICR Codes:</span>
                <a href="<?php echo $base_url; ?>bank-micr?micr=110002005" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">110002005 (Delhi SBI)</a>
                <a href="<?php echo $base_url; ?>bank-micr?micr=400024002" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">400024002 (Mumbai HDFC)</a>
                <a href="<?php echo $base_url; ?>bank-micr?micr=700002014" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">700002014 (Kolkata)</a>
                <a href="<?php echo $base_url; ?>bank-micr?micr=560002001" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">560002001 (Bengaluru)</a>
            </div>
        </div>

        <!-- ======================================================================
             SEARCH RESULTS DISPLAY
             ====================================================================== -->
        <?php if (!empty($micrcode)): ?>
            <?php if ($total_found > 0): ?>
                
                <!-- Single Highlight Card -->
                <?php $b = $res_data[0]; ?>
                <div class="micr-result-hero shadow-sm">
                    <div class="row align-items-center">
                        <div class="col-lg-8 mb-3 mb-lg-0">
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                <span class="badge badge-success px-2.5 py-1" style="background: #047857; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">
                                    <i class="fas fa-check-circle mr-1"></i> RBI Verified CTS Code
                                </span>
                                <span class="text-muted small">Updated August 2026</span>
                            </div>
                            <h2 class="font-weight-bold text-dark mb-1" style="font-size: clamp(20px, 2.4vw, 26px); line-height: 1.3;">
                                <?php echo htmlspecialchars($b['bank']); ?>
                            </h2>
                            <p class="text-muted font-weight-bold mb-3" style="font-size: 15px;">
                                <i class="fas fa-code-branch text-success mr-1.5"></i> <?php echo htmlspecialchars($b['branch']); ?> Branch &bull; <?php echo htmlspecialchars(!empty($b['city2']) ? $b['city2'] : $b['city1']); ?>, <?php echo htmlspecialchars($b['state']); ?>
                            </p>

                            <!-- MICR Code Box -->
                            <div class="d-inline-flex align-items-center flex-wrap gap-2 p-2.5 px-3 rounded-lg" style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 12px;">
                                <div>
                                    <div class="text-muted" style="font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">MICR Code (9-Digits)</div>
                                    <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 18px; color: #0f172a !important; letter-spacing: 0.5px;">
                                        <?php echo (!empty($b['micr']) && $b['micr'] != 'NA' && $b['micr'] != '-') ? htmlspecialchars($b['micr']) : 'N/A'; ?>
                                    </div>
                                </div>
                                <?php if (!empty($b['micr']) && $b['micr'] != 'NA' && $b['micr'] != '-'): ?>
                                    <button type="button" class="btn-copy-code ml-2 py-1.5 px-3" onclick="copyText('<?php echo htmlspecialchars($b['micr']); ?>', 'MICR Code copied!')">
                                        <i class="far fa-copy mr-1"></i> Copy MICR
                                    </button>
                                <?php endif; ?>
                            </div>

                            <!-- IFSC Code Box -->
                            <div class="d-inline-flex align-items-center flex-wrap gap-2 p-2.5 px-3 rounded-lg ml-md-2 mt-2 mt-md-0" style="background: #ffffff; border: 1.5px solid #a7f3d0; border-radius: 12px;">
                                <div>
                                    <div class="text-muted" style="font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">IFSC Code (Online Transfer)</div>
                                    <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 18px; color: #047857 !important;">
                                        <?php echo htmlspecialchars($b['ifsc']); ?>
                                    </div>
                                </div>
                                <button type="button" class="btn-copy-code ml-2 py-1.5 px-3" onclick="copyText('<?php echo htmlspecialchars($b['ifsc']); ?>', 'IFSC Code copied!')">
                                    <i class="far fa-copy mr-1"></i> Copy IFSC
                                </button>
                            </div>
                        </div>

                        <!-- Cheque Clearing Badges -->
                        <div class="col-lg-4 text-lg-right">
                            <div class="p-3 bg-white rounded-lg border" style="border-radius: 14px;">
                                <div class="font-weight-bold text-dark mb-2 small text-left text-lg-right">Cheque Clearance Capabilities:</div>
                                <div class="d-flex flex-wrap justify-content-start justify-content-lg-end gap-1.5" style="gap: 6px;">
                                    <span class="badge badge-primary px-2.5 py-1.5 font-weight-bold" style="border-radius: 6px; font-size: 11px;"><i class="fas fa-check mr-1"></i> CTS Cheque Truncation</span>
                                    <span class="badge badge-success px-2.5 py-1.5 font-weight-bold" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; border-radius: 6px; font-size: 11px;"><i class="fas fa-bolt mr-1"></i> Speed Clearing</span>
                                    <span class="badge badge-info px-2.5 py-1.5 font-weight-bold" style="border-radius: 6px; font-size: 11px;"><i class="fas fa-globe mr-1"></i> Grid Clearing</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Spec Grid -->
                <div class="row mb-4">
                    <div class="col-lg-6 mb-3 mb-lg-0">
                        <div class="bank-spec-tile">
                            <div class="d-flex align-items-start">
                                <div class="mr-3" style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <div class="text-muted small font-weight-bold text-uppercase">Branch Address</div>
                                    <div class="font-weight-bold text-dark" style="font-size: 14.5px; line-height: 1.5;">
                                        <?php echo htmlspecialchars($b['address']); ?>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        District: <strong><?php echo htmlspecialchars(!empty($b['city2']) ? $b['city2'] : $b['city1']); ?></strong> &bull; State: <strong><?php echo htmlspecialchars($b['state']); ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="bank-spec-tile">
                            <div class="d-flex align-items-start">
                                <div class="mr-3" style="width: 40px; height: 40px; border-radius: 10px; background: #fdf2f8; color: #db2777; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                                    <i class="fas fa-compass"></i>
                                </div>
                                <div class="w-100">
                                    <div class="text-muted small font-weight-bold text-uppercase mb-1">Quick Branch Navigation</div>
                                    <div class="d-flex flex-wrap gap-2" style="gap: 8px;">
                                        <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo urlencode($b['ifsc']); ?>" class="btn btn-sm btn-outline-success font-weight-bold" style="border-radius: 8px; font-size: 12px;">
                                            <i class="fas fa-barcode mr-1"></i> IFSC: <?php echo htmlspecialchars($b['ifsc']); ?>
                                        </a>
                                        <a href="<?php echo $base_url; ?>bank-finder?bank=<?php echo urlencode($b['bank']); ?>&state=<?php echo urlencode($b['state']); ?>&district=<?php echo urlencode(!empty($b['city2']) ? $b['city2'] : $b['city1']); ?>" class="btn btn-sm btn-outline-primary font-weight-bold" style="border-radius: 8px; font-size: 12px;">
                                            <i class="fas fa-landmark mr-1"></i> More in <?php echo htmlspecialchars(!empty($b['city2']) ? $b['city2'] : $b['city1']); ?>
                                        </a>
                                        <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name?branch=<?php echo urlencode($b['bank']); ?>" class="btn btn-sm btn-outline-secondary font-weight-bold" style="border-radius: 8px; font-size: 12px;">
                                            <i class="fas fa-university mr-1"></i> All <?php echo htmlspecialchars($b['bank']); ?> Branches
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Table (Desktop) -->
                <div class="desktop-only mb-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden;">
                        <div class="card-header bg-white py-3 px-4 border-bottom">
                            <h4 class="font-weight-bold text-dark mb-0" style="font-size: 16px;">
                                <i class="fas fa-list-alt text-success mr-2"></i> Verified Record for MICR: <?php echo htmlspecialchars($micrcode); ?>
                            </h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="micrcode_table">
                                <thead style="background: #f8fafc; font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #475569;">
                                    <tr>
                                        <th class="py-3 px-3">#</th>
                                        <th class="py-3 px-3">Bank Name</th>
                                        <th class="py-3 px-3">MICR Code</th>
                                        <th class="py-3 px-3">IFSC Code</th>
                                        <th class="py-3 px-3">Branch</th>
                                        <th class="py-3 px-3">District</th>
                                        <th class="py-3 px-3">State</th>
                                        <th class="py-3 px-3" style="min-width: 260px;">Address</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 13.5px;">
                                    <?php $idx = 1; foreach ($res_data as $row): ?>
                                        <tr>
                                            <td class="py-3 px-3 text-muted"><?php echo $idx++; ?></td>
                                            <td class="py-3 px-3">
                                                <span class="font-weight-bold text-dark"><?php echo htmlspecialchars($row['bank']); ?></span>
                                            </td>
                                            <td class="py-3 px-3">
                                                <?php if (!empty($row['micr']) && $row['micr'] != 'NA' && $row['micr'] != '-'): ?>
                                                    <div class="d-flex align-items-center">
                                                        <a href="<?php echo $base_url; ?>bank-micr?micr=<?php echo htmlspecialchars($row['micr']); ?>" class="micr-code-badge mr-1">
                                                            <?php echo htmlspecialchars($row['micr']); ?>
                                                        </a>
                                                        <button type="button" class="btn-copy-code" onclick="copyText('<?php echo htmlspecialchars($row['micr']); ?>', 'MICR Copied!')" title="Copy MICR">
                                                            <i class="far fa-copy"></i>
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted small">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3 px-3">
                                                <div class="d-flex align-items-center">
                                                    <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo htmlspecialchars($row['ifsc']); ?>" class="ifsc-code-badge mr-1">
                                                        <?php echo htmlspecialchars($row['ifsc']); ?>
                                                    </a>
                                                    <button type="button" class="btn-copy-code" onclick="copyText('<?php echo htmlspecialchars($row['ifsc']); ?>', 'IFSC Copied!')" title="Copy IFSC">
                                                        <i class="far fa-copy"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="py-3 px-3 font-weight-bold text-dark"><?php echo htmlspecialchars($row['branch']); ?></td>
                                            <td class="py-3 px-3"><?php echo htmlspecialchars(!empty($row['city2']) ? $row['city2'] : $row['city1']); ?></td>
                                            <td class="py-3 px-3"><?php echo htmlspecialchars($row['state']); ?></td>
                                            <td class="py-3 px-3 text-secondary" style="line-height: 1.4;"><?php echo htmlspecialchars($row['address']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Mobile Card List View -->
                <div class="mobile-only mb-4">
                    <?php foreach ($res_data as $row): ?>
                        <div class="bank-card-mobile">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0" style="font-size: 16px;">
                                        <?php echo htmlspecialchars($row['bank']); ?>
                                    </h5>
                                    <div class="text-muted small font-weight-bold">
                                        <i class="fas fa-code-branch text-success mr-1"></i> <?php echo htmlspecialchars($row['branch']); ?> Branch
                                    </div>
                                </div>
                            </div>

                            <div class="p-2.5 rounded-lg mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between align-items-center mb-1.5">
                                    <span class="text-muted small font-weight-bold">MICR Code:</span>
                                    <div class="d-flex align-items-center">
                                        <span class="micr-code-badge mr-1"><?php echo (!empty($row['micr']) && $row['micr'] != 'NA' && $row['micr'] != '-') ? htmlspecialchars($row['micr']) : 'N/A'; ?></span>
                                        <?php if (!empty($row['micr']) && $row['micr'] != 'NA' && $row['micr'] != '-'): ?>
                                            <button type="button" class="btn-copy-code" onclick="copyText('<?php echo htmlspecialchars($row['micr']); ?>', 'MICR Copied!')">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small font-weight-bold">IFSC Code:</span>
                                    <div class="d-flex align-items-center">
                                        <span class="ifsc-code-badge mr-1" style="font-size: 12px;"><?php echo htmlspecialchars($row['ifsc']); ?></span>
                                        <button type="button" class="btn-copy-code" onclick="copyText('<?php echo htmlspecialchars($row['ifsc']); ?>', 'IFSC Copied!')">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small font-weight-bold text-uppercase">Branch Address:</div>
                                <div class="text-secondary small mt-0.5" style="line-height: 1.5;">
                                    <?php echo htmlspecialchars($row['address']); ?>, <?php echo htmlspecialchars(!empty($row['city2']) ? $row['city2'] : $row['city1']); ?>, <?php echo htmlspecialchars($row['state']); ?>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 pt-2 border-top" style="gap: 6px;">
                                <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo urlencode($row['ifsc']); ?>" class="btn btn-sm btn-light border text-dark font-weight-bold" style="font-size: 11.5px; border-radius: 8px;">
                                    <i class="fas fa-barcode text-success mr-1"></i> IFSC Details
                                </a>
                                <a href="<?php echo $base_url; ?>bank-finder?bank=<?php echo urlencode($row['bank']); ?>&state=<?php echo urlencode($row['state']); ?>&district=<?php echo urlencode(!empty($row['city2']) ? $row['city2'] : $row['city1']); ?>" class="btn btn-sm btn-light border text-dark font-weight-bold" style="font-size: 11.5px; border-radius: 8px;">
                                    <i class="fas fa-landmark text-primary mr-1"></i> Nearby Branches
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- ==============================================================
                     NEARBY BRANCHES
                     ============================================================== -->
                <?php
                    $bank_ref = $b['bank'];
                    $city_ref = !empty($b['city2']) ? $b['city2'] : $b['city1'];
                    $state_ref = $b['state'];
                    
                    $safe_bank = mysqli_real_escape_string($con, $bank_ref);
                    $safe_city = mysqli_real_escape_string($con, $city_ref);
                    $safe_state = mysqli_real_escape_string($con, $state_ref);
                    $safe_cur_micr = mysqli_real_escape_string($con, $b['micr']);

                    // Cache nearby branches to eliminate repeated queries across 180k branch pages
                    $near_cache_dir = __DIR__ . '/tmp/bank_near';
                    $near_cache_key = md5('micr_' . strtolower($safe_bank . '_' . $safe_state . '_' . $safe_city));
                    $near_cache_file = "$near_cache_dir/$near_cache_key.json";
                    $cached_near = null;

                    if (file_exists($near_cache_file) && (time() - filemtime($near_cache_file) < 604800)) { // 7 days
                        $raw = @file_get_contents($near_cache_file);
                        if ($raw) $cached_near = json_decode($raw, true);
                    }

                    if (is_array($cached_near)) {
                        $near_dist_res = $cached_near['dist'] ?? ['count' => 0, 'data' => []];
                        $near_state_res = $cached_near['state'] ?? ['count' => 0, 'data' => []];
                    } else {
                        // Same bank in same district (including state to leverage composite index idx_bank_geo)
                        $near_dist_res = direct_sql("SELECT DISTINCT branch, micr, ifsc, city2 FROM bank_ifsc WHERE bank = '$safe_bank' AND state = '$safe_state' AND city2 = '$safe_city' AND micr != '$safe_cur_micr' AND micr != '' AND micr != 'NA' AND micr != '-' ORDER BY branch ASC LIMIT 8");

                        // Same bank in same state
                        $near_state_res = direct_sql("SELECT DISTINCT branch, micr, ifsc, city2 FROM bank_ifsc WHERE bank = '$safe_bank' AND state = '$safe_state' AND city2 != '$safe_city' AND micr != '' AND micr != 'NA' AND micr != '-' ORDER BY branch ASC LIMIT 8");

                        if (!is_dir($near_cache_dir)) {
                            @mkdir($near_cache_dir, 0755, true);
                        }
                        @file_put_contents($near_cache_file, json_encode([
                            'dist' => $near_dist_res,
                            'state' => $near_state_res
                        ]));
                    }
                ?>

                <?php if ($near_dist_res['count'] > 0 || $near_state_res['count'] > 0): ?>
                    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px; background: #ffffff;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3" style="width: 42px; height: 42px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                <i class="fas fa-code-branch"></i>
                            </div>
                            <div>
                                <h3 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Other Branches with MICR of <?php echo htmlspecialchars($bank_ref); ?></h3>
                                <p class="text-muted small mb-0">Browse other verified branches in <?php echo htmlspecialchars($city_ref); ?> and <?php echo htmlspecialchars($state_ref); ?>.</p>
                            </div>
                        </div>

                        <?php if ($near_dist_res['count'] > 0): ?>
                            <h6 class="text-uppercase text-muted font-weight-bold mb-2.5 mt-2" style="font-size: 11px; letter-spacing: 0.8px;">
                                <i class="fas fa-map-marker-alt text-danger mr-1"></i> Branches in <?php echo htmlspecialchars($city_ref); ?> District:
                            </h6>
                            <div class="row mb-3">
                                <?php foreach ($near_dist_res['data'] as $nr): ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-2.5">
                                        <a href="<?php echo $base_url; ?>bank-micr?micr=<?php echo htmlspecialchars($nr['micr']); ?>" class="d-block p-2.5 rounded-lg border text-decoration-none bg-light" style="border-radius: 10px; transition: all 0.2s ease;">
                                            <div class="font-weight-bold text-dark text-truncate" style="font-size: 13px;"><?php echo htmlspecialchars($nr['branch']); ?></div>
                                            <div class="text-secondary small font-weight-bold" style="font-family: var(--font-code); font-size: 11px;">MICR: <?php echo htmlspecialchars($nr['micr']); ?></div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($near_state_res['count'] > 0): ?>
                            <h6 class="text-uppercase text-muted font-weight-bold mb-2.5 mt-2" style="font-size: 11px; letter-spacing: 0.8px;">
                                <i class="fas fa-map text-primary mr-1"></i> Other Branches in <?php echo htmlspecialchars($state_ref); ?>:
                            </h6>
                            <div class="row">
                                <?php foreach ($near_state_res['data'] as $nr): ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-2.5">
                                        <a href="<?php echo $base_url; ?>bank-micr?micr=<?php echo htmlspecialchars($nr['micr']); ?>" class="d-block p-2.5 rounded-lg border text-decoration-none bg-light" style="border-radius: 10px; transition: all 0.2s ease;">
                                            <div class="font-weight-bold text-dark text-truncate" style="font-size: 13px;"><?php echo htmlspecialchars($nr['branch']); ?></div>
                                            <div class="text-muted small" style="font-size: 11px;"><?php echo htmlspecialchars($nr['city2']); ?> &bull; <span style="font-family: var(--font-code); font-weight: 600;"><?php echo htmlspecialchars($nr['micr']); ?></span></div>
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
                            <div class="mr-3" style="width: 42px; height: 42px; border-radius: 12px; background: #ecfdf5; color: #047857; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div>
                                <h3 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Frequently Asked Questions</h3>
                                <p class="text-muted small mb-0">Learn more about cheque clearing and MICR codes for <?php echo htmlspecialchars($b['bank']); ?>.</p>
                            </div>
                        </div>

                        <div class="accordion" id="micrFaqAccordion">
                            <?php foreach ($faq_items as $index => $faq): ?>
                                <div class="bank-faq-card">
                                    <div class="bank-faq-header" data-toggle="collapse" data-target="#faqCollapse<?php echo $index; ?>" aria-expanded="<?php echo ($index === 0) ? 'true' : 'false'; ?>">
                                        <span><i class="fas fa-circle-question text-success mr-2"></i> <?php echo htmlspecialchars($faq['q']); ?></span>
                                        <i class="fas fa-chevron-down text-muted" style="font-size: 12px;"></i>
                                    </div>
                                    <div id="faqCollapse<?php echo $index; ?>" class="collapse <?php echo ($index === 0) ? 'show' : ''; ?>" data-parent="#micrFaqAccordion">
                                        <div class="bank-faq-body">
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
                        <h4 class="font-weight-bold mb-0" style="font-size: 17px;">No Record Found for MICR: <?php echo htmlspecialchars($micrcode); ?></h4>
                    </div>
                    <p class="mb-3 small" style="line-height: 1.6;">
                        Please check if the 9-digit numeric MICR code was typed accurately as printed at the bottom of your cheque leaf.
                    </p>
                    <div class="d-flex flex-wrap gap-2" style="gap: 8px;">
                        <a href="<?php echo $base_url; ?>bank-ifsc" class="btn btn-sm btn-danger font-weight-bold px-3 py-2" style="border-radius: 10px;">
                            <i class="fas fa-barcode mr-1.5"></i> Search by IFSC Code
                        </a>
                        <a href="<?php echo $base_url; ?>bank-finder" class="btn btn-sm btn-outline-danger font-weight-bold px-3 py-2" style="border-radius: 10px;">
                            <i class="fas fa-landmark mr-1.5"></i> Find by Bank &amp; Branch
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- ======================================================================
             MICR CODE EXPLANATION & EDUCATIONAL GUIDE
             ====================================================================== -->
        <div class="card border-0 shadow-sm p-4 p-md-5 mb-4" style="border-radius: 24px; background: #ffffff;">
            <h3 class="font-weight-bold text-dark mb-2" style="font-size: 20px;">
                <i class="fas fa-info-circle text-success mr-2"></i> How Does a 9-Digit MICR Code Work?
            </h3>
            <p class="text-muted small mb-4" style="line-height: 1.65;">
                MICR (Magnetic Ink Character Recognition) is a 9-digit numeric technology used by banks to process cheques automatically through high-speed sorting machines in the Cheque Truncation System (CTS).
            </p>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-success mb-1" style="font-size: 15px;">
                            <i class="fas fa-1 mr-1"></i> First 3 Digits (City Code)
                        </div>
                        <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 14px;">e.g. 110 (Delhi), 400 (Mumbai)</div>
                        <div class="text-muted small mt-1">Identifies the city location of the bank branch, aligned with the first 3 digits of the postal PIN code.</div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-primary mb-1" style="font-size: 15px;">
                            <i class="fas fa-2 mr-1"></i> Middle 3 Digits (Bank Code)
                        </div>
                        <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 14px;">e.g. 002 (SBI), 240 (HDFC)</div>
                        <div class="text-muted small mt-1">Uniquely identifies the banking institution according to Reserve Bank of India assignment.</div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-warning mb-1" style="font-size: 15px;">
                            <i class="fas fa-3 mr-1"></i> Last 3 Digits (Branch Code)
                        </div>
                        <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 14px;">e.g. 005 (Main Branch)</div>
                        <div class="text-muted small mt-1">Specific branch location code in that particular city where the bank account is registered.</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Toast Notification -->
<div id="bankToast" class="bank-toast">
    <i class="fas fa-check-circle text-success" style="font-size: 18px;"></i>
    <span id="bankToastMsg">Copied to clipboard!</span>
</div>

<?php require_once('includes/footer.php'); ?>

<script>
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
        var toast = document.getElementById('bankToast');
        var msgEl = document.getElementById('bankToastMsg');
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
