<?php require_once ('includes/config.php'); ?> 
<link rel="icon" type="image/png" href="<?php echo $base_url; ?>images/favicon.png">
<link rel="canonical" href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
<meta name="robots" content="index, follow">
<!-- Google Fonts: Plus Jakarta Sans & Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<!-- Links of css files -->
<link rel="stylesheet" href="<?php echo $base_url; ?>css/bootstrap-4.1.3.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/fontawesome-all-5.5.0.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/jquery.fancybox.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/animate.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/owl.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>webfonts/flaticon/flaticon.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/responsive.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/custom-modern.css">
    
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<![endif]-->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1272970612043134"
     crossorigin="anonymous"></script>
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
						<button class="navbar-toggler border-0 shadow-none p-2" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"> 
							<span class="fas fa-bars" style="font-size: 20px; color: var(--text-main);"></span> 
						</button>
						<div class="collapse navbar-collapse" id="navbarSupportedContent">
						  <ul class="navbar-nav ml-auto align-items-lg-center">
							<li class="nav-item"> <a class="nav-link" href="<?php echo $base_url; ?>index"><i class="fas fa-home d-lg-none mr-2"></i>Home</a> </li>
							<li class="nav-item"> <a class="nav-link" href="<?php echo $base_url; ?>about"><i class="fas fa-info-circle d-lg-none mr-2"></i>About Us</a> </li>
							<li class="nav-item dropdown"> 
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
									<a class="dropdown-item" href="<?php echo $base_url; ?>bank-ifsc">
										<span><i class="fas fa-university text-warning mr-2" style="width: 16px;"></i>Bank IFSC</span>
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>bank-ifsc-by-branch-name">
										<span><i class="fas fa-code-branch text-warning mr-2" style="width: 16px;"></i>Bank IFSC by Branch</span>
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="<?php echo $base_url; ?>bank-micr">
										<span><i class="fas fa-money-check-alt text-success mr-2" style="width: 16px;"></i>Bank MICR Codes</span>
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
							<li class="nav-item dropdown"> 
								<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="fas fa-landmark d-lg-none mr-2"></i>Bank Directory
								</a>
							  <ul class="dropdown-menu">
								<li><a class="dropdown-item" href="<?php echo $base_url; ?>bank"><span><i class="fas fa-university text-primary mr-2" style="width: 16px;"></i>Directory Home</span></a></li>
								<li><a class="dropdown-item" href="<?php echo $base_url; ?>bank/services"><span><i class="fas fa-mobile-alt text-success mr-2" style="width: 16px;"></i>Digital Banking Services</span></a></li>
								<li><a class="dropdown-item" href="<?php echo $base_url; ?>bank/card"><span><i class="fas fa-credit-card text-warning mr-2" style="width: 16px;"></i>Card Helplines</span></a></li>
								<li><a class="dropdown-item" href="<?php echo $base_url; ?>bank/account"><span><i class="fas fa-phone-volume text-info mr-2" style="width: 16px;"></i>Balance Check Numbers</span></a></li>
								<li><a class="dropdown-item" href="<?php echo $base_url; ?>bank/contact-details"><span><i class="fas fa-address-book text-secondary mr-2" style="width: 16px;"></i>Official Bank Contacts</span></a></li>
							  </ul>
							</li>
							<li class="nav-item"> <a class="nav-link" href="<?php echo $base_url; ?>api"><i class="fas fa-code d-lg-none mr-2"></i>API</a> </li>
							<li class="nav-item"> <a class="nav-link" href="<?php echo $base_url; ?>contact"><i class="fas fa-envelope d-lg-none mr-2"></i>Contact</a> </li>
						  </ul>
						</div>
					  </nav>
					</div>
				</div>
			</div>
		</div>
	</header>
