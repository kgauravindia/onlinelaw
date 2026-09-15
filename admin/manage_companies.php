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
        $cat = strtoupper($s['category']);
        $stats[$cat] = (int)$s['cnt'];
        $stats['TOTAL'] += (int)$s['cnt'];
    }
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h2 class="page-title mb-1">Manage Companies Incorporated</h2>
        <p class="text-muted mb-0">Browse, search, edit, or delete registered Indian Companies, LLPs, and Foreign Companies.</p>
    </div>
    <div class="mt-2 mt-md-0 d-flex flex-wrap" style="gap: 8px;">
        <a href="https://www.mca.gov.in/content/mca/global/en/data-and-reports/company-llp-info/incorporated-closed-month.html" target="_blank" class="btn btn-outline-info">
            <i class="fas fa-external-link-alt mr-1"></i> MCA Portal
        </a>
        <a href="upload_companies.php" class="btn btn-success">
            <i class="fas fa-file-excel mr-1"></i> Upload Monthly Excel
        </a>
        <a href="edit_company.php" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add Company
        </a>
        <a href="manage_data.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
</div>

<!-- Category Stat Pills -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-2">
        <a href="manage_companies.php" class="card shadow-sm border-0 text-decoration-none text-dark h-100 <?php echo empty($filter_category) ? 'border-primary' : ''; ?>" style="border-radius: 10px; <?php echo empty($filter_category) ? 'background: #eff6ff;' : ''; ?>">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase font-weight-bold">All Companies</div>
                    <div class="h4 font-weight-bold mb-0 text-primary"><?php echo number_format($stats['TOTAL']); ?></div>
                </div>
                <i class="fas fa-building fa-2x text-primary opacity-50"></i>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <a href="manage_companies.php?category=INDIAN" class="card shadow-sm border-0 text-decoration-none text-dark h-100 <?php echo $filter_category === 'INDIAN' ? 'border-info' : ''; ?>" style="border-radius: 10px; <?php echo $filter_category === 'INDIAN' ? 'background: #e0f2fe;' : ''; ?>">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase font-weight-bold">Indian Companies</div>
                    <div class="h4 font-weight-bold mb-0 text-info"><?php echo number_format($stats['INDIAN']); ?></div>
                </div>
                <i class="fas fa-city fa-2x text-info opacity-50"></i>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <a href="manage_companies.php?category=LLP" class="card shadow-sm border-0 text-decoration-none text-dark h-100 <?php echo $filter_category === 'LLP' ? 'border-success' : ''; ?>" style="border-radius: 10px; <?php echo $filter_category === 'LLP' ? 'background: #dcfce7;' : ''; ?>">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase font-weight-bold">LLP Companies</div>
                    <div class="h4 font-weight-bold mb-0 text-success"><?php echo number_format($stats['LLP']); ?></div>
                </div>
                <i class="fas fa-handshake fa-2x text-success opacity-50"></i>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <a href="manage_companies.php?category=FOREIGN" class="card shadow-sm border-0 text-decoration-none text-dark h-100 <?php echo $filter_category === 'FOREIGN' ? 'border-warning' : ''; ?>" style="border-radius: 10px; <?php echo $filter_category === 'FOREIGN' ? 'background: #fef3c7;' : ''; ?>">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase font-weight-bold">Foreign Companies</div>
                    <div class="h4 font-weight-bold mb-0 text-warning"><?php echo number_format($stats['FOREIGN']); ?></div>
                </div>
                <i class="fas fa-globe fa-2x text-warning opacity-50"></i>
            </div>
        </a>
    </div>
</div>

