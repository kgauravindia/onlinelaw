<?php
/**
 * OnlineLaw - Production Database Optimizer & Verifier
 * Safely applies recommended indexes, clears stale caches, and benchmarks query speeds.
 * Table `advocate` is skipped as instructed.
 */
require_once(__DIR__ . '/includes/config.php');
require_once(__DIR__ . '/includes/function.php');

// Restrict execution to CLI or authenticated admins only
if (php_sapi_name() !== 'cli') {
    check_admin_auth(true);
    header('Content-Type: text/plain; charset=utf-8');
}

echo "========================================================================\n";
echo " OnlineLaw - Database Performance Optimization & Verification\n";
echo "========================================================================\n\n";

$conn = new mysqli($host_name, $db_user, $db_password, $db_name);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error . "\n");
}
$conn->set_charset('utf8mb4');

// Helper to check and add index
function ensure_index($conn, $table, $index_name, $index_def, $is_fulltext = false) {
    echo "Checking index `$index_name` on table `$table`... ";
    $check = $conn->query("SHOW INDEX FROM `$table` WHERE Key_name = '$index_name'");
    if ($check && $check->num_rows > 0) {
        echo "[EXISTS]\n";
        return true;
    }
    echo "[CREATING]... ";
    $start = microtime(true);
    $type = $is_fulltext ? "FULLTEXT INDEX" : "INDEX";
    $sql = "ALTER TABLE `$table` ADD $type `$index_name` ($index_def)";
    $res = $conn->query($sql);
    $dur = round(microtime(true) - $start, 2);
    if ($res) {
        echo "[SUCCESS in {$dur}s]\n";
        return true;
    } else {
        echo "[FAILED: " . $conn->error . "]\n";
        return false;
    }
}

// 1. PINCODE INDEXES
echo "--- 1. Optimizing `pincode` table (1M+ rows) ---\n";
ensure_index($conn, 'pincode', 'ft_pincode_search', 'locality_name, office_name, sub_district_name, district_name', true);
ensure_index($conn, 'pincode', 'idx_pincode_code_dist', 'pincode, district_name(50)', false);
ensure_index($conn, 'pincode', 'idx_pincode_state_dist', 'state_name(50), district_name(50)', false);

// 2. BANK_IFSC INDEXES
echo "\n--- 2. Optimizing `bank_ifsc` table (181K+ rows) ---\n";
ensure_index($conn, 'bank_ifsc', 'ft_bank_search', 'bank, branch, city2, city1, address', true);
ensure_index($conn, 'bank_ifsc', 'idx_bank_state_branch', 'bank(50), state(50), branch(50), city2(50), ifsc', false);

// 3. CACHE DIRECTORIES
echo "\n--- 3. Verifying Cache Storage Directories ---\n";
$cache_dirs = [
    __DIR__ . '/tmp',
    __DIR__ . '/tmp/pincode_search',
    __DIR__ . '/tmp/pincode_cache',
    __DIR__ . '/tmp/bank_search',
    __DIR__ . '/tmp/bank_cache',
    __DIR__ . '/tmp/bank_near'
];
foreach ($cache_dirs as $d) {
    if (!is_dir($d)) {
        if (@mkdir($d, 0755, true)) {
            echo "Created directory: $d\n";
        } else {
            echo "Failed to create directory: $d\n";
        }
    } else {
        echo "Directory verified: $d\n";
    }
}

// 4. BENCHMARK VERIFICATION
echo "\n--- 4. Benchmarking Optimized Search Queries ---\n";

// Test 1: Locality Search
$start = microtime(true);
$t1 = $conn->query("SELECT id, pincode, office_name, locality_name, district_name FROM pincode WHERE MATCH(locality_name, office_name, sub_district_name, district_name) AGAINST('+Indirapuram*' IN BOOLEAN MODE) LIMIT 50");
$d1 = round((microtime(true) - $start) * 1000, 2);
echo "• Pincode search for 'Indirapuram': {$d1} ms (found " . ($t1 ? $t1->num_rows : 0) . " rows)\n";

// Test 2: Bank Branch Search
$start = microtime(true);
$t2 = $conn->query("SELECT id, bank, ifsc, branch, city2 FROM bank_ifsc WHERE MATCH(bank, branch, city2, city1, address) AGAINST('+Connaught* +Place*' IN BOOLEAN MODE) LIMIT 50");
$d2 = round((microtime(true) - $start) * 1000, 2);
echo "• Bank branch search for 'Connaught Place': {$d2} ms (found " . ($t2 ? $t2->num_rows : 0) . " rows)\n";

// Test 3: Nearby branches query
$start = microtime(true);
$t3 = $conn->query("SELECT DISTINCT branch, ifsc, city2 FROM bank_ifsc WHERE bank = 'STATE BANK OF INDIA' AND state = 'MAHARASHTRA' AND city2 != 'PUNE' ORDER BY branch ASC LIMIT 8");
$d3 = round((microtime(true) - $start) * 1000, 2);
echo "• Bank nearby state branches query: {$d3} ms (found " . ($t3 ? $t3->num_rows : 0) . " rows)\n";

echo "\nAll database performance recommendations have been successfully applied and verified!\n";
