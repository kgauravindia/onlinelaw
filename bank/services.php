<?php
    // Set include path to root directory so that relative requires work correctly
    set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__DIR__));
    require_once('includes/config.php');

    // Strip subdirectory from base_url if needed
    if (isset($base_url)) {
        $base_url = preg_replace('/bank\/$/', '', $base_url);
    }

    require_once('includes/function.php'); 
    
    $page_title = "Digital Banking Services Guide (NEFT vs RTGS vs IMPS vs UPI Limits & Timings 2026) | Online Law";
    $meta_desc = "Complete comparative guide to Indian digital payment systems: NEFT, RTGS, IMPS, and UPI. Compare transfer limits, processing timings, RBI settlement rules, and zero online charges.";
    $meta_keywords = "NEFT transfer, RTGS limits, IMPS timing, UPI payment, digital banking India, bank fund transfer methods, online payments RBI, NEFT vs RTGS vs IMPS";
    $canonical_url = $base_url . "bank/services";

    // Schema.org Structured Data
    $schema_graphs = [];
    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "Bank Directory", "item" => $base_url . "bank"],
            ["@type" => "ListItem", "position" => 3, "name" => "Digital Banking Services", "item" => $canonical_url]
        ]
    ];

    $faq_items = [
        [
            "q" => "What is the difference between NEFT and RTGS?",
            "a" => "NEFT settles transactions in half-hourly batches 24x7 with no minimum amount requirement. RTGS settles funds in real-time on a continuous basis for high-value transfers (minimum ₹2,00,000)."
        ],
        [
            "q" => "Are online NEFT and RTGS transfers chargeable in India?",
            "a" => "No, as per Reserve Bank of India (RBI) mandates, banks are prohibited from levying any charges on digital NEFT and RTGS transactions initiated through internet banking or mobile banking applications."
        ],
        [
            "q" => "What is the maximum limit for an IMPS transfer?",
            "a" => "The standard per-transaction limit for IMPS (Immediate Payment Service) managed by NPCI is ₹5,00,000. It is available 24x7x365 including bank holidays."
        ],
        [
            "q" => "Do I need an IFSC code for UPI payments?",
            "a" => "No. UPI transfers require only the recipient's Virtual Payment Address (UPI ID), mobile number, or QR Code. However, transfers via NEFT, RTGS, and standard IMPS require the exact 11-digit IFSC code and bank account number."
        ]
    ];

    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => array_map(function($faq) {
            return [
                "@type" => "Question",
                "name" => $faq['q'],
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => $faq['a']
                ]
            ];
        }, $faq_items)
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">

    <!-- Open Graph Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:site_name" content="Online Law">
    <meta property="og:image" content="<?php echo $base_url; ?>images/logo/logo.png">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta name="twitter:image" content="<?php echo $base_url; ?>images/logo/logo.png">

    <link rel="stylesheet" href="<?php echo $base_url; ?>css/bank.css">

    <!-- JSON-LD Structured Data Schema -->
    <?php foreach ($schema_graphs as $schema): ?>
        <script type="application/ld+json">
        <?php echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>
        </script>
    <?php endforeach; ?>
</head>
<body>

<?php require_once('includes/header.php'); ?>

<style>
    :root {
        --service-primary: #047857;
        --service-primary-dark: #064e3b;
        --service-accent: #10b981;
    }

    /* Hero Banner */
    .hero-service-banner {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #0f172a 100%);
        padding: 56px 0 64px 0;
        position: relative;
        overflow: hidden;
    }
    .service-glow-orb-1 {
        position: absolute;
        top: -20%;
        left: -10%;
        width: 450px;
        height: 450px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, transparent 70%);
        border-radius: 50%;
        filter: blur(50px);
        pointer-events: none;
    }
    .service-glow-orb-2 {
        position: absolute;
        bottom: -25%;
        right: -10%;
        width: 480px;
        height: 480px;
        background: radial-gradient(circle, rgba(52, 211, 153, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        filter: blur(60px);
        pointer-events: none;
    }

    /* Modern Card Layout */
    .service-guide-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px;
        height: 100%;
        box-shadow: 0 4px 15px -2px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
    }
    .service-guide-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 25px -5px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }
    .service-guide-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    /* Comparison Table */
    .table-matrix thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        padding: 14px 18px;
    }
    .table-matrix tbody td {
        padding: 14px 18px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }
    .table-matrix tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

<!-- ==========================================================================
     HERO MODERN BANNER SECTION
     ========================================================================== -->
