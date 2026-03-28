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
        $page_title = "Bank IFSC results for '$branch_search' : Online Law";
        $meta_desc = "Quickly find Bank IFSC details by searching for '$branch_search' branch or city name across all banks in India.";
    } else {
        $page_title = "Find IFSC by Branch or City Name : Online Law";
        $meta_desc = "Quickly search for Bank IFSC codes by branch name or city across India using Online Law.";
    }
?>
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="Search IFSC, <?php echo $branch_search; ?>, IFSC Code, Bank, What is my IFSC, IFSC Code Download">
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
							<span class="badge" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary-color); font-weight: 500; padding: 8px 16px; border-radius: 50px; font-size: 13px; border: 1px solid rgba(var(--primary-rgb), 0.2);">
								<i class="fas fa-check-circle mr-1"></i> 178,453 Bank IFSC Added (Last Update: 15 March 2026 - RBI Official)
							</span>
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
                                                        <td> <span class="badge" style="background: var(--primary-light); color: var(--primary-color); font-size: 14px; padding: 6px 12px; border-radius: 6px;"><?php echo htmlentities($row['ifsc']); ?></span> </td>
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
                                                        <span class="badge" style="background: #eff6ff; color: #1e40af; font-size: 12px; padding: 6px 12px; border-radius: 8px; font-weight: 700;"><?php echo htmlentities($row['ifsc']); ?></span>
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

