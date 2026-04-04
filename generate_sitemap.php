<?php
/**
 * Unified Sitemap Generator for Online Law
 * Consolidates generation of Main, PIN code, IFSC, GST, and Passport sitemaps.
 * Run this script via CLI or access it in browser to update all sitemaps.
 */

// Basic setup
require_once('includes/config.php');
require_once('includes/function.php');

// Increase limits for processing large datasets
ini_set('memory_limit', '1024M');
set_time_limit(600); // 10 minutes

$base = rtrim($inst_url, '/') . '/';
echo "Starting Sitemap Generation...\n";
echo "Base URL: $base\n\n";

// --- 1. MAIN SITEMAP ---
echo "Generating Main Sitemap...\n";
$main_pages = [
    '' => ['priority' => '1.0', 'changefreq' => 'daily'],
    'about' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'contact' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'api' => ['priority' => '0.7', 'changefreq' => 'monthly'],
    'privacy-policy' => ['priority' => '0.3', 'changefreq' => 'monthly'],
    'terms-and-conditions' => ['priority' => '0.3', 'changefreq' => 'monthly'],
    'disclaimer' => ['priority' => '0.3', 'changefreq' => 'monthly'],
    'pincode' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'pincode-by-locality' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'bank-ifsc' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'bank-ifsc-by-branch-name' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'gst-hsn-sac' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'passport' => ['priority' => '0.9', 'changefreq' => 'daily'],
];

$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($main_pages as $page => $meta) {
    $url = $base . $page;
    $xml .= "\t<url>\n";
    $xml .= "\t\t<loc>" . htmlspecialchars($url) . "</loc>\n";
    $xml .= "\t\t<changefreq>" . $meta['changefreq'] . "</changefreq>\n";
    $xml .= "\t\t<priority>" . $meta['priority'] . "</priority>\n";
    $xml .= "\t</url>\n";
}
$xml .= '</urlset>';
file_put_contents('sitemap-main.xml', $xml);
echo "✓ Generated sitemap-main.xml with " . count($main_pages) . " URLs.\n";


// --- 2. PINCODE SITEMAP ---
echo "Generating PIN Code Sitemap...\n";
$sql = "SELECT DISTINCT pincode FROM pincode WHERE pincode IS NOT NULL AND pincode != ''";
$res = direct_sql($sql);
if($res['count'] > 0) {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach($res['data'] as $row) {
        $pin = $row['pincode'];
        $url = $base . "pincode/" . $pin;
        $xml .= "\t<url>\n\t\t<loc>".htmlspecialchars($url)."</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";
    }
    $xml .= '</urlset>';
    file_put_contents('sitemap-pincode.xml', $xml);
    echo "✓ Generated sitemap-pincode.xml with " . $res['count'] . " URLs.\n";
}


// --- 3. PASSPORT SITEMAP ---
echo "Generating Passport Sitemap...\n";
$sql = "SELECT DISTINCT name FROM passport WHERE name IS NOT NULL AND name != '' ORDER BY name ASC";
$res = direct_sql($sql);
$sql_rpo = "SELECT DISTINCT rpo FROM passport WHERE rpo IS NOT NULL AND rpo != '' ORDER BY rpo ASC";
$res_rpo = direct_sql($sql_rpo);

$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
$xml .= "\t<url>\n\t\t<loc>".$base."passport</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>1.0</priority>\n\t</url>\n";

if($res['count'] > 0) {
    foreach($res['data'] as $row) {
        $name = $row['name'];
        $url = $base . "passport/" . urlencode($name);
        $xml .= "\t<url>\n\t\t<loc>".htmlspecialchars($url)."</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.6</priority>\n\t</url>\n";
    }
}
if($res_rpo['count'] > 0) {
    foreach($res_rpo['data'] as $row) {
        $rpo = $row['rpo'];
        $url = $base . "passport/" . urlencode($rpo);
        $xml .= "\t<url>\n\t\t<loc>".htmlspecialchars($url)."</loc>\n\t\t<changefreq>weekly</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";
    }
}
$xml .= '</urlset>';
file_put_contents('sitemap-passport.xml', $xml);
echo "✓ Generated sitemap-passport.xml with " . ($res['count'] + $res_rpo['count'] + 1) . " URLs.\n";


// --- 4. BANK IFSC SITEMAP (MULTI-PART) ---
echo "Generating Bank IFSC Sitemap (Multi-part)...\n";
$sql = "SELECT DISTINCT ifsc FROM bank_ifsc WHERE ifsc IS NOT NULL AND ifsc != ''";
$res = direct_sql($sql);
if($res['count'] > 0) {
    $chunk_size = 45000;
    $chunks = array_chunk($res['data'], $chunk_size);
    $sitemaps = [];

    foreach($chunks as $index => $chunk) {
        $part = $index + 1;
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach($chunk as $row) {
            $ifsc = $row['ifsc'];
            $url = $base . "bank-ifsc/" . $ifsc;
            $xml .= "\t<url>\n\t\t<loc>".htmlspecialchars($url)."</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.7</priority>\n\t</url>\n";
        }
        $xml .= '</urlset>';
        $filename = "sitemap-ifsc-part$part.xml";
        file_put_contents($filename, $xml);
        $sitemaps[] = $filename;
    }

    // Index for IFSC
    $xml_index = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml_index .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach($sitemaps as $map) {
        $map_url = $base . $map;
        $xml_index .= "\t<sitemap>\n\t\t<loc>".htmlspecialchars($map_url)."</loc>\n\t\t<lastmod>".date('Y-m-d')."</lastmod>\n\t</sitemap>\n";
    }
    $xml_index .= '</sitemapindex>';
    file_put_contents('sitemap-ifsc.xml', $xml_index);
    echo "✓ Generated sitemap-ifsc.xml (Index) + " . count($sitemaps) . " parts.\n";
}


// --- 5. GST HSN/SAC SITEMAP (MULTI-PART) ---
echo "Generating GST HSN/SAC Sitemap (Multi-part)...\n";
$sql = "SELECT DISTINCT code FROM hsnsac WHERE code IS NOT NULL AND code != ''";
$res = direct_sql($sql);
if($res['count'] > 0) {
    $chunk_size = 45000;
    $chunks = array_chunk($res['data'], $chunk_size);
    $sitemaps = [];

    foreach($chunks as $index => $chunk) {
        $part = $index + 1;
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        if($part == 1) {
            $xml .= "\t<url>\n\t\t<loc>".$base."gst-hsn-sac</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>1.0</priority>\n\t</url>\n";
        }

        foreach($chunk as $row) {
            $code = $row['code'];
            $url = $base . "gst-hsn-sac/" . urlencode($code);
            $xml .= "\t<url>\n\t\t<loc>".htmlspecialchars($url)."</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.6</priority>\n\t</url>\n";
        }
        $xml .= '</urlset>';
        $filename = "sitemap-gst-part$part.xml";
        file_put_contents($filename, $xml);
        $sitemaps[] = $filename;
    }

    // Index for GST
    $xml_index = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml_index .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach($sitemaps as $map) {
        $map_url = $base . $map;
        $xml_index .= "\t<sitemap>\n\t\t<loc>".htmlspecialchars($map_url)."</loc>\n\t\t<lastmod>".date('Y-m-d')."</lastmod>\n\t</sitemap>\n";
    }
    $xml_index .= '</sitemapindex>';
    file_put_contents('sitemap-gst.xml', $xml_index);
    echo "✓ Generated sitemap-gst.xml (Index) + " . count($sitemaps) . " parts.\n";
}

echo "\nAll sitemaps generated successfully!\n";
?>
