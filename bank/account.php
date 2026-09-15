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
    
    $page_title = "Bank Account Types Guide & Balance Check Numbers | Online Law";
    $meta_desc = "Learn about the types of bank accounts in India, including Savings, Current, FD, and RD. Find official bank missed-call balance inquiry and mini-statement numbers.";
?>
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="savings account india, current account business, fixed deposit fd, recurring deposit rd, bank balance check number, missed call balance check">
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
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Account Services</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>bank" style="color: rgba(255,255,255,0.8); text-decoration: none;">Bank Directory</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">Account Services</li>
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
				<h3 style="font-family: var(--font-heading); font-size: 28px; color: var(--text-main); font-weight: 700; margin-bottom: 15px;">Understanding Bank Accounts</h3>
				<p style="color: var(--text-muted); font-size: 16px; line-height: 1.7;">Opening a bank account is the first step toward managing your personal and business finances securely. Learn about the different types of banking accounts available in India, compare savings versus current accounts, and access emergency missed-call numbers for balance inquiries.</p>
			</div>
		</div>
		
		<!-- Account Types Grid -->
		<div class="row mb-5">
			
			<!-- Savings Account -->
			<div class="col-lg-6 mb-4">
				<div class="service-card">
					<div class="service-header">
						<div class="service-icon"><i class="fas fa-piggy-bank"></i></div>
						<div>
							<h4 class="service-title">Savings Account</h4>
							<span class="text-muted small">For Personal Savings & Regular Interest</span>
						</div>
					</div>
					<div class="service-body">
						A Savings Account is meant for individuals who want to save a portion of their income. It helps accumulate funds while earning a nominal interest rate (typically 2.5% to 5.5% per annum) credited quarterly or half-yearly.
						<ul class="pl-4 mt-3">
							<li><b>Interest:</b> Earns regular interest based on daily balances.</li>
							<li><b>Withdrawals:</b> Generally limited frequency of free ATM/withdrawal transactions per month.</li>
							<li><b>Minimum Balance:</b> Usually requires maintaining an Average Monthly Balance (AMB).</li>
						</ul>
					</div>
				</div>
			</div>
			
			<!-- Current Account -->
			<div class="col-lg-6 mb-4">
				<div class="service-card">
					<div class="service-header">
						<div class="service-icon"><i class="fas fa-briefcase"></i></div>
						<div>
							<h4 class="service-title">Current Account</h4>
							<span class="text-muted small">For Business Operations & Liquid Funds</span>
						</div>
					</div>
					<div class="service-body">
						A Current Account is designed for businesses, firms, public enterprises, and businessmen who require high-frequency, high-value transactions. It supports fluid payments with zero restrictions on transaction volumes.
						<ul class="pl-4 mt-3">
							<li><b>Interest:</b> Usually earns zero interest.</li>
							<li><b>Overdraft:</b> Eligible for overdraft (OD) facility to borrow temporary credit.</li>
							<li><b>Limits:</b> Unlimited deposits and withdrawals, subject to transaction handling charges.</li>
						</ul>
					</div>
				</div>
			</div>
			
			<!-- Fixed Deposit (FD) -->
			<div class="col-lg-6 mb-4">
				<div class="service-card">
					<div class="service-header">
						<div class="service-icon"><i class="fas fa-lock"></i></div>
						<div>
							<h4 class="service-title">Fixed Deposit (FD)</h4>
							<span class="text-muted small">Lump-Sum Savings with Guaranteed Returns</span>
						</div>
					</div>
					<div class="service-body">
						Fixed Deposits allow you to lock a lump-sum amount of money for a pre-determined tenure at a fixed, guaranteed rate of interest. The tenure can range from 7 days up to 10 years.
						<ul class="pl-4 mt-3">
							<li><b>Returns:</b> Higher interest rates compared to savings accounts.</li>
							<li><b>Maturity:</b> Funds cannot be withdrawn until maturity without incurring a premature penalty.</li>
							<li><b>Tax Saving:</b> 5-Year tax-saver FDs are available for tax deductions under Section 80C.</li>
						</ul>
					</div>
				</div>
			</div>
			
			<!-- Recurring Deposit (RD) -->
			<div class="col-lg-6 mb-4">
				<div class="service-card">
					<div class="service-header">
						<div class="service-icon"><i class="fas fa-calendar-alt"></i></div>
						<div>
							<h4 class="service-title">Recurring Deposit (RD)</h4>
							<span class="text-muted small">Monthly Investment & Systematic Savings</span>
						</div>
					</div>
					<div class="service-body">
						Recurring Deposits allow customers to invest a fixed sum of money every month for a set period. It is an ideal tool for salaried professionals to cultivate a disciplined saving habit and earn interest similar to FDs.
						<ul class="pl-4 mt-3">
							<li><b>Deposits:</b> Invest a fixed amount monthly (e.g., Rs. 500, Rs. 1,000, etc.).</li>
							<li><b>Interest:</b> Earns compound interest rates comparable to FDs.</li>
							<li><b>Tenure:</b> Ranges from 6 months up to 10 years.</li>
						</ul>
					</div>
				</div>
			</div>
			
		</div>
		
		<!-- Comparison: Savings vs Current -->
		<div class="row mb-5 pt-4">
			<div class="col-lg-12">
				<div class="detail-area p-3 p-md-5" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); background: #fff;">
					<h3 class="text-center mb-4" style="font-family: var(--font-heading); font-weight: 700;">Savings vs. Current Account</h3>
					
					<div class="table-responsive">
						<table class="table-comparison">
							<thead>
								<tr>
									<th>Feature</th>
									<th>Savings Account</th>
									<th>Current Account</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Primary Purpose</td>
									<td>To encourage savings and earn interest</td>
									<td>To facilitate smooth, regular business transactions</td>
								</tr>
								<tr>
									<td>Suitable For</td>
									<td>Salaried professionals, students, individuals</td>
									<td>Proprietors, traders, partnerships, companies</td>
								</tr>
								<tr>
									<td>Interest Rate</td>
									<td>Earns 2.5% to 5.5% (credited periodically)</td>
									<td>No interest earned (0%)</td>
								</tr>
								<tr>
									<td>Overdraft Facility</td>
									<td>Not available (unless special salary scheme)</td>
									<td>Available (borrow up to a pre-set limit)</td>
								</tr>
								<tr>
									<td>Transaction Frequency</td>
									<td>Limited free transactions (withdrawals/transfers)</td>
									<td>Unlimited deposits and withdrawals</td>
								</tr>
								<tr>
									<td>Minimum Balance Requirement</td>
									<td>Low to moderate (typically Rs. 1,000 - Rs. 10,000)</td>
									<td>Higher (typically Rs. 5,000 - Rs. 1,00,000+)</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Bank Missed Call Balance Checking Directory -->
		<div class="row mb-5 pt-4">
			<div class="col-lg-12">
				<div class="detail-area p-3 p-md-5" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); background: #fff;">
					<div class="d-flex align-items-center mb-4">
						<div style="width: 40px; height: 40px; background: #e0f2fe; color: #0284c7; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-size: 18px;">
							<i class="fas fa-phone"></i>
						</div>
						<div>
							<h4 class="mb-0" style="font-family: var(--font-heading); font-weight: 700; color: #1e293b; font-size: 20px;">Missed-Call Balance Inquiry Directory</h4>
							<p class="mb-0 text-muted" style="font-size: 13px;">Check your bank account balance instantly by calling these official numbers from your registered mobile phone.</p>
						</div>
					</div>
					
					<div class="table-responsive">
						<table class="table-hotline">
							<thead>
								<tr>
									<th>Bank Name</th>
									<th>Balance Inquiry Number</th>
									<th>Mini Statement Number</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><b>State Bank of India (SBI)</b></td>
									<td><a href="tel:09223766666" class="text-primary font-weight-bold">09223766666</a></td>
									<td><a href="tel:09223866666" class="text-primary">09223866666</a></td>
								</tr>
								<tr>
									<td><b>HDFC Bank</b></td>
									<td><a href="tel:18002703333" class="text-primary font-weight-bold">1800 270 3333</a></td>
									<td><a href="tel:18002703355" class="text-primary">1800 270 3355</a></td>
								</tr>
								<tr>
									<td><b>ICICI Bank</b></td>
									<td><a href="tel:9555144444" class="text-primary font-weight-bold">9555144444</a></td>
									<td><a href="tel:9555244444" class="text-primary">9555244444</a></td>
								</tr>
								<tr>
									<td><b>Punjab National Bank (PNB)</b></td>
									<td><a href="tel:18001802223" class="text-primary font-weight-bold">1800 180 2223</a> / <a href="tel:01202303090" class="text-primary font-weight-bold">01202303090</a></td>
									<td>—</td>
								</tr>
								<tr>
									<td><b>Bank of Baroda (BOB)</b></td>
									<td><a href="tel:8468001111" class="text-primary font-weight-bold">8468001111</a></td>
									<td><a href="tel:8468001122" class="text-primary">8468001122</a></td>
								</tr>
								<tr>
									<td><b>Axis Bank</b></td>
									<td><a href="tel:18004195959" class="text-primary font-weight-bold">1800 419 5959</a></td>
									<td><a href="tel:18004196969" class="text-primary">1800 419 6969</a></td>
								</tr>
								<tr>
									<td><b>Canara Bank</b></td>
									<td><a href="tel:09015483483" class="text-primary font-weight-bold">09015483483</a></td>
									<td><a href="tel:09015734734" class="text-primary">09015734734</a></td>
								</tr>
								<tr>
									<td><b>Union Bank of India</b></td>
									<td><a href="tel:09223008586" class="text-primary font-weight-bold">09223008586</a></td>
									<td>—</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<div class="mt-4 p-3 bg-light rounded" style="border-left: 4px solid var(--primary-color);">
						<p class="mb-2 small text-dark"><i class="fas fa-info-circle text-primary mr-1"></i> <b>Note:</b> For missed-call balance banking to work, your mobile number must be registered with your bank account, and the call must be initiated from that registered mobile number.</p>
						<p class="mb-0 small text-dark"><i class="fas fa-exclamation-triangle text-warning mr-1"></i> <b>Disclaimer:</b> Bank contact numbers, email addresses, and official websites are subject to change. Please verify any critical details with your bank's official website or your printed passbook/chequebook.</p>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Call to Action -->
		<div class="row justify-content-center text-center mt-4">
			<div class="col-lg-8">
				<div style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.15); border-radius: 16px; padding: 30px;">
					<h4 style="font-family: var(--font-heading); font-weight: 700; color: var(--text-main); margin-bottom: 10px;">Looking for Bank Branch Codes?</h4>
					<p class="text-muted" style="font-size: 14.5px; margin-bottom: 20px;">Use our free IFSC locator to find branch addresses, MICR codes, and contact numbers for all branches in India.</p>
					<div class="d-flex justify-content-center flex-wrap">
						<a href="<?php echo $base_url; ?>bank" class="btn btn-primary m-2"><i class="fas fa-university mr-2"></i> Bank Directory</a>
						<a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name" class="btn btn-secondary m-2"><i class="fas fa-search mr-2"></i> Search by Branch</a>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</section>

<?php require_once ('includes/footer.php'); ?> 
