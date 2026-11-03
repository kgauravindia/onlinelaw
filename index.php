<?php 
/**
 * Online Law - Homepage
 * Modernized 2026 UI with Multi-Tool Instant Search & Platform Ecosystem
 */
require_once('includes/config.php');
require_once('includes/function.php');

$page_title = "Online Law | Free Bank IFSC, PIN Code, Company Incorporation, GST HSN & Passport Finder India (Updated 2026)";
$meta_desc = "India's leading digital public legal & directory platform. Search 155K+ PIN Codes, 181K+ Bank IFSC & MICR codes, 3.1M+ MCA Company Incorporations, 2,715 NIC 2025 activity codes, 22K+ GST HSN/SAC codes, and 784 Passport Offices instantly. 100% free for every citizen.";
$meta_keywords = "bank ifsc finder, pin code lookup india, mca company incorporation, gst hsn sac finder, passport seva office locator, bank micr search, nic 2025 codes, indian postal index number directory, bare acts india";
$canonical_url = $base_url . "index";

// Schema.org WebSite SearchAction Structured Data
$website_schema = [
    "@context" => "https://schema.org",
    "@type" => "WebSite",
    "name" => "Online Law",
    "url" => $base_url,
    "description" => $meta_desc,
    "potentialAction" => [
        "@type" => "SearchAction",
        "target" => [
            "@type" => "EntryPoint",
            "urlTemplate" => $base_url . "pincode?pincode={search_term_string}"
        ],
        "query-input" => "required name=search_term_string"
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

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    <?php echo json_encode($website_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>

    <style>
        /* Hero Banner Custom Ambient Overlays (Indian Flag Tricolor Theme) */
        .hero-modern-banner {
            position: relative;
            background: radial-gradient(135% 100% at 50% 0%, #071329 0%, #0b1e3d 45%, #071329 100%);
            padding: clamp(65px, 8vw, 115px) 0 clamp(80px, 9vw, 130px) 0;
            overflow: hidden;
        }
        .hero-ambient-orb-1 {
            position: absolute;
            top: -100px;
            left: 10%;
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, rgba(255, 103, 31, 0.22) 0%, rgba(255, 103, 31, 0) 70%);
            border-radius: 50%;
            filter: blur(50px);
            pointer-events: none;
            animation: orbFloat1 8s ease-in-out infinite alternate;
        }
        .hero-ambient-orb-2 {
            position: absolute;
            bottom: -50px;
            right: 10%;
            width: 460px;
            height: 460px;
            background: radial-gradient(circle, rgba(4, 106, 56, 0.22) 0%, rgba(4, 106, 56, 0) 70%);
            border-radius: 50%;
            filter: blur(50px);
            pointer-events: none;
            animation: orbFloat2 9s ease-in-out infinite alternate;
        }
        @keyframes orbFloat1 {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(25px) scale(1.08); }
        }
        @keyframes orbFloat2 {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(-25px) scale(1.06); }
        }

        /* Hero Heading & Gradient Text */
        .hero-main-heading {
            font-size: clamp(32px, 5vw, 54px) !important;
            font-weight: 800 !important;
            color: #ffffff !important;
            line-height: 1.16 !important;
            letter-spacing: -0.03em;
            margin-top: 15px;
            margin-bottom: 16px;
        }
        .hero-radiant-gradient {
            background: linear-gradient(135deg, #ffa500 0%, #ff8800 45%, #ffffff 75%, #34d399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        /* Hero Search Card Console */
        .hero-search-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 22px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.15) inset;
            padding: 24px 26px;
            margin-top: 32px;
            transition: var(--transition-smooth);
        }
        .hero-search-card:focus-within {
            box-shadow: 0 30px 70px -12px rgba(255, 103, 31, 0.28), 0 0 0 2px rgba(255, 103, 31, 0.4);
        }

        /* Search Tabs Navigation */
        .hero-tab-pill {
            background: #f1f5f9;
            color: #475569;
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 13px;
            padding: 8px 16px;
            border-radius: var(--radius-pill);
            border: 1px solid #e2e8f0;
            cursor: pointer;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
            user-select: none;
        }
        .hero-tab-pill:hover {
            background: #e2e8f0;
            color: var(--text-main);
            transform: translateY(-1px);
        }
        .hero-tab-pill.active {
            background: var(--primary-gradient);
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(255, 103, 31, 0.35);
            border-color: transparent;
        }

        /* Search Input Group */
        .hero-search-input-group {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 4px 14px;
            transition: var(--transition-smooth);
            position: relative;
        }
        .hero-search-input-group:focus-within {
            background: #ffffff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(255, 103, 31, 0.16);
        }
        .hero-search-input-group input {
            border: none !important;
            box-shadow: none !important;
            font-size: 15.5px;
            padding: 10px 10px;
            font-family: var(--font-body);
            color: var(--text-main);
            outline: none;
            flex: 1;
            background: transparent;
        }
        .hero-search-input-group input::placeholder {
            color: #94a3b8;
            font-size: 14.5px;
        }
        .hero-clear-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding: 4px 8px;
            font-size: 14px;
            cursor: pointer;
            display: none;
        }
        .hero-clear-btn:hover {
            color: #475569;
        }

        .hero-quick-tag {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 4px 12px;
            border-radius: 6px;
            text-decoration: none !important;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .hero-quick-tag:hover {
            background: var(--primary-light);
            border-color: rgba(37, 99, 235, 0.3);
            color: var(--primary-color);
            transform: translateY(-1px);
        }

        /* Floating Trust Strip */
        .trust-feature-strip {
            background: #ffffff;
            border-bottom: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            padding: 18px 0;
            position: relative;
            z-index: 10;
        }
        .trust-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .trust-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }
        .trust-item-title {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 14px;
            color: var(--text-main);
            margin-bottom: 1px;
        }
        .trust-item-desc {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.35;
        }

        /* Modern Tool Directory Cards */
        .features-section-wrap {
            padding: clamp(60px, 7vw, 90px) 0;
            background-color: var(--bg-body);
        }
        .modern-tool-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            padding: 30px 24px !important;
            transition: var(--transition-bounce);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .modern-tool-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 35px -8px rgba(15, 23, 42, 0.1);
            border-color: rgba(37, 99, 235, 0.35);
        }
        .tool-card-glow-bg {
            position: absolute;
            top: -30px;
            right: -30px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            opacity: 0.12;
            pointer-events: none;
            transition: var(--transition-smooth);
        }
        .modern-tool-card:hover .tool-card-glow-bg {
            transform: scale(1.4);
            opacity: 0.22;
        }
        .tool-icon-squircle {
            width: 58px;
            height: 58px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
            transition: var(--transition-bounce);
        }
        .modern-tool-card:hover .tool-icon-squircle {
            transform: scale(1.08) rotate(3deg);
        }
        .tool-card-badge {
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: var(--radius-pill);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .tool-card-title {
            font-size: 19px !important;
            font-weight: 700 !important;
            color: var(--text-main);
            margin-bottom: 8px;
            font-family: var(--font-heading);
        }
        .tool-card-desc {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
        }
        .tool-card-link {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 13.5px;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition-smooth);
        }
        .tool-card-link i {
            transition: transform 0.2s ease;
        }
        .modern-tool-card:hover .tool-card-link i {
            transform: translateX(4px);
        }

        /* Process Workflow Step Cards */
        .workflow-step-card {
            background: #ffffff;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-lg);
            padding: 32px 24px;
            height: 100%;
            transition: var(--transition-bounce);
            position: relative;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
        }
        .workflow-step-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(37, 99, 235, 0.3);
        }
        .step-number-pill {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--primary-light);
            color: var(--primary-color);
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
        }

        /* Citizen FAQ Accordion */
        .faq-accordion-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            margin-bottom: 12px;
            overflow: hidden;
            transition: var(--transition-smooth);
        }
        .faq-accordion-card:hover {
            border-color: rgba(37, 99, 235, 0.3);
        }
        .faq-accordion-btn {
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            padding: 18px 22px;
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 15.5px;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            cursor: pointer;
            outline: none !important;
        }
        .faq-accordion-btn i {
            color: var(--primary-color);
            font-size: 14px;
            transition: transform 0.3s ease;
        }
        .faq-accordion-btn[aria-expanded="true"] i {
            transform: rotate(180deg);
        }
        .faq-accordion-body {
            padding: 0 22px 20px 22px;
            color: var(--text-muted);
            font-size: 14.5px;
            line-height: 1.65;
        }

        /* CTA Developer Strip */
        .cta-developer-box {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #1e293b 100%);
            border-radius: 20px;
            padding: 40px 30px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .cta-ambient-glow {
            position: absolute;
            top: -50px;
            right: -50px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.3) 0%, rgba(99, 102, 241, 0) 70%);
            border-radius: 50%;
            filter: blur(40px);
            pointer-events: none;
        }
    </style>

