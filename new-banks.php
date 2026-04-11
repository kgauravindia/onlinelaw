<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
    require_once ('includes/function.php'); 
    
    $page_title = "Newly Opened Bank Branches in India (Updated 2026) | Online Law";
    $meta_desc = "Explore the list of 479 newly opened bank branches across India. Find latest IFSC codes, branch addresses, and contact details for recently inaugurated bank locations.";
?>
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="New Bank Branches India, Recently Opened Banks, New IFSC Codes 2026, Latest RBI Bank List, New Branch Locator, Online Law Bank Search">
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
    .new-badge {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }
</style>

<?php require_once ('includes/header.php'); ?> 

<!-- Banner Section Start -->
<section class="bg-img-3 overlay p-0 full-row" style="padding: 20px 0 !important;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="banner-text text-center">
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Newly Opened Branches</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">New Banks</li>
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
					<div class="d-flex flex-column align-items-center text-center mb-5">
						<h3 class="pb-2" style="font-family: var(--font-heading); font-weight: 700; font-size: calc(1.3rem + 0.6vw);">Recently Added Bank Branches</h3>
						<span class="text-muted" style="font-size: 15px; max-width: 700px;">Following is the comprehensive list of 479 bank branches that have been recently inaugurated and updated in the RBI official record as of March 2026.</span>
						
                        <div class="mt-4 d-flex flex-wrap justify-content-center gap-3">
							<div class="d-inline-flex align-items-center p-2 px-3" style="background: rgba(16, 185, 129, 0.1); color: #059669; font-weight: 600; border-radius: 12px; font-size: 13px; border: 1px solid rgba(16, 185, 129, 0.2);">
								<i class="fas fa-plus-circle mr-2"></i> 479 New Branches Detected
							</div>
                            <div class="d-inline-flex align-items-center p-2 px-3" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary-color); font-weight: 600; border-radius: 12px; font-size: 13px; border: 1px solid rgba(var(--primary-rgb), 0.2);">
								<i class="fas fa-calendar-alt mr-2"></i> Last Update: 31 March 2026
							</div>
						</div>
					</div>
					
					<?php 
                        // Fetching branches added in the latest update (April 2026)
                        $sql = "SELECT * FROM bank_ifsc WHERE created_at >= '2026-04-11 14:00:00' ORDER BY bank, branch";
                        $res = direct_sql($sql);
                        
                        if($res['count'] > 0) {
                            $i = 1;
                    ?>
                            <div class="results-container">
                                <!-- Desktop Table View -->
                                <div class="table-responsive desktop-only">
                                    <table class="display responsive nowrap w-100" id="new_banks_table">
                                        <thead> 
                                            <tr>
                                                <th> No </th>
                                                <th> Bank Name </th>
                                                <th> IFSC Code </th>
                                                <th> Branch </th>
                                                <th> District </th>
                                                <th> State / UT </th>
                                                <th> Status </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($res['data'] as $row) { ?>
                                            <tr>
                                                <td> <?php echo $i++; ?> </td>
                                                <td> <span style="font-weight: 600; color: var(--text-main);"><?php echo htmlentities($row['bank']); ?></span> </td>
                                                <td> <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo $row['ifsc']; ?>" class="badge" style="background: var(--primary-light); color: var(--primary-color); font-size: 14px; padding: 6px 12px; border-radius: 6px; text-decoration: none;"><?php echo htmlentities($row['ifsc']); ?></a> </td>
                                                <td> <?php echo htmlentities($row['branch']); ?> </td>
                                                <td> <?php echo htmlentities($row['city2']); ?> </td>
                                                <td> <?php echo htmlentities($row['state']); ?> </td>
                                                <td> <span class="new-badge">New</span> </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Mobile Card View -->
                                <div class="mobile-only">
                                    <?php foreach($res['data'] as $row) { ?>
                                        <div class="bank-card mb-4 p-4">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="pr-2">
                                                    <span class="new-badge d-inline-block mb-2">Recently Opened</span>
                                                    <h5 class="mb-0 text-primary" style="font-weight: 800; font-size: 18px; line-height: 1.3;"><?php echo htmlentities($row['bank']); ?></h5>
                                                </div>
                                                <a href="<?php echo $base_url; ?>bank-ifsc?ifsc=<?php echo $row['ifsc']; ?>" class="badge" style="background: #eff6ff; color: #1e40af; font-size: 12px; padding: 6px 12px; border-radius: 8px; font-weight: 700; text-decoration: none;"><?php echo htmlentities($row['ifsc']); ?></a>
                                            </div>
                                            
                                            <div class="mb-3 d-flex align-items-center">
                                                <i class="fas fa-code-branch text-muted mr-2" style="font-size: 12px;"></i>
                                                <span class="text-dark font-weight-bold" style="font-size: 14px;"><?php echo htmlentities($row['branch']); ?></span>
                                            </div>

                                            <div class="p-3 mb-1" style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px;">
                                                <div class="d-flex mb-1">
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
                        } else {
                    ?>
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-search-minus text-muted" style="font-size: 64px; opacity: 0.3;"></i>
                                </div>
                                <h4 class="text-muted">No new branches found at this time.</h4>
                                <p class="text-muted">All branches are already indexed in our master database.</p>
                                <a href="<?php echo $base_url; ?>bank-ifsc" class="btn btn-primary mt-3">Search Existing Branches</a>
                            </div>
                    <?php
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
        $('#new_banks_table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                searchPlaceholder: "Search by bank, city or IFSC...",
                search: ""
            }
        });
        
        // Custom styling for dataTables search input
        $('.dataTables_filter input').addClass('form-control').css({
            'width': '300px',
            'display': 'inline-block',
            'margin-left': '10px',
            'height': '40px',
            'border-radius': '10px'
        });
    });
</script>
</body>
</html>
