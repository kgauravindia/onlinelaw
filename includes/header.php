<?php 
/**
 * Online Law - Global Header Include
 * Modernized 2026 Design with Cascading Tool Menus & SEO Tags
 */
require_once (__DIR__ . '/config.php'); 
require_once (__DIR__ . '/function.php'); 

$ga_id = get_setting('google_analytics_id', 'G-TDGJS08YY4');
$ga_enabled = get_setting('google_analytics_enabled', '1');

$adsense_id = get_setting('google_adsense_id', 'ca-pub-1272970612043134');
$adsense_enabled = get_setting('google_adsense_enabled', '1');

$header_custom_code = get_setting('header_custom_code', '');
$g_site_verification = get_setting('google_site_verification', '');
$bing_site_verification = get_setting('bing_site_verification', '');
$current_page = basename($_SERVER['PHP_SELF'] ?? '', '.php');
?> 
<link rel="icon" type="image/png" href="<?php echo $base_url; ?>images/favicon.png">
<link rel="canonical" href="<?php echo !empty($canonical_url) ? htmlspecialchars($canonical_url) : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>">
<meta name="robots" content="<?php echo !empty($meta_robots) ? htmlspecialchars($meta_robots) : 'index, follow'; ?>">
<?php if (!empty($g_site_verification)): ?>
<!-- Google Search Console Verification -->
<meta name="google-site-verification" content="<?php echo htmlspecialchars($g_site_verification); ?>" />
<?php endif; ?>
<?php if (!empty($bing_site_verification)): ?>
<!-- Bing Webmaster Verification -->
<meta name="msvalidate.01" content="<?php echo htmlspecialchars($bing_site_verification); ?>" />
<?php endif; ?>
<!-- DNS Prefetch & Preconnect for High Performance -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="https://cdn.datatables.net">
<link rel="dns-prefetch" href="https://code.jquery.com">

<!-- Google Fonts: Plus Jakarta Sans, Inter & JetBrains Mono with font-display swap -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

<?php
$css_ver = '2026110502';
$style_ver = '2026110502';
$resp_ver = '2026110502';
try {
    $c_path = __DIR__ . '/../css/custom-modern.css';
    if (@file_exists($c_path)) {
        $m = @filemtime($c_path);
        if ($m) $css_ver = (string)$m;
    }
    $s_path = __DIR__ . '/../css/style.css';
    if (@file_exists($s_path)) {
        $m = @filemtime($s_path);
        if ($m) $style_ver = (string)$m;
    }
    $r_path = __DIR__ . '/../css/responsive.css';
    if (@file_exists($r_path)) {
        $m = @filemtime($r_path);
        if ($m) $resp_ver = (string)$m;
    }
} catch (Throwable $e) {}
?>
<!-- Optimized Core CSS Stylesheets with Cache Busting -->
<link rel="stylesheet" href="<?php echo $base_url; ?>css/bootstrap-4.1.3.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css?v=<?php echo $style_ver; ?>">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/responsive.css?v=<?php echo $resp_ver; ?>">
<!-- Non-blocking DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" media="print" onload="this.media='all'">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css" media="print" onload="this.media='all'">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/custom-modern.css?v=<?php echo $css_ver; ?>">
    
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<![endif]-->
<?php if (!empty($adsense_id) && $adsense_enabled == '1'): ?>
<!-- Google AdSense -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo htmlspecialchars($adsense_id); ?>" crossorigin="anonymous"></script>
<?php endif; ?>
<?php if (!empty($ga_id) && $ga_enabled == '1'): ?>
<!-- Google tag (gtag.js) / Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($ga_id); ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?php echo htmlspecialchars($ga_id); ?>');
</script>
<?php endif; ?>
<?php 
if (!empty($header_custom_code)) {
    if (function_exists('sanitize_custom_code')) {
        $header_custom_code = sanitize_custom_code($header_custom_code);
    }
    if (!empty(trim($header_custom_code))) {
        echo "<!-- Custom Header Code -->\n" . $header_custom_code . "\n";
    }
}
?>
</head>

