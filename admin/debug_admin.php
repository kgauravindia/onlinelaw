<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Step 1: PHP Version: " . phpversion() . "<br>\n";
echo "Step 2: __DIR__ is: " . __DIR__ . "<br>\n";

require_once(__DIR__ . '/../includes/config.php');
echo "Step 3: config.php loaded. Host: " . ($host ?? 'none') . "<br>\n";

require_once(__DIR__ . '/../includes/function.php');
echo "Step 4: function.php loaded. DB connected: " . ($con ? 'YES' : 'NO') . "<br>\n";

$u = direct_sql("SELECT COUNT(*) as c FROM op_user");
echo "Step 5: op_user count: " . ($u['data'][0]['c'] ?? 'ERROR: ' . mysqli_error($con)) . "<br>\n";

echo "Step 6: Calling check_admin_auth(false): ";
$auth = check_admin_auth(false);
var_dump($auth);
echo "<br>\n";

echo "Step 7: All checks passed without fatal error!<br>\n";
