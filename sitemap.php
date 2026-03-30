<?php
header('Content-type: application/xml; charset=utf-8');
require_once('includes/config.php');

$base = rtrim($inst_url, '/') . '/';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Main Website Sitemap Index -->
    
    <sitemap>
        <loc><?php echo $base; ?>sitemap-main.xml</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
    </sitemap>

    <sitemap>
        <loc><?php echo $base; ?>sitemap-pincode.xml</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
    </sitemap>

    <sitemap>
        <loc><?php echo $base; ?>sitemap-passport.xml</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
    </sitemap>

    <sitemap>
        <loc><?php echo $base; ?>sitemap-ifsc.xml</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
    </sitemap>

    <sitemap>
        <loc><?php echo $base; ?>sitemap-gst.xml</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
    </sitemap>

    <!-- Additional core pages can be added manually or dynamically if needed -->
</sitemapindex>
