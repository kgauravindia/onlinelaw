<?php
require_once('includes/header.php');

// Fetch recent pincode records
$pincodes = direct_sql("SELECT * FROM pincode ORDER BY id DESC LIMIT 500");

if(isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    $res = delete_data('pincode', $del_id);
    if($res['status'] == 'success') {
        echo "<script>alert('Record deleted successfully'); window.location.href='manage_pincode.php';</script>";
    } else {
        echo "<script>alert('Failed to delete record'); window.location.href='manage_pincode.php';</script>";
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">Manage PIN Codes (Recent 500)</h2>
    <a href="manage_data.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Manage Data</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="dataTable">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>PIN Code</th>
                        <th>Locality Name</th>
                        <th>Office Name</th>
                        <th>District</th>
                        <th>State</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if($pincodes['count'] > 0) {
                        foreach($pincodes['data'] as $p) {
                            ?>
                            <tr>
                                <td><?php echo $p['id']; ?></td>
                                <td><span class="badge badge-success"><?php echo htmlspecialchars($p['pincode']); ?></span></td>
                                <td><?php echo htmlspecialchars($p['locality_name']); ?></td>
                                <td><?php echo htmlspecialchars($p['office_name']); ?></td>
                                <td><?php echo htmlspecialchars($p['district_name']); ?></td>
                                <td><?php echo htmlspecialchars($p['state_name']); ?></td>
                                <td style="min-width: 120px;">
                                    <a href="edit_pincode.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="manage_pincode.php?del=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?');"><i class="fas fa-trash"></i></a>
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
