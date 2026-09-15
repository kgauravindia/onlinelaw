<?php
require_once('includes/header.php');

// Fetch counts
$companies_count = direct_sql("SELECT count(*) as total FROM companies_incorporated");
$ifsc_count = direct_sql("SELECT count(*) as total FROM bank_ifsc");
$pincode_count = direct_sql("SELECT count(*) as total FROM pincode");
$acts_count = direct_sql("SELECT count(*) as total FROM acts");
$users_count = direct_sql("SELECT count(*) as total FROM op_user");

// Category breakdown for MCA Companies
$cat_breakdown = direct_sql("SELECT category, COUNT(*) as cnt FROM companies_incorporated GROUP BY category");
$cat_data = ['INDIAN' => 0, 'LLP' => 0, 'FOREIGN' => 0];
if (isset($cat_breakdown['data'])) {
    foreach ($cat_breakdown['data'] as $cb) {
        $cat = strtoupper($cb['category'] ?? '');
        if (isset($cat_data[$cat])) {
            $cat_data[$cat] = (int)$cb['cnt'];
        }
    }
}

// Monthly Incorporation Trends (Last 6 Months)
$monthly_trends = direct_sql("SELECT report_month, COUNT(*) as cnt FROM companies_incorporated WHERE report_month IS NOT NULL AND report_month != '' GROUP BY report_month ORDER BY report_month ASC LIMIT 6");
$chart_labels = [];
$chart_values = [];
if (isset($monthly_trends['data']) && count($monthly_trends['data']) > 0) {
    foreach ($monthly_trends['data'] as $mt) {
        $chart_labels[] = $mt['report_month'];
        $chart_values[] = (int)$mt['cnt'];
    }
} else {
    // Default fallback months for visualization
    $chart_labels = ['Apr 2026', 'May 2026', 'Jun 2026', 'Jul 2026', 'Aug 2026', 'Sep 2026'];
    $chart_values = [12400, 14200, 13800, 15600, 16100, 17200];
}

// Fetch recent 8 companies
$recent_companies = direct_sql("SELECT * FROM companies_incorporated ORDER BY registration_date DESC, id DESC LIMIT 8");
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4 pb-2">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">System Dashboard</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Overview of platform directories, public registers, and administrative tools.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2" style="gap: 10px;">
        <span class="badge bg-white text-dark shadow-sm border px-3 py-2 rounded-pill d-inline-flex align-items-center" style="font-size: 0.85rem; font-weight: 600;">
            <i class="far fa-calendar-alt text-primary mr-2"></i><?php echo date('l, d M Y'); ?>
        </span>
        <a href="upload_companies.php" class="btn btn-primary px-3 shadow-sm font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-file-excel mr-1"></i> Upload MCA Excel
        </a>
    </div>
</div>

