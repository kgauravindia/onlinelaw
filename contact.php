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

<?php require_once ('includes/header.php'); ?> 

<section class="bg-white py-5">
    <div class="container py-5">
        <!-- Header -->
        <div class="text-center mb-5 pb-4">
            <h1 class="display-4" style="font-family: var(--font-heading); font-weight: 700; color: #1e293b; letter-spacing: -2px;">Contact Us</h1>
            <p class="text-muted mx-auto" style="max-width: 600px; font-size: 18px;">We're here to help you navigate the legal information you need. Reach out to our team anytime.</p>
        </div>

        <div class="row align-items-stretch justify-content-center">
            <!-- Details Column -->
            <div class="col-lg-4 col-md-5 mb-5 mb-md-0">
                <div class="h-100 p-5 d-flex flex-column justify-content-center" style="border: 1px solid #f1f5f9; background: #fafafa; border-radius: 24px;">
                    <div class="mb-5">
                        <h4 class="small fw-bold text-uppercase text-primary mb-3" style="letter-spacing: 2px;">Office</h4>
                        <p class="text-dark mb-0" style="font-weight: 600; line-height: 1.6;"><?php echo $inst_address1; ?><br><?php echo $inst_address2; ?></p>
                    </div>

                    <div class="mb-5">
                        <h4 class="small fw-bold text-uppercase text-success mb-3" style="letter-spacing: 2px;">Email</h4>
                        <p class="mb-0"><a href="mailto:<?php echo $inst_email; ?>" style="color: #1e293b; font-weight: 600; text-decoration: none; border-bottom: 2px solid var(--primary-light);"><?php echo $inst_email; ?></a></p>
                    </div>

                    <div class="mb-0">
                        <h4 class="small fw-bold text-uppercase text-warning mb-3" style="letter-spacing: 2px;">Phone</h4>
                        <p class="text-dark mb-0" style="font-weight: 600;"><?php echo $inst_contact; ?></p>
                    </div>
                </div>
            </div>

            <!-- Form Column -->
            <div class="col-lg-6 col-md-7">
                <div class="h-100 p-5" style="border: 1px solid #f1f5f9; border-radius: 24px; background: #fff;">
                    <form id="contact-form" action="<?php echo $base_url; ?>mail.php" method="post">
                        <div class="row g-4">
                            <div class="col-12 mb-4">
                                <label class="small text-muted mb-2">FULL NAME</label>
                                <input class="form-control" type="text" name="name" placeholder="John Doe" required style="border-width: 0 0 1px 0 !important; border-radius: 0 !important; padding: 10px 0 !important; background: transparent !important; font-size: 16px;">
                            </div>
                            <div class="col-12 mb-4">
                                <label class="small text-muted mb-2">EMAIL ADDRESS</label>
                                <input class="form-control" type="email" name="email" placeholder="john@example.com" required style="border-width: 0 0 1px 0 !important; border-radius: 0 !important; padding: 10px 0 !important; background: transparent !important; font-size: 16px;">
                            </div>
                            <div class="col-12 mb-5">
                                <label class="small text-muted mb-2">HOW CAN WE HELP?</label>
                                <textarea class="form-control" name="message" placeholder="Type your message here..." required style="border-width: 0 0 1px 0 !important; border-radius: 0 !important; padding: 10px 0 !important; background: transparent !important; min-height: 100px; font-size: 16px;"></textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-dark w-100 py-3" type="submit" style="border-radius: 50px; font-weight: 600; font-size: 16px; letter-spacing: 1px;">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Immersive Bottom Map (Verifiied Location) -->
<section style="height: 480px; width: 100%; filter: grayscale(1); transition: filter 0.6s ease; border-top: 1px solid #f1f5f9;" onmouseover="this.style.filter='grayscale(0)'" onmouseout="this.style.filter='grayscale(1)'">
    <div style="width: 100%; height: 100%;">
        <?php echo $google_map; ?>
    </div>
</section>
<?php require_once ('includes/footer.php'); ?> 
