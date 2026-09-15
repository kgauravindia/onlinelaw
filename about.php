<?php require_once('includes/config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Learn about Online Law - India's premier open legal and public directory platform. Providing 100% free, fast, and verified access to Bank IFSC & MICR, PIN Codes, MCA Company Incorporation, NIC 2025 Codes, GST HSN/SAC, and Passport Seva.">
<meta name="keywords" content="about online law, legal tools mission india, democratizing legal data, free financial tools team, offerplant legal initiative, ifsc directory mission, pincode directory india">
<meta name="author" content="OfferPlant">
<title>About Us | Democratizing Legal & Public Data in India - Online Law</title>

<?php require_once ('includes/header.php'); ?> 

<style>
    /* Hero Header Styling */
    .about-hero-section {
        background: linear-gradient(135deg, #090e1a 0%, #0f172a 40%, #1e293b 100%);
        padding: 56px 0 50px;
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .about-hero-section::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 480px;
        height: 480px;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.35) 0%, rgba(37, 99, 235, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .about-hero-section::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -5%;
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(6, 182, 212, 0.25) 0%, rgba(6, 182, 212, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .hero-gradient-text {
        background: linear-gradient(135deg, #60a5fa 0%, #38bdf8 45%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline;
    }
    .about-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(37, 99, 235, 0.2);
        border: 1px solid rgba(96, 165, 250, 0.4);
        color: #93c5fd;
        padding: 6px 18px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.8px;
        margin-bottom: 18px;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
    }

    /* Platform Scale Section Styling */
    .scale-stat-section {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 45%, #1d4ed8 100%);
        padding: 65px 0;
        position: relative;
        overflow: hidden;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }
    .scale-stat-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .scale-stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff;
        padding: 5px 16px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .stat-card-colored {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.24);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: var(--radius-xl);
        padding: 28px 20px;
        transition: var(--transition-bounce);
        text-align: center;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
    }
    .stat-card-colored:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.45);
        transform: translateY(-6px);
        box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.3);
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
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-lg);
        padding: 30px 25px;
        transition: var(--transition-smooth);
        height: 100%;
        position: relative;
    }
    .feature-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(37, 99, 235, 0.3);
    }
    .feature-icon-box {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 20px;
        transition: var(--transition-smooth);
    }
    .feature-card-modern:hover .feature-icon-box {
        transform: scale(1.08);
    }
    .leadership-card {
        background: #ffffff;
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-xl);
        padding: 35px 30px;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-smooth);
        position: relative;
        height: 100%;
    }
    .leadership-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(37, 99, 235, 0.3);
    }
    .tool-explore-item {
        background: #ffffff;
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-md);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        text-decoration: none !important;
        transition: var(--transition-smooth);
    }
    .tool-explore-item:hover {
        background: #f8fafc;
        border-color: var(--primary-color);
        transform: translateX(4px);
        box-shadow: var(--shadow-sm);
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
</style>

<!-- Page Hero Header -->
<section class="about-hero-section">
	<div class="container position-relative" style="z-index: 2;">
		<div class="row align-items-center justify-content-center text-center">
			<div class="col-lg-10">
                <div class="about-badge-pill">
                    <i class="fas fa-balance-scale"></i>
                    <span>EMPOWERING DIGITAL INDIA</span>
                </div>
				<h1 class="text-white mb-3" style="font-size: 38px; font-weight: 800; letter-spacing: -0.025em; line-height: 1.25;">
					<span style="background: linear-gradient(135deg, #38bdf8 0%, #60a5fa 50%, #93c5fd 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800;">Democratizing</span> Legal, Financial &amp; Administrative Data
				</h1>
				<p class="text-white-50 mb-4" style="font-size: 16px; line-height: 1.7; max-width: 760px; margin: 0 auto;">
					Online Law is India's fast, free, and unified public directory engine—designed to connect citizens, legal professionals, and businesses with authoritative public information instantly.
				</p>
				<ul class="list-inline mb-0" style="color: #94a3b8; font-size: 14px;">
					<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: #cbd5e1; text-decoration: none;"><i class="fas fa-home mr-1"></i> Home</a></li>
					<li class="list-inline-item mx-2 text-muted">/</li>
					<li class="list-inline-item text-white font-weight-medium">About Us</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<!-- Mission & Story Section -->
<section class="py-5 bg-body" style="padding: 70px 0 !important;">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-lg-5 mb-5 mb-lg-0">
				<div class="position-relative"> 
					<div class="overflow-hidden" style="border-radius: var(--radius-xl); box-shadow: var(--shadow-xl); border: 4px solid #ffffff;">
                        <img src="images/planning.jpg" alt="About Online Law Mission" style="width: 100%; height: auto; display: block; object-fit: cover;">
                    </div>
                    <!-- Overlay floating badge -->
                    <div class="position-absolute d-none d-sm-flex align-items-center" style="bottom: -20px; right: 20px; background: #ffffff; padding: 14px 22px; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); border: 1px solid var(--border-soft); gap: 12px;">
                        <div style="width: 42px; height: 42px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <div style="font-size: 13px; font-weight: 700; color: var(--text-main);">100% Free &amp; Public</div>
                            <div style="font-size: 12px; color: var(--text-muted);">Built for 1.4B+ Citizens</div>
                        </div>
                    </div>
				</div>
			</div>
			<div class="col-lg-7 pl-lg-5">
                <div class="detail-area p-0" style="background: transparent; border: none; box-shadow: none;">
                    <div class="mb-4">
                        <span class="text-primary font-weight-bold" style="font-size: 13px; letter-spacing: 1px; text-transform: uppercase;">Our Purpose &amp; Vision</span>
                        <h2 class="mt-2 mb-3" style="font-size: 30px; font-weight: 800; color: var(--text-main); line-height: 1.3;">
                            Building India's Most Reliable Digital Public Directory
                        </h2>
                        <p style="font-size: 15px; color: var(--text-muted); line-height: 1.8;">
                            In an increasingly digital economy, accessing essential civic and regulatory identifiers—such as postal PIN codes, banking routing codes (IFSC &amp; MICR), corporate incorporation records, business activity classifications (NIC 2025), and GST tariffs—should never be tedious or paywalled.
                        </p>
                        <p style="font-size: 15px; color: var(--text-muted); line-height: 1.8;">
                            <strong>Online Law</strong>, an initiative powered by OfferPlant, bridges the information gap by aggregating and indexing massive public data sets into a lightning-fast, secure, and mobile-friendly search interface.
                        </p>
                    </div>
                    
                    <div class="row pt-2 mb-4">
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div style="color: var(--primary-color); font-size: 18px; margin-right: 12px; margin-top: 2px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 font-weight-bold" style="font-size: 15px; color: var(--text-main);">Unified Access</h6>
                                    <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.5;">One platform for banking, postal, corporate, tax, and legal reference data.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div style="color: var(--accent-emerald); font-size: 18px; margin-right: 12px; margin-top: 2px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 font-weight-bold" style="font-size: 15px; color: var(--text-main);">Zero Paywalls</h6>
                                    <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.5;">Every tool and search lookup is completely free forever for all users.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div style="color: var(--accent-purple); font-size: 18px; margin-right: 12px; margin-top: 2px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 font-weight-bold" style="font-size: 15px; color: var(--text-main);">Privacy First</h6>
                                    <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.5;">No tracking of queries, no accounts required, no hidden monetization.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div style="color: var(--accent-amber); font-size: 18px; margin-right: 12px; margin-top: 2px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 font-weight-bold" style="font-size: 15px; color: var(--text-main);">Live API Support</h6>
                                    <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.5;">Fast REST endpoints available for software developers &amp; businesses.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3" style="gap: 12px;">
                        <a href="<?php echo $base_url; ?>pincode" class="btn btn-primary" style="padding: 10px 24px; border-radius: var(--radius-md); font-weight: 600;">
                            <i class="fas fa-search mr-2"></i> Explore Directory Tools
                        </a>
                        <a href="<?php echo $base_url; ?>contact" class="btn btn-outline-secondary" style="padding: 10px 24px; border-radius: var(--radius-md); font-weight: 600; border-color: var(--border-soft); color: var(--text-main);">
                            <i class="fas fa-envelope mr-2"></i> Contact Team
                        </a>
                    </div>
                </div>
			</div>
		</div>
	</div>
</section>

<!-- Scale & Key Numbers (Vibrant Gradient Section) -->
<section class="scale-stat-section">
	<div class="container position-relative" style="z-index: 2;">
        <div class="row text-center mb-4 pb-2">
            <div class="col-12">
                <div class="scale-stat-badge">
                    <i class="fas fa-chart-line mr-1"></i> Platform Scale
                </div>
                <h3 class="text-white mt-1 mb-0" style="font-size: 28px; font-weight: 800; letter-spacing: -0.01em;">
                    Indexed Records Across India
                </h3>
            </div>
        </div>
		<div class="row text-center">
			<div class="col-lg-3 col-6 mb-4 mb-lg-0">
				<div class="stat-card-colored"> 
                    <div class="stat-icon-circle" style="background: linear-gradient(135deg, #0284c7, #0ea5e9);">
                        <i class="fas fa-university"></i>
                    </div>
					<div class="text-white mb-1" style="font-size: 34px; font-weight: 800; font-family: var(--font-heading); line-height: 1.1;">181K+</div>
					<div style="font-size: 13px; font-weight: 700; color: #bfdbfe; text-transform: uppercase; letter-spacing: 0.5px;">Bank IFSC &amp; MICR</div>
				</div>
			</div>
			<div class="col-lg-3 col-6 mb-4 mb-lg-0">
				<div class="stat-card-colored"> 
                    <div class="stat-icon-circle" style="background: linear-gradient(135deg, #059669, #10b981);">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
					<div class="text-white mb-1" style="font-size: 34px; font-weight: 800; font-family: var(--font-heading); line-height: 1.1;">155K+</div>
					<div style="font-size: 13px; font-weight: 700; color: #a7f3d0; text-transform: uppercase; letter-spacing: 0.5px;">PIN Codes &amp; Localities</div>
				</div>
			</div>
			<div class="col-lg-3 col-6">
				<div class="stat-card-colored"> 
                    <div class="stat-icon-circle" style="background: linear-gradient(135deg, #d97706, #f59e0b);">
                        <i class="fas fa-building"></i>
                    </div>
					<div class="text-white mb-1" style="font-size: 34px; font-weight: 800; font-family: var(--font-heading); line-height: 1.1;">3.1M+</div>
					<div style="font-size: 13px; font-weight: 700; color: #fde68a; text-transform: uppercase; letter-spacing: 0.5px;">MCA Companies &amp; NIC</div>
				</div>
			</div>
			<div class="col-lg-3 col-6">
				<div class="stat-card-colored"> 
                    <div class="stat-icon-circle" style="background: linear-gradient(135deg, #7c3aed, #a855f7);">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
					<div class="text-white mb-1" style="font-size: 34px; font-weight: 800; font-family: var(--font-heading); line-height: 1.1;">22K+</div>
					<div style="font-size: 13px; font-weight: 700; color: #e9d5ff; text-transform: uppercase; letter-spacing: 0.5px;">GST HSN / SAC Codes</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Core Directory Offerings -->
<section class="py-5 bg-body" style="padding: 70px 0 !important;">
	<div class="container">
		<div class="row justify-content-center text-center mb-5">
			<div class="col-lg-8">
                <span class="text-primary font-weight-bold" style="font-size: 13px; letter-spacing: 1px; text-transform: uppercase;">Comprehensive Suite</span>
				<h2 class="mt-2 mb-3" style="font-size: 30px; font-weight: 800; color: var(--text-main);">What You Can Discover on Online Law</h2>
				<p class="text-muted" style="font-size: 15px;">Fast, reliable, and verified lookups covering essential business and civic workflows.</p>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="feature-card-modern">
					<div class="feature-icon-box" style="background: #eff6ff; color: var(--primary-color);">
						<i class="fas fa-university"></i>
					</div>
					<h5 class="font-weight-bold mb-2" style="font-size: 18px; color: var(--text-main);">Bank IFSC &amp; MICR</h5>
					<p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">Locate official RBI routing codes, branch addresses, contact info, and MICR numbers across all public and private banks in India.</p>
					<a href="<?php echo $base_url; ?>bank-ifsc" class="font-weight-bold" style="color: var(--primary-color); font-size: 14px; text-decoration: none;">
						Search IFSC Codes <i class="fas fa-arrow-right ml-1"></i>
					</a>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="feature-card-modern">
					<div class="feature-icon-box" style="background: #ecfdf5; color: var(--accent-emerald);">
						<i class="fas fa-map-pin"></i>
					</div>
					<h5 class="font-weight-bold mb-2" style="font-size: 18px; color: var(--text-main);">Postal PIN Codes</h5>
					<p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">Search India Post postal index numbers by 6-digit code or look up post offices by state, district, taluka, and locality name.</p>
					<a href="<?php echo $base_url; ?>pincode" class="font-weight-bold" style="color: var(--accent-emerald); font-size: 14px; text-decoration: none;">
						Find PIN Codes <i class="fas fa-arrow-right ml-1"></i>
					</a>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="feature-card-modern">
					<div class="feature-icon-box" style="background: #fef3c7; color: var(--accent-amber);">
						<i class="fas fa-building"></i>
					</div>
					<h5 class="font-weight-bold mb-2" style="font-size: 18px; color: var(--text-main);">Company Incorporation</h5>
					<p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">Explore MCA-registered companies, registration dates, state ROCs, authorized capital, and active incorporation profiles.</p>
					<a href="<?php echo $base_url; ?>company-incorporation" class="font-weight-bold" style="color: #d97706; font-size: 14px; text-decoration: none;">
						Explore Companies <i class="fas fa-arrow-right ml-1"></i>
					</a>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="feature-card-modern">
					<div class="feature-icon-box" style="background: #f3e8ff; color: var(--accent-purple);">
						<i class="fas fa-sitemap"></i>
					</div>
					<h5 class="font-weight-bold mb-2" style="font-size: 18px; color: var(--text-main);">NIC 2025 Activity Codes</h5>
					<p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">Access the National Industrial Classification (NIC 2025) hierarchy across Divisions, Groups, Classes, and Sub-classes for business registration.</p>
					<a href="<?php echo $base_url; ?>nic" class="font-weight-bold" style="color: var(--accent-purple); font-size: 14px; text-decoration: none;">
						View NIC Codes <i class="fas fa-arrow-right ml-1"></i>
					</a>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="feature-card-modern">
					<div class="feature-icon-box" style="background: #e0f2fe; color: var(--accent-cyan);">
						<i class="fas fa-file-invoice-dollar"></i>
					</div>
					<h5 class="font-weight-bold mb-2" style="font-size: 18px; color: var(--text-main);">GST HSN &amp; SAC</h5>
					<p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">Search official Harmonized System of Nomenclature (HSN) and Service Accounting Codes (SAC) tariffs for accurate GST invoicing.</p>
					<a href="<?php echo $base_url; ?>gst-hsn-sac" class="font-weight-bold" style="color: #0284c7; font-size: 14px; text-decoration: none;">
						GST HSN Finder <i class="fas fa-arrow-right ml-1"></i>
					</a>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 mb-4">
				<div class="feature-card-modern">
					<div class="feature-icon-box" style="background: #ffe4e6; color: var(--accent-rose);">
						<i class="fas fa-passport"></i>
					</div>
					<h5 class="font-weight-bold mb-2" style="font-size: 18px; color: var(--text-main);">Passport Seva &amp; Acts</h5>
					<p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">Find 784+ Passport Seva Kendra (PSK) &amp; POPSK offices across Indian states, along with constitutional Bare Acts directories.</p>
					<a href="<?php echo $base_url; ?>passport" class="font-weight-bold" style="color: var(--accent-rose); font-size: 14px; text-decoration: none;">
						Locate Offices <i class="fas fa-arrow-right ml-1"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Values & Commitment -->
<section style="background: #ffffff; padding: 70px 0; border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <span class="text-primary font-weight-bold" style="font-size: 13px; letter-spacing: 1px; text-transform: uppercase;">Our Core Principles</span>
                <h2 class="mt-2 mb-3" style="font-size: 28px; font-weight: 800; color: var(--text-main);">Designed With Accuracy, Speed &amp; Integrity</h2>
                <p style="font-size: 15px; color: var(--text-muted); line-height: 1.8;">
                    We believe essential public directory data should be available friction-free. We uphold four foundational pillars in everything we build:
                </p>
                
                <div class="mt-4">
                    <div class="d-flex mb-3">
                        <div class="mr-3 text-primary" style="font-size: 20px;"><i class="fas fa-bolt"></i></div>
                        <div>
                            <h6 class="font-weight-bold mb-1" style="font-size: 15px;">Sub-Second Search Latency</h6>
                            <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.6;">Optimized server indexing and smart caching ensure lookups complete in fractions of a second.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="mr-3 text-success" style="font-size: 20px;"><i class="fas fa-lock"></i></div>
                        <div>
                            <h6 class="font-weight-bold mb-1" style="font-size: 15px;">No Tracking &amp; Strict Privacy</h6>
                            <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.6;">We do not profile users, require sign-ups, or sell search patterns. Your searches stay private.</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="mr-3 text-warning" style="font-size: 20px;"><i class="fas fa-sync-alt"></i></div>
                        <div>
                            <h6 class="font-weight-bold mb-1" style="font-size: 15px;">Regular Official Updates</h6>
                            <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.6;">Databases are continuously synced against official gazettes, RBI records, MCA lists, and India Post data.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="p-4" style="background: var(--bg-body); border-radius: var(--radius-xl); border: 1px solid var(--border-soft);">
                    <h5 class="font-weight-bold mb-3" style="font-size: 17px; color: var(--text-main);">
                        <i class="fas fa-compass text-primary mr-2"></i> Quick Directory Shortcuts
                    </h5>
                    <div class="d-flex flex-column gap-2" style="gap: 10px;">
                        <a href="<?php echo $base_url; ?>bank" class="tool-explore-item">
                            <div class="item-icon" style="background: #eff6ff; color: var(--primary-color);"><i class="fas fa-university"></i></div>
                            <div class="flex-grow-1">
                                <div style="font-weight: 700; font-size: 14px; color: var(--text-main);">Bank Directory &amp; Helplines</div>
                                <div style="font-size: 12px; color: var(--text-muted);">Customer care, toll-free, balance check &amp; card support</div>
                            </div>
                            <i class="fas fa-chevron-right text-muted" style="font-size: 12px;"></i>
                        </a>
                        <a href="<?php echo $base_url; ?>pincode-by-locality" class="tool-explore-item">
                            <div class="item-icon" style="background: #ecfdf5; color: var(--accent-emerald);"><i class="fas fa-search-location"></i></div>
                            <div class="flex-grow-1">
                                <div style="font-weight: 700; font-size: 14px; color: var(--text-main);">PIN Code Locality Finder</div>
                                <div style="font-size: 12px; color: var(--text-muted);">Filter post offices by district, state, and village name</div>
                            </div>
                            <i class="fas fa-chevron-right text-muted" style="font-size: 12px;"></i>
                        </a>
                        <a href="<?php echo $base_url; ?>api" class="tool-explore-item">
                            <div class="item-icon" style="background: #fef3c7; color: var(--accent-amber);"><i class="fas fa-code"></i></div>
                            <div class="flex-grow-1">
                                <div style="font-weight: 700; font-size: 14px; color: var(--text-main);">Developer APIs</div>
                                <div style="font-size: 12px; color: var(--text-muted);">Integrate fast IFSC, PIN, and company search into your apps</div>
                            </div>
                            <i class="fas fa-chevron-right text-muted" style="font-size: 12px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Leadership & Team Note -->
<section class="py-5 bg-body" style="padding: 70px 0 !important;">
	<div class="container">
		<div class="row justify-content-center text-center mb-5">
			<div class="col-lg-8">
                <span class="text-primary font-weight-bold" style="font-size: 13px; letter-spacing: 1px; text-transform: uppercase;">Leadership &amp; Stewardship</span>
				<h2 class="mt-2 mb-3" style="font-size: 30px; font-weight: 800; color: var(--text-main);">Words from the Team</h2>
				<p class="text-muted" style="font-size: 15px;">Driven by a mission to engineer high-utility digital infrastructure for citizens and organizations.</p>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 mb-4">
				<div class="leadership-card">
                    <i class="fas fa-quote-left text-primary mb-3" style="font-size: 26px; opacity: 0.4;"></i>
                    <p class="mb-4" style="color: var(--text-muted); font-size: 15px; font-style: italic; line-height: 1.8;">
                        "Building Online Law is centered on one foundational truth: accurate civic and legal tools shouldn't be gated behind complex databases or subscription fees. We are dedicated to maintaining an open, fast, and verified repository that saves people time every single day."
                    </p>
                    <div class="d-flex align-items-center pt-3" style="border-top: 1px solid var(--border-soft);">
                        <img src="images/circle/gaurav.jpg" alt="K Gaurav" onerror="this.src='images/logo/logo.png';" style="width: 54px; height: 54px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-light);">
                        <div class="ml-3 pl-2">
                            <h5 class="m-0" style="font-size: 16px; font-weight: 700; color: var(--text-main);">K Gaurav</h5>
                            <span style="color: var(--primary-color); font-size: 13px; font-weight: 600;">Director, OfferPlant</span>
                        </div>
                    </div>
                </div>
			</div>
            <div class="col-lg-6 mb-4">
				<div class="leadership-card">
                    <i class="fas fa-quote-left text-primary mb-3" style="font-size: 26px; opacity: 0.4;"></i>
                    <p class="mb-4" style="color: var(--text-muted); font-size: 15px; font-style: italic; line-height: 1.8;">
                        "Our focus has been engineering resilience and speed at scale. By handling millions of records spanning MCA corporations, postal pins, and banking codes, we allow developers and businesses to integrate critical reference data seamlessly into their daily operations."
                    </p>
                    <div class="d-flex align-items-center pt-3" style="border-top: 1px solid var(--border-soft);">
                        <img src="images/circle/saurabh.jpg" alt="S Nagbanshi" onerror="this.src='images/logo/logo.png';" style="width: 54px; height: 54px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-light);">
                        <div class="ml-3 pl-2">
                            <h5 class="m-0" style="font-size: 16px; font-weight: 700; color: var(--text-main);">S Nagbanshi</h5>
                            <span style="color: var(--primary-color); font-size: 13px; font-weight: 600;">CEO, OfferPlant</span>
                        </div>
                    </div>
                </div>
			</div>
		</div>
	</div>
</section>

<!-- Call to Action Banner -->
<section style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%); padding: 50px 0;">
    <div class="container text-center">
        <h2 class="text-white mb-2" style="font-size: 28px; font-weight: 800;">Ready to Search India's Public Directory?</h2>
        <p class="text-white-50 mb-4" style="font-size: 15px; max-width: 600px; margin: 0 auto 24px;">Find verified Bank IFSC, PIN Codes, Company records, and GST tariffs in seconds.</p>
        <div class="d-inline-flex flex-wrap gap-2 justify-content-center" style="gap: 12px;">
            <a href="<?php echo $base_url; ?>index" class="btn btn-light" style="padding: 10px 24px; border-radius: var(--radius-md); font-weight: 700; color: var(--primary-color);">
                <i class="fas fa-home mr-2"></i> Go to Homepage
            </a>
            <a href="<?php echo $base_url; ?>contact" class="btn btn-outline-light" style="padding: 10px 24px; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fas fa-paper-plane mr-2"></i> Contact Us
            </a>
        </div>
    </div>
</section>

<?php require_once ('includes/footer.php'); ?> 
