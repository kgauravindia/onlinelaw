<?php
	require_once ('includes/function.php'); 
	$pincode = isset($_REQUEST['pincode']) ? trim($_REQUEST['pincode']) : '';
	
	if(!empty($pincode) && is_numeric($pincode)) {
		$meta_desc = "Get all post office details for PIN Code $pincode. Find locality, district, and state information for $pincode instantly at Online Law.";
		$page_title = "PIN Code $pincode Details - Online Law";
	} else {
		$meta_desc = "Search PIN Code of all states and UT of India in one click for free. Easily find post office detials using Online Law.";
		$page_title = "PIN Code Search by PIN : Online Law";
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
<meta name="keywords" content="Search PIN, <?php echo $pincode; ?>, PIN Code, PINCODE, Post Office, Where is my Post Office, Local Post Office, PIN Code Download">
<meta name="author" content="OfferPlant">
<title><?php echo $page_title; ?></title>

<?php require_once ('includes/header.php'); ?> 

<!-- Banner Section Start -->
<section class="bg-img-3 overlay p-0 full-row" style="padding: 20px 0 !important;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="banner-text text-center">
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Find PIN Code</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">PIN Search By Digital Code</li>
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
						<h3 class="pb-2" style="font-family: var(--font-heading); font-weight: 700;">PIN / Postal Index Number</h3>
						<span style="color: var(--text-muted); font-size: 15px;">Enter a 6-digit PIN code below to instantly retrieve Post Office details.</span>
					</div>
					
					<form method="post" action="<?php echo $base_url; ?>pincode" class="form-style-1 mb-5">
					  <div class="row justify-content-center">
						<div class="col-lg-5 col-md-8">
						  <div class="form-group mb-0 position-relative">
                            <i class="fas fa-search position-absolute" style="top: 50%; transform: translateY(-50%); left: 20px; color: var(--text-muted);"></i>
							<input class="form-control" style="padding-left: 50px !important; height: 55px; border-radius: var(--radius-md);" type="number" placeholder="e.g. 110001" maxlength="6" minlength="6" name="pincode" value="<?php echo htmlentities($pincode); ?>" required>
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
                        if(isset($_REQUEST['pincode']))
                        {
                            extract($_REQUEST);
                            // Safety Check for empty
                            if(!empty($pincode)) {
                                $safe_pincode = addslashes($pincode);
                                $sql = "select * from pincode where pincode ='$safe_pincode' order by locality_name";
                                $res = direct_sql($sql);
                                $ct =$res['count'];
                                
                                if($ct >0)
                                {
                                    $i=1;
                    ?>
                                    <div class="table-responsive mt-4">
                                        <h4 class="text-primary mb-4" style="font-family: var(--font-heading); font-weight: 600;">Showing results for PIN Code "<?php echo htmlentities($pincode); ?>" </h4>
                                        <table class="display responsive nowrap w-100" id="pincode_table">
                                            <thead> 
                                            <tr>
                                                <th> No </th>
                                                <th> Locality  </th>
                                                <th> Post Office </th>
                                                <th> Sub District</th>
                                                <th> District</th>
                                                <th>State / UT</th>
                                                <th> PIN Code </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            
                                            <?php
                                                foreach( $res['data'] as $row)
                                                { 
                                            ?>
                                                <tr>
                                                    <td> <?php echo $i; ?> </td>
                                                    <td> <?php echo htmlentities($row['locality_name']); ?> </td>
                                                    <td> <?php echo htmlentities($row['office_name']); ?> </td>
                                                    <td> <?php echo htmlentities($row['sub_district_name']); ?> </td>
                                                    <td> <?php echo htmlentities($row['district_name']); ?> </td>
                                                    <td> <?php echo htmlentities($row['state_name']); ?> </td>
                                                    <td> <span class="badge" style="background: var(--primary-light); color: var(--primary-color); font-size: 14px; padding: 6px 12px; border-radius: 6px;"><?php echo htmlentities($row['pincode']); ?></span> </td>
                                                </tr>
                                            <?php 
                                                    $i =$i+1; 
                                                }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                    <?php
                                }
                                else
                                {
                    ?>
                                    <div class="alert mt-4" style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: var(--radius-md); padding: 15px 20px;"> 
                                        <i class="fas fa-exclamation-circle mr-2"></i> Sorry! No results found for PIN Code <b> <?php echo htmlentities($pincode); ?> </b>
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
        $('#pincode_table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            pageLength: 10
        });
    });
</script>

