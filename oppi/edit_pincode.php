<?php
require_once('includes/header.php');

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid Record ID</div>";
    require_once('includes/footer.php');
    exit;
}

$id = intval($_GET['id']);
$record_data = get_data('pincode', $id);

if ($record_data['count'] == 0) {
    echo "<div class='alert alert-danger'>Record not found</div>";
    require_once('includes/footer.php');
    exit;
}

$r = $record_data['data'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_data = array(
        'pincode' => intval($_POST['pincode']),
        'locality_id' => intval($_POST['locality_id'] ?? 0),
        'locality_name' => trim($_POST['locality_name']),
        'office_name' => trim($_POST['office_name']),
        'sub_district_name' => trim($_POST['sub_district_name']),
        'district_name' => trim($_POST['district_name']),
        'state_name' => trim($_POST['state_name']),
        'status' => trim($_POST['status'])
    );

    $res = update_data('pincode', $update_data, $id);
    if ($res['status'] == 'success') {
        $success = "PIN code record updated successfully!";
        $r = get_data('pincode', $id)['data'];
    } else {
        $error = "Failed to update record: " . $res['msg'];
    }
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Edit Postal PIN Code</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Update postal index number, post office, district, and administrative locality.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <a href="manage_pincode.php" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-arrow-left mr-1"></i> Back to PIN Codes
        </a>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center mb-4" style="border-radius: var(--radius-md); gap: 10px;">
        <i class="fas fa-exclamation-circle fa-lg"></i>
        <div><?php echo htmlspecialchars($error); ?></div>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success shadow-sm border-0 d-flex align-items-center mb-4" style="border-radius: var(--radius-md); gap: 10px;">
        <i class="fas fa-check-circle fa-lg"></i>
        <div><?php echo htmlspecialchars($success); ?></div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="section-card p-4">
            <form method="post" action="">
                <div class="row">
                    <div class="col-md-4 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">6-Digit PIN Code *</label>
                        <input type="number" name="pincode" class="form-control font-monospace font-weight-bold" value="<?php echo htmlspecialchars($r['pincode']); ?>" required>
                    </div>

                    <div class="col-md-8 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Locality Name *</label>
                        <input type="text" name="locality_name" class="form-control" value="<?php echo htmlspecialchars($r['locality_name']); ?>" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Post Office Name</label>
                        <input type="text" name="office_name" class="form-control" value="<?php echo htmlspecialchars($r['office_name']); ?>">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Sub District / Taluka</label>
                        <input type="text" name="sub_district_name" class="form-control" value="<?php echo htmlspecialchars($r['sub_district_name'] ?? ''); ?>">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">District Name *</label>
                        <input type="text" name="district_name" class="form-control" value="<?php echo htmlspecialchars($r['district_name']); ?>" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">State Name *</label>
                        <input type="text" name="state_name" class="form-control" value="<?php echo htmlspecialchars($r['state_name']); ?>" required>
                    </div>

                    <div class="col-md-6 form-group mb-4">
                        <label class="small font-weight-bold text-muted mb-1">Status</label>
                        <select name="status" class="form-control">
                            <option value="ACTIVE" <?php echo ($r['status'] == 'ACTIVE' || empty($r['status'])) ? 'selected' : ''; ?>>ACTIVE</option>
                            <option value="INACTIVE" <?php echo ($r['status'] == 'INACTIVE') ? 'selected' : ''; ?>>INACTIVE</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2" style="gap: 10px;">
                    <a href="manage_pincode.php" class="btn btn-light px-4 font-weight-bold">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="background: var(--primary-gradient); border: none;">
                        <i class="fas fa-save mr-1"></i> Update PIN Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="section-card p-4">
            <h5 class="font-weight-bold mb-3">
                <i class="fas fa-map-pin text-success mr-2"></i> Postal Index Details
            </h5>
            <div class="small text-muted" style="line-height: 1.7;">
                <p>PIN codes in India are 6-digit codes:</p>
                <ul class="pl-3 mb-2">
                    <li>1st digit indicates the postal zone</li>
                    <li>2nd digit indicates the sub-zone</li>
                    <li>3rd digit indicates the sorting district</li>
                    <li>Last 3 digits represent the specific delivery post office</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
