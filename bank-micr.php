<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
    // SEO & URL Parameter Handling (like pincode.php)
    require_once ('includes/function.php'); 
    $micrcode = "";
    if(isset($_GET['ifsc'])) {
        $micrcode = strtoupper(trim($_GET['ifsc']));
        $_REQUEST['micrcode'] = $micrcode;
    } elseif(isset($_POST['micrcode'])) {
        $micrcode = strtoupper(trim($_POST['micrcode']));
    }

    if(!empty($micrcode)) {
        // Query database for a premium SEO title
        $res_seo = direct_sql("select * from bank_ifsc where ifsc = '$micrcode' limit 1");
        if($res_seo['count'] > 0) {
            $row_seo = $res_seo['data'][0];
            $page_title = "MICR Code: " . $row_seo['micr'] . ", " . $row_seo['branch'] . ", " . $row_seo['bank'];
            $meta_desc = "MICR Code: " . $row_seo['micr'] . ". Get " . $row_seo['bank'] . " " . $row_seo['branch'] . " branch MICR, IFSC Code, Address: " . $row_seo['address'] . ", " . $row_seo['city2'] . ", " . $row_seo['state'] . ".";
            
            // Generate Schema.org JSON-LD
            $schema_json = [
                "@context" => "https://schema.org",
                "@type" => "BankOrCreditUnion",
                "name" => $row_seo['bank'] . " " . $row_seo['branch'],
                "branchOf" => [
                    "@type" => "BankOrCreditUnion",
                    "name" => $row_seo['bank']
                ],
                "address" => [
                    "@type" => "PostalAddress",
                    "streetAddress" => $row_seo['address'],
                    "addressLocality" => $row_seo['city2'],
                    "addressRegion" => $row_seo['state'],
                    "addressCountry" => "IN"
                ],
                "identifier" => $row_seo['micr']
            ];
            $seo_schema = '<script type="application/ld+json">' . json_encode($schema_json) . '</script>';
            
        } else {
            $page_title = "MICR Code: $micrcode Details - Online Law";
            $meta_desc = "Find details for MICR Code $micrcode on Online Law. Fully updated RBI database.";
            $seo_schema = "";
        }
    } else {
        $page_title = "Bank MICR Search Tool | Find Any Bank Branch in India - Online Law";
        $meta_desc = "Quickly search for any Bank MICR code across India. Get instant details for NEFT, RTGS, and IMPS transactions. 100% accurate RBI database.";
        $seo_schema = "";
    }
?>
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="MICR Code Search, Find Bank MICR, RBI MICR List, NEFT RTGS Codes, Bank Branch Finder India, MICR Code Search, Net Banking Codes, <?php echo $micrcode; ?>">
<meta name="author" content="OfferPlant">
<title><?php echo $page_title; ?></title>
<?php echo $seo_schema; ?>
<style>
    @media (max-width: 767px) {
        .banner-tittle { font-size: 26px !important; }
        .detail-area { padding: 25px 15px !important; }
        .desktop-only { display: none !important; }
        .mobile-only { display: block !important; }
    }
    @media (min-width: 768px) {
        .mobile-only { display: none !important; }
        .desktop-only { display: block !important; }
    }
    .bank-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 20px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .bank-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
</style>


<?php require_once ('includes/header.php'); ?> 
<?php require_once ('includes/function.php'); ?> 

