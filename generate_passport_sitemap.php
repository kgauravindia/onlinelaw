<?php
require_once('includes/config.php');
require_once('includes/function.php');

// 1. Get ALL unique Passport Seva Offices
$sql = "SELECT DISTINCT name, rpo FROM passport WHERE name IS NOT NULL AND name != '' ORDER BY name ASC";
$res = direct_sql($sql);

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

// Main Passport Finder tool
$base = rtrim($inst_url, '/') . '/';
$xml .= "\t<url>\n\t\t<loc>".$base."passport</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>1.0</priority>\n\t</url>\n";

// 2. Add individual PSK searches
if($res['count'] > 0) {
    foreach($res['data'] as $row) {
        $name = $row['name'];
        $url = $base . "passport?q=" . urlencode($name);
        $xml .= "\t<url>\n\t\t<loc>".htmlspecialchars($url)."</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.6</priority>\n\t</url>\n";
    }
}

// 3. Add RPO specific searches (37 RPOs)
$sql_rpo = "SELECT DISTINCT rpo FROM passport WHERE rpo IS NOT NULL AND rpo != '' ORDER BY rpo ASC";
$res_rpo = direct_sql($sql_rpo);
if($res_rpo['count'] > 0) {
    foreach($res_rpo['data'] as $row) {
        $rpo = $row['rpo'];
        $url = $base . "passport?q=" . urlencode($rpo);
        $xml .= "\t<url>\n\t\t<loc>".htmlspecialchars($url)."</loc>\n\t\t<changefreq>weekly</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";
    }
}

$xml .= '</urlset>';

file_put_contents('sitemap-passport.xml', $xml);
echo "Success! Generated sitemap-passport.xml with " . ($res['count'] + $res_rpo['count'] + 1) . " URLs.\n";
?>
