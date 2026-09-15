<?php
	require_once ('includes/function.php'); 
	$q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';
    $year_filter = isset($_REQUEST['year']) ? trim($_REQUEST['year']) : '';
	
	if(!empty($q)) {
		$meta_desc = "Quickly search for Bare Acts and Central Acts in India: $q. Get act numbers, years, and categories. Updated 2026.";
		$page_title = "Bare Act: $q | Indian Central Acts Directory (Updated 2026) - Online Law";
	} else {
		$meta_desc = "Complete directory of Indian Bare Acts and Central Acts. Search by act name, year, or number. Free legal resource updated 2026.";
		$page_title = "Bare Acts Directory | Indian Law Central Acts Search (Updated 2026) - Online Law";
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="author" content="OfferPlant">
<title><?php echo $page_title; ?></title>

<!-- SEO Meta Tags -->
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="Bare Acts India, Central Acts Search, Indian Law Acts, Legal Acts Directory, Government Acts List, Law Search Tool, act no, <?php echo $q; ?>">
<meta property="og:title" content="<?php echo $page_title; ?>">
<meta property="og:description" content="<?php echo $meta_desc; ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo $base_url . 'acts' . (!empty($q) ? '/' . urlencode($q) : ''); ?>">
<meta property="og:image" content="<?php echo $base_url; ?>images/acts-banner.jpg">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo $page_title; ?>">
<meta name="twitter:description" content="<?php echo $meta_desc; ?>">

<!-- Structured Data for SEO -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LegalAct",
  "name": "<?php echo !empty($q) ? htmlentities($q) : 'Indian Central Acts Directory'; ?>",
  "description": "<?php echo $meta_desc; ?>",
  "url": "<?php echo $base_url . 'acts'; ?>",
  "legislationType": "Central Act",
  "jurisdiction": "India",
  "publisher": {
    "@type": "GovernmentOrganization",
    "name": "Ministry of Law & Justice, Legislative Department",
    "url": "https://www.legislative.gov.in/"
  }
}
</script>

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
    .act-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 20px;
        transition: all 0.3s ease;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .act-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1);
    }
    .card-id {
        min-width: 42px;
        height: 42px;
        background: #eff6ff;
        color: #1e40af;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 14px;
    }
    .act-badge {
        background: #f1f5f9;
        color: #475569;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
        margin-right: 5px;
    }
    .act-year-badge {
        background: #ecfdf5;
        color: #065f46;
    }
    .act-type-badge {
        background: #fff7ed;
        color: #9a3412;
    }
</style>


<?php require_once ('includes/header.php'); ?> 

