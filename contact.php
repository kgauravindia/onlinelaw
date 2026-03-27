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

<!-- Full Screen Map Hero -->
<section id="contact-hero" style="position: relative; height: calc(100vh - 100px); min-height: 800px; width: 100%; overflow: hidden; background-color: var(--bg-body);">
    <!-- The Map (Full Screen) -->
    <div id="full-map-container" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;">
        <?php echo $google_map; ?>
    </div>

    <!-- Floating Contact Card -->
    <div class="container" style="position: relative; z-index: 2; height: 100%; display: flex; align-items: center; pointer-events: none;">
        <div class="row w-100 m-0">
            <div class="col-lg-5 col-md-8" style="pointer-events: auto;">
                <div class="detail-area p-5 shadow-lg" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.8); border-radius: var(--radius-lg);">
                    <h3 class="mb-4" style="font-family: var(--font-heading); font-weight: 700;">Get in Touch</h3>
                    <p class="mb-4 text-muted small">Need legal information or resource support? Our team is here to help.</p>

                    <div class="contact-info-list mb-4">
                        <div class="d-flex mb-3">
                            <i class="fas fa-map-marker-alt text-primary mt-1 mr-3"></i>
                            <span class="small text-muted"><?php echo $inst_address1; ?>, <?php echo $inst_address2; ?></span>
                        </div>
                        <div class="d-flex mb-3">
                            <i class="fas fa-envelope text-primary mt-1 mr-3"></i>
                            <span class="small text-muted"><?php echo $inst_email; ?></span>
                        </div>
                        <div class="d-flex mb-3">
                            <i class="fas fa-phone-alt text-primary mt-1 mr-3"></i>
                            <span class="small text-muted"><?php echo $inst_contact; ?></span>
                        </div>
                    </div>

                    <form class="contact-message" id="contact-form" action="<?php echo $base_url; ?>mail.php" method="post">
                        <div class="form-group mb-3">
                            <input class="form-control" type="text" name="name" placeholder="Name" required style="height: 45px; font-size: 14px; border-radius: var(--radius-md);">
                        </div>
                        <div class="form-group mb-3">
                            <input class="form-control" type="email" name="email" placeholder="Email" required style="height: 45px; font-size: 14px; border-radius: var(--radius-md);">
                        </div>
                        <div class="form-group mb-4">
                            <textarea class="form-control" name="message" placeholder="Message" required style="border-radius: var(--radius-md); min-height: 100px; padding: 12px; font-size: 14px;"></textarea>
                        </div>
                        <div class="form-group mb-0">
                            <input class="btn btn-primary w-100" id="send" value="Send Message" type="submit" style="height: 45px; font-weight: 600; font-size: 14px; border-radius: var(--radius-md);">
                        </div>
                    </form>
                    
                    <div class="mt-4 pt-3 border-top">
                        <a href="<?php echo $google_map_url; ?>" target="_blank" class="btn btn-outline-primary w-100" style="border-radius: var(--radius-md); font-weight: 500; font-size: 13px;">
                            <i class="fas fa-directions mr-2"></i> GET DIRECTIONS ON GOOGLE
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once ('includes/footer.php'); ?> 
