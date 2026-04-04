<?php
	require_once ('includes/function.php'); 
	$q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';
	
	if(!empty($q)) {
		$meta_desc = "Quickly find GST HSN/SAC code $q details. Get full descriptions, current GST rates, and classification for $q at Online Law.";
		$page_title = "HSN/SAC Code $q GST Rates & Details - Online Law";
	} else {
		$meta_desc = "Professional GST HSN and SAC code lookup for goods and services. Search for tax rates, HSN chapters, and service accounting codes instantly.";
		$page_title = "GST HSN and SAC Code Finder | Current GST Rates India - Online Law";
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
<meta name="keywords" content="GST HSN Code Finder, SAC Code Search, GST Rate Finder 2026, HSN Chapters List, Service Accounting Codes, Goods and Services Tax Search, <?php echo $q; ?>">
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
    .hsn-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .hsn-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
</style>


<?php require_once ('includes/header.php'); ?> 

<!-- Banner Section Start -->
<section class="bg-img-3 overlay p-0 full-row" style="padding: 20px 0 !important;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="banner-text text-center">
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">GST HSN & SAC Finder</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">HSN / SAC Search</li>
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
						<h3 class="pb-2" style="font-family: var(--font-heading); font-weight: 700; font-size: calc(1.3rem + 0.6vw);">HSN/SAC Professional Search</h3>
						<span class="text-muted" style="font-size: 14px;">Search for any GST HSN (Goods) or SAC (Services) code or description to find its classification.</span>
					</div>
					
					<form method="get" action="<?php echo $base_url; ?>gst-hsn-sac" class="form-style-1 mb-5">
					  <div class="row justify-content-center">
						<div class="col-lg-6 col-md-8">
						  <div class="form-group mb-0 position-relative">
                            <i class="fas fa-search position-absolute" style="top: 50%; transform: translateY(-50%); left: 20px; color: var(--text-muted);"></i>
							<input class="form-control" style="padding-left: 50px !important; height: 55px; border-radius: var(--radius-md);" type="text" placeholder="Enter HSN/SAC Code or Keyword (e.g. 1001 or Rice)" name="q" value="<?php echo htmlentities($q); ?>" required>
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
                        if(empty($q)) {
                            // Show last 25 updated records if search is empty
                            $sql = "select * from hsnsac order by updated_at DESC LIMIT 25";
                            $res = direct_sql($sql);
                            $results_title = "Recently Updated Codes";
                        } else {
                            $safe_q = addslashes($q);
                            $sql = "select * from hsnsac where code like '%$safe_q%' OR description like '%$safe_q%' order by code ASC LIMIT 500";
                            $res = direct_sql($sql);
                            $results_title = 'Showing results for "' . htmlentities($q) . '"';
                        }
                        
                        $ct = $res['count'];
                        if($ct > 0) {
                            $i = 1;
                    ?>
                            <div class="results-container mt-4">
                                <h4 class="text-primary mb-4" style="font-family: var(--font-heading); font-weight: 600;"><?php echo $results_title; ?></h4>
                                
                                <!-- Desktop View -->
                                <div class="table-responsive desktop-only">
                                    <table class="display responsive nowrap w-100" id="hsn_table">
                                        <thead> 
                                            <tr>
                                                <th> No </th>
                                                <th> Type </th>
                                                <th> Code </th>
                                                <th> Description </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($res['data'] as $row) { ?>
                                                <tr>
                                                    <td> <?php echo $i++; ?> </td>
                                                    <td> <span class="badge" style="background: <?php echo ($row['type'] == 'HSN') ? '#eff6ff' : '#ecfdf5'; ?>; color: <?php echo ($row['type'] == 'HSN') ? '#1e40af' : '#065f46'; ?>; font-size: 13px; padding: 4px 10px; border-radius: 4px;"><?php echo htmlentities($row['type']); ?></span> </td>
                                                    <td> <strong style="color: var(--text-main); font-size: 16px;"><?php echo htmlentities($row['code']); ?></strong> </td>
                                                    <td style="white-space: normal; min-width: 300px; line-height: 1.6;"><?php echo htmlentities($row['description']); ?> </td>
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
                                        <div class="hsn-card mb-4 p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3" style="width: 32px; height: 32px; background: #f1f5f9; color: #64748b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;"><?php echo $mi++; ?></div>
                                                    <h5 class="mb-0 text-dark" style="font-weight: 800; font-size: 18px;"><?php echo htmlentities($row['code']); ?></h5>
                                                </div>
                                                <span class="badge" style="background: <?php echo ($row['type'] == 'HSN') ? '#eff6ff' : '#ecfdf5'; ?>; color: <?php echo ($row['type'] == 'HSN') ? '#1e40af' : '#065f46'; ?>; font-size: 12px; padding: 5px 10px; border-radius: 6px; font-weight: 700;"><?php echo htmlentities($row['type']); ?></span>
                                            </div>
                                            
                                            <div style="background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #f1f5f9;">
                                                <p class="mb-0" style="font-size: 14px; line-height: 1.6; color: #334155;">
                                                    <?php echo htmlentities($row['description']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="mt-4 pt-3 border-top text-center">
                                    <?php 
                                        $max_res = direct_sql("SELECT MAX(updated_at) as last_up FROM hsnsac");
                                        $last_up_date = date('d M Y', strtotime($max_res['data'][0]['last_up']));
                                    ?>
                                    <p class="text-muted small mb-0">Information as per data last updated on <strong><?php echo $last_up_date; ?></strong>. Source: gst.gov.in</p>
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
        $('#hsn_table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            pageLength: 25
        });
    });
</script>
