<?php
    // Set include path to root directory so that relative requires work correctly
    set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__DIR__));
    require_once('includes/config.php');

    // Strip subdirectory from base_url if needed
    if (isset($base_url)) {
        $base_url = preg_replace('/bank\/$/', '', $base_url);
    }

    require_once('includes/function.php'); 
    require_once(__DIR__ . '/bank-contacts-data.php');

    $page_title = "All Indian Banks Official Contact Directory (Toll-Free, Email & Website 2026) | Online Law";
    $meta_desc = "Verified 2026 directory of official customer care toll-free numbers, support email addresses, head office addresses, and official websites for 250+ banks in India. Includes Public, Private, Payments, Foreign & Co-operative Banks.";
    $meta_keywords = "Bank contact details, bank customer care numbers, toll free banking support, bank official websites, bank email list, sbi support email, hdfc toll free helpline, bank grievance redressal, rbi banking ombudsman";
    $canonical_url = $base_url . "bank/contact-details";

    // Category Counts
    $cat_counts = [
        'All' => count($bank_contacts),
        'Public Sector' => 0,
        'Private Sector' => 0,
        'Payments & SFB' => 0,
        'Foreign Bank' => 0,
        'Co-operative' => 0
    ];

    foreach ($bank_contacts as $b) {
        $cat = $b['category'] ?? 'Co-operative';
        if (isset($cat_counts[$cat])) {
            $cat_counts[$cat]++;
        } else {
            $cat_counts['Co-operative']++;
        }
    }

    // Schema.org Structured Data
    $schema_graphs = [];

    // BreadcrumbList
    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "Bank Directory", "item" => $base_url . "bank"],
            ["@type" => "ListItem", "position" => 3, "name" => "Bank Contact Details", "item" => $canonical_url]
        ]
    ];

    // FAQ Items
    $faq_items = [
        [
            "q" => "How do I contact bank customer care for urgent credit/debit card blocking?",
            "a" => "Dial your bank's dedicated 24x7 toll-free customer care number listed above immediately. Major banks like SBI (1800 1234), HDFC (1800 202 6161), and ICICI (1800 1080) have dedicated automated IVR options to block stolen or compromised cards instantly."
        ],
        [
            "q" => "What should I do if a bank does not resolve my grievance?",
            "a" => "If your complaint is not resolved within 30 days by the bank's internal grievance redressal officer or Principal Nodal Officer, you can escalate it to the RBI Banking Ombudsman via the official RBI Complaint Management System (CMS) portal (cms.rbi.org.in) or toll-free number 14448."
        ],
        [
            "q" => "How can I verify whether a bank website is genuine?",
            "a" => "Always ensure the web URL starts with HTTPS and uses the verified official domain name (e.g. sbi.co.in, hdfcbank.com, icicibank.com). Never enter net banking credentials on links received via SMS, WhatsApp, or unsolicited emails."
        ],
        [
            "q" => "Are customer care toll-free numbers chargeable?",
            "a" => "Numbers starting with 1800 are completely free to call from any mobile or landline network in India. Numbers starting with 1860 are local call charge numbers billed according to your telecommunication tariff."
        ]
    ];

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

    <link rel="stylesheet" href="<?php echo $base_url; ?>css/bank.css">

    <!-- JSON-LD Structured Data Schema -->
    <?php foreach ($schema_graphs as $schema): ?>
        <script type="application/ld+json">
        <?php echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>
        </script>
    <?php endforeach; ?>
</head>
<body>

<?php require_once('includes/header.php'); ?>

