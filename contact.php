<?php
    require_once ('includes/function.php'); 
    
    $msg_status = "";
    $msg_text = "";

    $req_type = isset($_GET['type']) ? trim($_GET['type']) : '';
    $req_cin = isset($_GET['cin']) ? trim($_GET['cin']) : '';
    $req_name = isset($_GET['name']) ? trim($_GET['name']) : '';
    $is_removal_request = ($req_type === 'removal' || $req_type === 'delete_company' || !empty($req_cin));

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_contact'])) {
        $name = mysqli_real_escape_string($con, trim($_POST['name'] ?? ''));
        $email = mysqli_real_escape_string($con, trim($_POST['email'] ?? ''));
        $mobile = mysqli_real_escape_string($con, trim($_POST['mobile'] ?? ''));
        $area = mysqli_real_escape_string($con, trim($_POST['area'] ?? 'General Inquiry'));
        $message = mysqli_real_escape_string($con, trim($_POST['message'] ?? ''));
        
        if (!empty($name) && !empty($mobile) && !empty($message)) {
            $created_by_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
            $sql = "INSERT INTO contact (name, email, mobile, area, message, status, created_by, updated_by) VALUES ('$name', '$email', '$mobile', '$area', '$message', 'NEW', $created_by_id, $created_by_id)";
            try {
                if (mysqli_query($con, $sql)) {
                    $insert_id = mysqli_insert_id($con);
                    $msg_status = "success";
                    $msg_text = ($is_removal_request || stripos($area, 'Complain') !== false || stripos($area, 'Correction') !== false) 
                        ? "Thank you! Your request / inquiry has been logged with priority (Ticket ID: #" . $insert_id . "). Our team will review and take action within standard turnaround time."
                        : "Thank you! Your message has been received successfully. Our team will get back to you shortly.";
                    
                    // --- Common Headers for HTML Mail ---
                    $headers = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
                    $headers .= "From: Online Law Support <noreply@olaw.in>\r\n";

                    // --- 1. Admin Email (care@olaw.in) ---
                    $admin_email_to = !empty($inst_email) ? $inst_email : 'care@olaw.in';
                    $admin_subject = ($is_removal_request)
                        ? "🚨 [URGENT DATA REMOVAL COMPLAINT] Company: " . ($req_name ?: $name) . " (" . ($req_cin ?: $mobile) . ")"
                        : "New Inquiry: [" . $area . "] from " . $name;
                    $admin_body = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset='utf-8'>
                        <style>
                            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #1e293b; background: #f8fafc; margin: 0; padding: 20px; }
                            .card { max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
                            .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; padding: 24px; text-align: center; }
                            .content { padding: 24px; }
                            .field-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                            .field-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
                            .label { color: #64748b; font-weight: 600; width: 30%; }
                            .value { color: #0f172a; font-weight: 500; }
                            .msg-box { background: #f8fafc; border-left: 4px solid #2563eb; padding: 14px; border-radius: 6px; margin-top: 15px; font-size: 14px; color: #334155; }
                            .footer { background: #f8fafc; padding: 14px 24px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
                        </style>
                    </head>
                    <body>
                        <div class='card'>
                            <div class='header'>
                                <h2 style='margin: 0; font-size: 20px;'>New Contact Form Inquiry</h2>
                                <p style='margin: 4px 0 0; color: #94a3b8; font-size: 13px;'>Online Law Portal &bull; olaw.in</p>
                            </div>
                            <div class='content'>
                                <table class='field-table'>
                                    <tr><td class='label'>Sender Name:</td><td class='value'><strong>" . htmlspecialchars($name) . "</strong></td></tr>
                                    <tr><td class='label'>Email Address:</td><td class='value'><a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></td></tr>
                                    <tr><td class='label'>Phone Number:</td><td class='value'><a href='tel:" . htmlspecialchars($mobile) . "'>" . htmlspecialchars($mobile) . "</a></td></tr>
                                    <tr><td class='label'>Area of Inquiry:</td><td class='value'><span style='background: #eff6ff; color: #1d4ed8; padding: 3px 8px; border-radius: 4px; font-weight: 600;'>" . htmlspecialchars($area) . "</span></td></tr>
                                </table>
                                <div style='margin-top: 20px;'>
                                    <strong style='font-size: 13px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;'>Message:</strong>
                                    <div class='msg-box'>" . nl2br(htmlspecialchars($message)) . "</div>
                                </div>
                            </div>
                            <div class='footer'>
                                This is an automated notification from the Online Law web portal.
                            </div>
                        </div>
                    </body>
                    </html>";
                    @mail($admin_email_to, $admin_subject, $admin_body, $headers);

                    // --- 2. Visitor Confirmation Email ---
                    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $visitor_subject = "We have received your message - Online Law";
                        $visitor_body = "
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <meta charset='utf-8'>
                            <style>
                                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #1e293b; background: #f8fafc; margin: 0; padding: 20px; }
                                .card { max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
                                .header { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff; padding: 28px; text-align: center; }
                                .content { padding: 28px; font-size: 15px; color: #334155; }
                                .quote-box { background: #f8fafc; border-left: 3px solid #2563eb; padding: 12px 16px; font-style: italic; color: #475569; margin: 16px 0; border-radius: 4px; font-size: 14px; }
                                .footer { background: #f8fafc; padding: 18px 28px; text-align: center; font-size: 13px; color: #64748b; border-top: 1px solid #f1f5f9; }
                            </style>
                        </head>
                        <body>
                            <div class='card'>
                                <div class='header'>
                                    <h2 style='margin: 0; font-size: 22px;'>Thank You for Reaching Out</h2>
                                    <p style='margin: 6px 0 0; color: #bfdbfe; font-size: 14px;'>Team Online Law &bull; India</p>
                                </div>
                                <div class='content'>
                                    <p>Dear <strong>" . htmlspecialchars($name) . "</strong>,</p>
                                    <p>Thank you for contacting <strong>Online Law</strong>. We have received your inquiry regarding <strong>" . htmlspecialchars($area) . "</strong>.</p>
                                    <p>Our support team is reviewing your message and will get back to you shortly via email or phone (" . htmlspecialchars($mobile) . ").</p>
                                    <div class='quote-box'>
                                        \"" . nl2br(htmlspecialchars($message)) . "\"
                                    </div>
                                    <p style='margin-bottom: 0;'>If you need immediate assistance with our public tools (Company Search, NIC Codes, IFSC, or PIN Codes), feel free to explore them on our portal.</p>
                                </div>
                                <div class='footer'>
                                    Best Regards,<br>
                                    <strong>Team Online Law</strong><br>
                                    <span style='font-size: 12px; color: #94a3b8;'>An initiative of OfferPlant Technologies Private Limited</span><br>
                                    <a href='https://olaw.in' style='color: #2563eb; text-decoration: none; font-weight: 600;'>www.olaw.in</a>
                                </div>
                            </div>
                        </body>
                        </html>";
                        @mail($email, $visitor_subject, $visitor_body, $headers);
                    }
                } else {
                    $msg_status = "error";
                    $msg_text = "Database error: Could not save your message. Please try again or email us directly.";
                }
            } catch (Exception $e) {
                $msg_status = "error";
                $msg_text = "An error occurred while submitting: " . $e->getMessage();
            }
        } else {
            $msg_status = "error";
            $msg_text = "Please fill in all required fields (Name, Mobile Number, and Message).";
        }
    }

    $canonical_url = $base_url . "contact";
    $page_title = "Contact Us | Support, Inquiries & Helpdesk - Online Law";
    $meta_desc = "Have questions about Company Incorporations, NIC 2025 activity codes, Bank IFSC, PIN Code directory, or Legal Bare Acts? Reach out to the Online Law support team.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="keywords" content="contact online law, legal support india, company incorporation query, bank ifsc help, nic code query, pincode help, offerplant legal support, legal directory feedback">
<meta name="author" content="OfferPlant">
<title><?php echo $page_title; ?></title>

<!-- Schema.org JSON-LD -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "name": "Contact Online Law",
  "url": "<?php echo $canonical_url; ?>",
  "description": "<?php echo $meta_desc; ?>",
  "mainEntity": {
    "@type": "Organization",
    "name": "Online Law",
    "url": "<?php echo $base_url; ?>",
    "email": "<?php echo $inst_email; ?>",
    "telephone": "<?php echo $inst_contact; ?>",
    "parentOrganization": {
      "@type": "Corporation",
      "name": "OfferPlant Technologies Private Limited"
    },
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "<?php echo $inst_address1; ?>",
      "addressLocality": "<?php echo $inst_address2; ?>",
      "addressCountry": "IN"
    }
  }
}
</script>

<?php require_once ('includes/header.php'); ?> 

<style>
    /* Hero Header Styling */
    .contact-hero-section {
        background: linear-gradient(135deg, #090e1a 0%, #0f172a 45%, #1e293b 100%);
        padding: 56px 0 48px;
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .contact-hero-section::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 480px;
        height: 480px;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.35) 0%, rgba(37, 99, 235, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .contact-hero-section::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -5%;
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(6, 182, 212, 0.25) 0%, rgba(6, 182, 212, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .hero-gradient-text {
        background: linear-gradient(135deg, #60a5fa 0%, #38bdf8 45%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline;
    }
    .contact-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(37, 99, 235, 0.2);
        border: 1px solid rgba(96, 165, 250, 0.4);
        color: #93c5fd;
        padding: 6px 18px;
        border-radius: var(--radius-pill);
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Contact Channel Info Cards */
    .contact-card-premium {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-lg);
        padding: 28px 24px;
        transition: var(--transition-smooth);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.04);
        position: relative;
        overflow: hidden;
    }
    .contact-card-premium:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(37, 99, 235, 0.3);
    }
    .channel-icon-squircle {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }
    .contact-card-premium:hover .channel-icon-squircle {
        transform: scale(1.08);
    }

    /* Modern Form Styling */
    .contact-form-container {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        padding: 40px;
        position: relative;
    }
    @media (max-width: 768px) {
        .contact-form-container {
            padding: 24px 18px;
        }
    }
    .form-group-modern {
        margin-bottom: 22px;
        position: relative;
    }
    .form-label-modern {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .form-control-modern {
        height: 50px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 16px 10px 44px;
        font-size: 15px;
        color: #0f172a;
        background: #f8fafc;
        transition: var(--transition-smooth);
        font-family: var(--font-body);
        width: 100%;
    }
    .form-control-modern:focus {
        background: #ffffff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        outline: none;
    }
    .form-icon-wrap {
        position: absolute;
        left: 16px;
        bottom: 14px;
        color: #94a3b8;
        font-size: 16px;
        pointer-events: none;
        transition: color 0.3s;
    }
    .form-group-modern:focus-within .form-icon-wrap {
        color: var(--primary-color);
    }
    textarea.form-control-modern {
        height: auto;
        min-height: 120px;
        padding-top: 14px;
    }
    .form-group-textarea .form-icon-wrap {
        top: 40px;
        bottom: auto;
    }

    /* Side Context Sidebar */
    .sidebar-context-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: var(--radius-xl);
        color: #ffffff;
        padding: 36px 30px;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
    }
    .sidebar-context-card::before {
        content: '';
        position: absolute;
        top: -20%;
        right: -20%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.3) 0%, transparent 70%);
        border-radius: 50%;
    }
    .context-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 50px;
        font-size: 12.5px;
        color: #cbd5e1;
        margin-bottom: 8px;
        margin-right: 6px;
    }
    .social-icon-btn {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #f8fafc;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }
    .social-icon-btn:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: #ffffff;
        transform: translateY(-3px) scale(1.05);
    }

    /* FAQ Box */
    .faq-mini-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        padding: 20px;
        margin-bottom: 16px;
        transition: var(--transition-smooth);
    }
    .faq-mini-card:hover {
        border-color: #cbd5e1;
        box-shadow: var(--shadow-sm);
    }

    /* Map Overlay Card */
    .map-container-rel {
        position: relative;
        overflow: hidden;
        border-radius: var(--radius-xl);
        border: 1px solid #e2e8f0;
        box-shadow: var(--shadow-md);
        height: 440px;
    }
    .map-overlay-badge {
        position: absolute;
        top: 24px;
        left: 24px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 16px 20px;
        border-radius: var(--radius-md);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
        max-width: 320px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        z-index: 10;
    }
</style>

<!-- ================================================================= -->
<!-- HERO HEADER SECTION -->
<!-- ================================================================= -->
<section class="contact-hero-section">
    <div class="container position-relative" style="z-index: 2;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-3" style="font-size: 13.5px;">
                <li class="breadcrumb-item"><a href="<?php echo $base_url; ?>" class="text-white-50 text-decoration-none"><i class="fas fa-home mr-1" style="color: #38bdf8;"></i> Home</a></li>
                <li class="breadcrumb-item active" style="color: #93c5fd;" aria-current="page">Contact Us</li>
            </ol>
        </nav>

        <div class="row align-items-center">
            <div class="col-lg-10 col-xl-8">
                <div class="contact-badge-pill mb-3" style="background: rgba(37, 99, 235, 0.25); border: 1px solid rgba(96, 165, 250, 0.45); color: #93c5fd;">
                    <i class="fas fa-headset" style="color: #38bdf8;"></i> Helpdesk &amp; Inquiries
                </div>
                <h1 class="font-weight-bold text-white mb-3" style="font-family: var(--font-heading); font-size: clamp(28px, 4vw, 42px); line-height: 1.25; letter-spacing: -0.02em;">
                    Let's Connect &amp; Assist You
                </h1>
                <p class="lead mb-0" style="font-size: 16.5px; max-width: 680px; line-height: 1.7; color: #cbd5e1;">
                    Have questions regarding Indian Company Incorporations, NIC Codes, Bank IFSC, PIN Code directories, Bare Acts, or data removal? Submit your inquiry online for <span style="color: #38bdf8; font-weight: 600;">priority 3-day resolution</span>.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================= -->
<!-- CONTACT CHANNELS QUICK-CARDS ROW (PRIORITIZING FAST ONLINE TICKETS) -->
<!-- ================================================================= -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        
        <!-- Resolution SLA Banner -->
        <div class="p-3 mb-4 rounded-xl shadow-sm border d-flex flex-wrap align-items-center justify-content-between" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; border-color: rgba(255,255,255,0.1) !important;">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <div class="p-2 rounded-circle bg-primary text-white mr-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div>
                    <strong class="d-block" style="font-size: 14.5px;">Resolution Speed &amp; Processing Comparison:</strong>
                    <span class="small text-white-50">Choose the fastest channel for your inquiry or data correction request.</span>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2" style="gap: 10px;">
                <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 12px; border-radius: 20px;">
                    <i class="fas fa-bolt mr-1"></i> Online Form: 3 Days
                </span>
                <span class="badge badge-info text-white px-3 py-2 font-weight-bold" style="font-size: 12px; border-radius: 20px;">
                    <i class="fas fa-envelope mr-1"></i> Email: 7 Working Days
                </span>
            </div>
        </div>

        <div class="row">
            
            <!-- Card 1: Online Ticket & Contact Form (Recommended) -->
            <div class="col-lg-4 col-md-6 mb-4 d-flex">
                <div class="contact-card-premium w-100" style="border-top: 3px solid var(--primary-color);">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="channel-icon-squircle bg-primary text-white shadow-sm mb-0" style="background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;">
                                <i class="fas fa-edit"></i>
                            </div>
                            <span class="badge badge-primary px-2.5 py-1 text-uppercase font-weight-bold" style="font-size: 10px; border-radius: 20px;">
                                RECOMMENDED
                            </span>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-family: var(--font-heading); font-size: 18px;">
                            Online Ticket Form
                        </h5>
                        <div class="badge badge-success px-2.5 py-1 mb-2 font-weight-bold" style="font-size: 11px;">
                            <i class="fas fa-bolt mr-1"></i> Fast 3-Day SLA
                        </div>
                        <p class="text-muted small mb-3" style="line-height: 1.6;">
                            Direct priority queue with automated ticket tracking. Best for company delisting, NIC, IFSC, and legal directory queries.
                        </p>
                    </div>
                    <a href="#contact-form-section" class="btn btn-sm btn-primary font-weight-bold py-2 shadow-sm" style="border-radius: 8px;">
                        <i class="fas fa-paper-plane mr-1"></i> Fill Quick Form &rarr;
                    </a>
                </div>
            </div>

            <!-- Card 2: Email Desk -->
            <div class="col-lg-4 col-md-6 mb-4 d-flex">
                <div class="contact-card-premium w-100" style="border-top: 3px solid #10b981;">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="channel-icon-squircle text-white shadow-sm mb-0" style="background: linear-gradient(135deg, #059669, #10b981) !important;">
                                <i class="fas fa-envelope-open-text"></i>
                            </div>
                            <span class="badge badge-success px-2.5 py-1 text-uppercase font-weight-bold" style="font-size: 10px; border-radius: 20px;">
                                DIRECT DESK
                            </span>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-family: var(--font-heading); font-size: 18px;">
                            Email Support
                        </h5>
                        <div class="badge badge-info text-white px-2.5 py-1 mb-2 font-weight-bold" style="font-size: 11px;">
                            <i class="fas fa-clock mr-1"></i> 7 Working Days
                        </div>
                        <p class="text-muted small mb-2" style="line-height: 1.6;">
                            Send official letters, API partnership proposals, or supporting documentation directly to our support inbox.
                        </p>
                        <div class="mb-3">
                            <a href="mailto:<?php echo $inst_email; ?>" class="text-dark font-weight-bold text-decoration-none small">
                                <?php echo htmlspecialchars($inst_email); ?>
                            </a>
                        </div>
                    </div>
                    <a href="mailto:<?php echo $inst_email; ?>" class="btn btn-sm btn-light border font-weight-bold text-success py-2">
                        <i class="fas fa-envelope mr-1"></i> Send Email
                    </a>
                </div>
            </div>

            <!-- Card 3: Delisting & Complaints Desk -->
            <div class="col-lg-4 col-md-6 mb-4 d-flex">
                <div class="contact-card-premium w-100" style="border-top: 3px solid #ef4444;">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="channel-icon-squircle text-white shadow-sm mb-0" style="background: linear-gradient(135deg, #dc2626, #ef4444) !important;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <span class="badge badge-danger px-2.5 py-1 text-uppercase font-weight-bold" style="font-size: 10px; border-radius: 20px;">
                                PRIORITY DESK
                            </span>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1" style="font-family: var(--font-heading); font-size: 18px;">
                            Delisting &amp; Complaints
                        </h5>
                        <div class="badge badge-success px-2.5 py-1 mb-2 font-weight-bold" style="font-size: 11px;">
                            <i class="fas fa-bolt mr-1"></i> Fast 3-Day SLA
                        </div>
                        <p class="text-muted small mb-3" style="line-height: 1.6;">
                            Submit requests to remove, delist, or correct company records, director information, and public directory entries.
                        </p>
                    </div>
                    <a href="#contact-form-section" class="btn btn-sm btn-light border font-weight-bold text-danger py-2">
                        <i class="fas fa-flag mr-1"></i> File Delisting Request &rarr;
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ================================================================= -->
<!-- MAIN INTERACTIVE CONTACT FORM & SIDEBAR SECTION -->
<!-- ================================================================= -->
<section class="py-5 bg-white" id="contact-form-section">
    <div class="container">
        
        <div class="row align-items-stretch">
            
            <!-- Left Column: The Form -->
            <div class="col-lg-7 mb-5 mb-lg-0">
                <div class="contact-form-container">
                    
                    <div class="mb-4">
                        <span class="badge badge-primary px-3 py-1 rounded-pill text-uppercase font-weight-bold mb-2" style="font-size: 11px;">
                            <?php echo $is_removal_request ? 'Data Delisting &amp; Complaints' : 'Send a Message'; ?>
                        </span>
                        <h2 class="font-weight-bold text-dark mb-2" style="font-family: var(--font-heading); font-size: 26px;">
                            <?php echo $is_removal_request ? 'File a Data Removal Complaint' : 'How Can We Assist You Today?'; ?>
                        </h2>
                        <p class="text-muted small mb-0">
                            <?php echo $is_removal_request ? 'Submit your official complaint/request to delete or delist company information from the public directory.' : 'Fill out the inquiry form below and our technical/support team will get back to you promptly.'; ?>
                        </p>
                    </div>

                    <?php if ($is_removal_request): ?>
                    <!-- Removal Request Notice Box -->
                    <div class="alert alert-warning p-3 mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid #fde68a; background: #fffbeb; color: #92400e;">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle fa-lg mr-3 mt-1 text-warning"></i>
                            <div>
                                <strong class="d-block" style="font-size: 14.5px;">Company Record Targeted for Removal / Deletion:</strong>
                                <div class="mt-1 small">
                                    <?php if (!empty($req_name)): ?>
                                    <div><strong>Company:</strong> <span class="text-dark font-weight-bold"><?php echo htmlspecialchars($req_name); ?></span></div>
                                    <?php endif; ?>
                                    <?php if (!empty($req_cin)): ?>
                                    <div><strong>CIN / LLPIN:</strong> <code class="text-primary font-weight-bold"><?php echo htmlspecialchars($req_cin); ?></code></div>
                                    <?php endif; ?>
                                    <div class="text-muted mt-1">Please provide your authorized contact credentials and grounds for removal (e.g. Struck Off, Closed, Inactive, Director Privacy, or Trademark issue) below.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Status Notification Alerts -->
                    <?php if ($msg_status == "success"): ?>
                        <div class="alert alert-success alert-dismissible fade show p-3 mb-4 shadow-sm" role="alert" style="border-radius: 12px; border: 1px solid #a7f3d0; background: #ecfdf5; color: #065f46;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x mr-3 text-success"></i>
                                <div>
                                    <strong class="d-block" style="font-size: 15px;">Inquiry Successfully Submitted!</strong>
                                    <span class="small"><?php echo htmlspecialchars($msg_text); ?></span>
                                </div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: #065f46;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php elseif ($msg_status == "error"): ?>
                        <div class="alert alert-danger alert-dismissible fade show p-3 mb-4 shadow-sm" role="alert" style="border-radius: 12px; border: 1px solid #fecaca; background: #fef2f2; color: #991b1b;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle fa-2x mr-3 text-danger"></i>
                                <div>
                                    <strong class="d-block" style="font-size: 15px;">Submission Error</strong>
                                    <span class="small"><?php echo htmlspecialchars($msg_text); ?></span>
                                </div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: #991b1b;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php
                        $selected_area = isset($_POST['area']) ? $_POST['area'] : ($is_removal_request ? 'Record Correction' : '');
                        
                        $default_msg_content = '';
                        if (isset($_POST['message'])) {
                            $default_msg_content = $_POST['message'];
                        } elseif ($is_removal_request) {
                            $default_msg_content = "REQUEST: RECORD CORRECTION / REMOVAL\n"
                                . ($req_name ? "Company Name: " . $req_name . "\n" : "")
                                . ($req_cin ? "CIN / LLPIN: " . $req_cin . "\n" : "")
                                . "Details / Grounds: \n"
                                . "Requester's Designation (Director / Authorized Agent): \n";
                        }
                    ?>

                    <form id="contact-form" action="<?php echo $base_url; ?>contact<?php echo $is_removal_request ? '?type=removal&cin='.urlencode($req_cin).'&name='.urlencode($req_name) : ''; ?>#contact-form-section" method="post">
                        
                        <div class="row">
                            <!-- Full Name -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Full Name <span class="text-danger ml-1">*</span></label>
                                    <input type="text" class="form-control-modern" name="name" placeholder="e.g. Ramesh Kumar" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                                    <i class="fas fa-user form-icon-wrap"></i>
                                </div>
                            </div>

                            <!-- Email Address -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Email Address <span class="text-danger ml-1">*</span></label>
                                    <input type="email" class="form-control-modern" name="email" placeholder="e.g. name@example.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                    <i class="fas fa-envelope form-icon-wrap"></i>
                                </div>
                            </div>

                            <!-- Mobile Phone -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Mobile Number <span class="text-danger ml-1">*</span></label>
                                    <input type="tel" class="form-control-modern" name="mobile" placeholder="e.g. +91 98765 43210" required value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>">
                                    <i class="fas fa-phone form-icon-wrap"></i>
                                </div>
                            </div>

                            <!-- Area of Inquiry -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Area of Inquiry <span class="text-danger ml-1">*</span></label>
                                    <select class="form-control-modern" name="area" required style="cursor: pointer;">
                                        <option value="" disabled <?php echo empty($selected_area) ? 'selected' : ''; ?>>Select Category...</option>
                                        <option value="API & Data Partnership" <?php echo ($selected_area === 'API & Data Partnership') ? 'selected' : ''; ?>>API &amp; Data Integration</option>
                                        <option value="Bank IFSC & Branches" <?php echo ($selected_area === 'Bank IFSC & Branches') ? 'selected' : ''; ?>>Bank IFSC &amp; Branch Verification</option>
                                        <option value="Legal Acts & Law" <?php echo ($selected_area === 'Legal Acts & Law') ? 'selected' : ''; ?>>Bare Acts &amp; Legal Tools</option>
                                        <option value="Company Incorporation" <?php echo ($selected_area === 'Company Incorporation') ? 'selected' : ''; ?>>Company Incorporation &amp; CIN</option>
                                        <option value="General Inquiry" <?php echo ($selected_area === 'General Inquiry') ? 'selected' : ''; ?>>General Legal Inquiry</option>
                                        <option value="NIC 2025 Codes" <?php echo ($selected_area === 'NIC 2025 Codes') ? 'selected' : ''; ?>>NIC 2025 Activity Classification</option>
                                        <option value="PIN Code Directory" <?php echo ($selected_area === 'PIN Code Directory') ? 'selected' : ''; ?>>PIN Code &amp; Postal Directory</option>
                                        <option value="Record Correction" <?php echo ($selected_area === 'Record Correction') ? 'selected' : ''; ?>>Record Correction / Grievance</option>
                                        <option value="Suggestion" <?php echo ($selected_area === 'Suggestion') ? 'selected' : ''; ?>>Suggestions &amp; Portal Feedback</option>
                                        <option value="Other" <?php echo ($selected_area === 'Other') ? 'selected' : ''; ?>>Other Inquiries</option>
                                    </select>
                                    <i class="fas fa-tags form-icon-wrap"></i>
                                </div>
                            </div>

                            <!-- Message Body -->
                            <div class="col-12">
                                <div class="form-group-modern form-group-textarea">
                                    <label class="form-label-modern">Your Message <span class="text-danger ml-1">*</span></label>
                                    <textarea class="form-control-modern" name="message" rows="5" placeholder="Please describe your inquiry, complaint, or grounds for deletion in detail..." required><?php echo htmlspecialchars($default_msg_content); ?></textarea>
                                    <i class="fas fa-comment-alt form-icon-wrap"></i>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 mt-2">
                                <button type="submit" name="submit_contact" class="btn btn-primary btn-block py-3 font-weight-bold shadow-sm" style="border-radius: 12px; font-size: 16px; letter-spacing: 0.5px; background: var(--primary-gradient); border: none;">
                                    <i class="fas fa-paper-plane mr-2"></i> Submit Inquiry
                                </button>
                                <div class="d-flex align-items-center justify-content-center text-muted small mt-3">
                                    <i class="fas fa-lock text-success mr-2"></i> Your information is kept strictly confidential and secure.
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

            <!-- Right Column: Organization Profile & Channels -->
            <div class="col-lg-5">
                <div class="sidebar-context-card h-100 d-flex flex-column justify-content-between">
                    
                    <div>
                        <!-- Header -->
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                            <div class="brand-icon-badge mr-3" style="width: 44px; height: 44px; font-size: 20px;">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                            <div>
                                <h4 class="font-weight-bold text-white mb-0" style="font-family: var(--font-heading);">
                                    Online<span class="text-primary">Law</span>
                                </h4>
                                <span class="small text-white-50">Public Legal &amp; Directory Portal</span>
                            </div>
                        </div>

                        <!-- Enterprise Parent Info -->
                        <div class="mb-4">
                            <span class="small text-white-50 text-uppercase font-weight-bold" style="letter-spacing: 1px;">Governing Entity</span>
                            <h5 class="text-white font-weight-bold mt-1 mb-2" style="font-size: 18px;">
                                OfferPlant Technologies Private Limited
                            </h5>
                            <p class="text-white-50 small mb-0" style="line-height: 1.6;">
                                India's dedicated legal technology initiative making statutory records, corporate registrations, IFSC directories, and bare acts accessible to every citizen.
                            </p>
                        </div>

                        <!-- Highlights / Quick Topics -->
                        <div class="mb-4">
                            <span class="small text-white-50 text-uppercase font-weight-bold d-block mb-2" style="letter-spacing: 1px;">Supported Areas</span>
                            <div>
                                <span class="context-pill"><i class="fas fa-check text-primary mr-1"></i> MCA Incorporations</span>
                                <span class="context-pill"><i class="fas fa-check text-warning mr-1"></i> NIC 2025 Activity</span>
                                <span class="context-pill"><i class="fas fa-check text-info mr-1"></i> Bank IFSC/MICR</span>
                                <span class="context-pill"><i class="fas fa-check text-success mr-1"></i> PIN Code Search</span>
                                <span class="context-pill"><i class="fas fa-check text-danger mr-1"></i> Indian Bare Acts</span>
                                <span class="context-pill"><i class="fas fa-check text-cyan mr-1"></i> API Integration</span>
                            </div>
                        </div>

                        <!-- Direct Help Notice -->
                        <div class="p-3 rounded-lg mb-4" style="background: rgba(37, 99, 235, 0.15); border: 1px solid rgba(96, 165, 250, 0.25);">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle text-primary mt-1 mr-2.5" style="font-size: 16px;"></i>
                                <div class="small text-white-50" style="line-height: 1.5;">
                                    <strong class="text-white">Commercial API / Bulk Inquiries:</strong> Looking to license Bank IFSC, PIN Code, or Corporate Master Data? Mention "API Licensing" in your inquiry for direct developer support.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Connectivity -->
                    <div class="pt-4 border-top" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                        <span class="small text-white-50 text-uppercase font-weight-bold d-block mb-3" style="letter-spacing: 1px;">Connect on Social Media</span>
                        <div class="d-flex align-items-center flex-wrap gap-2" style="gap: 10px;">
                            <?php if (!empty($facebook)): ?>
                            <a href="<?php echo $facebook; ?>" target="_blank" class="social-icon-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($twitter)): ?>
                            <a href="<?php echo $twitter; ?>" target="_blank" class="social-icon-btn" title="Twitter / X"><i class="fab fa-twitter"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($linkedin)): ?>
                            <a href="<?php echo $linkedin; ?>" target="_blank" class="social-icon-btn" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($instagram)): ?>
                            <a href="<?php echo $instagram; ?>" target="_blank" class="social-icon-btn" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($youtube)): ?>
                            <a href="<?php echo $youtube; ?>" target="_blank" class="social-icon-btn" title="YouTube"><i class="fab fa-youtube"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($pinterest)): ?>
                            <a href="<?php echo $pinterest; ?>" target="_blank" class="social-icon-btn" title="Pinterest"><i class="fab fa-pinterest-p"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</section>

