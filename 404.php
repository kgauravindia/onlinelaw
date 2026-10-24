<?php 
/**
 * Online Law - 404 Error Page
 * Modern 2026 UI with Smart Auto-Routing, Multi-Tool Search Console & Directory Navigation
 */
require_once('includes/config.php');
require_once('includes/function.php');

// Automatic Smart Routing: Check if 404 request matches known patterns
$req_uri = $_SERVER['REQUEST_URI'] ?? '';
$req_path = parse_url($req_uri, PHP_URL_PATH);
$req_slug = trim(basename(rtrim($req_path, '/')));

if (!empty($req_slug)) {
    // 1. PIN Code check (6 digits, e.g. 110001)
    if (preg_match('/^[1-9][0-9]{5}$/', $req_slug)) {
        header("Location: " . $base_url . "pincode?pincode=" . urlencode($req_slug), true, 301);
        exit;
    }

    // 2. Bank IFSC Code check (4 letters, 0, 6 alphanum, e.g. SBIN0000691)
    if (preg_match('/^[A-Za-z]{4}0[A-Za-z0-9]{6}$/', $req_slug)) {
        header("Location: " . $base_url . "bank-ifsc?ifsc=" . strtoupper(urlencode($req_slug)), true, 301);
        exit;
    }

    // 3. Bank MICR Code check (9 digits, e.g. 110002005)
    if (preg_match('/^[1-9][0-9]{8}$/', $req_slug)) {
        header("Location: " . $base_url . "bank-micr?micr=" . urlencode($req_slug), true, 301);
        exit;
    }

    // 4. Company CIN or Registered Name Slug check
    if (function_exists('resolve_company_incorporation') && function_exists('company_url')) {
        $matched_comp = resolve_company_incorporation($req_slug);
        if ($matched_comp) {
            header("Location: " . company_url($matched_comp), true, 301);
            exit;
        }
    }
}

// Set proper HTTP 404 response header
http_response_code(404);

