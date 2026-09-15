<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
    // Set include path to root directory so that relative requires in includes/header.php and function.php work correctly
    set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__DIR__));
    require_once('includes/config.php');

    // Since this script is in a subdirectory, if we are on localhost, $base_url will end in '/bank/'
    // We need to strip the '/bank/' from $base_url to correctly point to the root path
    if (isset($base_url)) {
        $base_url = preg_replace('/bank\/$/', '', $base_url);
    }

    require_once ('includes/function.php'); 
    
    $page_title = "Debit & Credit Card Guide & Bank Blocking Helplines | Online Law";
    $meta_desc = "Complete guide to Debit and Credit cards in India. Find emergency bank-wise card blocking toll-free hotlines, card network comparisons (RuPay, Visa, Mastercard), and safety practices.";
?>
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="block credit card, debit card safety, rupay visa mastercard, bank toll free numbers, lost card hotline, secure card payments">
<meta name="author" content="OfferPlant">
<title><?php echo $page_title; ?></title>

<link rel="stylesheet" href="<?php echo $base_url; ?>css/bank.css">

<?php require_once ('includes/header.php'); ?> 

<!-- Banner Section Start -->
<section class="bg-img-3 overlay p-0 full-row" style="padding: 20px 0 !important;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="banner-text text-center">
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Card Services</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>bank" style="color: rgba(255,255,255,0.8); text-decoration: none;">Bank Directory</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">Card Services</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Banner Section End --> 

