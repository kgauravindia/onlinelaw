<?php
	require_once ('includes/function.php'); 
	$q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';
	
	if(!empty($q)) {
		$meta_desc = "Search results for Passport Seva Kendra $q. Find contact details and office addresses for $q at Online Law.";
		$page_title = "Passport Office $q Details - Online Law";
	} else {
		$meta_desc = "Find Passport Seva Kendra (PSK) and Regional Passport Offices (RPO) across India. Look up office addresses and contact information at Online Law.";
		$page_title = "Passport Office Finder - Online Law";
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
<meta name="keywords" content="Passport Office, PSK, RPO, Passport Seva Kendra, Regional Passport Office, Passport India, Passport Seva, Contact Passport Office">
<meta name="author" content="OfferPlant">
<title><?php echo $page_title; ?></title>

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
				<div class="detail-area p-5" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); background: #fff;">
					<div class="d-flex flex-column align-items-center text-center mb-5">
						<h3 class="pb-2" style="font-family: var(--font-heading); font-weight: 700;">Search Passport Seva Kendras (PSK)</h3>
						<span style="color: var(--text-muted); font-size: 15px;">Search for any city, area, or office name to find contact details of Passport Seva Kendras and Regional Passport Offices.</span>
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
					<div class="alert mb-5" style="background: linear-gradient(to right, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-left: 4px solid var(--primary-color); border-radius: var(--radius-md); padding: 25px;">
						<div class="row align-items-center">
							<div class="col-md-8">
								<h5 class="mb-2" style="font-family: var(--font-heading); font-weight: 600; color: var(--text-main);"><i class="fas fa-info-circle mr-2 text-primary"></i>Official Resources</h5>
								<p class="mb-0 text-muted" style="font-size: 14px;">For official applications and details, visit: <a href="https://www.passportindia.gov.in/" target="_blank" class="text-primary font-weight-bold">www.passportindia.gov.in</a></p>
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
                            <div class="table-responsive mt-4">
                                <h4 class="text-primary mb-4" style="font-family: var(--font-heading); font-weight: 600;"><?php echo $results_title; ?></h4>
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
                                                <td> <a href="tel:<?php echo $row['phone']; ?>" style="color: var(--primary-color); underline: none;"><?php echo htmlentities($row['phone']); ?></a> </td>
                                                <td> <span class="badge" style="background: #eff6ff; color: #1e40af; font-size: 13px; padding: 4px 10px; border-radius: 4px;"><?php echo htmlentities($row['rpo']); ?></span> </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                
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
