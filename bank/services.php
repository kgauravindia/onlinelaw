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
    
    $page_title = "Digital Banking Services Guide (NEFT, RTGS, IMPS & UPI) | Online Law";
    $meta_desc = "Learn about various digital banking services and electronic fund transfer methods in India. Compare timings, transaction limits, and charges for NEFT, RTGS, IMPS, and UPI.";
?>
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="NEFT transfer, RTGS limits, IMPS timing, UPI payment, digital banking India, bank fund transfer methods, online payments RBI">
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
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Banking Services</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>bank" style="color: rgba(255,255,255,0.8); text-decoration: none;">Bank Directory</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">Banking Services</li>
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
				<h3 style="font-family: var(--font-heading); font-size: 28px; color: var(--text-main); font-weight: 700; margin-bottom: 15px;">Overview of Digital Banking Services</h3>
				<p style="color: var(--text-muted); font-size: 16px; line-height: 1.7;">In India, online bank transfers are facilitated primarily by four highly robust networks regulated by the Reserve Bank of India (RBI) and the National Payments Corporation of India (NPCI). Discover the key characteristics, limits, and processing speeds of these modern banking services.</p>
			</div>
		</div>
		
		<!-- Services Cards Grid -->
		<div class="row mb-5">
			
			<!-- NEFT -->
			<div class="col-lg-6 mb-4">
				<div class="service-card">
					<div class="service-header">
						<div class="service-icon"><i class="fas fa-exchange-alt"></i></div>
						<div>
							<h4 class="service-title">NEFT</h4>
							<span class="text-muted small">National Electronic Funds Transfer</span>
						</div>
					</div>
					<div class="service-body">
						NEFT is a nationwide electronic funds transfer system that facilitates one-to-one money transfers. Under this scheme, individuals and corporates can electronically transfer funds from any bank branch to any other bank branch participating in the NEFT network. 
						<ul class="pl-4 mt-3">
							<li><b>Settlement:</b> Settled in half-hourly batches throughout the day.</li>
							<li><b>Timings:</b> Available 24x7x365, including holidays.</li>
							<li><b>Limits:</b> No minimum or maximum limit set by the RBI (individual banks may have transaction caps).</li>
						</ul>
					</div>
				</div>
			</div>
			
			<!-- RTGS -->
			<div class="col-lg-6 mb-4">
				<div class="service-card">
					<div class="service-header">
						<div class="service-icon"><i class="fas fa-file-invoice"></i></div>
						<div>
							<h4 class="service-title">RTGS</h4>
							<span class="text-muted small">Real Time Gross Settlement</span>
						</div>
					</div>
					<div class="service-body">
						RTGS is designed for high-value financial transactions that require instant settlement. The transaction is processed individually on an continuous (real-time) basis throughout the RTGS business hours.
						<ul class="pl-4 mt-3">
							<li><b>Settlement:</b> Real-time, final, and irrevocable settlement in the RBI books.</li>
							<li><b>Timings:</b> Operational 24x7x365.</li>
							<li><b>Limits:</b> Minimum transaction value is <b>Rs. 2,00,000</b>. No maximum limit exists.</li>
						</ul>
					</div>
				</div>
			</div>
			
			<!-- IMPS -->
			<div class="col-lg-6 mb-4">
				<div class="service-card">
					<div class="service-header">
						<div class="service-icon"><i class="fas fa-bolt"></i></div>
						<div>
							<h4 class="service-title">IMPS</h4>
							<span class="text-muted small">Immediate Payment Service</span>
						</div>
					</div>
					<div class="service-body">
						IMPS is an instant real-time electronic fund transfer service managed by the NPCI. It can be accessed via mobile phones, internet banking, or ATMs. IMPS is primarily used for small, immediate money transfers.
						<ul class="pl-4 mt-3">
							<li><b>Settlement:</b> Instant, 24/7 transaction processing.</li>
							<li><b>Timings:</b> Available 24x7x365.</li>
							<li><b>Limits:</b> Minimum Rs. 1. Maximum limit is <b>Rs. 5,00,000</b> per transaction.</li>
						</ul>
					</div>
				</div>
			</div>
			
			<!-- UPI -->
			<div class="col-lg-6 mb-4">
				<div class="service-card">
					<div class="service-header">
						<div class="service-icon"><i class="fas fa-mobile-alt"></i></div>
						<div>
							<h4 class="service-title">UPI</h4>
							<span class="text-muted small">Unified Payments Interface</span>
						</div>
					</div>
					<div class="service-body">
						UPI merges multiple banking services, seamless fund routing, and merchant payments into a single mobile application. It allows instant transfers using a Virtual Payment Address (VPA) or QR codes without disclosing bank details.
						<ul class="pl-4 mt-3">
							<li><b>Settlement:</b> Real-time instant peer-to-peer (P2P) transfers.</li>
							<li><b>Timings:</b> Available 24x7x365.</li>
							<li><b>Limits:</b> Generally up to <b>Rs. 1,00,000</b> per transaction/day (varies by bank/app).</li>
						</ul>
					</div>
				</div>
			</div>
			
		</div>
		
		<!-- Comparison Section -->
		<div class="row mb-5 pt-4">
			<div class="col-lg-12">
				<div class="detail-area p-3 p-md-5" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); background: #fff;">
					<h3 class="text-center mb-4" style="font-family: var(--font-heading); font-weight: 700;">Service Comparison Matrix</h3>
					
					<div class="table-responsive">
						<table class="table-comparison">
							<thead>
								<tr>
									<th>Feature</th>
									<th>NEFT</th>
									<th>RTGS</th>
									<th>IMPS</th>
									<th>UPI</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Minimum Amount</td>
									<td>Rs. 1</td>
									<td>Rs. 2,00,000</td>
									<td>Rs. 1</td>
									<td>Rs. 1</td>
								</tr>
								<tr>
									<td>Maximum Amount</td>
									<td>No Limit (set by banks)</td>
									<td>No Limit</td>
									<td>Rs. 5,00,000</td>
									<td>Rs. 1,00,000 (standard limit)</td>
								</tr>
								<tr>
									<td>Settlement Mode</td>
									<td>Batch settlement</td>
									<td>Real-time gross basis</td>
									<td>Instant real-time</td>
									<td>Instant peer-to-peer</td>
								</tr>
								<tr>
									<td>Availability</td>
									<td>24x7x365</td>
									<td>24x7x365</td>
									<td>24x7x365</td>
									<td>24x7x365</td>
								</tr>
								<tr>
									<td>Online Transfer Charges</td>
									<td>Free (as per RBI guidelines)</td>
									<td>Free (as per RBI guidelines)</td>
									<td>Varies by bank (nominal)</td>
									<td>Free</td>
								</tr>
								<tr>
									<td>Key Requirement</td>
									<td>IFSC Code & Account Number</td>
									<td>IFSC Code & Account Number</td>
									<td>Mobile/Account & IFSC/MMID</td>
									<td>VPA (UPI ID) or QR Code</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<div class="mt-4 p-3 bg-light rounded" style="border-left: 4px solid var(--primary-color);">
						<p class="mb-0 small text-dark"><i class="fas fa-exclamation-triangle text-warning mr-1"></i> <b>Disclaimer:</b> Bank contact numbers, email addresses, and official websites are subject to change. Please verify any critical details with your bank's official website or your printed passbook/chequebook.</p>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Call to Action -->
		<div class="row justify-content-center text-center mt-4">
			<div class="col-lg-8">
				<div style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.15); border-radius: 16px; padding: 30px;">
					<h4 style="font-family: var(--font-heading); font-weight: 700; color: var(--text-main); margin-bottom: 10px;">Looking for a Bank Branch?</h4>
					<p class="text-muted" style="font-size: 14.5px; margin-bottom: 20px;">Use our free directory tools to find your bank's official IFSC code, MICR code, branch address, and contact number.</p>
					<div class="d-flex justify-content-center flex-wrap">
						<a href="<?php echo $base_url; ?>bank" class="btn btn-primary m-2"><i class="fas fa-university mr-2"></i> Bank Directory Home</a>
						<a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name" class="btn btn-secondary m-2"><i class="fas fa-search mr-2"></i> Search by Branch</a>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</section>

<?php require_once ('includes/footer.php'); ?> 