<!-- Banner Section Start -->
<section class="bg-img-3 overlay p-0 full-row" style="padding: 20px 0 !important;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="banner-text text-center">
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Bare Acts Directory</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">Bare Acts</li>
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
						<h3 class="pb-2" style="font-family: var(--font-heading); font-weight: 700; font-size: calc(1.3rem + 0.6vw);">Search Indian Central Acts</h3>
						<span class="text-muted" style="font-size: 14px;">Search by act name, act number, year, or category to find relevant legal documents.</span>
					</div>
					
					<form method="get" action="<?php echo $base_url; ?>acts" class="form-style-1 mb-5">
					  <div class="row justify-content-center">
						<div class="col-lg-6 col-md-8">
						  <div class="form-group mb-0 position-relative">
                            <i class="fas fa-search position-absolute" style="top: 50%; transform: translateY(-50%); left: 20px; color: var(--text-muted);"></i>
							<input class="form-control" style="padding-left: 50px !important; height: 55px; border-radius: var(--radius-md);" type="text" placeholder="Search by Act Name, Number, or Content..." name="q" value="<?php echo htmlentities($q); ?>" required>
						  </div>
						</div>
						<div class="col-lg-2 col-md-4 mt-3 mt-md-0">
						  <div class="form-group mb-0">
							<input id="submit_btn" class="btn-primary btn w-100" style="height: 55px; font-size: 16px; border-radius: var(--radius-md); padding-top: 0; padding-bottom: 0;" type="submit" value="Search">
						  </div>
						</div>
					  </div>
					</form>
					
					<!-- Info Box -->
					<div class="alert mb-4 official-resources-box" style="background: linear-gradient(to right, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-left: 4px solid var(--primary-color); border-radius: var(--radius-md); padding: 25px;">
						<div class="row align-items-center">
							<div class="col-md-8">
								<h5 class="mb-2" style="font-family: var(--font-heading); font-weight: 600; color: var(--text-main); font-size: 18px;"><i class="fas fa-gavel mr-2 text-primary"></i>Legal Reference</h5>
								<p class="mb-0 text-muted" style="font-size: 13px;">This directory provides basic information about various acts. For detailed sections and clauses, please refer to the official website of the <a href="https://www.legislative.gov.in/" target="_blank" class="text-primary font-weight-bold">Legislative Department</a>. <br> Also see: <a href="https://egazette.gov.in/" target="_blank" class="text-primary font-weight-bold">Central Gazette</a> | <a href="https://egazette.gov.in/(S(wpk42bqtwbsypjds4zvwccjx))/StateGazette.aspx" target="_blank" class="text-primary font-weight-bold">State Gazettes</a></p>
							</div>
							<div class="col-md-4 text-md-right mt-3 mt-md-0">
								<div class="d-inline-block text-left text-md-right">
									<p class="mb-1 small text-uppercase font-weight-bold text-muted">Legal Help Line</p>
									<a href="tel:15100" class="h5 mb-0 text-primary font-weight-bold" style="text-decoration: none;"><i class="fas fa-phone-alt mr-1"></i> 15100 (NALSA)</a>
								</div>
							</div>
						</div>
					</div>
					
					<!-- Table Results -->
					<?php 
                        if(empty($q)) {
                            // Show first 25 records if search is empty
                            $sql = "select * from acts order by name ASC LIMIT 100";
                            $res = direct_sql($sql);
                            $results_title = "Recently Added Bare Acts";
                        } else {
                            $safe_q = addslashes($q);
                            $sql = "select * from acts where name like '%$safe_q%' OR remarks like '%$safe_q%' OR act_no like '%$safe_q%' OR year like '%$safe_q%' order by year DESC, name ASC LIMIT 500";
                            $res = direct_sql($sql);
                            $results_title = 'Showing results for "' . htmlentities($q) . '"';
                        }
                        
                        $count = $res['count'] ?? 0;
                        if($count > 0) {
                            $i = 1;
                    ?>
                            <div class="results-container mt-4">
                                <h4 class="text-primary mb-4" style="font-family: var(--font-heading); font-weight: 600;"><?php echo $results_title; ?> (<?php echo $count; ?>)</h4>
                                
                                <!-- Desktop Table View -->
                                <div class="table-responsive desktop-only">
                                    <table class="display responsive nowrap w-100" id="acts_table">
                                        <thead> 
                                            <tr>
                                                <th> ID </th>
                                                <th> Act Name </th>
                                                <th> Year </th>
                                                <th> Act No </th>
                                                <th> Type </th>
                                                <th> Remarks </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($res['data'] as $row) { ?>
                                                <tr>
                                                    <td> <?php echo htmlentities($row['id']); ?> </td>
                                                    <td> <strong style="color: var(--text-main);"><?php echo htmlentities($row['name']); ?></strong> </td>
                                                    <td> <span class="badge" style="background: #ecfdf5; color: #065f46; font-size: 13px; padding: 4px 10px; border-radius: 4px;"><?php echo htmlentities($row['year']); ?></span> </td>
                                                    <td> <?php echo htmlentities($row['act_no']); ?> </td>
                                                    <td> <span class="badge" style="background: #fff7ed; color: #9a3412; font-size: 13px; padding: 4px 10px; border-radius: 4px;"><?php echo htmlentities($row['act_type']); ?></span> </td>
                                                    <td style="white-space: normal; min-width: 250px; line-height: 1.6; font-size: 14px;"><?php echo htmlentities($row['remarks']); ?> </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Mobile Card View -->
                                <div class="mobile-only">
                                    <?php 
                                    foreach($res['data'] as $row) { ?>
                                        <div class="act-card mb-4 p-4">
                                            <div class="d-flex align-items-start mb-3">
                                                <div class="mr-3 card-id">
                                                    <?php echo htmlentities($row['act_no']); ?>
                                                </div>
                                                <div style="flex: 1;">
                                                    <h5 class="mb-1" style="font-weight: 800; font-size: 17px; color: #1e293b; line-height: 1.4;"><?php echo htmlentities($row['name']); ?></h5>
                                                    <div class="d-flex flex-wrap mt-1">
                                                        <span class="act-badge act-year-badge">
                                                            <i class="fas fa-calendar-alt mr-1"></i> <?php echo htmlentities($row['year']); ?>
                                                        </span>
                                                        <span class="act-badge act-type-badge">
                                                            <i class="fas fa-tag mr-1"></i> <?php echo htmlentities($row['act_type']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <?php if(!empty($row['remarks'])) { ?>
                                            <div class="mt-3 pt-3 border-top">
                                                <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.6; font-style: italic;">
                                                    <i class="fas fa-info-circle mr-1 text-primary"></i> <?php echo htmlentities($row['remarks']); ?>
                                                </p>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                
                                <div class="mt-4 pt-3 border-top text-center">
                                    <p class="text-muted small mb-0">Source: Government of India, Ministry of Law & Justice, Legislative Department. <br>Official Website: www.legislative.gov.in | Last Updated: <strong>08 April 2026</strong></p>
                                </div>
                            </div>
                    <?php } else { ?>
                            <?php if(!empty($q)) { ?>
                                <div class="alert mt-4" style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: var(--radius-md); padding: 15px 20px;"> 
                                    <i class="fas fa-exclamation-circle mr-2"></i> Sorry! No acts found for "<b><?php echo htmlentities($q); ?></b>". Please try another keyword or search by year.
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
        if ($('#acts_table').length) {
            $('#acts_table').DataTable({
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 25,
                order: [[2, "desc"]] // Sort by year desc by default
            });
        }
    });
</script>

</body>
</html>
