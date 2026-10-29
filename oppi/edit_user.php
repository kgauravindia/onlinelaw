<?php
require_once('includes/header.php');

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid User ID</div>";
    require_once('includes/footer.php');
    exit;
}

$id = intval($_GET['id']);
$user_data = get_data('op_user', $id);

if ($user_data['count'] == 0) {
    echo "<div class='alert alert-danger'>User not found</div>";
    require_once('includes/footer.php');
    exit;
}

$u = $user_data['data'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '', 'admin_user')) {
        $error = "Security token mismatch. Please reload the page and try again.";
    } else {
        $update_data = array(
            'full_name' => trim($_POST['full_name']),
            'user_mobile' => trim($_POST['user_mobile']),
            'user_email' => trim($_POST['user_email']),
            'user_type' => trim($_POST['user_type']),
            'status' => trim($_POST['status'])
        );

        // If new password is provided, securely hash with PASSWORD_DEFAULT (Bcrypt)
        if (!empty($_POST['user_pass'])) {
            $update_data['user_pass'] = password_hash(trim($_POST['user_pass']), PASSWORD_DEFAULT);
        }

        $res = update_data('op_user', $update_data, $id);
        if ($res['status'] == 'success') {
            $success = "User account updated successfully!";
            $u = get_data('op_user', $id)['data'];
        } else {
            $error = "Failed to update user: " . $res['msg'];
        }
    }
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Edit User Profile</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Manage administrator account profile, contact details, and credentials.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <a href="manage_users.php" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-arrow-left mr-1"></i> Back to Users
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
                <?php echo get_csrf_input('admin_user'); ?>
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($u['full_name']); ?>" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Username (Login ID)</label>
                        <input type="text" class="form-control font-monospace" value="<?php echo htmlspecialchars($u['user_name']); ?>" disabled style="background: #f1f5f9;">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Mobile Number</label>
                        <input type="text" name="user_mobile" class="form-control" value="<?php echo htmlspecialchars($u['user_mobile'] ?? ''); ?>">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Email Address</label>
                        <input type="email" name="user_email" class="form-control" value="<?php echo htmlspecialchars($u['user_email'] ?? ''); ?>">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">User Role Type</label>
                        <select name="user_type" class="form-control">
                            <option value="ADMIN" <?php echo ($u['user_type'] == 'ADMIN') ? 'selected' : ''; ?>>ADMIN</option>
                            <option value="DEV" <?php echo ($u['user_type'] == 'DEV') ? 'selected' : ''; ?>>DEV (Developer)</option>
                            <option value="STAFF" <?php echo ($u['user_type'] == 'STAFF') ? 'selected' : ''; ?>>STAFF</option>
                        </select>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Account Status</label>
                        <select name="status" class="form-control">
                            <option value="ACTIVE" <?php echo ($u['status'] == 'ACTIVE' || $u['user_status'] == 'ACTIVE') ? 'selected' : ''; ?>>ACTIVE</option>
                            <option value="INACTIVE" <?php echo ($u['status'] == 'INACTIVE' || $u['user_status'] == 'INACTIVE') ? 'selected' : ''; ?>>INACTIVE</option>
                        </select>
                    </div>

                    <div class="col-12 form-group mb-4">
                        <label class="small font-weight-bold text-muted mb-1">New Password <span class="text-muted font-weight-normal">(Leave blank to keep unchanged)</span></label>
                        <input type="password" name="user_pass" class="form-control" placeholder="Enter new password">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2" style="gap: 10px;">
                    <a href="manage_users.php" class="btn btn-light px-4 font-weight-bold">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="background: var(--primary-gradient); border: none;">
                        <i class="fas fa-save mr-1"></i> Save User Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="section-card p-4 text-center">
            <div class="admin-avatar mx-auto mb-3" style="width: 72px; height: 72px; font-size: 1.8rem;">
                <?php echo strtoupper(substr($u['full_name'] ?: $u['user_name'] ?: 'U', 0, 1)); ?>
            </div>
            <h5 class="font-weight-bold mb-1"><?php echo htmlspecialchars($u['full_name'] ?: $u['user_name']); ?></h5>
            <span class="badge-premium badge-premium-primary mb-3"><?php echo htmlspecialchars($u['user_type'] ?: 'ADMIN'); ?></span>
            
            <div class="border-top pt-3 text-left small text-muted">
                <div class="d-flex justify-content-between mb-2">
                    <span>Username:</span>
                    <strong class="text-dark">@<?php echo htmlspecialchars($u['user_name'] ?? ''); ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Member Since:</span>
                    <strong class="text-dark"><?php echo !empty($u['created_at']) ? date('d M Y', strtotime($u['created_at'])) : 'N/A'; ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Last Login:</span>
                    <strong class="text-dark"><?php echo !empty($u['last_login']) ? date('d M Y, h:i A', strtotime($u['last_login'])) : 'N/A'; ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
