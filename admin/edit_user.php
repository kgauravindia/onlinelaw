<?php
require_once('includes/header.php');

$error = '';
$success = '';

if(!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid User ID</div>";
    require_once('includes/footer.php');
    exit;
}

$id = intval($_GET['id']);
$user_data = get_data('op_user', $id);

if($user_data['count'] == 0) {
    echo "<div class='alert alert-danger'>User not found</div>";
    require_once('includes/footer.php');
    exit;
}

$u = $user_data['data'];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_data = array(
        'full_name' => trim($_POST['full_name']),
        'user_mobile' => trim($_POST['user_mobile']),
        'user_email' => trim($_POST['user_email']),
        'user_type' => trim($_POST['user_type']),
        'status' => trim($_POST['status'])
    );

    // Only update password if a new one is provided
    if(!empty($_POST['user_pass'])) {
        $update_data['user_pass'] = password_hash(trim($_POST['user_pass']), PASSWORD_DEFAULT);
    }

    $res = update_data('op_user', $update_data, $id);
    if($res['status'] == 'success') {
        $success = "User details updated successfully!";
        // Refresh data
        $u = get_data('op_user', $id)['data'];
    } else {
        $error = "Failed to update user: " . $res['msg'];
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">Edit User</h2>
    <a href="manage_users.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Users</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
        
        <form method="post" action="">
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($u['full_name']); ?>" required>
                </div>
                <div class="col-md-6 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Mobile</label>
                    <input type="text" name="user_mobile" class="form-control" value="<?php echo htmlspecialchars($u['user_mobile']); ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Email</label>
                    <input type="email" name="user_email" class="form-control" value="<?php echo htmlspecialchars($u['user_email']); ?>" required>
                </div>
                <div class="col-md-6 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">User Type</label>
                    <select name="user_type" class="form-control" required>
                        <option value="CLIENT" <?php if($u['user_type'] == 'CLIENT') echo 'selected'; ?>>CLIENT</option>
                        <option value="ADMIN" <?php if($u['user_type'] == 'ADMIN') echo 'selected'; ?>>ADMIN</option>
                        <option value="LAWYER" <?php if($u['user_type'] == 'LAWYER') echo 'selected'; ?>>LAWYER</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="ACTIVE" <?php if($u['status'] == 'ACTIVE') echo 'selected'; ?>>ACTIVE</option>
                        <option value="BLOCK" <?php if($u['status'] == 'BLOCK') echo 'selected'; ?>>BLOCK</option>
                        <option value="PENDING" <?php if($u['status'] == 'PENDING') echo 'selected'; ?>>PENDING</option>
                    </select>
                </div>
                <div class="col-md-6 form-group mb-4">
                    <label class="font-weight-bold text-muted small text-uppercase">Reset Password</label>
                    <input type="password" name="user_pass" class="form-control" placeholder="Leave blank to keep current password">
                    <small class="text-muted">Only fill this if you want to change the password.</small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary px-4 py-2 mt-2"><i class="fas fa-save mr-2"></i> Save Changes</button>
        </form>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