<section class="full-row bg-body py-80" style="padding: 60px 0;">
	<div class="container">
		
		<!-- Introduction -->
		<div class="row mb-5 text-center">
			<div class="col-lg-10 mx-auto">
				<h3 style="font-family: var(--font-heading); font-size: 28px; color: var(--text-main); font-weight: 700; margin-bottom: 15px;">Debit & Credit Card Guide</h3>
				<p style="color: var(--text-muted); font-size: 16px; line-height: 1.7;">Payment cards are critical instruments in modern transactions. This guide provides necessary details on Debit and Credit cards, comparison of major card networks (RuPay, Visa, and Mastercard), best safety practices, and critical hotline information to block lost cards instantly.</p>
			</div>
		</div>
		
		<!-- 3 Columns Content Grid -->
		<div class="row mb-5">
			
			<!-- Card Networks -->
			<div class="col-lg-4 mb-4">
				<div class="feature-card">
					<div class="card-header-custom">
						<div class="card-icon"><i class="fas fa-network-wired"></i></div>
						<h4 class="card-title-custom">Card Networks</h4>
					</div>
					<div class="card-body-custom">
						Banks issue cards on different payment processing networks:
						<ul class="pl-4 mt-3">
							<li><b>RuPay:</b> Domestic network developed by NPCI. Features lower transaction costs and custom benefits for Indian consumers.</li>
							<li><b>Visa / Mastercard:</b> Global payment processing systems with wide international acceptance and premium rewards programs.</li>
						</ul>
					</div>
				</div>
			</div>
			
			<!-- Safety Measures -->
			<div class="col-lg-4 mb-4">
				<div class="feature-card">
					<div class="card-header-custom">
						<div class="card-icon"><i class="fas fa-shield-alt"></i></div>
						<h4 class="card-title-custom">Safety Practices</h4>
					</div>
					<div class="card-body-custom">
						Protect your card information from fraud:
						<ul class="pl-4 mt-3">
							<li>Never share your Card PIN, CVV, or OTP with anyone, including bank employees.</li>
							<li>Enable daily transaction limits and disable international transactions when not traveling.</li>
							<li>Avoid clicking on suspicious SMS or email links claiming your card is blocked.</li>
						</ul>
					</div>
				</div>
			</div>
			
			<!-- Contactless Payments -->
			<div class="col-lg-4 mb-4">
				<div class="feature-card">
					<div class="card-header-custom">
						<div class="card-icon"><i class="fas fa-wifi"></i></div>
						<h4 class="card-title-custom">Contactless / NCMC</h4>
					</div>
					<div class="card-body-custom">
						Modern banking cards support RFID payments:
						<ul class="pl-4 mt-3">
							<li>Allows tap-and-pay transactions up to Rs. 5,000 without requiring a PIN.</li>
							<li>National Common Mobility Card (NCMC) acts as a unified card for metro travel, buses, parking, and shopping across India.</li>
						</ul>
					</div>
				</div>
			</div>
			
		</div>
		
		<!-- Emergency Card Blocking Helpline Numbers -->
		<div class="row mb-5 pt-4">
			<div class="col-lg-12">
				<div class="detail-area p-3 p-md-5" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); background: #fff;">
					<div class="d-flex align-items-center mb-4">
						<div style="width: 40px; height: 40px; background: #fee2e2; color: #dc2626; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-size: 18px;">
							<i class="fas fa-phone-slash"></i>
						</div>
						<div>
							<h4 class="mb-0" style="font-family: var(--font-heading); font-weight: 700; color: #1e293b; font-size: 20px;">Emergency Card Blocking Helplines</h4>
							<p class="mb-0 text-muted" style="font-size: 13px;">If you have lost your card, call these toll-free numbers immediately to block it.</p>
						</div>
					</div>
					
					<div class="table-responsive">
						<table class="table-hotline">
							<thead>
								<tr>
									<th>Bank Name</th>
									<th>Toll-Free Helpline Numbers</th>
									<th>Alternate/International Numbers</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><b>State Bank of India (SBI)</b></td>
									<td><a href="tel:1800112211" class="text-primary font-weight-bold">1800 11 2211</a> / <a href="tel:18004253800" class="text-primary font-weight-bold">1800 425 3800</a></td>
									<td>080-26599990</td>
								</tr>
								<tr>
									<td><b>HDFC Bank</b></td>
									<td><a href="tel:18002026161" class="text-primary font-weight-bold">1800 202 6161</a> / <a href="tel:18602676161" class="text-primary font-weight-bold">1860 267 6161</a></td>
									<td>022-61606161</td>
								</tr>
								<tr>
									<td><b>ICICI Bank</b></td>
									<td><a href="tel:18001080" class="text-primary font-weight-bold">1800 1080</a></td>
									<td>022-33667777</td>
								</tr>
								<tr>
									<td><b>Punjab National Bank (PNB)</b></td>
									<td><a href="tel:18001802222" class="text-primary font-weight-bold">1800 180 2222</a> / <a href="tel:18001032222" class="text-primary font-weight-bold">1800 103 2222</a></td>
									<td>0120-2490000</td>
								</tr>
								<tr>
									<td><b>Bank of Baroda (BOB)</b></td>
									<td><a href="tel:18002584455" class="text-primary font-weight-bold">1800 258 4455</a> / <a href="tel:18001024455" class="text-primary font-weight-bold">1800 102 4455</a></td>
									<td>022-67511111</td>
								</tr>
								<tr>
									<td><b>Axis Bank</b></td>
									<td><a href="tel:18604195555" class="text-primary font-weight-bold">1860 419 5555</a> / <a href="tel:18605005555" class="text-primary font-weight-bold">1860 500 5555</a></td>
									<td>022-67987700</td>
								</tr>
								<tr>
									<td><b>Canara Bank</b></td>
									<td><a href="tel:18004250018" class="text-primary font-weight-bold">1800 425 0018</a></td>
									<td>080-25440629</td>
								</tr>
								<tr>
									<td><b>Union Bank of India</b></td>
									<td><a href="tel:1800222244" class="text-primary font-weight-bold">1800 22 22 44</a> / <a href="tel:18002082244" class="text-primary font-weight-bold">1800 208 2244</a></td>
									<td>022-25231022</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<div class="mt-4 p-3 bg-light rounded" style="border-left: 4px solid var(--primary-color);">
						<p class="mb-2 small text-dark"><i class="fas fa-info-circle text-primary mr-1"></i> <b>Pro-Tip:</b> You can also block your card instantly via your bank's Net Banking portal, Mobile App, or by sending a pre-formatted SMS to the bank's official banking number from your registered mobile phone.</p>
						<p class="mb-0 small text-dark"><i class="fas fa-exclamation-triangle text-warning mr-1"></i> <b>Disclaimer:</b> Bank contact numbers, email addresses, and official websites are subject to change. Please verify any critical details with your bank's official website or your printed passbook/chequebook.</p>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Call to Action -->
		<div class="row justify-content-center text-center mt-4">
			<div class="col-lg-8">
				<div style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.15); border-radius: 16px; padding: 30px;">
					<h4 style="font-family: var(--font-heading); font-weight: 700; color: var(--text-main); margin-bottom: 10px;">Need to Find Bank Details?</h4>
					<p class="text-muted" style="font-size: 14.5px; margin-bottom: 20px;">Locate official bank contact numbers, branch addresses, and IFSC codes quickly using our free lookup tool.</p>
					<div class="d-flex justify-content-center flex-wrap">
						<a href="<?php echo $base_url; ?>bank" class="btn btn-primary m-2"><i class="fas fa-university mr-2"></i> Bank Directory</a>
						<a href="<?php echo $base_url; ?>bank-ifsc" class="btn btn-secondary m-2"><i class="fas fa-search mr-2"></i> Find IFSC Code</a>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</section>

<?php require_once ('includes/footer.php'); ?> 
