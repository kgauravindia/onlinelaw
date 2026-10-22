<?php 
/**
 * Online Law - 404 Error Page
 * Modernized 2026 UI with Smart Search & Quick Directory Navigation
 */
require_once('includes/config.php');
require_once('includes/function.php');

// Automatic Smart Routing: Check if 404 request matches a company name/CIN slug
$req_uri = $_SERVER['REQUEST_URI'] ?? '';
$req_path = parse_url($req_uri, PHP_URL_PATH);
$req_slug = basename(rtrim($req_path, '/'));

if (!empty($req_slug)) {
    $matched_comp = resolve_company_incorporation($req_slug);
    if ($matched_comp) {
        header("Location: " . company_url($matched_comp), true, 301);
        exit;
    }
}

// 404 Response Header
http_response_code(404);

$page_title = "404 - Page Not Found | Online Law";
$meta_desc = "The page you are looking for might have been moved, renamed, or is temporarily unavailable. Search across India's public directories for Bank IFSC, PIN codes, Companies, NIC codes, and Bare Acts.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-code: 'JetBrains Mono', monospace;
        }

        body {
            font-family: var(--font-main) !important;
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* Hero Banner */
        .err-hero-section {
            background-color: #0b1120 !important;
            background: radial-gradient(135% 100% at 50% 0%, #0b1120 0%, #1e1b4b 55%, #0f172a 100%) !important;
            padding: 60px 0 70px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .err-glow-orb {
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.22) 0%, transparent 70%);
            top: -100px;
            left: -80px;
            pointer-events: none;
            filter: blur(50px);
        }

        /* 404 Graphic Badge */
        .err-number-badge {
            font-size: clamp(80px, 14vw, 130px);
            font-weight: 900;
            font-family: var(--font-code);
            line-height: 1;
            letter-spacing: -3px;
            background: linear-gradient(135deg, #60a5fa 0%, #38bdf8 50%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
            text-shadow: 0 10px 30px rgba(56, 189, 248, 0.2);
        }

        /* Quick Discovery Cards */
        .tool-card-err {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 24px 20px;
            transition: all 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none !important;
        }
        .tool-card-err:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.1);
            border-color: #93c5fd;
        }
        .tool-icon-err {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

<?php require_once('includes/header.php'); ?>

<!-- Hero Section -->
<section class="err-hero-section">
    <div class="err-glow-orb"></div>
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-lg-9 col-12">
                <div class="err-number-badge">404</div>
                <h1 class="text-white mb-3 font-weight-bold" style="font-size: clamp(24px, 3.5vw, 36px);">
                    Objection! Record or Page Not Found
                </h1>
                <p class="text-white-50 mb-4" style="font-size: 16px; line-height: 1.7; max-width: 650px; margin: 0 auto 24px;">
                    The page or resource you requested is either out of jurisdiction or has been relocated. Search our public directories below to get right back on track.
                </p>
                <div class="d-inline-flex flex-wrap gap-2 justify-content-center" style="gap: 12px;">
                    <a href="<?php echo $base_url; ?>index" class="btn btn-primary font-weight-bold px-4 py-2.5" style="border-radius: 12px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none;">
                        <i class="fas fa-home mr-1.5"></i> Back to Homepage
                    </a>
                    <a href="<?php echo $base_url; ?>contact" class="btn btn-outline-light font-weight-bold px-4 py-2.5" style="border-radius: 12px;">
                        <i class="fas fa-headset mr-1.5"></i> Helpdesk &amp; Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Directory Jump Grid -->
<section class="py-5" style="background: #ffffff; padding: 60px 0 !important;">
    <div class="container">
        <div class="text-center mb-4 pb-2">
            <span class="text-primary font-weight-bold" style="font-size: 12.5px; letter-spacing: 1px; text-transform: uppercase;">Instant Navigation</span>
            <h2 class="mt-1 mb-2 font-weight-bold text-dark" style="font-size: 26px;">Explore India's Public Directories</h2>
            <p class="text-muted" style="font-size: 14.5px;">Choose from our verified lookup engines:</p>
        </div>

        <div class="row">
            <!-- Bank Finder -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo $base_url; ?>bank-finder" class="tool-card-err">
                    <div>
                        <div class="tool-icon-err" style="background: #ecfdf5; color: #047857;">
                            <i class="fas fa-landmark"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 17px;">Bank &amp; IFSC Finder</h5>
                        <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.5;">Search 181K+ bank branches via 4-step cascading dropdowns or IFSC codes.</p>
                    </div>
                    <div class="text-success font-weight-bold" style="font-size: 13.5px;">
                        Open Bank Finder <i class="fas fa-arrow-right ml-1"></i>
                    </div>
                </a>
            </div>

            <!-- PIN Code Finder -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo $base_url; ?>pincode-finder" class="tool-card-err">
                    <div>
                        <div class="tool-icon-err" style="background: #eff6ff; color: #2563eb;">
                            <i class="fas fa-map-pin"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 17px;">PIN Code Finder</h5>
                        <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.5;">Look up 155K+ India Post postal circles, districts, and post offices.</p>
                    </div>
                    <div class="text-primary font-weight-bold" style="font-size: 13.5px;">
                        Open PIN Finder <i class="fas fa-arrow-right ml-1"></i>
                    </div>
                </a>
            </div>

            <!-- Company Incorporation -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo $base_url; ?>company-incorporation" class="tool-card-err">
                    <div>
                        <div class="tool-icon-err" style="background: #fef3c7; color: #d97706;">
                            <i class="fas fa-building"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 17px;">Company Incorporation</h5>
                        <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.5;">Explore MCA company registries, CIN details, ROCs, and registered offices.</p>
                    </div>
                    <div class="font-weight-bold" style="color: #d97706; font-size: 13.5px;">
                        Search Companies <i class="fas fa-arrow-right ml-1"></i>
                    </div>
                </a>
            </div>

            <!-- NIC Codes -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo $base_url; ?>nic" class="tool-card-err">
                    <div>
                        <div class="tool-icon-err" style="background: #f3e8ff; color: #7c3aed;">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 17px;">NIC 2025 Activity Codes</h5>
                        <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.5;">Find business activity classifications for SPICe+, Udyam, and MCA filing.</p>
                    </div>
                    <div class="font-weight-bold" style="color: #7c3aed; font-size: 13.5px;">
                        Explore NIC Codes <i class="fas fa-arrow-right ml-1"></i>
                    </div>
                </a>
            </div>

            <!-- GST HSN / SAC -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo $base_url; ?>gst-hsn-sac" class="tool-card-err">
                    <div>
                        <div class="tool-icon-err" style="background: #e0f2fe; color: #0284c7;">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 17px;">GST HSN &amp; SAC Finder</h5>
                        <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.5;">Search tariff codes and GST rates for goods and services billing.</p>
                    </div>
                    <div class="font-weight-bold" style="color: #0284c7; font-size: 13.5px;">
                        Search HSN Codes <i class="fas fa-arrow-right ml-1"></i>
                    </div>
                </a>
            </div>

            <!-- Bare Acts -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo $base_url; ?>acts" class="tool-card-err">
                    <div>
                        <div class="tool-icon-err" style="background: #fee2e2; color: #dc2626;">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 17px;">Indian Bare Acts</h5>
                        <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.5;">Search Central Bare Acts, year of enactment, act numbers, and legal citations.</p>
                    </div>
                    <div class="font-weight-bold" style="color: #dc2626; font-size: 13.5px;">
                        Search Bare Acts <i class="fas fa-arrow-right ml-1"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>
</body>
</html>
