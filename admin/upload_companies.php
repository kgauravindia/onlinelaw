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
        <h2 class="page-title mb-1">Upload Company Incorporation Excel</h2>
        <p class="text-muted mb-0">Upload monthly MCA incorporation report containing Indian Companies, LLPs, and Foreign Companies.</p>
    </div>
    <div class="mt-2 mt-md-0">
        <a href="manage_companies.php" class="btn btn-outline-primary mr-2"><i class="fas fa-list mr-1"></i> Manage Companies</a>
        <a href="manage_data.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Datasets</a>
    </div>
</div>

<!-- Official MCA Source Link Card -->
<div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #ffffff; border-radius: 12px;">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-7">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge badge-light text-primary font-weight-bold mr-2 px-3 py-1 text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Official MCA Portal</span>
                    <span style="opacity: 0.9; font-size: 13px;"><i class="fas fa-check-circle mr-1"></i> Source of Monthly Incorporation Excel Data</span>
                </div>
                <h4 class="font-weight-bold text-white mb-2">Download Latest Monthly Incorporation Reports</h4>
                <p class="mb-0" style="opacity: 0.9; font-size: 14px; line-height: 1.5;">
                    The Ministry of Corporate Affairs (MCA) releases monthly incorporation reports containing newly registered Indian Companies, LLPs, and Foreign Companies for each day of the month.
                </p>
            </div>
            <div class="col-lg-4 col-md-5 text-md-right mt-3 mt-md-0">
                <a href="https://www.mca.gov.in/content/mca/global/en/data-and-reports/company-llp-info/incorporated-closed-month.html" target="_blank" rel="noopener noreferrer" class="btn btn-light btn-lg font-weight-bold shadow-sm" style="color: #1e3a8a; border-radius: 8px;">
                    <i class="fas fa-external-link-alt mr-2 text-primary"></i> Visit MCA Portal
                </a>
            </div>
        </div>
    </div>
</div>

<?php if ($import_result): ?>
    <div class="alert alert-<?php echo ($import_result['status'] === 'success') ? 'success' : 'warning'; ?> shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="d-flex align-items-start">
            <div class="mr-3 mt-1">
                <i class="fas <?php echo ($import_result['status'] === 'success') ? 'fa-check-circle fa-2x text-success' : 'fa-exclamation-circle fa-2x text-warning'; ?>"></i>
            </div>
            <div>
                <h5 class="font-weight-bold mb-1"><?php echo ($import_result['status'] === 'success') ? 'Import Successful!' : 'Import Finished with Warnings'; ?></h5>
                <p class="mb-2"><?php echo htmlspecialchars($import_result['message']); ?></p>
                <div class="d-flex flex-wrap" style="gap: 15px;">
                    <span class="badge badge-pill badge-primary px-3 py-2">Indian Companies: <?php echo number_format($import_result['INDIAN']); ?></span>
                    <span class="badge badge-pill badge-success px-3 py-2">LLP Companies: <?php echo number_format($import_result['LLP']); ?></span>
                    <span class="badge badge-pill badge-warning px-3 py-2">Foreign Companies: <?php echo number_format($import_result['FOREIGN']); ?></span>
                    <span class="badge badge-pill badge-info px-3 py-2">Time Taken: <?php echo $import_result['duration']; ?>s</span>
                </div>
                <?php if (!empty($import_result['errors'])): ?>
                    <div class="mt-3">
                        <small class="text-danger font-weight-bold">Errors encountered:</small>
                        <ul class="text-danger mb-0 pl-3">
                            <?php foreach ($import_result['errors'] as $err): ?>
                                <li><small><?php echo htmlspecialchars($err); ?></small></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($error_msg): ?>
    <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <i class="fas fa-exclamation-triangle mr-2"></i> <?php echo htmlspecialchars($error_msg); ?>
    </div>
<?php endif; ?>