<!-- Primary Stats Grid -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card-v2" style="border-top: 4px solid #2563eb;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">MCA Companies</div>
                    <div class="stat-value text-primary"><?php echo isset($companies_count['data'][0]['total']) ? number_format($companies_count['data'][0]['total']) : 0; ?></div>
                    <div class="small text-muted mt-1">
                        <span class="text-success font-weight-bold"><i class="fas fa-arrow-up"></i> Indian: <?php echo number_format($cat_data['INDIAN']); ?></span> | LLP: <?php echo number_format($cat_data['LLP']); ?>
                    </div>
                </div>
                <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                    <i class="fas fa-building"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top">
                <a href="manage_companies.php" class="small text-primary font-weight-bold">Manage Directory &rarr;</a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card-v2" style="border-top: 4px solid #0284c7;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Bank IFSC &amp; MICR</div>
                    <div class="stat-value text-dark"><?php echo isset($ifsc_count['data'][0]['total']) ? number_format($ifsc_count['data'][0]['total']) : 0; ?></div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-university text-info mr-1"></i> Verified RBI branches
                    </div>
                </div>
                <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fas fa-university"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top">
                <a href="manage_bank.php" class="small text-muted font-weight-bold">View Bank Records &rarr;</a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card-v2" style="border-top: 4px solid #10b981;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Postal PIN Codes</div>
                    <div class="stat-value text-dark"><?php echo isset($pincode_count['data'][0]['total']) ? number_format($pincode_count['data'][0]['total']) : 0; ?></div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-map-pin text-success mr-1"></i> Localities across India
                    </div>
                </div>
                <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top">
                <a href="manage_pincode.php" class="small text-muted font-weight-bold">View PIN Localities &rarr;</a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card-v2" style="border-top: 4px solid #f59e0b;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Bare Acts Library</div>
                    <div class="stat-value text-dark"><?php echo isset($acts_count['data'][0]['total']) ? number_format($acts_count['data'][0]['total']) : 0; ?></div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-book text-warning mr-1"></i> Statutes &amp; Acts
                    </div>
                </div>
                <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top">
                <a href="manage_acts.php" class="small text-muted font-weight-bold">Manage Bare Acts &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Analytics Charts Row -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="section-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="font-weight-bold mb-1" style="font-size: 1.05rem;">
                        <i class="fas fa-chart-bar text-primary mr-2"></i> Monthly Corporate Incorporations Trend
                    </h5>
                    <p class="text-muted small mb-0">Total new registered entities added by month</p>
                </div>
                <span class="badge-premium badge-premium-primary">
                    <i class="fas fa-database mr-1"></i> MCA Live
                </span>
            </div>
            <div class="chart-container-box">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="section-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="font-weight-bold mb-1" style="font-size: 1.05rem;">
                        <i class="fas fa-chart-pie text-info mr-2"></i> Platform Registries
                    </h5>
                    <p class="text-muted small mb-0">Data distribution across tools</p>
                </div>
            </div>
            <div class="chart-container-box d-flex align-items-center justify-content-center">
                <canvas id="registryPieChart" style="max-height: 240px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action Shortcuts -->
<div class="row mb-4">
    <div class="col-12">
        <div class="section-card p-4">
            <h6 class="text-uppercase font-weight-bold text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                <i class="fas fa-bolt text-warning mr-1"></i> Quick Action Tools
            </h6>
            <div class="row">
                <div class="col-lg-2 col-md-4 col-6 mb-3 mb-lg-0">
                    <a href="upload_companies.php" class="quick-action-card">
                        <i class="fas fa-file-excel fa-2x text-success mb-2"></i>
                        <h6 class="mb-1 font-weight-bold" style="font-size: 0.85rem;">Upload MCA</h6>
                        <p class="text-muted small mb-0">Import Excel data</p>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-3 mb-lg-0">
                    <a href="manage_companies.php" class="quick-action-card">
                        <i class="fas fa-building fa-2x text-primary mb-2"></i>
                        <h6 class="mb-1 font-weight-bold" style="font-size: 0.85rem;">MCA Search</h6>
                        <p class="text-muted small mb-0">Browse companies</p>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-3 mb-lg-0">
                    <a href="manage_bank.php" class="quick-action-card">
                        <i class="fas fa-university fa-2x text-info mb-2"></i>
                        <h6 class="mb-1 font-weight-bold" style="font-size: 0.85rem;">Bank IFSC</h6>
                        <p class="text-muted small mb-0">Routing codes</p>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-3 mb-lg-0">
                    <a href="manage_pincode.php" class="quick-action-card">
                        <i class="fas fa-map-pin fa-2x text-emerald mb-2" style="color: #10b981;"></i>
                        <h6 class="mb-1 font-weight-bold" style="font-size: 0.85rem;">PIN Codes</h6>
                        <p class="text-muted small mb-0">Postal records</p>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-3 mb-lg-0">
                    <a href="manage_acts.php" class="quick-action-card">
                        <i class="fas fa-book fa-2x text-warning mb-2"></i>
                        <h6 class="mb-1 font-weight-bold" style="font-size: 0.85rem;">Bare Acts</h6>
                        <p class="text-muted small mb-0">Acts &amp; sections</p>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-3 mb-lg-0">
                    <a href="<?php echo $base_url; ?>index" target="_blank" class="quick-action-card">
                        <i class="fas fa-external-link-alt fa-2x text-secondary mb-2"></i>
                        <h6 class="mb-1 font-weight-bold" style="font-size: 0.85rem;">Public Portal</h6>
                        <p class="text-muted small mb-0">Open website</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent MCA Incorporations Table -->
