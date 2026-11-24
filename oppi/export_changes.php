<?php
/**
 * OPPI Admin - Database Changes SQL Exporter
 * Allows administrators to export incremental table changes (new and modified records)
 * filtered by date range (from date X to date Y) as downloadable .SQL files.
 */

require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../includes/security.php');
require_once(__DIR__ . '/../includes/function.php');

// Enforce Admin Authentication
$current_admin = check_admin_auth(true);

// Helper: Inspect table schema for date/time columns
function get_table_date_meta($con, $table) {
    $table_clean = mysqli_real_escape_string($con, $table);
    $res = mysqli_query($con, "SHOW COLUMNS FROM `$table_clean`");
    if (!$res) return [];
    $cols = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $field = $row['Field'];
        $type = strtolower($row['Type']);
        $f_lower = strtolower($field);
        $is_date = (
            strpos($type, 'datetime') !== false ||
            strpos($type, 'timestamp') !== false ||
            strpos($type, 'date') !== false ||
            in_array($f_lower, ['updated_at', 'created_at', 'registration_date', 'date', 'timestamp', 'modified_at', 'valid_until', 'last_login', 'last_used_at'])
        );
        if ($is_date) {
            $cols[$field] = $type;
        }
    }
    return $cols;
}

// Helper: Build WHERE clause for date filtering
function build_table_where_clause($table, $date_cols, $date_criteria, $from_esc, $to_esc, $include_nodate = false) {
    if (empty($date_cols)) {
        return $include_nodate ? '1' : '0';
    }

    $clauses = [];
    $make_col_clause = function($col, $type) use ($from_esc, $to_esc) {
        if (strpos($type, 'date') !== false && strpos($type, 'datetime') === false) {
            return "(`$col` >= '$from_esc' AND `$col` <= '$to_esc')";
        } else {
            return "(`$col` >= '$from_esc 00:00:00' AND `$col` <= '$to_esc 23:59:59')";
        }
    };

    if ($date_criteria === 'created_only') {
        if (isset($date_cols['created_at'])) {
            $clauses[] = $make_col_clause('created_at', $date_cols['created_at']);
        } elseif (isset($date_cols['registration_date'])) {
            $clauses[] = $make_col_clause('registration_date', $date_cols['registration_date']);
        } elseif (isset($date_cols['date'])) {
            $clauses[] = $make_col_clause('date', $date_cols['date']);
        } else {
            $first = array_key_first($date_cols);
            $clauses[] = $make_col_clause($first, $date_cols[$first]);
        }
    } elseif ($date_criteria === 'updated_only') {
        if (isset($date_cols['updated_at'])) {
            $clauses[] = $make_col_clause('updated_at', $date_cols['updated_at']);
        } elseif (isset($date_cols['modified_at'])) {
            $clauses[] = $make_col_clause('modified_at', $date_cols['modified_at']);
        } else {
            if (isset($date_cols['created_at'])) {
                $clauses[] = $make_col_clause('created_at', $date_cols['created_at']);
            }
        }
    } elseif ($date_criteria === 'all_dates') {
        foreach ($date_cols as $c => $t) {
            $clauses[] = $make_col_clause($c, $t);
        }
    } else {
        // Default: 'updated_or_created'
        if (isset($date_cols['updated_at'])) {
            $clauses[] = $make_col_clause('updated_at', $date_cols['updated_at']);
        }
        if (isset($date_cols['created_at'])) {
            $clauses[] = $make_col_clause('created_at', $date_cols['created_at']);
        }
        if (empty($clauses) && isset($date_cols['registration_date'])) {
            $clauses[] = $make_col_clause('registration_date', $date_cols['registration_date']);
        }
        if (empty($clauses)) {
            $first = array_key_first($date_cols);
            $clauses[] = $make_col_clause($first, $date_cols[$first]);
        }
    }

    if (empty($clauses)) {
        return $include_nodate ? '1' : '0';
    }

    return '(' . implode(' OR ', array_unique($clauses)) . ')';
}

// Fetch all database tables
$all_tables = [];
$show_tables_res = mysqli_query($con, "SHOW TABLES");
if ($show_tables_res) {
    while ($r = mysqli_fetch_row($show_tables_res)) {
        $all_tables[] = $r[0];
    }
}

