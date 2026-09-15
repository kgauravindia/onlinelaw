<?php
/**
 * Dynamic Sitemap Index for Online Law
 * This file serves as the main entry point for search engines.
 * It dynamically lists the generated sitemap files.
 */
header('Content-type: application/xml; charset=utf-8');
require_once('includes/config.php');

$base = rtrim($inst_url, '/') . '/';

// List of sitemap indices or files to include
$sitemaps = [
    'sitemap-main.xml',
    'sitemap-pincode.xml',
    'sitemap-passport.xml',
    'sitemap-ifsc.xml',
    'sitemap-micr.xml',
    'sitemap-gst.xml',
    'sitemap-acts.xml',
    'sitemap-nic.xml',
    'sitemap-company.xml'
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach($sitemaps as $sitemap): ?>
        <?php if(file_exists($sitemap)): ?>
            <sitemap>
                <loc><?php echo $base . $sitemap; ?></loc>
                <lastmod><?php echo date('Y-m-d', filemtime($sitemap)); ?></lastmod>
            </sitemap>
        <?php endif; ?>
    <?php endforeach; ?>
</sitemapindex>
