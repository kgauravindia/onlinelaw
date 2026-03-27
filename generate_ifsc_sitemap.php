<?php
require_once('includes/config.php');
require_once('includes/function.php');

// Fetch all UNIQUE IFSC codes
$sql = "SELECT DISTINCT ifsc FROM bank_ifsc WHERE ifsc IS NOT NULL AND ifsc != ''";
$res = direct_sql($sql);

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

if($res['count'] > 0) {
    foreach($res['data'] as $row) {
        $ifsc = $row['ifsc'];
        // Use the dynamically configured site URL
        $url = rtrim($inst_url, '/') . "/bank-ifsc/" . $ifsc;
        $xml .= "\t<url>\n\t\t<loc>".htmlspecialchars($url)."</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";
    }
}

$xml .= '</urlset>';

// Save to root directory
file_put_contents('sitemap-ifsc.xml', $xml);

echo "Success! Generated sitemap-ifsc.xml with " . $res['count'] . " unique IFSC codes.\n";
?>
