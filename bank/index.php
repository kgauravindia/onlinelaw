<?php
    // Set include path to root directory so that relative requires work correctly
    set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__DIR__));
    require_once('includes/config.php');

    // Strip subdirectory from base_url if needed
    if (isset($base_url)) {
        $base_url = preg_replace('/bank\/$/', '', $base_url);
    }

    require_once('includes/function.php'); 
    
    $page_title = "Indian Banks IFSC & MICR Directory (1.8+ Lakh Branches 2026) | Online Law";
    $meta_desc = "Search official RBI Bank IFSC codes, MICR codes, branch addresses, and customer care details across all Indian banks. Fast cascading Bank Finder, IFSC lookup, and branch locator.";
    $meta_keywords = "bank directory, ifsc search, bank finder, find micr code, bank branch list india, rbi bank code, ifsc code lookup, sbi ifsc, hdfc ifsc, icici ifsc";
    $canonical_url = $base_url . "bank";

    // Schema.org Structured Data
    $schema_graphs = [];
    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "Bank Directory", "item" => $canonical_url]
        ]
    ];
    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "WebSite",
        "name" => "Online Law Bank Directory",
        "url" => $canonical_url,
        "potentialAction" => [
            "@type" => "SearchAction",
            "target" => $base_url . "bank-ifsc?ifsc={search_term_string}",
            "query-input" => "required name=search_term_string"
        ]
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
        --bank-primary: #047857;
        --bank-primary-dark: #064e3b;
        --bank-accent: #10b981;
        --bank-surface: #f8fafc;
    }

    /* Hero Banner */
    .hero-bank-index-banner {
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
    .bank-index-console-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.8);
        position: relative;
        margin-top: -36px;
        z-index: 20;
    }

    /* Search Tab Navs */
    .nav-tabs-bank {
        border-bottom: 2px solid #e2e8f0;
        gap: 8px;
    }
    .nav-tabs-bank .nav-link {
        border: none;
        border-radius: 12px 12px 0 0;
        color: #475569;
        font-weight: 600;
        font-size: 14.5px;
        padding: 12px 20px;
        background: transparent;
        transition: all 0.2s ease;
    }
    .nav-tabs-bank .nav-link:hover {
        color: var(--bank-primary);
        background: #f8fafc;
    }
    .nav-tabs-bank .nav-link.active {
        color: var(--bank-primary);
        background: #ffffff;
        border-bottom: 3px solid var(--bank-primary);
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.02);
    }

    /* Feature Service Card */
    .bank-feature-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px;
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .bank-feature-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 25px -5px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }
    .bank-feature-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 16px;
    }

    /* Popular Bank Card */
    .popular-bank-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px;
        text-align: center;
        transition: all 0.2s ease;
        text-decoration: none !important;
        display: block;
        height: 100%;
    }
    .popular-bank-card:hover {
        transform: translateY(-3px);
        border-color: var(--bank-accent);
        box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.15);
    }

    @media (max-width: 767px) {
        .bank-index-console-card {
            margin-top: -24px;
            padding: 18px 14px !important;
            border-radius: 18px !important;
        }
        .nav-tabs-bank .nav-link {
            padding: 10px 12px;
            font-size: 13px;
        }
    }
</style>

<!-- ==========================================================================
     HERO MODERN BANNER SECTION
     ========================================================================== -->