$page_title = "404 - Page Not Found | Online Law";
$meta_desc = "The page you are looking for might have been moved, renamed, or is temporarily unavailable. Search across India's public directories for Bank IFSC, PIN codes, Companies, NIC codes, GST rates, and Bare Acts.";
$canonical_url = $base_url . "404";
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-code: 'JetBrains Mono', monospace;
            --err-primary: #2563eb;
            --err-indigo: #4f46e5;
            --err-slate-900: #0b1120;
        }

        body {
            font-family: var(--font-main) !important;
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* 404 Hero Section */
        .err-hero-banner {
            background-color: #0b1120 !important;
            background: radial-gradient(135% 100% at 50% 0%, #0b1120 0%, #1e1b4b 55%, #0f172a 100%) !important;
            padding: clamp(50px, 7vw, 85px) 0 clamp(40px, 6vw, 70px);
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .err-ambient-orb-1 {
            position: absolute;
            width: 550px;
            height: 550px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.22) 0%, transparent 70%);
            top: -120px;
            left: -100px;
            pointer-events: none;
            filter: blur(50px);
        }
        .err-ambient-orb-2 {
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.18) 0%, transparent 70%);
            bottom: -140px;
            right: -80px;
            pointer-events: none;
            filter: blur(60px);
        }

        /* 404 Big Numeric Typography */
        .err-number-badge {
            font-size: clamp(84px, 15vw, 140px);
            font-weight: 900;
            font-family: var(--font-code);
            line-height: 0.95;
            letter-spacing: -4px;
            background: linear-gradient(135deg, #93c5fd 0%, #38bdf8 45%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 14px;
            filter: drop-shadow(0 12px 28px rgba(56, 189, 248, 0.25));
            user-select: none;
        }

        /* Pill Badge */
        .err-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(30, 27, 75, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #93c5fd;
            padding: 6px 16px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 16px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
        }
        .err-pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #f43f5e;
            box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.7);
            animation: errPulse 2s infinite;
        }
        @keyframes errPulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(244, 63, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(244, 63, 94, 0); }
        }

        /* Embedded Search Console on 404 */
        .err-search-card {
            background: #ffffff;
            border-radius: 20px;
            padding: clamp(20px, 3.5vw, 32px);
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.15), 0 4px 16px rgba(15, 23, 42, 0.05);
            border: 1px solid #e2e8f0;
            margin-top: -36px;
            position: relative;
            z-index: 10;
        }

        /* Tool Switcher Pills */
        .err-tool-tabs {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .err-tab-pill {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
            padding: 8px 14px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            outline: none !important;
        }
        .err-tab-pill:hover {
            background: #eff6ff;
            color: var(--err-primary);
            border-color: #bfdbfe;
        }
        .err-tab-pill.active {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        /* Clean Search Input - NO overlapping icons */
        .err-search-input-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        .err-search-input-field {
            height: 54px;
            padding-left: 20px;
            padding-right: 45px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            font-size: 15px;
            font-weight: 600;
            background-color: #f8fafc;
            color: #0f172a;
            transition: all 0.2s ease;
            width: 100%;
        }
        .err-search-input-field:focus {
            background-color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
            outline: none;
        }
        .err-search-input-field::placeholder {
            color: #94a3b8;
            font-weight: 500;
            font-size: 14px;
        }
        .err-clear-btn {
            position: absolute;
            right: 14px;
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 14px;
            cursor: pointer;
            display: none;
            padding: 6px;
        }
        .err-clear-btn:hover {
            color: #475569;
        }

        .err-search-btn {
            height: 54px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
            transition: all 0.2s ease;
        }
        .err-search-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.4);
            color: #ffffff;
        }

        /* Popular Search Chips */
        .err-chip {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 4px 12px;
            border-radius: 6px;
            text-decoration: none !important;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .err-chip:hover {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
            transform: translateY(-1px);
        }

        /* Tool Directory Jump Grid */
        .err-dir-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 22px 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none !important;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .err-dir-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 30px -8px rgba(15, 23, 42, 0.1);
            border-color: #93c5fd;
        }
        .err-dir-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            margin-bottom: 14px;
        }
        .err-dir-badge {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 3px 8px;
            border-radius: 6px;
            position: absolute;
            top: 18px;
            right: 18px;
        }

        /* Troubleshooting Alert */
        .err-help-box {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #1e293b 100%);
            border-radius: 18px;
            padding: 32px 28px;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 767px) {
            .err-search-card {
                margin-top: -24px;
                padding: 18px 14px;
                border-radius: 16px;
            }
            .err-tab-pill {
                font-size: 12px;
                padding: 6px 11px;
            }
            .err-search-input-field {
                height: 48px;
                font-size: 14px;
            }
            .err-search-btn {
                height: 48px;
            }
        }
    </style>

<?php require_once('includes/header.php'); ?>

<!-- ==========================================================================
     HERO 404 BANNER
     ========================================================================== -->
<section class="err-hero-banner">
    <div class="err-ambient-orb-1"></div>
    <div class="err-ambient-orb-2"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-lg-10 col-12">

                <!-- Status Pill -->
                <div class="d-flex justify-content-center">
                    <div class="err-status-pill">
                        <span class="err-pulse-dot"></span>
                        <span>HTTP 404 &bull; RECORD NOT FOUND</span>
                    </div>
                </div>

                <!-- Big 404 Number -->
                <div class="err-number-badge">404</div>

                <!-- Headline -->
                <h1 class="text-white font-weight-bold mb-3" style="font-size: clamp(24px, 3.8vw, 38px); letter-spacing: -0.5px; line-height: 1.25;">
                    Objection! Page or Legal Record <span style="background: linear-gradient(135deg, #93c5fd 0%, #38bdf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Not Found</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-white-50 mb-4" style="font-size: 15.5px; line-height: 1.7; max-width: 680px; margin: 0 auto 28px;">
                    The resource or URL you requested is out of jurisdiction, has expired, or was relocated. Use our multi-directory search below or choose from verified public lookups.
                </p>

                <!-- Action Buttons -->
                <div class="d-inline-flex flex-wrap gap-2 justify-content-center" style="gap: 12px;">
                    <a href="<?php echo $base_url; ?>index" class="btn btn-primary font-weight-bold px-4 py-2.5" style="border-radius: 12px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);">
                        <i class="fas fa-home mr-1.5"></i> Back to Homepage
                    </a>
                    <a href="<?php echo $base_url; ?>contact" class="btn btn-outline-light font-weight-bold px-4 py-2.5" style="border-radius: 12px; border-color: rgba(255, 255, 255, 0.25);">
                        <i class="fas fa-headset mr-1.5"></i> Contact Support
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     MAIN CONTENT & SEARCH CONSOLE
     ========================================================================== -->
