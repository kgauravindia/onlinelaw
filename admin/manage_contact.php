<?php
require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../includes/function.php');

// Admin Auth Check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Handle Status Update
if (isset($_GET['update_status']) && isset($_GET['id'])) {
    $c_id = intval($_GET['id']);
    $new_status = mysqli_real_escape_string($con, trim($_GET['update_status']));
    mysqli_query($con, "UPDATE contact SET status = '$new_status' WHERE id = $c_id");
    
    $redirect_url = 'manage_contact.php' . (isset($_GET['tab']) ? '?tab=' . urlencode($_GET['tab']) : '');
    header("Location: $redirect_url");
    exit();
}

// Handle Deletion
if (isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    delete_data('contact', $del_id);
    $redirect_url = 'manage_contact.php' . (isset($_GET['tab']) ? '?tab=' . urlencode($_GET['tab']) : '');
    header("Location: $redirect_url");
    exit();
}

$tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'all';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$where_clauses = ["1=1"];
if ($tab === 'complaints') {
    $where_clauses[] = "(area LIKE '%Complain%' OR area LIKE '%Delete%' OR message LIKE '%CIN%' OR message LIKE '%DELIST%')";
} elseif ($tab === 'new') {
    $where_clauses[] = "(status = 'NEW' OR status = '' OR status IS NULL)";
} elseif ($tab === 'resolved') {
    $where_clauses[] = "status = 'RESOLVED'";
}

if (!empty($q)) {
    $sq = mysqli_real_escape_string($con, $q);
    $where_clauses[] = "(name LIKE '%$sq%' OR email LIKE '%$sq%' OR mobile LIKE '%$sq%' OR area LIKE '%$sq%' OR message LIKE '%$sq%')";
}

$where_sql = implode(' AND ', $where_clauses);

// Pagination
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$count_res = direct_sql("SELECT COUNT(*) as total FROM contact WHERE $where_sql");
$total_rows = isset($count_res['data'][0]['total']) ? (int)$count_res['data'][0]['total'] : 0;
$total_pages = ceil($total_rows / $limit);

$inquiries_res = direct_sql("SELECT * FROM contact WHERE $where_sql ORDER BY id DESC LIMIT $limit OFFSET $offset");
$inquiries = $inquiries_res['data'] ?? [];

// Counts for tabs
$cnt_all = direct_sql("SELECT COUNT(*) as total FROM contact")['data'][0]['total'] ?? 0;
$cnt_complaints = direct_sql("SELECT COUNT(*) as total FROM contact WHERE area LIKE '%Complain%' OR area LIKE '%Delete%' OR message LIKE '%CIN%' OR message LIKE '%DELIST%'")['data'][0]['total'] ?? 0;
$cnt_new = direct_sql("SELECT COUNT(*) as total FROM contact WHERE status = 'NEW' OR status = '' OR status IS NULL")['data'][0]['total'] ?? 0;

require_once('includes/header.php');
?>

<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Inquiries &amp; Complaints Desk</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Review contact messages, user feedback, and corporate data removal / delisting requests.</p>
    </div>
    <div class="mt-3 mt-md-0">
        <a href="../contact.php" target="_blank" class="btn btn-outline-primary px-3 shadow-sm font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-external-link-alt mr-1"></i> Public Contact Form
        </a>
    </div>
</div>