<?php require_once ('includes/header.php'); ?> 

<!-- Hero Banner Section Start -->
<section class="hero-modern-banner position-relative">
	<div class="hero-ambient-orb-1"></div>
	<div class="hero-ambient-orb-2"></div>
	<div class="container position-relative" style="z-index: 2;">
		<div class="row justify-content-center">
			<div class="col-lg-10 col-12 text-center">
				
				<!-- Live Version Badge -->
				<div class="hero-pill-tag mb-3">
					<span class="hero-pulse-dot"></span>
					<span>OFFICIAL PUBLIC ACCESS DIRECTORY • UPDATED 2026</span>
				</div>
				
				<!-- Main Headline -->
				<h1 class="hero-main-heading">
					Democratizing Access to India's <br>
					<span class="hero-radiant-gradient">Public Legal, Civic &amp; Financial Data</span>
				</h1>
				
				<!-- Subtitle -->
				<p class="hero-subtext">
					Instant, zero-cost access to official records. Search 181K+ Bank IFSC codes, 155K+ PIN codes, 3.1M+ MCA Companies, 2,715 NIC 2025 activity codes, 22K+ GST HSN/SAC codes, and 784+ Passport Seva centres.
				</p>

				<!-- Multi-Tool Instant Search Console -->
				<div class="hero-search-card text-left">
					<!-- Tool Select Tabs -->
					<div class="d-flex flex-wrap align-items-center justify-content-start justify-content-md-center gap-2 mb-3 pb-2 border-bottom" style="gap: 8px;" id="heroToolTabs">
						<button type="button" class="hero-tab-pill active" data-action="bank-finder" data-name="q" data-placeholder="Find Bank Branches by Bank, State, District &amp; Branch..." data-icon="fas fa-landmark">
							<i class="fas fa-landmark"></i> Bank Finder
						</button>
						<button type="button" class="hero-tab-pill" data-action="pincode-finder" data-name="q" data-placeholder="Find PIN Codes by State, District &amp; Region..." data-icon="fas fa-map-marked-alt">
							<i class="fas fa-map-marked-alt"></i> PIN Finder
						</button>
						<button type="button" class="hero-tab-pill" data-action="bank-ifsc" data-name="ifsc" data-placeholder="Enter 11-digit IFSC code (e.g. SBIN0000691, HDFC0000001)..." data-icon="fas fa-university">
							<i class="fas fa-university"></i> Bank IFSC
						</button>
						<button type="button" class="hero-tab-pill" data-action="pincode" data-name="pincode" data-placeholder="Enter 6-digit PIN code (e.g. 110001, 841301)..." data-icon="fas fa-map-pin">
							<i class="fas fa-map-pin"></i> PIN Code
						</button>
						<button type="button" class="hero-tab-pill" data-action="company-incorporation" data-name="q" data-placeholder="Enter Company Name, CIN (e.g. U47713DC2026PTC474566), or LLP..." data-icon="fas fa-building">
							<i class="fas fa-building"></i> Company (CIN)
						</button>
						<button type="button" class="hero-tab-pill" data-action="nic" data-name="q" data-placeholder="Search 2,715 NIC activity codes or industries (e.g. Software, 47713, Hospital)..." data-icon="fas fa-sitemap">
							<i class="fas fa-sitemap"></i> NIC 2025 Codes
						</button>
						<button type="button" class="hero-tab-pill" data-action="gst-hsn-sac" data-name="q" data-placeholder="Search 4-8 digit HSN/SAC code or product name (e.g. 8471, Software)..." data-icon="fas fa-file-invoice-dollar">
							<i class="fas fa-file-invoice-dollar"></i> GST HSN/SAC
						</button>
						<button type="button" class="hero-tab-pill" data-action="passport" data-name="q" data-placeholder="Search city, PSK, or Regional Passport Office (e.g. Patna, Delhi)..." data-icon="fas fa-passport">
							<i class="fas fa-passport"></i> Passport Seva
						</button>
					</div>

					<!-- Search Input Form -->
					<form id="heroInstantSearchForm" method="GET" action="<?php echo $base_url; ?>bank-finder">
						<div class="row align-items-center">
							<div class="col-md-9 col-12 mb-2 mb-md-0">
								<div class="hero-search-input-group">
									<i id="heroSearchIcon" class="fas fa-landmark text-primary mr-2" style="font-size: 18px; width: 22px; text-align: center;"></i>
									<input type="text" id="heroSearchInput" name="q" placeholder="Find Bank Branches by Bank, State, District &amp; Branch..." required autocomplete="off">
									<button type="button" id="heroClearBtn" class="hero-clear-btn" title="Clear input"><i class="fas fa-times"></i></button>
								</div>
								<div id="heroFormatHint" class="hero-format-hint">
									<i class="fas fa-lightbulb text-warning mr-1"></i> <span id="heroFormatHintText">Tip: Select Bank, State, District &amp; Branch or enter branch name</span>
								</div>
							</div>
							<div class="col-md-3 col-12">
								<button type="submit" class="btn btn-primary btn-block font-weight-bold" style="height: 50px; border-radius: var(--radius-md); font-size: 15px; background: linear-gradient(135deg, #ff671f, #ea580c); border: none; box-shadow: 0 4px 15px rgba(255, 103, 31, 0.4);">
									<i class="fas fa-search mr-1"></i> Instant Lookup
								</button>
							</div>
						</div>
					</form>

					<!-- Recent Searches Container -->
					<div id="heroRecentContainer" class="hero-recent-container" style="display: none;">
						<span class="small text-muted font-weight-bold mr-1"><i class="fas fa-history text-secondary mr-1"></i> Recent:</span>
						<div id="heroRecentList" class="d-inline-flex flex-wrap gap-1" style="gap: 5px;"></div>
					</div>

					<!-- Quick Trending Suggestions -->
					<div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-2">
						<span class="small text-muted font-weight-bold mr-1"><i class="fas fa-bolt text-warning mr-1"></i> Trending:</span>
						<a href="<?php echo $base_url; ?>bank-finder" class="hero-quick-tag text-success font-weight-bold"><i class="fas fa-landmark"></i> Cascading Bank Finder</a>
						<a href="<?php echo $base_url; ?>pincode-finder" class="hero-quick-tag text-primary font-weight-bold"><i class="fas fa-map-marked-alt"></i> Cascading PIN Finder</a>
						<a href="<?php echo $base_url; ?>company-incorporation" class="hero-quick-tag"><i class="fas fa-building text-primary"></i> 2026 Incorporations</a>
						<a href="<?php echo $base_url; ?>nic?division=62" class="hero-quick-tag"><i class="fas fa-sitemap text-warning"></i> NIC Software (62)</a>
						<a href="<?php echo $base_url; ?>bank-ifsc?ifsc=SBIN0000691" class="hero-quick-tag"><i class="fas fa-university text-info"></i> SBI Main IFSC</a>
						<a href="<?php echo $base_url; ?>pincode?pincode=110001" class="hero-quick-tag"><i class="fas fa-map-pin text-danger"></i> PIN 110001 (New Delhi)</a>
					</div>
				</div>

			</div>
		</div>
	</div>