<div class="full-row py-4" style="background-color: #f8fafc; position: relative; z-index: 10;">
    <div class="container">

        <!-- Multi-Tool Embedded Search Console -->
        <div class="err-search-card mb-5">
            <div class="text-left mb-3">
                <span class="text-muted small font-weight-bold text-uppercase" style="letter-spacing: 0.8px;">Instant Directory Search</span>
                <h4 class="font-weight-bold text-dark mb-0" style="font-size: 19px;">Find Any Branch, PIN, Company or Code Immediately</h4>
            </div>

            <!-- Tool Selector Tabs -->
            <div class="err-tool-tabs" id="errToolTabs">
                <button type="button" class="err-tab-pill active" data-action="bank-finder" data-name="q" data-placeholder="Find Bank Branches by Bank, State, District &amp; Branch...">
                    <i class="fas fa-landmark text-primary"></i> Bank Finder
                </button>
                <button type="button" class="err-tab-pill" data-action="pincode-finder" data-name="q" data-placeholder="Find PIN Codes by State, District &amp; Region...">
                    <i class="fas fa-map-marked-alt text-info"></i> PIN Finder
                </button>
                <button type="button" class="err-tab-pill" data-action="bank-ifsc" data-name="ifsc" data-placeholder="Enter 11-digit IFSC code (e.g. SBIN0000691, HDFC0000240)...">
                    <i class="fas fa-university text-success"></i> Bank IFSC
                </button>
                <button type="button" class="err-tab-pill" data-action="pincode" data-name="pincode" data-placeholder="Enter 6-digit PIN code (e.g. 110001, 400001, 800001)...">
                    <i class="fas fa-map-pin text-danger"></i> PIN Code
                </button>
                <button type="button" class="err-tab-pill" data-action="company-incorporation" data-name="q" data-placeholder="Enter Company Name, CIN (e.g. U47713DC2026PTC474566), or LLP...">
                    <i class="fas fa-building text-warning"></i> Company (CIN)
                </button>
                <button type="button" class="err-tab-pill" data-action="nic" data-name="q" data-placeholder="Search 2,715 NIC activity codes or industries (e.g. Software, 62013, Hospital)...">
                    <i class="fas fa-sitemap" style="color: #7c3aed;"></i> NIC 2025
                </button>
                <button type="button" class="err-tab-pill" data-action="gst-hsn-sac" data-name="q" data-placeholder="Search 4-8 digit HSN/SAC code or product name (e.g. 1001, Rice, Software)...">
                    <i class="fas fa-file-invoice-dollar text-primary"></i> GST HSN/SAC
                </button>
                <button type="button" class="err-tab-pill" data-action="acts" data-name="q" data-placeholder="Search Bare Act Name (e.g. Companies Act, IPC, Evidence), Act No, or Year...">
                    <i class="fas fa-gavel text-danger"></i> Bare Acts
                </button>
                <button type="button" class="err-tab-pill" data-action="passport" data-name="q" data-placeholder="Search city, PSK, or Regional Passport Office (e.g. Patna, Delhi)...">
                    <i class="fas fa-passport text-info"></i> Passport Seva
                </button>
            </div>

            <!-- Search Form -->
            <form id="errSearchForm" method="GET" action="<?php echo $base_url; ?>bank-finder">
                <div class="row align-items-center">
                    <div class="col-lg-9 col-md-8 col-12 mb-3 mb-md-0">
                        <div class="err-search-input-group">
                            <input type="text" id="errSearchInput" name="q" class="err-search-input-field" placeholder="Find Bank Branches by Bank, State, District &amp; Branch..." required autocomplete="off">
                            <button type="button" id="errClearBtn" class="err-clear-btn" title="Clear input"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-12">
                        <button type="submit" class="btn btn-block err-search-btn d-flex align-items-center justify-content-center">
                            <i class="fas fa-search mr-2"></i> Instant Lookup
                        </button>
                    </div>
                </div>
            </form>

            <!-- Popular Suggestions -->
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top" style="gap: 8px;">
                <span class="small text-muted font-weight-bold mr-1"><i class="fas fa-bolt text-warning mr-1"></i> Popular Searches:</span>
                <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=SBIN0000691" class="err-chip">SBI New Delhi IFSC</a>
                <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=HDFC0000240" class="err-chip">HDFC Mumbai IFSC</a>
                <a href="<?php echo $base_url; ?>pincode?pincode=110001" class="err-chip">PIN 110001 (Connaught Place)</a>
                <a href="<?php echo $base_url; ?>nic?code=62013" class="err-chip">NIC 62013 (Software)</a>
                <a href="<?php echo $base_url; ?>acts?q=Companies+Act" class="err-chip">Companies Act</a>
                <a href="<?php echo $base_url; ?>gst-hsn-sac?q=Rice" class="err-chip">Rice HSN Code</a>
            </div>
        </div>

        <!-- Section Heading for Directory Exploration -->
        <div class="text-center mb-4 pb-1">
            <span class="text-primary font-weight-bold text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Official Portals</span>
            <h2 class="font-weight-bold text-dark mt-1 mb-2" style="font-size: clamp(22px, 3vw, 28px);">
                Explore India's Public Legal &amp; Financial Directories
            </h2>
            <p class="text-muted" style="font-size: 14.5px; max-width: 600px; margin: 0 auto;">
                Jump directly to verified registry databases updated regularly:
            </p>
        </div>

        <!-- Directory Exploration Grid -->
        <div class="row">
            <!-- 1. Bank Finder -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo $base_url; ?>bank-finder" class="err-dir-card">
                    <div>
                        <span class="err-dir-badge" style="background: #ecfdf5; color: #047857;">181K+ Branches</span>
                        <div class="err-dir-icon" style="background: #ecfdf5; color: #047857;">
                            <i class="fas fa-landmark"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 17px;">Bank &amp; IFSC Finder</h5>
                        <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.55;">Find bank branches via 4-step cascading dropdowns or search 11-digit IFSC and 9-digit MICR codes.</p>
                    </div>
                    <div class="font-weight-bold" style="color: #047857; font-size: 13.5px;">
                        Open Bank Finder <i class="fas fa-arrow-right ml-1"></i>
                    </div>
                </a>
            </div>

            <!-- 2. PIN Code Finder -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo $base_url; ?>pincode-finder" class="err-dir-card">
                    <div>
                        <span class="err-dir-badge" style="background: #eff6ff; color: #2563eb;">155K+ Locations</span>
                        <div class="err-dir-icon" style="background: #eff6ff; color: #2563eb;">
                            <i class="fas fa-map-pin"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 17px;">PIN Code Directory</h5>
                        <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.55;">Lookup India Post circles, districts, talukas, and delivery post offices by digit or locality name.</p>
                    </div>
                    <div class="font-weight-bold" style="color: #2563eb; font-size: 13.5px;">
                        Open PIN Directory <i class="fas fa-arrow-right ml-1"></i>
                    </div>
                </a>
            </div>

            <!-- 3. Company Incorporation -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo $base_url; ?>company-incorporation" class="err-dir-card">
                    <div>
                        <span class="err-dir-badge" style="background: #fef3c7; color: #d97706;">3.1M+ Records</span>
                        <div class="err-dir-icon" style="background: #fef3c7; color: #d97706;">
                            <i class="fas fa-building"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 17px;">Company Incorporation</h5>
                        <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.55;">Explore MCA registries, 21-digit CINs, ROC offices, authorized capital, and registration dates.</p>
                    </div>
                    <div class="font-weight-bold" style="color: #d97706; font-size: 13.5px;">
                        Explore Companies <i class="fas fa-arrow-right ml-1"></i>
                    </div>
                </a>
            </div>

            <!-- 4. NIC 2025 Activity Codes -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo $base_url; ?>nic" class="err-dir-card">
                    <div>
                        <span class="err-dir-badge" style="background: #f3e8ff; color: #7c3aed;">2,715 Codes</span>
                        <div class="err-dir-icon" style="background: #f3e8ff; color: #7c3aed;">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 17px;">NIC 2025 Activity Codes</h5>
                        <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.55;">Search 5 classification tiers from Sections to 5-digit Sub-classes for SPICe+, Udyam, and GST.</p>
                    </div>
                    <div class="font-weight-bold" style="color: #7c3aed; font-size: 13.5px;">
                        Explore NIC Codes <i class="fas fa-arrow-right ml-1"></i>
                    </div>
                </a>
            </div>

            <!-- 5. GST HSN & SAC Finder -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo $base_url; ?>gst-hsn-sac" class="err-dir-card">
                    <div>
                        <span class="err-dir-badge" style="background: #e0f2fe; color: #0284c7;">22K+ Codes</span>
                        <div class="err-dir-icon" style="background: #e0f2fe; color: #0284c7;">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 17px;">GST HSN &amp; SAC Finder</h5>
                        <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.55;">Search Harmonized System Nomenclature (HSN) and Service Accounting Codes (SAC) with GST rates.</p>
                    </div>
                    <div class="font-weight-bold" style="color: #0284c7; font-size: 13.5px;">
                        Search HSN &amp; SAC <i class="fas fa-arrow-right ml-1"></i>
                    </div>
                </a>
            </div>

            <!-- 6. Bare Acts Directory -->
            <div class="col-lg-4 col-md-6 mb-4">
                <a href="<?php echo $base_url; ?>acts" class="err-dir-card">
                    <div>
                        <span class="err-dir-badge" style="background: #fee2e2; color: #dc2626;">Central Acts</span>
                        <div class="err-dir-icon" style="background: #fee2e2; color: #dc2626;">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 17px;">Indian Bare Acts</h5>
                        <p class="text-muted mb-3" style="font-size: 13.5px; line-height: 1.55;">Browse India's Central Bare Acts, year of enactment, act numbers, and legal citations.</p>
                    </div>
                    <div class="font-weight-bold" style="color: #dc2626; font-size: 13.5px;">
                        Browse Bare Acts <i class="fas fa-arrow-right ml-1"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- Helpdesk & API Information Banner -->
        <div class="err-help-box mt-3 mb-4">
            <div class="row align-items-center">
                <div class="col-lg-8 col-12 mb-3 mb-lg-0">
                    <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill mb-2" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); font-size: 12px; font-weight: 700; color: #93c5fd;">
                        <i class="fas fa-life-ring mr-1.5"></i> NEED ASSISTANCE?
                    </div>
                    <h4 class="text-white font-weight-bold mb-1" style="font-size: 20px;">Can't find what you're looking for?</h4>
                    <p class="text-white-50 mb-0" style="font-size: 14px; line-height: 1.6;">
                        If you believe a record should be here or experienced a broken link, notify our technical team or explore our programmatic JSON API endpoints.
                    </p>
                </div>
                <div class="col-lg-4 col-12 text-lg-right">
                    <a href="<?php echo $base_url; ?>contact" class="btn btn-light font-weight-bold px-3 py-2 mr-2" style="border-radius: 10px; font-size: 13.5px;">
                        <i class="fas fa-envelope mr-1"></i> Contact Desk
                    </a>
                    <a href="<?php echo $base_url; ?>api" class="btn btn-outline-light font-weight-bold px-3 py-2" style="border-radius: 10px; font-size: 13.5px; border-color: rgba(255, 255, 255, 0.3);">
                        <i class="fas fa-code mr-1"></i> API Docs
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Interactive Tool Switcher on 404 Page
document.addEventListener('DOMContentLoaded', function() {
    var tabs = document.querySelectorAll('#errToolTabs .err-tab-pill');
    var form = document.getElementById('errSearchForm');
    var input = document.getElementById('errSearchInput');
    var clearBtn = document.getElementById('errClearBtn');
    var baseUrl = '<?php echo $base_url; ?>';

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');

            var action = this.getAttribute('data-action');
            var paramName = this.getAttribute('data-name');
            var placeholder = this.getAttribute('data-placeholder');

            form.setAttribute('action', baseUrl + action);
            input.setAttribute('name', paramName);
            input.setAttribute('placeholder', placeholder);
            input.value = '';
            if (clearBtn) clearBtn.style.display = 'none';
            input.focus();
        });
    });

    if (input && clearBtn) {
        input.addEventListener('input', function() {
            if (this.value.trim().length > 0) {
                clearBtn.style.display = 'block';
            } else {
                clearBtn.style.display = 'none';
            }
        });

        clearBtn.addEventListener('click', function() {
            input.value = '';
            clearBtn.style.display = 'none';
            input.focus();
        });
    }
});
</script>

<?php require_once('includes/footer.php'); ?>
