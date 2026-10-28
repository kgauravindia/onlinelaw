<?php
require_once('includes/header.php');

// Fetch recent pincode records
$pincodes = direct_sql("SELECT * FROM pincode ORDER BY id DESC LIMIT 500");

if (isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    if (!validate_csrf_token($_GET['csrf'] ?? '', 'admin_delete')) {
        echo "<script>alert('Security token invalid or expired. Please refresh the page.'); window.location.href='manage_pincode.php';</script>";
        exit;
    }
    $res = delete_data('pincode', $del_id);
    if ($res['status'] == 'success') {
        echo "<script>alert('Record deleted successfully'); window.location.href='manage_pincode.php';</script>";
    } else {
        echo "<script>alert('Failed to delete record'); window.location.href='manage_pincode.php';</script>";
    }
    exit;
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Manage Postal PIN Codes</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Browse and maintain India Post postal index numbers, post offices, and localities (Recent 500 Records).</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <a href="manage_data.php" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-arrow-left mr-1"></i> Master Hub
        </a>
        <a href="edit_pincode.php" class="btn btn-primary font-weight-bold shadow-sm" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-plus mr-1"></i> Add PIN Code
        </a>
    </div>
</div>

<div class="section-card">
    <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom bg-white">
        <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
            <i class="fas fa-map-marked-alt text-success mr-2"></i> Postal Directory Records
        </div>
        <span class="badge-premium badge-premium-success">
            <?php echo isset($pincodes['data']) ? count($pincodes['data']) : 0; ?> Listed
        </span>
    </div>
    
    <div class="table-responsive">
        <table class="table align-middle" id="dataTable">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th style="width: 130px;">PIN Code</th>
                    <th>Locality Name</th>
                    <th>Office Name</th>
                    <th>District</th>
                    <th>State</th>
                    <th style="width: 100px;" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($pincodes['data']) && count($pincodes['data']) > 0): ?>
                    <?php foreach ($pincodes['data'] as $p): ?>
                        <tr>
                            <td class="text-muted small">#<?php echo $p['id']; ?></td>
                            <td>
                                <span class="badge-premium badge-premium-success font-monospace" style="font-size: 0.85rem;">
                                    <?php echo htmlspecialchars($p['pincode']); ?>
                                </span>
                            </td>
                            <td>
                                <strong class="text-dark" style="font-size: 0.9rem;"><?php echo htmlspecialchars($p['locality_name']); ?></strong>
                            </td>
                            <td>
                                <span class="small font-weight-bold text-muted"><?php echo htmlspecialchars($p['office_name']); ?></span>
                            </td>
                            <td>
                                <span class="small text-muted"><?php echo htmlspecialchars($p['district_name']); ?></span>
                            </td>
                            <td>
                                <span class="small text-muted">
                                    <i class="fas fa-map-marker-alt text-danger mr-1"></i> <?php echo htmlspecialchars($p['state_name']); ?>
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="d-inline-flex gap-1" style="gap: 5px;">
                                    <a href="edit_pincode.php?id=<?php echo $p['id']; ?>" class="btn-action-circle text-primary" title="Edit PIN Code">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="manage_pincode.php?del=<?php echo $p['id']; ?>&csrf=<?php echo generate_csrf_token('admin_delete'); ?>" class="btn-action-circle text-danger" title="Delete Record" onclick="return confirm('Are you sure you want to delete this PIN code?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-map-marker-alt fa-3x mb-3 d-block text-muted opacity-50"></i>
                            No PIN code records found in the database.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "pageLength": 25,
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Filter PIN codes..."
        }
    });
});
</script>

<?php require_once('includes/footer.php'); ?>
