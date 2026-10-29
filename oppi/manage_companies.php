<?php
require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../includes/function.php');

// Admin Auth Check
check_admin_auth(true);

// Handle Status Toggle (ACTIVE <-> STRUCK_OFF <-> BLOCK) via AJAX or direct GET/POST
if (isset($_REQUEST['toggle_status_id'])) {
    $toggle_id = intval($_REQUEST['toggle_status_id']);
    $to_status = isset($_REQUEST['to_status']) ? strtoupper(trim($_REQUEST['to_status'])) : '';
    
    if (!in_array($to_status, ['ACTIVE', 'STRUCK_OFF', 'BLOCK'])) {
        // Auto cycle if not explicitly specified: ACTIVE -> STRUCK_OFF -> BLOCK -> ACTIVE
        $curr = direct_sql("SELECT status FROM companies_incorporated WHERE id = $toggle_id LIMIT 1");
        $curr_status = strtoupper($curr['data'][0]['status'] ?? 'ACTIVE');
        if ($curr_status === 'ACTIVE' || empty($curr_status)) {
            $to_status = 'STRUCK_OFF';
        } elseif ($curr_status === 'STRUCK_OFF' || $curr_status === 'STRUCK OFF') {
            $to_status = 'BLOCK';
        } else {
            $to_status = 'ACTIVE';
        }
    }

    $update_res = update_data('companies_incorporated', ['status' => $to_status], $toggle_id);
    unset($_SESSION['mca_stats_cache']);
    @unlink(__DIR__ . '/../tmp/admin_dashboard_cache.json');
    @unlink(__DIR__ . '/../tmp/mca_months_cache.json');
    $is_ajax = isset($_REQUEST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

    if ($is_ajax) {
        header('Content-Type: application/json');
        if ($update_res['status'] === 'success') {
            $status_labels = [
                'ACTIVE' => 'ACTIVE (Visible on Website)',
                'STRUCK_OFF' => 'STRUCK OFF (Marked Struck Off by MCA)',
                'BLOCK' => 'BLOCKED (Hidden from Website)'
            ];
            echo json_encode([
                'status' => 'success',
                'id' => $toggle_id,
                'new_status' => $to_status,
                'message' => "Company status updated to " . ($status_labels[$to_status] ?? $to_status)
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update company status: ' . ($update_res['msg'] ?? 'Database error')
            ]);
        }
        exit();
    } else {
        $params = $_GET;
        unset($params['toggle_status_id'], $params['to_status'], $params['ajax']);
        $redirect_qs = !empty($params) ? '?' . http_build_query($params) : '';
        header("Location: manage_companies.php$redirect_qs");
        exit();
    }
}

// Handle Deletion
if (isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    if (!validate_csrf_token($_GET['csrf'] ?? '', 'admin_delete')) {
        echo "<script>alert('Security token invalid or expired. Please refresh the page.'); window.location.href='manage_companies.php';</script>";
        exit();
    }
    $res = delete_data('companies_incorporated', $del_id);
    unset($_SESSION['mca_stats_cache']);
    @unlink(__DIR__ . '/../tmp/admin_dashboard_cache.json');
    @unlink(__DIR__ . '/../tmp/mca_months_cache.json');
    if ($res['status'] === 'success') {
        echo "<script>alert('Company record deleted successfully'); window.location.href='manage_companies.php';</script>";
    } else {
        echo "<script>alert('Failed to delete company record'); window.location.href='manage_companies.php';</script>";
    }
    exit();
}

// Include standard admin header for HTML rendering
require_once('includes/header.php');

// Search & Filter parameters
$search_q = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter_category = isset($_GET['category']) ? trim($_GET['category']) : '';
$filter_status = isset($_GET['status']) ? strtoupper(trim($_GET['status'])) : '';
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
if (!empty($filter_status) && in_array($filter_status, ['ACTIVE', 'STRUCK_OFF', 'BLOCK'])) {
    $fs = mysqli_real_escape_string($con, $filter_status);
    if ($fs === 'BLOCK') {
        $where_clauses[] = "status = 'BLOCK'";
    } elseif ($fs === 'STRUCK_OFF') {
        $where_clauses[] = "(status = 'STRUCK_OFF' OR status = 'STRUCK OFF' OR status = 'STRIKE_OFF')";
    } else {
        $where_clauses[] = "(status = 'ACTIVE' OR status IS NULL OR status = '')";
    }
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

// Overall category & status stats with session caching (180s TTL)
$now = time();
if (!isset($_SESSION['mca_stats_cache']) || !isset($_SESSION['mca_stats_time']) || ($now - $_SESSION['mca_stats_time'] > 180)) {
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

    $status_stats_query = direct_sql("SELECT status, COUNT(*) as cnt FROM companies_incorporated GROUP BY status");
    $stats_status = ['ACTIVE' => 0, 'STRUCK_OFF' => 0, 'BLOCK' => 0];
    if ($status_stats_query['count'] > 0) {
        foreach ($status_stats_query['data'] as $st) {
            $st_val = strtoupper($st['status'] ?? '');
            if ($st_val === 'BLOCK') {
                $stats_status['BLOCK'] += (int)$st['cnt'];
            } elseif ($st_val === 'STRUCK_OFF' || $st_val === 'STRUCK OFF' || $st_val === 'STRIKE_OFF') {
                $stats_status['STRUCK_OFF'] += (int)$st['cnt'];
            } else {
                $stats_status['ACTIVE'] += (int)$st['cnt'];
            }
        }
    }
    $_SESSION['mca_stats_cache'] = ['stats' => $stats, 'stats_status' => $stats_status];
    $_SESSION['mca_stats_time'] = $now;
} else {
    $stats = $_SESSION['mca_stats_cache']['stats'];
    $stats_status = $_SESSION['mca_stats_cache']['stats_status'];
}

// Get total count for pagination (skip full table scan when unfiltered)
if ($where_sql === "1=1" && isset($stats['TOTAL']) && $stats['TOTAL'] > 0) {
    $total_records = $stats['TOTAL'];
} else {
    $count_query = direct_sql("SELECT COUNT(*) as total FROM companies_incorporated WHERE $where_sql");
    $total_records = isset($count_query['data'][0]['total']) ? (int)$count_query['data'][0]['total'] : 0;
}
$total_pages = ceil($total_records / $limit);

// Fetch records
$companies = direct_sql("SELECT * FROM companies_incorporated WHERE $where_sql ORDER BY registration_date DESC, id DESC LIMIT $offset, $limit");

// Get distinct months for filter dropdown (Cached for 1 hour to avoid scanning 1M rows on every page change)
$months_cache_file = __DIR__ . '/../tmp/mca_months_cache.json';
$months_res = null;
if (file_exists($months_cache_file) && (time() - filemtime($months_cache_file) < 3600)) {
    $raw_months = @file_get_contents($months_cache_file);
    if ($raw_months) {
        $months_res = json_decode($raw_months, true);
    }
}
if (!$months_res) {
    $months_res = direct_sql("SELECT DISTINCT report_month FROM companies_incorporated WHERE report_month IS NOT NULL AND report_month != '' ORDER BY report_month DESC");
    if (!is_dir(__DIR__ . '/../tmp')) {
        @mkdir(__DIR__ . '/../tmp', 0755, true);
    }
    @file_put_contents($months_cache_file, json_encode($months_res));
}
?>

<!-- Floating Toast Container for instant AJAX updates -->
<div id="statusToastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: none;">
    <div id="statusToast" class="alert alert-success shadow-lg border-0 d-flex align-items-center py-2 px-3" style="border-radius: var(--radius-md); font-size: 0.9rem;">
        <i id="statusToastIcon" class="fas fa-check-circle mr-2 fa-lg"></i>
        <span id="statusToastMsg" class="font-weight-semibold">Status updated successfully</span>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Manage Companies Incorporated</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Browse, search, edit, or toggle ACTIVE, STRUCK OFF &amp; BLOCK status for companies and LLPs.</p>
        
        <!-- Quick Status Pills -->
        <div class="d-flex align-items-center flex-wrap gap-2 mt-2" style="gap: 8px;">
            <span class="small font-weight-bold text-muted mr-1">MCA Status:</span>
            <a href="manage_companies.php?status=ACTIVE<?php echo !empty($filter_category) ? '&category='.$filter_category : ''; ?>" 
               class="badge <?php echo $filter_status === 'ACTIVE' ? 'badge-success shadow-sm' : 'badge-light border text-success'; ?> px-3 py-1 font-weight-bold" 
               style="border-radius: 20px; font-size: 0.8rem; text-decoration: none;">
                <i class="fas fa-check-circle mr-1"></i> Active on Web: <span id="hdr-active-count"><?php echo number_format($stats_status['ACTIVE']); ?></span>
            </a>
            <a href="manage_companies.php?status=STRUCK_OFF<?php echo !empty($filter_category) ? '&category='.$filter_category : ''; ?>" 
               class="badge <?php echo $filter_status === 'STRUCK_OFF' ? 'badge-warning shadow-sm' : 'badge-light border text-warning'; ?> px-3 py-1 font-weight-bold" 
               style="border-radius: 20px; font-size: 0.8rem; text-decoration: none; color: #b45309 !important;">
                <i class="fas fa-exclamation-triangle mr-1"></i> Struck Off: <span id="hdr-struck-count"><?php echo number_format($stats_status['STRUCK_OFF']); ?></span>
            </a>
            <a href="manage_companies.php?status=BLOCK<?php echo !empty($filter_category) ? '&category='.$filter_category : ''; ?>" 
               class="badge <?php echo $filter_status === 'BLOCK' ? 'badge-danger shadow-sm' : 'badge-light border text-danger'; ?> px-3 py-1 font-weight-bold" 
               style="border-radius: 20px; font-size: 0.8rem; text-decoration: none;">
                <i class="fas fa-ban mr-1"></i> Blocked from Web: <span id="hdr-block-count"><?php echo number_format($stats_status['BLOCK']); ?></span>
            </a>
            <?php if (!empty($filter_status)): ?>
                <a href="manage_companies.php<?php echo !empty($filter_category) ? '?category='.$filter_category : ''; ?>" class="small text-primary font-weight-bold ml-1 text-decoration-none" title="Show All Status">
                    <i class="fas fa-times-circle mr-1"></i> Reset Status Filter
                </a>
            <?php endif; ?>
        </div>
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
        <a href="manage_companies.php<?php echo !empty($filter_status) ? '?status='.$filter_status : ''; ?>" class="stat-card-v2 text-decoration-none d-block <?php echo empty($filter_category) ? 'border-primary' : ''; ?>" style="<?php echo empty($filter_category) ? 'background: #eff6ff; border-top: 4px solid var(--primary);' : ''; ?>">
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
        <a href="manage_companies.php?category=INDIAN<?php echo !empty($filter_status) ? '&status='.$filter_status : ''; ?>" class="stat-card-v2 text-decoration-none d-block <?php echo $filter_category === 'INDIAN' ? 'border-info' : ''; ?>" style="<?php echo $filter_category === 'INDIAN' ? 'background: #e0f2fe; border-top: 4px solid #0284c7;' : ''; ?>">
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
        <a href="manage_companies.php?category=LLP<?php echo !empty($filter_status) ? '&status='.$filter_status : ''; ?>" class="stat-card-v2 text-decoration-none d-block <?php echo $filter_category === 'LLP' ? 'border-success' : ''; ?>" style="<?php echo $filter_category === 'LLP' ? 'background: #dcfce7; border-top: 4px solid #10b981;' : ''; ?>">
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
        <a href="manage_companies.php?category=FOREIGN<?php echo !empty($filter_status) ? '&status='.$filter_status : ''; ?>" class="stat-card-v2 text-decoration-none d-block <?php echo $filter_category === 'FOREIGN' ? 'border-warning' : ''; ?>" style="<?php echo $filter_category === 'FOREIGN' ? 'background: #fef3c7; border-top: 4px solid #f59e0b;' : ''; ?>">
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
        <div class="col-xl-3 col-lg-3 col-md-6 mb-3">
            <label class="small font-weight-bold text-muted mb-1">Search CIN / Name / Code</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white text-muted"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" name="q" class="form-control" placeholder="Search CIN or Name..." value="<?php echo htmlspecialchars($search_q); ?>">
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
            <label class="small font-weight-bold text-muted mb-1">Category</label>
            <select name="category" class="form-control">
                <option value="">All Categories</option>
                <option value="INDIAN" <?php echo ($filter_category === 'INDIAN') ? 'selected' : ''; ?>>Indian Company</option>
                <option value="LLP" <?php echo ($filter_category === 'LLP') ? 'selected' : ''; ?>>LLP</option>
                <option value="FOREIGN" <?php echo ($filter_category === 'FOREIGN') ? 'selected' : ''; ?>>Foreign Company</option>
            </select>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-6 mb-3">
            <label class="small font-weight-bold text-muted mb-1">MCA Status</label>
            <select name="status" class="form-control">
                <option value="">All Status</option>
                <option value="ACTIVE" <?php echo ($filter_status === 'ACTIVE') ? 'selected' : ''; ?>>ACTIVE (Active on Web)</option>
                <option value="STRUCK_OFF" <?php echo ($filter_status === 'STRUCK_OFF') ? 'selected' : ''; ?>>STRUCK OFF (Struck Off by MCA)</option>
                <option value="BLOCK" <?php echo ($filter_status === 'BLOCK') ? 'selected' : ''; ?>>BLOCK (Hidden)</option>
            </select>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-6 mb-3">
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
        <div class="col-xl-3 col-lg-2 col-md-12 mb-3">
            <div class="d-flex gap-2" style="gap: 6px;">
                <button type="submit" class="btn btn-primary flex-grow-1 font-weight-bold">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <?php if (!empty($search_q) || !empty($filter_category) || !empty($filter_status) || !empty($filter_month) || !empty($filter_date_from) || !empty($filter_date_to)): ?>
                    <a href="manage_companies.php" class="btn btn-light text-muted" title="Reset All Filters"><i class="fas fa-undo"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- Records Table -->
<div class="section-card">
    <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom bg-white flex-wrap gap-2">
        <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
            Showing <?php echo number_format($total_records); ?> Records (Page <?php echo $page; ?> of <?php echo max(1, $total_pages); ?>)
            <?php if (!empty($filter_status)): ?>
                <span class="badge <?php echo $filter_status === 'BLOCK' ? 'badge-danger' : 'badge-success'; ?> ml-1">
                    Filtered: <?php echo $filter_status; ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="d-flex align-items-center gap-2" style="gap: 8px;">
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
                    <th style="width: 110px;">Category</th>
                    <th style="width: 220px;">CIN / LLPIN / FCIN</th>
                    <th>Company Name</th>
                    <th>Class / Type</th>
                    <th style="width: 85px;">Activity</th>
                    <th style="width: 115px;">Reg Date</th>
                    <th style="width: 135px;" class="text-center">Status</th>
                    <th style="width: 115px;" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($companies['count'] > 0): ?>
                    <?php foreach ($companies['data'] as $c): ?>
                        <?php 
                        $raw_status = strtoupper($c['status'] ?? 'ACTIVE');
                        $is_blocked = ($raw_status === 'BLOCK');
                        $is_struck = ($raw_status === 'STRUCK_OFF' || $raw_status === 'STRUCK OFF' || $raw_status === 'STRIKE_OFF');
                        $current_status = $is_blocked ? 'BLOCK' : ($is_struck ? 'STRUCK_OFF' : 'ACTIVE');
                        $next_status = $is_blocked ? 'ACTIVE' : ($is_struck ? 'BLOCK' : 'STRUCK_OFF');
                        ?>
                        <tr id="company-row-<?php echo $c['id']; ?>">
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
                                <a href="edit_company.php?id=<?php echo $c['id']; ?>" class="text-dark d-block text-decoration-none" style="font-size: 0.9rem;" title="Click to Edit this Company">
                                    <strong class="text-dark"><?php echo htmlspecialchars($c['company_name']); ?></strong>
                                </a>
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
                            <!-- Interactive Status Button -->
                            <td class="text-center" id="status-cell-<?php echo $c['id']; ?>">
                                <?php
                                $btnClass = 'btn-outline-success';
                                if ($is_blocked) {
                                    $btnClass = 'btn-outline-danger';
                                } elseif ($is_struck) {
                                    $btnClass = 'btn-outline-warning text-dark';
                                }
                                ?>
                                <button type="button" 
                                        class="btn btn-sm btn-status-toggle w-100 <?php echo $btnClass; ?> font-weight-bold"
                                        style="border-radius: 20px; font-size: 0.78rem; padding: 4px 8px; transition: all 0.2s;"
                                        data-id="<?php echo $c['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($c['company_name']); ?>"
                                        data-current="<?php echo $current_status; ?>"
                                        data-next="<?php echo $next_status; ?>"
                                        title="<?php echo $is_blocked ? 'Currently BLOCKED. Click to set ACTIVE.' : ($is_struck ? 'Currently STRUCK OFF. Click to set BLOCK.' : 'Currently ACTIVE. Click to set STRUCK OFF.'); ?>">
                                    <?php if ($is_blocked): ?>
                                        <i class="fas fa-ban mr-1"></i> <span>BLOCK</span>
                                        <span class="d-block small status-subtext" style="font-size: 0.68rem; font-weight: 500; opacity: 0.85;">Hidden on Web</span>
                                    <?php elseif ($is_struck): ?>
                                        <i class="fas fa-exclamation-triangle mr-1 text-warning"></i> <span>STRUCK OFF</span>
                                        <span class="d-block small status-subtext" style="font-size: 0.68rem; font-weight: 500; opacity: 0.85;">Struck in MCA</span>
                                    <?php else: ?>
                                        <i class="fas fa-check-circle mr-1"></i> <span>ACTIVE</span>
                                        <span class="d-block small status-subtext" style="font-size: 0.68rem; font-weight: 500; opacity: 0.85;">Visible on Web</span>
                                    <?php endif; ?>
                                </button>
                            </td>
                            <td class="text-right">
                                <div class="d-inline-flex gap-1" style="gap: 5px;">
                                    <?php 
                                    $action_params = $_GET;
                                    $action_params['toggle_status_id'] = $c['id'];
                                    $action_params['to_status'] = $next_status;
                                    $action_url = 'manage_companies.php?' . http_build_query($action_params);
                                    ?>
                                    <a href="../company-incorporation?cin=<?php echo urlencode($c['cin']); ?>" target="_blank" class="btn-action-circle text-info" title="View on Public Website">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <a href="<?php echo $action_url; ?>" 
                                       class="btn-action-circle <?php echo $is_blocked ? 'text-success' : ($is_struck ? 'text-danger' : 'text-warning'); ?> action-status-link-<?php echo $c['id']; ?>" 
                                       data-id="<?php echo $c['id']; ?>"
                                       data-next="<?php echo $next_status; ?>"
                                       title="Cycle status to <?php echo $next_status; ?>"
                                       onclick="return confirm('Change status of this company to <?php echo $next_status; ?>?');">
                                        <i class="fas <?php echo $is_blocked ? 'fa-eye' : ($is_struck ? 'fa-ban' : 'fa-exclamation-triangle'); ?>"></i>
                                    </a>
                                    <a href="edit_company.php?id=<?php echo $c['id']; ?>" class="btn-action-circle text-primary" title="Edit Company Details">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="manage_companies.php?del=<?php echo $c['id']; ?>&csrf=<?php echo generate_csrf_token('admin_delete'); ?>" class="btn-action-circle text-danger" title="Delete Record" onclick="return confirm('Are you sure you want to delete this company record?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Quick AJAX Status Toggle
    const toggleButtons = document.querySelectorAll('.btn-status-toggle');
    const toastContainer = document.getElementById('statusToastContainer');
    const toastBox = document.getElementById('statusToast');
    const toastMsg = document.getElementById('statusToastMsg');
    const toastIcon = document.getElementById('statusToastIcon');
    let toastTimeout = null;

    function showToast(message, isSuccess) {
        if (toastTimeout) clearTimeout(toastTimeout);
        toastMsg.textContent = message;
        if (isSuccess) {
            toastBox.className = 'alert alert-success shadow-lg border-0 d-flex align-items-center py-2 px-3';
            toastIcon.className = 'fas fa-check-circle mr-2 fa-lg';
        } else {
            toastBox.className = 'alert alert-danger shadow-lg border-0 d-flex align-items-center py-2 px-3';
            toastIcon.className = 'fas fa-exclamation-triangle mr-2 fa-lg';
        }
        toastContainer.style.display = 'block';
        toastTimeout = setTimeout(() => {
            toastContainer.style.display = 'none';
        }, 3500);
    }

    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const currentStatus = this.getAttribute('data-current');
            const nextStatus = this.getAttribute('data-next');

            // Optimistic loading state
            const origHtml = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';
            this.disabled = true;

            fetch('manage_companies.php?toggle_status_id=' + encodeURIComponent(id) + '&to_status=' + encodeURIComponent(nextStatus) + '&ajax=1', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.disabled = false;
                if (data.status === 'success') {
                    const newStatus = data.new_status;
                    let newNext = 'ACTIVE';
                    if (newStatus === 'ACTIVE') newNext = 'STRUCK_OFF';
                    else if (newStatus === 'STRUCK_OFF') newNext = 'BLOCK';
                    else if (newStatus === 'BLOCK') newNext = 'ACTIVE';

                    // Update button styling and contents
                    this.setAttribute('data-current', newStatus);
                    this.setAttribute('data-next', newNext);

                    if (newStatus === 'BLOCK') {
                        this.className = 'btn btn-sm btn-status-toggle w-100 btn-outline-danger font-weight-bold';
                        this.innerHTML = '<i class="fas fa-ban mr-1"></i> <span>BLOCK</span><span class="d-block small status-subtext" style="font-size: 0.68rem; font-weight: 500; opacity: 0.85;">Hidden on Web</span>';
                        this.title = 'Currently HIDDEN on website. Click to set ACTIVE.';
                    } else if (newStatus === 'STRUCK_OFF') {
                        this.className = 'btn btn-sm btn-status-toggle w-100 btn-outline-warning text-dark font-weight-bold';
                        this.innerHTML = '<i class="fas fa-exclamation-triangle mr-1 text-warning"></i> <span>STRUCK OFF</span><span class="d-block small status-subtext" style="font-size: 0.68rem; font-weight: 500; opacity: 0.85;">Struck in MCA</span>';
                        this.title = 'Currently STRUCK OFF in MCA. Click to set BLOCK.';
                    } else {
                        this.className = 'btn btn-sm btn-status-toggle w-100 btn-outline-success font-weight-bold';
                        this.innerHTML = '<i class="fas fa-check-circle mr-1"></i> <span>ACTIVE</span><span class="d-block small status-subtext" style="font-size: 0.68rem; font-weight: 500; opacity: 0.85;">Visible on Web</span>';
                        this.title = 'Currently VISIBLE on website. Click to set STRUCK OFF.';
                    }

                    // Update corresponding action link icon and title in same row
                    const actionLink = document.querySelector('.action-status-link-' + id);
                    if (actionLink) {
                        actionLink.setAttribute('data-next', newNext);
                        if (newStatus === 'BLOCK') {
                            actionLink.className = 'btn-action-circle text-success action-status-link-' + id;
                            actionLink.innerHTML = '<i class="fas fa-eye"></i>';
                            actionLink.title = 'Set ACTIVE';
                        } else if (newStatus === 'STRUCK_OFF') {
                            actionLink.className = 'btn-action-circle text-danger action-status-link-' + id;
                            actionLink.innerHTML = '<i class="fas fa-ban"></i>';
                            actionLink.title = 'Set BLOCK';
                        } else {
                            actionLink.className = 'btn-action-circle text-warning action-status-link-' + id;
                            actionLink.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                            actionLink.title = 'Set STRUCK OFF';
                        }
                    }

                    showToast((name ? name + ': ' : '') + data.message, true);
                } else {
                    this.innerHTML = origHtml;
                    showToast(data.message || 'Error updating company status', false);
                }
            })
            .catch(err => {
                this.disabled = false;
                this.innerHTML = origHtml;
                showToast('Network error while updating status', false);
            });
        });
    });
});
</script>

<?php require_once('includes/footer.php'); ?>
