<?php require_once('includes/config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="India's leading digital legal tool directory. Search 1M+ PIN Codes, 181K+ Bank IFSC & MICR codes, MCA Company Incorporations, 22K+ GST HSN/SAC codes, and 784 Passport Offices instantly. 100% free for every citizen (Updated 2026).">
<meta name="keywords" content="bank ifsc finder, pin code lookup india, mca company incorporation, gst hsn sac finder, passport seva office locator, bank micr search, indian postal index number directory, bare acts india">
<meta name="author" content="OfferPlant">
<title>Online Law | Free Bank IFSC, PIN Code, Company Incorporation, GST HSN & Passport Finder India (Updated 2026)</title>

<style>
    /* Hero Search Console Modern Styles */
    .hero-search-card {
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: var(--radius-xl);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        padding: 24px;
        margin-top: 30px;
        transition: var(--transition-smooth);
    }
    .hero-search-card:focus-within {
        box-shadow: 0 30px 60px -12px rgba(37, 99, 235, 0.3);
        border-color: rgba(37, 99, 235, 0.4);
    }
    .hero-tab-pill {
        background: #f1f5f9;
        color: #475569;
        font-family: var(--font-heading);
        font-weight: 600;
        font-size: 13px;
        padding: 7px 15px;
        border-radius: var(--radius-pill);
        border: 1px solid transparent;
        cursor: pointer;
        transition: var(--transition-smooth);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        user-select: none;
    }
    .hero-tab-pill:hover {
        background: #e2e8f0;
        color: var(--text-main);
    }
    .hero-tab-pill.active {
        background: var(--primary-gradient);
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        border-color: transparent;
    }
    .hero-search-input-group {
        display: flex;
        align-items: center;
        background: #ffffff;
        border: 2px solid var(--border-soft);
        border-radius: var(--radius-md);
        padding: 6px 12px;
        transition: var(--transition-smooth);
    }
    .hero-search-input-group:focus-within {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px var(--primary-light);
    }
    .hero-search-input-group input {
        border: none !important;
        box-shadow: none !important;
        font-size: 16px;
        padding: 10px 12px;
        font-family: var(--font-body);
        color: var(--text-main);
        outline: none;
        flex: 1;
        background: transparent;
    }
    .hero-quick-tag {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        padding: 3px 10px;
        border-radius: 6px;
        text-decoration: none !important;
        transition: var(--transition-smooth);
    }
    .hero-quick-tag:hover {
        background: var(--primary-light);
        color: var(--primary-color);
    }
    
    /* Process Workflow Step Cards */
    .workflow-step-card {
        background: #ffffff;
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-lg);
        padding: 30px 24px;
        height: 100%;
        transition: var(--transition-bounce);
        position: relative;
    }
    .workflow-step-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(37, 99, 235, 0.3);
    }
    .step-number-pill {
        width: 38px;
        height: 38px;
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
</style>

<?php require_once ('includes/header.php'); ?> 

<!-- Hero Banner Section Start -->
<section class="hero-modern-banner position-relative">
	<div class="hero-glow-sphere-1"></div>
	<div class="hero-glow-sphere-2"></div>
	<div class="container position-relative" style="z-index: 2;">
		<div class="row justify-content-center">
			<div class="col-lg-10 col-12 text-center">
				<div class="hero-pill-tag mb-3">
					<span class="hero-pulse-dot"></span>
					<span>OFFICIAL CITIZEN PORTAL • UPDATED 2026</span>
				</div>
				<h1 class="hero-main-heading">
					Empowering India with <br>
					<span class="hero-gradient-text">Free Digital Law & Compliance Tools</span>
				</h1>
				<p class="hero-subtext">
					Access critical legal, banking, and government data with zero friction. Search 1M+ PIN codes, 181K+ Bank IFSC/MICR records, MCA company incorporations, GST rates, and Passport Seva centres.
				</p>

				<!-- Multi-Tool Instant Search Console -->
				<div class="hero-search-card text-left">
					<!-- Tool Select Tabs -->
					<div class="d-flex flex-wrap align-items-center justify-content-start justify-content-md-center gap-2 mb-3 pb-2 border-bottom" style="gap: 8px;" id="heroToolTabs">
						<button type="button" class="hero-tab-pill active" data-action="pincode" data-name="pincode" data-placeholder="Enter 6-digit PIN code (e.g. 110001, 841301)..." data-icon="fas fa-map-pin">
							<i class="fas fa-map-pin"></i> PIN Code
						</button>
						<button type="button" class="hero-tab-pill" data-action="bank-ifsc" data-name="ifsccode" data-placeholder="Enter 11-digit IFSC code (e.g. SBIN0001234, HDFC0000001)..." data-icon="fas fa-university">
							<i class="fas fa-university"></i> Bank IFSC
						</button>
						<button type="button" class="hero-tab-pill" data-action="company-incorporation" data-name="q" data-placeholder="Enter Company Name, CIN (e.g. U47713DC2026PTC474566), or LLP..." data-icon="fas fa-building">
							<i class="fas fa-building"></i> Company (CIN)
						</button>
						<button type="button" class="hero-tab-pill" data-action="nic" data-name="q" data-placeholder="Search 2,715 NIC activity codes or industries (e.g. Software, 47713, Hospital)..." data-icon="fas fa-sitemap">
							<i class="fas fa-sitemap"></i> NIC 2025 Codes
						</button>
						<button type="button" class="hero-tab-pill" data-action="bank-micr" data-name="micr" data-placeholder="Enter 9-digit MICR code or Bank/City..." data-icon="fas fa-money-check-alt">
							<i class="fas fa-money-check-alt"></i> Bank MICR
						</button>
						<button type="button" class="hero-tab-pill" data-action="gst-hsn-sac" data-name="q" data-placeholder="Search 4-8 digit HSN/SAC code or product name..." data-icon="fas fa-file-invoice-dollar">
							<i class="fas fa-file-invoice-dollar"></i> GST HSN/SAC
						</button>
						<button type="button" class="hero-tab-pill" data-action="passport" data-name="q" data-placeholder="Search city, PSK, or Regional Passport Office (e.g. Patna, Delhi)..." data-icon="fas fa-passport">
							<i class="fas fa-passport"></i> Passport Seva
						</button>
					</div>

					<!-- Search Input Form -->
					<form id="heroInstantSearchForm" method="GET" action="<?php echo $base_url; ?>pincode">
						<div class="row align-items-center">
							<div class="col-md-9 col-12 mb-2 mb-md-0">
								<div class="hero-search-input-group">
									<i id="heroSearchIcon" class="fas fa-map-pin text-primary mr-2" style="font-size: 18px; width: 22px; text-align: center;"></i>
									<input type="text" id="heroSearchInput" name="pincode" placeholder="Enter 6-digit PIN code (e.g. 110001, 841301)..." required autocomplete="off">
								</div>
							</div>
							<div class="col-md-3 col-12">
								<button type="submit" class="btn btn-primary btn-block font-weight-bold" style="height: 52px; border-radius: var(--radius-md); font-size: 15px;">
									<i class="fas fa-search mr-1"></i> Instant Lookup
								</button>
							</div>
						</div>
					</form>

					<!-- Quick Trending Suggestions -->
					<div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-2">
						<span class="small text-muted font-weight-bold mr-1"><i class="fas fa-bolt text-warning mr-1"></i> Popular:</span>
						<a href="<?php echo $base_url; ?>company-incorporation" class="hero-quick-tag">New Incorporations 2026</a>
						<a href="<?php echo $base_url; ?>nic?division=62" class="hero-quick-tag">NIC Software 62</a>
						<a href="<?php echo $base_url; ?>pincode?pincode=110001" class="hero-quick-tag">PIN 110001 (New Delhi)</a>
						<a href="<?php echo $base_url; ?>bank-ifsc?ifsc=SBIN0000691" class="hero-quick-tag">SBI IFSC</a>
						<a href="<?php echo $base_url; ?>gst-hsn-sac?q=IT+Software" class="hero-quick-tag">GST Software SAC 998314</a>
						<a href="<?php echo $base_url; ?>passport?q=Patna" class="hero-quick-tag">RPO Patna</a>
					</div>
				</div>

			</div>
		</div>
	</div>
</section>
<!-- Hero Banner Section End --> 

<!-- Features Grid -->
<section class="features-section-wrap">
	<div class="container">
		<div class="row text-center mb-5 justify-content-center">
			<div class="col-lg-8 col-12">
				<div class="badge badge-primary px-3 py-1 mb-2 rounded-pill font-weight-bold" style="background: var(--primary-light); color: var(--primary-color); font-size: 12px; letter-spacing: 1px;">INSTANT ESSENTIAL DIRECTORIES</div>
				<h2 class="section-title mb-3" style="font-size: clamp(28px, 4vw, 38px);">Rapid Essential Lookups</h2>
				<p style="color: var(--text-muted); font-size: 16px;">Universally free digital lookups built with high accuracy from verified Indian government sources.</p>
			</div>
		</div>
		<div class="row">
			
			<!-- Card 0 (Company Incorporation) -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: var(--primary-color);"></div>
					<span class="tool-card-badge" style="background: #eff6ff; color: #2563eb;">MCA Live Data</span>
					<div>
						<div class="tool-icon-squircle" style="background: #eff6ff; color: #2563eb;">
							<i class="fas fa-building"></i>
						</div>
						<h3 class="tool-card-title">Company Incorporations</h3>
						<p class="tool-card-desc">Search newly registered Indian Companies, LLPs, and Foreign Entities from official MCA daily incorporation reports.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.6) !important;">
						<a href="<?php echo $base_url; ?>company-incorporation" class="tool-card-link" style="color: #2563eb;">
							Search Companies <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>

			<!-- Card 0b (NIC 2025 Activity Codes) -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #eab308;"></div>
					<span class="tool-card-badge" style="background: #fefce8; color: #a16207;">2,715 MoSPI Codes</span>
					<div>
						<div class="tool-icon-squircle" style="background: #fefce8; color: #ca8a04;">
							<i class="fas fa-sitemap"></i>
						</div>
						<h3 class="tool-card-title">NIC 2025 Activity Codes</h3>
						<p class="tool-card-desc">Search official 2-digit to 6-digit National Industrial Classification codes for MCA CIN, MSME Udyam, and GST registration.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.6) !important;">
						<a href="<?php echo $base_url; ?>nic" class="tool-card-link" style="color: #ca8a04;">
							Explore NIC Codes <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>

			<!-- Card 1 (PIN Code Directory) -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #0284c7;"></div>
					<span class="tool-card-badge" style="background: #f0f9ff; color: #0284c7;">1M+ PINs</span>
					<div>
						<div class="tool-icon-squircle" style="background: #f0f9ff; color: #0284c7;">
							<i class="fas fa-map-marked-alt"></i>
						</div>
						<h3 class="tool-card-title">PIN Code Directory</h3>
						<p class="tool-card-desc">Search and verify over 1M+ accurate Postal Index Numbers spanning every post office, district, and state across India.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.6) !important;">
						<a href="<?php echo $base_url; ?>pincode" class="tool-card-link" style="color: #0284c7;">
							Explore PINs <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>
			
			<!-- Card 2 (Bank IFSC Data) -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #d97706;"></div>
					<span class="tool-card-badge" style="background: #fffbeb; color: #d97706;">181K+ Branches</span>
					<div>
						<div class="tool-icon-squircle" style="background: #fffbeb; color: #d97706;">
							<i class="fas fa-university"></i>
						</div>
						<h3 class="tool-card-title">Bank IFSC Directory</h3>
						<p class="tool-card-desc">Safeguard NEFT, RTGS, and IMPS transactions by instantly retrieving exact IFSC codes for all scheduled bank branches.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.6) !important;">
						<a href="<?php echo $base_url; ?>bank-ifsc" class="tool-card-link" style="color: #d97706;">
							Find IFSC <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>
			
			<!-- Card 3 (Bank MICR Codes) -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #059669;"></div>
					<span class="tool-card-badge" style="background: #ecfdf5; color: #059669;">Cheque Clearing</span>
					<div>
						<div class="tool-icon-squircle" style="background: #ecfdf5; color: #059669;">
							<i class="fas fa-money-check-alt"></i>
						</div>
						<h3 class="tool-card-title">Bank MICR Codes</h3>
						<p class="tool-card-desc">Easily locate 9-digit Magnetic Ink Character Recognition codes for swift interbank clearing and cheque processing.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.6) !important;">
						<a href="<?php echo $base_url; ?>bank-micr" class="tool-card-link" style="color: #059669;">
							Search MICR <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>

			<!-- Card 4 (GST HSN & SAC) -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #7c3aed;"></div>
					<span class="tool-card-badge" style="background: #f5f3ff; color: #7c3aed;">22K+ Codes</span>
					<div>
						<div class="tool-icon-squircle" style="background: #f5f3ff; color: #7c3aed;">
							<i class="fas fa-file-invoice-dollar"></i>
						</div>
						<h3 class="tool-card-title">GST HSN & SAC Finder</h3>
						<p class="tool-card-desc">Search and verify over 22,000+ GST Harmonized System Nomenclature (HSN) and Service Accounting Codes (SAC).</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.6) !important;">
						<a href="<?php echo $base_url; ?>gst-hsn-sac" class="tool-card-link" style="color: #7c3aed;">
							Search HSN/SAC <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>

			<!-- Card 5 (Passport Offices) -->
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
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.6) !important;">
						<a href="<?php echo $base_url; ?>passport" class="tool-card-link" style="color: #dc2626;">
							Search Offices <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>
			
			<!-- Card 6 (Bare Acts) -->
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
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.6) !important;">
						<a href="<?php echo $base_url; ?>acts" class="tool-card-link" style="color: #ea580c;">
							Search Acts <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>

			<!-- Card 7 (Bank Services) -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #0284c7;"></div>
					<span class="tool-card-badge" style="background: #e0f2fe; color: #0284c7;">Direct Helplines</span>
					<div>
						<div class="tool-icon-squircle" style="background: #e0f2fe; color: #0284c7;">
							<i class="fas fa-phone-volume"></i>
						</div>
						<h3 class="tool-card-title">Bank Customer Helplines</h3>
						<p class="tool-card-desc">Instant access to toll-free customer support numbers, balance inquiry SMS services, and ATM block lines across all Indian banks.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.6) !important;">
						<a href="<?php echo $base_url; ?>bank" class="tool-card-link" style="color: #0284c7;">
							Explore Banking <i class="fas fa-arrow-right ml-1"></i>
						</a>
					</div>
				</div>
			</div>

			<!-- Card 8 (Developer API) -->
			<div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
				<div class="modern-tool-card w-100">
					<div class="tool-card-glow-bg" style="background: #4f46e5;"></div>
					<span class="tool-card-badge" style="background: #eef2ff; color: #4f46e5;">REST JSON</span>
					<div>
						<div class="tool-icon-squircle" style="background: #eef2ff; color: #4f46e5;">
							<i class="fas fa-code"></i>
						</div>
						<h3 class="tool-card-title">Developer JSON APIs</h3>
						<p class="tool-card-desc">Integrate verified IFSC, PIN Code, GST, and MCA datasets into your applications with high-speed JSON endpoints.</p>
					</div>
					<div class="pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.6) !important;">
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
				<div class="badge badge-primary px-3 py-1 mb-2 rounded-pill font-weight-bold" style="background: #eff6ff; color: #2563eb; font-size: 12px; letter-spacing: 1px;">FAST &amp; TRUSTED</div>
				<h2 class="section-title mb-2" style="font-size: clamp(26px, 3.5vw, 34px);">How OnlineLaw Empowers Citizens</h2>
				<p style="color: var(--text-muted); font-size: 15px;">Designed from the ground up for speed, reliability, and 100% free access to verified government records.</p>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-4 col-md-6 col-12 mb-4">
				<div class="workflow-step-card">
					<div class="step-number-pill">01</div>
					<h4 class="font-weight-bold text-dark mb-2" style="font-size: 18px; font-family: var(--font-heading);">Select Required Tool</h4>
					<p class="text-muted small mb-0" style="line-height: 1.7;">Choose from PIN Code, Bank IFSC/MICR, MCA Company Incorporations, GST HSN/SAC, or Passport Seva locator with zero login barriers.</p>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 col-12 mb-4">
				<div class="workflow-step-card">
					<div class="step-number-pill" style="background: #ecfdf5; color: #059669;">02</div>
					<h4 class="font-weight-bold text-dark mb-2" style="font-size: 18px; font-family: var(--font-heading);">Instant Verified Query</h4>
					<p class="text-muted small mb-0" style="line-height: 1.7;">High-speed database engine queries official RBI, India Post, MCA, and CBIC directories with sub-second response times.</p>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 col-12 mb-4">
				<div class="workflow-step-card">
					<div class="step-number-pill" style="background: #f5f3ff; color: #7c3aed;">03</div>
					<h4 class="font-weight-bold text-dark mb-2" style="font-size: 18px; font-family: var(--font-heading);">Verify, Copy &amp; Comply</h4>
					<p class="text-muted small mb-0" style="line-height: 1.7;">Get complete addresses, branch contact information, tax rates, and 1-click clipboard copy utilities for your official documentation.</p>
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
					<a href="<?php echo $base_url; ?>passport" class="btn btn-outline-primary">
						View All 784+ Passport Centres <i class="fas fa-arrow-right ml-2"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Regional Passport Offices (RPO) Section End -->

