<?php
require_once('includes/header.php');

// Handle Deletion
if (isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    $res = delete_data('companies_incorporated', $del_id);
    if ($res['status'] === 'success') {
        echo "<script>alert('Company record deleted successfully'); window.location.href='manage_companies.php';</script>";
    } else {
        echo "<script>alert('Failed to delete company record'); window.location.href='manage_companies.php';</script>";
    }
}

// Search & Filter parameters
$search_q = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter_category = isset($_GET['category']) ? trim($_GET['category']) : '';
$filter_month = isset($_GET['month']) ? trim($_GET['month']) : '';
$filter_date_from = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$filter_date_to = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

// Build WHERE clause
$where_clauses = ["1=1"];
if (!empty($search_q)) {
    $sq = mysqli_real_escape_string($con, $search_q);
    $where_clauses[] = "(cin LIKE '%$sq%' OR company_name LIKE '%$sq%' OR activity_code LIKE '%$sq%')";
}
if (!empty($filter_category) && in_array($filter_category, ['INDIAN', 'LLP', 'FOREIGN'])) {
    $fc = mysqli_real_escape_string($con, $filter_category);
    $where_clauses[] = "category = '$fc'";
}
if (!empty($filter_month)) {
    $fm = mysqli_real_escape_string($con, $filter_month);
    $where_clauses[] = "report_month = '$fm'";
}
if (!empty($filter_date_from)) {
    $fdf = mysqli_real_escape_string($con, $filter_date_from);
    $where_clauses[] = "registration_date >= '$fdf'";
}
if (!empty($filter_date_to)) {
    $fdt = mysqli_real_escape_string($con, $filter_date_to);
    $where_clauses[] = "registration_date <= '$fdt'";
}

$where_sql = implode(' AND ', $where_clauses);

// Get total count for pagination
$count_query = direct_sql("SELECT COUNT(*) as total FROM companies_incorporated WHERE $where_sql");
$total_records = isset($count_query['data'][0]['total']) ? (int)$count_query['data'][0]['total'] : 0;
$total_pages = ceil($total_records / $limit);

// Fetch records
$companies = direct_sql("SELECT * FROM companies_incorporated WHERE $where_sql ORDER BY registration_date DESC, id DESC LIMIT $offset, $limit");

// Get distinct months for filter dropdown
$months_res = direct_sql("SELECT DISTINCT report_month FROM companies_incorporated WHERE report_month IS NOT NULL AND report_month != '' ORDER BY report_month DESC");

