<?php
require_once(__DIR__ . '/includes/header.php');

$msg = '';
$msg_type = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '', 'admin_settings')) {
        $msg = "Security validation failed. Please refresh the page and try again.";
        $msg_type = "danger";
    } else {
        $settings_to_save = [
        // Analytics
        'google_analytics_id'        => trim($_POST['google_analytics_id'] ?? ''),
        'google_analytics_enabled'   => isset($_POST['google_analytics_enabled']) ? '1' : '0',
        
        // AdSense
        'google_adsense_id'          => trim($_POST['google_adsense_id'] ?? ''),
        'google_adsense_enabled'     => isset($_POST['google_adsense_enabled']) ? '1' : '0',
        
        // Webmaster Verification
        'google_site_verification'   => trim($_POST['google_site_verification'] ?? ''),
        'bing_site_verification'     => trim($_POST['bing_site_verification'] ?? ''),
        
        // Custom Code (Auto-sanitized to block hidden link farms & spam injections)
        'header_custom_code'         => function_exists('sanitize_custom_code') ? sanitize_custom_code(trim($_POST['header_custom_code'] ?? '')) : trim($_POST['header_custom_code'] ?? ''),
        'footer_custom_code'         => function_exists('sanitize_custom_code') ? sanitize_custom_code(trim($_POST['footer_custom_code'] ?? '')) : trim($_POST['footer_custom_code'] ?? ''),
        
        // General & Contact Info
        'site_name'                  => trim($_POST['site_name'] ?? ''),
        'site_tagline'               => trim($_POST['site_tagline'] ?? ''),
        'contact_email'              => trim($_POST['contact_email'] ?? ''),
        'contact_phone'              => trim($_POST['contact_phone'] ?? ''),
        'contact_support'            => trim($_POST['contact_support'] ?? ''),
        'contact_address'            => trim($_POST['contact_address'] ?? ''),
        
        // Social Links
        'social_facebook'            => trim($_POST['social_facebook'] ?? ''),
        'social_twitter'             => trim($_POST['social_twitter'] ?? ''),
        'social_linkedin'            => trim($_POST['social_linkedin'] ?? ''),
        'social_instagram'           => trim($_POST['social_instagram'] ?? ''),
        'social_youtube'             => trim($_POST['social_youtube'] ?? ''),
        'social_pinterest'           => trim($_POST['social_pinterest'] ?? '')
    ];

    $success_count = 0;
    foreach ($settings_to_save as $key => $val) {
        $group = 'general';
        if (strpos($key, 'google_analytics') !== false) $group = 'analytics';
        elseif (strpos($key, 'google_adsense') !== false) $group = 'ads';
        elseif (strpos($key, 'verification') !== false) $group = 'seo';
        elseif (strpos($key, 'custom_code') !== false) $group = 'scripts';
        elseif (strpos($key, 'social_') !== false) $group = 'social';

        if (set_setting($key, $val, $group)) {
            $success_count++;
        }
    }

        $msg = "Settings successfully updated!";
        $msg_type = "success";
    }
}

// Fetch all current settings
$all_settings = get_all_settings();

$ga_id = $all_settings['google_analytics_id'] ?? '';
$ga_enabled = ($all_settings['google_analytics_enabled'] ?? '1') === '1';

$adsense_id = $all_settings['google_adsense_id'] ?? (getenv('GOOGLE_ADSENSE_ID') ?: '');
$adsense_enabled = ($all_settings['google_adsense_enabled'] ?? '1') === '1';

$g_verification = $all_settings['google_site_verification'] ?? '';
$bing_verification = $all_settings['bing_site_verification'] ?? '';

$header_code = $all_settings['header_custom_code'] ?? '';
$footer_code = $all_settings['footer_custom_code'] ?? '';

$site_name = $all_settings['site_name'] ?? $inst_name;
$site_tagline = $all_settings['site_tagline'] ?? $inst_tag;
$contact_email = $all_settings['contact_email'] ?? $inst_email;
$contact_phone = $all_settings['contact_phone'] ?? $inst_contact;
$contact_support = $all_settings['contact_support'] ?? $inst_support;
$contact_address = $all_settings['contact_address'] ?? ($inst_address1 . ' ' . $inst_address2);

