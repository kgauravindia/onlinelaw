<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
    // SEO & URL Parameter Handling (like pincode.php)
    $branch_search = "";
    if(isset($_GET['branch'])) {
        $branch_search = trim($_GET['branch']);
        $_REQUEST['branch'] = $branch_search;
    } elseif(isset($_POST['branch'])) {
        $branch_search = trim($_POST['branch']);
    }

    if(!empty($branch_search)) {
        $page_title = "Bank IFSC results for '$branch_search' Branch - Online Law";
        $meta_desc = "Quickly find Bank IFSC details by searching for '$branch_search' branch or city name. All banks in India covered - SBI, ICICI, HDFC, PNB, and more.";
    } else {
        $page_title = "Search Bank IFSC by Branch or City Name | Online Law";
        $meta_desc = "Search for any Bank IFSC by typing the branch name or city. Comprehensive IFSC database for all Indian bank branches.";
    }
?>
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="IFSC Search by City, Bank Branch Name Search India, Find IFSC by Town, State Bank Branch Location, District Wise IFSC Codes, <?php echo $branch_search; ?>">
<meta name="author" content="OfferPlant">
<title><?php echo $page_title; ?></title>
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
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Find IFSC by Branch</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">Find IFSC by Branch</li>
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
						<h3 class="pb-2" style="font-family: var(--font-heading); font-weight: 700; font-size: calc(1.3rem + 0.6vw);">Find IFSC by Branch or City Name</h3>
						<span class="text-muted" style="font-size: 14px;">Enter the name of your city or bank branch below to retrieve matching IFSC records.</span>
						<div class="mt-3">
							<div class="d-inline-flex align-items-center justify-content-center p-2 px-3 text-center" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary-color); font-weight: 600; border-radius: 12px; font-size: 13px; border: 1px solid rgba(var(--primary-rgb), 0.2); line-height: 1.5; max-width: 100%;">
                                <div class="px-1">
								    <i class="fas fa-check-circle mr-1"></i> 178,915 Bank IFSC Added <span class="d-block d-md-inline-block text-muted" style="font-weight: 500; font-size: 12px;">(Last Update: 31 March 2026 - RBI Official)</span>
                                </div>
							</div>
						</div>

                        <!-- News Ticker / Latest News -->
                        <div class="mt-4 w-100" style="background: #fff8f1; border: 1px dashed #fb923c; border-radius: 12px; padding: 12px 20px; overflow: hidden;">
                            <div class="d-flex align-items-center">
                                <span class="badge py-1 px-2 mr-3" style="background: #ea580c; color: #fff; font-size: 11px; font-weight: 700; border-radius: 6px; text-transform: uppercase;">Latest News</span>
                                <div style="font-size: 14px; color: #7c2d12; font-weight: 500;">
                                    <i class="fas fa-bullhorn mr-2 text-warning"></i> 479 bank branches have been recently inaugurated and updated in the RBI official record as of 31 March 2026.
                                </div>
                            </div>
                        </div>
					</div>
					
					<form method="post" action="<?php echo $base_url; ?>bank-ifsc-by-branch-name" class="form-style-1 mb-5">
					  <div class="row justify-content-center">
						<div class="col-lg-5 col-md-8">
						  <div class="form-group mb-0 position-relative">
                            <i class="fas fa-building position-absolute" style="top: 50%; transform: translateY(-50%); left: 20px; color: var(--text-muted);"></i>
							<input class="form-control" style="padding-left: 50px !important; height: 55px; border-radius: var(--radius-md);" type="text" placeholder="e.g. Delhi" maxlength="25" minlength="4" name="branch" value="<?php echo htmlentities($branch_search); ?>" required>
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
                        if(isset($_REQUEST['branch']))
                        {
                            extract($_REQUEST);
                            if(!empty(trim($branch))) {
                                $search_term = trim($branch);
                                $sql = "select * from bank_ifsc where branch like '%$search_term%' or address like '%$search_term%' order by branch";
                                $res = direct_sql($sql);
                                $ct =$res['count'];
                                
                                if($ct >0)
                                {
                                    $i=1;
                    ?>
                                    <div class="results-container mt-4">
                                        <h4 class="text-primary mb-4" style="font-family: var(--font-heading); font-weight: 600;">Showing results for "<?php echo htmlentities($search_term); ?>" </h4>
                                        
                                        <!-- Desktop Table View -->
                                        <div class="table-responsive desktop-only">
                                            <table class="display responsive nowrap w-100" id="branch_table">
                                                <thead> 
                                                <tr>
                                                    <th> No </th>
                                                    <th> Bank Name </th>
                                                    <th> IFSC </th>
                                                    <th> Branch</th>
                                                    <th> District</th>
                                                    <th>State / UT</th>
                                                    <th> Address</th>
                                                    <th> Phone </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach( $res['data'] as $row) { ?>
                                                    <tr>
                                                        <td> <?php echo $i++; ?> </td>
                                                        <td> <span style="font-weight: 600; color: var(--text-main);"><?php echo htmlentities($row['bank']); ?></span> </td>
                                                        <td> <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo $row['ifsc']; ?>" class="badge" style="background: var(--primary-light); color: var(--primary-color); font-size: 14px; padding: 6px 12px; border-radius: 6px; text-decoration: none;"><?php echo htmlentities($row['ifsc']); ?></a> </td>
                                                        <td> <?php echo htmlentities($row['branch']); ?> </td>
                                                        <td> <?php echo htmlentities($row['city2']); ?> </td>
                                                        <td> <?php echo htmlentities($row['state']); ?> </td>
                                                        <td style="white-space: normal; max-width: 250px;"> <?php echo htmlentities($row['address']); ?> </td>
                                                        <td> <?php echo htmlentities($row['phone']); ?> </td>
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
                                                        <h5 class="mb-0 text-primary" style="font-weight: 800; font-size: 18px; line-height: 1.3;"><?php echo htmlentities($row['bank']); ?></h5>
                                                        <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo $row['ifsc']; ?>" class="badge" style="background: #eff6ff; color: #1e40af; font-size: 12px; padding: 6px 12px; border-radius: 8px; font-weight: 700; text-decoration: none;"><?php echo htmlentities($row['ifsc']); ?></a>
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

                                                    <?php if(!empty($row['phone']) && $row['phone'] != '-') { ?>
                                                        <a href="tel:<?php echo $row['phone']; ?>" class="btn btn-primary btn-block py-3" style="border-radius: 12px; font-weight: 700; font-size: 15px; box-shadow: 0 4px 10px rgba(var(--primary-rgb), 0.2);">
                                                            <i class="fas fa-phone-alt mr-2"></i> <?php echo htmlentities($row['phone']); ?>
                                                        </a>
                                                    <?php } else { ?>
                                                        <div class="text-center py-2" style="background: #fff5f5; border: 1px dashed #feb2b2; border-radius: 10px;">
                                                            <span class="text-secondary small font-italic" style="color: #c53030 !important;"><i class="fas fa-info-circle mr-1"></i> Contact bank directly for phone info</span>
                                                        </div>
                                                    <?php } ?>
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
                                    $safe_ifsc = addslashes($res['data'][0]['ifsc']);

                                    // Very Near (Same Bank in Same City)
                                    $very_near_sql = "SELECT DISTINCT branch, ifsc, city2 FROM bank_ifsc WHERE bank = '$safe_bank' AND city2 = '$safe_city' AND ifsc != '$safe_ifsc' ORDER BY branch LIMIT 12";
                                    $very_near_res = direct_sql($very_near_sql);

                                    // Nearby (Same Bank in Same State, excluding Same City)
                                    $nearby_sql = "SELECT DISTINCT branch, ifsc, city2 FROM bank_ifsc WHERE bank = '$safe_bank' AND state = '$safe_state' AND city2 != '$safe_city' ORDER BY branch LIMIT 12";
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
                                                    <p class="mb-0 text-muted" style="font-size: 13px;">Find more branches of <?php echo htmlentities($bank_ref); ?> in this region</p>
                                                </div>
                                            </div>

                                            <?php if($very_near_res['count'] > 0) { ?>
                                                <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="font-size: 11px; letter-spacing: 1px;">In <?php echo htmlentities($city_ref); ?> District</h6>
                                                <div class="row mb-4">
                                                    <?php foreach($very_near_res['data'] as $rel) { ?>
                                                        <div class="col-lg-3 col-md-4 col-6 mb-3">
                                                            <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo $rel['ifsc']; ?>" class="d-block p-3 text-center text-decoration-none" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                                                <div style="font-weight: 700; color: #3b82f6; font-size: 15px;"><?php echo htmlentities($rel['branch']); ?></div>
                                                                <div class="text-muted" style="font-size: 12px; font-weight: 600;"><?php echo htmlentities($rel['ifsc']); ?></div>
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
                                                            <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo $rel['ifsc']; ?>" class="d-block p-3 text-center text-decoration-none" style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; transition: all 0.2s ease;">
                                                                <div style="font-weight: 700; color: #64748b; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlentities($rel['branch']); ?></div>
                                                                <div class="text-muted" style="font-size: 11px;"><?php echo htmlentities($rel['city2']); ?> - <?php echo htmlentities($rel['ifsc']); ?></div>
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
                                        <i class="fas fa-exclamation-circle mr-2"></i> Sorry! No results found for <b> <?php echo htmlentities($search_term); ?> </b>
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
        $('#branch_table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            pageLength: 10
        });
    });
</script>

