<?php
require_once('includes/header.php');

// Fetch recent bare acts records
$acts = direct_sql("SELECT * FROM acts ORDER BY id DESC LIMIT 500");

if (isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    $res = delete_data('acts', $del_id);
    if ($res['status'] == 'success') {
        echo "<script>alert('Bare Act deleted successfully'); window.location.href='manage_acts.php';</script>";
    } else {
        echo "<script>alert('Failed to delete act'); window.location.href='manage_acts.php';</script>";
    }
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Manage Bare Acts</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Browse and maintain Indian statutes, constitutional acts, and legal directories (Recent 500 Records).</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <a href="manage_data.php" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-arrow-left mr-1"></i> Master Hub
        </a>
        <a href="edit_act.php" class="btn btn-primary font-weight-bold shadow-sm" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-plus mr-1"></i> Add Bare Act
        </a>
    </div>
</div>

<div class="section-card">
    <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom bg-white">
        <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
            <i class="fas fa-book text-warning mr-2"></i> Bare Acts Registry
        </div>
        <span class="badge-premium badge-premium-warning">
            <?php echo isset($acts['data']) ? count($acts['data']) : 0; ?> Listed
        </span>
    </div>
    
    <div class="table-responsive">
        <table class="table align-middle" id="dataTable">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Act Name</th>
                    <th style="width: 100px;">Year</th>
                    <th style="width: 110px;">Act Number</th>
                    <th style="width: 130px;">Type</th>
                    <th>Remarks</th>
                    <th style="width: 100px;" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($acts['data']) && count($acts['data']) > 0): ?>
                    <?php foreach ($acts['data'] as $a): ?>
                        <tr>
                            <td class="text-muted small">#<?php echo $a['id']; ?></td>
                            <td>
                                <strong class="text-dark" style="font-size: 0.9rem;"><?php echo htmlspecialchars($a['name']); ?></strong>
                            </td>
                            <td>
                                <span class="badge-premium badge-premium-primary font-weight-bold">
                                    <?php echo htmlspecialchars($a['year'] ?? '-'); ?>
                                </span>
                            </td>
                            <td>
                                <span class="small font-weight-bold text-muted"><?php echo htmlspecialchars($a['act_no'] ?? '-'); ?></span>
                            </td>
                            <td>
                                <span class="badge-premium badge-premium-info"><?php echo htmlspecialchars($a['act_type'] ?? 'Central'); ?></span>
                            </td>
                            <td>
                                <span class="small text-muted"><?php echo htmlspecialchars(substr($a['remarks'] ?? '', 0, 60)); ?><?php echo strlen($a['remarks'] ?? '') > 60 ? '...' : ''; ?></span>
                            </td>
                            <td class="text-right">
                                <div class="d-inline-flex gap-1" style="gap: 5px;">
                                    <a href="edit_act.php?id=<?php echo $a['id']; ?>" class="btn-action-circle text-primary" title="Edit Bare Act">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="manage_acts.php?del=<?php echo $a['id']; ?>" class="btn-action-circle text-danger" title="Delete Record" onclick="return confirm('Are you sure you want to delete this act?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-book fa-3x mb-3 d-block text-muted opacity-50"></i>
                            No bare acts records found in the database.
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
            "searchPlaceholder": "Filter acts..."
        }
    });
});
</script>

<?php require_once('includes/footer.php'); ?>
