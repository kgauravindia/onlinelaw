<?php
    // Set include path to root directory so that relative requires work correctly
    set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__DIR__));
    require_once('includes/config.php');

    // Strip subdirectory from base_url if needed
    if (isset($base_url)) {
        $base_url = preg_replace('/bank\/$/', '', $base_url);
    }

    require_once('includes/function.php'); 
    
    $page_title = "Bank Account Types & Missed Call Balance Check Numbers (2026) | Online Law";
    $meta_desc = "Official missed call bank balance inquiry numbers & mini statement codes for SBI, HDFC, ICICI, PNB, BOB, Axis, and all major Indian banks. Compare Savings vs Current vs FD vs RD accounts.";
    $meta_keywords = "bank balance check number, missed call balance inquiry, sbi balance check, hdfc mini statement number, savings vs current account, bank account types india";
    $canonical_url = $base_url . "bank/account";

    // Schema.org Structured Data
    $schema_graphs = [];
    $schema_graphs[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "Bank Directory", "item" => $base_url . "bank"],
            ["@type" => "ListItem", "position" => 3, "name" => "Bank Account Types & Balance Check", "item" => $canonical_url]
        ]
    ];

    $faq_items = [
        [
            "q" => "How does missed call balance checking work?",
            "a" => "Give a missed call from your bank-registered mobile number to your bank's official inquiry number. The call will automatically disconnect after 2-3 rings, and the bank will instantly send an SMS with your available balance."
        ],
        [
            "q" => "Is missed call banking free of cost?",
            "a" => "Yes. Since the call disconnects before being answered, you are not charged any telecom call fees, and banks provide this automated SMS balance alert service free of charge."
        ],
        [
            "q" => "What is the key difference between a Savings Account and a Current Account?",
            "a" => "A Savings Account is designed for individuals to earn interest (2.5%–5.5% p.a.) with monthly withdrawal limits. A Current Account is built for businesses with 0% interest, unlimited transactions, and overdraft (OD) facilities."
        ],
        [
            "q" => "How do I register for missed call banking?",
            "a" => "For most banks like SBI, send an SMS 'REG <Account Number>' from your registered mobile number to the bank's registration number (e.g. 09223488888 for SBI). Once registered, you will receive an instant confirmation SMS."
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

    $balance_numbers = [
        [
            'bank' => 'State Bank of India (SBI)',
            'balance' => '09223766666',
            'mini_statement' => '09223866666',
            'reg_sms' => 'REG <Account No> to 09223488888'
        ],
        [
            'bank' => 'HDFC Bank',
            'balance' => '1800 270 3333',
            'mini_statement' => '1800 270 3355',
            'reg_sms' => 'Auto-enabled on registered mobile'
        ],
        [
            'bank' => 'ICICI Bank',
            'balance' => '9555144444',
            'mini_statement' => '9555244444',
            'reg_sms' => 'SMS IBAL to 9215676766'
        ],
        [
            'bank' => 'Punjab National Bank (PNB)',
            'balance' => '1800 180 2223, 0120 2303090',
            'mini_statement' => '1800 180 2222',
            'reg_sms' => 'Auto-enabled on registered mobile'
        ],
        [
            'bank' => 'Bank of Baroda (BOB)',
            'balance' => '8468001111',
            'mini_statement' => '8468001122',
            'reg_sms' => 'SMS REG <last 4 digits> to 9176612303'
        ],
        [
            'bank' => 'Axis Bank',
            'balance' => '1800 419 5959',
            'mini_statement' => '1800 419 6969',
            'reg_sms' => 'Auto-enabled on registered mobile'
        ],
        [
            'bank' => 'Canara Bank',
            'balance' => '09015483483',
            'mini_statement' => '09015734734',
            'reg_sms' => 'Auto-enabled on registered mobile'
        ],
        [
            'bank' => 'Union Bank of India',
            'balance' => '09223008586',
            'mini_statement' => '09223008486',
            'reg_sms' => 'Auto-enabled on registered mobile'
        ],
        [
            'bank' => 'Kotak Mahindra Bank',
            'balance' => '1800 274 0110',
            'mini_statement' => '1800 274 0110',
            'reg_sms' => 'Auto-enabled on registered mobile'
        ],
        [
            'bank' => 'Indian Bank',
            'balance' => '09289592895',
            'mini_statement' => '09289592896',
            'reg_sms' => 'Auto-enabled on registered mobile'
        ],
        [
            'bank' => 'Central Bank of India',
            'balance' => '9555244442',
            'mini_statement' => '9555144441',
            'reg_sms' => 'Auto-enabled on registered mobile'
        ],
        [
            'bank' => 'Bank of India (BOI)',
            'balance' => '09015135135',
            'mini_statement' => '09266135135',
            'reg_sms' => 'Auto-enabled on registered mobile'
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
        --acc-primary: #047857;
        --acc-primary-dark: #064e3b;
        --acc-blue: #0284c7;
    }

    /* Hero Banner */
    .hero-account-banner {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #0f172a 100%);
        padding: 56px 0 64px 0;
        position: relative;
        overflow: hidden;
    }
    .account-glow-orb-1 {
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
    .account-glow-orb-2 {
        position: absolute;
        bottom: -25%;
        right: -10%;
        width: 480px;
        height: 480px;
        background: radial-gradient(circle, rgba(2, 132, 199, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        filter: blur(60px);
        pointer-events: none;
    }

    /* Account card */
    .acc-guide-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px;
        height: 100%;
        box-shadow: 0 4px 15px -2px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
    }
    .acc-guide-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 25px -5px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }
    .acc-guide-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    /* Comparison Table */
    .table-acc-matrix thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        padding: 14px 18px;
    }
    .table-acc-matrix tbody td {
        padding: 14px 18px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }

    /* Toast */
    .acc-toast {
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
    .acc-toast.show {
        opacity: 1;
        transform: translateY(0);
    }
</style>

<!-- ==========================================================================
     HERO MODERN BANNER SECTION
     ========================================================================== -->
<section class="hero-account-banner">
    <div class="account-glow-orb-1"></div>
    <div class="account-glow-orb-2"></div>

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
                        <span class="text-white font-weight-bold">Account Types &amp; Balance Inquiry</span>
                    </nav>
                </div>

                <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill mb-3" style="background: rgba(2, 132, 199, 0.85); border: 1px solid rgba(255, 255, 255, 0.2); color: #ffffff; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">
                    <i class="fas fa-phone-volume mr-1.5"></i> MISSED-CALL BANKING DIRECTORY 2026
                </div>

                <h1 class="text-white font-weight-bold mb-2" style="font-size: clamp(24px, 3.8vw, 36px); letter-spacing: -0.5px; line-height: 1.25;">
                    Bank Account Types <span style="background: linear-gradient(135deg, #38bdf8 0%, #86efac 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">&amp; Balance Check Numbers</span>
                </h1>
                <p class="text-light mb-0" style="color: #cbd5e1 !important; font-size: 15px; max-width: 680px; margin: 0 auto; line-height: 1.6;">
                    Instant missed-call numbers &amp; SMS codes for account balance checking, mini-statements, and a guide comparing Savings, Current, FD, and RD accounts in India.
                </p>

            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     MAIN BODY & MISSED CALL NUMBERS TABLE
     ========================================================================== -->
<div class="full-row py-5" style="background-color: #f8fafc;">
    <div class="container">

        <!-- ==================================================================
             MISSED-CALL BALANCE DIRECTORY TABLE
             ================================================================== -->
        <div class="bg-white rounded-xl shadow-sm border mb-5 p-4 p-md-5" style="border-radius: 22px;">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="mr-3" style="width: 44px; height: 44px; background: #e0f2fe; color: #0284c7; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div>
                        <h3 class="font-weight-bold text-dark mb-0" style="font-size: 20px;">
                            Official Missed-Call Balance Inquiry Directory
                        </h3>
                        <p class="text-muted small mb-0">Give a missed call from your registered mobile number for an instant SMS balance update.</p>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-acc-matrix mb-0">
                    <thead>
                        <tr>
                            <th>Bank Name</th>
                            <th>Balance Inquiry Number</th>
                            <th>Mini Statement Number</th>
                            <th>Registration SMS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($balance_numbers as $bn): ?>
                            <tr>
                                <td class="font-weight-bold text-dark"><?php echo htmlspecialchars($bn['bank']); ?></td>
                                <td>
                                    <?php 
                                    $bNums = explode(',', $bn['balance']);
                                    foreach ($bNums as $b): 
                                        $bClean = trim($b);
                                    ?>
                                        <div class="d-inline-flex align-items-center mr-2 mb-1">
                                            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $bClean); ?>" class="font-weight-bold text-primary text-decoration-none mr-1">
                                                <i class="fas fa-phone-alt mr-1 small"></i><?php echo htmlspecialchars($bClean); ?>
                                            </a>
                                            <button type="button" class="btn btn-xs btn-light border text-muted px-1.5 py-0.5" onclick="copyAccText('<?php echo htmlspecialchars($bClean); ?>');" title="Copy Number">
                                                <i class="far fa-copy" style="font-size: 10px;"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <?php if ($bn['mini_statement'] !== '—'): 
                                        $mNums = explode(',', $bn['mini_statement']);
                                        foreach ($mNums as $m): 
                                            $mClean = trim($m);
                                        ?>
                                            <div class="d-inline-flex align-items-center mr-2 mb-1">
                                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $mClean); ?>" class="font-weight-medium text-dark text-decoration-none mr-1">
                                                    <?php echo htmlspecialchars($mClean); ?>
                                                </a>
                                                <button type="button" class="btn btn-xs btn-light border text-muted px-1.5 py-0.5" onclick="copyAccText('<?php echo htmlspecialchars($mClean); ?>');" title="Copy Number">
                                                    <i class="far fa-copy" style="font-size: 10px;"></i>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-light border text-dark font-weight-medium p-1.5" style="font-size: 12px; font-family: monospace;">
                                        <?php echo htmlspecialchars($bn['reg_sms']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 p-3 rounded-xl border bg-light d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-radius: 14px;">
                <div class="small text-muted">
                    <i class="fas fa-info-circle text-primary mr-1"></i> Looking for bank branch locations, MICR codes, or IFSC details?
                </div>
                <a href="<?php echo $base_url; ?>bank-finder" class="btn btn-sm btn-success font-weight-bold px-3 py-1.5" style="border-radius: 8px; background: #047857; border-color: #047857;">
                    Find Bank IFSC &rarr;
                </a>
            </div>
        </div>

        <!-- ==================================================================
             ACCOUNT TYPES 4-CARD GRID
             ================================================================== -->
        <div class="row mb-5">
            
            <div class="col-lg-6 mb-4">
                <div class="acc-guide-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="acc-guide-icon mr-3" style="background: #eff6ff; color: #2563eb;">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Savings Account</h4>
                            <small class="text-muted font-weight-medium">Personal Liquidity &amp; Interest</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-2" style="line-height: 1.6;">
                        Ideal for salaried individuals, students, and families to safely store funds while earning periodic interest (2.5%–5.5% p.a.).
                    </p>
                    <ul class="pl-3 small text-dark mb-0" style="line-height: 1.7;">
                        <li><strong>Interest:</strong> Calculated on daily product balance, credited quarterly.</li>
                        <li><strong>Balance:</strong> Low to zero minimum Average Monthly Balance (AMB).</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="acc-guide-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="acc-guide-icon mr-3" style="background: #fef3c7; color: #d97706;">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Current Account</h4>
                            <small class="text-muted font-weight-medium">Business Operations &amp; Overdraft</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-2" style="line-height: 1.6;">
                        Tailored for businesses, traders, and companies requiring high-volume daily cash flow with zero deposit or withdrawal limits.
                    </p>
                    <ul class="pl-3 small text-dark mb-0" style="line-height: 1.7;">
                        <li><strong>Interest:</strong> 0% interest on credit balances.</li>
                        <li><strong>Overdraft (OD):</strong> Flexible credit facility against business collateral.</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="acc-guide-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="acc-guide-icon mr-3" style="background: #ecfdf5; color: #047857;">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Fixed Deposit (FD)</h4>
                            <small class="text-muted font-weight-medium">Lump-sum High Yield Investment</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-2" style="line-height: 1.6;">
                        Lock a one-time sum for fixed tenures (7 days to 10 years) at assured, non-volatile interest rates with senior citizen bonuses.
                    </p>
                    <ul class="pl-3 small text-dark mb-0" style="line-height: 1.7;">
                        <li><strong>Tax Saver:</strong> 5-Year FDs qualify for Section 80C tax deduction up to ₹1.5L.</li>
                        <li><strong>Loan against FD:</strong> Borrow up to 90%–95% of deposit value instantly.</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="acc-guide-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="acc-guide-icon mr-3" style="background: #faf5ff; color: #7c3aed;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">Recurring Deposit (RD)</h4>
                            <small class="text-muted font-weight-medium">Systematic Monthly Savings</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-2" style="line-height: 1.6;">
                        Invest a disciplined monthly installment (e.g. ₹1,000/mo) and earn compounded interest matching standard FD rates.
                    </p>
                    <ul class="pl-3 small text-dark mb-0" style="line-height: 1.7;">
                        <li><strong>Tenure:</strong> 6 months to 10 years with fixed maturity payouts.</li>
                        <li><strong>Accessibility:</strong> Easy auto-debit directly from savings account.</li>
                    </ul>
                </div>
            </div>

        </div>

        <!-- ======================================================================
             FAQ SECTION
             ====================================================================== -->
        <div class="p-4 p-md-5 bg-white rounded-xl border shadow-sm mb-4" style="border-radius: 22px;">
            <h4 class="font-weight-bold text-dark mb-3" style="font-size: 20px;">
                <i class="fas fa-question-circle text-success mr-1.5"></i> Frequently Asked Questions
            </h4>
            <div class="accordion" id="accFaqAccordion">
                <?php foreach ($faq_items as $fIdx => $faq): ?>
                    <div class="card border rounded-lg mb-2 shadow-none" style="border-radius: 12px !important; overflow: hidden;">
                        <button class="btn btn-link text-left text-dark font-weight-bold p-3 w-100 d-flex justify-content-between align-items-center text-decoration-none" type="button" data-toggle="collapse" data-target="#accCollapse<?php echo $fIdx; ?>">
                            <span><?php echo htmlspecialchars($faq['q']); ?></span>
                            <i class="fas fa-chevron-down small text-muted"></i>
                        </button>
                        <div id="accCollapse<?php echo $fIdx; ?>" class="collapse" data-parent="#accFaqAccordion">
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
<div id="accToast" class="acc-toast">
    <i class="fas fa-check-circle text-success" style="font-size: 18px;"></i>
    <span id="accToastMsg">Copied to clipboard!</span>
</div>

<?php require_once('includes/footer.php'); ?>

<script>
function showToast(msg) {
    const toast = document.getElementById('accToast');
    const toastMsg = document.getElementById('accToastMsg');
    if (!toast) return;
    toastMsg.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2800);
}

function copyAccText(text) {
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
