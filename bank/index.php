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
    
    $page_title = "Indian Banks IFSC Directory & Search Tool (Updated August 2026) | Online Law";
    $meta_desc = "Quickly find and search Bank IFSC codes, MICR codes, branch addresses, and phone numbers for all Indian banks. Verified RBI database updated as of 15 Aug 2026.";
?>
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="bank directory, ifsc search, find micr code, bank branch list india, rbi bank code, ifsc code lookup, sbi ifsc, hdfc ifsc">
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
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Bank IFSC Directory</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">Bank Directory</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Banner Section End --> 

<section class="full-row bg-body py-80" style="padding: 60px 0;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="detail-area p-3 p-md-5" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); background: #fff;">
					<div class="d-flex flex-column align-items-center text-center mb-4">
						<h3 class="pb-2" style="font-family: var(--font-heading); font-weight: 700; font-size: calc(1.3rem + 0.6vw);">IFSC & MICR Code Directory</h3>
						<span class="text-muted" style="font-size: 14px;">Find any bank branch details, including IFSC code, MICR code, address, and phone number.</span>
						<div class="mt-3">
							<div class="d-inline-flex align-items-center justify-content-center p-2 px-3 text-center" style="background: rgba(14, 165, 233, 0.1); color: var(--primary-color); font-weight: 600; border-radius: 12px; font-size: 13px; border: 1px solid rgba(14, 165, 233, 0.2); line-height: 1.5; max-width: 100%;">
                                <div class="px-1">
								    <i class="fas fa-check-circle mr-1"></i> 181,913 Bank IFSC Added <span class="d-block d-md-inline-block text-muted" style="font-weight: 500; font-size: 12px;">(Last Update: 15 Aug 2026 - RBI Official)</span>
                                </div>
							</div>
						</div>
					</div>
					
					<!-- Search Tabs Navigation -->
					<ul class="nav nav-tabs nav-tabs-custom justify-content-center" id="searchTabs" role="tablist">
					  <li class="nav-item">
						<a class="nav-link active" id="ifsc-tab" data-toggle="tab" href="#ifsc" role="tab" aria-controls="ifsc" aria-selected="true">
						  <i class="fas fa-university mr-2"></i> Search by IFSC Code
						</a>
					  </li>
					  <li class="nav-item">
						<a class="nav-link" id="branch-tab" data-toggle="tab" href="#branch" role="tab" aria-controls="branch" aria-selected="false">
						  <i class="fas fa-search-location mr-2"></i> Search by Branch / City
						</a>
					  </li>
					  <li class="nav-item">
						<a class="nav-link" id="micr-tab" data-toggle="tab" href="#micr" role="tab" aria-controls="micr" aria-selected="false">
						  <i class="fas fa-money-check-alt mr-2"></i> Search Bank MICR
						</a>
					  </li>
					</ul>
					
					<!-- Search Tabs Content -->
					<div class="tab-content pt-2" id="searchTabsContent">
					  
					  <!-- IFSC Search Tab -->
					  <div class="tab-pane fade show active" id="ifsc" role="tabpanel" aria-labelledby="ifsc-tab">
						<form method="post" action="<?php echo $base_url; ?>bank-ifsc" class="form-style-1 mb-4">
						  <div class="row justify-content-center">
							<div class="col-lg-7 col-md-9">
							  <div class="form-group mb-0 position-relative">
								<i class="fas fa-university position-absolute" style="top: 50%; transform: translateY(-50%); left: 20px; color: var(--text-muted); z-index: 10;"></i>
								<input class="form-control text-uppercase" style="padding-left: 50px !important; height: 55px; border-radius: var(--radius-md); text-transform: uppercase;" type="text" placeholder="e.g. SBIN0000001" maxlength="11" minlength="11" name="ifsccode" required>
							  </div>
							</div>
							<div class="col-lg-2 col-md-3 mt-3 mt-md-0">
							  <div class="form-group mb-0">
								<input class="btn-primary btn w-100" style="height: 55px; font-size: 16px; border-radius: var(--radius-md); padding-top: 0; padding-bottom: 0;" type="submit" value="Search Code">
							  </div>
							</div>
						  </div>
						</form>
					  </div>
					  
					  <!-- Branch Search Tab -->
					  <div class="tab-pane fade" id="branch" role="tabpanel" aria-labelledby="branch-tab">
						<form method="post" action="<?php echo $base_url; ?>bank-ifsc-by-branch-name" class="form-style-1 mb-4">
						  <div class="row justify-content-center">
							<div class="col-lg-7 col-md-9">
							  <div class="form-group mb-0 position-relative">
								<i class="fas fa-building position-absolute" style="top: 50%; transform: translateY(-50%); left: 20px; color: var(--text-muted); z-index: 10;"></i>
								<input class="form-control" style="padding-left: 50px !important; height: 55px; border-radius: var(--radius-md);" type="text" placeholder="e.g. Delhi Main or HDFC Noida" maxlength="50" minlength="3" name="branch" required>
							  </div>
							</div>
							<div class="col-lg-2 col-md-3 mt-3 mt-md-0">
							  <div class="form-group mb-0">
								<input class="btn-primary btn w-100" style="height: 55px; font-size: 16px; border-radius: var(--radius-md); padding-top: 0; padding-bottom: 0;" type="submit" value="Search Branch">
							  </div>
							</div>
						  </div>
						</form>
					  </div>
					  
					  <!-- MICR Search Tab -->
					  <div class="tab-pane fade" id="micr" role="tabpanel" aria-labelledby="micr-tab">
						<form method="post" action="<?php echo $base_url; ?>bank-micr" class="form-style-1 mb-4">
						  <div class="row justify-content-center">
							<div class="col-lg-7 col-md-9">
							  <div class="form-group mb-0 position-relative">
								<i class="fas fa-money-check-alt position-absolute" style="top: 50%; transform: translateY(-50%); left: 20px; color: var(--text-muted); z-index: 10;"></i>
								<input class="form-control text-uppercase" style="padding-left: 50px !important; height: 55px; border-radius: var(--radius-md); text-transform: uppercase;" type="text" placeholder="e.g. 400002010" maxlength="9" minlength="9" name="micrcode" required>
							  </div>
							</div>
							<div class="col-lg-2 col-md-3 mt-3 mt-md-0">
							  <div class="form-group mb-0">
								<input class="btn-primary btn w-100" style="height: 55px; font-size: 16px; border-radius: var(--radius-md); padding-top: 0; padding-bottom: 0;" type="submit" value="Search MICR">
							  </div>
							</div>
						  </div>
						</form>
					  </div>
					  
					</div>
					
				</div>
			</div>
		</div>
		
		<!-- Popular Banks Section -->
		<div class="row text-center mt-5 mb-4">
			<div class="col-lg-12">
				<h3 style="font-family: var(--font-heading); font-size: 26px; color: var(--text-main); font-weight: 700;">Browse Popular Banks</h3>
				<p style="color: var(--text-muted); font-size: 14.5px;">Click on any bank to view its branches and locate IFSC codes instantly.</p>
			</div>
		</div>
		
		<div class="row">
            <?php
            $popular_banks = [
                ['name' => 'State Bank of India', 'short' => 'SBI', 'query' => 'State Bank of India'],
                ['name' => 'HDFC Bank', 'short' => 'HDFC', 'query' => 'HDFC Bank'],
                ['name' => 'ICICI Bank', 'short' => 'ICICI', 'query' => 'ICICI Bank'],
                ['name' => 'Punjab National Bank', 'short' => 'PNB', 'query' => 'Punjab National Bank'],
                ['name' => 'Bank of Baroda', 'short' => 'BOB', 'query' => 'Bank of Baroda'],
                ['name' => 'Axis Bank', 'short' => 'AXIS', 'query' => 'Axis Bank'],
                ['name' => 'Canara Bank', 'short' => 'CANARA', 'query' => 'Canara Bank'],
                ['name' => 'Union Bank of India', 'short' => 'UNION', 'query' => 'Union Bank of India'],
                ['name' => 'Indian Bank', 'short' => 'INDIAN', 'query' => 'Indian Bank'],
                ['name' => 'IDBI Bank', 'short' => 'IDBI', 'query' => 'IDBI Bank'],
                ['name' => 'Central Bank of India', 'short' => 'CBI', 'query' => 'Central Bank of India'],
                ['name' => 'IndusInd Bank', 'short' => 'INDUSIND', 'query' => 'IndusInd Bank']
            ];
            foreach($popular_banks as $bank) {
            ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name?branch=<?php echo urlencode($bank['query']); ?>" class="text-decoration-none d-block h-100">
                        <div class="bank-card p-4 text-center">
                            <div class="bank-icon-wrapper mx-auto">
                                <i class="fas fa-university"></i>
                            </div>
                            <h5 class="mb-1" style="font-size: 15px; font-weight: 700; color: var(--text-main);"><?php echo htmlentities($bank['name']); ?></h5>
                            <span class="text-muted small" style="font-weight: 500;"><?php echo $bank['short']; ?> IFSC Codes</span>
                        </div>
                    </a>
                </div>
            <?php } ?>
		</div>
		
		<!-- Banking Resources Section -->
		<div class="row text-center mt-5 mb-4">
			<div class="col-lg-12">
				<h3 style="font-family: var(--font-heading); font-size: 26px; color: var(--text-main); font-weight: 700;">Explore Banking Services & Guides</h3>
				<p style="color: var(--text-muted); font-size: 14.5px;">Browse our comprehensive guides on fund transfers, payment cards, and bank account types.</p>
			</div>
		</div>

		<div class="row justify-content-center">
			
			<div class="col-lg-3 col-md-6 mb-4">
				<a href="<?php echo $base_url; ?>bank/services" class="text-decoration-none d-block h-100">
					<div class="bank-card p-4 text-center">
						<div class="bank-icon-wrapper mx-auto">
							<i class="fas fa-exchange-alt"></i>
						</div>
						<h5 class="mb-2" style="font-size: 16px; font-weight: 700; color: var(--text-main);">Digital Banking Services</h5>
						<p class="text-muted small mb-0" style="line-height: 1.5; font-weight: 400;">Compare NEFT, RTGS, IMPS, and UPI transaction limits, charges, and settlement timings.</p>
					</div>
				</a>
			</div>
			
			<div class="col-lg-3 col-md-6 mb-4">
				<a href="<?php echo $base_url; ?>bank/card" class="text-decoration-none d-block h-100">
					<div class="bank-card p-4 text-center">
						<div class="bank-icon-wrapper mx-auto">
							<i class="fas fa-credit-card"></i>
						</div>
						<h5 class="mb-2" style="font-size: 16px; font-weight: 700; color: var(--text-main);">Debit & Credit Cards</h5>
						<p class="text-muted small mb-0" style="line-height: 1.5; font-weight: 400;">Access emergency bank toll-free numbers to block lost cards, card network comparisons, and safety tips.</p>
					</div>
				</a>
			</div>
			
			<div class="col-lg-3 col-md-6 mb-4">
				<a href="<?php echo $base_url; ?>bank/account" class="text-decoration-none d-block h-100">
					<div class="bank-card p-4 text-center">
						<div class="bank-icon-wrapper mx-auto">
							<i class="fas fa-wallet"></i>
						</div>
						<h5 class="mb-2" style="font-size: 16px; font-weight: 700; color: var(--text-main);">Account Services</h5>
						<p class="text-muted small mb-0" style="line-height: 1.5; font-weight: 400;">Compare Savings vs. Current accounts, and find missed-call balance check and mini-statement numbers.</p>
					</div>
				</a>
			</div>

			<div class="col-lg-3 col-md-6 mb-4">
				<a href="<?php echo $base_url; ?>bank/contact-details" class="text-decoration-none d-block h-100">
					<div class="bank-card p-4 text-center">
						<div class="bank-icon-wrapper mx-auto">
							<i class="fas fa-address-book"></i>
						</div>
						<h5 class="mb-2" style="font-size: 16px; font-weight: 700; color: var(--text-main);">Bank Contact Info</h5>
						<p class="text-muted small mb-0" style="line-height: 1.5; font-weight: 400;">Official websites, toll-free customer care lines, support emails, and alternate contact numbers for all banks.</p>
					</div>
				</a>
			</div>
			
		</div>
		
		<!-- FAQs Section -->
		<div class="row justify-content-center mt-5 pt-4">
			<div class="col-lg-10">
				<div class="text-center mb-5">
					<h3 style="font-family: var(--font-heading); font-size: 26px; color: var(--text-main); font-weight: 700;">Frequently Asked Questions</h3>
					<p style="color: var(--text-muted); font-size: 14.5px;">Got questions about bank IFSC and MICR codes? Find answers below.</p>
				</div>
				
				<div class="accordion faq-accordion" id="faqAccordion">
				  
				  <div class="card">
					<div class="card-header" id="headingOne">
					  <h2 class="mb-0">
						<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						  What is an IFSC Code?
						  <i class="fas fa-chevron-down small"></i>
						</button>
					  </h2>
					</div>
					<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#faqAccordion">
					  <div class="card-body">
						An IFSC (Indian Financial System Code) is a unique 11-digit alphanumeric code assigned by the Reserve Bank of India (RBI). It is used to identify specific bank branches participating in electronic fund transfer systems such as NEFT (National Electronic Funds Transfer), RTGS (Real Time Gross Settlement), and IMPS (Immediate Payment Service).
					  </div>
					</div>
				  </div>
				  
				  <div class="card">
					<div class="card-header" id="headingTwo">
					  <h2 class="mb-0">
						<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
						  How is an IFSC Code structured?
						  <i class="fas fa-chevron-down small"></i>
						</button>
					  </h2>
					</div>
					<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#faqAccordion">
					  <div class="card-body">
						The 11-digit IFSC code is divided into three parts:
						<ul class="pl-4 mt-2">
							<li>The first 4 characters are alphabets representing the bank name (e.g., <b>SBIN</b> for State Bank of India).</li>
							<li>The 5th character is always the digit <b>0</b> (reserved for future use).</li>
							<li>The last 6 characters are numeric or alphabetic and identify the specific branch.</li>
						</ul>
					  </div>
					</div>
				  </div>
				  
				  <div class="card">
					<div class="card-header" id="headingThree">
					  <h2 class="mb-0">
						<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
						  Where can I find my bank branch's IFSC Code?
						  <i class="fas fa-chevron-down small"></i>
						</button>
					  </h2>
					</div>
					<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#faqAccordion">
					  <div class="card-body">
						You can find your branch's IFSC code in several places:
						<ul class="pl-4 mt-2">
							<li>Printed on your bank cheque book (usually at the top or bottom).</li>
							<li>Printed on the first page of your bank passbook.</li>
							<li>On your bank's official internet banking portal or mobile app.</li>
							<li>By searching online via our <b>Bank IFSC Directory</b> tool above.</li>
						</ul>
					  </div>
					</div>
				  </div>
				  
				  <div class="card">
					<div class="card-header" id="headingFour">
					  <h2 class="mb-0">
						<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
						  What is the difference between IFSC and MICR codes?
						  <i class="fas fa-chevron-down small"></i>
						</button>
					  </h2>
					</div>
					<div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#faqAccordion">
					  <div class="card-body">
						While both codes identify bank branches, their uses are different:
						<ul class="pl-4 mt-2">
							<li><b>IFSC Code:</b> An 11-character alphanumeric code used for digital fund transfers (NEFT, RTGS, IMPS).</li>
							<li><b>MICR Code:</b> A 9-digit numeric code printed using magnetic ink at the bottom of cheques. It is used primarily for the automated clearing and processing of physical cheques.</li>
						</ul>
					  </div>
					</div>
				  </div>
				  
				  <div class="card">
					<div class="card-header" id="headingFive">
					  <h2 class="mb-0">
						<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
						  Can an IFSC code change?
						  <i class="fas fa-chevron-down small"></i>
						</button>
					  </h2>
					</div>
					<div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#faqAccordion">
					  <div class="card-body">
						Yes, the Reserve Bank of India (RBI) can change or assign new IFSC codes to bank branches. This typically happens during bank mergers, restructuring, branch relocations, or when a branch is upgraded. It is always recommended to verify your IFSC code before initiating high-value transactions.
					  </div>
					</div>
				  </div>
				  
				</div>
			</div>
		</div>
		<!-- Disclaimer Note -->
		<div class="row mt-5">
			<div class="col-lg-10 mx-auto">
				<div class="p-3 bg-light rounded shadow-sm" style="border-left: 4px solid var(--primary-color);">
					<p class="mb-0 small text-dark"><i class="fas fa-info-circle text-primary mr-1"></i> <b>Disclaimer:</b> Bank contact numbers, email addresses, and official websites are subject to change. Please verify any critical details with your bank's official website or your printed passbook/chequebook before making financial decisions.</p>
				</div>
			</div>
		</div>
		
	</div>
</section>

<?php require_once ('includes/footer.php'); ?> 