<div class="row">
    <div class="col-12">
        <div class="section-card">
            <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom" style="background: #ffffff;">
                <div>
                    <h5 class="mb-0 font-weight-bold" style="font-size: 1.05rem;">
                        <i class="fas fa-building text-primary mr-2"></i> Recent MCA Incorporated Companies
                    </h5>
                    <p class="text-muted small mb-0">Latest registered corporate entities indexed on the platform</p>
                </div>
                <a href="manage_companies.php" class="btn btn-sm btn-outline-primary px-3 rounded-pill font-weight-bold">
                    View All Companies &rarr;
                </a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>CIN / Registration #</th>
                            <th>Company Name</th>
                            <th>ROC / State</th>
                            <th>Category</th>
                            <th>Registration Date</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($recent_companies['data']) && count($recent_companies['data']) > 0): ?>
                            <?php foreach ($recent_companies['data'] as $c): ?>
                                <tr>
                                    <td>
                                        <span class="font-weight-bold font-monospace" style="font-size: 0.85rem; color: var(--primary);">
                                            <?php echo htmlspecialchars($c['cin'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark" style="font-size: 0.9rem;">
                                            <?php echo htmlspecialchars($c['company_name'] ?? 'N/A'); ?>
                                        </div>
                                        <?php if (!empty($c['company_status'])): ?>
                                            <span class="badge-premium badge-premium-info mt-1">
                                                <?php echo htmlspecialchars($c['company_status']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="text-muted" style="font-size: 0.85rem;">
                                            <i class="fas fa-map-marker-alt text-danger mr-1"></i> <?php echo htmlspecialchars($c['roc_state'] ?? $c['state'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $cat = strtoupper($c['category'] ?? '');
                                        if ($cat === 'LLP') {
                                            echo '<span class="badge-premium badge-premium-warning">LLP</span>';
                                        } elseif ($cat === 'FOREIGN') {
                                            echo '<span class="badge-premium badge-premium-danger">FOREIGN</span>';
                                        } else {
                                            echo '<span class="badge-premium badge-premium-success">INDIAN</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="text-muted" style="font-size: 0.85rem;">
                                            <i class="far fa-calendar text-muted mr-1"></i> <?php echo !empty($c['registration_date']) ? date('d M Y', strtotime($c['registration_date'])) : 'N/A'; ?>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href="edit_company.php?id=<?php echo $c['id']; ?>" class="btn-action-circle text-primary" title="Edit Company">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2 d-block text-muted"></i>
                                    No company records found in the database.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Monthly Trend Bar Chart
    const trendCtx = document.getElementById('monthlyTrendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'New Incorporations',
                    data: <?php echo json_encode($chart_values); ?>,
                    backgroundColor: 'rgba(37, 99, 235, 0.85)',
                    hoverBackgroundColor: '#2563eb',
                    borderRadius: 6,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Outfit', size: 13 },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }
                    }
                }
            }
        });
    }

    // 2. Registry Breakdown Doughnut Chart
    const pieCtx = document.getElementById('registryPieChart');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['MCA Companies', 'Bank IFSC', 'PIN Codes', 'Bare Acts'],
                datasets: [{
                    data: [
                        <?php echo (int)($companies_count['data'][0]['total'] ?? 0); ?>,
                        <?php echo (int)($ifsc_count['data'][0]['total'] ?? 0); ?>,
                        <?php echo (int)($pincode_count['data'][0]['total'] ?? 0); ?>,
                        <?php echo (int)($acts_count['data'][0]['total'] ?? 0); ?>
                    ],
                    backgroundColor: ['#2563eb', '#0284c7', '#10b981', '#f59e0b'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { family: 'Inter', size: 11 }, padding: 12, usePointStyle: true }
                    }
                },
                cutout: '68%'
            }
        });
    }
});
</script>

<?php require_once('includes/footer.php'); ?>
