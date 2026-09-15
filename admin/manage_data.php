<?php
require_once('includes/header.php');

// Fetch counts for each dataset
$companies_count = direct_sql("SELECT count(*) as total FROM companies_incorporated");
$ifsc_count = direct_sql("SELECT count(*) as total FROM bank_ifsc");
$pincode_count = direct_sql("SELECT count(*) as total FROM pincode");
$acts_count = direct_sql("SELECT count(*) as total FROM acts");
$users_count = direct_sql("SELECT count(*) as total FROM users");
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Master Data Hub</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Select a core dataset registry below to inspect records, perform updates, or import batch updates.</p>
    </div>
</div>

<div class="row">
    <!-- MCA Companies -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="section-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fas fa-building"></i>
                    </div>
                    <span class="badge-premium badge-premium-primary">
                        <?php echo isset($companies_count['data'][0]['total']) ? number_format($companies_count['data'][0]['total']) : '0'; ?> Records
                    </span>
                </div>
                <h5 class="font-weight-bold mb-2">Companies Incorporated</h5>
                <p class="text-muted small mb-4">MCA registration database including Indian Companies, LLPs, and Foreign entities.</p>
            </div>
            <div class="d-flex gap-2" style="gap: 8px;">
                <a href="manage_companies.php" class="btn btn-sm btn-primary flex-grow-1 font-weight-bold py-2">
                    <i class="fas fa-search mr-1"></i> Browse Records
                </a>
                <a href="upload_companies.php" class="btn btn-sm btn-outline-success font-weight-bold py-2 px-3" title="Upload Excel">
                    <i class="fas fa-file-excel"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Bank IFSC & MICR -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="section-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fas fa-university"></i>
                    </div>
                    <span class="badge-premium badge-premium-info">
                        <?php echo isset($ifsc_count['data'][0]['total']) ? number_format($ifsc_count['data'][0]['total']) : '0'; ?> Records
                    </span>
                </div>
                <h5 class="font-weight-bold mb-2">Bank IFSC &amp; MICR Directory</h5>
                <p class="text-muted small mb-4">Official Reserve Bank routing codes, branch addresses, and MICR codes across India.</p>
            </div>
            <div>
                <a href="manage_bank.php" class="btn btn-sm btn-primary btn-block font-weight-bold py-2">
                    <i class="fas fa-search mr-1"></i> Manage Bank Records
                </a>
            </div>
        </div>
    </div>

    <!-- PIN Codes -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="section-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <span class="badge-premium badge-premium-success">
                        <?php echo isset($pincode_count['data'][0]['total']) ? number_format($pincode_count['data'][0]['total']) : '0'; ?> Records
                    </span>
                </div>
                <h5 class="font-weight-bold mb-2">Postal PIN Codes</h5>
                <p class="text-muted small mb-4">India Post postal index numbers, post offices, districts, talukas, and states.</p>
            </div>
            <div>
                <a href="manage_pincode.php" class="btn btn-sm btn-primary btn-block font-weight-bold py-2">
                    <i class="fas fa-search mr-1"></i> Manage PIN Codes
                </a>
            </div>
        </div>
    </div>

    <!-- Bare Acts -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="section-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fas fa-book"></i>
                    </div>
                    <span class="badge-premium badge-premium-warning">
                        <?php echo isset($acts_count['data'][0]['total']) ? number_format($acts_count['data'][0]['total']) : '0'; ?> Acts
                    </span>
                </div>
                <h5 class="font-weight-bold mb-2">Bare Acts Registry</h5>
                <p class="text-muted small mb-4">Indian constitutional, civil, criminal, and commercial statutes repository.</p>
            </div>
            <div>
                <a href="manage_acts.php" class="btn btn-sm btn-primary btn-block font-weight-bold py-2">
                    <i class="fas fa-search mr-1"></i> Manage Bare Acts
                </a>
            </div>
        </div>
    </div>

    <!-- Platform Users -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="section-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #f3e8ff; color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="badge-premium badge-premium-primary" style="background: #f3e8ff; color: #8b5cf6;">
                        <?php echo isset($users_count['data'][0]['total']) ? number_format($users_count['data'][0]['total']) : '0'; ?> Users
                    </span>
                </div>
                <h5 class="font-weight-bold mb-2">Platform Users</h5>
                <p class="text-muted small mb-4">Manage admin accounts, access permissions, and system administrators.</p>
            </div>
            <div>
                <a href="manage_users.php" class="btn btn-sm btn-primary btn-block font-weight-bold py-2">
                    <i class="fas fa-user-cog mr-1"></i> Manage Users
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
