<?php 
    require_once('includes/function.php');

    // Automatic Smart Routing: Check if 404 request matches a company name/CIN slug
    $req_uri = $_SERVER['REQUEST_URI'] ?? '';
    $req_path = parse_url($req_uri, PHP_URL_PATH);
    $req_slug = basename(rtrim($req_path, '/'));

    if (!empty($req_slug)) {
        $matched_comp = resolve_company_incorporation($req_slug);
        if ($matched_comp) {
            header("Location: " . company_url($matched_comp), true, 301);
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, follow">
<title>404 - Page Not Found | Online Law</title>

<?php 
    require_once ('includes/header.php'); 
?>

<!-- Page Banner -->
<section class="bg-img-3 overlay p-0 full-row" style="padding: 20px 0 !important;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="banner-text text-center">
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 28px; color: #fff !important; margin: 0;">404 Page Not Found</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 14px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">404 Error</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Error Section Content -->
<section class="full-row bg-body pt-80 pb-50" style="padding-top: 80px; min-height: 60vh;">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-10 text-center">
                <div class="error-visual mb-5">
                    <h1 style="font-family: var(--font-heading); font-size: 150px; font-weight: 800; line-height: 1; background: linear-gradient(135deg, var(--primary-color), #38bdf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 20px;">404</h1>
                    <h2 class="section-title mb-3" style="font-family: var(--font-heading); font-size: 36px;">Objection! <span style="color: var(--primary-color);">Page Not Found</span></h2>
                    <p style="font-size: 18px; color: var(--text-muted); max-width: 700px; margin: 0 auto;">The page you're searching for seems to have been dismissed. Let's get you back on track with our official legal tools.</p>
                </div>

                <!-- Tools Grid -->
                <div class="row mt-5">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="detail-area p-4 text-center" style="background: #fff; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-soft); transition: all 0.3s ease;">
                            <div class="icon-box mb-3">
                                <i class="fas fa-university" style="font-size: 32px; color: var(--primary-color);"></i>
                            </div>
                            <h5 style="font-family: var(--font-heading); font-weight: 700;">Bank IFSC</h5>
                            <p style="font-size: 13px; color: var(--text-muted);">Find any branch code instantly</p>
                            <a href="<?php echo $base_url; ?>bank-ifsc" style="color: var(--primary-color); font-weight: 600; text-decoration: none; font-size: 14px;">Find IFSC <i class="fas fa-arrow-right ml-1" style="font-size: 10px;"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="detail-area p-4 text-center" style="background: #fff; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-soft); transition: all 0.3s ease;">
                            <div class="icon-box mb-3">
                                <i class="fas fa-map-marker-alt" style="font-size: 32px; color: var(--primary-color);"></i>
                            </div>
                            <h5 style="font-family: var(--font-heading); font-weight: 700;">PIN Code</h5>
                            <p style="font-size: 13px; color: var(--text-muted);">Search 1.1M+ Pincode locations</p>
                            <a href="<?php echo $base_url; ?>pincode" style="color: var(--primary-color); font-weight: 600; text-decoration: none; font-size: 14px;">Locate PIN <i class="fas fa-arrow-right ml-1" style="font-size: 10px;"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="detail-area p-4 text-center" style="background: #fff; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border-soft); transition: all 0.3s ease;">
                            <div class="icon-box mb-3">
                                <i class="fas fa-file-invoice-dollar" style="font-size: 32px; color: var(--primary-color);"></i>
                            </div>
                            <h5 style="font-family: var(--font-heading); font-weight: 700;">GST Finder</h5>
                            <p style="font-size: 13px; color: var(--text-muted);">Complete HSN & SAC directory</p>
                            <a href="<?php echo $base_url; ?>gst-hsn-sac" style="color: var(--primary-color); font-weight: 600; text-decoration: none; font-size: 14px;">Search GST <i class="fas fa-arrow-right ml-1" style="font-size: 10px;"></i></a>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <a href="<?php echo $base_url; ?>index" class="btn btn-primary px-5 py-3" style="border-radius: var(--radius-md); font-weight: 700;">Return to Homepage</a>
                </div>
			</div>
		</div>
	</div>
</section>

<?php require_once ('includes/footer.php'); ?> 
</body>
</html>