</section>
<!-- Hero Banner Section End --> 

<!-- Floating Trust & Transparency Strip -->
<div class="trust-feature-strip">
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-sm-6 col-12 mb-3 mb-lg-0">
				<div class="trust-item">
					<div class="trust-icon-box" style="background: #ecfdf5; color: #059669;">
						<i class="fas fa-unlock-alt"></i>
					</div>
					<div>
						<div class="trust-item-title">100% Free &amp; Open</div>
						<div class="trust-item-desc">No login, paywalls or captcha friction</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6 col-12 mb-3 mb-lg-0">
				<div class="trust-item">
					<div class="trust-icon-box" style="background: #fff7ed; color: #ff671f;">
						<i class="fas fa-shield-alt"></i>
					</div>
					<div>
						<div class="trust-item-title">Official Registry Data</div>
						<div class="trust-item-desc">Synced with RBI, MCA, India Post &amp; CBIC</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6 col-12 mb-3 mb-sm-0">
				<div class="trust-item">
					<div class="trust-icon-box" style="background: #fefce8; color: #ca8a04;">
						<i class="fas fa-bolt"></i>
					</div>
					<div>
						<div class="trust-item-title">Sub-Second Engine</div>
						<div class="trust-item-desc">Instant indexed database queries</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6 col-12">
				<div class="trust-item">
					<div class="trust-icon-box" style="background: #f5f3ff; color: #7c3aed;">
						<i class="fas fa-code"></i>
					</div>
					<div>
						<div class="trust-item-title">Developer JSON APIs</div>
						<div class="trust-item-desc">High-speed endpoints for fintech apps</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Features Grid -->
