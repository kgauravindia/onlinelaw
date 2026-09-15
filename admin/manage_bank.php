<?php
require_once('includes/header.php');

// Fetch recent bank IFSC records
$banks = direct_sql("SELECT * FROM bank_ifsc ORDER BY id DESC LIMIT 500");

if(isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    $res = delete_data('bank_ifsc', $del_id);
    if($res['status'] == 'success') {
        echo "<script>alert('Record deleted successfully'); window.location.href='manage_bank.php';</script>";
    } else {
        echo "<script>alert('Failed to delete record'); window.location.href='manage_bank.php';</script>";
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">Manage Bank IFSC (Recent 500)</h2>
    <a href="manage_data.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Manage Data</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="dataTable">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Bank</th>
                        <th>IFSC</th>
                        <th>Branch</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if($banks['count'] > 0) {
                        foreach($banks['data'] as $b) {
                            ?>
                            <tr>
                                <td><?php echo $b['id']; ?></td>
                                <td><?php echo htmlspecialchars($b['bank']); ?></td>
                                <td><span class="badge badge-info"><?php echo htmlspecialchars($b['ifsc']); ?></span></td>
                                <td><?php echo htmlspecialchars($b['branch']); ?></td>
                                <td><?php echo htmlspecialchars($b['city1']); ?></td>
                                <td><?php echo htmlspecialchars($b['state']); ?></td>
                                <td style="min-width: 120px;">
                                    <a href="edit_bank.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="manage_bank.php?del=<?php echo $b['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?');"><i class="fas fa-trash"></i></a>
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
