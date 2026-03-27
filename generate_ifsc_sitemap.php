<?php
require_once('includes/config.php');
require_once('includes/function.php');

// Increase memory limit for large data processing
ini_set('memory_limit', '1024M');

// Fetch all UNIQUE IFSC codes
$sql = "SELECT DISTINCT ifsc FROM bank_ifsc WHERE ifsc IS NOT NULL AND ifsc != ''";
$res = direct_sql($sql);

if($res['count'] > 0) {
    $total_urls = $res['count'];
    $chunk_size = 40000; // Safer than the 50,000 limit
    $chunks = array_chunk($res['data'], $chunk_size);
    $sitemaps = [];

    foreach($chunks as $index => $chunk) {
        $part = $index + 1;
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
        foreach($chunk as $row) {
            $ifsc = $row['ifsc'];
            $url = rtrim($inst_url, '/') . "/bank-ifsc/" . $ifsc;
            $xml .= "\t<url>\n\t\t<loc>".htmlspecialchars($url)."</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";
        }
        $xml .= '</urlset>';
        $filename = "sitemap-ifsc-part$part.xml";
        file_put_contents($filename, $xml);
        $sitemaps[] = $filename;
        echo "Generated $filename with " . count($chunk) . " URLs.\n";
    }

    // Generate Sitemap Index
    $xml_index = '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
    foreach($sitemaps as $map) {
        $map_url = rtrim($inst_url, '/') . "/" . $map;
        $xml_index .= "\t<sitemap>\n\t\t<loc>".htmlspecialchars($map_url)."</loc>\n\t\t<lastmod>".date('Y-m-d')."</lastmod>\n\t</sitemap>\n";
    }
    $xml_index .= '</sitemapindex>';
    file_put_contents('sitemap-ifsc.xml', $xml_index);
    echo "Summary! Generated sitemap-ifsc.xml (Index) + " . count($sitemaps) . " part files.\n";

} else {
    echo "No IFSC records found.\n";
}
?>
