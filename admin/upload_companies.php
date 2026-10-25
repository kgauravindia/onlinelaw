<?php
require_once('includes/header.php');
require_once('../includes/company_importer.php');

$import_result = null;
$error_msg = null;
$success_msg = null;

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_excel') {
    if (!empty($_FILES['excel_file']['name'])) {
        $validation = validate_uploaded_file($_FILES['excel_file'], ['xlsx']);
        if (!$validation['safe']) {
            $error_msg = $validation['error'];
        } else {
            $fileName = sanitize_uploaded_filename($_FILES['excel_file']['name']);
            $fileTmp = $_FILES['excel_file']['tmp_name'];
            $forcedMonth = !empty($_POST['report_month']) ? trim($_POST['report_month']) : null;
            $reportType = !empty($_POST['report_type']) ? trim($_POST['report_type']) : 'auto';
            
            $uploadDir = '../uploads/reports/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $targetPath = $uploadDir . time() . '_' . $fileName;
            if (move_uploaded_file($fileTmp, $targetPath)) {
                // Auto-detect if needed
                if ($reportType === 'auto') {
                    $lowerName = strtolower($fileName);
                    if (strpos($lowerName, 'struck') !== false || strpos($lowerName, 'strike') !== false || strpos($lowerName, 'company-report') !== false || strpos($lowerName, 'llp-report') !== false) {
                        $reportType = 'struck_off';
                    } else {
                        $reportType = 'incorporation';
                    }
                }

                if ($reportType === 'struck_off') {
                    $import_result = import_mca_struck_off_excel($targetPath, $con, $forcedMonth);
                    $import_result['import_mode'] = 'struck_off';
                } else {
                    $import_result = import_mca_incorporation_excel($targetPath, $con, $forcedMonth);
                    $import_result['import_mode'] = 'incorporation';
                }
            } else {
                $error_msg = "Failed to save uploaded file on server.";
            }
        }
    } else {
        $error_msg = "Please select an Excel file to upload.";
    }
}

// Quick Server File Actions
if (isset($_POST['import_server_company_struck'])) {
    $serverFile = '../Company-Report-20260901.xlsx';
    if (file_exists($serverFile)) {
        $import_result = import_mca_struck_off_excel($serverFile, $con);
        $import_result['import_mode'] = 'struck_off';
        $import_result['file_title'] = 'Company Struck Off Report (Company-Report-20260901.xlsx)';
    } else {
        $error_msg = "Server file 'Company-Report-20260901.xlsx' not found in project root.";
    }
}

if (isset($_POST['import_server_llp_struck'])) {
    $serverFile = '../LLP-Report-20260901.xlsx';
    if (file_exists($serverFile)) {
        $import_result = import_mca_struck_off_excel($serverFile, $con);
        $import_result['import_mode'] = 'struck_off';
        $import_result['file_title'] = 'LLP Struck Off Report (LLP-Report-20260901.xlsx)';
    } else {
        $error_msg = "Server file 'LLP-Report-20260901.xlsx' not found in project root.";
    }
}

if (isset($_POST['import_server_all_struck'])) {
    $file1 = '../Company-Report-20260901.xlsx';
    $file2 = '../LLP-Report-20260901.xlsx';
    
    $total_processed = 0;
    $total_indian = 0;
    $total_llp = 0;
    $errors = [];
    $startAll = microtime(true);

    if (file_exists($file1)) {
        $res1 = import_mca_struck_off_excel($file1, $con);
        $total_processed += ($res1['total'] ?? 0);
        $total_indian += ($res1['INDIAN'] ?? 0);
        if (!empty($res1['errors'])) $errors = array_merge($errors, $res1['errors']);
    } else {
        $errors[] = "Company-Report-20260901.xlsx not found";
    }

    if (file_exists($file2)) {
        $res2 = import_mca_struck_off_excel($file2, $con);
        $total_processed += ($res2['total'] ?? 0);
        $total_llp += ($res2['LLP'] ?? 0);
        if (!empty($res2['errors'])) $errors = array_merge($errors, $res2['errors']);
    } else {
        $errors[] = "LLP-Report-20260901.xlsx not found";
    }

    $import_result = [
        'status' => empty($errors) ? 'success' : 'partial',
        'import_mode' => 'struck_off',
        'file_title' => 'Both Company & LLP Struck Off Reports',
        'total' => $total_processed,
        'total_processed' => $total_processed,
        'INDIAN' => $total_indian,
        'indian_companies' => $total_indian,
        'LLP' => $total_llp,
        'llp_companies' => $total_llp,
        'foreign_companies' => 0,
        'duration' => round(microtime(true) - $startAll, 2),
        'errors' => $errors,
        'message' => "Successfully processed all struck-off records: {$total_processed} total ({$total_indian} Indian Companies, {$total_llp} LLPs) in " . round(microtime(true) - $startAll, 2) . "s."
    ];
}

