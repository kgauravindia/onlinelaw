<?php
require_once('includes/config.php');
require_once('includes/function.php');

// Restrict execution to CLI or authenticated admins only
if (php_sapi_name() !== 'cli') {
    check_admin_auth(true);
}

$conn = new mysqli($host_name, $db_user, $db_password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tables = ['bank_ifsc', 'pincode', 'acts'];
// Also let's find the GST table name
$result = $conn->query("SHOW TABLES");
if ($result) {
    while($row = $result->fetch_array()) {
        if(stripos($row[0], 'gst') !== false) {
            $tables[] = $row[0];
        }
    }
}

foreach($tables as $table) {
    echo "\n--- Table: $table ---\n";
    $result = $conn->query("SHOW COLUMNS FROM $table");
    if ($result) {
        while($row = $result->fetch_assoc()) {
            echo $row['Field'] . " - " . $row['Type'] . "\n";
        }
    } else {
        echo "Table $table not found or error: " . $conn->error . "\n";
    }
}
$conn->close();
?>