<body>
<div id="page-wrapper">
	<header id="page-header">
	  <div id="header-top" class="py-1">
		<div class="container">
		  <div class="row align-items-center">
			<div class="col-md-6 col-12 text-center text-md-left">
				<a href="mailto:<?php echo $inst_email; ?>" class="d-inline-flex align-items-center text-decoration-none">
					<i class="fas fa-envelope mr-2" style="color: var(--primary-color);"></i> <?php echo $inst_email; ?>
				</a>
			</div>
			<div class="col-md-6 col-12 text-center text-md-right mt-2 mt-md-0">
			  <ul class="social-media-1 m-0 p-0 d-inline-flex align-items-center justify-content-center justify-content-md-end" style="list-style: none; gap: 6px;">
				<li><a href="<?php echo $facebook; ?>" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a> </li>
				<li><a href="<?php echo $twitter; ?>" target="_blank" title="Twitter / X"><i class="fab fa-twitter"></i></a> </li>
				<li><a href="<?php echo $linkedin; ?>" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a> </li>
				<li><a href="<?php echo $instagram; ?>" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a> </li>
				<li><a href="<?php echo $youtube; ?>" target="_blank" title="YouTube"><i class="fab fa-youtube"></i></a> </li>
				<li><a href="<?php echo $pinterest; ?>" target="_blank" title="Pinterest"><i class="fab fa-pinterest-p"></i></a> </li>
			  </ul>
			</div>
		  </div>
		</div>
	  </div>
	  <div id="main-header" class="position-relative">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-12">
						<nav class="navbar navbar-expand-lg navbar-light w-100 p-0 d-flex align-items-center justify-content-between"> 
							<a class="navbar-brand py-1 d-flex align-items-center" href="<?php echo $base_url; ?>index">
								<div class="brand-icon-badge mr-2">
									<i class="fas fa-balance-scale"></i>
								</div>
								<span><span style="color: var(--primary-color);">Online</span>Law</span>
							</a>

						<!-- Quick Search Trigger & Mobile Toggle Wrap -->
						<div class="d-flex align-items-center ml-auto ml-lg-0 order-lg-2">
							<button type="button" class="header-search-trigger mr-2 d-none d-sm-inline-flex" id="globalSearchTrigger" title="Quick Search (Ctrl + K)">
								<i class="fas fa-search text-primary mr-2" style="font-size: 13px;"></i>
								<span class="search-label">Quick Search...</span>
								<kbd class="cmd-kbd-badge">Ctrl K</kbd>
							</button>
							<button type="button" class="btn btn-sm btn-light border d-inline-flex d-sm-none mr-2 p-2 align-items-center justify-content-center" id="globalSearchTriggerMobile" aria-label="Search" style="border-radius: 10px; width: 38px; height: 38px;">
								<i class="fas fa-search text-primary"></i>
							</button>
							<button class="navbar-toggler border-0 shadow-none p-2 ml-1" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"> 
								<span class="fas fa-bars" style="font-size: 20px; color: var(--text-main);"></span> 
							</button>
						</div>

						<div class="collapse navbar-collapse order-lg-1" id="navbarSupportedContent">
						  <ul class="navbar-nav ml-auto align-items-lg-center">
							<li class="nav-item <?php echo ($current_page == 'index' || empty($current_page)) ? 'active' : ''; ?>"> <a class="nav-link" href="<?php echo $base_url; ?>index"><i class="fas fa-home d-lg-none mr-2"></i>Home</a> </li>
							<li class="nav-item <?php echo ($current_page == 'about') ? 'active' : ''; ?>"> <a class="nav-link" href="<?php echo $base_url; ?>about"><i class="fas fa-info-circle d-lg-none mr-2"></i>About Us</a> </li>
							<li class="nav-item dropdown <?php echo in_array($current_page, ['company-incorporation', 'nic', 'pincode-finder', 'pincode', 'pincode-by-locality', 'gst-hsn-sac', 'passport', 'acts']) ? 'active' : ''; ?>"> 
								<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="fas fa-search d-lg-none mr-2"></i>Search Tools
								</a>
							  <ul class="dropdown-menu">
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>company-incorporation">
										<span><i class="fas fa-building text-primary mr-2" style="width: 16px;"></i>Company Incorporation</span>
										<span class="nav-badge-pill nav-badge-new">NEW</span>
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>nic">
										<span><i class="fas fa-sitemap text-warning mr-2" style="width: 16px;"></i>NIC 2025 Activity Codes</span>
										<span class="nav-badge-pill nav-badge-new">NEW</span>
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>pincode-finder">
										<span><i class="fas fa-map-marked-alt text-success mr-2" style="width: 16px;"></i>PIN Code Finder (State &amp; District)</span>
										<span class="nav-badge-pill nav-badge-new">NEW</span>
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>pincode">
										<span><i class="fas fa-map-pin text-info mr-2" style="width: 16px;"></i>PIN Code by Number</span>
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>pincode-by-locality">
										<span><i class="fas fa-search-location text-info mr-2" style="width: 16px;"></i>PIN Code by Locality</span>
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>gst-hsn-sac">
										<span><i class="fas fa-file-invoice-dollar text-purple mr-2" style="width: 16px; color: var(--accent-purple);"></i>GST HSN & SAC</span>
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>passport">
										<span><i class="fas fa-passport text-danger mr-2" style="width: 16px;"></i>Passport Office Locator</span>
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>acts">
										<span><i class="fas fa-gavel text-secondary mr-2" style="width: 16px;"></i>Bare Acts Directory</span>
									</a>
								</li>
							  </ul>
							</li>
							<li class="nav-item dropdown <?php echo in_array($current_page, ['bank-finder', 'bank-ifsc', 'bank-ifsc-by-branch-name', 'bank-micr', 'bank']) ? 'active' : ''; ?>"> 
								<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="fas fa-landmark d-lg-none mr-2"></i>Bank &amp; IFSC
								</a>
							  <ul class="dropdown-menu">
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>bank-finder">
										<span><i class="fas fa-landmark text-success mr-2" style="width: 16px;"></i>Bank Finder (Bank, State &amp; Branch)</span>
										<span class="nav-badge-pill nav-badge-new">NEW</span>
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>bank-ifsc">
										<span><i class="fas fa-university text-primary mr-2" style="width: 16px;"></i>Bank IFSC Search</span>
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>bank-ifsc-by-branch-name">
										<span><i class="fas fa-code-branch text-warning mr-2" style="width: 16px;"></i>Bank IFSC by Branch Name</span>
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>bank-micr">
										<span><i class="fas fa-money-check-alt text-info mr-2" style="width: 16px;"></i>Bank MICR Codes</span>
									</a>
								</li>
								<div class="dropdown-divider my-1"></div>
								<li><a class="dropdown-item" href="<?php echo $base_url; ?>bank"><span><i class="fas fa-book text-muted mr-2" style="width: 16px;"></i>Bank Services Directory</span></a></li>
								<li><a class="dropdown-item" href="<?php echo $base_url; ?>bank/services"><span><i class="fas fa-mobile-alt text-success mr-2" style="width: 16px;"></i>Digital Banking Services</span></a></li>
								<li><a class="dropdown-item" href="<?php echo $base_url; ?>bank/card"><span><i class="fas fa-credit-card text-warning mr-2" style="width: 16px;"></i>Card Helplines</span></a></li>
								<li><a class="dropdown-item" href="<?php echo $base_url; ?>bank/account"><span><i class="fas fa-phone-volume text-info mr-2" style="width: 16px;"></i>Balance Check Numbers</span></a></li>
								<li><a class="dropdown-item" href="<?php echo $base_url; ?>bank/contact-details"><span><i class="fas fa-address-book text-secondary mr-2" style="width: 16px;"></i>Official Bank Contacts</span></a></li>
							  </ul>
							</li>
							<li class="nav-item <?php echo ($current_page == 'api') ? 'active' : ''; ?>"> <a class="nav-link" href="<?php echo $base_url; ?>api"><i class="fas fa-code d-lg-none mr-2"></i>API</a> </li>
							<li class="nav-item <?php echo ($current_page == 'contact') ? 'active' : ''; ?>"> <a class="nav-link" href="<?php echo $base_url; ?>contact"><i class="fas fa-envelope d-lg-none mr-2"></i>Contact</a> </li>
						  </ul>
						</div>
					  </nav>
					</div>
				</div>
			</div>
		</div>
	</header>
