<?php
	require_once ('includes/function.php'); 
	$q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';
	
	if(!empty($q)) {
		$meta_desc = "Quickly locate Passport Seva Kendra (PSK) details for $q. Get office addresses, contact numbers, and Regional Passport Office (RPO) links. Fully updated 2026.";
		$page_title = "Passport Office $q | PSK & RPO Contact Details (Updated 2026) - Online Law";
	} else {
		$meta_desc = "Find your nearest Passport Seva Kendra (PSK), POPSK, or Regional Passport Office (RPO) in India. Full directory of passport office addresses and helplines updated 2026.";
		$page_title = "Passport Office Finder | Locate PSK & RPO Offices India (Updated 2026) - Online Law";
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="Passport Office Search, Find PSK Near Me, Regional Passport Office India, Passport Seva Kendra Address, POPSK Locations, Passport Help Number, <?php echo $q; ?>">
<meta name="author" content="OfferPlant">
<title><?php echo $page_title; ?></title>
<style>
    @media (max-width: 767px) {
        .banner-tittle { font-size: 26px !important; }
        .detail-area { padding: 25px 15px !important; }
        .official-resources-box { padding: 15px !important; }
        .official-resources-box .h5 { font-size: 16px !important; }
        .desktop-only { display: none !important; }
        .mobile-only { display: block !important; }
    }
    @media (min-width: 768px) {
        .mobile-only { display: none !important; }
        .desktop-only { display: block !important; }
    }
    .passport-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 20px;
        transition: all 0.3s ease;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .passport-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1);
    }
    .card-number {
        min-width: 42px;
        height: 42px;
        background: #eff6ff;
        color: #1e40af;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
    }
</style>


<?php require_once ('includes/header.php'); ?> 