<!-- ================================================================= -->
<!-- FREQUENTLY ASKED CONTACT QUESTIONS -->
<!-- ================================================================= -->
<section class="py-5" style="background: #f8fafc; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        
        <div class="text-center mb-5">
            <span class="badge badge-light border text-dark px-3 py-1 rounded-pill font-weight-bold text-uppercase mb-2" style="font-size: 11px;">
                Instant Answers
            </span>
            <h2 class="font-weight-bold text-dark mb-2" style="font-family: var(--font-heading); font-size: 28px;">
                Frequently Asked Questions
            </h2>
            <p class="text-muted small mb-0" style="max-width: 580px; margin: 0 auto;">
                Find quick answers regarding our data sources, response times, and API integration.
            </p>
        </div>

        <div class="row">
            
            <div class="col-md-6 mb-3">
                <div class="faq-mini-card h-100">
                    <h6 class="font-weight-bold text-dark mb-2">
                        <i class="fas fa-question-circle text-primary mr-2"></i> How fast will I receive a reply?
                    </h6>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Our dedicated helpdesk reviews submissions throughout the business week. You will generally receive an email or telephone response within 3 to 7 business days.
                    </p>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="faq-mini-card h-100">
                    <h6 class="font-weight-bold text-dark mb-2">
                        <i class="fas fa-database text-warning mr-2"></i> How do I request a record update or correction?
                    </h6>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Select "Record Correction / Grievance" in the contact form, specify the exact CIN, IFSC, or PIN Code, along with any official supporting links, and our data team will review and refresh the directory.
                    </p>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="faq-mini-card h-100">
                    <h6 class="font-weight-bold text-dark mb-2">
                        <i class="fas fa-code text-success mr-2"></i> Do you provide API endpoints for developers?
                    </h6>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Yes! We provide robust REST APIs for Bank IFSC lookup, PIN Code validation, and MCA corporate records. Please select "API &amp; Data Integration" above with your use case.
                    </p>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="faq-mini-card h-100">
                    <h6 class="font-weight-bold text-dark mb-2">
                        <i class="fas fa-shield-alt text-info mr-2"></i> Is Online Law a government portal?
                    </h6>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        No. Online Law is an independent legal and public information platform operated by OfferPlant Technologies Pvt Ltd. We compile open data published by official sources (MCA, RBI, India Post) for public convenience.
                    </p>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- ================================================================= -->