$social_facebook = $all_settings['social_facebook'] ?? $inst_facebook;
$social_twitter = $all_settings['social_twitter'] ?? $inst_twitter;
$social_linkedin = $all_settings['social_linkedin'] ?? $inst_linkedin;
$social_instagram = $all_settings['social_instagram'] ?? $inst_instagram;
$social_youtube = $all_settings['social_youtube'] ?? $inst_youtube;
$social_pinterest = $all_settings['social_pinterest'] ?? $inst_pinterest;
?>

<div class="container-fluid p-0">
    <!-- Page Title & Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 font-weight-bold mb-1" style="font-family: 'Outfit', sans-serif; color: var(--text-main);">
                <i class="fas fa-sliders-h text-primary mr-2"></i>Site Settings &amp; Tracking Configuration
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.92rem;">
                Configure Google Analytics (GA4), Google AdSense, Search Console verification, custom tracking tags, and site metadata.
            </p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 10px;">
            <a href="../index.php" target="_blank" class="btn btn-outline-secondary btn-sm px-3 py-2 font-weight-bold">
                <i class="fas fa-external-link-alt mr-1"></i> Preview Website
            </a>
            <a href="https://analytics.google.com/analytics/web/#/a409144439p555477805/realtime/overview?params=_u..nav%3Dmaui" target="_blank" class="btn btn-primary btn-sm px-3 py-2 font-weight-bold shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                <i class="fas fa-chart-line mr-1"></i> GA Realtime Dashboard
            </a>
        </div>
    </div>

    <?php if (!empty($msg)): ?>
    <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show shadow-sm border-0 d-flex align-items-center mb-4" role="alert" style="border-radius: 12px; background: <?php echo $msg_type === 'success' ? '#ecfdf5' : '#fef2f2'; ?>; color: <?php echo $msg_type === 'success' ? '#065f46' : '#991b1b'; ?>;">
        <i class="fas <?php echo $msg_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-2" style="font-size: 1.25rem;"></i>
        <div><strong><?php echo htmlspecialchars($msg); ?></strong></div>
        <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close" style="outline: none;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <!-- Status Overview Cards -->
    <div class="row mb-4">
        <!-- Google Analytics Status Card -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: #ffffff;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: <?php echo (!empty($ga_id) && $ga_enabled) ? '#ecfdf5' : '#fef2f2'; ?>; color: <?php echo (!empty($ga_id) && $ga_enabled) ? '#10b981' : '#ef4444'; ?>; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-right: 16px;">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-muted small text-uppercase font-weight-bold" style="letter-spacing: 0.5px;">Google Analytics (GA4)</div>
                        <div class="d-flex align-items-center mt-1">
                            <?php if (!empty($ga_id) && $ga_enabled): ?>
                                <span class="badge badge-success px-2 py-1 font-weight-bold mr-2" style="border-radius: 6px; font-size: 12px; background: #10b981;">ACTIVE</span>
                                <span class="text-dark font-weight-bold font-monospace" style="font-size: 13px;"><?php echo htmlspecialchars($ga_id); ?></span>
                            <?php elseif (!empty($ga_id)): ?>
                                <span class="badge badge-warning px-2 py-1 font-weight-bold" style="border-radius: 6px; font-size: 12px;">DISABLED</span>
                            <?php else: ?>
                                <span class="badge badge-danger px-2 py-1 font-weight-bold" style="border-radius: 6px; font-size: 12px; background: #ef4444;">NOT CONFIGURED</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Google AdSense Status Card -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: #ffffff;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: <?php echo (!empty($adsense_id) && $adsense_enabled) ? '#eff6ff' : '#f8fafc'; ?>; color: <?php echo (!empty($adsense_id) && $adsense_enabled) ? '#2563eb' : '#64748b'; ?>; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-right: 16px;">
                        <i class="fas fa-ad"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-muted small text-uppercase font-weight-bold" style="letter-spacing: 0.5px;">Google AdSense</div>
                        <div class="d-flex align-items-center mt-1">
                            <?php if (!empty($adsense_id) && $adsense_enabled): ?>
                                <span class="badge badge-primary px-2 py-1 font-weight-bold mr-2" style="border-radius: 6px; font-size: 12px; background: #2563eb;">ACTIVE</span>
                                <span class="text-muted small font-weight-bold"><?php echo htmlspecialchars(substr($adsense_id, 0, 18)) . '...'; ?></span>
                            <?php else: ?>
                                <span class="badge badge-secondary px-2 py-1 font-weight-bold" style="border-radius: 6px; font-size: 12px;">INACTIVE</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO & Custom Scripts Card -->
        <div class="col-lg-4 col-md-12 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: #ffffff;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: #fdf4ff; color: #a855f7; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-right: 16px;">
                        <i class="fas fa-code"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-muted small text-uppercase font-weight-bold" style="letter-spacing: 0.5px;">Custom Tags &amp; SEO</div>
                        <div class="d-flex align-items-center mt-1">
                            <span class="text-dark font-weight-bold" style="font-size: 13.5px;">
                                <?php 
                                $custom_items = 0;
                                if (!empty($g_verification)) $custom_items++;
                                if (!empty($bing_verification)) $custom_items++;
                                if (!empty($header_code)) $custom_items++;
                                if (!empty($footer_code)) $custom_items++;
                                echo $custom_items . ' Custom Tag' . ($custom_items !== 1 ? 's' : '') . ' Configured';
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form method="POST" action="manage_settings.php">
        <?php echo get_csrf_input('admin_settings'); ?>
        <div class="card border-0 shadow-sm mb-5" style="border-radius: 16px; background: #ffffff; overflow: hidden;">
            <!-- Nav Tabs -->
            <div class="card-header bg-white border-bottom p-0" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <ul class="nav nav-tabs border-0 px-4 pt-3" id="settingsTab" role="tablist" style="gap: 8px;">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active font-weight-bold py-3 px-3" id="analytics-tab" data-toggle="tab" href="#tab-analytics" role="tab" style="border-radius: 8px 8px 0 0; color: var(--text-main);">
                            <i class="fas fa-chart-line text-success mr-2"></i>Google Analytics
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link font-weight-bold py-3 px-3" id="adsense-tab" data-toggle="tab" href="#tab-adsense" role="tab" style="border-radius: 8px 8px 0 0; color: var(--text-main);">
                            <i class="fas fa-dollar-sign text-primary mr-2"></i>Google AdSense
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link font-weight-bold py-3 px-3" id="seo-tab" data-toggle="tab" href="#tab-seo" role="tab" style="border-radius: 8px 8px 0 0; color: var(--text-main);">
                            <i class="fas fa-search text-warning mr-2"></i>Search Console &amp; Verification
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link font-weight-bold py-3 px-3" id="scripts-tab" data-toggle="tab" href="#tab-scripts" role="tab" style="border-radius: 8px 8px 0 0; color: var(--text-main);">
                            <i class="fas fa-code text-purple mr-2" style="color: #9333ea;"></i>Custom Scripts &amp; Pixels
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link font-weight-bold py-3 px-3" id="general-tab" data-toggle="tab" href="#tab-general" role="tab" style="border-radius: 8px 8px 0 0; color: var(--text-main);">
                            <i class="fas fa-building text-info mr-2"></i>Platform &amp; Social Info
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Tab Content -->
            <div class="card-body p-4 p-md-5">
                <div class="tab-content" id="settingsTabContent">
                    
                    <!-- TAB 1: Google Analytics -->
                    <div class="tab-pane fade show active" id="tab-analytics" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-8">
                                <h5 class="font-weight-bold mb-3" style="color: var(--text-main);">Google Analytics 4 (GA4) Configuration</h5>
                                <p class="text-muted small mb-4">
                                    Integrate Google Analytics across all public pages of the platform by entering your GA4 Measurement ID (or Google Tag Manager ID).
                                </p>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold" for="google_analytics_id">
                                        Google Analytics Measurement ID <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light"><i class="fas fa-tag text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg font-monospace" id="google_analytics_id" name="google_analytics_id" placeholder="e.g. G-XXXXXXXXXX or GTM-XXXXXXX" value="<?php echo htmlspecialchars($ga_id); ?>" style="font-size: 15px; font-weight: 600; letter-spacing: 0.5px;">
                                    </div>
                                    <small class="form-text text-muted mt-2">
                                        Found in Google Analytics &rarr; <strong>Admin ⚙️</strong> &rarr; <strong>Data Streams</strong> &rarr; Click your <strong>Web</strong> stream &rarr; Copy <strong>Measurement ID</strong>.
                                    </small>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="custom-control custom-switch custom-switch-lg">
                                        <input type="checkbox" class="custom-control-input" id="google_analytics_enabled" name="google_analytics_enabled" value="1" <?php echo $ga_enabled ? 'checked' : ''; ?>>
                                        <label class="custom-control-label font-weight-bold" for="google_analytics_enabled">
                                            Enable Google Analytics Tracking
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">When enabled, the Google tag (gtag.js) script is injected automatically into the &lt;head&gt; of every public page.</small>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="p-4" style="background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
                                    <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-lightbulb text-warning mr-2"></i>Realtime Data Troubleshooting</h6>
                                    <ul class="pl-3 mb-3 text-muted small" style="line-height: 1.7;">
                                        <li><strong>Save Measurement ID:</strong> Paste your exact <code>G-XXXXXXXXXX</code> ID and click <em>Save All Settings</em> below.</li>
                                        <li><strong>Disable AdBlockers:</strong> AdBlock / uBlock Origin / Brave Shields block Google Analytics requests when you test locally.</li>
                                        <li><strong>Open Realtime Report:</strong> Visit <a href="https://analytics.google.com/analytics/web/#/a409144439p555477805/realtime/overview?params=_u..nav%3Dmaui" target="_blank" class="font-weight-bold text-primary">GA4 Realtime <i class="fas fa-external-link-alt" style="font-size: 10px;"></i></a> in one tab, and open any page on your website in another tab.</li>
                                        <li><strong>Check Network Tab:</strong> Open DevTools (F12) &rarr; Network &rarr; Filter by <code>collect?v=2</code> to confirm tracking hits are firing.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Google AdSense -->
                    <div class="tab-pane fade" id="tab-adsense" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-8">
                                <h5 class="font-weight-bold mb-3" style="color: var(--text-main);">Google AdSense Monetization</h5>
                                <p class="text-muted small mb-4">
                                    Manage your Google AdSense Publisher account integration across all platform directory pages.
                                </p>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold" for="google_adsense_id">
                                        AdSense Publisher Client ID
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light"><i class="fas fa-coins text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg font-monospace" id="google_adsense_id" name="google_adsense_id" placeholder="e.g. ca-pub-XXXXXXXXXXXXXXXX" value="<?php echo htmlspecialchars($adsense_id); ?>" style="font-size: 15px; font-weight: 600;">
                                    </div>
                                    <small class="form-text text-muted mt-2">
                                        Your official Google AdSense publisher ID (e.g., <code>ca-pub-XXXXXXXXXXXXXXXX</code>).
                                    </small>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="custom-control custom-switch custom-switch-lg">
                                        <input type="checkbox" class="custom-control-input" id="google_adsense_enabled" name="google_adsense_enabled" value="1" <?php echo $adsense_enabled ? 'checked' : ''; ?>>
                                        <label class="custom-control-label font-weight-bold" for="google_adsense_enabled">
                                            Enable Google AdSense Script Tag
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Automatically loads the Google AdSense asynchronous library.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Search Console & Verification -->
                    <div class="tab-pane fade" id="tab-seo" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-8">
                                <h5 class="font-weight-bold mb-3" style="color: var(--text-main);">Search Engine Verification Meta Tags</h5>
                                <p class="text-muted small mb-4">
                                    Verify site ownership for Google Search Console and Bing Webmaster Tools without editing theme files.
                                </p>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold" for="google_site_verification">
                                        Google Search Console Verification Token / Content
                                    </label>
                                    <input type="text" class="form-control form-control-lg font-monospace" id="google_site_verification" name="google_site_verification" placeholder="e.g. 8kL-example-verification-token-string" value="<?php echo htmlspecialchars($g_verification); ?>" style="font-size: 14px;">
                                    <small class="form-text text-muted mt-2">
                                        Enter the token string from <code>&lt;meta name="google-site-verification" content="HERE" /&gt;</code>.
                                    </small>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold" for="bing_site_verification">
                                        Bing Webmaster Verification Code
                                    </label>
                                    <input type="text" class="form-control form-control-lg font-monospace" id="bing_site_verification" name="bing_site_verification" placeholder="e.g. 5244... (MSValidate token)" value="<?php echo htmlspecialchars($bing_verification); ?>" style="font-size: 14px;">
                                    <small class="form-text text-muted mt-2">
                                        Enter the token from <code>&lt;meta name="msvalidate.01" content="HERE" /&gt;</code>.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: Custom Scripts & Pixels -->
                    <div class="tab-pane fade" id="tab-scripts" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="font-weight-bold mb-3" style="color: var(--text-main);">Custom Tracking Scripts &amp; Code Injection</h5>
                                <p class="text-muted small mb-4">
                                    Inject custom HTML, CSS, JavaScript, or tracking pixels (e.g. Microsoft Clarity, Meta/Facebook Pixel, Hotjar, Schema JSON-LD) directly into headers and footers.
                                </p>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold" for="header_custom_code">
                                        <i class="fas fa-heading text-primary mr-1"></i> Header Custom Code (Injected inside <code>&lt;head&gt;...&lt;/head&gt;</code>)
                                    </label>
                                    <textarea class="form-control font-monospace" id="header_custom_code" name="header_custom_code" rows="6" placeholder="<!-- Paste custom tracking scripts, meta tags, or pixels here -->" style="font-size: 13px; background: #0f172a; color: #38bdf8; border: 1px solid #334155;"><?php echo htmlspecialchars($header_code); ?></textarea>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold" for="footer_custom_code">
                                        <i class="fas fa-shoe-prints text-info mr-1"></i> Footer Custom Code (Injected right before <code>&lt;/body&gt;</code>)
                                    </label>
                                    <textarea class="form-control font-monospace" id="footer_custom_code" name="footer_custom_code" rows="6" placeholder="<!-- Paste chat widgets, footer analytics, or conversion scripts here -->" style="font-size: 13px; background: #0f172a; color: #38bdf8; border: 1px solid #334155;"><?php echo htmlspecialchars($footer_code); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 5: Platform & Social Info -->
                    <div class="tab-pane fade" id="tab-general" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5 class="font-weight-bold mb-3" style="color: var(--text-main);">General Platform Profile</h5>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold" for="site_name">Website Name</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo htmlspecialchars($site_name); ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold" for="site_tagline">Tagline</label>
                                    <input type="text" class="form-control" id="site_tagline" name="site_tagline" value="<?php echo htmlspecialchars($site_tagline); ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold" for="contact_email">Public Contact Email</label>
                                    <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($contact_email); ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold" for="contact_phone">Primary Phone</label>
                                    <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="<?php echo htmlspecialchars($contact_phone); ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold" for="contact_support">Support Hotline</label>
                                    <input type="text" class="form-control" id="contact_support" name="contact_support" value="<?php echo htmlspecialchars($contact_support); ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold" for="contact_address">Office Address</label>
                                    <textarea class="form-control" id="contact_address" name="contact_address" rows="2"><?php echo htmlspecialchars($contact_address); ?></textarea>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h5 class="font-weight-bold mb-3" style="color: var(--text-main);">Official Social Media Channels</h5>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold"><i class="fab fa-facebook text-primary mr-1"></i> Facebook URL</label>
                                    <input type="text" class="form-control" name="social_facebook" value="<?php echo htmlspecialchars($social_facebook); ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold"><i class="fab fa-twitter text-info mr-1"></i> Twitter / X URL</label>
                                    <input type="text" class="form-control" name="social_twitter" value="<?php echo htmlspecialchars($social_twitter); ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold"><i class="fab fa-linkedin text-primary mr-1"></i> LinkedIn URL</label>
                                    <input type="text" class="form-control" name="social_linkedin" value="<?php echo htmlspecialchars($social_linkedin); ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold"><i class="fab fa-instagram text-danger mr-1"></i> Instagram URL</label>
                                    <input type="text" class="form-control" name="social_instagram" value="<?php echo htmlspecialchars($social_instagram); ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold"><i class="fab fa-youtube text-danger mr-1"></i> YouTube URL</label>
                                    <input type="text" class="form-control" name="social_youtube" value="<?php echo htmlspecialchars($social_youtube); ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold"><i class="fab fa-pinterest text-danger mr-1"></i> Pinterest URL</label>
                                    <input type="text" class="form-control" name="social_pinterest" value="<?php echo htmlspecialchars($social_pinterest); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Sticky Save Bar Footer -->
            <div class="card-footer bg-light border-top p-4 d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    <i class="fas fa-shield-alt text-success mr-1"></i> All updates are applied immediately across all public web pages.
                </span>
                <button type="submit" name="save_settings" class="btn btn-primary px-4 py-2 font-weight-bold shadow-sm" style="border-radius: 8px;">
                    <i class="fas fa-save mr-2"></i> Save All Settings
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once('includes/footer.php'); ?>
