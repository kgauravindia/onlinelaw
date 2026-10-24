<?php
/**
 * Online Law - GST HSN and SAC Code Finder
 * Fully updated for 2026 CBIC & GST Council Tariff Schedules
 */
require_once('includes/config.php');
require_once('includes/function.php');

$q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';
$type_filter = isset($_REQUEST['type']) ? strtoupper(trim($_REQUEST['type'])) : '';

$schema_graphs = [];
$faq_items = [];

if (!empty($q)) {
    $meta_desc = "Quickly find GST HSN/SAC code '$q' details. Get official descriptions, classification chapters, and GST tariff rates for '$q'. Updated for 2026.";
    $page_title = "HSN/SAC Code $q - GST Rates & Classification Details (2026) | Online Law";
    $canonical_url = $base_url . "gst-hsn-sac?q=" . urlencode($q);

    // BreadcrumbList Schema
    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "GST Tools", "item" => $base_url . "gst-hsn-sac"],
            ["@type" => "ListItem", "position" => 3, "name" => "HSN/SAC '$q'", "item" => $canonical_url]
        ]
    ];

    // FAQ items
    $faq_items = [
        [
            "q" => "What is the meaning of HSN and SAC code in GST?",
            "a" => "HSN (Harmonized System of Nomenclature) is an internationally standardized system of numbers used to classify goods. SAC (Services Accounting Code) is a code issued by CBIC to classify services under GST."
        ],
        [
            "q" => "How many digits of HSN code are mandatory on GST tax invoices in 2026?",
            "a" => "For businesses with an aggregate annual turnover exceeding ₹5 Crores, a 6-digit HSN/SAC code is mandatory on all B2B and B2C invoices. For businesses with turnover up to ₹5 Crores, a 4-digit HSN is required on B2B invoices."
        ],
        [
            "q" => "What is the difference between HSN Code and SAC Code?",
            "a" => "HSN codes apply exclusively to physical products and goods (Chapters 1 to 98). SAC codes apply exclusively to services and start with the prefix '99' (e.g. 9983 for professional IT and legal services)."
        ]
    ];
} else {
    $meta_desc = "Professional GST HSN and SAC code lookup for goods and services. Search for tax rates, HSN chapters, and service accounting codes. Updated 2026.";
    $page_title = "GST HSN & SAC Code Finder | Current GST Rates & Classification 2026 - Online Law";
    $canonical_url = $base_url . "gst-hsn-sac";

    // BreadcrumbList Schema
    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "GST HSN & SAC Finder", "item" => $canonical_url]
        ]
    ];

    $faq_items = [
        [
            "q" => "What is an HSN Code in GST invoicing?",
            "a" => "An HSN (Harmonized System of Nomenclature) is an 8-digit multipurpose international product nomenclature developed by the World Customs Organization (WCO) to classify goods systematically."
        ],
        [
            "q" => "What is a SAC Code in GST?",
            "a" => "A SAC (Services Accounting Code) is a numeric code designed by the Central Board of Indirect Taxes and Customs (CBIC) in India to categorize and assess tax rates on all types of services."
        ],
        [
            "q" => "How can I find the correct HSN code for my products?",
            "a" => "Type the name of your product or material (e.g., 'Cotton', 'Rice', 'Footwear') or the starting digits of the code in the search bar above to look up its full tariff heading."
        ],
        [
            "q" => "Why is mentioning the correct HSN/SAC code critical on tax invoices?",
            "a" => "Entering an incorrect HSN/SAC code can cause mismatch errors in GSTR-1, delay input tax credit (ITC) claims for buyers, and attract penalties under Section 125 of the CGST Act."
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
    <meta name="keywords" content="GST HSN Code Finder, SAC Code Search, GST Rate Finder 2026, HSN Chapters List, Service Accounting Codes, Goods and Services Tax Search, <?php echo htmlspecialchars($q); ?>">
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
            --gst-primary: #0284c7;
            --gst-primary-hover: #0369a1;
            --gst-accent: #38bdf8;
            --gst-dark: #0f172a;
            --gst-surface: #f8fafc;
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-code: 'JetBrains Mono', monospace;
        }

        body {
            font-family: var(--font-main) !important;
            background-color: var(--gst-surface);
            color: #1e293b;
        }

        /* Hero Banner */
        .hero-gst-banner {
            background-color: #0b1120 !important;
            background: radial-gradient(135% 100% at 50% 0%, #0b1120 0%, #0c4a6e 55%, #0f172a 100%) !important;
            color: #ffffff !important;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 56px 0 64px 0;
        }
        .hero-gst-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.7;
            pointer-events: none;
        }
        .gst-glow-orb-1 {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, transparent 70%);
            top: -120px;
            left: -100px;
            pointer-events: none;
            filter: blur(50px);
        }
        .gst-glow-orb-2 {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.2) 0%, transparent 70%);
            bottom: -140px;
            right: -80px;
            pointer-events: none;
            filter: blur(60px);
        }

        /* Modern Card Console */
        .gst-console-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.8);
            position: relative;
            margin-top: -36px;
            z-index: 20;
            transition: box-shadow 0.3s ease;
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
            font-size: 15.5px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .search-input-field:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15);
            outline: none;
        }
        .search-btn-primary {
            height: 56px;
            border-radius: 14px;
            background: #0284c7;
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }
        .search-btn-primary:hover {
            background: #0369a1;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.35);
        }

        /* Type Badges */
        .badge-hsn {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 11.5px;
            letter-spacing: 0.5px;
        }
        .badge-sac {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 11.5px;
            letter-spacing: 0.5px;
        }

        /* Code Badges */
        .hsn-code-badge {
            font-family: var(--font-code);
            font-weight: 800;
            font-size: 15px;
            letter-spacing: 0.5px;
            color: #0f172a;
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
            color: #0284c7;
            border-color: #38bdf8;
        }

        /* Mobile Card */
        .hsn-card-mobile {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 6px -1px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
        }
        .hsn-card-mobile:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        /* FAQ Card */
        .gst-faq-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .gst-faq-header {
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
        .gst-faq-header:hover {
            background: #f8fafc;
            color: #0284c7;
        }
        .gst-faq-body {
            padding: 0 22px 18px 22px;
            color: #475569;
            font-size: 14px;
            line-height: 1.65;
        }

        /* Toast */
        .gst-toast {
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
        .gst-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 767px) {
            .gst-console-card {
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
<section class="hero-gst-banner">
    <div class="gst-glow-orb-1"></div>
    <div class="gst-glow-orb-2"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-12 text-center">

                <!-- Breadcrumbs -->
                <div class="d-flex justify-content-center">
                    <nav class="d-flex align-items-center flex-wrap gap-2 mb-3" style="font-size: 13.5px; color: #94a3b8;" aria-label="breadcrumb">
                        <a href="<?php echo $base_url; ?>index" style="color: #cbd5e1; text-decoration: none;"><i class="fas fa-home mr-1"></i> Home</a>
                        <span class="mx-1">/</span>
                        <a href="<?php echo $base_url; ?>gst-hsn-sac" style="color: #cbd5e1; text-decoration: none;">GST Tools</a>
                        <span class="mx-1">/</span>
                        <span class="text-white font-weight-bold">HSN / SAC Code Search</span>
                        <?php if (!empty($q)): ?>
                            <span class="mx-1">/</span>
                            <span class="text-info font-weight-bold" style="color: #38bdf8 !important;"><?php echo htmlspecialchars($q); ?></span>
                        <?php endif; ?>
                    </nav>
                </div>

                <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill mb-3" style="background: rgba(12, 74, 110, 0.85); border: 1px solid rgba(255, 255, 255, 0.2); color: #7dd3fc; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">
                    <i class="fas fa-file-invoice-dollar mr-1.5"></i> OFFICIAL CBIC &amp; GST COUNCIL 2026 DIRECTORY
                </div>

                <h1 class="text-white font-weight-bold mb-2" style="font-size: clamp(24px, 3.8vw, 36px); letter-spacing: -0.5px; line-height: 1.25;">
                    GST HSN &amp; SAC <span style="background: linear-gradient(135deg, #7dd3fc 0%, #38bdf8 50%, #818cf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Code Finder</span>
                </h1>
                <p class="text-light mb-0" style="color: #cbd5e1 !important; font-size: 15px; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Search 10,000+ official Harmonized System of Nomenclature (HSN) for Goods and Services Accounting Codes (SAC) for accurate GST invoicing and tax classification.
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
        <div class="gst-console-card p-4 p-md-4 mb-4">

            <!-- Search Form -->
            <form method="GET" action="<?php echo $base_url; ?>gst-hsn-sac" class="mb-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-8 mb-3 mb-md-0">
                        <div class="search-input-group">
                            <input type="text" name="q" class="form-control search-input-field" placeholder="Enter HSN/SAC Code or Product Name (e.g. 1001, Rice, Software, 9983, Cement, Medical)" minlength="2" maxlength="60" value="<?php echo htmlspecialchars($q); ?>" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <button type="submit" class="btn btn-block search-btn-primary d-flex align-items-center justify-content-center">
                            <i class="fas fa-search mr-2"></i> Search HSN / SAC
                        </button>
                    </div>
                </div>
            </form>

            <!-- Quick Suggestions -->
            <div class="d-flex align-items-center flex-wrap justify-content-center gap-2 pt-2 border-top">
                <span class="text-muted small font-weight-bold mr-2"><i class="fas fa-bolt text-warning mr-1"></i> Popular Searches:</span>
                <a href="<?php echo $base_url; ?>gst-hsn-sac?q=1006" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">1006 (Rice)</a>
                <a href="<?php echo $base_url; ?>gst-hsn-sac?q=9983" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">9983 (IT &amp; Legal Services)</a>
                <a href="<?php echo $base_url; ?>gst-hsn-sac?q=5208" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">5208 (Cotton Fabrics)</a>
                <a href="<?php echo $base_url; ?>gst-hsn-sac?q=3004" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">3004 (Medicaments)</a>
                <a href="<?php echo $base_url; ?>gst-hsn-sac?q=9954" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">9954 (Construction Services)</a>
                <a href="<?php echo $base_url; ?>gst-hsn-sac?q=9963" class="badge badge-light border px-2.5 py-1.5 text-secondary" style="font-family: var(--font-code); font-size: 11.5px;">9963 (Restaurant &amp; Hotel)</a>
            </div>
        </div>

        <!-- ======================================================================
             SEARCH RESULTS DISPLAY
             ====================================================================== -->
        <?php
            if (empty($q)) {
                $sql = "SELECT * FROM hsnsac ORDER BY updated_at DESC LIMIT 30";
                $res = direct_sql($sql);
                $results_title = "Recently Updated Tariff Codes";
            } else {
                $safe_q = mysqli_real_escape_string($con, $q);
                $sql = "SELECT * FROM hsnsac WHERE code LIKE '%$safe_q%' OR description LIKE '%$safe_q%' ORDER BY code ASC LIMIT 500";
                $res = direct_sql($sql);
                $results_title = 'Found ' . number_format($res['count']) . ' Result(s) for "' . htmlspecialchars($q) . '"';
            }
            $ct = $res['count'];
        ?>

        <?php if ($ct > 0): ?>
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                <h3 class="font-weight-bold text-dark mb-0" style="font-size: 19px;">
                    <?php echo $results_title; ?>
                </h3>
                <div class="text-muted small">
                    Official CBIC Tariff Database
                </div>
            </div>

            <!-- Desktop View Table -->
            <div class="desktop-only mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden;">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="hsn_table">
                            <thead style="background: #f8fafc; font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #475569;">
                                <tr>
                                    <th class="py-3 px-3" style="width: 60px;">#</th>
                                    <th class="py-3 px-3" style="width: 100px;">Classification</th>
                                    <th class="py-3 px-3" style="width: 160px;">Code</th>
                                    <th class="py-3 px-3">Description of Goods / Services</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 13.5px;">
                                <?php $idx = 1; foreach ($res['data'] as $row): ?>
                                    <tr>
                                        <td class="py-3 px-3 text-muted"><?php echo $idx++; ?></td>
                                        <td class="py-3 px-3">
                                            <span class="<?php echo ($row['type'] === 'HSN') ? 'badge-hsn' : 'badge-sac'; ?>">
                                                <?php echo htmlspecialchars($row['type']); ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-3">
                                            <div class="d-flex align-items-center">
                                                <span class="hsn-code-badge mr-2"><?php echo htmlspecialchars($row['code']); ?></span>
                                                <button type="button" class="btn-copy-code" onclick="copyText('<?php echo htmlspecialchars($row['code']); ?>', 'Code copied!')" title="Copy Code">
                                                    <i class="far fa-copy"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 text-secondary" style="line-height: 1.55;">
                                            <?php echo htmlspecialchars($row['description']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mobile View Cards -->
            <div class="mobile-only mb-4">
                <?php $mi = 1; foreach ($res['data'] as $row): ?>
                    <div class="hsn-card-mobile">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <span class="<?php echo ($row['type'] === 'HSN') ? 'badge-hsn' : 'badge-sac'; ?> mr-2">
                                    <?php echo htmlspecialchars($row['type']); ?>
                                </span>
                                <span class="hsn-code-badge"><?php echo htmlspecialchars($row['code']); ?></span>
                            </div>
                            <button type="button" class="btn-copy-code" onclick="copyText('<?php echo htmlspecialchars($row['code']); ?>', 'Code copied!')">
                                <i class="far fa-copy mr-1"></i> Copy
                            </button>
                        </div>
                        <div class="p-3 rounded-lg" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 13.5px; line-height: 1.55; color: #334155;">
                            <?php echo htmlspecialchars($row['description']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Last Updated Note -->
            <?php 
                $max_res = direct_sql("SELECT MAX(updated_at) as last_up FROM hsnsac");
                $last_up_date = !empty($max_res['data'][0]['last_up']) ? date('d M Y', strtotime($max_res['data'][0]['last_up'])) : 'August 2026';
            ?>
            <div class="text-center text-muted small mb-4">
                <i class="fas fa-shield-halved text-success mr-1"></i> Information synchronized with official GST Portal (gst.gov.in) &bull; Last updated: <strong><?php echo $last_up_date; ?></strong>
            </div>

        <?php else: ?>
            <!-- Not Found Alert -->
            <div class="alert p-4 mb-4" style="background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b; border-radius: 18px;">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-circle-exclamation mr-2" style="font-size: 22px;"></i>
                    <h4 class="font-weight-bold mb-0" style="font-size: 17px;">No HSN/SAC Codes Found for "<?php echo htmlspecialchars($q); ?>"</h4>
                </div>
                <p class="mb-0 small" style="line-height: 1.6;">
                    Try searching with the primary commodity name or a broader 2-digit/4-digit code (e.g., try "Cotton" or "5208").
                </p>
            </div>
        <?php endif; ?>

        <!-- ======================================================================
             FAQ SECTION
             ====================================================================== -->
        <?php if (!empty($faq_items)): ?>
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px; background: #ffffff;">
                <div class="d-flex align-items-center mb-3">
                    <div class="mr-3" style="width: 42px; height: 42px; border-radius: 12px; background: #eff6ff; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div>
                        <h3 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Frequently Asked Questions</h3>
                        <p class="text-muted small mb-0">Learn about HSN/SAC invoicing rules, digit requirements, and GST rate schedules.</p>
                    </div>
                </div>

                <div class="accordion" id="gstFaqAccordion">
                    <?php foreach ($faq_items as $index => $faq): ?>
                        <div class="gst-faq-card">
                            <div class="gst-faq-header" data-toggle="collapse" data-target="#faqCollapse<?php echo $index; ?>" aria-expanded="<?php echo ($index === 0) ? 'true' : 'false'; ?>">
                                <span><i class="fas fa-circle-question text-info mr-2"></i> <?php echo htmlspecialchars($faq['q']); ?></span>
                                <i class="fas fa-chevron-down text-muted" style="font-size: 12px;"></i>
                            </div>
                            <div id="faqCollapse<?php echo $index; ?>" class="collapse <?php echo ($index === 0) ? 'show' : ''; ?>" data-parent="#gstFaqAccordion">
                                <div class="gst-faq-body">
                                    <?php echo htmlspecialchars($faq['a']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ======================================================================
             EDUCATIONAL GUIDE: HSN VS SAC BREAKDOWN
             ====================================================================== -->
        <div class="card border-0 shadow-sm p-4 p-md-5 mb-4" style="border-radius: 24px; background: #ffffff;">
            <h3 class="font-weight-bold text-dark mb-2" style="font-size: 20px;">
                <i class="fas fa-book-open text-primary mr-2"></i> Understanding HSN &amp; SAC Code Structure
            </h3>
            <p class="text-muted small mb-4" style="line-height: 1.65;">
                Every GST registered entity is mandated by law to provide the correct HSN/SAC codes in their GSTR-1 returns and tax invoices.
            </p>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="p-3.5 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-primary mb-1" style="font-size: 15px;">
                            <i class="fas fa-boxes-stacked mr-1"></i> HSN Code for Goods (2, 4, 6, or 8 Digits)
                        </div>
                        <ul class="text-muted small pl-3 mb-0" style="line-height: 1.7;">
                            <li><strong>First 2 Digits:</strong> Chapter under the Customs Tariff Act (e.g. Chapter 10 for Cereals).</li>
                            <li><strong>Next 2 Digits:</strong> Heading within that chapter (e.g. 10.06 for Rice).</li>
                            <li><strong>Next 2 Digits:</strong> Sub-heading product description.</li>
                            <li><strong>Last 2 Digits:</strong> Specific regional tariff item for Indian customs &amp; GST.</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="p-3.5 rounded-lg border h-100" style="background: #f8fafc; border-radius: 14px;">
                        <div class="font-weight-bold text-success mb-1" style="font-size: 15px;">
                            <i class="fas fa-hand-holding-dollar mr-1"></i> SAC Code for Services (6 Digits)
                        </div>
                        <ul class="text-muted small pl-3 mb-0" style="line-height: 1.7;">
                            <li><strong>First 2 Digits:</strong> Always fixed as '99' for all services in India.</li>
                            <li><strong>Middle 2 Digits:</strong> Broad service group (e.g. 9983 for Information Technology &amp; Professional services).</li>
                            <li><strong>Last 2 Digits:</strong> Detailed specific service nature (e.g. 998314 for IT infrastructure provisioning).</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Toast Notification -->
<div id="gstToast" class="gst-toast">
    <i class="fas fa-check-circle text-info" style="font-size: 18px;"></i>
    <span id="gstToastMsg">Copied to clipboard!</span>
</div>

<?php require_once('includes/footer.php'); ?>

<script>
    $(document).ready(function() {
        if ($('#hsn_table').length) {
            $('#hsn_table').DataTable({
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 25,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Filter table items..."
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
        var toast = document.getElementById('gstToast');
        var msgEl = document.getElementById('gstToastMsg');
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
