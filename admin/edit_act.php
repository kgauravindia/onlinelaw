<?php
require_once('includes/header.php');

$error = '';
$success = '';

if(!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid Record ID</div>";
    require_once('includes/footer.php');
    exit;
}

$id = intval($_GET['id']);
$record_data = get_data('acts', $id);

if($record_data['count'] == 0) {
    echo "<div class='alert alert-danger'>Record not found</div>";
    require_once('includes/footer.php');
    exit;
}

$r = $record_data['data'];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_data = array(
        'name' => trim($_POST['name']),
        'year' => intval($_POST['year']),
        'act_no' => trim($_POST['act_no']),
        'act_type' => trim($_POST['act_type']),
        'remarks' => trim($_POST['remarks']),
        'status' => trim($_POST['status'])
    );

    $res = update_data('acts', $update_data, $id);
    if($res['status'] == 'success') {
        $success = "Record updated successfully!";
        $r = get_data('acts', $id)['data'];
    } else {
        $error = "Failed to update record: " . $res['msg'];
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">Edit Act</h2>
    <a href="manage_acts.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
        
        <form method="post" action="">
            <div class="row">
                <div class="col-md-8 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Act Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($r['name']); ?>" required>
                </div>
                <div class="col-md-4 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Year</label>
                    <input type="number" name="year" class="form-control" value="<?php echo htmlspecialchars($r['year']); ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Act No.</label>
                    <input type="text" name="act_no" class="form-control" value="<?php echo htmlspecialchars($r['act_no']); ?>">
                </div>
                <div class="col-md-4 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Act Type</label>
                    <input type="text" name="act_type" class="form-control" value="<?php echo htmlspecialchars($r['act_type']); ?>">
                </div>
                <div class="col-md-4 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="ACTIVE" <?php if(strtoupper($r['status']) == 'ACTIVE') echo 'selected'; ?>>ACTIVE</option>
                        <option value="INACTIVE" <?php if(strtoupper($r['status']) == 'INACTIVE') echo 'selected'; ?>>INACTIVE</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="3"><?php echo htmlspecialchars($r['remarks']); ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary px-4 py-2 mt-2"><i class="fas fa-save mr-2"></i> Save Changes</button>
        </form>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
