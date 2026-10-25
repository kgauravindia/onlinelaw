<?php
require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../includes/function.php');

// Admin Auth Check
check_admin_auth(true);

// Auto Schema Migration / Table Verification
$tbl_check = direct_sql("SHOW TABLES LIKE 'api_logs'");
if (empty($tbl_check['count']) || $tbl_check['count'] == 0) {
    direct_sql("CREATE TABLE IF NOT EXISTS `api_logs` (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `api_key_id` int(11) NULL,
        `api_key` varchar(100) NULL,
        `endpoint` varchar(100) NULL,
        `ip_address` varchar(45) NULL,
        `user_agent` varchar(255) NULL,
        `referer` varchar(255) NULL,
        `status_code` int(11) DEFAULT 200,
        `response_time_ms` float DEFAULT 0,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_api_key_id` (`api_key_id`),
        KEY `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;", 'set');
}

$cols_res = direct_sql("DESCRIBE api_keys");
if (!empty($cols_res['data'])) {
    $existing_fields = array_column($cols_res['data'], 'Field');
    $needed_cols = [
        'contact_email' => "VARCHAR(255) NULL",
        'allowed_endpoints' => "TEXT NULL",
        'allowed_ips' => "TEXT NULL",
        'allowed_domains' => "TEXT NULL",
        'rate_limit_per_day' => "INT DEFAULT 0",
        'rate_limit_per_min' => "INT DEFAULT 0",
        'last_used_at' => "DATETIME NULL",
        'notes' => "TEXT NULL"
    ];
    foreach ($needed_cols as $col => $def) {
        if (!in_array($col, $existing_fields)) {
            direct_sql("ALTER TABLE api_keys ADD $col $def", 'set');
        }
    }
}

// Handle Direct GET Actions (e.g. ?del=ID or ?toggle=ID)
if (isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    if ($del_id > 0) {
        direct_sql("DELETE FROM api_keys WHERE id = $del_id", 'set');
        direct_sql("DELETE FROM api_logs WHERE api_key_id = $del_id", 'set');
        echo "<script>alert('API Key and associated logs deleted successfully.'); window.location.href='manage_api_keys.php';</script>";
        exit;
    }
}

if (isset($_GET['toggle'])) {
    $toggle_id = intval($_GET['toggle']);
    if ($toggle_id > 0) {
        $curr = direct_sql("SELECT is_active FROM api_keys WHERE id = $toggle_id LIMIT 1");
        $curr_status = (!empty($curr['data'][0]['is_active'])) ? intval($curr['data'][0]['is_active']) : 0;
        $new_status = ($curr_status === 1) ? 0 : 1;
        direct_sql("UPDATE api_keys SET is_active = $new_status, updated_at = NOW() WHERE id = $toggle_id", 'set');
        echo "<script>window.location.href='manage_api_keys.php';</script>";
        exit;
    }
}

if (isset($_GET['clear_logs'])) {
    direct_sql("TRUNCATE TABLE api_logs", 'set');
    echo "<script>alert('All API request logs have been cleared.'); window.location.href='manage_api_keys.php';</script>";
    exit;
}

// Handle AJAX Actions (BEFORE header.php is loaded)
if (isset($_POST['ajax_action'])) {
    header('Content-Type: application/json; charset=utf-8');
    $ajax_action = trim($_POST['ajax_action']);

    if ($ajax_action === 'toggle_status') {
        $id = intval($_POST['id'] ?? 0);
        $new_status = intval($_POST['status'] ?? 0);
        if ($id > 0) {
            $q = mysqli_query($con, "UPDATE api_keys SET is_active = $new_status, updated_at = NOW() WHERE id = $id");
            if ($q) {
                echo json_encode(['status' => 'success', 'message' => 'Status updated successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($con)]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid key ID.']);
        }
        exit;
    }

    if ($ajax_action === 'regenerate_key') {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $new_key = 'OLAW_' . strtoupper(bin2hex(random_bytes(10)));
            $safe_key = addslashes($new_key);
            $q = mysqli_query($con, "UPDATE api_keys SET api_key = '$safe_key', updated_at = NOW() WHERE id = $id");
            if ($q) {
                echo json_encode(['status' => 'success', 'new_key' => $new_key, 'message' => 'API Key regenerated successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($con)]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid key ID.']);
        }
        exit;
    }

    if ($ajax_action === 'delete_key') {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            mysqli_query($con, "DELETE FROM api_keys WHERE id = $id");
            mysqli_query($con, "DELETE FROM api_logs WHERE api_key_id = $id");
            echo json_encode(['status' => 'success', 'message' => 'API Key and associated logs deleted.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid key ID.']);
        }
        exit;
    }

    if ($ajax_action === 'clear_logs') {
        mysqli_query($con, "TRUNCATE TABLE api_logs");
        echo json_encode(['status' => 'success', 'message' => 'All API request logs cleared successfully.']);
        exit;
    }

    if ($ajax_action === 'quick_create') {
        $name = trim($_POST['website_name'] ?? '');
        $email = trim($_POST['contact_email'] ?? '');
        $endpoints = isset($_POST['endpoints']) && is_array($_POST['endpoints']) ? implode(',', $_POST['endpoints']) : '*';
        $valid_until = !empty($_POST['valid_until']) ? trim($_POST['valid_until']) : null;
        $rate_limit_day = intval($_POST['rate_limit_per_day'] ?? 0);

        if (empty($name)) {
            echo json_encode(['status' => 'error', 'message' => 'Application/Client Name is required.']);
            exit;
        }

        $new_key = 'OLAW_' . strtoupper(bin2hex(random_bytes(10)));
        $safe_name = addslashes($name);
        $safe_email = addslashes($email);
        $safe_endpoints = addslashes($endpoints);
        $safe_key = addslashes($new_key);
        $valid_sql = $valid_until ? "'$valid_until'" : "NULL";

        $sql = "INSERT INTO api_keys (website_name, contact_email, api_key, allowed_endpoints, rate_limit_per_day, valid_until, is_active, usage_count, created_at, created_by, updated_at, updated_by) VALUES ('$safe_name', '$safe_email', '$safe_key', '$safe_endpoints', $rate_limit_day, $valid_sql, 1, 0, NOW(), 1, NOW(), 1)";
        $q = mysqli_query($con, $sql);

        if ($q) {
            echo json_encode(['status' => 'success', 'key' => $new_key, 'message' => 'API Key created successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error creating key: ' . mysqli_error($con)]);
        }
        exit;
    }
}

// Fetch Metrics & Key Data
$keys_res = direct_sql("SELECT * FROM api_keys ORDER BY id DESC");
$keys = $keys_res['data'] ?? [];

$total_keys = count($keys);
$active_keys = 0;
$total_calls = 0;
foreach ($keys as $k) {
    if (isset($k['is_active']) && intval($k['is_active']) === 1) $active_keys++;
    $total_calls += intval($k['usage_count'] ?? 0);
}

// Fetch Today's Logs Stats
$today_start = date('Y-m-d 00:00:00');
$today_logs_res = direct_sql("SELECT COUNT(*) as total, SUM(CASE WHEN status_code = 200 THEN 1 ELSE 0 END) as success_count FROM api_logs WHERE created_at >= '$today_start'");
$today_total_logs = isset($today_logs_res['data'][0]['total']) ? intval($today_logs_res['data'][0]['total']) : 0;
$today_success_logs = isset($today_logs_res['data'][0]['success_count']) ? intval($today_logs_res['data'][0]['success_count']) : 0;
$today_success_rate = $today_total_logs > 0 ? round(($today_success_logs / $today_total_logs) * 100, 1) : 100;

// Fetch Recent Logs (Latest 50)
$recent_logs_res = direct_sql("SELECT l.*, k.website_name FROM api_logs l LEFT JOIN api_keys k ON l.api_key_id = k.id ORDER BY l.id DESC LIMIT 50");
$recent_logs = $recent_logs_res['data'] ?? [];

// Include HTML Header
require_once('includes/header.php');
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">API Access Control &amp; Key Manager</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Manage API keys, enforce endpoint permissions, configure rate limits, IP whitelists, and inspect live traffic logs.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2" style="gap: 8px;">
        <button type="button" class="btn btn-outline-info font-weight-bold" data-toggle="modal" data-target="#apiPlaygroundModal" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-play-circle mr-1"></i> Live API Playground
        </button>
        <a href="<?php echo $base_url; ?>api" target="_blank" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-book-open mr-1"></i> Public API Docs
        </a>
        <button type="button" class="btn btn-primary font-weight-bold shadow-sm" data-toggle="modal" data-target="#createKeyModal" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-plus-circle mr-1"></i> Create API Key
        </button>
    </div>
</div>

<!-- Quick Metric Stat Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="stat-card-v2" style="border-top: 4px solid #2563eb;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Total API Keys</div>
                    <div class="stat-value text-primary"><?php echo number_format($total_keys); ?></div>
                    <div class="small text-muted mt-1">
                        <span class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> <?php echo $active_keys; ?> Active</span> &bull; <?php echo ($total_keys - $active_keys); ?> Inactive
                    </div>
                </div>
                <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                    <i class="fas fa-key"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="stat-card-v2" style="border-top: 4px solid #0284c7;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Lifetime Requests</div>
                    <div class="stat-value text-dark"><?php echo number_format($total_calls); ?></div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-chart-line text-info mr-1"></i> Cumulative API invocations
                    </div>
                </div>
                <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fas fa-server"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="stat-card-v2" style="border-top: 4px solid #10b981;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Today's Traffic</div>
                    <div class="stat-value text-success"><?php echo number_format($today_total_logs); ?></div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-shield-alt text-success mr-1"></i> <?php echo $today_success_rate; ?>% Success Rate
                    </div>
                </div>
                <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                    <i class="fas fa-bolt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="stat-card-v2" style="border-top: 4px solid #f59e0b;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Protected Endpoints</div>
                    <div class="stat-value text-warning">7 Datasets</div>
                    <div class="small text-muted mt-1">
                        IFSC, MICR, PIN, GST, Passport, MCA, NIC
                    </div>
                </div>
                <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs for Keys and Logs -->
<ul class="nav nav-pills mb-3" id="apiTabs" role="tablist">
    <li class="nav-item mr-2">
        <a class="nav-link active font-weight-bold px-4 py-2" id="keys-tab" data-toggle="pill" href="#keysTabPane" role="tab" style="border-radius: var(--radius-sm);">
            <i class="fas fa-key mr-2"></i> Active API Keys (<?php echo $total_keys; ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link font-weight-bold px-4 py-2" id="logs-tab" data-toggle="pill" href="#logsTabPane" role="tab" style="border-radius: var(--radius-sm);">
            <i class="fas fa-history mr-2"></i> Live Request Logs (Recent <?php echo count($recent_logs); ?>)
        </a>
    </li>
</ul>

<div class="tab-content" id="apiTabsContent">
    <!-- Keys Management Tab -->
    <div class="tab-pane fade show active" id="keysTabPane" role="tabpanel">
        <div class="section-card">
            <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom bg-white">
                <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                    <i class="fas fa-shield-alt text-primary mr-2"></i> Client Access Keys &amp; Permission Policies
                </div>
                <a href="edit_api_key.php" class="btn btn-sm btn-outline-primary font-weight-bold">
                    <i class="fas fa-sliders-h mr-1"></i> Advanced Key Creator
                </a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle" id="apiKeysTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>Application / Owner</th>
                            <th style="min-width: 240px;">API Secret Key</th>
                            <th>Allowed Endpoints</th>
                            <th>Security &amp; Quotas</th>
                            <th>Usage</th>
                            <th>Validity</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 130px;" class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($keys)): ?>
                            <?php foreach ($keys as $k): 
                                $is_active = isset($k['is_active']) && intval($k['is_active']) === 1;
                                $raw_endpoints = trim($k['allowed_endpoints'] ?? '');
                                $is_all_endpoints = empty($raw_endpoints) || $raw_endpoints === '*' || $raw_endpoints === 'all';
                                
                                $endpoints_arr = [];
                                if (!$is_all_endpoints) {
                                    if (strpos($raw_endpoints, '[') === 0) {
                                        $endpoints_arr = json_decode($raw_endpoints, true) ?: [];
                                    } else {
                                        $endpoints_arr = explode(',', $raw_endpoints);
                                    }
                                }

                                $is_expired = false;
                                if (!empty($k['valid_until'])) {
                                    $is_expired = strtotime($k['valid_until'] . ' 23:59:59') < time();
                                }
                            ?>
                                <tr id="row-key-<?php echo $k['id']; ?>">
                                    <td class="text-muted small">#<?php echo $k['id']; ?></td>
                                    <td>
                                        <div class="font-weight-bold text-dark" style="font-size: 0.92rem;">
                                            <?php echo htmlspecialchars($k['website_name']); ?>
                                        </div>
                                        <?php if (!empty($k['contact_email'])): ?>
                                            <div class="small text-muted"><i class="fas fa-envelope mr-1"></i><?php echo htmlspecialchars($k['contact_email']); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($k['notes'])): ?>
                                            <div class="small text-muted font-italic mt-1" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <i class="fas fa-sticky-note mr-1"></i><?php echo htmlspecialchars($k['notes']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm" style="max-width: 250px;">
                                            <input type="password" class="form-control font-monospace text-dark bg-light font-weight-bold" id="key-input-<?php echo $k['id']; ?>" value="<?php echo htmlspecialchars($k['api_key']); ?>" readonly style="font-size: 0.8rem; border-color: #cbd5e1;">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" onclick="toggleKeyVisibility(<?php echo $k['id']; ?>)" title="Show/Hide Key">
                                                    <i class="fas fa-eye" id="eye-icon-<?php echo $k['id']; ?>"></i>
                                                </button>
                                                <button class="btn btn-outline-primary" type="button" onclick="copyToClipboard('<?php echo htmlspecialchars($k['api_key']); ?>', this)" title="Copy API Key">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($is_all_endpoints): ?>
                                            <span class="badge-premium badge-premium-success">
                                                <i class="fas fa-check-double mr-1"></i> All Endpoints (7)
                                            </span>
                                        <?php else: ?>
                                            <div class="d-flex flex-wrap gap-1" style="gap: 4px; max-width: 220px;">
                                                <?php foreach ($endpoints_arr as $ep): ?>
                                                    <span class="badge-premium badge-premium-primary" style="font-size: 0.72rem; padding: 2px 7px;">
                                                        <?php echo htmlspecialchars(trim($ep)); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <?php if (!empty($k['allowed_ips'])): ?>
                                                <div class="text-dark"><i class="fas fa-network-wired text-info mr-1"></i> IP Whitelisted</div>
                                            <?php else: ?>
                                                <div class="text-muted"><i class="fas fa-globe text-muted mr-1"></i> Any IP</div>
                                            <?php endif; ?>

                                            <?php if (!empty($k['rate_limit_per_day']) && intval($k['rate_limit_per_day']) > 0): ?>
                                                <div class="text-warning font-weight-bold mt-1">
                                                    <i class="fas fa-tachometer-alt mr-1"></i> <?php echo number_format($k['rate_limit_per_day']); ?> req/day
                                                </div>
                                            <?php else: ?>
                                                <div class="text-success mt-1"><i class="fas fa-infinity mr-1"></i> Unlimited quota</div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?php echo number_format($k['usage_count'] ?? 0); ?></div>
                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                            <?php if (!empty($k['last_used_at'])): ?>
                                                Last: <?php echo date('d M, H:i', strtotime($k['last_used_at'])); ?>
                                            <?php else: ?>
                                                Never used
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (empty($k['valid_until'])): ?>
                                            <span class="badge-premium badge-premium-success">
                                                <i class="fas fa-infinity mr-1"></i> Lifetime
                                            </span>
                                        <?php elseif ($is_expired): ?>
                                            <span class="badge-premium badge-premium-danger">
                                                <i class="fas fa-clock mr-1"></i> Expired (<?php echo date('d M Y', strtotime($k['valid_until'])); ?>)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge-premium badge-premium-info">
                                                <i class="fas fa-calendar-check mr-1"></i> <?php echo date('d M Y', strtotime($k['valid_until'])); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="manage_api_keys.php?toggle=<?php echo $k['id']; ?>" class="btn btn-sm <?php echo $is_active ? 'btn-outline-success' : 'btn-outline-danger'; ?> font-weight-bold py-1 px-2" style="border-radius: 20px; font-size: 0.78rem;" title="Click to toggle status">
                                            <i class="fas <?php echo $is_active ? 'fa-check-circle' : 'fa-ban'; ?> mr-1"></i>
                                            <?php echo $is_active ? 'Active' : 'Suspended'; ?>
                                        </a>
                                    </td>
                                    <td class="text-right">
                                        <div class="d-inline-flex gap-1" style="gap: 5px;">
                                            <button type="button" class="btn-action-circle text-info" title="Test in Playground" onclick="openInPlayground('<?php echo htmlspecialchars($k['api_key']); ?>')">
                                                <i class="fas fa-play"></i>
                                            </button>
                                            <a href="edit_api_key.php?id=<?php echo $k['id']; ?>" class="btn-action-circle text-primary" title="Edit Permissions">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <button type="button" class="btn-action-circle text-warning" title="Regenerate Key" onclick="regenerateKey(<?php echo $k['id']; ?>, '<?php echo htmlspecialchars($k['website_name']); ?>')">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                            <a href="manage_api_keys.php?del=<?php echo $k['id']; ?>" class="btn-action-circle text-danger" title="Revoke & Delete Key" onclick="return confirm('Are you sure you want to completely revoke and delete the API key for \'<?php echo htmlspecialchars($k['website_name']); ?>\'?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Live Request Logs Tab -->
    <div class="tab-pane fade" id="logsTabPane" role="tabpanel">
        <div class="section-card">
            <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom bg-white">
                <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                    <i class="fas fa-history text-primary mr-2"></i> Real-time API Traffic &amp; Security Auditing
                </div>
                <div>
                    <a href="manage_api_keys.php?clear_logs=1" class="btn btn-sm btn-outline-danger font-weight-bold" onclick="return confirm('Are you sure you want to purge and clear all API audit logs?');">
                        <i class="fas fa-trash-alt mr-1"></i> Clear Audit Logs
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle" id="apiLogsTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Log ID</th>
                            <th>Timestamp</th>
                            <th>Endpoint Action</th>
                            <th>Client / Key ID</th>
                            <th>IP Address</th>
                            <th>Status Code</th>
                            <th>Latency</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_logs)): ?>
                            <?php foreach ($recent_logs as $log): 
                                $status = intval($log['status_code'] ?? 200);
                                $badge_class = 'badge-premium-success';
                                if ($status === 401 || $status === 403) $badge_class = 'badge-premium-danger';
                                elseif ($status === 429) $badge_class = 'badge-premium-warning';
                                elseif ($status === 404) $badge_class = 'badge-premium-info';
                            ?>
                                <tr>
                                    <td class="text-muted small">#<?php echo $log['id']; ?></td>
                                    <td class="small text-dark font-weight-bold"><?php echo date('d M Y, H:i:s', strtotime($log['created_at'])); ?></td>
                                    <td>
                                        <span class="badge-premium badge-premium-primary font-monospace">
                                            <?php echo htmlspecialchars($log['endpoint'] ?: 'unknown'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small font-weight-bold text-dark"><?php echo htmlspecialchars($log['website_name'] ?: 'External/Unknown'); ?></div>
                                        <span class="font-monospace text-muted" style="font-size: 0.72rem;"><?php echo htmlspecialchars(substr($log['api_key'] ?? '', 0, 12) . '...'); ?></span>
                                    </td>
                                    <td>
                                        <span class="font-monospace small text-dark"><?php echo htmlspecialchars($log['ip_address']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge-premium <?php echo $badge_class; ?>">
                                            HTTP <?php echo $status; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="small font-weight-bold text-muted"><?php echo number_format(floatval($log['response_time_ms'] ?? 0), 1); ?> ms</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Quick Create API Key -->
<div class="modal fade" id="createKeyModal" tabindex="-1" role="dialog" aria-labelledby="createKeyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg);">
            <div class="modal-header bg-primary text-white border-0" style="border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
                <h5 class="modal-title font-weight-bold" id="createKeyModalLabel">
                    <i class="fas fa-key mr-2"></i> Create New API Key
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickCreateKeyForm">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-dark mb-1">Application / Website / Client Name *</label>
                        <input type="text" name="website_name" id="new_website_name" class="form-control" placeholder="e.g. FinTech Portal / Mobile App" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-dark mb-1">Owner Contact Email (Optional)</label>
                        <input type="email" name="contact_email" id="new_contact_email" class="form-control" placeholder="developer@partner.com">
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-dark mb-1">Allowed Endpoints Access</label>
                        <div class="d-flex flex-wrap gap-2" style="gap: 8px;">
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox" class="custom-control-input ep-quick-check" id="ep-all" value="*" checked onchange="toggleQuickAllEndpoints(this)">
                                <label class="custom-control-label font-weight-bold text-primary" for="ep-all">All Endpoints</label>
                            </div>
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox" name="endpoints[]" class="custom-control-input ep-item-check" id="ep-ifsc" value="bank-ifsc" checked>
                                <label class="custom-control-label" for="ep-ifsc">Bank IFSC</label>
                            </div>
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox" name="endpoints[]" class="custom-control-input ep-item-check" id="ep-micr" value="bank-micr" checked>
                                <label class="custom-control-label" for="ep-micr">Bank MICR</label>
                            </div>
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox" name="endpoints[]" class="custom-control-input ep-item-check" id="ep-pincode" value="pincode" checked>
                                <label class="custom-control-label" for="ep-pincode">PIN Codes</label>
                            </div>
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox" name="endpoints[]" class="custom-control-input ep-item-check" id="ep-hsn" value="hsn-sac" checked>
                                <label class="custom-control-label" for="ep-hsn">GST HSN/SAC</label>
                            </div>
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox" name="endpoints[]" class="custom-control-input ep-item-check" id="ep-passport" value="passport" checked>
                                <label class="custom-control-label" for="ep-passport">Passport</label>
                            </div>
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox" name="endpoints[]" class="custom-control-input ep-item-check" id="ep-company" value="company" checked>
                                <label class="custom-control-label" for="ep-company">Companies (MCA)</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="endpoints[]" class="custom-control-input ep-item-check" id="ep-nic" value="nic" checked>
                                <label class="custom-control-label" for="ep-nic">NIC Codes</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="small font-weight-bold text-dark mb-1">Daily Request Quota</label>
                            <input type="number" name="rate_limit_per_day" class="form-control" value="0" placeholder="0 = Unlimited">
                            <small class="text-muted">0 for unlimited requests/day</small>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="small font-weight-bold text-dark mb-1">Valid Until (Expiry)</label>
                            <input type="date" name="valid_until" class="form-control">
                            <small class="text-muted">Leave empty for Lifetime</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" id="btnSubmitQuickKey">
                        <i class="fas fa-check mr-1"></i> Generate API Key
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Interactive API Live Playground -->
<div class="modal fade" id="apiPlaygroundModal" tabindex="-1" role="dialog" aria-labelledby="apiPlaygroundModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg);">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
                <h5 class="modal-title font-weight-bold" id="apiPlaygroundModalLabel">
                    <i class="fas fa-terminal text-info mr-2"></i> Interactive API Live Playground
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-dark mb-1">Select API Key for Testing</label>
                        <select class="form-control font-monospace" id="pg_api_key">
                            <?php foreach ($keys as $k): ?>
                                <option value="<?php echo htmlspecialchars($k['api_key']); ?>">
                                    <?php echo htmlspecialchars($k['website_name']); ?> (<?php echo substr($k['api_key'], 0, 10); ?>...)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-dark mb-1">Select Endpoint / Action</label>
                        <select class="form-control font-weight-bold" id="pg_action" onchange="updatePlaygroundInputs()">
                            <option value="bank-ifsc">Bank IFSC Lookup (action=bank-ifsc)</option>
                            <option value="bank-micr">Bank MICR Lookup (action=bank-micr)</option>
                            <option value="pincode">PIN Code Lookup (action=pincode)</option>
                            <option value="hsn-sac">GST HSN / SAC Code Search (action=hsn-sac)</option>
                            <option value="passport">Passport Office Search (action=passport)</option>
                            <option value="company">MCA Company Search (action=company)</option>
                            <option value="nic">NIC Activity Code Search (action=nic)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3" id="pg_param_container">
                    <label class="small font-weight-bold text-dark mb-1" id="pg_param_label">IFSC Code parameter (ifsc)</label>
                    <input type="text" class="form-control font-monospace" id="pg_param_value" value="SBIN0000001" placeholder="e.g. SBIN0000001">
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button type="button" class="btn btn-primary font-weight-bold" id="pg_send_btn" onclick="executePlaygroundRequest()">
                        <i class="fas fa-paper-plane mr-1"></i> Send Live Request
                    </button>
                    <div id="pg_meta" class="small text-muted font-monospace"></div>
                </div>

                <div class="form-group mb-0">
                    <label class="small font-weight-bold text-dark mb-1">Live JSON Response Output</label>
                    <pre id="pg_response_box" class="p-3 bg-dark text-light rounded font-monospace" style="max-height: 280px; overflow-y: auto; font-size: 0.85rem; border: 1px solid #334155;">// Response will appear here after clicking "Send Live Request"...</pre>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>

<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        // Prevent generic alert popups
        $.fn.dataTable.ext.errMode = 'none';

        $('#apiKeysTable').DataTable({
            "pageLength": 25,
            "autoWidth": false,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Filter API keys...",
                "emptyTable": "No API Keys generated yet. Click 'Create API Key' to get started."
            }
        });

        var logsTable = $('#apiLogsTable').DataTable({
            "pageLength": 25,
            "order": [[0, "desc"]],
            "autoWidth": false,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Filter logs...",
                "emptyTable": "No API access logs recorded yet. Inbound requests will appear here in real time."
            }
        });

        // Auto-adjust columns when tabs are switched
        $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
            if ($.fn.DataTable.isDataTable('#apiLogsTable')) {
                $('#apiLogsTable').DataTable().columns.adjust().responsive.recalc();
            }
            if ($.fn.DataTable.isDataTable('#apiKeysTable')) {
                $('#apiKeysTable').DataTable().columns.adjust().responsive.recalc();
            }
        });
    }

    // Handle Quick Create Key
    $('#quickCreateKeyForm').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#btnSubmitQuickKey');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Generating...');

        var formData = $(this).serialize() + '&ajax_action=quick_create';
        $.post('manage_api_keys.php', formData, function(res) {
            btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Generate API Key');
            if (res.status === 'success') {
                alert('API Key Generated Successfully!\nKey: ' + res.key);
                location.reload();
            } else {
                alert('Error: ' + res.message);
            }
        }, 'json').fail(function() {
            btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Generate API Key');
            alert('Request failed. Check server logs.');
        });
    });
});

