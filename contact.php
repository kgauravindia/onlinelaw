<?php
	require_once ('includes/function.php'); 
    
    $msg_status = "";
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_contact'])) {
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $mobile = mysqli_real_escape_string($con, $_POST['mobile']);
        $area = mysqli_real_escape_string($con, $_POST['area']);
        $message = mysqli_real_escape_string($con, $_POST['message']);
        
        $sql = "INSERT INTO contact (name, email, mobile, area, message) VALUES ('$name', '$email', '$mobile', '$area', '$message')";
        if (mysqli_query($con, $sql)) {
            $msg_status = "success";
            
            // --- Common Headers for HTML Mail ---
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: Online Law <noreply@olaw.in>' . "\r\n";

            // --- 1. Admin Email (care@olaw.in) ---
            $admin_subject = "New Inquiry Received: " . $area;
            $admin_body = "
            <html>
            <head><title>New Contact Inquiry</title></head>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px; border-radius: 10px;'>
                    <h2 style='color: #0284c7;'>New Contact Submission</h2>
                    <p>You have received a new inquiry from the Online Law website.</p>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Name:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>$name</td></tr>
                        <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Email:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>$email</td></tr>
                        <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Mobile:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>$mobile</td></tr>
                        <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>Area:</strong></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>$area</td></tr>
                        <tr><td style='padding: 8px; vertical-align: top;'><strong>Message:</strong></td><td style='padding: 8px;'>$message</td></tr>
                    </table>
                    <p style='font-size: 12px; color: #777; margin-top: 20px;'>This is an automated notification from Online Law.</p>
                </div>
            </body>
            </html>";
            @mail('care@olaw.in', $admin_subject, $admin_body, $headers);

            // --- 2. Visitor Email (Thank You) ---
            if (!empty($email)) {
                $visitor_subject = "We have received your message - Online Law";
                $visitor_body = "
                <html>
                <head><title>Thank You - Online Law</title></head>
                <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                    <div style='max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px; border-radius: 10px;'>
                        <h2 style='color: #0284c7;'>Dear $name,</h2>
                        <p>Thank you for reaching out to <strong>Online Law</strong>. We have successfully received your inquiry regarding <strong>$area</strong>.</p>
                        <p>Our team is currently reviewing your message and will get back to you as soon as possible via email or your provided mobile number ($mobile).</p>
                        <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                        <p><strong>Your Message Summary:</strong></p>
                        <p style='font-style: italic; color: #555;'>\"$message\"</p>
                        <p>Best Regards,<br><strong>Team Online Law</strong><br><a href='https://olaw.in' style='color: #0284c7; text-decoration: none;'>www.olaw.in</a></p>
                    </div>
                </body>
                </html>";
                @mail($email, $visitor_subject, $visitor_body, $headers);
            }
        } else {
            $msg_status = "error";
        }
    }

    $page_title = "Contact Us | Support for Bank IFSC, PIN Code & Legal Tools - Online Law";
    $meta_desc = "Have questions about our Bank IFSC, PIN Code, or GST HSN/SAC tools? Contact the Online Law support team for help, API inquiries, or suggestions. We're here for every citizen.";
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
<style>
    .social-hover-primary {
        transition: all 0.3s ease;
    }
    .social-hover-primary:hover {
        color: var(--primary-color) !important;
        transform: scale(1.2);
    }
