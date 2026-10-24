<?php
/**
 * Online Law - Bank IFSC Code Search Tool
 * Fully updated for 2026 RBI Master Database
 */
require_once('includes/config.php');
require_once('includes/function.php');

// Retrieve search parameter (support GET ?ifsc=..., ?ifsccode=..., and POST)
$ifsccode = "";
if (isset($_GET['ifsc'])) {
    $ifsccode = strtoupper(trim($_GET['ifsc']));
} elseif (isset($_GET['ifsccode'])) {
    $ifsccode = strtoupper(trim($_GET['ifsccode']));
} elseif (isset($_POST['ifsccode'])) {
    $ifsccode = strtoupper(trim($_POST['ifsccode']));
}

$schema_graphs = [];
$row_seo = null;
$faq_items = [];
$res_data = [];
$total_found = 0;

if (!empty($ifsccode)) {
    $safe_ifsc = mysqli_real_escape_string($con, $ifsccode);
    $res = direct_sql("SELECT * FROM bank_ifsc WHERE ifsc = '$safe_ifsc' ORDER BY branch ASC");
    $total_found = $res['count'];
    $res_data = $res['data'];

    if ($total_found > 0) {
        $row_seo = $res_data[0];
        $bank_name = $row_seo['bank'];
        $branch_name = $row_seo['branch'];
        $city_name = !empty($row_seo['city2']) ? $row_seo['city2'] : $row_seo['city1'];
        $state_name = $row_seo['state'];
        $micr_val = (!empty($row_seo['micr']) && $row_seo['micr'] != 'NA' && $row_seo['micr'] != '-') ? $row_seo['micr'] : 'N/A';
        $address_val = $row_seo['address'];

        $canonical_url = $base_url . "bank-ifsc?ifsc=" . urlencode($ifsccode);
        $page_title = "$ifsccode IFSC Code - $bank_name, $branch_name Branch ($city_name, $state_name) | Online Law";
        $meta_desc = "Get verified $bank_name $branch_name branch IFSC Code: $ifsccode (MICR: $micr_val). Address: $address_val, $city_name, $state_name. 100% verified RBI code for NEFT, RTGS & IMPS transfers.";
        $meta_keywords = "$ifsccode, $ifsccode IFSC Code, $bank_name $branch_name IFSC, $bank_name IFSC Code $city_name, $bank_name $state_name IFSC, NEFT RTGS IMPS Code $ifsccode, Online Law";

        // BreadcrumbList Schema
        $schema_graphs[] = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
                ["@type" => "ListItem", "position" => 2, "name" => "Bank Directory", "item" => $base_url . "bank/"],
                ["@type" => "ListItem", "position" => 3, "name" => "IFSC Code Search", "item" => $base_url . "bank-ifsc"],
                ["@type" => "ListItem", "position" => 4, "name" => "$ifsccode ($bank_name)", "item" => $canonical_url]
            ]
        ];

        // FinancialService / BankOrCreditUnion Entity Schema
        $bank_schema = [
            "@context" => "https://schema.org",
            "@type" => ["BankOrCreditUnion", "FinancialService"],
            "name" => "$bank_name, $branch_name Branch",
            "alternateName" => "$ifsccode Branch",
            "url" => $canonical_url,
            "branchOf" => [
                "@type" => "BankOrCreditUnion",
                "name" => $bank_name
            ],
            "identifier" => $ifsccode,
            "address" => [
                "@type" => "PostalAddress",
                "streetAddress" => $address_val,
                "addressLocality" => $city_name,
                "addressRegion" => $state_name,
                "addressCountry" => "IN"
            ],
            "paymentAccepted" => ["NEFT", "RTGS", "IMPS", "UPI"],
            "currenciesAccepted" => "INR"
        ];
        $schema_graphs[] = $bank_schema;

        // FAQ Items
        $faq_items = [
            [
                "q" => "What is the IFSC Code for $bank_name $branch_name branch?",
                "a" => "The official IFSC Code for $bank_name $branch_name branch in $city_name, $state_name is $ifsccode. It is used to identify this specific branch during online fund transfers via NEFT, RTGS, IMPS, and UPI."
            ],
            [
                "q" => "What is the MICR Code for $bank_name $branch_name branch?",
                "a" => ($micr_val !== 'N/A')
                    ? "The MICR code of $bank_name $branch_name branch is $micr_val. MICR (Magnetic Ink Character Recognition) is a 9-digit numeric code printed on cheques used for automated cheque clearance."
                    : "The MICR code for $bank_name $branch_name branch is not allocated or is non-MICR in official RBI records."
            ],
            [
                "q" => "Where is $bank_name $branch_name branch located?",
                "a" => "The complete branch postal address is: $address_val, $city_name, $state_name, India."
            ],
            [
                "q" => "Can I transfer money 24x7 using IFSC $ifsccode?",
                "a" => "Yes. NEFT, RTGS, and IMPS services are available 24x7x365 across all Indian banks. You can transfer funds round the clock using IFSC code $ifsccode."
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
        $canonical_url = $base_url . "bank-ifsc";
        $page_title = "IFSC Code $ifsccode - Not Found | Online Law";
        $meta_desc = "No bank branch record found for IFSC code $ifsccode. Search over 181,900+ RBI bank branches across India.";
        $meta_keywords = "IFSC Code $ifsccode, Bank IFSC Search, Online Law";

        $schema_graphs[] = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
                ["@type" => "ListItem", "position" => 2, "name" => "Bank Directory", "item" => $base_url . "bank/"],
                ["@type" => "ListItem", "position" => 3, "name" => "Bank IFSC Code Search", "item" => $canonical_url]
            ]
        ];
    }
} else {
    $canonical_url = $base_url . "bank-ifsc";
    $page_title = "Bank IFSC Code Search 2026 | Find Any Branch IFSC & MICR in India - Online Law";
    $meta_desc = "Search and verify IFSC & MICR codes for all 181,913+ bank branches across India. Find branch addresses and NEFT, RTGS & IMPS details updated for 2026.";
    $meta_keywords = "IFSC Code Search, Find Bank IFSC, RBI IFSC List 2026, NEFT RTGS Codes, Bank Branch Finder India, MICR Code Search, Net Banking Codes, Online Law";

    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "Bank Directory", "item" => $base_url . "bank/"],
            ["@type" => "ListItem", "position" => 3, "name" => "Bank IFSC Code Search", "item" => $canonical_url]
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
            font-family: var(--font-code);
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
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

        /* IFSC Badge */
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
            font-weight: 600;
            font-size: 12px;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Detail hero block */
        .ifsc-result-hero {
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
            .ifsc-result-hero {
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
                        <span class="text-white font-weight-bold">IFSC Search</span>
                        <?php if (!empty($ifsccode)): ?>
                            <span class="mx-1">/</span>
                            <span class="text-success font-weight-bold"><?php echo htmlspecialchars($ifsccode); ?></span>
                        <?php endif; ?>
                    </nav>
                </div>

                <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill mb-3" style="background: rgba(6, 78, 59, 0.85); border: 1px solid rgba(255, 255, 255, 0.2); color: #86efac; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">
                    <i class="fas fa-barcode mr-1.5"></i> RBI MASTER IFSC CODE SEARCH 2026
                </div>

                <h1 class="text-white font-weight-bold mb-2" style="font-size: clamp(24px, 3.8vw, 36px); letter-spacing: -0.5px; line-height: 1.25;">
                    Bank IFSC <span style="background: linear-gradient(135deg, #86efac 0%, #38bdf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Code Search</span>
                </h1>
                <p class="text-light mb-0" style="color: #cbd5e1 !important; font-size: 15px; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Search and verify 11-digit alphanumeric IFSC codes for 181,913+ branches across India. Instant NEFT, RTGS, IMPS &amp; UPI money transfer routing details.
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
                <a href="<?php echo $base_url; ?>bank-ifsc" class="tool-switcher-pill active">
                    <i class="fas fa-barcode"></i> Search by IFSC
                </a>
                <a href="<?php echo $base_url; ?>bank-micr" class="tool-switcher-pill">
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
            <form method="GET" action="<?php echo $base_url; ?>bank-ifsc" class="mb-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-8 mb-3 mb-md-0">
                        <div class="search-input-group">
                            <i class="fas fa-university search-icon"></i>
                            <input type="text" name="ifsc" class="form-control search-input-field" placeholder="Enter 11-digit IFSC (e.g. SBIN0000691, HDFC0000240, PUNB0024000)" maxlength="11" minlength="11" value="<?php echo htmlspecialchars($ifsccode); ?>" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <button type="submit" class="btn btn-block search-btn-primary d-flex align-items-center justify-content-center">
                            <i class="fas fa-search mr-2"></i> Search IFSC
                        </button>
                    </div>
                </div>
            </form>

            <!-- Quick Suggestions -->
            <div class="d-flex align-items-center flex-wrap justify-content-center gap-2 pt-2 border-top">
                <span class="text-muted small font-weight-bold mr-2"><i class="fas fa-bolt text-warning mr-1"></i> Popular IFSCs:</span>
                <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=SBIN0000691" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">SBIN0000691</a>
                <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=HDFC0000240" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">HDFC0000240</a>
                <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=ICIC0000001" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">ICIC0000001</a>
                <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=PUNB0024000" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">PUNB0024000</a>
                <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=BARB0DELHIX" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">BARB0DELHIX</a>
                <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=CNRB0000101" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">CNRB0000101</a>
            </div>
        </div>

        <!-- ======================================================================
             SEARCH RESULTS DISPLAY
             ====================================================================== -->
        <?php if (!empty($ifsccode)): ?>
            <?php if ($total_found > 0): ?>
                
                <!-- Single Branch Highlight Card -->
                <?php $b = $res_data[0]; ?>
                <div class="ifsc-result-hero shadow-sm">
                    <div class="row align-items-center">
                        <div class="col-lg-8 mb-3 mb-lg-0">
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                <span class="badge badge-success px-2.5 py-1" style="background: #047857; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">
                                    <i class="fas fa-check-circle mr-1"></i> 100% RBI Verified
                                </span>
                                <span class="text-muted small">Updated August 2026</span>
                            </div>
                            <h2 class="font-weight-bold text-dark mb-1" style="font-size: clamp(20px, 2.4vw, 26px); line-height: 1.3;">
                                <?php echo htmlspecialchars($b['bank']); ?>
                            </h2>
                            <p class="text-muted font-weight-bold mb-3" style="font-size: 15px;">
                                <i class="fas fa-code-branch text-success mr-1.5"></i> <?php echo htmlspecialchars($b['branch']); ?> Branch &bull; <?php echo htmlspecialchars(!empty($b['city2']) ? $b['city2'] : $b['city1']); ?>, <?php echo htmlspecialchars($b['state']); ?>
                            </p>

                            <!-- IFSC Code Box -->
                            <div class="d-inline-flex align-items-center flex-wrap gap-2 p-2.5 px-3 rounded-lg" style="background: #ffffff; border: 1.5px solid #a7f3d0; border-radius: 12px;">
                                <div>
                                    <div class="text-muted" style="font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">IFSC Code (11-Digits)</div>
                                    <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 18px; color: #047857 !important; letter-spacing: 0.5px;">
                                        <?php echo htmlspecialchars($b['ifsc']); ?>
                                    </div>
                                </div>
                                <button type="button" class="btn-copy-code ml-2 py-1.5 px-3" onclick="copyText('<?php echo htmlspecialchars($b['ifsc']); ?>', 'IFSC Code copied!')">
                                    <i class="far fa-copy mr-1"></i> Copy IFSC
                                </button>
                            </div>

                            <!-- MICR Code Box -->
                            <?php if (!empty($b['micr']) && $b['micr'] != 'NA' && $b['micr'] != '-'): ?>
                                <div class="d-inline-flex align-items-center flex-wrap gap-2 p-2.5 px-3 rounded-lg ml-md-2 mt-2 mt-md-0" style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px;">
                                    <div>
                                        <div class="text-muted" style="font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">MICR Code (Cheque)</div>
                                        <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 18px; color: #334155 !important;">
                                            <?php echo htmlspecialchars($b['micr']); ?>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-copy-code ml-2 py-1.5 px-3" onclick="copyText('<?php echo htmlspecialchars($b['micr']); ?>', 'MICR Code copied!')">
                                        <i class="far fa-copy mr-1"></i> Copy MICR
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Settlement Badges -->
                        <div class="col-lg-4 text-lg-right">
                            <div class="p-3 bg-white rounded-lg border" style="border-radius: 14px;">
                                <div class="font-weight-bold text-dark mb-2 small text-left text-lg-right">Electronic Settlement Status:</div>
                                <div class="d-flex flex-wrap justify-content-start justify-content-lg-end gap-1.5" style="gap: 6px;">
                                    <span class="badge badge-success px-2.5 py-1.5 font-weight-bold" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; border-radius: 6px; font-size: 11px;"><i class="fas fa-check mr-1"></i> NEFT 24x7</span>
                                    <span class="badge badge-success px-2.5 py-1.5 font-weight-bold" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; border-radius: 6px; font-size: 11px;"><i class="fas fa-check mr-1"></i> RTGS 24x7</span>
                                    <span class="badge badge-success px-2.5 py-1.5 font-weight-bold" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; border-radius: 6px; font-size: 11px;"><i class="fas fa-check mr-1"></i> IMPS Instant</span>
                                    <span class="badge badge-success px-2.5 py-1.5 font-weight-bold" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; border-radius: 6px; font-size: 11px;"><i class="fas fa-check mr-1"></i> UPI Compatible</span>
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
                                        <a href="<?php echo $base_url; ?>bank-finder?bank=<?php echo urlencode($b['bank']); ?>&state=<?php echo urlencode($b['state']); ?>&district=<?php echo urlencode(!empty($b['city2']) ? $b['city2'] : $b['city1']); ?>" class="btn btn-sm btn-outline-success font-weight-bold" style="border-radius: 8px; font-size: 12px;">
                                            <i class="fas fa-landmark mr-1"></i> More in <?php echo htmlspecialchars(!empty($b['city2']) ? $b['city2'] : $b['city1']); ?>
                                        </a>
                                        <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name?branch=<?php echo urlencode($b['bank']); ?>" class="btn btn-sm btn-outline-primary font-weight-bold" style="border-radius: 8px; font-size: 12px;">
                                            <i class="fas fa-search mr-1"></i> All <?php echo htmlspecialchars($b['bank']); ?> Branches
                                        </a>
                                        <?php if (!empty($b['micr']) && $b['micr'] != 'NA' && $b['micr'] != '-'): ?>
                                            <a href="<?php echo $base_url; ?>bank-micr?micr=<?php echo urlencode($b['micr']); ?>" class="btn btn-sm btn-outline-secondary font-weight-bold" style="border-radius: 8px; font-size: 12px;">
                                                <i class="fas fa-money-check-alt mr-1"></i> Check MICR <?php echo htmlspecialchars($b['micr']); ?>
                                            </a>
                                        <?php endif; ?>
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
                                <i class="fas fa-list-alt text-success mr-2"></i> Verified Record for IFSC: <?php echo htmlspecialchars($ifsccode); ?>
                            </h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="ifsccode_table">
                                <thead style="background: #f8fafc; font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #475569;">
                                    <tr>
                                        <th class="py-3 px-3">#</th>
                                        <th class="py-3 px-3">Bank Name</th>
                                        <th class="py-3 px-3">IFSC / MICR</th>
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
                                                <div class="d-flex align-items-center mb-1">
                                                    <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo htmlspecialchars($row['ifsc']); ?>" class="ifsc-code-badge mr-1">
                                                        <?php echo htmlspecialchars($row['ifsc']); ?>
                                                    </a>
                                                    <button type="button" class="btn-copy-code" onclick="copyText('<?php echo htmlspecialchars($row['ifsc']); ?>', 'IFSC Copied!')" title="Copy IFSC">
                                                        <i class="far fa-copy"></i>
                                                    </button>
                                                </div>
                                                <?php if (!empty($row['micr']) && $row['micr'] != 'NA' && $row['micr'] != '-'): ?>
                                                    <div class="d-flex align-items-center">
                                                        <span class="micr-code-badge mr-1">MICR: <?php echo htmlspecialchars($row['micr']); ?></span>
                                                        <button type="button" class="btn-copy-code" onclick="copyText('<?php echo htmlspecialchars($row['micr']); ?>', 'MICR Copied!')" title="Copy MICR">
                                                            <i class="far fa-copy"></i>
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted small">MICR: N/A</span>
                                                <?php endif; ?>
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
                                    <span class="text-muted small font-weight-bold">IFSC Code:</span>
                                    <div class="d-flex align-items-center">
                                        <span class="ifsc-code-badge mr-1" style="font-size: 12px;"><?php echo htmlspecialchars($row['ifsc']); ?></span>
                                        <button type="button" class="btn-copy-code" onclick="copyText('<?php echo htmlspecialchars($row['ifsc']); ?>', 'IFSC Copied!')">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
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
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small font-weight-bold text-uppercase">Branch Address:</div>
                                <div class="text-secondary small mt-0.5" style="line-height: 1.5;">
                                    <?php echo htmlspecialchars($row['address']); ?>, <?php echo htmlspecialchars(!empty($row['city2']) ? $row['city2'] : $row['city1']); ?>, <?php echo htmlspecialchars($row['state']); ?>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 pt-2 border-top" style="gap: 6px;">
                                <a href="<?php echo $base_url; ?>bank-finder?bank=<?php echo urlencode($row['bank']); ?>&state=<?php echo urlencode($row['state']); ?>&district=<?php echo urlencode(!empty($row['city2']) ? $row['city2'] : $row['city1']); ?>" class="btn btn-sm btn-light border text-dark font-weight-bold" style="font-size: 11.5px; border-radius: 8px;">
                                    <i class="fas fa-landmark text-success mr-1"></i> Nearby Branches
                                </a>
                                <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name?branch=<?php echo urlencode($row['bank']); ?>" class="btn btn-sm btn-light border text-dark font-weight-bold" style="font-size: 11.5px; border-radius: 8px;">
                                    <i class="fas fa-university text-primary mr-1"></i> All <?php echo htmlspecialchars($row['bank']); ?>
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
                    $safe_cur_ifsc = mysqli_real_escape_string($con, $b['ifsc']);

                    // Cache nearby branches to eliminate repeated queries across 180k branch pages
                    $near_cache_dir = __DIR__ . '/tmp/bank_near';
                    $near_cache_key = md5(strtolower($safe_bank . '_' . $safe_state . '_' . $safe_city));
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
                        $near_dist_res = direct_sql("SELECT DISTINCT branch, ifsc, city2 FROM bank_ifsc WHERE bank = '$safe_bank' AND state = '$safe_state' AND city2 = '$safe_city' AND ifsc != '$safe_cur_ifsc' ORDER BY branch ASC LIMIT 8");

                        // Same bank in same state
                        $near_state_res = direct_sql("SELECT DISTINCT branch, ifsc, city2 FROM bank_ifsc WHERE bank = '$safe_bank' AND state = '$safe_state' AND city2 != '$safe_city' ORDER BY branch ASC LIMIT 8");

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
                                <h3 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Other Nearby Branches of <?php echo htmlspecialchars($bank_ref); ?></h3>
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
                                        <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo htmlspecialchars($nr['ifsc']); ?>" class="d-block p-2.5 rounded-lg border text-decoration-none bg-light" style="border-radius: 10px; transition: all 0.2s ease;">
                                            <div class="font-weight-bold text-dark text-truncate" style="font-size: 13px;"><?php echo htmlspecialchars($nr['branch']); ?></div>
                                            <div class="text-success small font-weight-bold" style="font-family: var(--font-code); font-size: 11px;"><?php echo htmlspecialchars($nr['ifsc']); ?></div>
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
                                        <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo htmlspecialchars($nr['ifsc']); ?>" class="d-block p-2.5 rounded-lg border text-decoration-none bg-light" style="border-radius: 10px; transition: all 0.2s ease;">
                                            <div class="font-weight-bold text-dark text-truncate" style="font-size: 13px;"><?php echo htmlspecialchars($nr['branch']); ?></div>
                                            <div class="text-muted small" style="font-size: 11px;"><?php echo htmlspecialchars($nr['city2']); ?> &bull; <span class="text-success" style="font-family: var(--font-code);"><?php echo htmlspecialchars($nr['ifsc']); ?></span></div>
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
                                <p class="text-muted small mb-0">Answers regarding <?php echo htmlspecialchars($b['bank']); ?> <?php echo htmlspecialchars($b['branch']); ?> branch IFSC, MICR &amp; money transfers.</p>
                            </div>
                        </div>

                        <div class="accordion" id="ifscFaqAccordion">
                            <?php foreach ($faq_items as $index => $faq): ?>
                                <div class="bank-faq-card">
                                    <div class="bank-faq-header" data-toggle="collapse" data-target="#faqCollapse<?php echo $index; ?>" aria-expanded="<?php echo ($index === 0) ? 'true' : 'false'; ?>">
                                        <span><i class="fas fa-circle-question text-success mr-2"></i> <?php echo htmlspecialchars($faq['q']); ?></span>
                                        <i class="fas fa-chevron-down text-muted" style="font-size: 12px;"></i>
                                    </div>
                                    <div id="faqCollapse<?php echo $index; ?>" class="collapse <?php echo ($index === 0) ? 'show' : ''; ?>" data-parent="#ifscFaqAccordion">
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
                        <h4 class="font-weight-bold mb-0" style="font-size: 17px;">No Record Found for IFSC: <?php echo htmlspecialchars($ifsccode); ?></h4>
                    </div>
                    <p class="mb-3 small" style="line-height: 1.6;">
                        Please ensure the 11-digit IFSC code is spelled correctly. Note that the 5th character in any Indian IFSC code is always the digit zero (<code>0</code>).
                    </p>
                    <div class="d-flex flex-wrap gap-2" style="gap: 8px;">
                        <a href="<?php echo $base_url; ?>bank-finder" class="btn btn-sm btn-danger font-weight-bold px-3 py-2" style="border-radius: 10px;">
                            <i class="fas fa-landmark mr-1.5"></i> Find by Bank &amp; Branch
                        </a>
                        <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name" class="btn btn-sm btn-outline-danger font-weight-bold px-3 py-2" style="border-radius: 10px;">
                            <i class="fas fa-search mr-1.5"></i> Search by Branch Name
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- ======================================================================
             IFSC CODE EXPLANATION & EDUCATIONAL GUIDE
             ====================================================================== -->
        <div class="card border-0 shadow-sm p-4 p-md-5 mb-4" style="border-radius: 24px; background: #ffffff;">
            <h3 class="font-weight-bold text-dark mb-2" style="font-size: 20px;">
                <i class="fas fa-info-circle text-success mr-2"></i> Understanding Indian Financial System Code (IFSC)
            </h3>
            <p class="text-muted small mb-4" style="line-height: 1.65;">
                An IFSC (Indian Financial System Code) is an 11-character alphanumeric code assigned by the Reserve Bank of India (RBI) to uniquely identify every bank branch participating in NEFT, RTGS, and IMPS electronic money transfers.
            </p>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-success mb-1" style="font-size: 15px;">
                            <i class="fas fa-1 mr-1"></i> First 4 Characters
                        </div>
                        <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 14px;">Bank Code (e.g. SBIN)</div>
                        <div class="text-muted small mt-1">Represents the bank identity name. (SBIN for State Bank of India, HDFC for HDFC Bank).</div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-warning mb-1" style="font-size: 15px;">
                            <i class="fas fa-2 mr-1"></i> 5th Character
                        </div>
                        <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 14px;">Always Zero (0)</div>
                        <div class="text-muted small mt-1">Reserved by the RBI for future use and standard control across all Indian banks.</div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-primary mb-1" style="font-size: 15px;">
                            <i class="fas fa-3 mr-1"></i> Last 6 Characters
                        </div>
                        <div class="text-dark font-weight-bold" style="font-family: var(--font-code); font-size: 14px;">Branch Code (e.g. 000691)</div>
                        <div class="text-muted small mt-1">Uniquely identifies the specific physical bank branch location within India.</div>
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