<style>
    :root {
        --contact-primary: #047857;
        --contact-primary-dark: #064e3b;
        --contact-accent: #10b981;
        --contact-surface: #f8fafc;
    }

    /* Hero Banner */
    .hero-contact-banner {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #0f172a 100%);
        padding: 56px 0 64px 0;
        position: relative;
        overflow: hidden;
    }
    .contact-glow-orb-1 {
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
    .contact-glow-orb-2 {
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

    /* Main Console Container */
    .contact-console-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.8);
        position: relative;
        margin-top: -36px;
        z-index: 20;
    }

    /* Filter pill buttons */
    .cat-filter-btn {
        padding: 8px 16px;
        border-radius: 30px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .cat-filter-btn:hover {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: var(--contact-primary);
        transform: translateY(-1px);
    }
    .cat-filter-btn.active {
        background: var(--contact-primary);
        border-color: var(--contact-primary);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(4, 120, 87, 0.25);
    }
    .cat-filter-btn .cat-count-badge {
        background: rgba(0, 0, 0, 0.08);
        padding: 2px 7px;
        border-radius: 20px;
        font-size: 11px;
    }
    .cat-filter-btn.active .cat-count-badge {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Quick spec tiles */
    .contact-spec-tile {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
    }
    .contact-spec-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.06);
        border-color: #cbd5e1;
    }

    /* Table styling */
    .table-contact thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        padding: 14px 16px;
    }
    .table-contact tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-contact tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Mobile Card Layout */
    .bank-contact-card-mobile {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 14px;
        box-shadow: 0 2px 6px -1px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
    }
    .bank-contact-card-mobile:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    /* Category Badges */
    .badge-cat-public { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .badge-cat-private { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .badge-cat-payment { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .badge-cat-foreign { background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }
    .badge-cat-coop { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

    /* FAQ Card */
    .contact-faq-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        margin-bottom: 12px;
        overflow: hidden;
        transition: all 0.2s ease;
    }
    .contact-faq-card .faq-header-btn {
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
    .contact-faq-card .faq-header-btn:hover {
        background: #f8fafc;
        color: var(--contact-primary);
    }
    .contact-faq-card .faq-body-text {
        padding: 0 20px 16px 20px;
        font-size: 14px;
        line-height: 1.6;
        color: #475569;
    }

    /* Floating Toast */
    .contact-toast {
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
    .contact-toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    @media (max-width: 767px) {
        .contact-console-card {
            margin-top: -24px;
            padding: 16px 14px !important;
            border-radius: 18px !important;
        }
        .desktop-only { display: none !important; }
        .mobile-only { display: block !important; }
        .contact-toast {
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

<!-- ==========================================================================
     HERO MODERN BANNER SECTION
     ========================================================================== -->
<section class="hero-contact-banner">
    <div class="contact-glow-orb-1"></div>
    <div class="contact-glow-orb-2"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-12 text-center">

                <!-- Breadcrumbs -->
                <div class="d-flex justify-content-center">
                    <nav class="d-flex align-items-center flex-wrap gap-2 mb-3" style="font-size: 13.5px; color: #94a3b8;" aria-label="breadcrumb">
                        <a href="<?php echo $base_url; ?>index" style="color: #cbd5e1; text-decoration: none;"><i class="fas fa-home mr-1"></i> Home</a>
                        <span class="mx-1">/</span>
                        <a href="<?php echo $base_url; ?>bank" style="color: #cbd5e1; text-decoration: none;">Bank Directory</a>
                        <span class="mx-1">/</span>
                        <span class="text-white font-weight-bold">Contact Details</span>
                    </nav>
                </div>

                <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill mb-3" style="background: rgba(6, 78, 59, 0.85); border: 1px solid rgba(255, 255, 255, 0.2); color: #86efac; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">
                    <i class="fas fa-headset mr-1.5"></i> OFFICIAL BANK HELPLINE DIRECTORY 2026
                </div>

                <h1 class="text-white font-weight-bold mb-2" style="font-size: clamp(24px, 3.8vw, 36px); letter-spacing: -0.5px; line-height: 1.25;">
                    All Indian Banks <span style="background: linear-gradient(135deg, #86efac 0%, #38bdf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Official Contact Details</span>
                </h1>
                <p class="text-light mb-0" style="color: #cbd5e1 !important; font-size: 15px; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Verified directory of 24x7 customer care toll-free numbers, support email addresses, head office addresses, and official banking websites for 250+ banks in India.
                </p>

            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     MAIN BODY & INTERACTIVE CONSOLE
     ========================================================================== -->
<div class="full-row py-4" style="background-color: #f8fafc; position: relative; z-index: 10;">
    <div class="container">

        <!-- Console Card -->
        <div class="contact-console-card p-4 p-md-4 mb-4">
            
            <!-- Quick Search Input & Live Filter -->
            <div class="row align-items-center mb-3">
                <div class="col-lg-6 col-md-7 mb-3 mb-md-0">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute text-muted" style="top: 50%; transform: translateY(-50%); left: 16px; font-size: 15px;"></i>
                        <input type="text" id="liveBankSearch" class="form-control" placeholder="Search by Bank Name, City, Phone or Email..." style="height: 48px; border-radius: 14px; padding-left: 44px; padding-right: 36px; font-size: 14.5px; border: 1.5px solid #e2e8f0;" onkeyup="filterBankDirectory();">
                        <button type="button" id="clearSearchBtn" class="btn btn-link position-absolute text-muted p-0" style="top: 50%; transform: translateY(-50%); right: 14px; display: none;" onclick="clearBankSearch();">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-6 col-md-5 text-md-right">
                    <span class="small text-muted font-weight-bold mr-2">Showing:</span>
                    <span id="filteredCountBadge" class="badge badge-success px-3 py-2 font-weight-bold" style="background: #047857; font-size: 13px; border-radius: 20px;">
                        <?php echo count($bank_contacts); ?> Banks
                    </span>
                    <a href="<?php echo $base_url; ?>bank-finder" class="btn btn-sm btn-outline-success font-weight-bold ml-2 px-3 py-2" style="border-radius: 12px;">
                        <i class="fas fa-search-location mr-1"></i> Bank Finder &rarr;
                    </a>
                </div>
            </div>

            <!-- Category Filter Tabs -->
            <div class="d-flex flex-wrap align-items-center gap-2 pt-2 pb-1 border-top" style="gap: 8px; border-color: #f1f5f9 !important;">
                <span class="small font-weight-bold text-muted text-uppercase mr-1">Categories:</span>
                <button type="button" class="cat-filter-btn active" data-cat="All" onclick="setCategoryFilter('All', this)">
                    All Banks <span class="cat-count-badge"><?php echo $cat_counts['All']; ?></span>
                </button>
                <button type="button" class="cat-filter-btn" data-cat="Public Sector" onclick="setCategoryFilter('Public Sector', this)">
                    <i class="fas fa-landmark text-primary mr-1"></i> Public Sector <span class="cat-count-badge"><?php echo $cat_counts['Public Sector']; ?></span>
                </button>
                <button type="button" class="cat-filter-btn" data-cat="Private Sector" onclick="setCategoryFilter('Private Sector', this)">
                    <i class="fas fa-building text-success mr-1"></i> Private Sector <span class="cat-count-badge"><?php echo $cat_counts['Private Sector']; ?></span>
                </button>
                <button type="button" class="cat-filter-btn" data-cat="Payments & SFB" onclick="setCategoryFilter('Payments & SFB', this)">
                    <i class="fas fa-mobile-alt text-warning mr-1"></i> Payments &amp; SFB <span class="cat-count-badge"><?php echo $cat_counts['Payments & SFB']; ?></span>
                </button>
                <button type="button" class="cat-filter-btn" data-cat="Foreign Bank" onclick="setCategoryFilter('Foreign Bank', this)">
                    <i class="fas fa-globe text-purple mr-1" style="color: #7c3aed;"></i> Foreign Banks <span class="cat-count-badge"><?php echo $cat_counts['Foreign Bank']; ?></span>
                </button>
                <button type="button" class="cat-filter-btn" data-cat="Co-operative" onclick="setCategoryFilter('Co-operative', this)">
                    <i class="fas fa-users text-muted mr-1"></i> Co-operative <span class="cat-count-badge"><?php echo $cat_counts['Co-operative']; ?></span>
                </button>
            </div>

        </div>

        <!-- ======================================================================
             DESKTOP DATA TABLE VIEW
             ====================================================================== -->
        <div class="bg-white rounded-xl shadow-sm border mb-4 p-4 desktop-only" style="border-radius: 20px;">
            <div class="table-responsive">
                <table class="table table-hover table-contact mb-0" id="bankContactTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="min-width: 200px;">Bank Name</th>
                            <th style="min-width: 220px;">Head Office / Address</th>
                            <th style="min-width: 150px;">Official Website</th>
                            <th style="min-width: 160px;">Toll-Free Customer Care</th>
                            <th style="min-width: 180px;">Support Email</th>
                            <th class="text-right" style="width: 110px;">Branch Finder</th>
                        </tr>
                    </thead>
                    <tbody id="bankTableBody">
                        <?php 
                        $sr = 1;
                        foreach ($bank_contacts as $b): 
                            $cat = $b['category'] ?? 'Co-operative';
                            $badge_class = 'badge-cat-coop';
                            if ($cat === 'Public Sector') $badge_class = 'badge-cat-public';
                            elseif ($cat === 'Private Sector') $badge_class = 'badge-cat-private';
                            elseif ($cat === 'Payments & SFB') $badge_class = 'badge-cat-payment';
                            elseif ($cat === 'Foreign Bank') $badge_class = 'badge-cat-foreign';
                        ?>
                            <tr class="bank-row" data-category="<?php echo htmlspecialchars($cat); ?>" data-search="<?php echo htmlspecialchars(strtolower($b['name'] . ' ' . $b['address'] . ' ' . $b['toll_free'] . ' ' . $b['email'])); ?>">
                                <td class="text-muted small"><?php echo $sr++; ?></td>
                                <td>
                                    <div class="font-weight-bold text-dark mb-1" style="font-size: 14.5px;">
                                        <?php echo htmlspecialchars($b['name']); ?>
                                    </div>
                                    <span class="badge <?php echo $badge_class; ?> px-2 py-0.5 rounded-pill font-weight-bold" style="font-size: 10.5px;">
                                        <?php echo htmlspecialchars($cat); ?>
                                    </span>
                                </td>
                                <td class="small text-muted" style="line-height: 1.45;">
                                    <?php echo htmlspecialchars($b['address'] ?: '-'); ?>
                                </td>
                                <td class="small">
                                    <?php if (!empty($b['website']) && $b['website'] !== '-'): 
                                        $clean_domain = preg_replace('/^https?:\/\/(www\.)?/i', '', rtrim($b['website'], '/'));
                                    ?>
                                        <a href="<?php echo htmlspecialchars($b['website']); ?>" target="_blank" rel="noopener noreferrer" class="text-primary font-weight-bold text-decoration-none d-inline-flex align-items-center">
                                            <i class="fas fa-external-link-alt mr-1.5" style="font-size: 11px;"></i>
                                            <span class="text-truncate" style="max-width: 140px;" title="<?php echo htmlspecialchars($b['website']); ?>">
                                                <?php echo htmlspecialchars($clean_domain); ?>
                                            </span>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-nowrap">
                                    <?php if (!empty($b['toll_free']) && $b['toll_free'] !== '-'): 
                                        $tolls = explode(',', $b['toll_free']);
                                    ?>
                                        <div class="d-flex flex-column gap-1" style="gap: 4px;">
                                            <?php foreach ($tolls as $t): 
                                                $t_clean = trim($t);
                                                if (empty($t_clean)) continue;
                                            ?>
                                                <div class="d-inline-flex align-items-center">
                                                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $t_clean); ?>" class="font-weight-bold text-success text-decoration-none mr-1.5">
                                                        <i class="fas fa-phone-alt mr-1" style="font-size: 11px;"></i><?php echo htmlspecialchars($t_clean); ?>
                                                    </a>
                                                    <button type="button" class="btn btn-xs btn-light border text-muted px-1.5 py-0.5" onclick="copyContactText('<?php echo htmlspecialchars($t_clean); ?>');" title="Copy Number">
                                                        <i class="far fa-copy" style="font-size: 10px;"></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <?php if (!empty($b['email']) && $b['email'] !== '-'): 
                                        $emails = explode(',', $b['email']);
                                    ?>
                                        <div class="d-flex flex-column gap-1" style="gap: 4px;">
                                            <?php foreach ($emails as $em): 
                                                $em_clean = trim($em);
                                                if (empty($em_clean)) continue;
                                            ?>
                                                <div class="d-inline-flex align-items-center">
                                                    <a href="mailto:<?php echo htmlspecialchars($em_clean); ?>" class="text-dark font-weight-medium text-decoration-none mr-1.5 text-truncate" style="max-width: 140px;" title="<?php echo htmlspecialchars($em_clean); ?>">
                                                        <i class="fas fa-envelope mr-1 text-muted" style="font-size: 11px;"></i><?php echo htmlspecialchars($em_clean); ?>
                                                    </a>
                                                    <button type="button" class="btn btn-xs btn-light border text-muted px-1.5 py-0.5" onclick="copyContactText('<?php echo htmlspecialchars($em_clean); ?>');" title="Copy Email">
                                                        <i class="far fa-copy" style="font-size: 10px;"></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right text-nowrap">
                                    <a href="<?php echo $base_url; ?>bank-finder?bank=<?php echo urlencode($b['name']); ?>" class="btn btn-sm btn-outline-success font-weight-bold px-2.5 py-1" style="border-radius: 8px; font-size: 12px;" title="Find branches and IFSC codes for <?php echo htmlspecialchars($b['name']); ?>">
                                        IFSC &rarr;
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ======================================================================
             MOBILE RESPONSIVE CARD VIEW
             ====================================================================== -->
        <div class="mobile-only" id="bankMobileContainer">
            <?php 
            $mSr = 1;
            foreach ($bank_contacts as $b): 
                $cat = $b['category'] ?? 'Co-operative';
                $badge_class = 'badge-cat-coop';
                if ($cat === 'Public Sector') $badge_class = 'badge-cat-public';
                elseif ($cat === 'Private Sector') $badge_class = 'badge-cat-private';
                elseif ($cat === 'Payments & SFB') $badge_class = 'badge-cat-payment';
                elseif ($cat === 'Foreign Bank') $badge_class = 'badge-cat-foreign';
            ?>
                <div class="bank-contact-card-mobile bank-mobile-card" data-category="<?php echo htmlspecialchars($cat); ?>" data-search="<?php echo htmlspecialchars(strtolower($b['name'] . ' ' . $b['address'] . ' ' . $b['toll_free'] . ' ' . $b['email'])); ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge badge-light border text-muted font-weight-bold small mb-1">#<?php echo $mSr++; ?></span>
                            <h5 class="mb-1 font-weight-bold text-dark" style="font-size: 15.5px;"><?php echo htmlspecialchars($b['name']); ?></h5>
                            <span class="badge <?php echo $badge_class; ?> px-2 py-0.5 rounded-pill font-weight-bold" style="font-size: 10px;">
                                <?php echo htmlspecialchars($cat); ?>
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($b['address'])): ?>
                        <p class="small text-muted mb-2.5" style="line-height: 1.4;">
                            <i class="fas fa-map-marker-alt text-danger mr-1"></i> <?php echo htmlspecialchars($b['address']); ?>
                        </p>
                    <?php endif; ?>

                    <div class="row no-gutters mb-2">
                        <?php if (!empty($b['toll_free']) && $b['toll_free'] !== '-'): ?>
                            <div class="col-12 mb-2">
                                <small class="text-uppercase text-muted font-weight-bold d-block" style="font-size: 10px;">Toll-Free Customer Care</small>
                                <div class="d-flex flex-wrap gap-1 align-items-center mt-1">
                                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', explode(',', $b['toll_free'])[0]); ?>" class="btn btn-xs btn-outline-success font-weight-bold px-2 py-1" style="border-radius: 8px; font-size: 12px;">
                                        <i class="fas fa-phone-alt mr-1"></i> <?php echo htmlspecialchars($b['toll_free']); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($b['email']) && $b['email'] !== '-'): ?>
                            <div class="col-12 mb-2">
                                <small class="text-uppercase text-muted font-weight-bold d-block" style="font-size: 10px;">Support Email</small>
                                <a href="mailto:<?php echo htmlspecialchars(explode(',', $b['email'])[0]); ?>" class="small text-dark font-weight-medium">
                                    <i class="fas fa-envelope text-primary mr-1"></i> <?php echo htmlspecialchars($b['email']); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($b['website']) && $b['website'] !== '-'): ?>
                            <div class="col-12 mb-2">
                                <small class="text-uppercase text-muted font-weight-bold d-block" style="font-size: 10px;">Official Portal</small>
                                <a href="<?php echo htmlspecialchars($b['website']); ?>" target="_blank" rel="noopener noreferrer" class="small text-primary font-weight-bold">
                                    <i class="fas fa-globe mr-1"></i> <?php echo htmlspecialchars(preg_replace('/^https?:\/\/(www\.)?/i', '', rtrim($b['website'], '/'))); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <a href="<?php echo $base_url; ?>bank-finder?bank=<?php echo urlencode($b['name']); ?>" class="btn btn-xs btn-success font-weight-bold px-3 py-1" style="border-radius: 8px; font-size: 12px; background: #047857; border-color: #047857;">
                            <i class="fas fa-university mr-1"></i> Find IFSC &amp; Branches &rarr;
                        </a>
                        <?php if (!empty($b['toll_free']) && $b['toll_free'] !== '-'): ?>
                            <button type="button" class="btn btn-xs btn-light border text-muted px-2.5 py-1" style="border-radius: 8px; font-size: 12px;" onclick="copyContactText('<?php echo htmlspecialchars(explode(',', $b['toll_free'])[0]); ?>');">
                                <i class="far fa-copy mr-1"></i> Copy Care No
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results Fallback -->
        <div id="noResultsAlert" class="bg-white rounded-xl shadow-sm border py-5 text-center mb-4" style="border-radius: 20px; display: none;">
            <div class="mx-auto mb-3 text-muted" style="width: 54px; height: 54px; background: #f1f5f9; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center;">
                <i class="fas fa-search fa-2x"></i>
            </div>
            <h4 class="font-weight-bold text-dark mb-2">No Matching Banks Found</h4>
            <p class="text-muted mb-4" style="max-width: 480px; margin: 0 auto;">No bank matches your search keyword or selected category. Try searching with a different term.</p>
            <button type="button" class="btn btn-success font-weight-bold px-4" style="border-radius: 12px; background: #047857; border-color: #047857;" onclick="clearBankSearch();">Reset Search</button>
        </div>

        <!-- ======================================================================
             SEO CONTENT & ACCORDION FAQ SECTION
             ====================================================================== -->
        <div class="p-4 p-md-5 bg-white rounded-xl border shadow-sm mb-4" style="border-radius: 22px;">
            <div class="row">
                <div class="col-lg-7 col-12 mb-4 mb-lg-0">
                    <h3 class="font-weight-bold text-dark mb-3" style="font-size: 22px;">
                        Customer Care &amp; Grievance Redressal Mechanism in Indian Banks
                    </h3>
                    <p class="text-muted" style="line-height: 1.7; font-size: 14.5px;">
                        Every commercial, payment, and co-operative bank in India operates a dedicated <strong>3-Tier Grievance Redressal Mechanism</strong> under Reserve Bank of India (RBI) guidelines. If you experience unauthorized transactions, ATM cash dispensing failures, delayed NEFT/RTGS refunds, or credit card billing disputes:
                    </p>
                    
                    <div class="row mt-3">
                        <div class="col-sm-6 mb-3">
                            <div class="contact-spec-tile">
                                <span class="badge badge-primary px-2.5 py-1 rounded-pill font-weight-bold mb-2">Level 1</span>
                                <h6 class="font-weight-bold text-dark mb-1">Branch / Toll-Free Helpdesk</h6>
                                <p class="text-muted small mb-0">Call the 24x7 toll-free helpline or email customer care with transaction reference IDs.</p>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="contact-spec-tile">
                                <span class="badge badge-warning text-dark px-2.5 py-1 rounded-pill font-weight-bold mb-2">Level 2</span>
                                <h6 class="font-weight-bold text-dark mb-1">Principal Nodal Officer</h6>
                                <p class="text-muted small mb-0">Escalate unresolved tickets to the bank's zonal or central Grievance Redressal Nodal Officer.</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="contact-spec-tile" style="background: #f0fdf4; border-color: #bbf7d0;">
                                <span class="badge badge-success px-2.5 py-1 rounded-pill font-weight-bold mb-2">Level 3</span>
                                <h6 class="font-weight-bold text-dark mb-1"><i class="fas fa-balance-scale mr-1"></i> RBI Integrated Ombudsman Scheme (CMS)</h6>
                                <p class="text-muted small mb-0">If unresolved within 30 days, file an online complaint at <strong>cms.rbi.org.in</strong> or call RBI toll-free <strong>14448</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-12">
                    <h4 class="font-weight-bold text-dark mb-3" style="font-size: 19px;">
                        <i class="fas fa-question-circle text-success mr-1.5"></i> Frequently Asked Questions
                    </h4>

                    <div class="accordion" id="contactFaqAccordion">
                        <?php foreach ($faq_items as $fIndex => $faq): ?>
                            <div class="contact-faq-card">
                                <button class="faq-header-btn collapsed" type="button" data-toggle="collapse" data-target="#contactFaqCollapse<?php echo $fIndex; ?>" aria-expanded="false" aria-controls="contactFaqCollapse<?php echo $fIndex; ?>">
                                    <span><?php echo htmlspecialchars($faq['q']); ?></span>
                                    <i class="fas fa-chevron-down text-muted"></i>
                                </button>
                                <div id="contactFaqCollapse<?php echo $fIndex; ?>" class="collapse" data-parent="#contactFaqAccordion">
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

<!-- Floating Toast -->
<div id="contactToast" class="contact-toast">
    <i class="fas fa-check-circle text-success" style="font-size: 18px;"></i>
    <span id="contactToastMsg">Copied to clipboard!</span>
</div>

<?php require_once('includes/footer.php'); ?>

<!-- ==========================================================================
     INTERACTIVE FILTER & COPY SCRIPT
     ========================================================================== -->
<script>
let currentCategory = 'All';

function showToast(msg) {
    const toast = document.getElementById('contactToast');
    const toastMsg = document.getElementById('contactToastMsg');
    if (!toast) return;
    toastMsg.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2800);
}

function copyContactText(text) {
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
        prompt('Copy text:', text);
    }
    document.body.removeChild(ta);
}

function setCategoryFilter(cat, btn) {
    currentCategory = cat;
    document.querySelectorAll('.cat-filter-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    filterBankDirectory();
}

function filterBankDirectory() {
    const query = document.getElementById('liveBankSearch').value.toLowerCase().trim();
    const clearBtn = document.getElementById('clearSearchBtn');
    if (clearBtn) {
        clearBtn.style.display = query.length > 0 ? 'inline-block' : 'none';
    }

    const tableRows = document.querySelectorAll('#bankTableBody .bank-row');
    const mobileCards = document.querySelectorAll('#bankMobileContainer .bank-mobile-card');
    let visibleCount = 0;

    tableRows.forEach(row => {
        const cat = row.getAttribute('data-category');
        const searchData = row.getAttribute('data-search');
        const matchesCat = (currentCategory === 'All' || cat === currentCategory);
        const matchesQuery = (!query || searchData.indexOf(query) !== -1);

        if (matchesCat && matchesQuery) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    mobileCards.forEach(card => {
        const cat = card.getAttribute('data-category');
        const searchData = card.getAttribute('data-search');
        const matchesCat = (currentCategory === 'All' || cat === currentCategory);
        const matchesQuery = (!query || searchData.indexOf(query) !== -1);

        if (matchesCat && matchesQuery) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });

    // Update count badge
    const badge = document.getElementById('filteredCountBadge');
    if (badge) {
        badge.textContent = visibleCount + ' Banks';
    }

    // Toggle No Results
    const noResults = document.getElementById('noResultsAlert');
    if (noResults) {
        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

function clearBankSearch() {
    const input = document.getElementById('liveBankSearch');
    if (input) input.value = '';
    currentCategory = 'All';
    document.querySelectorAll('.cat-filter-btn').forEach(b => {
        b.classList.toggle('active', b.getAttribute('data-cat') === 'All');
    });
    filterBankDirectory();
}
</script>

</body>
</html>
