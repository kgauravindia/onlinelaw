<?php
	require_once ('includes/function.php'); 
    $page_title = "Contact Us | " . $inst_name;
    $meta_desc = "Get in touch with $inst_name for any information or legal resource support. We are here to help every citizen.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="advocate, contact online law, legal help, attorney, business law, consulting, corporate, justice, law firm, law office, lawyer, legal assistant">
<meta name="author" content="OfferPlant">
<title><?php echo $page_title; ?></title>

<?php require_once ('includes/header.php'); ?> 

<!-- Hero Banner -->
<section class="bg-img-3 overlay py-5" style="background-color: #0f172a; border-bottom: 2px solid var(--primary-color);">
	<div class="container py-4">
		<div class="row align-items-center">
			<div class="col-lg-12 text-center text-white">
				<h1 class="display-4 fw-bold mb-3" style="font-family: var(--font-heading); font-weight: 800; letter-spacing: -1px;">Let's Connect</h1>
				<p class="lead opacity-75 mb-0" style="font-family: var(--font-body); max-width: 600px; margin: 0 auto;">Reach out to Online Law for trusted legal resource support and expert information.</p>
			</div>
		</div>
	</div>
</section>

<!-- Main Contact Section -->
<section class="py-5 bg-white">
	<div class="container py-5">
		<div class="row g-5">
            <!-- Left: Contact Info -->
			<div class="col-lg-4 mb-5 mb-lg-0">
                <div class="pe-lg-5">
                    <h2 class="h3 mb-4" style="font-family: var(--font-heading); color: var(--text-main);">Contact Information</h2>
                    <p class="text-muted mb-5">Our office is located in the heart of Chapra. We're here to assist you during business hours.</p>
                    
                    <div class="space-y-4">
                        <div class="d-flex align-items-center mb-4 p-4" style="background: #f8fafc; border-radius: 15px; border-left: 4px solid var(--primary-color);">
                            <div class="flex-shrink-0 me-3 mr-3">
                                <i class="fas fa-map-marker-alt text-primary fa-lg"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-bold text-dark" style="font-weight: 700;">Office Address</p>
                                <p class="mb-0 text-muted small"><?php echo $inst_address1; ?>, <?php echo $inst_address2; ?></p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-4 p-4" style="background: #f8fafc; border-radius: 15px; border-left: 4px solid #10b981;">
                            <div class="flex-shrink-0 me-3 mr-3">
                                <i class="fas fa-envelope text-success fa-lg"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-bold text-dark" style="font-weight: 700;">Email Address</p>
                                <p class="mb-0 text-muted small"><?php echo $inst_email; ?></p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-4 p-4" style="background: #f8fafc; border-radius: 15px; border-left: 4px solid #f59e0b;">
                            <div class="flex-shrink-0 me-3 mr-3">
                                <i class="fas fa-phone-alt text-warning fa-lg"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-bold text-dark" style="font-weight: 700;">Phone Support</p>
                                <p class="mb-0 text-muted small"><?php echo $inst_contact; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
			</div>

            <!-- Right: Contact Form -->
			<div class="col-lg-8">
				<div class="p-5 shadow-sm border" style="background: #fff; border-radius: 20px;">
					<h3 class="mb-4" style="font-family: var(--font-heading); font-weight: 700;">Send us a Message</h3>
					<form id="contact-form" action="<?php echo $base_url; ?>mail.php" method="post">
						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label small fw-bold text-uppercase" style="letter-spacing: 1px;">Full Name</label>
								<input class="form-control" type="text" name="name" placeholder="John Doe" required style="height: 55px; border-radius: 10px;">
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label small fw-bold text-uppercase" style="letter-spacing: 1px;">Email Address</label>
								<input class="form-control" type="email" name="email" placeholder="john@example.com" required style="height: 55px; border-radius: 10px;">
							</div>
						</div>
						<div class="mb-4 mt-3">
							<label class="form-label small fw-bold text-uppercase" style="letter-spacing: 1px;">Your Message</label>
							<textarea class="form-control" name="message" placeholder="How can we help you?" required style="border-radius: 10px; min-height: 180px; padding: 15px;"></textarea>
						</div>
						<div class="text-end">
							<button class="btn btn-primary" type="submit" style="padding: 15px 40px; border-radius: 10px; font-weight: 700; width: 220px;">
                                <i class="fas fa-paper-plane mr-2"></i> Send Message
                            </button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Full-Width Immersive Map -->
<section id="map-section" style="height: 500px; position: relative; border-top: 1px solid #e2e8f0;">
    <div style="width: 100%; height: 100%;">
        <?php echo $google_map; ?>
    </div>
    <div style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%);">
        <a href="<?php echo $google_map_url; ?>" target="_blank" class="btn btn-dark shadow-lg py-3 px-5" style="border-radius: 50px; font-weight: 700; letter-spacing: 1px;">
            <i class="fas fa-directions mr-2"></i> VIEW IN GOOGLE MAPS
        </a>
    </div>
</section>
<?php require_once ('includes/footer.php'); ?> 