<section class="features-section-wrap">
	<div class="container">
		<div class="row text-center mb-5 justify-content-center">
			<div class="col-lg-8 col-12">
				<div class="badge badge-primary px-3 py-1 mb-2 rounded-pill font-weight-bold" style="background: var(--primary-light); color: var(--primary-color); font-size: 12px; letter-spacing: 1px;">INSTANT ESSENTIAL DIRECTORIES</div>
				<h2 class="section-title mb-2" style="font-size: clamp(28px, 4vw, 38px);">Rapid Public Lookups</h2>
				<p style="color: var(--text-muted); font-size: 15.5px;">Universally free digital lookups built with high precision from verified Indian government records.</p>
			</div>
		</div>
		<div class="row">
			
			<!-- Card 1: Cascading Bank Finder -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #059669;"></div>
					<span class="tool-card-badge" style="background: #ecfdf5; color: #059669;">181,913 Branches</span>
					<div>
						<div class="tool-icon-squircle" style="background: #ecfdf5; color: #059669;">
							<i class="fas fa-landmark"></i>
						</div>
						<h3 class="tool-card-title">Bank &amp; IFSC Finder</h3>
						<p class="tool-card-desc">4-step cascading dropdown lookup (Bank &rarr; State &rarr; District &rarr; Branch) or instant IFSC/MICR search for all Indian banks.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.8) !important;">
						<a href="<?php echo $base_url; ?>bank-finder" class="tool-card-link" style="color: #059669;">
							Launch Bank Finder <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>

			<!-- Card 2: Cascading PIN Code Finder -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #0284c7;"></div>
					<span class="tool-card-badge" style="background: #f0f9ff; color: #0284c7;">155,000+ PIN Codes</span>
					<div>
						<div class="tool-icon-squircle" style="background: #f0f9ff; color: #0284c7;">
							<i class="fas fa-map-marked-alt"></i>
						</div>
						<h3 class="tool-card-title">PIN Code Finder</h3>
						<p class="tool-card-desc">3-step cascading lookup (State &rarr; District &rarr; Region) across India Post circles, postal divisions, and local delivery post offices.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.8) !important;">
						<a href="<?php echo $base_url; ?>pincode-finder" class="tool-card-link" style="color: #0284c7;">
							Launch PIN Finder <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>

			<!-- Card 3: Company Incorporation -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #ff671f;"></div>
					<span class="tool-card-badge" style="background: #fff7ed; color: #ff671f;">3.1M+ Records</span>
					<div>
						<div class="tool-icon-squircle" style="background: #fff7ed; color: #ff671f;">
							<i class="fas fa-building"></i>
						</div>
						<h3 class="tool-card-title">Company Incorporations</h3>
						<p class="tool-card-desc">Search newly registered Indian Companies, LLPs, and Foreign Entities from official MCA daily incorporation reports.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.8) !important;">
						<a href="<?php echo $base_url; ?>company-incorporation" class="tool-card-link" style="color: #ff671f;">
							Search Companies <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>

			<!-- Card 4: NIC 2025 Activity Codes -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #eab308;"></div>
					<span class="tool-card-badge" style="background: #fefce8; color: #a16207;">2,715 MoSPI Codes</span>
					<div>
						<div class="tool-icon-squircle" style="background: #fefce8; color: #ca8a04;">
							<i class="fas fa-sitemap"></i>
						</div>
						<h3 class="tool-card-title">NIC 2025 Activity Codes</h3>
						<p class="tool-card-desc">Search official 2-digit to 5-digit National Industrial Classification codes for MCA CIN, MSME Udyam, and GST registration.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.8) !important;">
						<a href="<?php echo $base_url; ?>nic" class="tool-card-link" style="color: #ca8a04;">
							Explore NIC Codes <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>

			<!-- Card 5: GST HSN & SAC -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #7c3aed;"></div>
					<span class="tool-card-badge" style="background: #f5f3ff; color: #7c3aed;">22K+ Codes</span>
					<div>
						<div class="tool-icon-squircle" style="background: #f5f3ff; color: #7c3aed;">
							<i class="fas fa-file-invoice-dollar"></i>
						</div>
						<h3 class="tool-card-title">GST HSN &amp; SAC Finder</h3>
						<p class="tool-card-desc">Search and verify over 22,000+ GST Harmonized System Nomenclature (HSN) and Service Accounting Codes (SAC).</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.8) !important;">
						<a href="<?php echo $base_url; ?>gst-hsn-sac" class="tool-card-link" style="color: #7c3aed;">
							Search HSN/SAC <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>

			<!-- Card 6: Passport Seva Offices -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #dc2626;"></div>
					<span class="tool-card-badge" style="background: #fef2f2; color: #dc2626;">784+ Centres</span>
					<div>
						<div class="tool-icon-squircle" style="background: #fef2f2; color: #dc2626;">
							<i class="fas fa-passport"></i>
						</div>
						<h3 class="tool-card-title">Passport Seva Offices</h3>
						<p class="tool-card-desc">Locate your nearest Passport Seva Kendra (PSK), Post Office PSK (POPSK), and Regional Passport Office (RPO) nationwide.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.8) !important;">
						<a href="<?php echo $base_url; ?>passport" class="tool-card-link" style="color: #dc2626;">
							Search Offices <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>
			
			<!-- Card 7: Bare Acts -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #ea580c;"></div>
					<span class="tool-card-badge" style="background: #fff7ed; color: #ea580c;">Central Acts</span>
					<div>
						<div class="tool-icon-squircle" style="background: #fff7ed; color: #ea580c;">
							<i class="fas fa-gavel"></i>
						</div>
						<h3 class="tool-card-title">Bare Acts Directory</h3>
						<p class="tool-card-desc">Browse a comprehensive legal repository of Indian Central Acts, statutory regulations, and legislative enactments.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.8) !important;">
						<a href="<?php echo $base_url; ?>acts" class="tool-card-link" style="color: #ea580c;">
							Search Acts <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>

			<!-- Card 8: Bank Helplines & Customer Care -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #e11d48;"></div>
					<span class="tool-card-badge" style="background: #ffe4e6; color: #e11d48;">Direct Helplines</span>
					<div>
						<div class="tool-icon-squircle" style="background: #ffe4e6; color: #e11d48;">
							<i class="fas fa-headset"></i>
						</div>
						<h3 class="tool-card-title">Customer Care Directory</h3>
						<p class="tool-card-desc">Verified toll-free helplines, ATM card blocking hotlines, and missed-call balance numbers across public, private, and foreign banks.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.8) !important;">
						<a href="<?php echo $base_url; ?>bank/contact-details" class="tool-card-link" style="color: #e11d48;">
							View Helplines <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>

			<!-- Card 9: Developer APIs -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #4f46e5;"></div>
					<span class="tool-card-badge" style="background: #eef2ff; color: #4f46e5;">REST JSON API</span>
					<div>
						<div class="tool-icon-squircle" style="background: #eef2ff; color: #4f46e5;">
							<i class="fas fa-code"></i>
						</div>
						<h3 class="tool-card-title">Developer JSON APIs</h3>
						<p class="tool-card-desc">Integrate verified IFSC, PIN Code, GST, MCA, and NIC datasets into your applications with high-speed JSON endpoints.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.8) !important;">
						<a href="<?php echo $base_url; ?>api" class="tool-card-link" style="color: #4f46e5;">
							Explore APIs <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</section>
