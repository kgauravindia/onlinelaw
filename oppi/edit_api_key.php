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

$error = '';
$success = '';
$is_edit = isset($_GET['id']) && intval($_GET['id']) > 0;
$id = $is_edit ? intval($_GET['id']) : 0;

$key_data = [
    'website_name' => '',
    'contact_email' => '',
    'api_key' => 'OLAW_' . strtoupper(bin2hex(random_bytes(10))),
    'allowed_endpoints' => '*',
    'allowed_ips' => '',
    'allowed_domains' => '',
    'rate_limit_per_day' => 0,
    'rate_limit_per_min' => 0,
    'valid_until' => '',
    'is_active' => 1,
    'usage_count' => 0,
    'last_used_at' => '',
    'notes' => ''
];

if ($is_edit) {
    $res = direct_sql("SELECT * FROM api_keys WHERE id = $id LIMIT 1");
    if (empty($res['count']) || $res['count'] == 0) {
        require_once('includes/header.php');
        echo "<div class='alert alert-danger'>API Key not found.</div>";
        require_once('includes/footer.php');
        exit;
    }
    $key_data = $res['data'][0];
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $website_name = trim($_POST['website_name'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $api_key = trim($_POST['api_key'] ?? '');
    $allowed_ips = trim($_POST['allowed_ips'] ?? '');
    $allowed_domains = trim($_POST['allowed_domains'] ?? '');
    $rate_limit_per_day = intval($_POST['rate_limit_per_day'] ?? 0);
    $rate_limit_per_min = intval($_POST['rate_limit_per_min'] ?? 0);
    $valid_until = !empty($_POST['valid_until']) ? trim($_POST['valid_until']) : null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $notes = trim($_POST['notes'] ?? '');

    // Endpoint permissions
    $endpoints_scope = trim($_POST['endpoints_scope'] ?? 'all');
    if ($endpoints_scope === 'all') {
        $allowed_endpoints = '*';
    } else {
        $selected_eps = isset($_POST['endpoints']) && is_array($_POST['endpoints']) ? $_POST['endpoints'] : [];
        if (empty($selected_eps)) {
            $allowed_endpoints = '*';
        } else {
            $allowed_endpoints = implode(',', $selected_eps);
        }
    }

    if (empty($website_name)) {
        $error = "Client / Application Name is required.";
    } elseif (empty($api_key)) {
        $error = "API Key cannot be empty.";
    } else {
        $safe_name = addslashes($website_name);
        $safe_email = addslashes($contact_email);
        $safe_key = addslashes($api_key);
        $safe_eps = addslashes($allowed_endpoints);
        $safe_ips = addslashes($allowed_ips);
        $safe_domains = addslashes($allowed_domains);
        $safe_notes = addslashes($notes);
        $valid_sql = $valid_until ? "'$valid_until'" : "NULL";

        if ($is_edit) {
            $sql = "UPDATE api_keys SET 
                website_name = '$safe_name',
                contact_email = '$safe_email',
                api_key = '$safe_key',
                allowed_endpoints = '$safe_eps',
                allowed_ips = '$safe_ips',
                allowed_domains = '$safe_domains',
                rate_limit_per_day = $rate_limit_per_day,
                rate_limit_per_min = $rate_limit_per_min,
                valid_until = $valid_sql,
                is_active = $is_active,
                notes = '$safe_notes',
                updated_at = NOW(),
                updated_by = 1
                WHERE id = $id";
            $update_res = direct_sql($sql, 'set');
            if ($update_res['status'] === 'success') {
                $success = "API Key and access control policies updated successfully!";
                $refresh = direct_sql("SELECT * FROM api_keys WHERE id = $id LIMIT 1");
                if (!empty($refresh['data'])) {
                    $key_data = $refresh['data'][0];
                }
            } else {
                $error = "Failed to update API Key: " . ($update_res['msg'] ?? 'DB Error');
            }
        } else {
            // Check for duplicate key
            $dup_check = direct_sql("SELECT id FROM api_keys WHERE api_key = '$safe_key' LIMIT 1");
            if (!empty($dup_check['count']) && $dup_check['count'] > 0) {
                $error = "This API Key already exists in the system. Please generate a unique key.";
            } else {
                $sql = "INSERT INTO api_keys (website_name, contact_email, api_key, allowed_endpoints, allowed_ips, allowed_domains, rate_limit_per_day, rate_limit_per_min, valid_until, is_active, usage_count, notes, created_at, created_by, updated_at, updated_by) VALUES ('$safe_name', '$safe_email', '$safe_key', '$safe_eps', '$safe_ips', '$safe_domains', $rate_limit_per_day, $rate_limit_per_min, $valid_sql, $is_active, 0, '$safe_notes', NOW(), 1, NOW(), 1)";
                $insert_res = direct_sql($sql, 'set');
                if ($insert_res['status'] === 'success') {
                    echo "<script>alert('API Key created successfully!'); window.location.href='manage_api_keys.php';</script>";
                    exit;
                } else {
                    $error = "Failed to create API key: " . ($insert_res['msg'] ?? 'DB Error');
                }
            }
        }
    }
}

// Parse endpoints for checkboxes
$raw_eps = trim($key_data['allowed_endpoints'] ?? '');
$is_all_eps = empty($raw_eps) || $raw_eps === '*' || $raw_eps === 'all';
$current_eps = [];
if (!$is_all_eps) {
    if (strpos($raw_eps, '[') === 0) {
        $current_eps = json_decode($raw_eps, true) ?: [];
    } else {
        $current_eps = array_map('trim', explode(',', $raw_eps));
    }
}

// Include HTML Header
require_once('includes/header.php');
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">
            <?php echo $is_edit ? 'Edit API Key &amp; Access Policy' : 'Create New API Key'; ?>
        </h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">
            Configure granular endpoint authorization, rate limits, IP/Domain firewall rules, and key expiration.
        </p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <a href="manage_api_keys.php" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-arrow-left mr-1"></i> Back to API Keys
        </a>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center mb-4" style="border-radius: var(--radius-md); gap: 10px;">
        <i class="fas fa-exclamation-circle fa-lg"></i>
        <div><?php echo htmlspecialchars($error); ?></div>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success shadow-sm border-0 d-flex align-items-center mb-4" style="border-radius: var(--radius-md); gap: 10px;">
        <i class="fas fa-check-circle fa-lg"></i>
        <div><?php echo htmlspecialchars($success); ?></div>
    </div>
<?php endif; ?>

<form method="post" action="">
    <div class="row">
        <!-- Main Configuration Column -->
        <div class="col-lg-8 mb-4">
            <!-- 1. Identity & Credentials Card -->
            <div class="section-card p-4 mb-4">
                <h5 class="font-weight-bold text-dark mb-3">
                    <i class="fas fa-id-card text-primary mr-2"></i> Client Identity &amp; API Key
                </h5>
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Application / Client Name *</label>
                        <input type="text" name="website_name" class="form-control font-weight-bold" value="<?php echo htmlspecialchars($key_data['website_name']); ?>" placeholder="e.g. Acme FinTech / Web Portal" required>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Owner Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($key_data['contact_email'] ?? ''); ?>" placeholder="developer@example.com">
                    </div>

                    <div class="col-12 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">API Key / Secret Token *</label>
                        <div class="input-group">
                            <input type="text" name="api_key" id="api_key_input" class="form-control font-monospace font-weight-bold bg-light" value="<?php echo htmlspecialchars($key_data['api_key']); ?>" required style="letter-spacing: 0.5px;">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary font-weight-bold" onclick="generateRandomKey()" title="Generate New Key">
                                    <i class="fas fa-magic mr-1"></i> Generate
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="copyKeyText()" title="Copy Key">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">The client passes this in the request as <code>?api_key=...</code> or header <code>X-API-Key: ...</code></small>
                    </div>

                    <div class="col-12 form-group mb-0">
                        <label class="small font-weight-bold text-muted mb-1">Internal Memo / Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Authorized partner integration for mobile app billing"><?php echo htmlspecialchars($key_data['notes'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 2. Granular Endpoint Access Control Matrix -->
            <div class="section-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-layer-group text-primary mr-2"></i> Granular Endpoint Permissions
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" onclick="selectAllEndpoints(true)">Allow All</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="selectAllEndpoints(false)">Deselect All</button>
                    </div>
                </div>
                <p class="text-muted small mb-3">Select which datasets and API actions this key is permitted to query. Requests to unselected endpoints will return <code>403 Forbidden</code>.</p>

                <div class="form-group mb-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="scope_all" name="endpoints_scope" value="all" class="custom-control-input" <?php echo $is_all_eps ? 'checked' : ''; ?> onchange="toggleScopeRadio()">
                        <label class="custom-control-label font-weight-bold text-dark" for="scope_all">Full Access (All Current &amp; Future Endpoints)</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="scope_custom" name="endpoints_scope" value="custom" class="custom-control-input" <?php echo !$is_all_eps ? 'checked' : ''; ?> onchange="toggleScopeRadio()">
                        <label class="custom-control-label font-weight-bold text-dark" for="scope_custom">Custom Endpoint Policy</label>
                    </div>
                </div>

                <div id="custom_endpoints_container" style="<?php echo $is_all_eps ? 'display:none;' : ''; ?>">
                    <div class="row">
                        <!-- IFSC -->
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 bg-light h-100">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="endpoints[]" value="bank-ifsc" class="custom-control-input ep-chk" id="chk_ifsc" <?php echo (in_array('bank-ifsc', $current_eps) || $is_all_eps) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label font-weight-bold text-dark" for="chk_ifsc">
                                        <i class="fas fa-university text-primary mr-1"></i> Bank IFSC Directory
                                    </label>
                                    <div class="small text-muted mt-1">IFSC code, bank name, branch, std code, city, state.</div>
                                </div>
                            </div>
                        </div>

                        <!-- MICR -->
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 bg-light h-100">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="endpoints[]" value="bank-micr" class="custom-control-input ep-chk" id="chk_micr" <?php echo (in_array('bank-micr', $current_eps) || $is_all_eps) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label font-weight-bold text-dark" for="chk_micr">
                                        <i class="fas fa-money-check text-info mr-1"></i> Bank MICR Directory
                                    </label>
                                    <div class="small text-muted mt-1">9-digit magnetic ink clearing codes for bank branches.</div>
                                </div>
                            </div>
                        </div>

                        <!-- PIN Code -->
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 bg-light h-100">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="endpoints[]" value="pincode" class="custom-control-input ep-chk" id="chk_pin" <?php echo (in_array('pincode', $current_eps) || $is_all_eps) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label font-weight-bold text-dark" for="chk_pin">
                                        <i class="fas fa-map-pin text-success mr-1"></i> Postal PIN Codes
                                    </label>
                                    <div class="small text-muted mt-1">6-digit PIN codes, post offices, taluk, district &amp; state.</div>
                                </div>
                            </div>
                        </div>

                        <!-- GST HSN/SAC -->
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 bg-light h-100">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="endpoints[]" value="hsn-sac" class="custom-control-input ep-chk" id="chk_hsn" <?php echo (in_array('hsn-sac', $current_eps) || $is_all_eps) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label font-weight-bold text-dark" for="chk_hsn">
                                        <i class="fas fa-receipt text-warning mr-1"></i> GST HSN &amp; SAC Codes
                                    </label>
                                    <div class="small text-muted mt-1">HSN commodity tariff codes and SAC services registry.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Passport -->
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 bg-light h-100">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="endpoints[]" value="passport" class="custom-control-input ep-chk" id="chk_passport" <?php echo (in_array('passport', $current_eps) || $is_all_eps) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label font-weight-bold text-dark" for="chk_passport">
                                        <i class="fas fa-passport text-secondary mr-1"></i> Passport Seva Kendras
                                    </label>
                                    <div class="small text-muted mt-1">Regional passport offices (RPO) and PSK locator.</div>
                                </div>
                            </div>
                        </div>

                        <!-- MCA Companies -->
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 bg-light h-100">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="endpoints[]" value="company" class="custom-control-input ep-chk" id="chk_company" <?php echo (in_array('company', $current_eps) || $is_all_eps) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label font-weight-bold text-dark" for="chk_company">
                                        <i class="fas fa-building text-primary mr-1"></i> MCA Companies &amp; LLPs
                                    </label>
                                    <div class="small text-muted mt-1">Corporate registry, CIN, capital, ROC, and status.</div>
                                </div>
                            </div>
                        </div>

                        <!-- NIC Codes -->
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 bg-light h-100">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="endpoints[]" value="nic" class="custom-control-input ep-chk" id="chk_nic" <?php echo (in_array('nic', $current_eps) || $is_all_eps) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label font-weight-bold text-dark" for="chk_nic">
                                        <i class="fas fa-sitemap text-danger mr-1"></i> NIC Industrial Codes
                                    </label>
                                    <div class="small text-muted mt-1">National Industrial Classification business activities.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Security Firewall & Quota Rules -->
            <div class="section-card p-4 mb-4">
                <h5 class="font-weight-bold text-dark mb-3">
                    <i class="fas fa-shield-virus text-primary mr-2"></i> Firewall &amp; Rate Limit Restrictions
                </h5>
                
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">IP Address Whitelist</label>
                        <input type="text" name="allowed_ips" class="form-control font-monospace" value="<?php echo htmlspecialchars($key_data['allowed_ips'] ?? ''); ?>" placeholder="e.g. 103.21.244.2, 192.168.1.5">
                        <small class="text-muted">Comma-separated IPv4/IPv6. Leave blank to allow all IPs.</small>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Allowed Origin Domains</label>
                        <input type="text" name="allowed_domains" class="form-control font-monospace" value="<?php echo htmlspecialchars($key_data['allowed_domains'] ?? ''); ?>" placeholder="e.g. clientapp.com, portal.partner.in">
                        <small class="text-muted">Comma-separated domains for CORS/Referer protection.</small>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Daily Request Limit (24 Hours)</label>
                        <div class="input-group">
                            <input type="number" name="rate_limit_per_day" class="form-control font-weight-bold" value="<?php echo intval($key_data['rate_limit_per_day'] ?? 0); ?>" min="0">
                            <div class="input-group-append">
                                <span class="input-group-text small">calls / day</span>
                            </div>
                        </div>
                        <small class="text-muted">Set 0 for unlimited daily quota.</small>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-muted mb-1">Per-Minute Throttle (RPM)</label>
                        <div class="input-group">
                            <input type="number" name="rate_limit_per_min" class="form-control font-weight-bold" value="<?php echo intval($key_data['rate_limit_per_min'] ?? 0); ?>" min="0">
                            <div class="input-group-append">
                                <span class="input-group-text small">calls / min</span>
                            </div>
                        </div>
                        <small class="text-muted">Set 0 for unlimited RPM.</small>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex gap-2 mb-4" style="gap: 10px;">
                <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold shadow-sm" style="border-radius: var(--radius-sm);">
                    <i class="fas fa-save mr-1"></i> <?php echo $is_edit ? 'Save Changes' : 'Create API Key'; ?>
                </button>
                <a href="manage_api_keys.php" class="btn btn-outline-secondary px-4 py-2 font-weight-bold" style="border-radius: var(--radius-sm);">
                    Cancel
                </a>
            </div>
        </div>

        <!-- Sidebar / Status Column -->
        <div class="col-lg-4 mb-4">
            <!-- Key Status & Validity Card -->
            <div class="section-card p-4 mb-4">
                <h5 class="font-weight-bold text-dark mb-3">
                    <i class="fas fa-toggle-on text-primary mr-2"></i> Key Status &amp; Validity
                </h5>

                <div class="form-group mb-3">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="is_active" class="custom-control-input" id="is_active_toggle" <?php echo (isset($key_data['is_active']) && intval($key_data['is_active']) === 1) ? 'checked' : ''; ?>>
                        <label class="custom-control-label font-weight-bold text-dark" for="is_active_toggle">
                            Key Active &amp; Authorized
                        </label>
                    </div>
                    <small class="text-muted d-block mt-1">If unchecked, all incoming requests with this key are rejected with 403.</small>
                </div>

                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-muted mb-1">Key Expiration Date</label>
                    <input type="date" name="valid_until" class="form-control" value="<?php echo htmlspecialchars($key_data['valid_until'] ?? ''); ?>">
                    <small class="text-muted">Leave blank for Lifetime (No Expiry)</small>
                </div>

                <?php if ($is_edit): ?>
                    <hr>
                    <div class="small">
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Total Invocations:</span>
                            <span class="font-weight-bold text-dark"><?php echo number_format($key_data['usage_count'] ?? 0); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Last Activity:</span>
                            <span class="font-weight-bold text-dark"><?php echo !empty($key_data['last_used_at']) ? date('d M Y, H:i', strtotime($key_data['last_used_at'])) : 'Never'; ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Created:</span>
                            <span class="text-dark"><?php echo date('d M Y', strtotime($key_data['created_at'])); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Code Integration Preview Snippets -->
            <div class="section-card p-4">
                <h5 class="font-weight-bold text-dark mb-3">
                    <i class="fas fa-code text-primary mr-2"></i> Ready-to-use Code Snippet
                </h5>
                <p class="text-muted small mb-2">Developers can query this API using standard cURL or Javascript:</p>

                <div class="bg-dark p-3 rounded font-monospace small text-light mb-3" style="overflow-x: auto; font-size: 0.78rem;">
curl -X GET "<?php echo $base_url; ?>api.php?action=bank-ifsc&ifsc=SBIN0000001&api_key=<span id="snippet_key"><?php echo htmlspecialchars($key_data['api_key']); ?></span>"
                </div>

                <div class="bg-dark p-3 rounded font-monospace small text-light" style="overflow-x: auto; font-size: 0.78rem;">
fetch('<?php echo $base_url; ?>api.php?action=pincode&pincode=110001', {
  headers: {
    'X-API-Key': '<span id="snippet_key_js"><?php echo htmlspecialchars($key_data['api_key']); ?></span>'
  }
})
.then(res => res.json())
.then(data => console.log(data));
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once('includes/footer.php'); ?>

<script>
function generateRandomKey() {
    var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var key = 'OLAW_';
    for (var i = 0; i < 20; i++) {
        key += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    $('#api_key_input').val(key);
    $('#snippet_key').text(key);
    $('#snippet_key_js').text(key);
}

function copyKeyText() {
    var key = $('#api_key_input').val();
    navigator.clipboard.writeText(key).then(function() {
        alert('API Key copied to clipboard: ' + key);
    });
}

function toggleScopeRadio() {
    var isAll = $('#scope_all').is(':checked');
    if (isAll) {
        $('#custom_endpoints_container').slideUp(200);
    } else {
        $('#custom_endpoints_container').slideDown(200);
    }
}

function selectAllEndpoints(val) {
    $('#scope_custom').prop('checked', true);
    $('#custom_endpoints_container').slideDown(200);
    $('.ep-chk').prop('checked', val);
}

$(document).ready(function() {
    $('#api_key_input').on('input', function() {
        var val = $(this).val();
        $('#snippet_key').text(val);
        $('#snippet_key_js').text(val);
    });
});
</script>