<section class="hero-bank-index-banner">
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
                        <span class="text-white font-weight-bold">Bank Directory</span>
                    </nav>
                </div>

                <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill mb-3" style="background: rgba(6, 78, 59, 0.85); border: 1px solid rgba(255, 255, 255, 0.2); color: #86efac; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">
                    <i class="fas fa-university mr-1.5"></i> RBI MASTER BANKING DIRECTORY 2026
                </div>

                <h1 class="text-white font-weight-bold mb-2" style="font-size: clamp(24px, 3.8vw, 36px); letter-spacing: -0.5px; line-height: 1.25;">
                    Indian Banks <span style="background: linear-gradient(135deg, #86efac 0%, #38bdf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">IFSC &amp; Branch Directory</span>
                </h1>
                <p class="text-light mb-0" style="color: #cbd5e1 !important; font-size: 15px; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Lookup verified RBI IFSC codes, MICR codes, branch addresses, emergency card hotlines, and balance inquiry numbers across 181,900+ bank branches in India.
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
        <div class="bank-index-console-card p-4 p-md-4 mb-5">
            
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <div>
                    <h3 class="font-weight-bold text-dark mb-1" style="font-size: 20px;">
                        <i class="fas fa-search-dollar text-success mr-1.5"></i> Find Bank Codes &amp; Branches
                    </h3>
                    <p class="text-muted small mb-0">Select your search method to find verified IFSC, MICR, or branch details.</p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <span class="badge badge-success px-3 py-1.5 font-weight-bold" style="background: #ecfdf5; color: #047857; font-size: 12px; border: 1px solid #a7f3d0; border-radius: 20px;">
                        <i class="fas fa-check-circle mr-1"></i> 181,913 Branches Indexed
                    </span>
                </div>
            </div>

            <!-- Search Tabs Navigation -->
            <ul class="nav nav-tabs nav-tabs-bank mb-4" id="bankSearchTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-finder" data-toggle="tab" href="#pane-finder" role="tab">
                        <i class="fas fa-landmark mr-1.5 text-success"></i> Cascading Bank Finder
                        <span class="badge badge-success ml-1" style="font-size: 10px; background: #047857;">NEW</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-ifsc" data-toggle="tab" href="#pane-ifsc" role="tab">
                        <i class="fas fa-university mr-1.5 text-primary"></i> Search by IFSC Code
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-branch" data-toggle="tab" href="#pane-branch" role="tab">
                        <i class="fas fa-search-location mr-1.5 text-warning"></i> Search by Branch Name
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-micr" data-toggle="tab" href="#pane-micr" role="tab">
                        <i class="fas fa-money-check-alt mr-1.5 text-info"></i> Search by MICR Code
                    </a>
                </li>
            </ul>

            <!-- Search Tabs Content Panes -->
            <div class="tab-content" id="bankSearchTabsContent">
                
                <!-- Pane 1: Cascading Bank Finder Promotion -->
                <div class="tab-pane fade show active py-2" id="pane-finder" role="tabpanel">
                    <div class="p-4 rounded-xl border bg-light d-flex flex-column flex-md-row align-items-center justify-content-between" style="border-radius: 18px;">
                        <div class="mb-3 mb-md-0 pr-md-4">
                            <h4 class="font-weight-bold text-dark mb-1" style="font-size: 18px;">
                                4-Step Cascading Bank &amp; Branch Finder
                            </h4>
                            <p class="text-muted small mb-0" style="line-height: 1.6;">
                                Select your <strong>Bank</strong> &rarr; <strong>State</strong> &rarr; <strong>District / City</strong> &rarr; <strong>Branch</strong> to get verified 11-digit IFSC, MICR, address, and settlement capabilities.
                            </p>
                        </div>
                        <a href="<?php echo $base_url; ?>bank-finder" class="btn btn-success font-weight-bold px-4 py-2.5 text-nowrap d-inline-flex align-items-center" style="border-radius: 12px; background: #047857; border-color: #047857;">
                            Launch Bank Finder <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Pane 2: Search by IFSC Code -->
                <div class="tab-pane fade py-2" id="pane-ifsc" role="tabpanel">
                    <form method="GET" action="<?php echo $base_url; ?>bank-ifsc">
                        <div class="row justify-content-center">
                            <div class="col-lg-8 col-md-9 mb-3 mb-md-0">
                                <div class="position-relative">
                                    <i class="fas fa-university position-absolute text-muted" style="top: 50%; transform: translateY(-50%); left: 18px; font-size: 16px;"></i>
                                    <input class="form-control text-uppercase" style="padding-left: 48px; height: 52px; border-radius: 14px; font-weight: 600; letter-spacing: 0.5px; border: 1.5px solid #e2e8f0;" type="text" placeholder="Enter 11-digit IFSC (e.g. SBIN0000691, HDFC0000240)" maxlength="11" minlength="11" name="ifsc" required>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <button type="submit" class="btn btn-success font-weight-bold btn-block d-flex align-items-center justify-content-center" style="height: 52px; border-radius: 14px; background: #047857; border-color: #047857;">
                                    <i class="fas fa-search mr-2"></i> Search IFSC
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Pane 3: Search by Branch Name -->
                <div class="tab-pane fade py-2" id="pane-branch" role="tabpanel">
                    <form method="GET" action="<?php echo $base_url; ?>bank-ifsc-by-branch-name">
                        <div class="row justify-content-center">
                            <div class="col-lg-8 col-md-9 mb-3 mb-md-0">
                                <div class="position-relative">
                                    <i class="fas fa-building position-absolute text-muted" style="top: 50%; transform: translateY(-50%); left: 18px; font-size: 16px;"></i>
                                    <input class="form-control" style="padding-left: 48px; height: 52px; border-radius: 14px; border: 1.5px solid #e2e8f0;" type="text" placeholder="Enter Branch Name or City (e.g. Connaught Place, Andheri East)" maxlength="50" minlength="3" name="branch" required>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <button type="submit" class="btn btn-success font-weight-bold btn-block d-flex align-items-center justify-content-center" style="height: 52px; border-radius: 14px; background: #047857; border-color: #047857;">
                                    <i class="fas fa-search mr-2"></i> Search Branch
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Pane 4: Search by MICR Code -->
                <div class="tab-pane fade py-2" id="pane-micr" role="tabpanel">
                    <form method="GET" action="<?php echo $base_url; ?>bank-micr">
                        <div class="row justify-content-center">
                            <div class="col-lg-8 col-md-9 mb-3 mb-md-0">
                                <div class="position-relative">
                                    <i class="fas fa-money-check-alt position-absolute text-muted" style="top: 50%; transform: translateY(-50%); left: 18px; font-size: 16px;"></i>
                                    <input class="form-control text-uppercase" style="padding-left: 48px; height: 52px; border-radius: 14px; font-weight: 600; letter-spacing: 0.5px; border: 1.5px solid #e2e8f0;" type="text" placeholder="Enter 9-digit MICR Code (e.g. 110002001, 400002010)" maxlength="9" minlength="9" name="micr" required>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <button type="submit" class="btn btn-success font-weight-bold btn-block d-flex align-items-center justify-content-center" style="height: 52px; border-radius: 14px; background: #047857; border-color: #047857;">
                                    <i class="fas fa-search mr-2"></i> Search MICR
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>

        <!-- ======================================================================
             POPULAR BANKS QUICK ACCESS SECTION
             ====================================================================== -->
        <div class="p-4 bg-white rounded-xl border shadow-sm mb-5" style="border-radius: 22px;">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <div>
                    <h3 class="font-weight-bold text-dark mb-1" style="font-size: 20px;">
                        <i class="fas fa-university text-success mr-1.5"></i> Popular Banks Directory
                    </h3>
                    <p class="text-muted small mb-0">Select any bank to instantly explore branch codes, IFSC numbers, and locations.</p>
                </div>
                <a href="<?php echo $base_url; ?>bank-finder" class="btn btn-sm btn-outline-success font-weight-bold px-3 py-1.5" style="border-radius: 10px;">
                    View All 200+ Banks &rarr;
                </a>
            </div>

            <div class="row">
                <?php
                $popular_banks = [
                    ['name' => 'STATE BANK OF INDIA', 'short' => 'SBI', 'branches' => '25,600+ Branches', 'color' => '#0284c7'],
                    ['name' => 'PUNJAB NATIONAL BANK', 'short' => 'PNB', 'branches' => '13,000+ Branches', 'color' => '#d97706'],
                    ['name' => 'CANARA BANK', 'short' => 'CANARA', 'branches' => '12,100+ Branches', 'color' => '#2563eb'],
                    ['name' => 'UNION BANK OF INDIA', 'short' => 'UNION', 'branches' => '11,100+ Branches', 'color' => '#059669'],
                    ['name' => 'HDFC BANK', 'short' => 'HDFC', 'branches' => '10,900+ Branches', 'color' => '#dc2626'],
                    ['name' => 'BANK OF BARODA', 'short' => 'BOB', 'branches' => '10,100+ Branches', 'color' => '#ea580c'],
                    ['name' => 'ICICI BANK LIMITED', 'short' => 'ICICI', 'branches' => '8,300+ Branches', 'color' => '#b91c1c'],
                    ['name' => 'BANK OF INDIA', 'short' => 'BOI', 'branches' => '7,100+ Branches', 'color' => '#0284c7'],
                    ['name' => 'AXIS BANK', 'short' => 'AXIS', 'branches' => '7,000+ Branches', 'color' => '#9333ea'],
                    ['name' => 'INDIAN BANK', 'short' => 'INDIAN', 'branches' => '6,000+ Branches', 'color' => '#2563eb'],
                    ['name' => 'CENTRAL BANK OF INDIA', 'short' => 'CBI', 'branches' => '4,700+ Branches', 'color' => '#059669'],
                    ['name' => 'KOTAK MAHINDRA BANK LIMITED', 'short' => 'KOTAK', 'branches' => '1,800+ Branches', 'color' => '#dc2626']
                ];
                foreach($popular_banks as $bank):
                ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <a href="<?php echo $base_url; ?>bank-finder?bank=<?php echo urlencode($bank['name']); ?>" class="popular-bank-card">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white mr-2.5" style="width: 32px; height: 32px; background: <?php echo $bank['color']; ?>; font-size: 13px; font-weight: 700;">
                                    <?php echo substr($bank['short'], 0, 1); ?>
                                </div>
                                <div class="text-left text-truncate">
                                    <h6 class="font-weight-bold text-dark mb-0 text-truncate" style="font-size: 14px;" title="<?php echo htmlspecialchars($bank['name']); ?>">
                                        <?php echo htmlspecialchars($bank['name']); ?>
                                    </h6>
                                    <small class="text-muted font-weight-medium" style="font-size: 11px;"><?php echo $bank['branches']; ?></small>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ======================================================================
             BANKING SERVICES & GUIDES CARDS
             ====================================================================== -->
        <div class="row mb-5">
            <div class="col-lg-4 col-md-6 col-12 mb-4">
                <div class="bank-feature-card">
                    <div>
                        <div class="bank-feature-icon" style="background: #ecfdf5; color: #047857;">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2" style="font-size: 18px;">Official Bank Contacts</h4>
                        <p class="text-muted small mb-3" style="line-height: 1.6;">
                            Access verified toll-free customer care numbers, head office addresses, official portals, and support emails for 250+ banks.
                        </p>
                    </div>
                    <a href="<?php echo $base_url; ?>bank/contact-details" class="font-weight-bold text-success text-decoration-none small">
                        View Bank Contacts Directory &rarr;
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-12 mb-4">
                <div class="bank-feature-card">
                    <div>
                        <div class="bank-feature-icon" style="background: #eff6ff; color: #2563eb;">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2" style="font-size: 18px;">Card Helplines &amp; Safety</h4>
                        <p class="text-muted small mb-3" style="line-height: 1.6;">
                            Emergency 24x7 hotlines to immediately block lost, stolen, or compromised debit and credit cards across all major banks.
                        </p>
                    </div>
                    <a href="<?php echo $base_url; ?>bank/card" class="font-weight-bold text-primary text-decoration-none small">
                        View Card Hotlines &rarr;
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-12 col-12 mb-4">
                <div class="bank-feature-card">
                    <div>
                        <div class="bank-feature-icon" style="background: #faf5ff; color: #7c3aed;">
                            <i class="fas fa-phone-volume"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2" style="font-size: 18px;">Balance Check Numbers</h4>
                        <p class="text-muted small mb-3" style="line-height: 1.6;">
                            Official missed-call banking numbers and SMS codes to instantly check account balance and receive mini-statements.
                        </p>
                    </div>
                    <a href="<?php echo $base_url; ?>bank/account" class="font-weight-bold text-purple text-decoration-none small" style="color: #7c3aed;">
                        View Missed-Call Numbers &rarr;
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once('includes/footer.php'); ?>

</body>
</html>
