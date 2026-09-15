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
$record_data = get_data('pincode', $id);

if($record_data['count'] == 0) {
    echo "<div class='alert alert-danger'>Record not found</div>";
    require_once('includes/footer.php');
    exit;
}

$r = $record_data['data'];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_data = array(
        'pincode' => intval($_POST['pincode']),
        'locality_id' => intval($_POST['locality_id']),
        'locality_name' => trim($_POST['locality_name']),
        'office_name' => trim($_POST['office_name']),
        'sub_district_name' => trim($_POST['sub_district_name']),
        'district_name' => trim($_POST['district_name']),
        'state_name' => trim($_POST['state_name']),
        'status' => trim($_POST['status'])
    );

    $res = update_data('pincode', $update_data, $id);
    if($res['status'] == 'success') {
        $success = "Record updated successfully!";
        $r = get_data('pincode', $id)['data'];
    } else {
        $error = "Failed to update record: " . $res['msg'];
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">Edit PIN Code</h2>
    <a href="manage_pincode.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
        
        <form method="post" action="">
            <div class="row">
                <div class="col-md-4 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">PIN Code</label>
                    <input type="number" name="pincode" class="form-control" value="<?php echo htmlspecialchars($r['pincode']); ?>" required>
                </div>
                <div class="col-md-4 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Locality ID</label>
                    <input type="number" name="locality_id" class="form-control" value="<?php echo htmlspecialchars($r['locality_id']); ?>" required>
                </div>
                <div class="col-md-4 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Status</label>
                    <input type="text" name="status" class="form-control" value="<?php echo htmlspecialchars($r['status']); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Locality Name</label>
                    <input type="text" name="locality_name" class="form-control" value="<?php echo htmlspecialchars($r['locality_name']); ?>" required>
                </div>
                <div class="col-md-6 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Office Name</label>
                    <input type="text" name="office_name" class="form-control" value="<?php echo htmlspecialchars($r['office_name']); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Sub District</label>
                    <input type="text" name="sub_district_name" class="form-control" value="<?php echo htmlspecialchars($r['sub_district_name']); ?>" required>
                </div>
                <div class="col-md-4 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">District</label>
                    <input type="text" name="district_name" class="form-control" value="<?php echo htmlspecialchars($r['district_name']); ?>" required>
                </div>
                <div class="col-md-4 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">State</label>
                    <input type="text" name="state_name" class="form-control" value="<?php echo htmlspecialchars($r['state_name']); ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary px-4 py-2 mt-2"><i class="fas fa-save mr-2"></i> Save Changes</button>
        </form>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
