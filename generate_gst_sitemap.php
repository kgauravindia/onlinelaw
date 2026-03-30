<?php
require_once('includes/config.php');
require_once('includes/function.php');

// Increase memory limit for large data processing
ini_set('memory_limit', '1024M');

// 1. Fetch ALL unique HSN/SAC codes
$sql = "SELECT DISTINCT code FROM hsnsac WHERE code IS NOT NULL AND code != ''";
$res = direct_sql($sql);

if($res['count'] > 0) {
    $total_urls = $res['count'];
    $chunk_size = 40000;
    $chunks = array_chunk($res['data'], $chunk_size);
    $sitemaps = [];

    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'olaw.in';
    $base = rtrim($inst_url, '/') . '/';

    foreach($chunks as $index => $chunk) {
        $part = $index + 1;
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
        // Add the main tool page to the first part
        if($part == 1) {
            $xml .= "\t<url>\n\t\t<loc>".$base."gst-hsn-sac</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>1.0</priority>\n\t</url>\n";
        }

        foreach($chunk as $row) {
            $code = $row['code'];
            // Clean URL mapping available in .htaccess potentially, or just use query string if not sure.
            // Check .htaccess for GST mapping... wait, I didn't see one for GST.
            // I'll use the standard query format but make it look nice.
            $url = $base . "gst-hsn-sac?q=" . urlencode($code);
            $xml .= "\t<url>\n\t\t<loc>".htmlspecialchars($url)."</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.6</priority>\n\t</url>\n";
        }
        $xml .= '</urlset>';
        $filename = "sitemap-gst-part$part.xml";
        file_put_contents($filename, $xml);
        $sitemaps[] = $filename;
        echo "Generated $filename with " . count($chunk) . " URLs.\n";
    }

    // Generate Sitemap Index
    $xml_index = '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
    foreach($sitemaps as $map) {
        $map_url = $base . $map;
        $xml_index .= "\t<sitemap>\n\t\t<loc>".htmlspecialchars($map_url)."</loc>\n\t\t<lastmod>".date('Y-m-d')."</lastmod>\n\t</sitemap>\n";
    }
    $xml_index .= '</sitemapindex>';
    file_put_contents('sitemap-gst.xml', $xml_index);
    echo "Summary! Generated sitemap-gst.xml (Index) + " . count($sitemaps) . " part files.\n";

} else {
    echo "No GST HSN/SAC records found.\n";
}
?>