<!-- Banner Section Start -->
<section class="bg-img-3 overlay p-0 full-row" style="padding: 20px 0 !important;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="banner-text text-center">
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Passport Office Finder</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">Passport Offices</li>
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
						<h3 class="pb-2" style="font-family: var(--font-heading); font-weight: 700; font-size: calc(1.3rem + 0.6vw);">Search Passport Seva Kendras (PSK)</h3>
						<span class="text-muted" style="font-size: 14px;">Search for any city, area, or office name to find contact details of Passport Seva Kendras and Regional Passport Offices.</span>
					</div>
					
					<form method="get" action="<?php echo $base_url; ?>passport" class="form-style-1 mb-5">
					  <div class="row justify-content-center">
						<div class="col-lg-6 col-md-8">
						  <div class="form-group mb-0 position-relative">
                            <i class="fas fa-search position-absolute" style="top: 50%; transform: translateY(-50%); left: 20px; color: var(--text-muted);"></i>
							<input class="form-control" style="padding-left: 50px !important; height: 55px; border-radius: var(--radius-md);" type="text" placeholder="Search by Office Name, Area or RPO..." name="q" value="<?php echo htmlentities($q); ?>" required>
						  </div>
						</div>
						<div class="col-lg-2 col-md-4 mt-3 mt-md-0">
						  <div class="form-group mb-0">
							<input id="submit_btn" class="btn-primary btn w-100" style="height: 55px; font-size: 16px; border-radius: var(--radius-md); padding-top: 0; padding-bottom: 0;" type="submit" value="Search">
						  </div>
						</div>
					  </div>
					</form>
					
					<!-- Official Notice Box -->
					<div class="alert mb-4 official-resources-box" style="background: linear-gradient(to right, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-left: 4px solid var(--primary-color); border-radius: var(--radius-md); padding: 25px;">
						<div class="row align-items-center">
							<div class="col-md-8">
								<h5 class="mb-2" style="font-family: var(--font-heading); font-weight: 600; color: var(--text-main); font-size: 18px;"><i class="fas fa-info-circle mr-2 text-primary"></i>Official Resources</h5>
								<p class="mb-0 text-muted" style="font-size: 13px;">For official applications and details, visit: <a href="https://www.passportindia.gov.in/" target="_blank" class="text-primary font-weight-bold">www.passportindia.gov.in</a></p>
							</div>
							<div class="col-md-4 text-md-right mt-3 mt-md-0">
								<div class="d-inline-block text-left">
									<p class="mb-1 small text-uppercase font-weight-bold text-muted">National Call Center</p>
									<a href="tel:18002581800" class="h5 mb-0 text-primary font-weight-bold" style="text-decoration: none;"><i class="fas fa-phone-alt mr-1"></i> 1800-258-1800</a>
								</div>
							</div>
						</div>
					</div>
					
					<!-- Table Results -->
					<?php 
                        if(empty($q)) {
                            // Show first 25 records if search is empty
                            $sql = "select * from passport order by name ASC LIMIT 25";
                            $res = direct_sql($sql);
                            $results_title = "Featured Passport Offices";
                        } else {
                            $safe_q = addslashes($q);
                            $sql = "select * from passport where name like '%$safe_q%' OR address like '%$safe_q%' OR rpo like '%$safe_q%' order by name ASC LIMIT 500";
                            $res = direct_sql($sql);
                            $results_title = 'Showing results for "' . htmlentities($q) . '"';
                        }
                        
                        $ct = $res['count'];
                        if($ct > 0) {
                            $i = 1;
                    ?>
                            <div class="results-container mt-4">
                                <h4 class="text-primary mb-4" style="font-family: var(--font-heading); font-weight: 600;"><?php echo $results_title; ?></h4>
                                
                                <!-- Desktop Table View -->
                                <div class="table-responsive desktop-only">
                                    <table class="display responsive nowrap w-100" id="passport_table">
                                        <thead> 
                                            <tr>
                                                <th> No </th>
                                                <th> Office Name </th>
                                                <th> Address </th>
                                                <th> Phone </th>
                                                <th> RPO </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($res['data'] as $row) { ?>
                                                <tr>
                                                    <td> <?php echo $i++; ?> </td>
                                                    <td> <strong style="color: var(--text-main);"><?php echo htmlentities($row['name']); ?></strong> </td>
                                                    <td style="white-space: normal; min-width: 300px; line-height: 1.6;"><?php echo htmlentities($row['address']); ?> </td>
                                                    <td> <a href="tel:<?php echo $row['phone']; ?>" style="color: var(--primary-color); text-decoration: none;"><?php echo htmlentities($row['phone']); ?></a> </td>
                                                    <td> <span class="badge" style="background: #eff6ff; color: #1e40af; font-size: 13px; padding: 4px 10px; border-radius: 4px;"><?php echo htmlentities($row['rpo']); ?></span> </td>
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
                                        <div class="passport-card mb-4 p-4">
                                            <div class="d-flex align-items-start mb-4">
                                                <div class="mr-3 card-number">
                                                    <?php echo $mi++; ?>
                                                </div>
                                                <div style="flex: 1;">
                                                    <h5 class="mb-1" style="font-weight: 800; font-size: 18px; color: #1e293b; line-height: 1.4;"><?php echo htmlentities($row['name']); ?></h5>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                                                            <i class="fas fa-building mr-1 text-primary"></i> <?php echo htmlentities($row['rpo']); ?> RPO
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-4" style="background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #f1f5f9;">
                                                <div class="d-flex">
                                                    <i class="fas fa-map-marker-alt text-danger mt-1 mr-2" style="font-size: 14px;"></i>
                                                    <p class="mb-0" style="font-size: 14px; line-height: 1.6; color: #334155;"><?php echo htmlentities($row['address']); ?></p>
                                                </div>
                                            </div>

                                            <div class="pt-2">
                                                <?php if(!empty($row['phone']) && $row['phone'] != '-') { ?>
                                                    <a href="tel:<?php echo $row['phone']; ?>" class="btn btn-primary btn-block py-3 d-flex align-items-center justify-content-center" style="border-radius: 14px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 12px rgba(30, 64, 175, 0.2);">
                                                        <i class="fas fa-phone-alt mr-2"></i> <?php echo htmlentities($row['phone']); ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <div class="text-center p-2" style="background: #fff5f5; border: 1px dashed #feb2b2; border-radius: 10px;">
                                                        <span class="text-secondary small font-italic" style="color: #c53030 !important;"><i class="fas fa-info-circle mr-1"></i> Phone details not available</span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                
                                <div class="mt-4 pt-3 border-top text-center">
                                    <p class="text-muted small mb-0">Information as per data last updated on <strong>28 March 2026</strong>. Source: passportindia.gov.in</p>
                                </div>
                            </div>
                    <?php } else { ?>
                            <?php if(!empty($q)) { ?>
                                <div class="alert mt-4" style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: var(--radius-md); padding: 15px 20px;"> 
                                    <i class="fas fa-exclamation-circle mr-2"></i> Sorry! No results found for "<b><?php echo htmlentities($q); ?></b>". Please try another keyword.
                                </div>
                            <?php } ?>
                    <?php } ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php require_once ('includes/footer.php'); ?> 
<script>
    $(document).ready(function() {
        $('#passport_table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            pageLength: 25
        });
    });
</script>