if (isset($_POST['import_server_file'])) {
    $serverFile = '../Incorporation-Report-20260901.xlsx';
    if (file_exists($serverFile)) {
        $import_result = import_mca_incorporation_excel($serverFile, $con);
        $import_result['import_mode'] = 'incorporation';
        $import_result['file_title'] = 'Incorporation Report (Incorporation-Report-20260901.xlsx)';
    } else {
        $error_msg = "Server file 'Incorporation-Report-20260901.xlsx' not found.";
    }
}

// Clear public & admin stats cache if import just completed
if ($import_result !== null) {
    if (function_exists('set_setting')) {
        set_setting('company_stats_cache', '');
    }
    unset($_SESSION['mca_stats_cache']);
    @unlink(__DIR__ . '/../tmp/admin_dashboard_cache.json');
    @unlink(__DIR__ . '/../tmp/mca_months_cache.json');
}

// Get current database counts
$stats_query = direct_sql("SELECT category, COUNT(*) as cnt FROM companies_incorporated GROUP BY category");
$stats = ['INDIAN' => 0, 'LLP' => 0, 'FOREIGN' => 0, 'TOTAL' => 0];
if (isset($stats_query['data']) && count($stats_query['data']) > 0) {
    foreach ($stats_query['data'] as $s) {
        $cat = strtoupper($s['category'] ?? '');
        if (isset($stats[$cat])) {
            $stats[$cat] = (int)$s['cnt'];
        }
        $stats['TOTAL'] += (int)$s['cnt'];
    }
}