function toggleKeyVisibility(id) {
    var input = $('#key-input-' + id);
    var icon = $('#eye-icon-' + id);
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
}

function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(function() {
        var origHtml = $(btn).html();
        $(btn).html('<i class="fas fa-check text-success"></i>');
        setTimeout(function() {
            $(btn).html(origHtml);
        }, 1500);
    });
}

function regenerateKey(id, name) {
    if (confirm('Are you sure you want to regenerate the API key for "' + name + '"?\nThe old key will immediately stop working!')) {
        $.post('manage_api_keys.php', {
            ajax_action: 'regenerate_key',
            id: id
        }, function(res) {
            if (res.status === 'success') {
                alert('New API Key Generated:\n' + res.new_key);
                location.reload();
            } else {
                alert('Error: ' + res.message);
            }
        }, 'json');
    }
}

function toggleQuickAllEndpoints(cb) {
    $('.ep-item-check').prop('checked', cb.checked);
}

// Playground Logic
function openInPlayground(apiKey) {
    $('#pg_api_key').val(apiKey);
    $('#apiPlaygroundModal').modal('show');
}

function updatePlaygroundInputs() {
    var act = $('#pg_action').val();
    var label = $('#pg_param_label');
    var input = $('#pg_param_value');

    if (act === 'bank-ifsc') {
        label.text('IFSC Code parameter (ifsc)');
        input.val('SBIN0000001');
    } else if (act === 'bank-micr') {
        label.text('MICR Clearing Code parameter (micr)');
        input.val('110002001');
    } else if (act === 'pincode') {
        label.text('PIN Code parameter (pincode)');
        input.val('110001');
    } else if (act === 'hsn-sac') {
        label.text('GST Search Query parameter (q)');
        input.val('software');
    } else if (act === 'passport') {
        label.text('Passport Office Search query (q)');
        input.val('Delhi');
    } else if (act === 'company') {
        label.text('Company Name or CIN parameter (q / cin)');
        input.val('Tata');
    } else if (act === 'nic') {
        label.text('NIC Code / Industry search query (q)');
        input.val('Legal');
    }
}

