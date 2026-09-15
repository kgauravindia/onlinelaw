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
$record_data = get_data('bank_ifsc', $id);

if($record_data['count'] == 0) {
    echo "<div class='alert alert-danger'>Record not found</div>";
    require_once('includes/footer.php');
    exit;
}

$r = $record_data['data'];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_data = array(
        'bank' => trim($_POST['bank']),
        'ifsc' => trim($_POST['ifsc']),
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
    if($res['status'] == 'success') {
        $success = "Record updated successfully!";
        $r = get_data('bank_ifsc', $id)['data'];
    } else {
        $error = "Failed to update record: " . $res['msg'];
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">Edit Bank IFSC</h2>
    <a href="manage_bank.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
        
        <form method="post" action="">
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Bank Name</label>
                    <input type="text" name="bank" class="form-control" value="<?php echo htmlspecialchars($r['bank']); ?>" required>
                </div>
                <div class="col-md-6 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">IFSC Code</label>
                    <input type="text" name="ifsc" class="form-control" value="<?php echo htmlspecialchars($r['ifsc']); ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Branch</label>
                    <input type="text" name="branch" class="form-control" value="<?php echo htmlspecialchars($r['branch']); ?>">
                </div>
                <div class="col-md-6 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Address</label>
                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($r['address']); ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">City 1</label>
                    <input type="text" name="city1" class="form-control" value="<?php echo htmlspecialchars($r['city1']); ?>">
                </div>
                <div class="col-md-4 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">City 2</label>
                    <input type="text" name="city2" class="form-control" value="<?php echo htmlspecialchars($r['city2']); ?>">
                </div>
                <div class="col-md-4 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">State</label>
                    <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($r['state']); ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">STD Code</label>
                    <input type="text" name="stdcode" class="form-control" value="<?php echo htmlspecialchars($r['stdcode']); ?>">
                </div>
                <div class="col-md-3 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($r['phone']); ?>">
                </div>
                <div class="col-md-3 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Status</label>
                    <input type="text" name="status" class="form-control" value="<?php echo htmlspecialchars($r['status']); ?>">
                </div>
                <div class="col-md-3 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Remarks</label>
                    <input type="text" name="remarks" class="form-control" value="<?php echo htmlspecialchars($r['remarks']); ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary px-4 py-2 mt-2"><i class="fas fa-save mr-2"></i> Save Changes</button>
        </form>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