</style>
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
                        <div class="small text-muted" style="line-height: 1.5;">
                            <strong class="text-dark" style="font-size: 14px;">Online Law</strong><br>
                            an Initiative of<br>
                            <strong class="text-dark" style="font-size: 14px;">OfferPlant Technologies Private Limited</strong>
                        </div>
                        <h4 class="small fw-bold text-uppercase text-primary mt-4 mb-3" style="letter-spacing: 2px;">Office</h4>
                        <p class="text-dark mb-0" style="font-weight: 600; line-height: 1.6;"><?php echo $inst_address1; ?><br><?php echo $inst_address2; ?></p>
                    </div>

                    <div class="mb-5">
                        <h4 class="small fw-bold text-uppercase text-success mb-3" style="letter-spacing: 2px;">Email</h4>
                        <p class="mb-0"><a href="mailto:<?php echo $inst_email; ?>" style="color: #1e293b; font-weight: 600; text-decoration: none; border-bottom: 2px solid var(--primary-light);"><?php echo $inst_email; ?></a></p>
                    </div>

                    <div class="mb-0">
                        <h4 class="small fw-bold text-uppercase text-warning mb-3" style="letter-spacing: 2px;">Phone</h4>
                        <p class="text-dark mb-0" style="font-weight: 600;"><?php echo $inst_contact; ?><br><?php echo $inst_contact2; ?></p>
                    </div>
                </div>
            </div>

            <!-- Form Column -->
            <div class="col-lg-7 col-md-7">
                <div class="h-100 p-5" style="border: 1px solid #f1f5f9; border-radius: 24px; background: #fff;">
                    
                    <?php if ($msg_status == "success"): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; border: none; background: #ecfdf5; color: #065f46;">
                            <strong>Thank you!</strong> Your message has been sent successfully. We will get back to you shortly.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                    <?php elseif ($msg_status == "error"): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; border: none; background: #fef2f2; color: #991b1b;">
                            <strong>Sorry!</strong> There was an error processing your request. Please try again.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <form id="contact-form" action="" method="post">
                        <div class="row g-4">
                            <div class="col-md-6 mb-4">
                                <label class="small text-muted mb-2">FULL NAME</label>
                                <input class="form-control" type="text" name="name" placeholder="John Doe" required style="border-width: 0 0 1px 0 !important; border-radius: 0 !important; padding: 10px 0 !important; background: transparent !important; font-size: 16px;">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="small text-muted mb-2">EMAIL ADDRESS</label>
                                <input class="form-control" type="email" name="email" placeholder="john@example.com" required style="border-width: 0 0 1px 0 !important; border-radius: 0 !important; padding: 10px 0 !important; background: transparent !important; font-size: 16px;">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="small text-muted mb-2">MOBILE NO</label>
                                <input class="form-control" type="tel" name="mobile" placeholder="+91 91234 56789" required style="border-width: 0 0 1px 0 !important; border-radius: 0 !important; padding: 10px 0 !important; background: transparent !important; font-size: 16px;">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="small text-muted mb-2">AREA OF CONTACT</label>
                                <select class="form-control" name="area" required style="border-width: 0 0 1px 0 !important; border-radius: 0 !important; padding: 10px 0 !important; background: transparent !important; font-size: 16px; height: 45px;">
                                    <option value="" disabled selected>Select Area</option>
                                    <option value="API">API</option>
                                    <option value="Complain">Complain</option>
                                    <option value="Suggestion">Suggestion</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-12 mb-5">
                                <label class="small text-muted mb-2">HOW CAN WE HELP?</label>
                                <textarea class="form-control" name="message" placeholder="Type your message here..." required style="border-width: 0 0 1px 0 !important; border-radius: 0 !important; padding: 10px 0 !important; background: transparent !important; min-height: 100px; font-size: 16px;"></textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-dark w-100 py-3" type="submit" name="submit_contact" style="border-radius: 50px; font-weight: 600; font-size: 16px; letter-spacing: 1px;">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Social Media Tray -->
            <div class="col-lg-1 d-none d-lg-flex flex-column justify-content-center align-items-center">
                <div class="d-flex flex-column align-items-center gap-4" style="gap: 30px;">
                    <a href="<?php echo $facebook; ?>" target="_blank" class="text-muted social-hover-primary" style="font-size: 20px;"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo $twitter; ?>" target="_blank" class="text-muted social-hover-primary" style="font-size: 20px;"><i class="fab fa-twitter"></i></a>
                    <a href="<?php echo $linkedin; ?>" target="_blank" class="text-muted social-hover-primary" style="font-size: 20px;"><i class="fab fa-linkedin-in"></i></a>
                    <a href="<?php echo $youtube; ?>" target="_blank" class="text-muted social-hover-primary" style="font-size: 20px;"><i class="fab fa-youtube"></i></a>
                    <a href="<?php echo $pinterest; ?>" target="_blank" class="text-muted social-hover-primary" style="font-size: 20px;"><i class="fab fa-pinterest-p"></i></a>
                    <a href="<?php echo $instagram; ?>" target="_blank" class="text-muted social-hover-primary" style="font-size: 20px;"><i class="fab fa-instagram"></i></a>
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
