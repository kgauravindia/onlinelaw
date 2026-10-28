<?php
require_once(__DIR__ . '/includes/config.php');
require_once(__DIR__ . '/includes/function.php');

// Restrict execution to CLI or authenticated admins only
if (php_sapi_name() !== 'cli') {
    check_admin_auth(true);
}

$source_file = __DIR__ . '/bank-ifsc.php';
$target_file = __DIR__ . '/bank-micr.php';

if (!file_exists($source_file)) {
    die("Source bank-ifsc.php file not found.\n");
}

$content = file_get_contents($source_file);

$content = str_replace('ifsccode', 'micrcode', $content);
$content = str_replace('$ifsccode', '$micrcode', $content);
$content = str_replace('IFSC Code', 'MICR Code', $content);
$content = str_replace('IFSC', 'MICR', $content);
$content = str_replace('ifsc=', 'micr=', $content);
$content = str_replace('bank-ifsc?', 'bank-micr?', $content);
$content = str_replace('bank-ifsc"', 'bank-micr"', $content);
$content = str_replace("where micr ='\$micrcode'", "where micr ='\$micrcode'", $content);
$content = str_replace('bank_micr', 'bank_ifsc', $content);

file_put_contents($target_file, $content);
echo "Successfully refreshed bank-micr.php\n";
?>