<!-- Features Grid End --> 

<!-- How It Works / Citizen Trust Section Start -->
<section class="py-5" style="background: #ffffff; border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft);">
	<div class="container">
		<div class="row text-center mb-5 justify-content-center">
			<div class="col-lg-8 col-12">
				<div class="badge badge-primary px-3 py-1 mb-2 rounded-pill font-weight-bold" style="background: #fff7ed; color: #ff671f; font-size: 12px; letter-spacing: 1px;">FAST &amp; TRUSTED WORKFLOW</div>
				<h2 class="section-title mb-2" style="font-size: clamp(26px, 3.5vw, 34px);">How OnlineLaw Empowers Citizens</h2>
				<p style="color: var(--text-muted); font-size: 15px;">Designed from the ground up for speed, reliability, and 100% free access to verified government records.</p>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-4 col-md-6 col-12 mb-4">
				<div class="workflow-step-card">
					<div class="step-number-pill">01</div>
					<h4 class="font-weight-bold text-dark mb-2" style="font-size: 18px; font-family: var(--font-heading);">Select Required Tool</h4>
					<p class="text-muted small mb-0" style="line-height: 1.7;">Choose from Cascading Bank Finder, PIN Finder, MCA Company Incorporations, NIC Codes, or GST HSN/SAC with zero login barriers.</p>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 col-12 mb-4">
				<div class="workflow-step-card">
					<div class="step-number-pill" style="background: #ecfdf5; color: #046a38;">02</div>
					<h4 class="font-weight-bold text-dark mb-2" style="font-size: 18px; font-family: var(--font-heading);">Instant Verified Query</h4>
					<p class="text-muted small mb-0" style="line-height: 1.7;">High-speed database engine queries official RBI, India Post, MCA, and CBIC directories with sub-second response times.</p>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 col-12 mb-4">
				<div class="workflow-step-card">
					<div class="step-number-pill" style="background: #eef2ff; color: #002b7f;">03</div>
					<h4 class="font-weight-bold text-dark mb-2" style="font-size: 18px; font-family: var(--font-heading);">Verify, Copy &amp; Comply</h4>
					<p class="text-muted small mb-0" style="line-height: 1.7;">Get complete addresses, branch codes, tax rates, and 1-click clipboard copy utilities for your official documentation.</p>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- How It Works Section End -->

