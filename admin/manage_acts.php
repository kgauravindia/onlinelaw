<?php
require_once('includes/header.php');

// Fetch recent acts records
$acts = direct_sql("SELECT * FROM acts ORDER BY id DESC LIMIT 500");

if(isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    $res = delete_data('acts', $del_id);
    if($res['status'] == 'success') {
        echo "<script>alert('Record deleted successfully'); window.location.href='manage_acts.php';</script>";
    } else {
        echo "<script>alert('Failed to delete record'); window.location.href='manage_acts.php';</script>";
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">Manage Acts (Recent 500)</h2>
    <a href="manage_data.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Manage Data</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="dataTable">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Year</th>
                        <th>Act No</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if($acts['count'] > 0) {
                        foreach($acts['data'] as $a) {
                            ?>
                            <tr>
                                <td><?php echo $a['id']; ?></td>
                                <td><?php echo htmlspecialchars($a['name']); ?></td>
                                <td><span class="badge badge-warning"><?php echo htmlspecialchars($a['year']); ?></span></td>
                                <td><?php echo htmlspecialchars($a['act_no']); ?></td>
                                <td><?php echo htmlspecialchars($a['act_type']); ?></td>
                                <td>
                                    <?php if($a['status'] == 'ACTIVE' || strtolower($a['status']) == 'active') { ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php } else { ?>
                                        <span class="badge badge-secondary"><?php echo htmlspecialchars($a['status']); ?></span>
                                    <?php } ?>
                                </td>
                                <td style="min-width: 120px;">
                                    <a href="edit_act.php?id=<?php echo $a['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="manage_acts.php?del=<?php echo $a['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>

<?php require_once('includes/footer.php'); ?>