// Struck Off and Block stats
$status_stats_query = direct_sql("SELECT status, COUNT(*) as cnt FROM companies_incorporated GROUP BY status");
$stats_status = ['ACTIVE' => 0, 'STRUCK_OFF' => 0, 'BLOCK' => 0];
if (isset($status_stats_query['data']) && count($status_stats_query['data']) > 0) {
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

// Check server file existence
$server_company_struck = file_exists('../Company-Report-20260901.xlsx');
$server_company_struck_size = $server_company_struck ? round(filesize('../Company-Report-20260901.xlsx') / 1024, 1) . ' KB' : null;

$server_llp_struck = file_exists('../LLP-Report-20260901.xlsx');
$server_llp_struck_size = $server_llp_struck ? round(filesize('../LLP-Report-20260901.xlsx') / 1024, 1) . ' KB' : null;

$server_incorp = file_exists('../Incorporation-Report-20260901.xlsx');
$server_incorp_size = $server_incorp ? round(filesize('../Incorporation-Report-20260901.xlsx') / (1024 * 1024), 2) . ' MB' : null;
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Upload &amp; Update MCA Company Records</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Upload monthly MCA incorporation workbooks and strike off / struck off reports for Indian Companies and LLPs.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <a href="manage_companies.php?status=STRUCK_OFF" class="btn btn-outline-warning font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem; color: #b45309; border-color: #f59e0b;">
            <i class="fas fa-exclamation-triangle mr-1"></i> View Struck Off (<?php echo number_format($stats_status['STRUCK_OFF']); ?>)
        </a>
        <a href="manage_companies.php" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-list mr-1"></i> View All Companies
        </a>
    </div>
</div>

<!-- Live Database Stats Row -->
<div class="row mb-4">
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stat-card-v2" style="border-top: 4px solid var(--primary);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label mb-1">Total Indexed</div>
                    <div class="stat-value text-primary"><?php echo number_format($stats['TOTAL']); ?></div>
                </div>
                <div class="stat-icon" style="background: #eff6ff; color: var(--primary);">
                    <i class="fas fa-database"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stat-card-v2" style="border-top: 4px solid #0284c7;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label mb-1">Indian Companies</div>
                    <div class="stat-value text-info"><?php echo number_format($stats['INDIAN']); ?></div>
                </div>
                <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fas fa-city"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stat-card-v2" style="border-top: 4px solid #10b981;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label mb-1">LLP Entities</div>
                    <div class="stat-value text-success"><?php echo number_format($stats['LLP']); ?></div>
                </div>
                <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stat-card-v2" style="border-top: 4px solid #6366f1;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label mb-1">Foreign Entities</div>
                    <div class="stat-value" style="color: #6366f1;"><?php echo number_format($stats['FOREIGN']); ?></div>
                </div>
                <div class="stat-icon" style="background: #eef2ff; color: #6366f1;">
                    <i class="fas fa-globe"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stat-card-v2" style="border-top: 4px solid #f59e0b; background: #fffdf5;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label mb-1">Struck Off (MCA)</div>
                    <div class="stat-value text-warning" style="color: #d97706 !important;"><?php echo number_format($stats_status['STRUCK_OFF']); ?></div>
                </div>
                <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                    <i class="fas fa-ban"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="stat-card-v2" style="border-top: 4px solid #ef4444;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label mb-1">Blocked on Web</div>
                    <div class="stat-value text-danger"><?php echo number_format($stats_status['BLOCK']); ?></div>
                </div>
                <div class="stat-icon" style="background: #fef2f2; color: #ef4444;">
                    <i class="fas fa-eye-slash"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Alerts & Status -->
<?php if ($error_msg): ?>
    <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center mb-4" style="border-radius: var(--radius-md); gap: 10px;">
        <i class="fas fa-exclamation-triangle fa-lg"></i>
        <div><?php echo htmlspecialchars($error_msg); ?></div>
    </div>
<?php endif; ?>

<?php if ($import_result): ?>
    <?php 
    $isStruckResult = (($import_result['import_mode'] ?? '') === 'struck_off');
    $resBorderColor = $isStruckResult ? '#f59e0b' : '#10b981';
    $resBgIcon = $isStruckResult ? '#fef3c7' : '#dcfce7';
    $resTextIcon = $isStruckResult ? '#d97706' : '#16a34a';
    $resTitle = $isStruckResult ? 'Strike Off Report Processed &amp; Updated' : 'Batch Import Completed Successfully';
    ?>
    <div class="section-card p-4 mb-4" style="border-left: 5px solid <?php echo $resBorderColor; ?>;">
        <div class="d-flex align-items-center mb-3">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: <?php echo $resBgIcon; ?>; color: <?php echo $resTextIcon; ?>; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-right: 12px;">
                <i class="fas <?php echo $isStruckResult ? 'fa-check-double' : 'fa-check-circle'; ?>"></i>
            </div>
            <div>
                <h5 class="font-weight-bold mb-0" style="color: <?php echo $resTextIcon; ?>;"><?php echo $resTitle; ?></h5>
                <p class="text-muted small mb-0">
                    <?php echo htmlspecialchars($import_result['message'] ?? 'Records were parsed and updated into the database.'); ?>
                    <?php if (!empty($import_result['duration'])): ?>
                        <span class="badge badge-light border ml-1"><i class="fas fa-bolt text-warning mr-1"></i><?php echo $import_result['duration']; ?>s</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="row pt-2">
            <div class="col-md-3 col-6 mb-2">
                <div class="p-3 bg-light rounded text-center">
                    <div class="small text-muted text-uppercase font-weight-bold">Total Records</div>
                    <div class="h4 font-weight-bold text-dark mb-0"><?php echo number_format($import_result['total_processed'] ?? $import_result['total'] ?? 0); ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="p-3 bg-light rounded text-center">
                    <div class="small text-muted text-uppercase font-weight-bold"><?php echo $isStruckResult ? 'Companies Struck' : 'Indian Companies'; ?></div>
                    <div class="h4 font-weight-bold text-primary mb-0"><?php echo number_format($import_result['indian_companies'] ?? $import_result['INDIAN'] ?? 0); ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="p-3 bg-light rounded text-center">
                    <div class="small text-muted text-uppercase font-weight-bold"><?php echo $isStruckResult ? 'LLPs Struck' : 'LLPs Added'; ?></div>
                    <div class="h4 font-weight-bold text-success mb-0"><?php echo number_format($import_result['llp_companies'] ?? $import_result['LLP'] ?? 0); ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="p-3 bg-light rounded text-center">
                    <div class="small text-muted text-uppercase font-weight-bold"><?php echo $isStruckResult ? 'Status Applied' : 'Foreign Entities'; ?></div>
                    <div class="h4 font-weight-bold <?php echo $isStruckResult ? 'text-warning' : 'text-secondary'; ?> mb-0">
                        <?php echo $isStruckResult ? 'STRUCK OFF' : number_format($import_result['foreign_companies'] ?? $import_result['FOREIGN'] ?? 0); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2" style="gap: 8px;">
            <?php if ($isStruckResult): ?>
                <a href="manage_companies.php?status=STRUCK_OFF" class="btn btn-sm btn-warning font-weight-bold px-3 text-dark shadow-sm">
                    <i class="fas fa-exclamation-triangle mr-1"></i> View Struck Off Records
                </a>
            <?php else: ?>
                <a href="manage_companies.php" class="btn btn-sm btn-primary font-weight-bold px-3 shadow-sm">
                    <i class="fas fa-arrow-right mr-1"></i> View Imported Records
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- 1-Click Server Struck Off & Report Processors -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: var(--radius-md); background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); border: 1px solid #e2e8f0 !important;">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <h5 class="font-weight-bold mb-1" style="font-family: 'Outfit', sans-serif;">
                    <i class="fas fa-bolt text-warning mr-2"></i> Quick 1-Click Server File Processors
                </h5>
                <p class="text-muted small mb-0">Directly process official MCA workbooks located in the server root folder without re-uploading.</p>
            </div>
            <form method="POST" action="upload_companies.php" class="mt-2 mt-md-0">
                <button type="submit" name="import_server_all_struck" class="btn btn-warning font-weight-bold text-dark shadow-sm" style="border-radius: var(--radius-sm); font-size: 0.88rem;" onclick="return confirm('Process both Company and LLP Struck Off reports now?');">
                    <i class="fas fa-sync-alt mr-1"></i> Process ALL Struck Off Reports (1-Click)
                </button>
            </form>
        </div>

        <div class="row pt-2">
            <!-- Company Struck Off Card -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="p-3 bg-white border rounded shadow-2xs h-100 d-flex flex-column justify-content-between" style="border-radius: 12px !important; border-top: 3px solid #f59e0b !important;">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="font-size: 11px;">
                                <i class="fas fa-building mr-1"></i> Company Strike Off
                            </span>
                            <?php if ($server_company_struck): ?>
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Ready (<?php echo $server_company_struck_size; ?>)</span>
                            <?php else: ?>
                                <span class="badge badge-secondary px-2 py-1">Missing</span>
                            <?php endif; ?>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1" style="font-size: 0.95rem;">Company-Report-20260901.xlsx</h6>
                        <p class="text-muted small mb-3">Contains ~3,797 struck off Indian Private &amp; Public Limited Companies.</p>
                    </div>
                    <form method="POST" action="upload_companies.php">
                        <button type="submit" name="import_server_company_struck" class="btn btn-outline-warning btn-block font-weight-bold text-dark" <?php echo !$server_company_struck ? 'disabled' : ''; ?> style="border-color: #f59e0b;">
                            <i class="fas fa-file-excel mr-1"></i> Update Company Struck Off
                        </button>
                    </form>
                </div>
            </div>

            <!-- LLP Struck Off Card -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="p-3 bg-white border rounded shadow-2xs h-100 d-flex flex-column justify-content-between" style="border-radius: 12px !important; border-top: 3px solid #10b981 !important;">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge badge-success font-weight-bold px-2 py-1" style="font-size: 11px;">
                                <i class="fas fa-handshake mr-1"></i> LLP Strike Off
                            </span>
                            <?php if ($server_llp_struck): ?>
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Ready (<?php echo $server_llp_struck_size; ?>)</span>
                            <?php else: ?>
                                <span class="badge badge-secondary px-2 py-1">Missing</span>
                            <?php endif; ?>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1" style="font-size: 0.95rem;">LLP-Report-20260901.xlsx</h6>
                        <p class="text-muted small mb-3">Contains ~653 struck off Limited Liability Partnerships.</p>
                    </div>
                    <form method="POST" action="upload_companies.php">
                        <button type="submit" name="import_server_llp_struck" class="btn btn-outline-success btn-block font-weight-bold" <?php echo !$server_llp_struck ? 'disabled' : ''; ?>>
                            <i class="fas fa-file-excel mr-1"></i> Update LLP Struck Off
                        </button>
                    </form>
                </div>
            </div>

            <!-- New Incorporations Server Card -->
            <div class="col-lg-4 col-md-12 mb-3">
                <div class="p-3 bg-white border rounded shadow-2xs h-100 d-flex flex-column justify-content-between" style="border-radius: 12px !important; border-top: 3px solid #2563eb !important;">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge badge-primary font-weight-bold px-2 py-1" style="font-size: 11px;">
                                <i class="fas fa-plus-circle mr-1"></i> Incorporations
                            </span>
                            <?php if ($server_incorp): ?>
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Ready (<?php echo $server_incorp_size; ?>)</span>
                            <?php else: ?>
                                <span class="badge badge-secondary px-2 py-1">Missing</span>
                            <?php endif; ?>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1" style="font-size: 0.95rem;">Incorporation-Report-20260901.xlsx</h6>
                        <p class="text-muted small mb-3">Monthly workbook with Indian Companies, LLPs &amp; Foreign entities.</p>
                    </div>
                    <form method="POST" action="upload_companies.php">
                        <button type="submit" name="import_server_file" class="btn btn-outline-primary btn-block font-weight-bold" <?php echo !$server_incorp ? 'disabled' : ''; ?>>
                            <i class="fas fa-cloud-download-alt mr-1"></i> Import New Incorporations
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Upload Console -->
<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="section-card p-4 h-100">
            <h5 class="font-weight-bold mb-3">
                <i class="fas fa-cloud-upload-alt text-primary mr-2"></i> Upload Custom MCA Excel File (.xlsx)
            </h5>
            
            <form action="upload_companies.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_excel">
                
                <!-- Report Type Selector -->
                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-muted mb-2">Select Report Format / Type</label>
                    <div class="row g-2">
                        <div class="col-md-4 col-12 mb-2">
                            <label class="p-3 border rounded d-block cursor-pointer h-100 text-center report-type-card" style="border-radius: 10px; cursor: pointer; transition: all 0.2s; background: #fff;">
                                <input type="radio" name="report_type" value="struck_off" checked class="mr-1">
                                <strong class="d-block text-warning text-dark mt-1" style="font-size: 0.9rem;">
                                    <i class="fas fa-ban mr-1 text-warning"></i> Strike Off Report
                                </strong>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Company / LLP Struck Off</small>
                            </label>
                        </div>
                        <div class="col-md-4 col-12 mb-2">
                            <label class="p-3 border rounded d-block cursor-pointer h-100 text-center report-type-card" style="border-radius: 10px; cursor: pointer; transition: all 0.2s; background: #fff;">
                                <input type="radio" name="report_type" value="incorporation" class="mr-1">
                                <strong class="d-block text-primary mt-1" style="font-size: 0.9rem;">
                                    <i class="fas fa-building mr-1"></i> Incorporation
                                </strong>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">New Companies / LLPs</small>
                            </label>
                        </div>
                        <div class="col-md-4 col-12 mb-2">
                            <label class="p-3 border rounded d-block cursor-pointer h-100 text-center report-type-card" style="border-radius: 10px; cursor: pointer; transition: all 0.2s; background: #fff;">
                                <input type="radio" name="report_type" value="auto" class="mr-1">
                                <strong class="d-block text-info mt-1" style="font-size: 0.9rem;">
                                    <i class="fas fa-magic mr-1"></i> Auto-Detect
                                </strong>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Detect from file content</small>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="custom-dropzone mb-3" onclick="document.getElementById('excelFileInput').click();">
                    <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                    <h6 class="font-weight-bold text-dark mb-1" id="dropzoneTitle">Click or Drag MCA Excel File Here</h6>
                    <p class="text-muted small mb-0" id="dropzoneSubtitle">Accepts standard Ministry of Corporate Affairs .xlsx reports</p>
                    <input type="file" name="excel_file" id="excelFileInput" accept=".xlsx" style="display: none;" onchange="handleFileSelected(this);">
                </div>

                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-muted mb-1">
                        Report Month Override <span class="font-weight-normal text-muted">(Optional - e.g. "Sep 2026" or "2026-09")</span>
                    </label>
                    <input type="text" name="report_month" class="form-control" placeholder="Auto-detected from filename or report date if left blank">
                </div>

                <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold shadow-sm" style="font-size: 0.95rem; background: var(--primary-gradient); border: none;">
                    <i class="fas fa-file-import mr-2"></i> Parse &amp; Process MCA Records
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-5 mb-4">
        <div class="section-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="font-weight-bold mb-3">
                    <i class="fas fa-info-circle text-info mr-2"></i> MCA Import Features
                </h5>
                <ul class="list-unstyled mb-4" style="font-size: 0.88rem; line-height: 1.8;">
                    <li class="mb-2.5 d-flex align-items-start">
                        <i class="fas fa-check-circle text-success mr-2 mt-1"></i>
                        <span><strong>Strike Off Updates:</strong> Automatically sets matching CINs/LLPINs status to <code class="text-warning font-weight-bold" style="background:#fffbeb; color:#b45309; padding:1px 4px; border-radius:4px;">STRUCK_OFF</code> and adds new dissolved entities.</span>
                    </li>
                    <li class="mb-2.5 d-flex align-items-start">
                        <i class="fas fa-check-circle text-success mr-2 mt-1"></i>
                        <span><strong>Multi-Sheet Incorporations:</strong> Reads separate sheets for <strong>Indian Companies</strong>, <strong>LLPs</strong>, and <strong>Foreign Companies</strong>.</span>
                    </li>
                    <li class="mb-2.5 d-flex align-items-start">
                        <i class="fas fa-check-circle text-success mr-2 mt-1"></i>
                        <span><strong>High Speed XML Streaming:</strong> Directly parses compressed OpenXML structures for rapid processing of thousands of records without memory spikes.</span>
                    </li>
                    <li class="mb-2.5 d-flex align-items-start">
                        <i class="fas fa-check-circle text-success mr-2 mt-1"></i>
                        <span><strong>Non-Destructive Upsert:</strong> Preserves existing registration dates, states, and activity categories during strike off synchronization.</span>
                    </li>
                </ul>
            </div>

            <div class="p-3 bg-light rounded">
                <div class="small font-weight-bold text-dark mb-1">Need fresh MCA monthly workbooks?</div>
                <div class="small text-muted mb-2">Download recent monthly incorporation and struck off lists directly from the MCA portal.</div>
                <a href="https://www.mca.gov.in/content/mca/global/en/data-and-reports/company-llp-info/incorporated-closed-month.html" target="_blank" class="btn btn-sm btn-outline-primary btn-block font-weight-bold">
                    <i class="fas fa-external-link-alt mr-1"></i> Open MCA Portal Downloads
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function handleFileSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('dropzoneTitle').innerText = file.name;
        document.getElementById('dropzoneSubtitle').innerText = (file.size / (1024 * 1024)).toFixed(2) + ' MB • Ready to import';

        const nameLower = file.name.toLowerCase();
        if (nameLower.includes('struck') || nameLower.includes('strike') || nameLower.includes('company-report') || nameLower.includes('llp-report')) {
            const struckRadio = document.querySelector('input[name="report_type"][value="struck_off"]');
            if (struckRadio) struckRadio.checked = true;
        } else if (nameLower.includes('incorporation')) {
            const incorpRadio = document.querySelector('input[name="report_type"][value="incorporation"]');
            if (incorpRadio) incorpRadio.checked = true;
        }
    }
}
</script>

<?php require_once('includes/footer.php'); ?>
