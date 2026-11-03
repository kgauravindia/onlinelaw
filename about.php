<?php
/**
 * Online Law - About Us
 * Modernized for 2026 Public Legal & Civic Directory Platform
 */
require_once('includes/config.php');
require_once('includes/function.php');

$page_title = "About Us | Democratizing Legal, Banking & Public Data in India - Online Law";
$meta_desc = "Learn about Online Law - India's premier open legal and public directory platform. Providing 100% free, fast, and verified access to Bank IFSC & MICR, PIN Codes, MCA Company Incorporation, NIC 2025 Codes, GST HSN/SAC, and Customer Care Helplines.";
$meta_keywords = "about online law, legal tools mission india, democratizing public data, free financial tools, offerplant legal initiative, ifsc directory mission, pincode directory india, nic code directory";
$canonical_url = $base_url . "about";

// Schema.org Structured Data
$schema_graphs = [
    [
        "@context" => "https://schema.org",
        "@type" => "AboutPage",
        "name" => $page_title,
        "description" => $meta_desc,
        "url" => $canonical_url,
        "breadcrumb" => [
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
                ["@type" => "ListItem", "position" => 2, "name" => "About Us", "item" => $canonical_url]
            ]
        ]
    ],
    [
        "@context" => "https://schema.org",
        "@type" => "Organization",
        "name" => "Online Law",
        "url" => $base_url,
        "logo" => $base_url . "images/logo/logo.png",
        "description" => "India's open civic and regulatory public directory engine.",
        "founder" => [
            "@type" => "Person",
            "name" => "K Gaurav"
        ],
        "parentOrganization" => [
            "@type" => "Organization",
            "name" => "OfferPlant Technologies"
        ]
    ]
];
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
    <meta name="author" content="OfferPlant">
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
            --about-primary: #ff671f;
            --about-primary-hover: #ea580c;
            --about-dark: #071329;
            --about-surface: #f8fafc;
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-code: 'JetBrains Mono', monospace;
        }

        body {
            font-family: var(--font-main) !important;
            background-color: var(--about-surface);
            color: #1e293b;
        }

        /* Hero Header */
        .about-hero-section {
            background-color: #ffffff !important;
            background: linear-gradient(180deg, #ffffff 0%, #fafbfc 55%, #f1f5f9 100%) !important;
            padding: 65px 0 75px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid #e2e8f0;
        }
        .about-glow-orb-1 {
            position: absolute;
            width: 520px;
            height: 520px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 103, 31, 0.08) 0%, transparent 70%);
            top: -140px;
            left: -100px;
            pointer-events: none;
            filter: blur(60px);
        }
        .about-glow-orb-2 {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(4, 106, 56, 0.07) 0%, transparent 70%);
            bottom: -140px;
            right: -80px;
            pointer-events: none;
            filter: blur(65px);
        }

        .about-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border: 1.5px solid #fed7aa;
            color: #c2410c;
            padding: 7px 20px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.9px;
            text-transform: uppercase;
            box-shadow: 0 4px 14px rgba(255, 103, 31, 0.08);
        }
        .about-pulse-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
            animation: aboutPulse 2s infinite;
        }
        @keyframes aboutPulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .hero-title-highlight {
            background: linear-gradient(135deg, #ff671f 0%, #ea580c 45%, #046a38 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }
        .hero-title-white {
            color: #0f172a;
            text-shadow: none;
        }

        /* Scale Stat Section */
        .scale-stat-section {
            background: #ffffff;
            padding: 65px 0;
            position: relative;
            overflow: hidden;
            border-top: 3.5px solid #ff671f;
            border-bottom: 3.5px solid #046a38;
        }
        .stat-card-colored {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 20px;
            padding: 28px 20px;
            transition: all 0.3s ease;
            text-align: center;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        }
        .stat-card-colored:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            transform: translateY(-6px);
            box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.08);
        }
        .stat-icon-circle {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #ffffff;
            margin-bottom: 14px;
            box-shadow: 0 8px 18px -4px rgba(0, 0, 0, 0.25);
        }

        /* Modern Feature Cards */
        .feature-card-modern {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 28px 24px;
            transition: all 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .feature-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.1);
            border-color: #93c5fd;
        }
        .feature-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 18px;
            transition: transform 0.25s ease;
        }
        .feature-card-modern:hover .feature-icon-box {
            transform: scale(1.08);
        }

        /* Leadership Cards */
        .leadership-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 32px 28px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
            transition: all 0.25s ease;
            height: 100%;
        }
        .leadership-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.09);
            border-color: #93c5fd;
        }

        /* Tool Explore Shortcut Item */
        .tool-explore-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none !important;
            transition: all 0.2s ease;
        }
        .tool-explore-item:hover {
            background: #f8fafc;
            border-color: #ff671f;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .tool-explore-item .item-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        /* FAQ Card */
        .about-faq-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .about-faq-header {
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
        .about-faq-header:hover {
            background: #fff7ed;
            color: #ff671f;
        }
        .about-faq-body {
            padding: 0 22px 18px 22px;
            color: #475569;
            font-size: 14px;
            line-height: 1.65;
        }
    </style>
</head>
<body>

<?php require_once('includes/header.php'); ?>

<!-- ==========================================================================
     PAGE HERO HEADER
     ========================================================================== -->
<section class="about-hero-section">
    <div class="about-glow-orb-1"></div>
    <div class="about-glow-orb-2"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-lg-10 col-12">
                <div class="about-badge-pill mb-3">
                    <span class="about-pulse-dot"></span>
                    <span>EMPOWERING 1.4B+ CITIZENS &bull; DIGITAL INDIA INITIATIVE</span>
                </div>
                <h1 class="mb-3 font-weight-bold" style="font-size: clamp(28px, 4.2vw, 44px); letter-spacing: -0.6px; line-height: 1.25;">
                    <span class="hero-title-highlight">Democratizing Access</span> <span class="hero-title-white">to India's Civic, Legal &amp; Financial Data</span>
                </h1>
                <p class="mb-4" style="font-size: 16px; line-height: 1.75; max-width: 780px; margin: 0 auto; color: #475569 !important;">
                    Online Law is India's fast, 100% free, and unified open public directory engine—designed to connect citizens, startups, legal practitioners, and enterprises with authoritative civic information instantly.
                </p>
                <ul class="list-inline mb-0" style="color: #64748b; font-size: 14px;">
                    <li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: #475569; text-decoration: none;"><i class="fas fa-home mr-1"></i> Home</a></li>
                    <li class="list-inline-item mx-2 text-muted">/</li>
                    <li class="list-inline-item text-dark font-weight-bold">About Us</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     MISSION & STORY SECTION
     ========================================================================== -->
<section class="py-5" style="background: #ffffff; padding: 70px 0 !important;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 col-12 mb-5 mb-lg-0">
                <div class="position-relative"> 
                    <div class="overflow-hidden" style="border-radius: 24px; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.15); border: 4px solid #ffffff;">
                        <img src="images/planning.jpg" alt="About Online Law Mission" style="width: 100%; height: auto; display: block; object-fit: cover;">
                    </div>
                    <!-- Overlay floating badge -->
                    <div class="position-absolute d-none d-sm-flex align-items-center" style="bottom: -20px; right: 20px; background: #ffffff; padding: 14px 22px; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; gap: 12px;">
                        <div style="width: 42px; height: 42px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <div style="font-size: 13px; font-weight: 700; color: #0f172a;">100% Free &amp; Public</div>
                            <div style="font-size: 12px; color: #64748b;">Built for 1.4B+ Citizens</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-12 pl-lg-5">
                <div class="mb-4">
                    <span class="text-primary font-weight-bold" style="font-size: 13px; letter-spacing: 1px; text-transform: uppercase;">Our Purpose &amp; Vision</span>
                    <h2 class="mt-2 mb-3 font-weight-bold text-dark" style="font-size: clamp(22px, 3vw, 30px); line-height: 1.3;">
                        Building India's Most Reliable Digital Public Directory
                    </h2>
                    <p style="font-size: 15px; color: #475569; line-height: 1.8;">
                        In an increasingly digital economy, accessing essential civic and regulatory identifiers—such as postal PIN codes, banking routing codes (IFSC &amp; MICR), corporate incorporation records, business activity classifications (NIC 2025), and GST tariffs—should never be tedious or paywalled.
                    </p>
                    <p style="font-size: 15px; color: #475569; line-height: 1.8;">
                        <strong>Online Law</strong>, an initiative powered by OfferPlant Technologies, bridges the information gap by aggregating and indexing millions of public data records into a lightning-fast, secure, and mobile-friendly search interface.
                    </p>
                </div>
                
                <div class="row pt-2 mb-4">
                    <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-start">
                            <div style="color: #ff671f; font-size: 18px; margin-right: 12px; margin-top: 2px;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 font-weight-bold" style="font-size: 15px; color: #0f172a;">Unified Access</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.5;">One platform for banking, postal, corporate, tax, and legal reference data.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-start">
                            <div style="color: #059669; font-size: 18px; margin-right: 12px; margin-top: 2px;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 font-weight-bold" style="font-size: 15px; color: #0f172a;">Zero Paywalls</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.5;">Every tool and search lookup is completely free forever for all citizens and businesses.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-start">
                            <div style="color: #7c3aed; font-size: 18px; margin-right: 12px; margin-top: 2px;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 font-weight-bold" style="font-size: 15px; color: #0f172a;">Privacy First</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.5;">No tracking of queries, no accounts required, no intrusive advertisements.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-start">
                            <div style="color: #d97706; font-size: 18px; margin-right: 12px; margin-top: 2px;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 font-weight-bold" style="font-size: 15px; color: #0f172a;">1-Click Productivity</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.5;">Instant 1-click copy buttons for IFSC, MICR, PIN codes, and HSN numbers.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-3" style="gap: 12px;">
                    <a href="<?php echo $base_url; ?>bank-finder" class="btn btn-primary font-weight-bold px-4 py-2.5" style="border-radius: 12px;">
                        <i class="fas fa-landmark mr-1.5"></i> Bank Finder
                    </a>
                    <a href="<?php echo $base_url; ?>pincode-finder" class="btn btn-outline-primary font-weight-bold px-4 py-2.5" style="border-radius: 12px;">
                        <i class="fas fa-map-pin mr-1.5"></i> PIN Code Finder
                    </a>
                    <a href="<?php echo $base_url; ?>contact" class="btn btn-outline-secondary font-weight-bold px-4 py-2.5" style="border-radius: 12px;">
                        <i class="fas fa-envelope mr-1.5"></i> Contact Team
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     SCALE & KEY NUMBERS (VIBRANT GRADIENT SECTION)
     ========================================================================== -->
<section class="scale-stat-section">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row text-center mb-4 pb-2">
            <div class="col-12">
                <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill mb-2" style="background: #fff7ed; border: 1.5px solid #fed7aa; color: #c2410c; font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                    <i class="fas fa-chart-line mr-1.5"></i> PLATFORM SCALE &amp; INDEX
                </div>
                <h3 class="text-dark mt-1 mb-0 font-weight-bold" style="font-size: clamp(22px, 3vw, 28px);">
                    Millions of Authoritative Records Across India
                </h3>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-lg-3 col-6 mb-4 mb-lg-0">
                <div class="stat-card-colored"> 
                    <div class="stat-icon-circle" style="background: linear-gradient(135deg, #0284c7, #0ea5e9);">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="text-dark mb-1" style="font-size: 34px; font-weight: 800; font-family: var(--font-code); line-height: 1.1;">181K+</div>
                    <div style="font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Bank Branches (IFSC &amp; MICR)</div>
                </div>
            </div>
            <div class="col-lg-3 col-6 mb-4 mb-lg-0">
                <div class="stat-card-colored"> 
                    <div class="stat-icon-circle" style="background: linear-gradient(135deg, #059669, #10b981);">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="text-dark mb-1" style="font-size: 34px; font-weight: 800; font-family: var(--font-code); line-height: 1.1;">155K+</div>
                    <div style="font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">PIN Codes &amp; Post Offices</div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stat-card-colored"> 
                    <div class="stat-icon-circle" style="background: linear-gradient(135deg, #d97706, #f59e0b);">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="text-dark mb-1" style="font-size: 34px; font-weight: 800; font-family: var(--font-code); line-height: 1.1;">3.1M+</div>
                    <div style="font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">MCA Companies &amp; NIC</div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stat-card-colored"> 
                    <div class="stat-icon-circle" style="background: linear-gradient(135deg, #7c3aed, #a855f7);">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="text-dark mb-1" style="font-size: 34px; font-weight: 800; font-family: var(--font-code); line-height: 1.1;">22K+</div>
                    <div style="font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">GST HSN / SAC Codes</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     CORE DIRECTORY OFFERINGS
     ========================================================================== -->
<section class="py-5" style="background: #f8fafc; padding: 70px 0 !important;">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8 col-12">
                <span class="text-primary font-weight-bold" style="font-size: 13px; letter-spacing: 1px; text-transform: uppercase;">Comprehensive Suite</span>
                <h2 class="mt-2 mb-3 font-weight-bold text-dark" style="font-size: 30px;">What You Can Discover on Online Law</h2>
                <p class="text-muted" style="font-size: 15px;">Fast, reliable, and verified lookups covering essential business and civic workflows.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card-modern">
                    <div>
                        <div class="feature-icon-box" style="background: #ecfdf5; color: #047857;">
                            <i class="fas fa-landmark"></i>
                        </div>
                        <h5 class="font-weight-bold mb-2 text-dark" style="font-size: 18px;">Bank &amp; IFSC Finder</h5>
                        <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">Search 181,913+ branches using our 4-step cascading finder (Bank &rarr; State &rarr; District &rarr; Branch), IFSC lookup, and MICR code tools.</p>
                    </div>
                    <a href="<?php echo $base_url; ?>bank-finder" class="font-weight-bold text-success" style="font-size: 14px; text-decoration: none;">
                        Launch Bank Finder <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card-modern">
                    <div>
                        <div class="feature-icon-box" style="background: #fff7ed; color: #ff671f;">
                            <i class="fas fa-map-location-dot"></i>
                        </div>
                        <h5 class="font-weight-bold mb-2 text-dark" style="font-size: 18px;">PIN Code Finder</h5>
                        <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">Search India Post postal index numbers by 6-digit code or cascade through State &rarr; District &rarr; Region &rarr; Post Office.</p>
                    </div>
                    <a href="<?php echo $base_url; ?>pincode-finder" class="font-weight-bold text-primary" style="font-size: 14px; text-decoration: none;">
                        Find PIN Codes <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card-modern">
                    <div>
                        <div class="feature-icon-box" style="background: #fef3c7; color: #d97706;">
                            <i class="fas fa-building"></i>
                        </div>
                        <h5 class="font-weight-bold mb-2 text-dark" style="font-size: 18px;">Company Incorporation</h5>
                        <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">Explore MCA-registered companies, state ROC registrations, authorized capital, active company profiles, and industry classifications.</p>
                    </div>
                    <a href="<?php echo $base_url; ?>company-incorporation" class="font-weight-bold" style="color: #d97706; font-size: 14px; text-decoration: none;">
                        Explore Companies <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card-modern">
                    <div>
                        <div class="feature-icon-box" style="background: #f3e8ff; color: #7c3aed;">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <h5 class="font-weight-bold mb-2 text-dark" style="font-size: 18px;">NIC 2025 Activity Codes</h5>
                        <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">Access the official MoSPI National Industrial Classification hierarchy across Divisions, Groups, Classes, and Sub-classes for SPICe+ and Udyam.</p>
                    </div>
                    <a href="<?php echo $base_url; ?>nic" class="font-weight-bold" style="color: #7c3aed; font-size: 14px; text-decoration: none;">
                        View NIC Codes <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card-modern">
                    <div>
                        <div class="feature-icon-box" style="background: #e0f2fe; color: #0284c7;">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h5 class="font-weight-bold mb-2 text-dark" style="font-size: 18px;">GST HSN &amp; SAC Finder</h5>
                        <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">Search official Harmonized System of Nomenclature (HSN) and Service Accounting Codes (SAC) for accurate GST invoicing and GSTR returns.</p>
                    </div>
                    <a href="<?php echo $base_url; ?>gst-hsn-sac" class="font-weight-bold" style="color: #0284c7; font-size: 14px; text-decoration: none;">
                        GST HSN Finder <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card-modern">
                    <div>
                        <div class="feature-icon-box" style="background: #ffe4e6; color: #e11d48;">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h5 class="font-weight-bold mb-2 text-dark" style="font-size: 18px;">Bank Customer Care &amp; Helplines</h5>
                        <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">Verified toll-free helplines, card blocking hotlines, balance inquiry numbers, and grievance email contacts for all Indian banks.</p>
                    </div>
                    <a href="<?php echo $base_url; ?>bank/contact-details" class="font-weight-bold" style="color: #e11d48; font-size: 14px; text-decoration: none;">
                        Customer Care Directory <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     VALUES & COMMITMENT SECTION
     ========================================================================== -->
<section style="background: #ffffff; padding: 70px 0; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <span class="text-primary font-weight-bold" style="font-size: 13px; letter-spacing: 1px; text-transform: uppercase;">Our Core Principles</span>
                <h2 class="mt-2 mb-3 font-weight-bold text-dark" style="font-size: 28px;">Designed With Accuracy, Speed &amp; Integrity</h2>
                <p style="font-size: 15px; color: #475569; line-height: 1.8;">
                    We believe essential civic and regulatory directory data should be available friction-free. We uphold four foundational pillars in everything we build:
                </p>
                
                <div class="mt-4">
                    <div class="d-flex mb-3">
                        <div class="mr-3 text-primary" style="font-size: 20px;"><i class="fas fa-bolt"></i></div>
                        <div>
                            <h6 class="font-weight-bold mb-1 text-dark" style="font-size: 15px;">Sub-Second Search Latency</h6>
                            <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.6;">Optimized server indexing and smart queries ensure lookups complete in fractions of a second.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="mr-3 text-success" style="font-size: 20px;"><i class="fas fa-lock"></i></div>
                        <div>
                            <h6 class="font-weight-bold mb-1 text-dark" style="font-size: 15px;">No Tracking &amp; Strict Privacy</h6>
                            <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.6;">We do not profile users, require sign-ups, or sell search patterns. Your lookups remain private.</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="mr-3 text-warning" style="font-size: 20px;"><i class="fas fa-sync-alt"></i></div>
                        <div>
                            <h6 class="font-weight-bold mb-1 text-dark" style="font-size: 15px;">Continuous Official Synchronization</h6>
                            <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.6;">Databases are continuously synced against official RBI lists, MCA registries, CBIC tariff notifications, and India Post data.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="p-4" style="background: #f8fafc; border-radius: 24px; border: 1px solid #e2e8f0;">
                    <h5 class="font-weight-bold mb-3 text-dark" style="font-size: 17px;">
                        <i class="fas fa-compass text-primary mr-2"></i> Quick Directory Shortcuts
                    </h5>
                    <div class="d-flex flex-column gap-2" style="gap: 10px;">
                        <a href="<?php echo $base_url; ?>bank-finder" class="tool-explore-item">
                            <div class="item-icon" style="background: #ecfdf5; color: #047857;"><i class="fas fa-landmark"></i></div>
                            <div class="flex-grow-1">
                                <div style="font-weight: 700; font-size: 14px; color: #0f172a;">Bank &amp; IFSC Finder</div>
                                <div style="font-size: 12px; color: #64748b;">Cascading Bank &rarr; State &rarr; District &rarr; Branch lookup</div>
                            </div>
                            <i class="fas fa-chevron-right text-muted" style="font-size: 12px;"></i>
                        </a>
                        <a href="<?php echo $base_url; ?>pincode-finder" class="tool-explore-item">
                            <div class="item-icon" style="background: #fff7ed; color: #ff671f;"><i class="fas fa-map-location-dot"></i></div>
                            <div class="flex-grow-1">
                                <div style="font-weight: 700; font-size: 14px; color: #0f172a;">PIN Code Finder</div>
                                <div style="font-size: 12px; color: #64748b;">Cascading State &rarr; District &rarr; Region PIN search</div>
                            </div>
                            <i class="fas fa-chevron-right text-muted" style="font-size: 12px;"></i>
                        </a>
                        <a href="<?php echo $base_url; ?>bank/contact-details" class="tool-explore-item">
                            <div class="item-icon" style="background: #fff1f2; color: #e11d48;"><i class="fas fa-headset"></i></div>
                            <div class="flex-grow-1">
                                <div style="font-weight: 700; font-size: 14px; color: #0f172a;">Bank Helplines &amp; Customer Care</div>
                                <div style="font-size: 12px; color: #64748b;">Toll-free customer care, card blocking &amp; balance check</div>
                            </div>
                            <i class="fas fa-chevron-right text-muted" style="font-size: 12px;"></i>
                        </a>
                        <a href="<?php echo $base_url; ?>nic" class="tool-explore-item">
                            <div class="item-icon" style="background: #f3e8ff; color: #7c3aed;"><i class="fas fa-sitemap"></i></div>
                            <div class="flex-grow-1">
                                <div style="font-weight: 700; font-size: 14px; color: #0f172a;">NIC 2025 Activity Codes</div>
                                <div style="font-size: 12px; color: #64748b;">National Industrial Classification for SPICe+ &amp; Udyam</div>
                            </div>
                            <i class="fas fa-chevron-right text-muted" style="font-size: 12px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     LEADERSHIP & STEWARDSHIP
     ========================================================================== -->
<section class="py-5" style="background: #f8fafc; padding: 70px 0 !important;">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8 col-12">
                <span class="text-primary font-weight-bold" style="font-size: 13px; letter-spacing: 1px; text-transform: uppercase;">Leadership &amp; Stewardship</span>
                <h2 class="mt-2 mb-3 font-weight-bold text-dark" style="font-size: 30px;">Words from the Team</h2>
                <p class="text-muted" style="font-size: 15px;">Driven by a mission to engineer high-utility digital infrastructure for citizens and organizations.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="leadership-card">
                    <i class="fas fa-quote-left text-primary mb-3" style="font-size: 26px; opacity: 0.4;"></i>
                    <p class="mb-4" style="color: #475569; font-size: 15px; font-style: italic; line-height: 1.8;">
                        "Building Online Law is centered on one foundational truth: accurate civic and legal tools shouldn't be gated behind complex databases or subscription fees. We are dedicated to maintaining an open, fast, and verified repository that saves people time every single day."
                    </p>
                    <div class="d-flex align-items-center pt-3 border-top">
                        <img src="images/circle/gaurav.jpg" alt="K Gaurav" onerror="this.src='images/logo/logo.png';" style="width: 54px; height: 54px; border-radius: 50%; object-fit: cover; border: 2px solid #bfdbfe;">
                        <div class="ml-3 pl-2">
                            <h5 class="m-0 font-weight-bold text-dark" style="font-size: 16px;">K Gaurav</h5>
                            <span style="color: #ff671f; font-size: 13px; font-weight: 600;">Director, OfferPlant</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="leadership-card">
                    <i class="fas fa-quote-left text-primary mb-3" style="font-size: 26px; opacity: 0.4;"></i>
                    <p class="mb-4" style="color: #475569; font-size: 15px; font-style: italic; line-height: 1.8;">
                        "Our focus has been engineering resilience and speed at scale. By handling millions of records spanning MCA corporations, postal PINs, and banking codes, we allow citizens, developers, and businesses to integrate critical reference data seamlessly into their daily operations."
                    </p>
                    <div class="d-flex align-items-center pt-3 border-top">
                        <img src="images/circle/saurabh.jpg" alt="S Nagbanshi" onerror="this.src='images/logo/logo.png';" style="width: 54px; height: 54px; border-radius: 50%; object-fit: cover; border: 2px solid #bfdbfe;">
                        <div class="ml-3 pl-2">
                            <h5 class="m-0 font-weight-bold text-dark" style="font-size: 16px;">S Nagbanshi</h5>
                            <span style="color: #ff671f; font-size: 13px; font-weight: 600;">CEO, OfferPlant</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     CALL TO ACTION BANNER
     ========================================================================== -->
<section style="background: #ffffff; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; padding: 56px 0;">
    <div class="container text-center">
        <h2 class="text-dark mb-2 font-weight-bold" style="font-size: 28px;">Ready to Search India's Public Directory?</h2>
        <p class="text-muted mb-4" style="font-size: 15px; max-width: 600px; margin: 0 auto 24px;">Find verified Bank IFSC, PIN Codes, Company records, and GST tariffs in seconds.</p>
        <div class="d-inline-flex flex-wrap gap-2 justify-content-center" style="gap: 12px;">
            <a href="<?php echo $base_url; ?>index" class="btn btn-primary font-weight-bold px-4 py-2.5" style="border-radius: 12px; background: linear-gradient(135deg, #ff671f, #ea580c); border: none; box-shadow: 0 4px 15px rgba(255, 103, 31, 0.4);">
                <i class="fas fa-home mr-2"></i> Go to Homepage
            </a>
            <a href="<?php echo $base_url; ?>contact" class="btn btn-outline-secondary font-weight-bold px-4 py-2.5" style="border-radius: 12px; border: 1.5px solid #cbd5e1; color: #1e293b; background: #ffffff;">
                <i class="fas fa-paper-plane mr-2"></i> Contact Us
            </a>
        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>
</body>
</html>
