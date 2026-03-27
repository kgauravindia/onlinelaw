<?php
	require_once ('includes/function.php'); 
    $page_title = "Contact Us - Online Law";
    $meta_desc = "Get in touch with Online Law for any information or legal resource support. We are here to help every citizen.";
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

<!-- Banner Section Start -->
<section class="bg-img-3 overlay p-0 full-row" style="padding: 20px 0 !important;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="banner-text text-center">
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Contact Us</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">Contact</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Banner Section End --> 

<section class="full-row bg-body py-80">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-10">
				<div class="detail-area p-5" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); background: #fff;">
					<div class="row">
						<div class="col-lg-7">
							<h3 class="mb-4" style="font-family: var(--font-heading); font-weight: 700;">Get in Touch</h3>
							<p class="mb-5 text-muted">Have a question or need legal information? Fill out the form below and our team will get back to you shortly.</p>
							
							<form class="form-style-1 contact-message" id="contact-form" action="<?php echo $base_url; ?>mail.php" method="post">
								<div class="form-group mb-4">
									<input class="form-control" type="text" name="name" placeholder="Your Name" required style="height: 55px; border-radius: var(--radius-md);">
								</div>
								<div class="form-group mb-4">
									<input class="form-control" type="email" name="email" placeholder="Email Address" required style="height: 55px; border-radius: var(--radius-md);">
								</div>
								<div class="form-group mb-4">
									<input class="form-control" type="text" name="subject" placeholder="Subject" required style="height: 55px; border-radius: var(--radius-md);">
								</div>
								<div class="form-group mb-4">
									<textarea class="form-control" name="message" placeholder="Message" required style="border-radius: var(--radius-md); min-height: 150px; padding: 15px;"></textarea>
								</div>
								<div class="form-group mb-0">
									<input class="btn btn-primary w-100" id="send" value="Send Message" type="submit" style="height: 55px; font-weight: 600; border-radius: var(--radius-md);">
								</div>
								<div class="mt-3">
									<div class="error-handel">
										<div id="success" class="alert alert-success" style="display:none;">Your message has been sent successfully. Thank you!</div>
										<div id="error" class="alert alert-danger" style="display:none;">An error occurred while sending your message. Please try again.</div>
									</div>
								</div>
							</form>
						</div>
						
						<div class="col-lg-5 mt-5 mt-lg-0 pl-lg-5">
							<div class="contact-info-block" style="border-left: 1px solid var(--border-soft); padding-left: 30px;">
								<h4 class="mb-4" style="font-family: var(--font-heading); font-weight: 700;">Office Info</h4>
								
								<div class="mb-4 d-flex align-items-start">
									<div class="icon-box mr-3" style="width: 45px; height: 45px; background: var(--primary-light); color: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
										<i class="fas fa-envelope"></i>
									</div>
									<div>
										<h6 class="mb-1" style="font-weight: 700;">Email Us</h6>
										<span><a href="mailto:<?php echo $inst_email;?>" class="text-muted"> <?php echo $inst_email; ?> </a></span>
									</div>
								</div>
								
								<div class="mb-4 d-flex align-items-start">
									<div class="icon-box mr-3" style="width: 45px; height: 45px; background: #e0f2fe; color: #0284c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
										<i class="fas fa-map-marker-alt"></i>
									</div>
									<div>
										<h6 class="mb-1" style="font-weight: 700;">Our Location</h6>
										<span class="text-muted"><?php echo $inst_address1; ?>, <?php echo $inst_address2; ?></span>
									</div>
								</div>
								
								<div class="mb-4 d-flex align-items-start">
									<div class="icon-box mr-3" style="width: 45px; height: 45px; background: #f0fdf4; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
										<i class="fas fa-phone-alt"></i>
									</div>
									<div>
										<h6 class="mb-1" style="font-weight: 700;">Call Support</h6>
										<span class="text-muted"><?php echo $inst_contact; ?></span>
									</div>
								</div>
								
								<div class="d-flex align-items-start">
									<div class="icon-box mr-3" style="width: 45px; height: 45px; background: #fef2f2; color: #dc2626; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
										<i class="fab fa-whatsapp"></i>
									</div>
									<div>
										<h6 class="mb-1" style="font-weight: 700;">WhatsApp</h6>
										<span><a href="<?php echo $inst_whatsapp; ?>" target="_blank" class="text-muted">Join Our Channel</a></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!--Message Location Section Start-->
<div class="full-row" id="message-location">
    <div class="container-fluid">
        <div class="row">
			<div class="col-lg-12">
			
				<div id="map" style="margin: 0 -15px"> 
				<?php echo $google_map; ?> 
				</div>
			</div>
        </div>
    </div>
</div>
<!--Message Location Section End--> 
<?php require_once ('includes/footer.php'); ?> 