<section class="hero-service-banner">
    <div class="service-glow-orb-1"></div>
    <div class="service-glow-orb-2"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-12 text-center">

                <!-- Breadcrumbs -->
                <div class="d-flex justify-content-center">
                    <nav class="d-flex align-items-center flex-wrap gap-2 mb-3" style="font-size: 13.5px; color: #94a3b8;" aria-label="breadcrumb">
                        <a href="<?php echo $base_url; ?>index" style="color: #cbd5e1; text-decoration: none;"><i class="fas fa-home mr-1"></i> Home</a>
                        <span class="mx-1">/</span>
                        <a href="<?php echo $base_url; ?>bank" style="color: #cbd5e1; text-decoration: none;">Bank Directory</a>
                        <span class="mx-1">/</span>
                        <span class="text-white font-weight-bold">Digital Banking Services</span>
                    </nav>
                </div>

                <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill mb-3" style="background: rgba(6, 78, 59, 0.85); border: 1px solid rgba(255, 255, 255, 0.2); color: #86efac; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">
                    <i class="fas fa-exchange-alt mr-1.5"></i> DIGITAL PAYMENT ARCHITECTURE 2026
                </div>

                <h1 class="text-white font-weight-bold mb-2" style="font-size: clamp(24px, 3.8vw, 36px); letter-spacing: -0.5px; line-height: 1.25;">
                    Digital Banking Services <span style="background: linear-gradient(135deg, #86efac 0%, #38bdf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">&amp; Transfer Methods</span>
                </h1>
                <p class="text-light mb-0" style="color: #cbd5e1 !important; font-size: 15px; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Compare processing speeds, RBI limits, operational timings, and settlement rules for NEFT, RTGS, IMPS, and UPI transfers in India.
                </p>

            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     MAIN BODY & SERVICE COMPARISONS
     ========================================================================== -->