// Known table friendly metadata
$table_meta_map = [
    'companies_incorporated' => [
        'title' => 'Companies Incorporated (MCA)',
        'icon' => 'fas fa-building',
        'desc' => 'MCA corporate entity registry (Companies & LLPs)',
        'group' => 'core'
    ],
    'bank_ifsc' => [
        'title' => 'Bank IFSC & MICR Directory',
        'icon' => 'fas fa-university',
        'desc' => 'Official RBI IFSC, MICR, and branch routing details',
        'group' => 'core'
    ],
    'pincode' => [
        'title' => 'Postal PIN Codes',
        'icon' => 'fas fa-map-marked-alt',
        'desc' => 'India Post pin codes, offices, talukas, districts',
        'group' => 'core'
    ],
    'acts' => [
        'title' => 'Bare Acts Registry',
        'icon' => 'fas fa-book',
        'desc' => 'Statutes, bare acts, sections, and legal acts',
        'group' => 'core'
    ],
    'contact' => [
        'title' => 'Helpdesk Inquiries & Delisting',
        'icon' => 'fas fa-envelope-open-text',
        'desc' => 'User messages, delisting requests, and contact inquiries',
        'group' => 'core'
    ],
    'api_keys' => [
        'title' => 'API Access Keys',
        'icon' => 'fas fa-key',
        'desc' => 'Client API authorization credentials and permissions',
        'group' => 'api'
    ],
    'api_logs' => [
        'title' => 'API Usage Logs',
        'icon' => 'fas fa-list-alt',
        'desc' => 'API access request records and audit logs',
        'group' => 'api'
    ],
    'fi' => [
        'title' => 'Financial Institutions',
        'icon' => 'fas fa-landmark',
        'desc' => 'Registered financial institutions and entities',
        'group' => 'aux'
    ],
    'hsnsac' => [
        'title' => 'HSN & SAC Codes',
        'icon' => 'fas fa-tags',
        'desc' => 'GST goods & services tariff codes directory',
        'group' => 'aux'
    ],
    'nic' => [
        'title' => 'NIC Industry Classifications',
        'icon' => 'fas fa-industry',
        'desc' => 'National Industrial Classification codes and titles',
        'group' => 'aux'
    ],
    'op_user' => [
        'title' => 'Platform Users',
        'icon' => 'fas fa-users-cog',
        'desc' => 'Admin users, credentials, and access roles',
        'group' => 'admin'
    ],
    'passport' => [
        'title' => 'Passport Offices Directory',
        'icon' => 'fas fa-passport',
        'desc' => 'Regional passport offices and PSK centres',
        'group' => 'aux'
    ],
    'site_settings' => [
        'title' => 'Site Settings & Analytics',
        'icon' => 'fas fa-sliders-h',
        'desc' => 'Global site configurations and analytics settings',
        'group' => 'admin'
    ]
];

// ---------------------------------------------------------------------------
// 1. AJAX Action: Preview Record Counts
// ---------------------------------------------------------------------------
if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'preview_counts') {
    header('Content-Type: application/json; charset=utf-8');

    $from_input = trim($_REQUEST['from_date'] ?? '');
    $to_input = trim($_REQUEST['to_date'] ?? '');
    $date_criteria = trim($_REQUEST['date_criteria'] ?? 'updated_or_created');
    $include_nodate = !empty($_REQUEST['include_nodate']);
    $selected_tables = isset($_REQUEST['tables']) && is_array($_REQUEST['tables']) ? $_REQUEST['tables'] : $all_tables;

    $from_date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $from_input) ? $from_input : date('Y-m-d', strtotime('-30 days'));
    $to_date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_input) ? $to_input : date('Y-m-d');
    if ($from_date > $to_date) {
        $tmp = $from_date;
        $from_date = $to_date;
        $to_date = $tmp;
    }

    $from_esc = mysqli_real_escape_string($con, $from_date);
    $to_esc = mysqli_real_escape_string($con, $to_date);

    $results = [];
    $total_rows = 0;

    foreach ($selected_tables as $tbl) {
        if (!in_array($tbl, $all_tables, true)) continue;

        $date_meta = get_table_date_meta($con, $tbl);
        $where = build_table_where_clause($tbl, $date_meta, $date_criteria, $from_esc, $to_esc, $include_nodate);

        $cnt = 0;
        $q = "SELECT COUNT(*) as cnt FROM `$tbl` WHERE $where";
        $cres = mysqli_query($con, $q);
        if ($cres) {
            $cnt = (int)(mysqli_fetch_assoc($cres)['cnt'] ?? 0);
        }

        $total_rows += $cnt;
        $results[] = [
            'table' => $tbl,
            'title' => $table_meta_map[$tbl]['title'] ?? $tbl,
            'date_cols' => array_keys($date_meta),
            'count' => $cnt,
            'where' => $where
        ];
    }

    echo json_encode([
        'status' => 'success',
        'from_date' => $from_date,
        'to_date' => $to_date,
        'date_criteria' => $date_criteria,
        'total_rows' => $total_rows,
        'tables' => $results
    ]);
    exit;
}

