<?php
require_once('includes/header.php');

// Fetch users
$users = get_all('op_user', '*', null, 'id DESC');

if(isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    $res = delete_data('op_user', $del_id);
    if($res['status'] == 'success') {
        echo "<script>alert('User deleted successfully'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Failed to delete user'); window.location.href='manage_users.php';</script>";
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">Manage Users</h2>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="userTable">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if($users['count'] > 0) {
                        foreach($users['data'] as $u) {
                            ?>
                            <tr>
                                <td><?php echo $u['id']; ?></td>
                                <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($u['user_mobile']); ?></td>
                                <td><?php echo htmlspecialchars($u['user_email']); ?></td>
                                <td><span class="badge badge-info"><?php echo htmlspecialchars($u['user_type']); ?></span></td>
                                <td>
                                    <?php if($u['status'] == 'ACTIVE') { ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php } else { ?>
                                        <span class="badge badge-warning"><?php echo htmlspecialchars($u['status']); ?></span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="manage_users.php?del=<?php echo $u['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#userTable').DataTable();
});
</script>

<?php require_once('includes/footer.php'); ?>
