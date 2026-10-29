<?php
require_once('includes/header.php');

// Fetch recent bank IFSC records
$banks = direct_sql("SELECT * FROM bank_ifsc ORDER BY id DESC LIMIT 500");

if (isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    if (!validate_csrf_token($_GET['csrf'] ?? '', 'admin_delete')) {
        echo "<script>alert('Security token invalid or expired. Please refresh the page.'); window.location.href='manage_bank.php';</script>";
        exit;
    }
    $res = delete_data('bank_ifsc', $del_id);
    if ($res['status'] == 'success') {
        echo "<script>alert('Record deleted successfully'); window.location.href='manage_bank.php';</script>";
    } else {
        echo "<script>alert('Failed to delete record'); window.location.href='manage_bank.php';</script>";
    }
    exit;
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Manage Bank IFSC &amp; MICR</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Browse and maintain official RBI routing numbers, bank branches, and contact info (Recent 500 Records).</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <a href="manage_data.php" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-arrow-left mr-1"></i> Master Hub
        </a>
        <a href="edit_bank.php" class="btn btn-primary font-weight-bold shadow-sm" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-plus mr-1"></i> Add Bank Record
        </a>
    </div>
</div>

<div class="section-card">
    <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom bg-white">
        <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
            <i class="fas fa-university text-primary mr-2"></i> Bank IFSC Records
        </div>
        <span class="badge-premium badge-premium-primary">
            <?php echo isset($banks['data']) ? count($banks['data']) : 0; ?> Listed
        </span>
    </div>
    
    <div class="table-responsive">
        <table class="table align-middle" id="dataTable">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Bank Name</th>
                    <th style="width: 130px;">IFSC Code</th>
                    <th style="width: 110px;">MICR Code</th>
                    <th>Branch</th>
                    <th>City / District</th>
                    <th>State</th>
                    <th style="width: 100px;" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($banks['data']) && count($banks['data']) > 0): ?>
                    <?php foreach ($banks['data'] as $b): ?>
                        <tr>
                            <td class="text-muted small">#<?php echo $b['id']; ?></td>
                            <td>
                                <strong class="text-dark" style="font-size: 0.9rem;"><?php echo htmlspecialchars($b['bank']); ?></strong>
                            </td>
                            <td>
                                <span class="badge-premium badge-premium-primary font-monospace">
                                    <?php echo htmlspecialchars($b['ifsc']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge-premium badge-premium-info font-monospace">
                                    <?php echo htmlspecialchars($b['micr'] ?? '-'); ?>
                                </span>
                            </td>
                            <td>
                                <span class="small font-weight-bold text-dark"><?php echo htmlspecialchars($b['branch']); ?></span>
                            </td>
                            <td>
                                <span class="small text-muted"><?php echo htmlspecialchars($b['city1'] ?? $b['city2'] ?? '-'); ?></span>
                            </td>
                            <td>
                                <span class="small text-muted">
                                    <i class="fas fa-map-marker-alt text-danger mr-1"></i> <?php echo htmlspecialchars($b['state']); ?>
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="d-inline-flex gap-1" style="gap: 5px;">
                                    <a href="edit_bank.php?id=<?php echo $b['id']; ?>" class="btn-action-circle text-primary" title="Edit Bank">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="manage_bank.php?del=<?php echo $b['id']; ?>&csrf=<?php echo generate_csrf_token('admin_delete'); ?>" class="btn-action-circle text-danger" title="Delete Record" onclick="return confirm('Are you sure you want to delete this bank record?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-university fa-3x mb-3 d-block text-muted opacity-50"></i>
                            No bank records found in the database.
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
            "searchPlaceholder": "Filter bank records..."
        }
    });
});
</script>

<?php require_once('includes/footer.php'); ?>
