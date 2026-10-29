<?php
require_once('includes/header.php');

// Fetch users
$users = get_all('op_user', '*', null, 'id DESC');

if (isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    if (!validate_csrf_token($_GET['csrf'] ?? '', 'admin_delete')) {
        echo "<script>alert('Security token invalid or expired. Please refresh the page.'); window.location.href='manage_users.php';</script>";
        exit;
    }
    if ($del_id === intval($_SESSION['user_id'] ?? 0)) {
        echo "<script>alert('Error: You cannot delete your currently signed-in account.'); window.location.href='manage_users.php';</script>";
        exit;
    }
    $res = delete_data('op_user', $del_id);
    if ($res['status'] == 'success') {
        echo "<script>alert('User deleted successfully'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Failed to delete user'); window.location.href='manage_users.php';</script>";
    }
    exit;
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Manage Platform Users</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">View and manage system administrators, staff accounts, and access permissions.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <a href="manage_data.php" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-arrow-left mr-1"></i> Master Hub
        </a>
    </div>
</div>

<div class="section-card">
    <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom bg-white">
        <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
            <i class="fas fa-users text-primary mr-2"></i> System Administrators &amp; Staff
        </div>
        <span class="badge-premium badge-premium-primary">
            <?php echo isset($users['data']) ? count($users['data']) : 0; ?> Active Accounts
        </span>
    </div>
    
    <div class="table-responsive">
        <table class="table align-middle" id="userTable">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>User / Name</th>
                    <th>Mobile</th>
                    <th>Email Address</th>
                    <th style="width: 130px;">Role Type</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 100px;" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users['count'] > 0): ?>
                    <?php foreach ($users['data'] as $u): ?>
                        <tr>
                            <td class="text-muted small">#<?php echo $u['id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="admin-avatar mr-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        <?php echo strtoupper(substr($u['full_name'] ?: $u['user_name'] ?: 'U', 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark" style="font-size: 0.9rem;">
                                            <?php echo htmlspecialchars($u['full_name'] ?: 'N/A'); ?>
                                        </div>
                                        <small class="text-muted font-monospace">@<?php echo htmlspecialchars($u['user_name'] ?? ''); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="small font-weight-bold text-dark"><?php echo htmlspecialchars($u['user_mobile'] ?: '-'); ?></span>
                            </td>
                            <td>
                                <span class="small text-muted"><?php echo htmlspecialchars($u['user_email'] ?: '-'); ?></span>
                            </td>
                            <td>
                                <span class="badge-premium badge-premium-info">
                                    <?php echo htmlspecialchars($u['user_type'] ?: 'ADMIN'); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (strtoupper($u['status'] ?? '') === 'ACTIVE' || strtoupper($u['user_status'] ?? '') === 'ACTIVE'): ?>
                                    <span class="badge-premium badge-premium-success">Active</span>
                                <?php else: ?>
                                    <span class="badge-premium badge-premium-warning"><?php echo htmlspecialchars($u['status'] ?: 'Inactive'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <div class="d-inline-flex gap-1" style="gap: 5px;">
                                    <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="btn-action-circle text-primary" title="Edit User">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="manage_users.php?del=<?php echo $u['id']; ?>&csrf=<?php echo generate_csrf_token('admin_delete'); ?>" class="btn-action-circle text-danger" title="Delete User" onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-users fa-3x mb-3 d-block text-muted opacity-50"></i>
                            No users found in the database.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        "pageLength": 25,
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Filter users..."
        }
    });
});
</script>

<?php require_once('includes/footer.php'); ?>