function executePlaygroundRequest() {
    var apiKey = $('#pg_api_key').val();
    var act = $('#pg_action').val();
    var paramVal = $('#pg_param_value').val();
    var outBox = $('#pg_response_box');
    var meta = $('#pg_meta');
    var sendBtn = $('#pg_send_btn');

    if (!apiKey) {
        alert('Please select or provide an API Key.');
        return;
    }

    sendBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Calling API...');
    outBox.text('// Requesting ../api.php...');

    var startTime = performance.now();
    var data = { api_key: apiKey, action: act };

    if (act === 'bank-ifsc') data.ifsc = paramVal;
    else if (act === 'bank-micr') data.micr = paramVal;
    else if (act === 'pincode') data.pincode = paramVal;
    else if (act === 'hsn-sac' || act === 'passport' || act === 'company' || act === 'nic') data.q = paramVal;

    $.ajax({
        url: '../api.php',
        type: 'GET',
        data: data,
        dataType: 'json'
    }).done(function(res, textStatus, xhr) {
        var elapsed = (performance.now() - startTime).toFixed(1);
        sendBtn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Send Live Request');
        meta.html('<span class="text-success font-weight-bold">HTTP ' + xhr.status + ' OK</span> | ' + elapsed + ' ms');
        outBox.text(JSON.stringify(res, null, 4));
    }).fail(function(xhr) {
        var elapsed = (performance.now() - startTime).toFixed(1);
        sendBtn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Send Live Request');
        meta.html('<span class="text-danger font-weight-bold">HTTP ' + xhr.status + ' ' + xhr.statusText + '</span> | ' + elapsed + ' ms');
        try {
            var errJson = JSON.parse(xhr.responseText);
            outBox.text(JSON.stringify(errJson, null, 4));
        } catch(e) {
            outBox.text(xhr.responseText || '// Error occurred');
        }
    });
}
</script>