// ---------------------------------------------------------------------------
// 2. Action: Download SQL File (Streamed direct to browser)
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'download_sql') {
    // Validate CSRF
    if (!validate_csrf_token($_POST['csrf_token'] ?? '', 'export_sql')) {
        die("Security validation failed. Please refresh the page and try again.");
    }

    $from_input = trim($_POST['from_date'] ?? '');
    $to_input = trim($_POST['to_date'] ?? '');
    $date_criteria = trim($_POST['date_criteria'] ?? 'updated_or_created');
    $sql_mode = trim($_POST['sql_mode'] ?? 'INSERT_IGNORE');
    $include_schema = !empty($_POST['include_schema']);
    $include_nodate = !empty($_POST['include_nodate']);
    $selected_tables = isset($_POST['tables']) && is_array($_POST['tables']) ? $_POST['tables'] : [];

    // Filter tables to valid whitelist
    $target_tables = [];
    foreach ($selected_tables as $tbl) {
        if (in_array($tbl, $all_tables, true)) {
            $target_tables[] = $tbl;
        }
    }

    if (empty($target_tables)) {
        die("Error: No tables were selected for export. Please select at least one table.");
    }

    // Sanitize Dates
    $from_date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $from_input) ? $from_input : date('Y-m-d', strtotime('-30 days'));
    $to_date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_input) ? $to_input : date('Y-m-d');
    if ($from_date > $to_date) {
        $tmp = $from_date;
        $from_date = $to_date;
        $to_date = $tmp;
    }

    $from_esc = mysqli_real_escape_string($con, $from_date);
    $to_esc = mysqli_real_escape_string($con, $to_date);

    // Map SQL Mode prefix
    $sql_verb = 'INSERT IGNORE INTO';
    if ($sql_mode === 'REPLACE') {
        $sql_verb = 'REPLACE INTO';
    } elseif ($sql_mode === 'INSERT') {
        $sql_verb = 'INSERT INTO';
    }

    // Set unlimited execution time and buffer streaming
    @set_time_limit(0);
    @ini_set('memory_limit', '512M');

    // Clean any prior output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Generate filename
    $filename = "onlinelaw_changes_" . $from_date . "_to_" . $to_date . ".sql";

    // HTTP Headers for direct download
    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Expires: 0');

    $out = fopen('php://output', 'w');

    // Header comments
    fwrite($out, "-- ==============================================================\n");
    fwrite($out, "-- OnlineLaw Database Changes SQL Export\n");
    fwrite($out, "-- ==============================================================\n");
    fwrite($out, "-- Date Range (From X to Y): " . $from_date . " to " . $to_date . "\n");
    fwrite($out, "-- Filter Criteria: " . htmlspecialchars($date_criteria) . "\n");
    fwrite($out, "-- SQL Mode: " . $sql_verb . "\n");
    fwrite($out, "-- Generated At: " . date('Y-m-d H:i:s') . "\n");
    fwrite($out, "-- Exported By Admin: " . ($_SESSION['username'] ?? 'Administrator') . "\n");
    fwrite($out, "-- Selected Tables (" . count($target_tables) . "): " . implode(', ', $target_tables) . "\n");
    fwrite($out, "-- ==============================================================\n\n");

    fwrite($out, "SET FOREIGN_KEY_CHECKS = 0;\n");
    fwrite($out, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n");
    fwrite($out, "SET time_zone = '+00:00';\n");
    fwrite($out, "SET NAMES utf8mb4;\n\n");

    $grand_total_exported = 0;
    $table_export_summary = [];

    foreach ($target_tables as $tbl) {
        $date_meta = get_table_date_meta($con, $tbl);
        $where = build_table_where_clause($tbl, $date_meta, $date_criteria, $from_esc, $to_esc, $include_nodate);

        fwrite($out, "-- -------------------------------------------------------------\n");
        fwrite($out, "-- Table: `$tbl`\n");
        fwrite($out, "-- -------------------------------------------------------------\n");

        // Optional schema
        if ($include_schema) {
            $schema_res = mysqli_query($con, "SHOW CREATE TABLE `$tbl`");
            if ($schema_res && $srow = mysqli_fetch_row($schema_res)) {
                $create_sql = preg_replace('/^CREATE TABLE/i', 'CREATE TABLE IF NOT EXISTS', $srow[1]);
                fwrite($out, $create_sql . ";\n\n");
            }
        }

        // Query data changes with unbuffered result for low memory usage
        $query = "SELECT * FROM `$tbl` WHERE $where";
        $data_res = mysqli_query($con, $query, MYSQLI_USE_RESULT);

        if (!$data_res) {
            fwrite($out, "-- Error querying `$tbl`: " . mysqli_error($con) . "\n\n");
            $table_export_summary[$tbl] = 0;
            continue;
        }

        // Fetch column headers
        $col_names = [];
        while ($finfo = mysqli_fetch_field($data_res)) {
            $col_names[] = '`' . $finfo->name . '`';
        }
        $cols_sql = implode(', ', $col_names);

        $row_count = 0;
        $batch = [];
        $batch_size = 100;

        while ($row = mysqli_fetch_row($data_res)) {
            $row_count++;
            $vals = [];
            foreach ($row as $val) {
                if ($val === null) {
                    $vals[] = 'NULL';
                } else {
                    $vals[] = "'" . mysqli_real_escape_string($con, $val) . "'";
                }
            }
            $batch[] = "(" . implode(', ', $vals) . ")";

            if (count($batch) >= $batch_size) {
                fwrite($out, $sql_verb . " `$tbl` ($cols_sql) VALUES\n" . implode(",\n", $batch) . ";\n");
                $batch = [];
                fflush($out);
            }
        }

        if (!empty($batch)) {
            fwrite($out, $sql_verb . " `$tbl` ($cols_sql) VALUES\n" . implode(",\n", $batch) . ";\n");
            $batch = [];
            fflush($out);
        }

        mysqli_free_result($data_res);

        if ($row_count === 0) {
            fwrite($out, "-- No modified or created records found for `$tbl` within selected date range.\n\n");
        } else {
            fwrite($out, "-- Exported $row_count rows for `$tbl`.\n\n");
        }

        $grand_total_exported += $row_count;
        $table_export_summary[$tbl] = $row_count;
    }

    fwrite($out, "SET FOREIGN_KEY_CHECKS = 1;\n\n");
    fwrite($out, "-- ==============================================================\n");
    fwrite($out, "-- Export Complete Summary\n");
    fwrite($out, "-- Total Records Exported: " . number_format($grand_total_exported) . "\n");
    foreach ($table_export_summary as $t => $c) {
        fwrite($out, "--   - $t: " . number_format($c) . " records\n");
    }
    fwrite($out, "-- ==============================================================\n");

    fclose($out);
    exit;
}

