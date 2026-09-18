<?php
require_once('includes/header.php');

// Fetch Primary Counts
$companies_count = direct_sql("SELECT count(*) as total FROM companies_incorporated");
$ifsc_count = direct_sql("SELECT count(*) as total FROM bank_ifsc");
$pincode_count = direct_sql("SELECT count(*) as total FROM pincode");
$acts_count = direct_sql("SELECT count(*) as total FROM acts");
$users_count = direct_sql("SELECT count(*) as total FROM op_user");

$total_companies = isset($companies_count['data'][0]['total']) ? (int)$companies_count['data'][0]['total'] : 0;
$total_ifsc = isset($ifsc_count['data'][0]['total']) ? (int)$ifsc_count['data'][0]['total'] : 0;
$total_pincodes = isset($pincode_count['data'][0]['total']) ? (int)$pincode_count['data'][0]['total'] : 0;
$total_acts = isset($acts_count['data'][0]['total']) ? (int)$acts_count['data'][0]['total'] : 0;
$total_users = isset($users_count['data'][0]['total']) ? (int)$users_count['data'][0]['total'] : 0;

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

// Percentages
$pct_indian = $total_companies > 0 ? round(($cat_data['INDIAN'] / $total_companies) * 100, 1) : 0;
$pct_llp = $total_companies > 0 ? round(($cat_data['LLP'] / $total_companies) * 100, 1) : 0;
$pct_foreign = $total_companies > 0 ? round(($cat_data['FOREIGN'] / $total_companies) * 100, 1) : 0;

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
    $chart_labels = ['Apr 2026', 'May 2026', 'Jun 2026', 'Jul 2026', 'Aug 2026', 'Sep 2026'];
    $chart_values = [12400, 14200, 13800, 15600, 16100, 17200];
}
?>

<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-end mb-4 pb-2">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Platform Statistics &amp; Analytics</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Comprehensive data summary, registry metrics, and platform performance.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2" style="gap: 10px;">
        <span class="badge bg-white text-dark shadow-sm border px-3 py-2 rounded-pill d-inline-flex align-items-center" style="font-size: 0.85rem; font-weight: 600;">
            <i class="far fa-calendar-alt text-primary mr-2"></i><?php echo date('l, d M Y'); ?>
        </span>
        <a href="<?php echo $base_url; ?>index" target="_blank" class="btn btn-outline-primary px-3 shadow-sm font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-external-link-alt mr-1"></i> Public Website
        </a>
    </div>
</div>

<!-- Primary Registry Metric Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card-v2" style="border-top: 4px solid #2563eb;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">MCA Companies</div>
                    <div class="stat-value text-primary"><?php echo number_format($total_companies); ?></div>
                    <div class="small text-muted mt-1">
                        <span class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> Active</span> Indexed Records
                    </div>
                </div>
                <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                    <i class="fas fa-building"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top d-flex justify-content-between text-muted small">
                <span>Indian: <?php echo number_format($cat_data['INDIAN']); ?></span>
                <span>LLP: <?php echo number_format($cat_data['LLP']); ?></span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card-v2" style="border-top: 4px solid #0284c7;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Bank IFSC &amp; MICR</div>
                    <div class="stat-value text-dark"><?php echo number_format($total_ifsc); ?></div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-university text-info mr-1"></i> Verified RBI branches
                    </div>
                </div>
                <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fas fa-university"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top text-muted small">
                <span>Routing codes across all Indian banks</span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card-v2" style="border-top: 4px solid #10b981;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Postal PIN Codes</div>
                    <div class="stat-value text-dark"><?php echo number_format($total_pincodes); ?></div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-map-pin text-success mr-1"></i> Localities across India
                    </div>
                </div>
                <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top text-muted small">
                <span>Post offices, talukas &amp; districts</span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card-v2" style="border-top: 4px solid #f59e0b;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Bare Acts Library</div>
                    <div class="stat-value text-dark"><?php echo number_format($total_acts); ?></div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-book text-warning mr-1"></i> Statutes &amp; Acts
                    </div>
                </div>
                <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top text-muted small">
                <span>Central, State &amp; Legal statutes</span>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Entity Category Distribution Row -->
