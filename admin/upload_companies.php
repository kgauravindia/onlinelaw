<?php
require_once('includes/header.php');
require_once('../includes/company_importer.php');

$import_result = null;
$error_msg = null;

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_excel') {
    if (!empty($_FILES['excel_file']['name'])) {
        $fileName = $_FILES['excel_file']['name'];
        $fileTmp = $_FILES['excel_file']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExt !== 'xlsx') {
            $error_msg = "Please upload a valid Microsoft Excel file (.xlsx format only).";
        } else {
            $forcedMonth = !empty($_POST['report_month']) ? trim($_POST['report_month']) : null;
            $uploadDir = '../uploads/reports/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $targetPath = $uploadDir . time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
            if (move_uploaded_file($fileTmp, $targetPath)) {
                $import_result = import_mca_incorporation_excel($targetPath, $con, $forcedMonth);
            } else {
                $error_msg = "Failed to save uploaded file on server.";
            }
        }
    } else {
        $error_msg = "Please select an Excel file to upload.";
    }
}

// Handle quick import of existing server file
if (isset($_POST['import_server_file'])) {
    $serverFile = '../Incorporation-Report-20260901.xlsx';
    if (file_exists($serverFile)) {
        $import_result = import_mca_incorporation_excel($serverFile, $con);
    } else {
        $error_msg = "Server file 'Incorporation-Report-20260901.xlsx' not found.";
    }
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
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Upload MCA Company Excel</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Upload monthly MCA incorporation workbooks containing Indian Companies, LLPs, and Foreign Companies.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <a href="manage_companies.php" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-list mr-1"></i> View Registered Companies
        </a>
    </div>
</div>

<!-- Live Database Stats Row -->
<div class="row mb-4">
    <div class="col-md-3 col-6 mb-3 mb-md-0">
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
    <div class="col-md-3 col-6 mb-3 mb-md-0">
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
    <div class="col-md-3 col-6 mb-3 mb-md-0">
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
    <div class="col-md-3 col-6 mb-3 mb-md-0">
        <div class="stat-card-v2" style="border-top: 4px solid #f59e0b;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label mb-1">Foreign Companies</div>
                    <div class="stat-value text-warning"><?php echo number_format($stats['FOREIGN']); ?></div>
                </div>
                <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                    <i class="fas fa-globe"></i>
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
    <div class="section-card p-4 mb-4 border-success" style="border-left: 5px solid #10b981;">
        <div class="d-flex align-items-center mb-3">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-right: 12px;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <h5 class="font-weight-bold mb-0 text-success">Batch Import Completed Successfully</h5>
                <p class="text-muted small mb-0">The workbook was parsed and upserted into the MCA database.</p>
            </div>
        </div>

        <div class="row pt-2">
            <div class="col-md-3 col-6 mb-2">
                <div class="p-3 bg-light rounded text-center">
                    <div class="small text-muted text-uppercase font-weight-bold">Total Processed</div>
                    <div class="h4 font-weight-bold text-dark mb-0"><?php echo number_format($import_result['total_processed'] ?? 0); ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="p-3 bg-light rounded text-center">
                    <div class="small text-muted text-uppercase font-weight-bold">Indian Companies</div>
                    <div class="h4 font-weight-bold text-primary mb-0"><?php echo number_format($import_result['indian_companies'] ?? 0); ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="p-3 bg-light rounded text-center">
                    <div class="small text-muted text-uppercase font-weight-bold">LLPs Added</div>
                    <div class="h4 font-weight-bold text-success mb-0"><?php echo number_format($import_result['llp_companies'] ?? 0); ?></div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="p-3 bg-light rounded text-center">
                    <div class="small text-muted text-uppercase font-weight-bold">Foreign Entities</div>
                    <div class="h4 font-weight-bold text-warning mb-0"><?php echo number_format($import_result['foreign_companies'] ?? 0); ?></div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="manage_companies.php" class="btn btn-sm btn-primary font-weight-bold px-3">
                <i class="fas fa-arrow-right mr-1"></i> View Imported Records
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Main Upload Console -->
<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="section-card p-4 h-100">
            <h5 class="font-weight-bold mb-3">
                <i class="fas fa-cloud-upload-alt text-primary mr-2"></i> Upload New Excel File (.xlsx)
            </h5>
            
            <form action="upload_companies.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_excel">
                
                <div class="custom-dropzone mb-3" onclick="document.getElementById('excelFileInput').click();">
                    <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                    <h6 class="font-weight-bold text-dark mb-1" id="dropzoneTitle">Click or Drag MCA Excel File Here</h6>
                    <p class="text-muted small mb-0" id="dropzoneSubtitle">Accepts standard Ministry of Corporate Affairs .xlsx reports</p>
                    <input type="file" name="excel_file" id="excelFileInput" accept=".xlsx" style="display: none;" onchange="handleFileSelected(this);">
                </div>

                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-muted mb-1">
                        Report Month Override <span class="font-weight-normal text-muted">(Optional - e.g. "Sep 2026")</span>
                    </label>
                    <input type="text" name="report_month" class="form-control" placeholder="Auto-detected from filename if left blank">
                </div>

                <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold shadow-sm" style="font-size: 0.95rem; background: var(--primary-gradient); border: none;">
                    <i class="fas fa-file-import mr-2"></i> Parse &amp; Import Company Records
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-5 mb-4">
        <div class="section-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="font-weight-bold mb-3">
                    <i class="fas fa-info-circle text-info mr-2"></i> Import Guidelines
                </h5>
                <ul class="list-unstyled mb-4" style="font-size: 0.88rem; line-height: 1.8;">
                    <li class="mb-2 d-flex align-items-start">
                        <i class="fas fa-check-circle text-success mr-2 mt-1"></i>
                        <span>Excel file should contain separate sheets for <strong>Indian Companies</strong>, <strong>LLPs</strong>, and <strong>Foreign Companies</strong>.</span>
                    </li>
                    <li class="mb-2 d-flex align-items-start">
                        <i class="fas fa-check-circle text-success mr-2 mt-1"></i>
                        <span>Existing CIN/LLPIN duplicates are automatically updated (upsert mode).</span>
                    </li>
                    <li class="mb-2 d-flex align-items-start">
                        <i class="fas fa-check-circle text-success mr-2 mt-1"></i>
                        <span>Registration dates and states are normalized for seamless searching.</span>
                    </li>
                </ul>
            </div>

            <div class="p-3 bg-light rounded">
                <div class="small font-weight-bold text-dark mb-1">Need official MCA reports?</div>
                <div class="small text-muted mb-2">Download recent monthly workbooks directly from the MCA portal.</div>
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
    }
}
</script>

<?php require_once('includes/footer.php'); ?>