<!-- Search & Filter Filter Form -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
    <div class="card-body p-3">
        <form action="manage_companies.php" method="GET" class="row align-items-end">
            <div class="col-md-3 mb-2">
                <label class="small font-weight-bold text-muted mb-1">Search CIN / Name / Code</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" name="q" class="form-control" placeholder="Search CIN or Name..." value="<?php echo htmlspecialchars($search_q); ?>">
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <label class="small font-weight-bold text-muted mb-1">Category</label>
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    <option value="INDIAN" <?php echo ($filter_category === 'INDIAN') ? 'selected' : ''; ?>>Indian Company</option>
                    <option value="LLP" <?php echo ($filter_category === 'LLP') ? 'selected' : ''; ?>>LLP</option>
                    <option value="FOREIGN" <?php echo ($filter_category === 'FOREIGN') ? 'selected' : ''; ?>>Foreign Company</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label class="small font-weight-bold text-muted mb-1">Report Month</label>
                <select name="month" class="form-control">
                    <option value="">All Months</option>
                    <?php 
                    if ($months_res['count'] > 0) {
                        foreach ($months_res['data'] as $m) {
                            $sel = ($filter_month === $m['report_month']) ? 'selected' : '';
                            echo "<option value='{$m['report_month']}' $sel>{$m['report_month']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label class="small font-weight-bold text-muted mb-1">Registration Date</label>
                <div class="d-flex" style="gap: 5px;">
                    <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($filter_date_from); ?>" placeholder="From">
                    <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($filter_date_to); ?>" placeholder="To">
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <?php if (!empty($search_q) || !empty($filter_category) || !empty($filter_month) || !empty($filter_date_from) || !empty($filter_date_to)): ?>
                    <a href="manage_companies.php" class="btn btn-sm btn-light btn-block mt-1 text-muted">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
        <div class="font-weight-bold text-dark">
            Showing <?php echo number_format($total_records); ?> Records (Page <?php echo $page; ?> of <?php echo max(1, $total_pages); ?>)
        </div>
        <div>
            <a href="upload_companies.php" class="btn btn-sm btn-outline-success"><i class="fas fa-upload mr-1"></i> Upload More</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 110px;">Category</th>
                        <th style="width: 220px;">CIN / LLPIN / FCIN</th>
                        <th>Company Name</th>
                        <th>Class / Type</th>
                        <th style="width: 80px;">Activity</th>
                        <th style="width: 120px;">Reg Date</th>
                        <th style="width: 110px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($companies['count'] > 0): ?>
                        <?php foreach ($companies['data'] as $c): ?>
                            <?php
                            $badgeClass = 'badge-primary';
                            if ($c['category'] === 'LLP') $badgeClass = 'badge-success';
                            if ($c['category'] === 'FOREIGN') $badgeClass = 'badge-warning';
                            ?>
                            <tr>
                                <td class="text-muted small"><?php echo $c['id']; ?></td>
                                <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($c['category']); ?></span></td>
                                <td>
                                    <code class="font-weight-bold text-dark" style="font-size: 13px;"><?php echo htmlspecialchars($c['cin']); ?></code>
                                </td>
                                <td>
                                    <strong class="text-dark"><?php echo htmlspecialchars($c['company_name']); ?></strong>
                                </td>
                                <td>
                                    <span class="small text-muted d-block"><?php echo htmlspecialchars($c['class']); ?></span>
                                    <small class="text-secondary"><?php echo htmlspecialchars($c['company_type']); ?></small>
                                </td>
                                <td><span class="badge badge-light border"><?php echo htmlspecialchars($c['activity_code']); ?></span></td>
                                <td>
                                    <span class="small font-weight-bold text-dark">
                                        <?php echo !empty($c['registration_date']) ? date('d-M-Y', strtotime($c['registration_date'])) : '-'; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit_company.php?id=<?php echo $c['id']; ?>" class="btn btn-primary" title="Edit Company"><i class="fas fa-edit"></i></a>
                                        <a href="manage_companies.php?del=<?php echo $c['id']; ?>" class="btn btn-danger" title="Delete Company" onclick="return confirm('Are you sure you want to delete this company record?');"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-search fa-3x mb-3 text-muted opacity-50"></i>
                                <h6>No company records found matching your filters.</h6>
                                <p class="small mb-0">Try adjusting your search terms or <a href="upload_companies.php">upload a monthly Excel report</a>.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="card-footer bg-white border-top p-3 d-flex flex-wrap justify-content-between align-items-center">
        <div class="small text-muted mb-2 mb-md-0">
            Page <?php echo $page; ?> of <?php echo $total_pages; ?> (Total: <?php echo number_format($total_records); ?> records)
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                <?php
                $queryParams = $_GET;
                
                // Previous
                if ($page > 1) {
                    $queryParams['p'] = $page - 1;
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParams) . '">&laquo; Prev</a></li>';
                } else {
                    echo '<li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>';
                }

                // Range of page numbers
                $startPage = max(1, $page - 3);
                $endPage = min($total_pages, $page + 3);

                if ($startPage > 1) {
                    $queryParams['p'] = 1;
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParams) . '">1</a></li>';
                    if ($startPage > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }

                for ($i = $startPage; $i <= $endPage; $i++) {
                    $queryParams['p'] = $i;
                    $active = ($i == $page) ? 'active' : '';
                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="?' . http_build_query($queryParams) . '">' . $i . '</a></li>';
                }

                if ($endPage < $total_pages) {
                    if ($endPage < $total_pages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    $queryParams['p'] = $total_pages;
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParams) . '">' . $total_pages . '</a></li>';
                }

                // Next
                if ($page < $total_pages) {
                    $queryParams['p'] = $page + 1;
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParams) . '">Next &raquo;</a></li>';
                } else {
                    echo '<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>';
                }
                ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php require_once('includes/footer.php'); ?>