// ---------------------------------------------------------------------------
// 3. Render HTML UI
// ---------------------------------------------------------------------------
require_once(__DIR__ . '/includes/header.php');

$csrf_token = generate_csrf_token('export_sql');
$default_from = date('Y-m-d', strtotime('-30 days'));
$default_to = date('Y-m-d');
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1" style="gap: 8px;">
            <a href="manage_data.php" class="text-muted small text-decoration-none">
                <i class="fas fa-database mr-1"></i> Master Data Hub
            </a>
            <span class="text-muted small">/</span>
            <span class="text-primary small font-weight-bold">Export SQL Changes</span>
        </div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Export Database Changes in SQL</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">
            Select date range <strong>(From Date X to Date Y)</strong>, choose tables, and download incremental database changes as ready-to-run SQL files.
        </p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <a href="manage_data.php" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-arrow-left mr-1"></i> Back to Hub
        </a>
    </div>
</div>

<form id="exportForm" method="POST" action="export_changes.php">
    <input type="hidden" name="action" value="download_sql">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

    <!-- Date Range & Presets Card -->
    <div class="section-card p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
            <div class="d-flex align-items-center">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0" style="font-size: 1.05rem;">1. Select Date Range (From Date X to Date Y)</h5>
                    <p class="text-muted small mb-0">Records newly inserted or modified between these dates will be exported.</p>
                </div>
            </div>
            <span class="badge-premium badge-premium-primary">Step 1</span>
        </div>

        <!-- Quick Presets -->
        <div class="mb-3">
            <label class="font-weight-bold text-dark small mb-2 d-block">Quick Presets:</label>
            <div class="btn-group flex-wrap" role="group" aria-label="Date Presets">
                <button type="button" class="btn btn-sm btn-outline-secondary date-preset-btn" data-preset="today">Today</button>
                <button type="button" class="btn btn-sm btn-outline-secondary date-preset-btn" data-preset="yesterday">Yesterday</button>
                <button type="button" class="btn btn-sm btn-outline-secondary date-preset-btn" data-preset="last7">Last 7 Days</button>
                <button type="button" class="btn btn-sm btn-outline-primary date-preset-btn active" data-preset="last30">Last 30 Days</button>
                <button type="button" class="btn btn-sm btn-outline-secondary date-preset-btn" data-preset="this_month">This Month</button>
                <button type="button" class="btn btn-sm btn-outline-secondary date-preset-btn" data-preset="last_month">Last Month</button>
                <button type="button" class="btn btn-sm btn-outline-secondary date-preset-btn" data-preset="this_year">This Year</button>
                <button type="button" class="btn btn-sm btn-outline-secondary date-preset-btn" data-preset="all_time">All Time</button>
            </div>
        </div>

        <!-- Date Inputs Row -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="from_date" class="font-weight-bold text-dark small mb-1">
                    <i class="fas fa-calendar-day text-primary mr-1"></i> From Date (X):
                </label>
                <input type="date" class="form-control form-control-lg font-weight-bold" id="from_date" name="from_date" value="<?php echo htmlspecialchars($default_from); ?>" required style="border-radius: var(--radius-sm); font-size: 0.95rem;">
                <small class="text-muted">Start of the modification or creation window (00:00:00)</small>
            </div>
            <div class="col-md-6 mb-3">
                <label for="to_date" class="font-weight-bold text-dark small mb-1">
                    <i class="fas fa-calendar-check text-primary mr-1"></i> To Date (Y):
                </label>
                <input type="date" class="form-control form-control-lg font-weight-bold" id="to_date" name="to_date" value="<?php echo htmlspecialchars($default_to); ?>" required style="border-radius: var(--radius-sm); font-size: 0.95rem;">
                <small class="text-muted">End of the modification or creation window (23:59:59)</small>
            </div>
        </div>
    </div>

    <!-- Export Options & Modes Card -->
    <div class="section-card p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
            <div class="d-flex align-items-center">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                    <i class="fas fa-sliders-h"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0" style="font-size: 1.05rem;">2. Date Criteria &amp; SQL Generation Rules</h5>
                    <p class="text-muted small mb-0">Configure how records are matched and formatted into SQL statements.</p>
                </div>
            </div>
            <span class="badge-premium badge-premium-success">Step 2</span>
        </div>

        <div class="row">
            <!-- Filter Criteria -->
            <div class="col-lg-6 mb-3">
                <label class="font-weight-bold text-dark small mb-2 d-block">
                    <i class="fas fa-filter text-success mr-1"></i> Date Filter Target:
                </label>
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="crit_both" name="date_criteria" value="updated_or_created" class="custom-control-input" checked>
                    <label class="custom-control-label font-weight-bold text-dark" for="crit_both">
                        Modified OR Newly Created Records <span class="badge badge-primary ml-1" style="font-size: 10px;">Recommended</span>
                    </label>
                    <div class="text-muted small pl-4 ml-1">Matches <code>updated_at</code> OR <code>created_at</code> within the date range. Captures all updates and inserts.</div>
                </div>
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="crit_created" name="date_criteria" value="created_only" class="custom-control-input">
                    <label class="custom-control-label font-weight-bold text-dark" for="crit_created">
                        Newly Created Records Only
                    </label>
                    <div class="text-muted small pl-4 ml-1">Matches <code>created_at</code> or <code>registration_date</code> only. Ignores edits to older records.</div>
                </div>
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="crit_updated" name="date_criteria" value="updated_only" class="custom-control-input">
                    <label class="custom-control-label font-weight-bold text-dark" for="crit_updated">
                        Last Updated / Modified Records Only
                    </label>
                    <div class="text-muted small pl-4 ml-1">Matches <code>updated_at</code> only. Filters specifically for edited records.</div>
                </div>
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="crit_all" name="date_criteria" value="all_dates" class="custom-control-input">
                    <label class="custom-control-label font-weight-bold text-dark" for="crit_all">
                        Any Date Field
                    </label>
                    <div class="text-muted small pl-4 ml-1">Matches ANY date/timestamp column available in the table.</div>
                </div>
            </div>

            <!-- SQL Mode & Schema Options -->
            <div class="col-lg-6 mb-3">
                <label class="font-weight-bold text-dark small mb-2 d-block">
                    <i class="fas fa-file-code text-success mr-1"></i> SQL Statement Format:
                </label>
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="mode_ignore" name="sql_mode" value="INSERT_IGNORE" class="custom-control-input" checked>
                    <label class="custom-control-label font-weight-bold text-dark" for="mode_ignore">
                        <code>INSERT IGNORE INTO</code> <span class="badge badge-success ml-1" style="font-size: 10px;">Safest</span>
                    </label>
                    <div class="text-muted small pl-4 ml-1">Safely skips duplicate primary keys without throwing SQL syntax errors on import.</div>
                </div>
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="mode_replace" name="sql_mode" value="REPLACE" class="custom-control-input">
                    <label class="custom-control-label font-weight-bold text-dark" for="mode_replace">
                        <code>REPLACE INTO</code>
                    </label>
                    <div class="text-muted small pl-4 ml-1">Overwrites existing rows matching primary/unique keys with the newer exported data.</div>
                </div>
                <div class="custom-control custom-radio mb-3">
                    <input type="radio" id="mode_insert" name="sql_mode" value="INSERT" class="custom-control-input">
                    <label class="custom-control-label font-weight-bold text-dark" for="mode_insert">
                        <code>INSERT INTO</code> (Standard)
                    </label>
                    <div class="text-muted small pl-4 ml-1">Standard insert statement (aborts execution on duplicate key collision).</div>
                </div>

                <div class="border-top pt-3">
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" id="include_schema" name="include_schema" value="1">
                        <label class="custom-control-label font-weight-bold text-dark" for="include_schema">
                            Include <code>CREATE TABLE IF NOT EXISTS</code> statements
                        </label>
                        <div class="text-muted small pl-4 ml-1">Prepends table structure DDL before inserting data.</div>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="include_nodate" name="include_nodate" value="1" checked>
                        <label class="custom-control-label font-weight-bold text-dark" for="include_nodate">
                            Include tables without date columns (export all rows)
                        </label>
                        <div class="text-muted small pl-4 ml-1">Applies to tables like <code>passport</code> that lack timestamp columns.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Selection Grid Card -->
    <div class="section-card p-4 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 border-bottom pb-3">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #fef3c7; color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                    <i class="fas fa-database"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0" style="font-size: 1.05rem;">3. Select Database Tables to Export</h5>
                    <p class="text-muted small mb-0">Select individual tables or all tables. Click "Preview Record Counts" to inspect matching rows.</p>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center" style="gap: 8px;">
                <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold" id="btnSelectAll">
                    <i class="fas fa-check-double mr-1"></i> Select All
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold" id="btnDeselectAll">
                    <i class="fas fa-times mr-1"></i> Deselect All
                </button>
                <button type="button" class="btn btn-sm btn-outline-info font-weight-bold" id="btnSelectCore">
                    <i class="fas fa-star mr-1"></i> Core Datasets
                </button>
            </div>
        </div>

        <div class="row">
            <?php foreach ($all_tables as $tbl): 
                $meta = $table_meta_map[$tbl] ?? [
                    'title' => ucwords(str_replace('_', ' ', $tbl)),
                    'icon' => 'fas fa-table',
                    'desc' => "Table: $tbl",
                    'group' => 'other'
                ];
                $date_cols = get_table_date_meta($con, $tbl);
            ?>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="table-card p-3 h-100 border rounded" style="background: #ffffff; border-color: #e2e8f0 !important; transition: all 0.2s ease;">
                    <div class="custom-control custom-checkbox d-flex align-items-start">
                        <input type="checkbox" class="custom-control-input table-checkbox" id="tbl_<?php echo htmlspecialchars($tbl); ?>" name="tables[]" value="<?php echo htmlspecialchars($tbl); ?>" checked data-group="<?php echo htmlspecialchars($meta['group']); ?>">
                        <label class="custom-control-label w-100 font-weight-bold text-dark cursor-pointer" for="tbl_<?php echo htmlspecialchars($tbl); ?>" style="cursor: pointer;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-truncate mr-2" style="font-size: 0.95rem;">
                                    <i class="<?php echo htmlspecialchars($meta['icon']); ?> text-primary mr-1"></i>
                                    <?php echo htmlspecialchars($meta['title']); ?>
                                </span>
                                <span class="badge badge-light border text-monospace row-count-badge" id="badge_<?php echo htmlspecialchars($tbl); ?>" style="font-size: 11px;">
                                    Ready
                                </span>
                            </div>
                            <div class="text-muted small mb-1" style="font-size: 11px; font-weight: normal; line-height: 1.3;">
                                <?php echo htmlspecialchars($meta['desc']); ?>
                            </div>
                            <div class="d-flex flex-wrap gap-1 mt-1" style="gap: 4px;">
                                <span class="badge badge-light text-secondary border font-weight-normal text-monospace" style="font-size: 10px;">
                                    `<?php echo htmlspecialchars($tbl); ?>`
                                </span>
                                <?php if (!empty($date_cols)): ?>
                                    <?php foreach (array_slice(array_keys($date_cols), 0, 2) as $dc): ?>
                                    <span class="badge badge-primary-light text-primary font-weight-normal text-monospace" style="font-size: 10px; background: #eff6ff; padding: 2px 5px; border-radius: 4px;">
                                        <i class="fas fa-clock mr-1" style="font-size: 9px;"></i><?php echo htmlspecialchars($dc); ?>
                                    </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="badge badge-light text-muted font-weight-normal" style="font-size: 10px; background: #f8fafc; padding: 2px 5px; border-radius: 4px;">
                                        No date col (Full dump)
                                    </span>
                                <?php endif; ?>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Preview Results Panel (Hidden by default, shown when user clicks Preview) -->
    <div class="section-card p-4 mb-4 d-none" id="previewPanel">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 border-bottom pb-3">
            <div class="d-flex align-items-center">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                    <i class="fas fa-poll"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-0" style="font-size: 1.05rem;">Preview of Matching Records</h5>
                    <p class="text-muted small mb-0">Summary of records detected between <span id="previewDateRange" class="font-weight-bold text-dark"></span></p>
                </div>
            </div>
            <div>
                <span class="badge-premium badge-premium-primary" id="previewTotalBadge" style="font-size: 1rem; padding: 6px 14px;">
                    0 Total Records
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" id="previewTable">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 35%;">Table Name</th>
                        <th style="width: 25%;">Date Filter Field(s)</th>
                        <th style="width: 20%;" class="text-right">Matching Records</th>
                        <th style="width: 20%;" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="previewTableBody">
                    <!-- Populated via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Bar Sticky Footer Card -->
    <div class="section-card p-4 mb-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 2px solid #e2e8f0;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3" style="gap: 16px;">
            <div class="d-flex align-items-center">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-right: 14px;">
                    <i class="fas fa-file-export"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold mb-1" style="font-size: 1.1rem;">Ready to Export</h5>
                    <div class="text-muted small">
                        Selected: <span id="selectedCountDisplay" class="font-weight-bold text-primary"><?php echo count($all_tables); ?></span> tables
                        &bull; Streaming download direct to <code>.sql</code> file
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2" style="gap: 10px;">
                <button type="button" class="btn btn-outline-primary btn-lg font-weight-bold px-4" id="btnPreview">
                    <i class="fas fa-search-plus mr-1" id="previewSpinnerIcon"></i>
                    <span>Preview Record Counts</span>
                </button>
                <button type="submit" class="btn btn-primary btn-lg font-weight-bold px-4 shadow-sm" id="btnDownload">
                    <i class="fas fa-file-download mr-1"></i>
                    <span>Download Changes (.SQL)</span>
                </button>
            </div>
        </div>
    </div>