<!-- Tabs & Search -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-md);">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            
            <ul class="nav nav-pills" style="gap: 6px;">
                <li class="nav-item">
                    <a class="nav-link font-weight-bold px-3 py-2 <?php echo ($tab === 'all') ? 'active' : 'text-dark bg-light'; ?>" href="manage_contact.php" style="border-radius: 20px; font-size: 0.88rem;">
                        <i class="fas fa-inbox mr-1"></i> All Messages <span class="badge <?php echo ($tab === 'all') ? 'badge-light text-primary' : 'badge-secondary'; ?> ml-1"><?php echo $cnt_all; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold px-3 py-2 <?php echo ($tab === 'complaints') ? 'active bg-danger text-white' : 'text-danger bg-light border border-danger'; ?>" href="manage_contact.php?tab=complaints" style="border-radius: 20px; font-size: 0.88rem;">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Data Removal Complaints <span class="badge <?php echo ($tab === 'complaints') ? 'badge-light text-danger' : 'badge-danger'; ?> ml-1"><?php echo $cnt_complaints; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold px-3 py-2 <?php echo ($tab === 'new') ? 'active bg-warning text-dark' : 'text-dark bg-light'; ?>" href="manage_contact.php?tab=new" style="border-radius: 20px; font-size: 0.88rem;">
                        <i class="fas fa-clock mr-1"></i> New &bull; Unresolved <span class="badge <?php echo ($tab === 'new') ? 'badge-dark' : 'badge-warning text-dark'; ?> ml-1"><?php echo $cnt_new; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold px-3 py-2 <?php echo ($tab === 'resolved') ? 'active bg-success text-white' : 'text-dark bg-light'; ?>" href="manage_contact.php?tab=resolved" style="border-radius: 20px; font-size: 0.88rem;">
                        <i class="fas fa-check mr-1"></i> Resolved
                    </a>
                </li>
            </ul>

            <form method="get" action="manage_contact.php" class="d-flex align-items-center" style="min-width: 260px;">
                <?php if ($tab !== 'all'): ?>
                    <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab); ?>">
                <?php endif; ?>
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search inquiry or CIN..." value="<?php echo htmlspecialchars($q); ?>" style="border-radius: 20px 0 0 20px; font-size: 0.88rem;">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit" style="border-radius: 0 20px 20px 0;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Inquiries Table -->
<div class="card border-0 shadow-sm" style="border-radius: var(--radius-md); overflow: hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                    <tr>
                        <th class="px-4 py-3 border-0" style="width: 70px;">ID</th>
                        <th class="px-4 py-3 border-0" style="width: 170px;">Sender</th>
                        <th class="px-4 py-3 border-0" style="width: 150px;">Category</th>
                        <th class="px-4 py-3 border-0">Message &amp; Extraction</th>
                        <th class="px-4 py-3 border-0" style="width: 130px;">Status</th>
                        <th class="px-4 py-3 border-0 text-right" style="width: 170px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($inquiries) > 0): ?>
                        <?php foreach ($inquiries as $row): ?>
                            <?php
                            $is_complaint = (stripos($row['area'], 'Complain') !== false || stripos($row['area'], 'Delete') !== false || stripos($row['message'], 'DELETE') !== false);
                            
                            // Extract possible CIN from message
                            $cin_match = '';
                            if (preg_match('/[ULul][0-9]{5}[A-Za-z]{2}[0-9]{4}[A-Za-z]{3}[0-9]{6}/', $row['message'], $m)) {
                                $cin_match = strtoupper($m[0]);
                            } elseif (preg_match('/[A-Za-z]{3}-[0-9]{4}/', $row['message'], $m)) {
                                $cin_match = strtoupper($m[0]);
                            }
                            ?>
                            <tr style="<?php echo $is_complaint ? 'background-color: #fffbfa;' : ''; ?>">
                                <td class="px-4 py-3 font-weight-bold text-muted">
                                    #<?php echo $row['id']; ?>
                                    <div class="small text-muted font-weight-normal" style="font-size: 11px;">
                                        <?php echo !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : ''; ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-weight-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></div>
                                    <?php if (!empty($row['email'])): ?>
                                        <div class="small"><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" class="text-muted"><i class="fas fa-envelope mr-1 text-primary"></i> <?php echo htmlspecialchars($row['email']); ?></a></div>
                                    <?php endif; ?>
                                    <?php if (!empty($row['mobile'])): ?>
                                        <div class="small"><a href="tel:<?php echo htmlspecialchars($row['mobile']); ?>" class="text-muted"><i class="fas fa-phone mr-1 text-success"></i> <?php echo htmlspecialchars($row['mobile']); ?></a></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($is_complaint): ?>
                                        <span class="badge badge-danger px-2.5 py-1 text-uppercase font-weight-bold" style="font-size: 11px;">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Data Removal
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-light border text-dark px-2.5 py-1" style="font-size: 11px;">
                                            <?php echo htmlspecialchars($row['area']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="small text-dark" style="max-width: 480px; white-space: pre-line; line-height: 1.5;">
                                        <?php echo htmlspecialchars($row['message']); ?>
                                    </div>
                                    
                                    <?php if (!empty($cin_match)): ?>
                                        <div class="mt-2 p-2 rounded bg-light border d-inline-flex align-items-center gap-2" style="font-size: 12px; gap: 8px;">
                                            <span class="text-muted">Detected CIN:</span>
                                            <code class="font-weight-bold text-primary"><?php echo $cin_match; ?></code>
                                            <a href="manage_companies.php?q=<?php echo urlencode($cin_match); ?>" target="_blank" class="btn btn-xs btn-primary py-0 px-2 font-weight-bold" style="font-size: 11px;">
                                                <i class="fas fa-search mr-1"></i> Manage Company &rarr;
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php 
                                    $st = strtoupper($row['status'] ?? 'NEW');
                                    if ($st === 'RESOLVED') {
                                        echo '<span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Resolved</span>';
                                    } elseif ($st === 'IN_PROGRESS') {
                                        echo '<span class="badge badge-info px-2 py-1">In Progress</span>';
                                    } elseif ($st === 'REJECTED') {
                                        echo '<span class="badge badge-secondary px-2 py-1">Rejected</span>';
                                    } else {
                                        echo '<span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> New</span>';
                                    }
                                    ?>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="btn-group btn-group-sm">
                                        <?php if (strtoupper($row['status'] ?? '') !== 'RESOLVED'): ?>
                                            <a href="manage_contact.php?id=<?php echo $row['id']; ?>&update_status=RESOLVED<?php echo ($tab !== 'all') ? '&tab='.urlencode($tab) : ''; ?>" class="btn btn-outline-success font-weight-bold" title="Mark as Resolved">
                                                <i class="fas fa-check"></i> Resolve
                                            </a>
                                        <?php else: ?>
                                            <a href="manage_contact.php?id=<?php echo $row['id']; ?>&update_status=NEW<?php echo ($tab !== 'all') ? '&tab='.urlencode($tab) : ''; ?>" class="btn btn-outline-secondary" title="Reopen as New">
                                                Reopen
                                            </a>
                                        <?php endif; ?>
                                        <a href="manage_contact.php?del=<?php echo $row['id']; ?><?php echo ($tab !== 'all') ? '&tab='.urlencode($tab) : ''; ?>" onclick="return confirm('Delete this inquiry permanently?');" class="btn btn-outline-danger" title="Delete Inquiry">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                <div class="font-weight-bold">No Inquiries Found</div>
                                <div class="small">No contact submissions match the selected filter.</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<div class="d-flex justify-content-center mt-4">
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm">
            <?php
            $qp = $_GET;
            for ($i = 1; $i <= $total_pages; $i++):
                $qp['p'] = $i;
                $active = ($i == $page) ? 'active' : '';
            ?>
                <li class="page-item <?php echo $active; ?>">
                    <a class="page-link" href="?<?php echo http_build_query($qp); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
<?php endif; ?>

<?php require_once('includes/footer.php'); ?>
