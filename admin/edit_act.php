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
$record_data = get_data('acts', $id);

if ($record_data['count'] == 0) {
    echo "<div class='alert alert-danger'>Record not found</div>";
    require_once('includes/footer.php');
    exit;
}

$r = $record_data['data'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_data = array(
        'name' => trim($_POST['name']),
        'year' => intval($_POST['year']),
        'act_no' => trim($_POST['act_no']),
        'act_type' => trim($_POST['act_type']),
        'remarks' => trim($_POST['remarks']),
        'status' => trim($_POST['status'])
    );

    $res = update_data('acts', $update_data, $id);
    if ($res['status'] == 'success') {
        $success = "Bare act record updated successfully!";
        $r = get_data('acts', $id)['data'];
    } else {
        $error = "Failed to update record: " . $res['msg'];
    }
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Edit Bare Act Record</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Update statute name, year of enactment, act number, and category details.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <a href="manage_acts.php" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-arrow-left mr-1"></i> Back to Bare Acts
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
                    <div class="col-12 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Act Title / Legal Name *</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($r['name']); ?>" required>
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Year of Enactment *</label>
                        <input type="number" name="year" class="form-control font-monospace" value="<?php echo htmlspecialchars($r['year']); ?>" required>
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Act Number</label>
                        <input type="text" name="act_no" class="form-control font-monospace" value="<?php echo htmlspecialchars($r['act_no'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Act Type</label>
                        <select name="act_type" class="form-control">
                            <option value="Central" <?php echo ($r['act_type'] == 'Central') ? 'selected' : ''; ?>>Central Act</option>
                            <option value="State" <?php echo ($r['act_type'] == 'State') ? 'selected' : ''; ?>>State Act</option>
                            <option value="Rules" <?php echo ($r['act_type'] == 'Rules') ? 'selected' : ''; ?>>Rules &amp; Regulations</option>
                        </select>
                    </div>

                    <div class="col-12 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Description / Summary Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3"><?php echo htmlspecialchars($r['remarks'] ?? ''); ?></textarea>
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
                    <a href="manage_acts.php" class="btn btn-light px-4 font-weight-bold">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="background: var(--primary-gradient); border: none;">
                        <i class="fas fa-save mr-1"></i> Update Act Details
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="section-card p-4">
            <h5 class="font-weight-bold mb-3">
                <i class="fas fa-book text-warning mr-2"></i> Indian Bare Acts
            </h5>
            <div class="small text-muted" style="line-height: 1.7;">
                <p>Bare Acts contain the pure, unannotated legislative text passed by Parliament or State Legislative Assemblies.</p>
                <p>Changes made here reflect immediately in the public bare acts directory searches.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