<!-- SUPPORT HELPLINE & CALLING DESK (DOWN THE PAGE) -->
<!-- ================================================================= -->
<section class="py-5" style="background: #ffffff; border-bottom: 1px solid #e2e8f0;" id="support-helpline">
    <div class="container">
        
        <div class="p-4 p-md-5 rounded-xl border shadow-sm" style="border-radius: var(--radius-xl); background: #fdfefe; border-color: #e2e8f0 !important;">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge badge-warning text-dark font-weight-bold px-3 py-1 rounded-pill" style="font-size: 11px;">
                            <i class="fas fa-phone-volume mr-1"></i> Telephonic &amp; WhatsApp Support Desk
                        </span>
                    </div>

                    <h3 class="font-weight-bold text-dark mb-2" style="font-family: var(--font-heading); font-size: clamp(20px, 3vw, 26px);">
                        Support Helpline
                    </h3>
                    
                    <p class="text-muted small mb-3" style="line-height: 1.7;">
                        Speak to our support team for telephone inquiries and assistance.
                    </p>

                    <!-- Important Notice & Comparative Turnaround -->
                    <div class="p-3 rounded-lg border mb-3" style="background: #fffbeb; border-color: #fde68a !important; color: #92400e;">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle mt-1 mr-2.5 text-warning" style="font-size: 16px;"></i>
                            <div class="small" style="line-height: 1.6;">
                                <strong class="d-block mb-1">Turnaround Time &amp; Resolution SLA:</strong>
                                <div>&bull; <strong>Call / WhatsApp Complain:</strong> <span class="text-danger font-weight-bold">10 working days</span> (Subject to manual phone queue) +91 9431 42 6600, +91 9430 55 2117.</div>
                                <div>&bull; <strong>Email Support:</strong> <span class="text-warning font-weight-bold">7 working days</span> (Standard mailbox queue).</div>
                                <div>&bull; <strong>Online Contact Form:</strong> <span class="text-success font-weight-bold">3 days only</span> (Fast automated ticket creation &amp; priority legal desk).</div>
                                <div class="mt-2 text-dark font-weight-semibold">
                                    <i class="fas fa-arrow-circle-up text-primary mr-1"></i> To save time and get your request processed faster, please fill the online form above.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-3 mt-3" style="gap: 12px;">
                        <a href="#contact-form-section" class="btn btn-primary font-weight-bold px-4 py-2" style="border-radius: 20px;">
                            <i class="fas fa-bolt mr-1"></i> Fill Quick Online Form (3 Days) &uarr;
                        </a>
                        <a href="mailto:<?php echo $inst_email; ?>" class="btn btn-outline-secondary font-weight-bold px-3 py-2" style="border-radius: 20px;">
                            <i class="fas fa-envelope mr-1"></i> Email Support (7 Days)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================= -->