// Overall stats
$stats_query = direct_sql("SELECT category, COUNT(*) as cnt FROM companies_incorporated GROUP BY category");
$stats = ['INDIAN' => 0, 'LLP' => 0, 'FOREIGN' => 0, 'TOTAL' => 0];
if ($stats_query['count'] > 0) {
    foreach ($stats_query['data'] as $s) {
        $cat = strtoupper($s['category'] ?? '');
        if (isset($stats[$cat])) {
            $stats[$cat] = (int)$s['cnt'];
        }
        $stats['TOTAL'] += (int)$s['cnt'];
    }
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Manage Companies Incorporated</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Browse, search, edit, or filter registered Indian Companies, LLPs, and Foreign entities.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2" style="gap: 8px;">
        <a href="https://www.mca.gov.in/content/mca/global/en/data-and-reports/company-llp-info/incorporated-closed-month.html" target="_blank" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-external-link-alt mr-1"></i> MCA Portal
        </a>
        <a href="upload_companies.php" class="btn btn-success font-weight-bold shadow-sm" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-file-excel mr-1"></i> Upload MCA Excel
        </a>
        <a href="edit_company.php" class="btn btn-primary font-weight-bold shadow-sm" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-plus mr-1"></i> Add Company
        </a>
    </div>
</div>

<!-- Category Stat Filters -->
<div class="row mb-4">
    <div class="col-md-3 col-6 mb-3 mb-md-0">
        <a href="manage_companies.php" class="stat-card-v2 text-decoration-none d-block <?php echo empty($filter_category) ? 'border-primary' : ''; ?>" style="<?php echo empty($filter_category) ? 'background: #eff6ff; border-top: 4px solid var(--primary);' : ''; ?>">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label mb-1">All Companies</div>
                    <div class="stat-value text-primary"><?php echo number_format($stats['TOTAL']); ?></div>
                </div>
                <div class="stat-icon" style="background: #eff6ff; color: var(--primary);">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-6 mb-3 mb-md-0">
        <a href="manage_companies.php?category=INDIAN" class="stat-card-v2 text-decoration-none d-block <?php echo $filter_category === 'INDIAN' ? 'border-info' : ''; ?>" style="<?php echo $filter_category === 'INDIAN' ? 'background: #e0f2fe; border-top: 4px solid #0284c7;' : ''; ?>">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label mb-1">Indian Companies</div>
                    <div class="stat-value text-info"><?php echo number_format($stats['INDIAN']); ?></div>
                </div>
                <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fas fa-city"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-6 mb-3 mb-md-0">
        <a href="manage_companies.php?category=LLP" class="stat-card-v2 text-decoration-none d-block <?php echo $filter_category === 'LLP' ? 'border-success' : ''; ?>" style="<?php echo $filter_category === 'LLP' ? 'background: #dcfce7; border-top: 4px solid #10b981;' : ''; ?>">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label mb-1">LLPs</div>
                    <div class="stat-value text-success"><?php echo number_format($stats['LLP']); ?></div>
                </div>
                <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-6 mb-3 mb-md-0">
        <a href="manage_companies.php?category=FOREIGN" class="stat-card-v2 text-decoration-none d-block <?php echo $filter_category === 'FOREIGN' ? 'border-warning' : ''; ?>" style="<?php echo $filter_category === 'FOREIGN' ? 'background: #fef3c7; border-top: 4px solid #f59e0b;' : ''; ?>">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label mb-1">Foreign Companies</div>
                    <div class="stat-value text-warning"><?php echo number_format($stats['FOREIGN']); ?></div>
                </div>
                <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                    <i class="fas fa-globe"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Search & Filter Console -->
<div class="section-card p-4 mb-4">
    <form action="manage_companies.php" method="GET" class="row align-items-end">
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <label class="small font-weight-bold text-muted mb-1">Search CIN / Name / Code</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white text-muted"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" name="q" class="form-control" placeholder="Search CIN or Name..." value="<?php echo htmlspecialchars($search_q); ?>">
            </div>
        </div>
        <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
            <label class="small font-weight-bold text-muted mb-1">Category</label>
            <select name="category" class="form-control">
                <option value="">All Categories</option>
                <option value="INDIAN" <?php echo ($filter_category === 'INDIAN') ? 'selected' : ''; ?>>Indian Company</option>
                <option value="LLP" <?php echo ($filter_category === 'LLP') ? 'selected' : ''; ?>>LLP</option>
                <option value="FOREIGN" <?php echo ($filter_category === 'FOREIGN') ? 'selected' : ''; ?>>Foreign Company</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
            <label class="small font-weight-bold text-muted mb-1">Report Month</label>
            <select name="month" class="form-control">
                <option value="">All Months</option>
                <?php 
                if (isset($months_res['data']) && count($months_res['data']) > 0) {
                    foreach ($months_res['data'] as $m) {
                        $sel = ($filter_month === $m['report_month']) ? 'selected' : '';
                        echo "<option value='{$m['report_month']}' $sel>{$m['report_month']}</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <label class="small font-weight-bold text-muted mb-1">Registration Date</label>
            <div class="d-flex" style="gap: 6px;">
                <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($filter_date_from); ?>">
                <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($filter_date_to); ?>">
            </div>
        </div>
        <div class="col-lg-2 col-md-12">
            <div class="d-flex gap-2" style="gap: 6px;">
                <button type="submit" class="btn btn-primary flex-grow-1 font-weight-bold">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <?php if (!empty($search_q) || !empty($filter_category) || !empty($filter_month) || !empty($filter_date_from) || !empty($filter_date_to)): ?>
                    <a href="manage_companies.php" class="btn btn-light text-muted" title="Reset Filters"><i class="fas fa-undo"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- Records Table -->
<div class="section-card">
    <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom bg-white">
        <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
            Showing <?php echo number_format($total_records); ?> Records (Page <?php echo $page; ?> of <?php echo max(1, $total_pages); ?>)
        </div>
        <div>
            <a href="upload_companies.php" class="btn btn-sm btn-outline-success font-weight-bold rounded-pill px-3">
                <i class="fas fa-upload mr-1"></i> Upload Excel
            </a>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th style="width: 120px;">Category</th>
                    <th style="width: 230px;">CIN / LLPIN / FCIN</th>
                    <th>Company Name</th>
                    <th>Class / Type</th>
                    <th style="width: 90px;">Activity</th>
                    <th style="width: 130px;">Reg Date</th>
                    <th style="width: 110px;" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($companies['count'] > 0): ?>
                    <?php foreach ($companies['data'] as $c): ?>
                        <tr>
                            <td class="text-muted small">#<?php echo $c['id']; ?></td>
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
                                <span class="font-weight-bold font-monospace" style="font-size: 0.85rem; color: var(--primary);">
                                    <?php echo htmlspecialchars($c['cin']); ?>
                                </span>
                            </td>
                            <td>
                                <strong class="text-dark d-block" style="font-size: 0.9rem;"><?php echo htmlspecialchars($c['company_name']); ?></strong>
                                <small class="text-muted"><i class="fas fa-map-marker-alt text-danger mr-1"></i> <?php echo htmlspecialchars($c['roc_state'] ?? $c['state'] ?? 'N/A'); ?></small>
                            </td>
                            <td>
                                <span class="small font-weight-bold text-dark d-block"><?php echo htmlspecialchars($c['class'] ?? 'N/A'); ?></span>
                                <small class="text-muted"><?php echo htmlspecialchars($c['company_type'] ?? ''); ?></small>
                            </td>
                            <td>
                                <span class="badge-premium badge-premium-info"><?php echo htmlspecialchars($c['activity_code'] ?? 'N/A'); ?></span>
                            </td>
                            <td>
                                <span class="small font-weight-bold text-dark">
                                    <i class="far fa-calendar text-muted mr-1"></i> <?php echo !empty($c['registration_date']) ? date('d-M-Y', strtotime($c['registration_date'])) : '-'; ?>
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="d-inline-flex gap-1" style="gap: 5px;">
                                    <a href="edit_company.php?id=<?php echo $c['id']; ?>" class="btn-action-circle text-primary" title="Edit Company">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="manage_companies.php?del=<?php echo $c['id']; ?>" class="btn-action-circle text-danger" title="Delete Record" onclick="return confirm('Are you sure you want to delete this company record?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-search fa-3x mb-3 d-block text-muted opacity-50"></i>
                            <h6 class="font-weight-bold">No Company Records Found</h6>
                            <p class="small text-muted mb-0">Try adjusting your search filters or upload a new MCA Excel sheet.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="p-3 px-4 border-top d-flex justify-content-between align-items-center flex-wrap bg-white">
            <div class="small text-muted mb-2 mb-md-0">
                Page <?php echo $page; ?> of <?php echo $total_pages; ?> (<?php echo number_format($total_records); ?> Total)
            </div>
            <ul class="pagination pagination-sm mb-0">
                <?php
                // Helper to preserve existing GET parameters in pagination links
                $params = $_GET;
                
                // Previous page
                if ($page > 1) {
                    $params['p'] = $page - 1;
                    $prev_link = 'manage_companies.php?' . http_build_query($params);
                    echo "<li class='page-item'><a class='page-link' href='$prev_link'>&laquo; Prev</a></li>";
                }

                $start_p = max(1, $page - 2);
                $end_p = min($total_pages, $page + 2);
                for ($pi = $start_p; $pi <= $end_p; $pi++) {
                    $params['p'] = $pi;
                    $p_link = 'manage_companies.php?' . http_build_query($params);
                    $act = ($pi == $page) ? 'active' : '';
                    echo "<li class='page-item $act'><a class='page-link' href='$p_link'>$pi</a></li>";
                }

                // Next page
                if ($page < $total_pages) {
                    $params['p'] = $page + 1;
                    $next_link = 'manage_companies.php?' . http_build_query($params);
                    echo "<li class='page-item'><a class='page-link' href='$next_link'>Next &raquo;</a></li>";
                }
                ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<?php require_once('includes/footer.php'); ?>