<!-- Regional Passport Offices (RPO) Section Start -->
<section class="py-5" style="background: var(--bg-body);">
	<div class="container">
		<div class="row text-center mb-4 justify-content-center">
			<div class="col-lg-8 col-12">
				<div class="badge badge-primary px-3 py-1 mb-2 rounded-pill font-weight-bold" style="background: #fef2f2; color: #dc2626; font-size: 12px; letter-spacing: 1px;">PASSPORT SEVA KENDRA</div>
				<h2 class="section-title mb-2" style="font-size: clamp(24px, 3.5vw, 32px);">Regional Passport Offices (RPO)</h2>
				<p style="color: var(--text-muted); font-size: 15px;">Quickly browse passport seva services spanning all Regional Passport Offices across India.</p>
			</div>
		</div>
		<div class="row justify-content-center">
			<div class="col-12">
				<div class="d-flex flex-wrap justify-content-center align-items-center" style="gap: 10px;">
					<?php 
						$rpos = ["Ahmedabad", "Amritsar", "Bareilly", "Bengaluru", "Bhopal", "Bhubaneswar", "Chandigarh", "Chennai", "Cochin", "Coimbatore", "Dehradun", "Delhi", "Ghaziabad", "Goa", "Guwahati", "Hyderabad", "Jaipur", "Jalandhar", "Jammu", "Kolkata", "Kota", "Kozhikode", "Lucknow", "Madurai", "Mumbai", "Nagpur", "Patna", "Pune", "Raipur", "Ranchi", "Shimla", "Srinagar", "Surat", "Tiruchirappalli", "Trivandrum", "Vijayawada", "Visakhapatnam"];
						foreach($rpos as $rpo) {
					?>
						<a href="<?php echo $base_url; ?>passport?q=<?php echo urlencode($rpo); ?>" class="rpo-pill-link">
							<i class="fas fa-passport"></i> <?php echo $rpo; ?>
						</a>
					<?php } ?>
				</div>
				<div class="text-center mt-4">
					<a href="<?php echo $base_url; ?>passport" class="btn btn-outline-primary" style="border-radius: var(--radius-pill); font-weight: 600; padding: 10px 24px;">
						View All 784+ Passport Centres <i class="fas fa-arrow-right ml-2"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Regional Passport Offices (RPO) Section End -->

