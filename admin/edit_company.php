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
            if ($insert_res['status'] === 'success') {
                echo "<script>alert('New company added successfully!'); window.location.href='manage_companies.php';</script>";
                exit();
            } else {
                $msg = "Failed to add company: " . $insert_res['msg'];
                $msg_type = "danger";
            }
        }
    }
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">
            <?php echo $is_edit ? 'Edit Company Record' : 'Add New Company'; ?>
        </h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">
            <?php echo $is_edit ? 'Update MCA corporate registration details.' : 'Register a new corporate entity in the local directory.'; ?>
        </p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <a href="manage_companies.php" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-arrow-left mr-1"></i> Back to Companies
        </a>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-<?php echo $msg_type; ?> shadow-sm border-0 d-flex align-items-center mb-4" style="border-radius: var(--radius-md); gap: 10px;">
        <i class="fas fa-<?php echo $msg_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?> fa-lg"></i>
        <div><?php echo htmlspecialchars($msg); ?></div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="section-card p-4">
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Company Category *</label>
                        <select name="category" class="form-control" required>
                            <option value="INDIAN" <?php echo ($company['category'] === 'INDIAN') ? 'selected' : ''; ?>>Indian Company</option>
                            <option value="LLP" <?php echo ($company['category'] === 'LLP') ? 'selected' : ''; ?>>Limited Liability Partnership (LLP)</option>
                            <option value="FOREIGN" <?php echo ($company['category'] === 'FOREIGN') ? 'selected' : ''; ?>>Foreign Company</option>
                        </select>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">CIN / LLPIN / FCRN *</label>
                        <input type="text" name="cin" class="form-control font-monospace" placeholder="e.g. U72900DL2026PTC123456" value="<?php echo htmlspecialchars($company['cin']); ?>" required>
                    </div>

                    <div class="col-12 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Company Full Legal Name *</label>
                        <input type="text" name="company_name" class="form-control" placeholder="e.g. ACME TECH PRIVATE LIMITED" value="<?php echo htmlspecialchars($company['company_name']); ?>" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Class</label>
                        <input type="text" name="class" class="form-control" placeholder="Private / Public" value="<?php echo htmlspecialchars($company['class'] ?? ''); ?>">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Company Type</label>
                        <input type="text" name="company_type" class="form-control" placeholder="Non-government company" value="<?php echo htmlspecialchars($company['company_type'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">NIC Activity Code</label>
                        <input type="text" name="activity_code" class="form-control" placeholder="e.g. 6201" value="<?php echo htmlspecialchars($company['activity_code'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Registration Date</label>
                        <input type="date" name="registration_date" class="form-control" value="<?php echo htmlspecialchars($company['registration_date'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4 form-group mb-4">
                        <label class="small font-weight-bold text-muted mb-1">Report Month</label>
                        <input type="text" name="report_month" class="form-control" placeholder="e.g. Sep 2026" value="<?php echo htmlspecialchars($company['report_month'] ?? ''); ?>">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2" style="gap: 10px;">
                    <a href="manage_companies.php" class="btn btn-light px-4 font-weight-bold">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="background: var(--primary-gradient); border: none;">
                        <i class="fas fa-save mr-1"></i> <?php echo $is_edit ? 'Save Changes' : 'Create Company'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="section-card p-4">
            <h5 class="font-weight-bold mb-3">
                <i class="fas fa-shield-alt text-primary mr-2"></i> Entity Guidelines
            </h5>
            <div class="small text-muted" style="line-height: 1.7;">
                <p>Ensure the 21-digit <strong>CIN</strong> (Corporate Identity Number) or 7/8-digit <strong>LLPIN</strong> is entered exactly as registered with the Ministry of Corporate Affairs.</p>
                <p>Active registration updates immediately sync with the public company search pages.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
