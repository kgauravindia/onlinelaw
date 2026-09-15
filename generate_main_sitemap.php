<?php
require_once('includes/config.php');

$base = rtrim($inst_url, '/') . '/';

$pages = [
    '' => ['priority' => '1.0', 'changefreq' => 'daily'],
    'about' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'contact' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'api' => ['priority' => '0.7', 'changefreq' => 'monthly'],
    'privacy-policy' => ['priority' => '0.3', 'changefreq' => 'monthly'],
    'terms-and-conditions' => ['priority' => '0.3', 'changefreq' => 'monthly'],
    'disclaimer' => ['priority' => '0.3', 'changefreq' => 'monthly'],
    'company-incorporation' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'nic' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'pincode' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'pincode-by-locality' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'bank-ifsc' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'bank-ifsc-by-branch-name' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'bank-micr' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'gst-hsn-sac' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'passport' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'acts' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'bank' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'bank/services' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'bank/card' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'bank/account' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'bank/contact-details' => ['priority' => '0.8', 'changefreq' => 'monthly'],
];

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

foreach ($pages as $page => $meta) {
    $url = $base . $page;
    $xml .= "\t<url>\n";
    $xml .= "\t\t<loc>" . htmlspecialchars($url) . "</loc>\n";
    $xml .= "\t\t<changefreq>" . $meta['changefreq'] . "</changefreq>\n";
    $xml .= "\t\t<priority>" . $meta['priority'] . "</priority>\n";
    $xml .= "\t</url>\n";
}

$xml .= '</urlset>';

file_put_contents('sitemap-main.xml', $xml);
echo "Success! Generated sitemap-main.xml with " . count($pages) . " core URLs.\n";
?>