<!-- Citizen Knowledge & FAQ Accordion Section Start -->
<section class="py-5" style="background: #ffffff; border-top: 1px solid var(--border-soft);">
	<div class="container">
		<div class="row text-center mb-5 justify-content-center">
			<div class="col-lg-8 col-12">
				<div class="badge badge-primary px-3 py-1 mb-2 rounded-pill font-weight-bold" style="background: #fff7ed; color: #ff671f; font-size: 12px; letter-spacing: 1px;">CITIZEN KNOWLEDGE HUB</div>
				<h2 class="section-title mb-2" style="font-size: clamp(26px, 3.5vw, 34px);">Frequently Asked Questions</h2>
				<p style="color: var(--text-muted); font-size: 15px;">Understand how our directories, lookup tools, and data compliance standards work.</p>
			</div>
		</div>
		<div class="row justify-content-center">
			<div class="col-lg-10 col-12">
				
				<div class="faq-accordion-card">
					<button type="button" class="faq-accordion-btn" data-toggle="collapse" data-target="#faq1" aria-expanded="true" aria-controls="faq1">
						<span><i class="fas fa-university mr-2"></i> How does the Cascading Bank Finder work?</span>
						<i class="fas fa-chevron-down"></i>
					</button>
					<div id="faq1" class="collapse show">
						<div class="faq-accordion-body">
							Our Cascading Bank Finder lets you select your Bank name &rarr; State &rarr; District &rarr; Branch sequentially via fast AJAX dropdowns. It retrieves the exact 11-digit IFSC code, 9-digit MICR code, branch address, contact details, and electronic transfer capabilities (NEFT, RTGS, IMPS) across all 181,913+ RBI registered bank branches in India.
						</div>
					</div>
				</div>

				<div class="faq-accordion-card">
					<button type="button" class="faq-accordion-btn collapsed" data-toggle="collapse" data-target="#faq2" aria-expanded="false" aria-controls="faq2">
						<span><i class="fas fa-map-marked-alt mr-2"></i> What is the structure of a 6-digit Indian PIN Code?</span>
						<i class="fas fa-chevron-down"></i>
					</button>
					<div id="faq2" class="collapse">
						<div class="faq-accordion-body">
							India's Postal Index Number (PIN) is a 6-digit code: the 1st digit indicates the postal zone (1 to 9), the 2nd digit represents the sub-zone/state, the 3rd digit indicates the sorting district, and the last 3 digits pinpoint the specific delivery post office. Our PIN Finder maps over 155,000+ delivery locations nationwide.
						</div>
					</div>
				</div>

				<div class="faq-accordion-card">
					<button type="button" class="faq-accordion-btn collapsed" data-toggle="collapse" data-target="#faq3" aria-expanded="false" aria-controls="faq3">
						<span><i class="fas fa-sitemap mr-2"></i> What are NIC 2025 activity codes used for?</span>
						<i class="fas fa-chevron-down"></i>
					</button>
					<div id="faq3" class="collapse">
						<div class="faq-accordion-body">
							National Industrial Classification (NIC) codes are published by the Ministry of Statistics and Programme Implementation (MoSPI). They classify business and manufacturing activities for MCA Corporate Identity Numbers (CIN), MSME Udyam registration, and GST registrations. Our directory covers all 2-digit divisions, 3-digit groups, 4-digit classes, and 5-digit sub-classes.
						</div>
					</div>
				</div>

				<div class="faq-accordion-card">
					<button type="button" class="faq-accordion-btn collapsed" data-toggle="collapse" data-target="#faq4" aria-expanded="false" aria-controls="faq4">
						<span><i class="fas fa-code mr-2"></i> Can developers integrate OnlineLaw data into their apps?</span>
						<i class="fas fa-chevron-down"></i>
					</button>
					<div id="faq4" class="collapse">
						<div class="faq-accordion-body">
							Yes! We offer a high-performance developer API portal (<a href="<?php echo $base_url; ?>api" class="text-primary font-weight-bold">/api</a>) providing standardized JSON endpoints for Bank IFSC lookup, PIN Code validation, GST HSN/SAC queries, NIC code search, and Passport Seva centres.
						</div>
					</div>
				</div>

			</div>
		</div>

		<!-- Developer & API Callout Box -->
		<div class="row justify-content-center mt-5">
			<div class="col-lg-10 col-12">
				<div class="cta-developer-box">
					<div class="cta-ambient-glow"></div>
					<div class="row align-items-center position-relative" style="z-index: 2;">
						<div class="col-lg-8 col-12 text-center text-lg-left mb-3 mb-lg-0">
							<span class="badge badge-pill badge-primary px-3 py-1 font-weight-bold mb-2" style="background: rgba(99, 102, 241, 0.3); border: 1px solid rgba(99, 102, 241, 0.5); color: #c7d2fe;">FOR DEVELOPERS &amp; ENTERPRISES</span>
							<h3 class="font-weight-bold text-white mb-2" style="font-family: var(--font-heading); font-size: clamp(20px, 3vw, 26px);">Power your software with verified Indian civic data</h3>
							<p class="text-light small mb-0" style="color: #cbd5e1 !important; line-height: 1.6;">Plug-and-play RESTful JSON APIs for KYC verification, checkout postal validation, and compliance workflows.</p>
						</div>
						<div class="col-lg-4 col-12 text-center text-lg-right">
							<a href="<?php echo $base_url; ?>api" class="btn btn-light font-weight-bold px-4 py-3" style="border-radius: var(--radius-pill); color: #1e1b4b; font-size: 14.5px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
								<i class="fas fa-terminal text-primary mr-2"></i> Explore JSON API
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</section>
<!-- FAQ Section End -->

