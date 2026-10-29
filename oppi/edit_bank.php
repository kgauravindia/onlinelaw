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
$record_data = get_data('bank_ifsc', $id);

if ($record_data['count'] == 0) {
    echo "<div class='alert alert-danger'>Record not found</div>";
    require_once('includes/footer.php');
    exit;
}

$r = $record_data['data'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_data = array(
        'bank' => trim($_POST['bank']),
        'ifsc' => trim($_POST['ifsc']),
        'micr' => trim($_POST['micr'] ?? ''),
        'branch' => trim($_POST['branch']),
        'address' => trim($_POST['address']),
        'city1' => trim($_POST['city1']),
        'city2' => trim($_POST['city2']),
        'state' => trim($_POST['state']),
        'stdcode' => trim($_POST['stdcode']),
        'phone' => trim($_POST['phone']),
        'status' => trim($_POST['status']),
        'remarks' => trim($_POST['remarks'])
    );

    $res = update_data('bank_ifsc', $update_data, $id);
    if ($res['status'] == 'success') {
        $success = "Bank IFSC record updated successfully!";
        $r = get_data('bank_ifsc', $id)['data'];
    } else {
        $error = "Failed to update record: " . $res['msg'];
    }
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Edit Bank IFSC Record</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Update bank name, branch address, routing codes, and contact details.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <a href="manage_bank.php" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-arrow-left mr-1"></i> Back to Bank IFSC
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
                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Bank Name *</label>
                        <input type="text" name="bank" class="form-control" value="<?php echo htmlspecialchars($r['bank']); ?>" required>
                    </div>

                    <div class="col-md-3 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">IFSC Code *</label>
                        <input type="text" name="ifsc" class="form-control font-monospace" value="<?php echo htmlspecialchars($r['ifsc']); ?>" required>
                    </div>

                    <div class="col-md-3 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">MICR Code</label>
                        <input type="text" name="micr" class="form-control font-monospace" value="<?php echo htmlspecialchars($r['micr'] ?? ''); ?>">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Branch Name *</label>
                        <input type="text" name="branch" class="form-control" value="<?php echo htmlspecialchars($r['branch']); ?>" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">State *</label>
                        <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($r['state']); ?>" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">City / District</label>
                        <input type="text" name="city1" class="form-control" value="<?php echo htmlspecialchars($r['city1']); ?>">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">City 2 / Region</label>
                        <input type="text" name="city2" class="form-control" value="<?php echo htmlspecialchars($r['city2'] ?? ''); ?>">
                    </div>

                    <div class="col-12 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Branch Address</label>
                        <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($r['address']); ?></textarea>
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">STD Code</label>
                        <input type="text" name="stdcode" class="form-control" value="<?php echo htmlspecialchars($r['stdcode'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($r['phone'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4 form-group mb-4">
                        <label class="small font-weight-bold text-muted mb-1">Status</label>
                        <select name="status" class="form-control">
                            <option value="ACTIVE" <?php echo ($r['status'] == 'ACTIVE' || empty($r['status'])) ? 'selected' : ''; ?>>ACTIVE</option>
                            <option value="INACTIVE" <?php echo ($r['status'] == 'INACTIVE') ? 'selected' : ''; ?>>INACTIVE</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2" style="gap: 10px;">
                    <a href="manage_bank.php" class="btn btn-light px-4 font-weight-bold">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="background: var(--primary-gradient); border: none;">
                        <i class="fas fa-save mr-1"></i> Update Bank Details
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="section-card p-4">
            <h5 class="font-weight-bold mb-3">
                <i class="fas fa-info-circle text-primary mr-2"></i> IFSC Verification
            </h5>
            <div class="small text-muted" style="line-height: 1.7;">
                <p>An Indian Financial System Code (IFSC) is an 11-character alphanumeric code assigned by RBI to identify bank branches participating in NEFT, RTGS, and IMPS.</p>
                <p>The first 4 characters represent the bank code, the 5th character is '0', and the last 6 characters represent the specific branch.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
