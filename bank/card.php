<?php
    // Set include path to root directory so that relative requires work correctly
    set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__DIR__));
    require_once('includes/config.php');

    // Strip subdirectory from base_url if needed
    if (isset($base_url)) {
        $base_url = preg_replace('/bank\/$/', '', $base_url);
    }

    require_once('includes/function.php'); 
    
    $page_title = "Debit & Credit Card Helplines (Emergency 24x7 Card Blocking Numbers 2026) | Online Law";
    $meta_desc = "Emergency 24x7 card blocking toll-free numbers for SBI, HDFC, ICICI, PNB, Axis, and all major Indian banks. Complete guide to card security, CVV protection, and RuPay vs Visa vs Mastercard.";
    $meta_keywords = "block credit card, debit card safety, lost card toll free, sbi block card number, hdfc card helpline, rupay visa mastercard, emergency bank hotline";
    $canonical_url = $base_url . "bank/card";

    // Schema.org Structured Data
    $schema_graphs = [];
    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "Bank Directory", "item" => $base_url . "bank"],
            ["@type" => "ListItem", "position" => 3, "name" => "Card Helplines & Safety", "item" => $canonical_url]
        ]
    ];

    $faq_items = [
        [
            "q" => "What is the fastest way to block a lost or stolen bank card?",
            "a" => "Call your bank's dedicated 24x7 emergency toll-free hotline immediately from the table below, or use your Mobile Banking app to lock or hotlist the card with a single tap under the 'Card Controls' section."
        ],
        [
            "q" => "What information is needed to block a card over the phone?",
            "a" => "You will typically need your registered mobile number, account number, or customer ID. Never disclose your secret 4-digit ATM PIN, CVV, or one-time password (OTP) even to customer support agents."
        ],
        [
            "q" => "What is the difference between RuPay, Visa, and Mastercard?",
            "a" => "RuPay is India's domestic payment network developed by NPCI with lower merchant processing charges and linked UPI credit card capabilities. Visa and Mastercard are global networks with extensive international acceptance and global travel concierge services."
        ],
        [
            "q" => "How do contactless (NCMC / RFID) tap-and-pay limits work in India?",
            "a" => "Under RBI rules, contactless tap-and-pay transactions up to ₹5,000 can be processed at POS terminals without entering an ATM PIN. Transactions above ₹5,000 always require PIN verification."
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

    $card_helplines = [
        [
            'bank' => 'State Bank of India (SBI)',
            'toll_free' => '1800 1234, 1800 2100, 1800 11 2211',
            'sms_block' => 'SMS BLOCK <last 4 digits> to 567676',
            'intl' => '+91 80 2659 9990'
        ],
        [
            'bank' => 'HDFC Bank',
            'toll_free' => '1800 202 6161, 1800 1600',
            'sms_block' => 'Via Mobile Banking or NetBanking App',
            'intl' => '+91 22 6160 6161'
        ],
        [
            'bank' => 'ICICI Bank',
            'toll_free' => '1800 1080, 1860 120 7777',
            'sms_block' => 'SMS BLOCK <last 4 digits> to 5676766',
            'intl' => '+91 22 3366 7777'
        ],
        [
            'bank' => 'Punjab National Bank (PNB)',
            'toll_free' => '1800 1800, 1800 2021',
            'sms_block' => 'SMS HOTLIST <Card No> to 5607040',
            'intl' => '+91 120 249 0000'
        ],
        [
            'bank' => 'Bank of Baroda (BOB)',
            'toll_free' => '1800 258 4455, 1800 102 4455',
            'sms_block' => 'SMS BLOCK <last 4 digits> to 8422009988',
            'intl' => '+91 22 6751 1111'
        ],
        [
            'bank' => 'Axis Bank',
            'toll_free' => '1860 419 5555, 1860 500 5555',
            'sms_block' => 'SMS BLOCK <last 4 digits> to 5676782',
            'intl' => '+91 22 6798 7700'
        ],
        [
            'bank' => 'Canara Bank',
            'toll_free' => '1800 425 0018, 1800 1030',
            'sms_block' => 'SMS CAN <SPACE> HOTLIST <Card No> to 5607060',
            'intl' => '+91 80 2544 0629'
        ],
        [
            'bank' => 'Union Bank of India',
            'toll_free' => '1800 22 2244, 1800 208 2244',
            'sms_block' => 'SMS UBLOCK <Card No> to 9223008486',
            'intl' => '+91 22 2523 1022'
        ],
        [
            'bank' => 'Kotak Mahindra Bank',
            'toll_free' => '1860 266 2666, 1800 209 0000',
            'sms_block' => 'SMS DCBLOCK <last 4 digits> to 9971056767',
            'intl' => '+91 22 6600 6022'
        ],
        [
            'bank' => 'IndusInd Bank',
            'toll_free' => '1860 267 7777, 1800 209 0061',
            'sms_block' => 'SMS BLOCK <last 4 digits> to 5676757',
            'intl' => '+91 22 4406 6666'
        ]
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
        --card-primary: #047857;
        --card-primary-dark: #064e3b;
        --card-danger: #dc2626;
    }

    /* Hero Banner */
    .hero-card-banner {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #0f172a 100%);
        padding: 56px 0 64px 0;
        position: relative;
        overflow: hidden;
    }
    .card-glow-orb-1 {
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
    .card-glow-orb-2 {
        position: absolute;
        bottom: -25%;
        right: -10%;
        width: 480px;
        height: 480px;
        background: radial-gradient(circle, rgba(239, 68, 68, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        filter: blur(60px);
        pointer-events: none;
    }

    /* Card Box */
    .card-guide-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px;
        height: 100%;
        box-shadow: 0 4px 15px -2px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
    }
    .card-guide-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 25px -5px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }
    .card-guide-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    /* Table hotline */
    .table-hotline thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        padding: 14px 18px;
    }
    .table-hotline tbody td {
        padding: 14px 18px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }
    .table-hotline tbody tr:hover {
        background-color: #fef2f2;
    }

    /* Toast */
    .card-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #064e3b;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        box-shadow: 0 14px 30px -5px rgba(0, 0, 0, 0.4);
        z-index: 999999;
        display: flex;
        align-items: center;
        gap: 10px;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .card-toast.show {
        opacity: 1;
        transform: translateY(0);
    }
</style>

<!-- ==========================================================================
     HERO MODERN BANNER SECTION
     ========================================================================== -->
<section class="hero-card-banner">
    <div class="card-glow-orb-1"></div>
    <div class="card-glow-orb-2"></div>

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
                        <span class="text-white font-weight-bold">Card Helplines &amp; Safety</span>
                    </nav>
                </div>

                <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill mb-3" style="background: rgba(220, 38, 38, 0.85); border: 1px solid rgba(255, 255, 255, 0.2); color: #ffffff; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">
                    <i class="fas fa-shield-alt mr-1.5"></i> EMERGENCY 24x7 CARD HOTLINES 2026
                </div>

                <h1 class="text-white font-weight-bold mb-2" style="font-size: clamp(24px, 3.8vw, 36px); letter-spacing: -0.5px; line-height: 1.25;">
                    Debit &amp; Credit Card <span style="background: linear-gradient(135deg, #f87171 0%, #38bdf8 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Blocking Helplines</span>
                </h1>
                <p class="text-light mb-0" style="color: #cbd5e1 !important; font-size: 15px; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Immediate 24x7 toll-free customer care numbers, emergency SMS hotlines, and security practices to protect payment cards from unauthorized transactions.
                </p>

            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     MAIN BODY & CARD HELPLINES
     ========================================================================== -->
<div class="full-row py-5" style="background-color: #f8fafc;">
    <div class="container">

        <!-- 3 Feature Columns Grid -->
        <div class="row mb-5">
            
            <div class="col-lg-4 mb-4">
                <div class="card-guide-box">
                    <div class="d-flex align-items-center mb-3">
                        <div class="card-guide-icon mr-3" style="background: #eff6ff; color: #2563eb;">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Payment Networks</h4>
                    </div>
                    <p class="text-muted small mb-2" style="line-height: 1.6;">
                        Comparing card infrastructure in India:
                    </p>
                    <ul class="pl-3 small text-dark mb-0" style="line-height: 1.7;">
                        <li><strong>RuPay:</strong> Domestic NPCI network with lowest merchant fees &amp; seamless UPI linkage.</li>
                        <li><strong>Visa / Mastercard:</strong> Global payment networks offering worldwide international lounge access &amp; rewards.</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card-guide-box">
                    <div class="d-flex align-items-center mb-3">
                        <div class="card-guide-icon mr-3" style="background: #fef2f2; color: #dc2626;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Card Security Rules</h4>
                    </div>
                    <p class="text-muted small mb-2" style="line-height: 1.6;">
                        Essential fraud prevention measures:
                    </p>
                    <ul class="pl-3 small text-dark mb-0" style="line-height: 1.7;">
                        <li>Never share your 3-digit CVV, ATM PIN, or OTP with anyone.</li>
                        <li>Keep international online transactions turned OFF when not in use.</li>
                        <li>Set customized daily limits for ATM and POS spending via Mobile App.</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card-guide-box">
                    <div class="d-flex align-items-center mb-3">
                        <div class="card-guide-icon mr-3" style="background: #ecfdf5; color: #047857;">
                            <i class="fas fa-wifi"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Contactless &amp; NCMC</h4>
                    </div>
                    <p class="text-muted small mb-2" style="line-height: 1.6;">
                        Modern tap-and-pay payment cards:
                    </p>
                    <ul class="pl-3 small text-dark mb-0" style="line-height: 1.7;">
                        <li>Tap-and-Pay allowed up to ₹5,000 without entering a PIN.</li>
                        <li><strong>NCMC:</strong> National Common Mobility Card functions as a single card for metro trains, buses, and toll booths.</li>
                    </ul>
                </div>
            </div>

        </div>

        <!-- ======================================================================
             EMERGENCY CARD BLOCKING TABLE
             ====================================================================== -->
        <div class="bg-white rounded-xl shadow-sm border mb-5 p-4 p-md-5" style="border-radius: 22px;">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="mr-3" style="width: 44px; height: 44px; background: #fee2e2; color: #dc2626; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fas fa-phone-slash"></i>
                    </div>
                    <div>
                        <h3 class="font-weight-bold text-dark mb-0" style="font-size: 20px;">
                            Emergency 24x7 Bank Card Blocking Numbers
                        </h3>
                        <p class="text-muted small mb-0">Call these verified numbers immediately if your card is lost, stolen, or compromised.</p>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hotline mb-0">
                    <thead>
                        <tr>
                            <th>Bank Name</th>
                            <th>24x7 Toll-Free Hotlines</th>
                            <th>SMS Hotlisting Format</th>
                            <th>International / Alternate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($card_helplines as $ch): ?>
                            <tr>
                                <td class="font-weight-bold text-dark"><?php echo htmlspecialchars($ch['bank']); ?></td>
                                <td>
                                    <?php 
                                    $tolls = explode(',', $ch['toll_free']);
                                    foreach ($tolls as $t): 
                                        $tClean = trim($t);
                                    ?>
                                        <div class="d-inline-flex align-items-center mr-2 mb-1">
                                            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $tClean); ?>" class="font-weight-bold text-danger text-decoration-none mr-1">
                                                <i class="fas fa-phone-alt mr-1 small"></i><?php echo htmlspecialchars($tClean); ?>
                                            </a>
                                            <button type="button" class="btn btn-xs btn-light border text-muted px-1.5 py-0.5" onclick="copyCardText('<?php echo htmlspecialchars($tClean); ?>');" title="Copy Number">
                                                <i class="far fa-copy" style="font-size: 10px;"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <span class="badge badge-light border text-dark font-weight-medium p-1.5" style="font-size: 12px; font-family: monospace;">
                                        <?php echo htmlspecialchars($ch['sms_block']); ?>
                                    </span>
                                </td>
                                <td class="small text-muted text-nowrap"><?php echo htmlspecialchars($ch['intl']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 p-3 rounded-xl border bg-light d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-radius: 14px;">
                <div class="small text-muted">
                    <i class="fas fa-info-circle text-primary mr-1"></i> Looking for branch addresses or verified IFSC codes?
                </div>
                <a href="<?php echo $base_url; ?>bank-finder" class="btn btn-sm btn-success font-weight-bold px-3 py-1.5" style="border-radius: 8px; background: #047857; border-color: #047857;">
                    Open Bank Finder &rarr;
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
            <div class="accordion" id="cardFaqAccordion">
                <?php foreach ($faq_items as $fIdx => $faq): ?>
                    <div class="card border rounded-lg mb-2 shadow-none" style="border-radius: 12px !important; overflow: hidden;">
                        <button class="btn btn-link text-left text-dark font-weight-bold p-3 w-100 d-flex justify-content-between align-items-center text-decoration-none" type="button" data-toggle="collapse" data-target="#cardCollapse<?php echo $fIdx; ?>">
                            <span><?php echo htmlspecialchars($faq['q']); ?></span>
                            <i class="fas fa-chevron-down small text-muted"></i>
                        </button>
                        <div id="cardCollapse<?php echo $fIdx; ?>" class="collapse" data-parent="#cardFaqAccordion">
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

<!-- Floating Toast -->
<div id="cardToast" class="card-toast">
    <i class="fas fa-check-circle text-success" style="font-size: 18px;"></i>
    <span id="cardToastMsg">Copied to clipboard!</span>
</div>

<?php require_once('includes/footer.php'); ?>

<script>
function showToast(msg) {
    const toast = document.getElementById('cardToast');
    const toastMsg = document.getElementById('cardToastMsg');
    if (!toast) return;
    toastMsg.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2800);
}

function copyCardText(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Copied: ' + text);
        }).catch(() => {
            fallbackCopy(text);
        });
    } else {
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    try {
        document.execCommand('copy');
        showToast('Copied: ' + text);
    } catch (e) {
        prompt('Copy number:', text);
    }
    document.body.removeChild(ta);
}
</script>

</body>
</html>