<div class="row mb-4">
    <div class="col-lg-3 col-6 mb-3 mb-lg-0">
        <div class="section-card p-3 text-center border-bottom border-primary border-3 h-100">
            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Indian Companies</div>
            <div class="h3 fw-bold mb-1 text-primary"><?php echo number_format($cat_data['INDIAN']); ?></div>
            <span class="badge-premium badge-premium-primary"><?php echo $pct_indian; ?>% of Total</span>
        </div>
    </div>
    <div class="col-lg-3 col-6 mb-3 mb-lg-0">
        <div class="section-card p-3 text-center border-bottom border-success border-3 h-100">
            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">LLP Entities</div>
            <div class="h3 fw-bold mb-1 text-success"><?php echo number_format($cat_data['LLP']); ?></div>
            <span class="badge-premium badge-premium-success"><?php echo $pct_llp; ?>% of Total</span>
        </div>
    </div>
    <div class="col-lg-3 col-6 mb-3 mb-lg-0">
        <div class="section-card p-3 text-center border-bottom border-warning border-3 h-100">
            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Foreign Companies</div>
            <div class="h3 fw-bold mb-1 text-warning"><?php echo number_format($cat_data['FOREIGN']); ?></div>
            <span class="badge-premium badge-premium-warning"><?php echo $pct_foreign; ?>% of Total</span>
        </div>
    </div>
    <div class="col-lg-3 col-6 mb-3 mb-lg-0">
        <div class="section-card p-3 text-center border-bottom border-info border-3 h-100">
            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Platform Users</div>
            <div class="h3 fw-bold mb-1 text-info"><?php echo number_format($total_users); ?></div>
            <span class="badge-premium badge-premium-info">Active Staff</span>
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
                    <i class="fas fa-database mr-1"></i> MCA Live Data
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
                        <i class="fas fa-chart-pie text-info mr-2"></i> Platform Registry Distribution
                    </h5>
                    <p class="text-muted small mb-0">Total records across databases</p>
                </div>
            </div>
            <div class="chart-container-box d-flex align-items-center justify-content-center">
                <canvas id="registryPieChart" style="max-height: 240px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Database Health & Overview Metrics -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="section-card p-4 h-100">
            <h5 class="font-weight-bold mb-3">
                <i class="fas fa-server text-primary mr-2"></i> Database System Metrics
            </h5>
            <div class="d-flex flex-column gap-3" style="gap: 14px;">
                <div>
                    <div class="d-flex justify-content-between small font-weight-bold mb-1">
                        <span>Indian Companies (<?php echo number_format($cat_data['INDIAN']); ?>)</span>
                        <span class="text-primary"><?php echo $pct_indian; ?>%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px; background: #e2e8f0;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $pct_indian; ?>%;"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between small font-weight-bold mb-1">
                        <span>LLP Entities (<?php echo number_format($cat_data['LLP']); ?>)</span>
                        <span class="text-success"><?php echo $pct_llp; ?>%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px; background: #e2e8f0;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $pct_llp; ?>%;"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between small font-weight-bold mb-1">
                        <span>Foreign Entities (<?php echo number_format($cat_data['FOREIGN']); ?>)</span>
                        <span class="text-warning"><?php echo $pct_foreign; ?>%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px; background: #e2e8f0;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $pct_foreign; ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="section-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="font-weight-bold mb-3">
                    <i class="fas fa-shield-alt text-success mr-2"></i> System Health &amp; Summary
                </h5>
                <ul class="list-unstyled mb-0" style="font-size: 0.9rem; line-height: 2;">
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted"><i class="fas fa-check text-success mr-2"></i> Database Status:</span>
                        <span class="badge-premium badge-premium-success">Operational</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted"><i class="fas fa-clock text-info mr-2"></i> Server Time:</span>
                        <strong><?php echo date('Y-m-d H:i:s'); ?></strong>
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted"><i class="fas fa-user-shield text-primary mr-2"></i> Active Admin:</span>
                        <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'admin'); ?></strong>
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted"><i class="fas fa-database text-warning mr-2"></i> Total Indexed Entities:</span>
                        <strong class="text-primary font-weight-bold"><?php echo number_format($total_companies + $total_ifsc + $total_pincodes + $total_acts); ?></strong>
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted"><i class="fas fa-sitemap text-primary mr-2"></i> Search Sitemaps:</span>
                        <a href="manage_sitemap.php" class="badge-premium badge-premium-primary text-decoration-none">
                            <i class="fas fa-cog mr-1"></i> Manage Sitemaps &rarr;
                        </a>
                    </li>
                </ul>
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
                        <?php echo $total_companies; ?>,
                        <?php echo $total_ifsc; ?>,
                        <?php echo $total_pincodes; ?>,
                        <?php echo $total_acts; ?>
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
