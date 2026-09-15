<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
    // SEO & URL Parameter Handling (like pincode.php)
    $locality_search = "";
    if(isset($_GET['locality'])) {
        $locality_search = trim($_GET['locality']);
        $_REQUEST['locality'] = $locality_search;
    } elseif(isset($_POST['locality'])) {
        $locality_search = trim($_POST['locality']);
    }

    if(!empty($locality_search)) {
        $page_title = "Pincode results for '$locality_search' Locality (Updated 2026) - Online Law";
        $meta_desc = "Quickly search for Indian PIN Codes in '$locality_search'. Find precise postal details for cities, villages, and post offices. Updated 2026.";
    } else {
        $page_title = "Find PIN Code by Locality, City or Village (Updated 2026) | Online Law";
        $meta_desc = "Search for Indian PIN Codes by entering a locality, city, or village name. Fast, comprehensive postal index directory updated 2026.";
    }
?>
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="PIN Code by Locality, Village PIN Code Search, City Postal Codes India, Post Office Name Search, Find PIN by Area, <?php echo $locality_search; ?>">
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
    .pincode-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .pincode-card:hover {
        transform: translateY(-3px);
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
					<h2 class="text-white banner-tittle position-relative mb-1">PIN Code Search by Locality</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">PIN Code Search by Locality</li>
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
						<h3 class="pb-2" style="font-family: var(--font-heading); font-weight: 700; font-size: calc(1.3rem + 0.6vw);">PIN Locality Search</h3>
						<span class="text-muted" style="font-size: 14px;">Search for any Indian PIN Code by entering a City, Village, or Post Office name.</span>
					</div>
					
					<form method="post" action="<?php echo $base_url; ?>pincode-by-locality" class="form-style-1 mb-5">
					  <div class="row justify-content-center">
						<div class="col-lg-5 col-md-8">
						  <div class="form-group mb-0 position-relative">
                            <i class="fas fa-map-marker-alt position-absolute" style="top: 50%; transform: translateY(-50%); left: 20px; color: var(--text-muted);"></i>
							<input class="form-control" style="padding-left: 50px !important; height: 55px; border-radius: var(--radius-md);" type="text" placeholder="e.g. New Delhi" name="locality" value="<?php echo htmlentities($locality_search); ?>" required>
						  </div>
						</div>
						<div class="col-lg-2 col-md-4 mt-3 mt-md-0">
						  <div class="form-group mb-0">
							<input id="submit_btn" class="btn-primary btn w-100" style="height: 55px; font-size: 16px; border-radius: var(--radius-md); padding-top: 0; padding-bottom: 0;" type="submit" value="Search">
						  </div>
						</div>
					  </div>
					</form>

					<?php 
					if(isset($_REQUEST['locality']))
					{
						$locality = trim($_REQUEST['locality']);
						// Basic escape for the SQL query string
						$safe_query = addslashes($locality);
						$sql = "select * from pincode where locality_name like '%$safe_query%' or office_name like '%$safe_query%' order by locality_name";
						$res = direct_sql($sql);
						$ct = $res['count'];
					?>
					
					<hr style="border-color: var(--border-soft); margin: 40px 0;">
					<h4 class="text-primary mb-4" style="font-family: var(--font-heading); font-weight: 600;">Showing PIN details covering: "<?php echo htmlentities($locality); ?>"</h4>
					
					<?php if($ct > 0) { ?>
					<div class="results-container mt-4">
						<!-- Desktop View -->
						<div class="table-responsive desktop-only">
							<table class="display responsive nowrap w-100" id="locality" style="border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-sm);">
								<thead> 
									<tr>
										<th>No</th>
										<th>Locality</th>
										<th>Post Office</th>
										<th>Sub District</th>
										<th>District</th>
										<th>State / UT</th>
										<th>PIN Code</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$i = 1;
									foreach($res['data'] as $row) { 
								?>
									<tr>
										<td><?php echo $i++; ?></td>
										<td><?php echo htmlentities($row['locality_name']); ?></td>
										<td><?php echo htmlentities($row['office_name']); ?></td>
										<td><?php echo htmlentities($row['sub_district_name']); ?></td>
										<td><?php echo htmlentities($row['district_name']); ?></td>
										<td><?php echo htmlentities($row['state_name']); ?></td>
										<td><a href="<?php echo $base_url; ?>pincode?pincode=<?php echo $row['pincode']; ?>" class="font-weight-bold text-primary"><?php echo htmlentities($row['pincode']); ?></a></td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>

						<!-- Mobile View -->
						<div class="mobile-only">
							<?php 
							$mi = 1;
							foreach($res['data'] as $row) { ?>
								<div class="pincode-card mb-4 p-4">
									<div class="d-flex justify-content-between align-items-start mb-3">
										<div class="d-flex align-items-center">
											<div class="mr-3" style="width: 32px; height: 32px; background: #eff6ff; color: #1e40af; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;"><?php echo $mi++; ?></div>
											<h5 class="mb-0" style="font-weight: 700; color: #1e293b; font-size: 16px; line-height: 1.3;"><?php echo htmlentities($row['locality_name']); ?></h5>
										</div>
										<a href="<?php echo $base_url; ?>pincode?pincode=<?php echo $row['pincode']; ?>" class="badge" style="background: var(--primary-color); color: #fff; font-size: 12px; padding: 5px 10px; border-radius: 6px; text-decoration: none;"><?php echo htmlentities($row['pincode']); ?></a>
									</div>
									
									<div class="row no-gutters mb-0">
										<div class="col-6 mb-3">
											<small class="text-uppercase text-muted font-weight-bold" style="letter-spacing: 0.5px; font-size: 10px;">Post Office</small>
											<p class="mb-0 text-dark" style="font-size: 13px; font-weight: 500;"><?php echo htmlentities($row['office_name']); ?></p>
										</div>
										<div class="col-6 mb-3">
											<small class="text-uppercase text-muted font-weight-bold" style="letter-spacing: 0.5px; font-size: 10px;">District</small>
											<p class="mb-0 text-dark" style="font-size: 13px; font-weight: 500;"><?php echo htmlentities($row['district_name']); ?></p>
										</div>
										<div class="col-12">
											<small class="text-uppercase text-muted font-weight-bold" style="letter-spacing: 0.5px; font-size: 10px;">State / UT</small>
											<p class="mb-0 text-dark" style="font-size: 13px; font-weight: 500;"><?php echo htmlentities($row['state_name']); ?></p>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>

                    <?php
                        // Nearest PIN Codes Logic derived from the first search result
                        $ref_pincode = $res['data'][0]['pincode'];
                        if(strlen($ref_pincode) >= 3) {
                            $pincode = $ref_pincode; // for compatibility with snippets
                            $prefix3 = substr($pincode, 0, 3);
                            $prefix4 = (strlen($pincode) >= 4) ? substr($pincode, 0, 4) : $prefix3;
                            
                            $safe_prefix3 = addslashes($prefix3);
                            $safe_prefix4 = addslashes($prefix4);
                            $safe_pincode = addslashes($pincode);

                            // Very Near (4-digit match)
                            $very_near_sql = "SELECT DISTINCT pincode, district_name FROM pincode WHERE pincode LIKE '$safe_prefix4%' AND pincode != '$safe_pincode' ORDER BY pincode LIMIT 12";
                            $very_near_res = direct_sql($very_near_sql);

                            // Nearby (3-digit match, excluding 4-digit pool)
                            $nearby_sql = "SELECT DISTINCT pincode, district_name FROM pincode WHERE pincode LIKE '$safe_prefix3%' AND pincode NOT LIKE '$safe_prefix4%' AND pincode != '$safe_pincode' ORDER BY pincode LIMIT 12";
                            $nearby_res = direct_sql($nearby_sql);

                            if($very_near_res['count'] > 0 || $nearby_res['count'] > 0) {
                    ?>
                                <div class="nearby-pincodes mt-5 pt-4" style="border-top: 1px solid #eef2f6;">
                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 40px; height: 40px; background: #eff6ff; color: #2563eb; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0" style="font-family: var(--font-heading); font-weight: 700; color: #1e293b; font-size: 20px;">Nearest PIN Codes</h4>
                                            <p class="mb-0 text-muted" style="font-size: 13px;">Related PIN codes for results in this region</p>
                                        </div>
                                    </div>

                                    <?php if($very_near_res['count'] > 0) { ?>
                                        <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="font-size: 11px; letter-spacing: 1px;">Very Near (<?php echo $prefix4; ?>...)</h6>
                                        <div class="row mb-4">
                                            <?php foreach($very_near_res['data'] as $rel) { ?>
                                                <div class="col-lg-3 col-md-4 col-6 mb-3">
                                                    <a href="<?php echo $base_url; ?>pincode?pincode=<?php echo $rel['pincode']; ?>" class="d-block p-3 text-center text-decoration-none" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                                        <div style="font-weight: 700; color: #3b82f6; font-size: 16px;"><?php echo htmlentities($rel['pincode']); ?></div>
                                                        <div class="text-muted" style="font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlentities($rel['district_name']); ?></div>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>

                                    <?php if($nearby_res['count'] > 0) { ?>
                                        <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="font-size: 11px; letter-spacing: 1px;">Nearby (<?php echo $prefix3; ?>...)</h6>
                                        <div class="row">
                                            <?php foreach($nearby_res['data'] as $rel) { ?>
                                                <div class="col-lg-3 col-md-4 col-6 mb-3">
                                                    <a href="<?php echo $base_url; ?>pincode?pincode=<?php echo $rel['pincode']; ?>" class="d-block p-3 text-center text-decoration-none" style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; transition: all 0.2s ease;">
                                                        <div style="font-weight: 700; color: #64748b; font-size: 16px;"><?php echo htmlentities($rel['pincode']); ?></div>
                                                        <div class="text-muted" style="font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlentities($rel['district_name']); ?></div>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                    <?php
                            }
                        }
                    ?>

					<?php } else { ?>
						<div class="alert mt-4" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #f87171; border-radius: var(--radius-md); padding: 16px 20px;">
							<i class="fas fa-exclamation-circle mr-2"></i> Sorry! No results found for village or locality: <b><?php echo htmlentities($locality); ?></b>
						</div>
					<?php } } ?>
					
				</div>
			</div>
		</div>
	</div>
</section>
<?php require_once ('includes/footer.php'); ?> 
<script>
    $(document).ready(function() {
        $('#locality').DataTable({
            responsive: true, // Enable responsiveness
            paging: true,
            searching: true,
            ordering: true,
            pageLength: 5
        });
    });
</script>


