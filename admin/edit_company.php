<?php
require_once('includes/header.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$company = [
    'category' => 'INDIAN',
    'cin' => '',
    'company_name' => '',
    'class' => 'Private',
    'company_type' => 'Non-government company',
    'activity_code' => '',
    'registration_date' => date('Y-m-d'),
    'report_month' => date('Y-m'),
    'status' => 'ACTIVE'
];

$is_edit = false;
$msg = '';
$msg_type = '';

if ($id > 0) {
    $res = direct_sql("SELECT * FROM companies_incorporated WHERE id = $id LIMIT 1");
    if ($res['count'] > 0) {
        $company = $res['data'][0];
        $is_edit = true;
    } else {
        echo "<script>alert('Company not found'); window.location.href='manage_companies.php';</script>";
        exit();
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = in_array($_POST['category'], ['INDIAN', 'LLP', 'FOREIGN']) ? $_POST['category'] : 'INDIAN';
    $cin = trim($_POST['cin']);
    $company_name = trim($_POST['company_name']);
    $class = trim($_POST['class']);
    $company_type = trim($_POST['company_type']);
    $activity_code = trim($_POST['activity_code']);
    $registration_date = !empty($_POST['registration_date']) ? $_POST['registration_date'] : null;
    $report_month = !empty($_POST['report_month']) ? trim($_POST['report_month']) : ($registration_date ? date('Y-m', strtotime($registration_date)) : date('Y-m'));
    $status = !empty($_POST['status']) ? trim($_POST['status']) : 'ACTIVE';

    if (empty($cin) || empty($company_name)) {
        $msg = "CIN/LLPIN and Company Name are required fields.";
        $msg_type = "danger";
    } else {
        $dbData = [
            'category' => $category,
            'cin' => $cin,
            'company_name' => $company_name,
            'class' => $class,
            'company_type' => $company_type,
            'activity_code' => $activity_code,
            'registration_date' => $registration_date,
            'report_month' => $report_month,
            'status' => $status
        ];

        if ($is_edit) {
            $update_res = update_data('companies_incorporated', $dbData, $id);
            if ($update_res['status'] === 'success') {
                $msg = "Company record updated successfully!";
                $msg_type = "success";
                $company = array_merge($company, $dbData);
            } else {
                $msg = "Failed to update company: " . $update_res['msg'];
                $msg_type = "danger";
            }
        } else {
            $insert_res = insert_data('companies_incorporated', $dbData);
            if ($insert_res['status'] === 'success' && $insert_res['id'] > 0) {
                echo "<script>alert('New company added successfully!'); window.location.href='manage_companies.php';</script>";
                exit();
            } else {
                $msg = "Failed to add company (CIN may already exist): " . $insert_res['msg'];
                $msg_type = "danger";
            }
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="page-title mb-1"><?php echo $is_edit ? 'Edit Company Record' : 'Add New Company'; ?></h2>
        <p class="text-muted mb-0"><?php echo $is_edit ? 'Update details for ' . htmlspecialchars($company['company_name']) : 'Enter registration details for the new company or LLP.'; ?></p>
    </div>
    <div>
        <a href="manage_companies.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Companies</a>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-<?php echo $msg_type; ?> shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <i class="fas <?php echo ($msg_type === 'success') ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> mr-2"></i>
        <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="card-body p-4">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-dark">Company Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-control" required id="categorySelect">
                        <option value="INDIAN" <?php echo ($company['category'] === 'INDIAN') ? 'selected' : ''; ?>>Indian Company</option>
                        <option value="LLP" <?php echo ($company['category'] === 'LLP') ? 'selected' : ''; ?>>Limited Liability Partnership (LLP)</option>
                        <option value="FOREIGN" <?php echo ($company['category'] === 'FOREIGN') ? 'selected' : ''; ?>>Foreign Company</option>
                    </select>
                </div>

                <div class="col-md-8 mb-3">
                    <label class="font-weight-bold text-dark" id="cinLabel">CIN / LLPIN / FCIN <span class="text-danger">*</span></label>
                    <input type="text" name="cin" class="form-control text-uppercase" placeholder="e.g. U88100ME2026NPL476032 or ADA-7895" value="<?php echo htmlspecialchars($company['cin']); ?>" required>
                    <small class="text-muted">Corporate Identification Number or LLP Identification Number</small>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="font-weight-bold text-dark">Company / LLP Name <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control" placeholder="e.g. ABC ENTERPRISES PRIVATE LIMITED" value="<?php echo htmlspecialchars($company['company_name']); ?>" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-dark">Class</label>
                    <input type="text" name="class" class="form-control" placeholder="e.g. Private, Public, LLP" value="<?php echo htmlspecialchars($company['class']); ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-dark">Company Type</label>
                    <input type="text" name="company_type" class="form-control" placeholder="e.g. Non-government company" value="<?php echo htmlspecialchars($company['company_type']); ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-dark">Activity Code / Sector</label>
                    <input type="text" name="activity_code" class="form-control" placeholder="e.g. 62, 88, 56" value="<?php echo htmlspecialchars($company['activity_code']); ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-dark">Date of Registration</label>
                    <input type="date" name="registration_date" class="form-control" value="<?php echo htmlspecialchars($company['registration_date'] ?? ''); ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-dark">Report Month</label>
                    <input type="month" name="report_month" class="form-control" value="<?php echo htmlspecialchars($company['report_month'] ?? ''); ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-dark">Status</label>
                    <select name="status" class="form-control">
                        <option value="ACTIVE" <?php echo ($company['status'] === 'ACTIVE') ? 'selected' : ''; ?>>ACTIVE</option>
                        <option value="INACTIVE" <?php echo ($company['status'] === 'INACTIVE') ? 'selected' : ''; ?>>INACTIVE</option>
                    </select>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end" style="gap: 10px;">
                <a href="manage_companies.php" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn btn-primary font-weight-bold px-4">
                    <i class="fas fa-save mr-1"></i> <?php echo $is_edit ? 'Save Changes' : 'Create Company'; ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
