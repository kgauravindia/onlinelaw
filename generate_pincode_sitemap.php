<?php
require_once('includes/config.php');
require_once('includes/function.php');

$sql = "SELECT DISTINCT pincode FROM pincode WHERE pincode IS NOT NULL AND pincode != ''";
$res = direct_sql($sql);

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

if($res['count'] > 0) {
    foreach($res['data'] as $row) {
        $pin = $row['pincode'];
        $url = rtrim($inst_url, '/') . "/pincode/" . $pin;
        $xml .= "\t<url>\n\t\t<loc>".htmlspecialchars($url)."</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";
    }
}

$xml .= '</urlset>';

file_put_contents('sitemap-pincode.xml', $xml);
echo "Success! Generated sitemap-pincode.xml with " . $res['count'] . " unique PIN codes.\n";
?>