<!-- Banner Section Start -->
<section class="bg-img-3 overlay p-0 full-row" style="padding: 20px 0 !important;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="banner-text text-center">
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Find MICR Code</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">Find MICR Code</li>
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
						<h3 class="pb-2" style="font-family: var(--font-heading); font-weight: 700; font-size: calc(1.3rem + 0.6vw);">Magnetic Ink Character Recognition (MICR)</h3>
						<span class="text-muted" style="font-size: 14px;">Enter a 9-digit numeric MICR code below to retrieve detailed branch information.</span>
						<div class="mt-3">
							<div class="d-inline-flex align-items-center justify-content-center p-2 px-3 text-center" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary-color); font-weight: 600; border-radius: 12px; font-size: 13px; border: 1px solid rgba(var(--primary-rgb), 0.2); line-height: 1.5; max-width: 100%;">
                                <div class="px-1">
								    <i class="fas fa-check-circle mr-1"></i> 181,913 Bank MICR Added <span class="d-block d-md-inline-block text-muted" style="font-weight: 500; font-size: 12px;">(Last Update: 15 Aug 2026 - RBI Official)</span>
                                </div>
							</div>
						</div>
                        
                        <!-- News Ticker / Latest News -->
                        <div class="mt-4 w-100" style="background: #fff8f1; border: 1px dashed #fb923c; border-radius: 12px; padding: 12px 20px; overflow: hidden;">
                            <div class="d-flex align-items-center">
                                <span class="badge py-1 px-2 mr-3" style="background: #ea580c; color: #fff; font-size: 11px; font-weight: 700; border-radius: 6px; text-transform: uppercase;">Latest News</span>
                                <div style="font-size: 14px; color: #7c2d12; font-weight: 500;">
                                    <i class="fas fa-bullhorn mr-2 text-warning"></i> 1,096 bank branches have been recently inaugurated and updated in the RBI official record as of 15 Aug 2026.
                                </div>
                            </div>
                        </div>
					</div>
					
					<form method="post" action="<?php echo $base_url; ?>bank-micr" class="form-style-1 mb-5">
					  <div class="row justify-content-center">
						<div class="col-lg-5 col-md-8">
						  <div class="form-group mb-0 position-relative">
                            <i class="fas fa-university position-absolute" style="top: 50%; transform: translateY(-50%); left: 20px; color: var(--text-muted);"></i>
							<input class="form-control text-uppercase" style="padding-left: 50px !important; height: 55px; border-radius: var(--radius-md); text-transform: uppercase;" type="text" placeholder="e.g. 110002005" maxlength="9" minlength="9" name="micrcode" value="<?php echo htmlentities($micrcode); ?>" required>
						  </div>
						</div>
						<div class="col-lg-2 col-md-4 mt-3 mt-md-0">
						  <div class="form-group mb-0">
							<input id="submit_btn" class="btn-primary btn w-100" style="height: 55px; font-size: 16px; border-radius: var(--radius-md); padding-top: 0; padding-bottom: 0;" type="submit" value="Search">
						  </div>
						</div>
					  </div>
					</form>
					
					<!-- Table Results -->
					<?php 
                        if(isset($_REQUEST['micrcode']))
                        {
                            extract($_REQUEST);
                            // Safety Check for empty
                            if(!empty($micrcode)) {
                                // Upper case standardization
                                $micrcode = strtoupper(trim($micrcode));
                                $sql = "select * from bank_ifsc where micr ='$micrcode' order by branch";
                                $res = direct_sql($sql);
                                $ct =$res['count'];
                                
                                if($ct >0)
                                {
                                    $i=1;
                    ?>
                                    <div class="results-container mt-4">
                                        <h4 class="text-primary mb-4" style="font-family: var(--font-heading); font-weight: 600;">Showing results for MICR Code "<?php echo htmlentities($micrcode); ?>" </h4>
                                        
                                        <!-- Desktop Table View -->
                                        <div class="table-responsive desktop-only">
                                            <table class="display responsive nowrap w-100" id="micrcode_table">
                                                <thead> 
                                                <tr>
                                                    <th> No </th>
                                                    <th> Bank Name </th>
                                                    <th> IFSC / MICR </th>
                                                    <th> Branch</th>
                                                    <th> District</th>
                                                    <th>State / UT</th>
                                                    <th> Address</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach( $res['data'] as $row) { ?>
                                                    <tr>
                                                        <td> <?php echo $i++; ?> </td>
                                                        <td> <span style="font-weight: 600; color: var(--text-main);"><?php echo htmlentities($row['bank']); ?></span> </td>
                                                        <td> 
                                                            <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo $row['ifsc']; ?>" class="badge mb-1" style="background: var(--primary-light); color: var(--primary-color); font-size: 14px; padding: 6px 12px; border-radius: 6px; text-decoration: none; display: inline-block;"><?php echo htmlentities($row['ifsc']); ?></a><br>
                                                            <span style="font-size: 11px; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0;">MICR: <?php echo !empty($row['micr']) && $row['micr'] != 'NA' && $row['micr'] != '-' ? htmlentities($row['micr']) : 'N/A'; ?></span> 
                                                        </td>
                                                        <td> <?php echo htmlentities($row['branch']); ?> </td>
                                                        <td> <?php echo htmlentities($row['city2']); ?> </td>
                                                        <td> <?php echo htmlentities($row['state']); ?> </td>
                                                        <td style="white-space: normal; max-width: 250px;"> <?php echo htmlentities($row['address']); ?> </td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Mobile Card View -->
                                        <div class="mobile-only">
                                            <?php 
                                            $mi = 1;
                                            foreach($res['data'] as $row) { ?>
                                                <div class="bank-card mb-4 p-4">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <h5 class="mb-0 text-primary" style="font-weight: 800; font-size: 18px; line-height: 1.3; margin-right: 10px;"><?php echo htmlentities($row['bank']); ?></h5>
                                                        <div class="text-right">
                                                            <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo $row['ifsc']; ?>" class="badge mb-1" style="background: #eff6ff; color: #1e40af; font-size: 12px; padding: 6px 12px; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-block;"><?php echo htmlentities($row['ifsc']); ?></a>
                                                            <div style="font-size: 10px; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0; display: inline-block;">MICR: <?php echo !empty($row['micr']) && $row['micr'] != 'NA' && $row['micr'] != '-' ? htmlentities($row['micr']) : 'N/A'; ?></div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3 d-flex align-items-center">
                                                        <i class="fas fa-code-branch text-muted mr-2" style="font-size: 12px;"></i>
                                                        <span class="text-dark font-weight-bold" style="font-size: 14px;"><?php echo htmlentities($row['branch']); ?></span>
                                                    </div>

                                                    <div class="p-3 mb-4" style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px;">
                                                        <div class="d-flex">
                                                            <i class="fas fa-map-marker-alt text-danger mt-1 mr-2" style="font-size: 13px;"></i>
                                                            <p class="mb-0 text-dark" style="font-size: 13px; line-height: 1.6;"><?php echo htmlentities($row['address']); ?></p>
                                                        </div>
                                                        <div class="mt-2 text-muted px-4" style="font-size: 12px;">
                                                            <?php echo htmlentities($row['city2']); ?>, <?php echo htmlentities($row['state']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                    <?php
                                    // Nearest Banks Logic derived from the first search result
                                    $bank_ref = $res['data'][0]['bank'];
                                    $city_ref = $res['data'][0]['city2'];
                                    $state_ref = $res['data'][0]['state'];
                                    
                                    $safe_bank = addslashes($bank_ref);
                                    $safe_city = addslashes($city_ref);
                                    $safe_state = addslashes($state_ref);
                                    $safe_micr = addslashes($res['data'][0]['micr']);

                                    // Very Near (Same Bank in Same City)
                                    $very_near_sql = "SELECT DISTINCT branch, micr, city2 FROM bank_ifsc WHERE bank = '$safe_bank' AND city2 = '$safe_city' AND micr != '$safe_micr' AND micr != '' AND micr != 'NA' AND micr != '-' ORDER BY branch LIMIT 12";
                                    $very_near_res = direct_sql($very_near_sql);

                                    // Nearby (Same Bank in Same State, excluding Same City)
                                    $nearby_sql = "SELECT DISTINCT branch, micr, city2 FROM bank_ifsc WHERE bank = '$safe_bank' AND state = '$safe_state' AND city2 != '$safe_city' AND micr != '' AND micr != 'NA' AND micr != '-' ORDER BY branch LIMIT 12";
                                    $nearby_res = direct_sql($nearby_sql);

                                    if($very_near_res['count'] > 0 || $nearby_res['count'] > 0) {
                    ?>
                                        <div class="nearby-banks mt-5 pt-4" style="border-top: 1px solid #eef2f6;">
                                            <div class="d-flex align-items-center mb-4">
                                                <div style="width: 40px; height: 40px; background: #fff7ed; color: #ea580c; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                                    <i class="fas fa-university"></i>
                                                </div>
                                                <div>
                                                    <h4 class="mb-0" style="font-family: var(--font-heading); font-weight: 700; color: #1e293b; font-size: 20px;">Other Nearby Branches</h4>
                                                    <p class="mb-0 text-muted" style="font-size: 13px;">Find more branches of <?php echo htmlentities($bank_ref); ?> in this region MICR Code</p>
                                                </div>
                                            </div>

                                            <?php if($very_near_res['count'] > 0) { ?>
                                                <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="font-size: 11px; letter-spacing: 1px;">In <?php echo htmlentities($city_ref); ?> District</h6>
                                                <div class="row mb-4">
                                                    <?php foreach($very_near_res['data'] as $rel) { ?>
                                                        <div class="col-lg-3 col-md-4 col-6 mb-3">
                                                            <a href="<?php echo $base_url; ?>bank-micr?micrcode=<?php echo $rel['micr']; ?>" class="d-block p-3 text-center text-decoration-none" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                                                <div style="font-weight: 700; color: #3b82f6; font-size: 15px;"><?php echo htmlentities($rel['branch']); ?></div>
                                                                <div class="text-muted" style="font-size: 12px; font-weight: 600;"><?php echo htmlentities($rel['micr']); ?></div>
                                                            </a>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>

                                            <?php if($nearby_res['count'] > 0) { ?>
                                                <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="font-size: 11px; letter-spacing: 1px;">Other Branches in <?php echo htmlentities($state_ref); ?></h6>
                                                <div class="row">
                                                    <?php foreach($nearby_res['data'] as $rel) { ?>
                                                        <div class="col-lg-3 col-md-4 col-6 mb-3">
                                                            <a href="<?php echo $base_url; ?>bank-micr?micrcode=<?php echo $rel['micr']; ?>" class="d-block p-3 text-center text-decoration-none" style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; transition: all 0.2s ease;">
                                                                <div style="font-weight: 700; color: #64748b; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlentities($rel['branch']); ?></div>
                                                                <div class="text-muted" style="font-size: 11px;"><?php echo htmlentities($rel['city2']); ?> - <?php echo htmlentities($rel['micr']); ?></div>
                                                            </a>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                    <?php
                                    }
                                }
                                else
                                {
                    ?>
                                    <div class="alert mt-4" style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: var(--radius-md); padding: 15px 20px;"> 
                                        <i class="fas fa-exclamation-circle mr-2"></i> Sorry! No results found for MICR Code <b> <?php echo htmlentities($micrcode); ?> </b>
                                    </div>
                    <?php
                                }
                            }
                        }
                    ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php require_once ('includes/footer.php'); ?> 
<script>
    $(document).ready(function() {
        $('#micrcode_table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            pageLength: 10
        });
    });
</script>