</form>

<script>
(function() {
    // -------------------------------------------------------------------------
    // Date Presets Handler
    // -------------------------------------------------------------------------
    var fromInput = document.getElementById('from_date');
    var toInput = document.getElementById('to_date');
    var presetButtons = document.querySelectorAll('.date-preset-btn');

    function formatDate(d) {
        var year = d.getFullYear();
        var month = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    presetButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            presetButtons.forEach(function(b) { b.classList.remove('active', 'btn-outline-primary'); b.classList.add('btn-outline-secondary'); });
            this.classList.add('active', 'btn-outline-primary');
            this.classList.remove('btn-outline-secondary');

            var preset = this.getAttribute('data-preset');
            var now = new Date();
            var from = new Date();
            var to = new Date();

            switch (preset) {
                case 'today':
                    from = new Date();
                    to = new Date();
                    break;
                case 'yesterday':
                    from.setDate(now.getDate() - 1);
                    to.setDate(now.getDate() - 1);
                    break;
                case 'last7':
                    from.setDate(now.getDate() - 7);
                    to = new Date();
                    break;
                case 'last30':
                    from.setDate(now.getDate() - 30);
                    to = new Date();
                    break;
                case 'this_month':
                    from = new Date(now.getFullYear(), now.getMonth(), 1);
                    to = new Date();
                    break;
                case 'last_month':
                    from = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                    to = new Date(now.getFullYear(), now.getMonth(), 0);
                    break;
                case 'this_year':
                    from = new Date(now.getFullYear(), 0, 1);
                    to = new Date();
                    break;
                case 'all_time':
                    from = new Date(2000, 0, 1);
                    to = new Date();
                    break;
            }

            fromInput.value = formatDate(from);
            toInput.value = formatDate(to);
        });
    });

    // -------------------------------------------------------------------------
    // Table Selection Checkboxes
    // -------------------------------------------------------------------------
    var tableCheckboxes = document.querySelectorAll('.table-checkbox');
    var selectedCountDisplay = document.getElementById('selectedCountDisplay');

    function updateSelectedCount() {
        var count = 0;
        tableCheckboxes.forEach(function(cb) {
            if (cb.checked) count++;
        });
        if (selectedCountDisplay) {
            selectedCountDisplay.textContent = count;
        }
    }

    tableCheckboxes.forEach(function(cb) {
        cb.addEventListener('change', updateSelectedCount);
    });

    document.getElementById('btnSelectAll').addEventListener('click', function() {
        tableCheckboxes.forEach(function(cb) { cb.checked = true; });
        updateSelectedCount();
    });

    document.getElementById('btnDeselectAll').addEventListener('click', function() {
        tableCheckboxes.forEach(function(cb) { cb.checked = false; });
        updateSelectedCount();
    });

    document.getElementById('btnSelectCore').addEventListener('click', function() {
        tableCheckboxes.forEach(function(cb) {
            var group = cb.getAttribute('data-group');
            cb.checked = (group === 'core' || group === 'admin');
        });
        updateSelectedCount();
    });

    // -------------------------------------------------------------------------
    // AJAX Preview Record Counts
    // -------------------------------------------------------------------------
    var btnPreview = document.getElementById('btnPreview');
    var previewPanel = document.getElementById('previewPanel');
    var previewTableBody = document.getElementById('previewTableBody');
    var previewDateRange = document.getElementById('previewDateRange');
    var previewTotalBadge = document.getElementById('previewTotalBadge');
    var previewSpinnerIcon = document.getElementById('previewSpinnerIcon');

    btnPreview.addEventListener('click', function() {
        var fromVal = fromInput.value;
        var toVal = toInput.value;
        var selectedTables = [];
        tableCheckboxes.forEach(function(cb) {
            if (cb.checked) selectedTables.push(cb.value);
        });

        if (selectedTables.length === 0) {
            alert('Please select at least one database table.');
            return;
        }

        var criteria = document.querySelector('input[name="date_criteria"]:checked').value;
        var includeNoDate = document.getElementById('include_nodate').checked ? 1 : 0;

        // Visual loading state
        btnPreview.disabled = true;
        previewSpinnerIcon.className = 'fas fa-spinner fa-spin mr-1';

        var postData = new URLSearchParams();
        postData.append('action', 'preview_counts');
        postData.append('from_date', fromVal);
        postData.append('to_date', toVal);
        postData.append('date_criteria', criteria);
        postData.append('include_nodate', includeNoDate);
        selectedTables.forEach(function(tbl) {
            postData.append('tables[]', tbl);
        });

        fetch('export_changes.php', {
            method: 'POST',
            body: postData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            btnPreview.disabled = false;
            previewSpinnerIcon.className = 'fas fa-search-plus mr-1';

            if (data.status !== 'success') {
                alert('Error loading preview.');
                return;
            }

            previewPanel.classList.remove('d-none');
            previewDateRange.textContent = data.from_date + ' to ' + data.to_date;
            previewTotalBadge.textContent = Number(data.total_rows).toLocaleString() + ' Total Records';

            // Reset table rows
            previewTableBody.innerHTML = '';

            data.tables.forEach(function(item) {
                // Update badge on card
                var cardBadge = document.getElementById('badge_' + item.table);
                if (cardBadge) {
                    if (item.count > 0) {
                        cardBadge.className = 'badge badge-success text-monospace font-weight-bold';
                        cardBadge.textContent = Number(item.count).toLocaleString() + ' rows';
                    } else {
                        cardBadge.className = 'badge badge-light border text-monospace text-muted';
                        cardBadge.textContent = '0 rows';
                    }
                }

                // Add to preview table
                var tr = document.createElement('tr');
                var colsDisplay = item.date_cols.length > 0 ? item.date_cols.join(', ') : 'None (Full table)';
                var statusBadge = item.count > 0 ?
                    '<span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i>Has Changes</span>' :
                    '<span class="badge badge-light text-muted border px-2 py-1">No Changes</span>';

                tr.innerHTML = 
                    '<td><strong>' + item.title + '</strong> <span class="text-muted small text-monospace">(`' + item.table + '`)</span></td>' +
                    '<td><span class="text-monospace small text-primary">' + colsDisplay + '</span></td>' +
                    '<td class="text-right font-weight-bold text-monospace ' + (item.count > 0 ? 'text-success' : 'text-muted') + '">' + Number(item.count).toLocaleString() + '</td>' +
                    '<td class="text-center">' + statusBadge + '</td>';

                previewTableBody.appendChild(tr);
            });

            // Smooth scroll to preview panel
            previewPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        })
        .catch(function(err) {
            btnPreview.disabled = false;
            previewSpinnerIcon.className = 'fas fa-search-plus mr-1';
            alert('An error occurred while fetching preview counts.');
            console.error(err);
        });
    });

    updateSelectedCount();
})();
</script>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
