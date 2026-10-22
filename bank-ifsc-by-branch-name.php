<?php
/**
 * Online Law - Find Bank IFSC & MICR by Branch Name or City
 * Fully updated for 2026 RBI Master Database
 */
require_once('includes/config.php');
require_once('includes/function.php');

// Retrieve search query (support GET ?branch=..., ?q=..., and POST)
$branch_search = "";
if (isset($_GET['branch'])) {
    $branch_search = trim($_GET['branch']);
} elseif (isset($_GET['q'])) {
    $branch_search = trim($_GET['q']);
} elseif (isset($_POST['branch'])) {
    $branch_search = trim($_POST['branch']);
}

$schema_graphs = [];
$res_data = [];
$total_found = 0;
$faq_items = [];

if (!empty($branch_search)) {
    $safe_term = mysqli_real_escape_string($con, $branch_search);
    $sql = "SELECT * FROM bank_ifsc WHERE bank LIKE '%$safe_term%' OR branch LIKE '%$safe_term%' OR city2 LIKE '%$safe_term%' OR city1 LIKE '%$safe_term%' OR address LIKE '%$safe_term%' ORDER BY bank ASC, branch ASC LIMIT 250";
    
    $res = direct_sql($sql);
    $total_found = $res['count'];
    $res_data = $res['data'];

    $canonical_url = $base_url . "bank-ifsc-by-branch-name?branch=" . urlencode($branch_search);
    $page_title = "Bank IFSC & MICR Codes for '$branch_search' Branches (2026) | Online Law";
    $meta_desc = "Found $total_found bank branches matching '$branch_search'. Search verified RBI IFSC & MICR codes, branch addresses, and RTGS/NEFT settlement details.";
    $meta_keywords = "$branch_search Bank IFSC, $branch_search Branch Code, $branch_search MICR Code, Find Bank IFSC by City $branch_search, Online Law";

    // BreadcrumbList Schema
    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "Bank Directory", "item" => $base_url . "bank/"],
            ["@type" => "ListItem", "position" => 3, "name" => "Branch Name Search", "item" => $base_url . "bank-ifsc-by-branch-name"],
            ["@type" => "ListItem", "position" => 4, "name" => "$branch_search Branches", "item" => $canonical_url]
        ]
    ];

    // FAQ Items
    $faq_items = [
        [
            "q" => "How do I find the correct IFSC Code for a branch in $branch_search?",
            "a" => "Look through the search results list for your specific bank name and branch location in $branch_search. Click the 1-click 'Copy IFSC' button to grab the 11-digit code for NEFT, RTGS, IMPS, or UPI money transfers."
        ],
        [
            "q" => "What if multiple branches appear for '$branch_search'?",
            "a" => "Use the search filter inside the table or verify the exact postal address shown in the table to pick the exact physical branch you need."
        ],
        [
            "q" => "Are these IFSC and MICR codes updated for 2026?",
            "a" => "Yes. All branch records on Online Law are synchronized with the official Reserve Bank of India (RBI) master directory for 2026."
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
    $canonical_url = $base_url . "bank-ifsc-by-branch-name";
    $page_title = "Search Bank IFSC & MICR by Branch or City Name (2026) | Online Law";
    $meta_desc = "Quickly search for any Bank branch IFSC & MICR codes in India by branch name, locality, town, or city. Comprehensive 181,913+ RBI directory updated for 2026.";
    $meta_keywords = "IFSC Search by City, Bank Branch Name Search India, Find IFSC by Town, State Bank Branch Location, District Wise IFSC Codes, Online Law";

    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "Bank Directory", "item" => $base_url . "bank/"],
            ["@type" => "ListItem", "position" => 3, "name" => "Branch Name Search", "item" => $canonical_url]
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
            font-size: 15.5px;
            font-weight: 600;
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

        /* Code Badges */
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
            font-size: 11.5px;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 2px 7px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
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
                        <span class="text-white font-weight-bold">Branch Name Search</span>
                        <?php if (!empty($branch_search)): ?>
                            <span class="mx-1">/</span>
                            <span class="text-success font-weight-bold"><?php echo htmlspecialchars($branch_search); ?></span>
                        <?php endif; ?>
                    </nav>
                </div>

                <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill mb-3" style="background: rgba(6, 78, 59, 0.85); border: 1px solid rgba(255, 255, 255, 0.2); color: #86efac; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">
                    <i class="fas fa-building mr-1.5"></i> BRANCH &amp; CITY IFSC LOOKUP 2026
                </div>

                <h1 class="text-white font-weight-bold mb-2" style="font-size: clamp(24px, 3.8vw, 36px); letter-spacing: -0.5px; line-height: 1.25;">
                    Find IFSC by <span style="background: linear-gradient(135deg, #86efac 0%, #38bdf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Branch Name or City</span>
                </h1>
                <p class="text-light mb-0" style="color: #cbd5e1 !important; font-size: 15px; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Type any branch name, city, or locality in India to instantly locate verified 11-digit IFSC, MICR, and branch addresses.
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
                <a href="<?php echo $base_url; ?>bank-micr" class="tool-switcher-pill">
                    <i class="fas fa-money-check-alt"></i> Search by MICR
                </a>
                <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name" class="tool-switcher-pill active">
                    <i class="fas fa-building"></i> Search by Branch Name
                </a>
                <a href="<?php echo $base_url; ?>bank/contact-details" class="tool-switcher-pill">
                    <i class="fas fa-headset"></i> Bank Customer Care
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="<?php echo $base_url; ?>bank-ifsc-by-branch-name" class="mb-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-8 mb-3 mb-md-0">
                        <div class="search-input-group">
                            <i class="fas fa-map-pin search-icon"></i>
                            <input type="text" name="branch" class="form-control search-input-field" placeholder="Enter Branch Name or City (e.g. Connaught Place, Andheri East, MG Road, Patna)" minlength="3" maxlength="60" value="<?php echo htmlspecialchars($branch_search); ?>" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <button type="submit" class="btn btn-block search-btn-primary d-flex align-items-center justify-content-center">
                            <i class="fas fa-search mr-2"></i> Search Branch
                        </button>
                    </div>
                </div>
            </form>

            <!-- Quick Suggestions -->
            <div class="d-flex align-items-center flex-wrap justify-content-center gap-2 pt-2 border-top">
                <span class="text-muted small font-weight-bold mr-2"><i class="fas fa-bolt text-warning mr-1"></i> Popular Searches:</span>
                <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name?branch=Connaught+Place" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-size: 11.5px;">Connaught Place</a>
                <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name?branch=Andheri+East" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-size: 11.5px;">Andheri East</a>
                <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name?branch=MG+Road" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-size: 11.5px;">MG Road</a>
                <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name?branch=Bandra" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-size: 11.5px;">Bandra</a>
                <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name?branch=Sector+18" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-size: 11.5px;">Sector 18 Noida</a>
                <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name?branch=Salt+Lake" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-size: 11.5px;">Salt Lake</a>
            </div>
        </div>

        <!-- ======================================================================
             SEARCH RESULTS DISPLAY
             ====================================================================== -->
        <?php if (!empty($branch_search)): ?>
            <?php if ($total_found > 0): ?>
                
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                    <h3 class="font-weight-bold text-dark mb-0" style="font-size: 19px;">
                        Found <span class="text-success"><?php echo number_format($total_found); ?></span> Branches matching "<span class="text-dark"><?php echo htmlspecialchars($branch_search); ?></span>"
                    </h3>
                    <div class="text-muted small">
                        Showing verified RBI records
                    </div>
                </div>

                <!-- Results Table (Desktop) -->
                <div class="desktop-only mb-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden;">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="branch_table">
                                <thead style="background: #f8fafc; font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #475569;">
                                    <tr>
                                        <th class="py-3 px-3">#</th>
                                        <th class="py-3 px-3">Bank Name</th>
                                        <th class="py-3 px-3">Branch Name</th>
                                        <th class="py-3 px-3">IFSC Code</th>
                                        <th class="py-3 px-3">MICR Code</th>
                                        <th class="py-3 px-3">District</th>
                                        <th class="py-3 px-3">State</th>
                                        <th class="py-3 px-3" style="min-width: 240px;">Address</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 13.5px;">
                                    <?php $idx = 1; foreach ($res_data as $row): ?>
                                        <tr>
                                            <td class="py-3 px-3 text-muted"><?php echo $idx++; ?></td>
                                            <td class="py-3 px-3">
                                                <span class="font-weight-bold text-dark"><?php echo htmlspecialchars($row['bank']); ?></span>
                                            </td>
                                            <td class="py-3 px-3 font-weight-bold text-dark">
                                                <?php echo htmlspecialchars($row['branch']); ?>
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
                                <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo urlencode($row['ifsc']); ?>" class="btn btn-sm btn-light border text-dark font-weight-bold" style="font-size: 11.5px; border-radius: 8px;">
                                    <i class="fas fa-barcode text-success mr-1"></i> IFSC Details
                                </a>
                                <a href="<?php echo $base_url; ?>bank-finder?bank=<?php echo urlencode($row['bank']); ?>&state=<?php echo urlencode($row['state']); ?>&district=<?php echo urlencode(!empty($row['city2']) ? $row['city2'] : $row['city1']); ?>" class="btn btn-sm btn-light border text-dark font-weight-bold" style="font-size: 11.5px; border-radius: 8px;">
                                    <i class="fas fa-landmark text-primary mr-1"></i> All in <?php echo htmlspecialchars(!empty($row['city2']) ? $row['city2'] : $row['city1']); ?>
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
                            <div class="mr-3" style="width: 42px; height: 42px; border-radius: 12px; background: #ecfdf5; color: #047857; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div>
                                <h3 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Frequently Asked Questions</h3>
                                <p class="text-muted small mb-0">Learn how to search bank branches and verify IFSC codes.</p>
                            </div>
                        </div>

                        <div class="accordion" id="branchFaqAccordion">
                            <?php foreach ($faq_items as $index => $faq): ?>
                                <div class="bank-faq-card">
                                    <div class="bank-faq-header" data-toggle="collapse" data-target="#faqCollapse<?php echo $index; ?>" aria-expanded="<?php echo ($index === 0) ? 'true' : 'false'; ?>">
                                        <span><i class="fas fa-circle-question text-success mr-2"></i> <?php echo htmlspecialchars($faq['q']); ?></span>
                                        <i class="fas fa-chevron-down text-muted" style="font-size: 12px;"></i>
                                    </div>
                                    <div id="faqCollapse<?php echo $index; ?>" class="collapse <?php echo ($index === 0) ? 'show' : ''; ?>" data-parent="#branchFaqAccordion">
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
                        <h4 class="font-weight-bold mb-0" style="font-size: 17px;">No Branches Found for "<?php echo htmlspecialchars($branch_search); ?>"</h4>
                    </div>
                    <p class="mb-3 small" style="line-height: 1.6;">
                        Try searching with a shorter keyword or check spelling (e.g., try "Delhi" instead of "New Delhi Central"). You can also use our cascading bank finder.
                    </p>
                    <div class="d-flex flex-wrap gap-2" style="gap: 8px;">
                        <a href="<?php echo $base_url; ?>bank-finder" class="btn btn-sm btn-danger font-weight-bold px-3 py-2" style="border-radius: 10px;">
                            <i class="fas fa-landmark mr-1.5"></i> Launch Cascading Bank Finder
                        </a>
                        <a href="<?php echo $base_url; ?>bank-ifsc" class="btn btn-sm btn-outline-danger font-weight-bold px-3 py-2" style="border-radius: 10px;">
                            <i class="fas fa-barcode mr-1.5"></i> Search by IFSC Code
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
                <i class="fas fa-lightbulb text-warning mr-2"></i> How to Find Bank Branches Quickly
            </h3>
            <p class="text-muted small mb-4" style="line-height: 1.65;">
                Follow these tips to instantly track down any bank branch's IFSC code and MICR details across India:
            </p>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-success mb-1" style="font-size: 15px;">
                            <i class="fas fa-city mr-1"></i> Search by City / District
                        </div>
                        <div class="text-dark font-weight-bold" style="font-size: 14px;">e.g. Mumbai, Noida, Patna</div>
                        <div class="text-muted small mt-1">Typing a city name lists all major branches of various banks operating in that district.</div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-primary mb-1" style="font-size: 15px;">
                            <i class="fas fa-university mr-1"></i> Search by Bank Name
                        </div>
                        <div class="text-dark font-weight-bold" style="font-size: 14px;">e.g. Axis Bank, Canara Bank</div>
                        <div class="text-muted small mt-1">Typing a bank name displays all branches across different districts for easy scanning.</div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="p-3 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-warning mb-1" style="font-size: 15px;">
                            <i class="fas fa-road mr-1"></i> Search by Locality / Road
                        </div>
                        <div class="text-dark font-weight-bold" style="font-size: 14px;">e.g. MG Road, Mall Road</div>
                        <div class="text-muted small mt-1">Search specific prominent streets or shopping complexes to locate nearby branch counters.</div>
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
    $(document).ready(function() {
        if ($('#branch_table').length) {
            $('#branch_table').DataTable({
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 15,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Filter table branches..."
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
