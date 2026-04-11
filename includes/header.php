<?php require_once ('includes/config.php'); ?> 
<link rel="icon" type="image/png" href="<?php echo $base_url; ?>images/favicon.png">
<link rel="canonical" href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
<meta name="robots" content="index, follow">
<!-- Links of css files -->
<link rel="stylesheet" href="<?php echo $base_url; ?>css/bootstrap-4.1.3.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/fontawesome-all-5.5.0.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/jquery.fancybox.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/animate.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/owl.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>webfonts/flaticon/flaticon.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/layerslider.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/responsive.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/custom-modern.css">
    
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<![endif]-->
</head>

<body>
<div id="page-wrapper">
	<div class="row">
		<header id="page-header">
		  <div id="header-top" class="py-10">
			<div class="container">
			  <div class="row align-items-center">
				<div class="col-md-6 col-sm-12">
				  <ul class="line-menu m-0 p-0" style="list-style: none;">
					<li class="float-left"><a href="mailto:<?php echo $inst_email; ?>" style="color: #94a3b8; font-weight: 500; font-size: 14px; text-decoration: none; transition: color 0.3s;"><i class="fas fa-envelope" style="color: var(--primary-color); margin-right: 8px;"></i> <?php echo $inst_email; ?></a></li>
				  </ul>
				</div>
				<div class="col-md-6 col-sm-12">
				  <ul class="social-media-1 float-md-right float-sm-left mt-2 mt-md-0 m-0 p-0" style="list-style: none;">
					<li class="mr-30 d-inline-block"><a href="<?php echo $facebook; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a> </li>
					<li class="mr-30 d-inline-block"><a href="<?php echo $twitter; ?>" target="_blank"><i class="fab fa-twitter"></i></a> </li>
					<li class="mr-30 d-inline-block"><a href="<?php echo $linkedin; ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a> </li>
					<li class="mr-30 d-inline-block"><a href="<?php echo $instagram; ?>" target="_blank"><i class="fab fa-instagram"></i></a> </li>
					<li class="mr-30 d-inline-block"><a href="<?php echo $youtube; ?>" target="_blank"><i class="fab fa-youtube"></i></a> </li>
					<li class="mr-30 d-inline-block"><a href="<?php echo $pinterest; ?>" target="_blank"><i class="fab fa-pinterest-p"></i></a> </li>
				  </ul>
				</div>
			  </div>
			</div>
		  </div>
		  <div id="main-header" class="position-relative">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<nav class="navbar navbar-expand-lg navbar-light w-100 p-0"> 
                                <a class="navbar-brand" href="<?php echo $base_url; ?>index" style="font-family: var(--font-heading); font-size: 26px; font-weight: 800; color: var(--text-main); text-decoration: none; letter-spacing: -0.5px; padding: 15px 0;">
                                    <i class="fas fa-balance-scale text-primary mr-1"></i> <span style="color: var(--primary-color);">Online</span> Law
                                </a>
							<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"> <i class="fas fa-bars"></i> </button>
							<div class="collapse navbar-collapse" id="navbarSupportedContent">
							  <ul class="navbar-nav ml-auto">
								<li class="nav-item"> <a class="nav-link" href="<?php echo $base_url; ?>index">Home</a> </li>
								<li class="nav-item"> <a class="nav-link" href="<?php echo $base_url; ?>about">About Us</a> </li>
								<li class="nav-item dropdown"> <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Search Tools</a>
								  <ul class="dropdown-menu">
									<li><a class="dropdown-item" href="<?php echo $base_url; ?>pincode-by-locality">PIN Code by Name</a></li>
									<li><a class="dropdown-item" href="<?php echo $base_url; ?>pincode">PIN Code by Number</a></li>
									<li><a class="dropdown-item" href="<?php echo $base_url; ?>bank-ifsc">Bank IFSC</a></li>
									<li><a class="dropdown-item" href="<?php echo $base_url; ?>bank-ifsc-by-branch-name">Bank IFSC by Branch</a></li>
									<li><a class="dropdown-item" href="<?php echo $base_url; ?>gst-hsn-sac">GST HSN & SAC Finder</a></li>
									<li><a class="dropdown-item" href="<?php echo $base_url; ?>passport">Passport Office Finder</a></li>
									<li><a class="dropdown-item" href="<?php echo $base_url; ?>acts">Bare Acts</a></li>
								  </ul>
								</li>
								<li class="nav-item"> <a class="nav-link" href="<?php echo $base_url; ?>api">Developer API</a> </li>
								<li class="nav-item"> <a class="nav-link" href="<?php echo $base_url; ?>contact">Contact</a> </li>
							  </ul>
							</div>
						  </nav>
						</div>
					</div>
				</div>
			</div>
		</header>