<!-- Metrics Section Start -->
<section class="metrics-modern-section">
	<div class="container position-relative" style="z-index: 2;">
		<div class="row text-center justify-content-center align-items-stretch">
			<div class="col-lg-3 col-sm-6 col-12 mb-4 mb-lg-0 d-flex">
				<div class="metric-glass-card w-100 d-flex flex-column justify-content-center align-items-center"> 
					<div class="metric-icon-ring" style="color: #38bdf8;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
					<div class="metric-number" style="color: #38bdf8;">22K+</div>
					<div class="metric-label">GST HSN & SAC</div>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6 col-12 mb-4 mb-lg-0 d-flex">
				<div class="metric-glass-card w-100 d-flex flex-column justify-content-center align-items-center"> 
					<div class="metric-icon-ring" style="color: #fbbf24;">
                        <i class="fas fa-passport"></i>
                    </div>
					<div class="metric-number" style="color: #fbbf24;">784+</div>
					<div class="metric-label">Passport Offices</div>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6 col-12 mb-4 mb-lg-0 d-flex">
				<div class="metric-glass-card w-100 d-flex flex-column justify-content-center align-items-center"> 
					<div class="metric-icon-ring" style="color: #10b981;">
                        <i class="fas fa-university"></i>
                    </div>
					<div class="metric-number" style="color: #10b981;">181K+</div>
					<div class="metric-label">Bank IFSC Records</div>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6 col-12 mb-4 mb-lg-0 d-flex">
				<div class="metric-glass-card w-100 d-flex flex-column justify-content-center align-items-center"> 
					<div class="metric-icon-ring" style="color: #a78bfa;">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
					<div class="metric-number" style="color: #a78bfa;">1M+</div>
					<div class="metric-label">PIN Locations</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Metrics Section End --> 

<script>
// Interactive Hero Multi-Tool Switcher Script
document.addEventListener('DOMContentLoaded', function() {
    var tabs = document.querySelectorAll('#heroToolTabs .hero-tab-pill');
    var form = document.getElementById('heroInstantSearchForm');
    var input = document.getElementById('heroSearchInput');
    var icon = document.getElementById('heroSearchIcon');
    var baseUrl = '<?php echo $base_url; ?>';

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
            
            icon.className = iconClass + ' text-primary mr-2';
            input.focus();
        });
    });
});
</script>

<?php require_once ('includes/footer.php'); ?>