<div class="full-row py-5" style="background-color: #f8fafc;">
    <div class="container">

        <!-- 4 Key Transfer Networks Grid -->
        <div class="row mb-5">
            
            <!-- NEFT -->
            <div class="col-lg-6 mb-4">
                <div class="service-guide-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="service-guide-icon mr-3" style="background: #fff7ed; color: #ff671f;">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">NEFT</h4>
                            <small class="text-muted font-weight-medium">National Electronic Funds Transfer</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3" style="line-height: 1.6;">
                        Nationwide payment system facilitating one-to-one money transfers between any participating bank branches in India.
                    </p>
                    <ul class="list-unstyled small text-dark mb-0" style="line-height: 1.8;">
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Settlement:</strong> Half-hourly batches round the clock</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Timings:</strong> 24x7x365 (including Sundays &amp; holidays)</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Limits:</strong> Min ₹1 &bull; No upper ceiling set by RBI</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Requirements:</strong> 11-digit IFSC + Beneficiary Account No.</li>
                    </ul>
                </div>
            </div>

            <!-- RTGS -->
            <div class="col-lg-6 mb-4">
                <div class="service-guide-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="service-guide-icon mr-3" style="background: #fef3c7; color: #d97706;">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">RTGS</h4>
                            <small class="text-muted font-weight-medium">Real-Time Gross Settlement</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3" style="line-height: 1.6;">
                        Continuous real-time gross settlement system designed for high-value, critical institutional and business remittances.
                    </p>
                    <ul class="list-unstyled small text-dark mb-0" style="line-height: 1.8;">
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Settlement:</strong> Instantaneous individual settlement in RBI books</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Timings:</strong> 24x7x365 operational availability</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Limits:</strong> Min ₹2,00,000 &bull; No maximum ceiling</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Requirements:</strong> 11-digit IFSC + Beneficiary Account No.</li>
                    </ul>
                </div>
            </div>

            <!-- IMPS -->
            <div class="col-lg-6 mb-4">
                <div class="service-guide-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="service-guide-icon mr-3" style="background: #ecfdf5; color: #047857;">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">IMPS</h4>
                            <small class="text-muted font-weight-medium">Immediate Payment Service</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3" style="line-height: 1.6;">
                        Real-time interbank electronic funds transfer service operated by NPCI via Mobile Banking, Net Banking, and ATMs.
                    </p>
                    <ul class="list-unstyled small text-dark mb-0" style="line-height: 1.8;">
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Settlement:</strong> Instant 24/7 credit to beneficiary account</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Timings:</strong> 24x7x365 real-time processing</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Limits:</strong> Min ₹1 &bull; Up to ₹5,00,000 per transaction</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Requirements:</strong> Account &amp; IFSC or Mobile &amp; MMID</li>
                    </ul>
                </div>
            </div>

            <!-- UPI -->
            <div class="col-lg-6 mb-4">
                <div class="service-guide-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="service-guide-icon mr-3" style="background: #faf5ff; color: #7c3aed;">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">UPI</h4>
                            <small class="text-muted font-weight-medium">Unified Payments Interface</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3" style="line-height: 1.6;">
                        Instant mobile payment framework powering seamless peer-to-peer (P2P) and person-to-merchant (P2M) transactions.
                    </p>
                    <ul class="list-unstyled small text-dark mb-0" style="line-height: 1.8;">
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Settlement:</strong> Instantaneous mobile-first routing</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Timings:</strong> 24x7x365 (zero downtime)</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Limits:</strong> Standard ₹1,00,000 / day (₹5L for IPO/tax)</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Requirements:</strong> Virtual Payment Address (VPA) / QR Code</li>
                    </ul>
                </div>
            </div>

        </div>

        <!-- ======================================================================
             COMPARISON MATRIX TABLE
             ====================================================================== -->
        <div class="bg-white rounded-xl shadow-sm border mb-5 p-4 p-md-5" style="border-radius: 22px;">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <div>
                    <h3 class="font-weight-bold text-dark mb-1" style="font-size: 20px;">
                        <i class="fas fa-table text-success mr-1.5"></i> Comprehensive Service Comparison Matrix
                    </h3>
                    <p class="text-muted small mb-0">Detailed feature breakdown across all 4 primary payment rails in India.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-matrix mb-0">
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>NEFT</th>
                            <th>RTGS</th>
                            <th>IMPS</th>
                            <th>UPI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold text-dark">Minimum Amount</td>
                            <td>₹1</td>
                            <td><span class="badge badge-warning text-dark font-weight-bold">₹2,00,000</span></td>
                            <td>₹1</td>
                            <td>₹1</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-dark">Maximum Amount</td>
                            <td>No Limit (Bank policy applies)</td>
                            <td>No Limit</td>
                            <td>₹5,00,000 / txn</td>
                            <td>₹1,00,000 / day</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-dark">Settlement Speed</td>
                            <td>Batched (every 30 mins)</td>
                            <td>Instant (Real-Time Gross)</td>
                            <td>Instant (Real-Time)</td>
                            <td>Instant (Real-Time)</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-dark">Operating Hours</td>
                            <td><span class="badge badge-success">24x7x365</span></td>
                            <td><span class="badge badge-success">24x7x365</span></td>
                            <td><span class="badge badge-success">24x7x365</span></td>
                            <td><span class="badge badge-success">24x7x365</span></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-dark">Online Transfer Charges</td>
                            <td><strong class="text-success">₹0 (Free via Net/App)</strong></td>
                            <td><strong class="text-success">₹0 (Free via Net/App)</strong></td>
                            <td>₹0 to ₹5 (Bank specific)</td>
                            <td><strong class="text-success">₹0 (Free)</strong></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-dark">Beneficiary Details Needed</td>
                            <td>IFSC + Account Number</td>
                            <td>IFSC + Account Number</td>
                            <td>IFSC + A/C or Mobile + MMID</td>
                            <td>UPI ID / Mobile / QR Code</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 p-3 rounded-xl border bg-light d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-radius: 14px;">
                <div class="small text-muted">
                    <i class="fas fa-info-circle text-primary mr-1"></i> Need to find the exact 11-digit IFSC code for your NEFT or RTGS transfer?
                </div>
                <a href="<?php echo $base_url; ?>bank-finder" class="btn btn-sm btn-success font-weight-bold px-3 py-1.5" style="border-radius: 8px; background: #047857; border-color: #047857;">
                    Find Bank IFSC &rarr;
                </a>
            </div>
        </div>

        <!-- ======================================================================
             FAQ SECTION
             ====================================================================== -->
        <div class="p-4 p-md-5 bg-white rounded-xl border shadow-sm mb-4" style="border-radius: 22px;">
            <h4 class="font-weight-bold text-dark mb-3" style="font-size: 20px;">
                <i class="fas fa-question-circle text-success mr-1.5"></i> Frequently Asked Questions
            </h4>
            <div class="accordion" id="servicesFaqAccordion">
                <?php foreach ($faq_items as $fIdx => $faq): ?>
                    <div class="card border rounded-lg mb-2 shadow-none" style="border-radius: 12px !important; overflow: hidden;">
                        <button class="btn btn-link text-left text-dark font-weight-bold p-3 w-100 d-flex justify-content-between align-items-center text-decoration-none" type="button" data-toggle="collapse" data-target="#servCollapse<?php echo $fIdx; ?>">
                            <span><?php echo htmlspecialchars($faq['q']); ?></span>
                            <i class="fas fa-chevron-down small text-muted"></i>
                        </button>
                        <div id="servCollapse<?php echo $fIdx; ?>" class="collapse" data-parent="#servicesFaqAccordion">
                            <div class="card-body text-muted small pt-0 pb-3" style="line-height: 1.6;">
                                <?php echo htmlspecialchars($faq['a']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<?php require_once('includes/footer.php'); ?>

</body>
</html>
