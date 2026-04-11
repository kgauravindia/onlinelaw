<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, follow">
<title>Page Not Found | Online Law</title>

<?php 
    require_once ('includes/function.php');
    require_once ('includes/header.php'); 
?>

<style>
    .error-container {
        padding: 100px 0;
        min-height: 80vh;
        display: flex;
        align-items: center;
        background: radial-gradient(circle at top right, rgba(var(--primary-rgb), 0.05), transparent),
                    radial-gradient(circle at bottom left, rgba(var(--primary-rgb), 0.05), transparent);
    }
    .error-code {
        font-size: 150px;
        font-weight: 900;
        line-height: 1;
        background: linear-gradient(135deg, var(--primary-color), #38bdf8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 20px;
        font-family: var(--font-heading);
        letter-spacing: -5px;
    }
    .error-title {
        font-family: var(--font-heading);
        font-size: 32px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 15px;
    }
    .error-desc {
        font-size: 18px;
        color: var(--text-muted);
        max-width: 500px;
        margin: 0 auto 40px;
        line-height: 1.6;
    }
    .tool-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none !important;
        height: 100%;
        display: block;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .tool-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        border-color: var(--primary-color);
    }
    .tool-card i {
        font-size: 32px;
        color: var(--primary-color);
        margin-bottom: 15px;
        display: block;
    }
    .tool-card h5 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 5px;
    }
    .tool-card span {
        font-size: 13px;
        color: var(--text-muted);
    }
</style>

<section class="error-container">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12 text-center">
                <div class="error-code">404</div>
                <h1 class="error-title">Objection! Page Not Found</h1>
                <p class="error-desc">It seems the link you followed has been dismissed or relocated. Don't worry, you can still find what you're looking for using our tools below.</p>
                
                <div class="row justify-content-center mt-5">
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <a href="<?php echo $base_url; ?>bank-ifsc" class="tool-card">
                                    <i class="fas fa-university"></i>
                                    <h5>Bank IFSC</h5>
                                    <span>Find any branch code</span>
                                </a>
                            </div>
                            <div class="col-md-4 mb-4">
                                <a href="<?php echo $base_url; ?>pincode" class="tool-card">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <h5>PIN Code</h5>
                                    <span>Search 1M+ locations</span>
                                </a>
                            </div>
                            <div class="col-md-4 mb-4">
                                <a href="<?php echo $base_url; ?>gst-hsn-sac" class="tool-card">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <h5>GST Finder</h5>
                                    <span>HSN & SAC codes</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <a href="<?php echo $base_url; ?>index" class="btn btn-primary px-4 py-3" style="border-radius: 12px; font-weight: 600;">
                        <i class="fas fa-home mr-2"></i> Return to Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once ('includes/footer.php'); ?> 
</body>
</html>
