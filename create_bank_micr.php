<?php
$content = file_get_contents('d:/laragon/www/onlinelaw/bank-ifsc.php');

$content = str_replace('ifsccode', 'micrcode', $content);
$content = str_replace('$ifsccode', '$micrcode', $content); // might be redundant but safe
$content = str_replace('IFSC Code', 'MICR Code', $content);
$content = str_replace('IFSC', 'MICR', $content); // Be careful this changes uppercase IFSC to MICR
$content = str_replace('ifsc=', 'micr=', $content);
$content = str_replace('bank-ifsc?', 'bank-micr?', $content);
$content = str_replace('bank-ifsc"', 'bank-micr"', $content);
$content = str_replace("where micr ='\$micrcode'", "where micr ='\$micrcode'", $content); // check

// We need to fix the case where we might have replaced `bank_ifsc` table name with `bank_micr`.
$content = str_replace('bank_micr', 'bank_ifsc', $content);

// And we might have replaced `bank-ifsc.php` with `bank-micr.php` in comments which is fine.

file_put_contents('d:/laragon/www/onlinelaw/bank-micr.php', $content);
echo "Created bank-micr.php\n";