<!-- IMMERSIVE GOOGLE MAP SECTION WITH OVERLAY BADGE -->
<!-- ================================================================= -->
<section class="py-5 bg-white" id="location-map">
    <div class="container">
        
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <span class="badge badge-light border text-primary px-3 py-1 rounded-pill font-weight-bold text-uppercase mb-1" style="font-size: 11px;">
                    Navigation
                </span>
                <h3 class="font-weight-bold text-dark mb-0" style="font-family: var(--font-heading); font-size: 24px;">
                    Our Location &amp; Directions
                </h3>
            </div>
            <a href="https://maps.google.com" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary font-weight-bold px-3">
                <i class="fas fa-external-link-alt mr-1"></i> Open in Google Maps
            </a>
        </div>

        <div class="map-container-rel">
            <div class="map-overlay-badge d-none d-md-block">
                <div class="d-flex align-items-center mb-2">
                    <div class="brand-icon-badge mr-2" style="width: 28px; height: 28px; font-size: 13px;">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <strong class="text-dark" style="font-size: 14px;">Online Law HQ</strong>
                </div>
                <p class="text-muted small mb-2" style="line-height: 1.4;">
                    <?php echo !empty($inst_address1) ? htmlspecialchars($inst_address1) : 'Headquarters'; ?>, <?php echo !empty($inst_address2) ? htmlspecialchars($inst_address2) : ''; ?>
                </p>
                <div class="small text-primary font-weight-bold">
                    <i class="fas fa-directions mr-1"></i> Verified Business Address
                </div>
            </div>

            <div style="width: 100%; height: 100%; border: 0;">
                <?php echo $google_map; ?>
            </div>
        </div>

    </div>
</section>

<?php require_once ('includes/footer.php'); ?> 