<!-- Metrics Section Start -->
<section class="metrics-modern-section">
	<div class="container position-relative" style="z-index: 2;">
		<div class="row text-center justify-content-center align-items-stretch">
			<div class="col-lg-3 col-sm-6 col-12 mb-4 mb-lg-0 d-flex">
				<div class="metric-glass-card w-100 d-flex flex-column justify-content-center align-items-center"> 
					<div class="metric-icon-ring" style="color: #38bdf8;">
                        <i class="fas fa-landmark"></i>
                    </div>
					<div class="metric-number" style="color: #38bdf8;">181K+</div>
					<div class="metric-label">Bank IFSC &amp; Branches</div>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6 col-12 mb-4 mb-lg-0 d-flex">
				<div class="metric-glass-card w-100 d-flex flex-column justify-content-center align-items-center"> 
					<div class="metric-icon-ring" style="color: #10b981;">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
					<div class="metric-number" style="color: #10b981;">155K+</div>
					<div class="metric-label">PIN Codes &amp; Locations</div>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6 col-12 mb-4 mb-lg-0 d-flex">
				<div class="metric-glass-card w-100 d-flex flex-column justify-content-center align-items-center"> 
					<div class="metric-icon-ring" style="color: #fbbf24;">
                        <i class="fas fa-building"></i>
                    </div>
					<div class="metric-number" style="color: #fbbf24;">3.1M+</div>
					<div class="metric-label">Companies &amp; NIC Codes</div>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6 col-12 mb-4 mb-lg-0 d-flex">
				<div class="metric-glass-card w-100 d-flex flex-column justify-content-center align-items-center"> 
					<div class="metric-icon-ring" style="color: #a78bfa;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
					<div class="metric-number" style="color: #a78bfa;">22K+</div>
					<div class="metric-label">GST HSN &amp; SAC Codes</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Metrics Section End --> 

<script>
// Interactive Hero Multi-Tool Switcher & Modern Recent Searches
document.addEventListener('DOMContentLoaded', function() {
    var tabs = document.querySelectorAll('#heroToolTabs .hero-tab-pill');
    var form = document.getElementById('heroInstantSearchForm');
    var input = document.getElementById('heroSearchInput');
    var icon = document.getElementById('heroSearchIcon');
    var clearBtn = document.getElementById('heroClearBtn');
    var hintText = document.getElementById('heroFormatHintText');
    var baseUrl = '<?php echo $base_url; ?>';

    var toolHints = {
        'bank-finder': 'Tip: Select Bank, State, District & Branch or enter branch name',
        'pincode-finder': 'Tip: Select State, District, and postal delivery division',
        'bank-ifsc': 'Tip: Enter 11-digit IFSC code (e.g. SBIN0000691, HDFC0000001, ICIC0000002)',
        'pincode': 'Tip: Enter 6-digit postal PIN code (e.g. 110001, 841301, 400001)',
        'company-incorporation': 'Tip: Enter Company Name or 21-digit Corporate Identity Number (CIN)',
        'nic': 'Tip: Search 2 to 5-digit NIC 2025 activity code or sector (e.g. 6201, Software)',
        'gst-hsn-sac': 'Tip: Search 4 to 8-digit HSN code or product name (e.g. 8471, Computer)',
        'passport': 'Tip: Enter City, Passport Seva Kendra (PSK), or RPO name (e.g. Patna, Delhi)'
    };

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');

            var action = this.getAttribute('data-action');
            var paramName = this.getAttribute('data-name');
            var placeholder = this.getAttribute('data-placeholder');
            var iconClass = this.getAttribute('data-icon');

            form.setAttribute('action', baseUrl + action);
            input.setAttribute('name', paramName);
            input.setAttribute('placeholder', placeholder);
            input.value = '';
            if (clearBtn) clearBtn.style.display = 'none';
            if (hintText && toolHints[action]) hintText.textContent = toolHints[action];
            
            icon.className = iconClass + ' text-primary mr-2';
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

    // Recent Searches Storage & Rendering
    function loadRecentSearches() {
        var container = document.getElementById('heroRecentContainer');
        var list = document.getElementById('heroRecentList');
        if (!container || !list) return;

        try {
            var raw = localStorage.getItem('olaw_recent_searches');
            if (!raw) return;
            var searches = JSON.parse(raw);
            if (!Array.isArray(searches) || searches.length === 0) return;

            list.innerHTML = '';
            searches.slice(0, 5).forEach(function(item) {
                var a = document.createElement('a');
                a.className = 'hero-recent-tag';
                a.href = baseUrl + item.action + '?' + encodeURIComponent(item.param || 'q') + '=' + encodeURIComponent(item.term);
                a.innerHTML = '<i class="fas fa-search text-muted mr-1" style="font-size: 10px;"></i>' + item.term;
                list.appendChild(a);
            });
            container.style.display = 'flex';
        } catch(e) {}
    }

    if (form) {
        form.addEventListener('submit', function() {
            var query = input.value.trim();
            if (!query) return;
            var activeTab = document.querySelector('#heroToolTabs .hero-tab-pill.active');
            var action = activeTab ? activeTab.getAttribute('data-action') : 'bank-finder';
            var param = activeTab ? activeTab.getAttribute('data-name') : 'q';

            try {
                var raw = localStorage.getItem('olaw_recent_searches');
                var searches = raw ? JSON.parse(raw) : [];
                searches = searches.filter(function(s) { return s.term.toLowerCase() !== query.toLowerCase(); });
                searches.unshift({ term: query, action: action, param: param });
                localStorage.setItem('olaw_recent_searches', JSON.stringify(searches.slice(0, 8)));
            } catch(e) {}
        });
    }

    loadRecentSearches();
});
</script>

<?php require_once ('includes/footer.php'); ?>