<!-- Stats Overview Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #3b82f6 !important; border-radius: 10px;">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase font-weight-bold">Total Companies</div>
                <h3 class="font-weight-bold text-dark mb-0 mt-1"><?php echo number_format($stats['TOTAL']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #0284c7 !important; border-radius: 10px;">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase font-weight-bold">Indian Companies</div>
                <h3 class="font-weight-bold text-primary mb-0 mt-1"><?php echo number_format($stats['INDIAN']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #10b981 !important; border-radius: 10px;">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase font-weight-bold">LLP Companies</div>
                <h3 class="font-weight-bold text-success mb-0 mt-1"><?php echo number_format($stats['LLP']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #f59e0b !important; border-radius: 10px;">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase font-weight-bold">Foreign Companies</div>
                <h3 class="font-weight-bold text-warning mb-0 mt-1"><?php echo number_format($stats['FOREIGN']); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Upload Form -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="font-weight-bold mb-0 text-dark"><i class="fas fa-file-excel text-success mr-2"></i> Upload Monthly Excel Report</h5>
            </div>
            <div class="card-body p-4">
                <form action="upload_companies.php" method="POST" enctype="multipart/form-data" id="uploadForm">
                    <input type="hidden" name="action" value="upload_excel">
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark">Select Monthly .xlsx File <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="excelFile" name="excel_file" accept=".xlsx" required>
                            <label class="custom-file-label" for="excelFile" id="fileLabel">Choose .xlsx file...</label>
                        </div>
                        <small class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle mr-1"></i> Excel file downloaded from MCA containing sheets: <strong>Indian Companies</strong>, <strong>LLP Companies</strong>, and <strong>Foreign Companies</strong>.
                        </small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark">Report Month (Optional)</label>
                        <input type="month" class="form-control" name="report_month" value="<?php echo date('Y-m'); ?>">
                        <small class="form-text text-muted">If left blank or auto-detected, dates inside the sheet will automatically determine the registration month.</small>
                    </div>

                    <div class="p-3 bg-light rounded mb-4" style="border: 1px dashed #cbd5e1;">
                        <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-bolt text-warning mr-1"></i> Fast High-Speed Batch Processing</h6>
                        <p class="small text-muted mb-0">Our streaming XML parser can process 30,000+ records in ~3 seconds with automatic deduplication (existing records updated, new records inserted).</p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bold shadow-sm" id="submitBtn">
                        <i class="fas fa-cloud-upload-alt mr-2"></i> Upload and Import Companies
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Instructions & Quick Tools -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="font-weight-bold mb-0 text-dark"><i class="fas fa-question-circle text-primary mr-2"></i> How to Get the Monthly Report</h5>
            </div>
            <div class="card-body p-4">
                <ol class="pl-3 mb-0" style="font-size: 14px; line-height: 1.8;">
                    <li>Open the official <a href="https://www.mca.gov.in/content/mca/global/en/data-and-reports/company-llp-info/incorporated-closed-month.html" target="_blank" class="font-weight-bold">MCA Monthly Reports Page <i class="fas fa-external-link-alt fa-xs"></i></a>.</li>
                    <li>Click on the desired month's report under <strong>"Company/LLP Incorporated during the Month"</strong>.</li>
                    <li>Download the <code>.xlsx</code> file (e.g. <code>Incorporation-Report-YYYYMMDD.xlsx</code>).</li>
                    <li>Upload it using the form on the left. All 3 categories will be parsed and indexed instantly.</li>
                </ol>
            </div>
        </div>

        <?php if (file_exists('../Incorporation-Report-20260901.xlsx')): ?>
        <div class="card shadow-sm border-0" style="border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-server text-info mr-2"></i>
                    <h6 class="font-weight-bold mb-0 text-dark">Server File Detected</h6>
                </div>
                <p class="small text-muted mb-3"><code>Incorporation-Report-20260901.xlsx</code> is present in the workspace root directory.</p>
                <form action="upload_companies.php" method="POST">
                    <input type="hidden" name="import_server_file" value="1">
                    <button type="submit" class="btn btn-outline-secondary btn-block font-weight-bold">
                        <i class="fas fa-sync mr-1"></i> Re-import Workspace Sample File
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('excelFile').addEventListener('change', function(e) {
    var fileName = e.target.files[0] ? e.target.files[0].name : 'Choose .xlsx file...';
    document.getElementById('fileLabel').textContent = fileName;
});

document.getElementById('uploadForm').addEventListener('submit', function() {
    var btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Importing Companies (Please wait)...';
});
</script>

<?php require_once('includes/footer.php'); ?>
