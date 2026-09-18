<?php
/**
 * Unified Sitemap Generator Engine for Online Law
 * Supports modular individual generation as well as complete batch execution.
 */

require_once(__DIR__ . '/includes/config.php');
require_once(__DIR__ . '/includes/function.php');

ini_set('memory_limit', '1024M');
set_time_limit(600); // 10 minutes

function get_sitemap_base_url() {
    global $inst_url;
    return rtrim($inst_url, '/') . '/';
}

function generate_main_sitemap_xml($base = null) {
    if (!$base) $base = get_sitemap_base_url();
    $main_pages = [
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
    file_put_contents(__DIR__ . '/sitemap-main.xml', $xml);
    return ['count' => count($main_pages), 'file' => 'sitemap-main.xml', 'message' => 'Main sitemap generated successfully.'];
}

function generate_pincode_sitemap_xml($base = null) {
    if (!$base) $base = get_sitemap_base_url();
    $sql = "SELECT DISTINCT pincode FROM pincode WHERE pincode IS NOT NULL AND pincode != ''";
    $res = direct_sql($sql);
    $count = 0;
    if ($res['count'] > 0) {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($res['data'] as $row) {
            $pin = $row['pincode'];
            $url = $base . "pincode/" . $pin;
            $xml .= "\t<url>\n\t\t<loc>" . htmlspecialchars($url) . "</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";
        }
        $xml .= '</urlset>';
        file_put_contents(__DIR__ . '/sitemap-pincode.xml', $xml);
        $count = $res['count'];
    }
    return ['count' => $count, 'file' => 'sitemap-pincode.xml', 'message' => "PIN Code sitemap generated with $count URLs."];
}

function generate_passport_sitemap_xml($base = null) {
    if (!$base) $base = get_sitemap_base_url();
    $sql = "SELECT DISTINCT name FROM passport WHERE name IS NOT NULL AND name != '' ORDER BY name ASC";
    $res = direct_sql($sql);
    $sql_rpo = "SELECT DISTINCT rpo FROM passport WHERE rpo IS NOT NULL AND rpo != '' ORDER BY rpo ASC";
    $res_rpo = direct_sql($sql_rpo);

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    $xml .= "\t<url>\n\t\t<loc>" . $base . "passport</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>1.0</priority>\n\t</url>\n";

    if ($res['count'] > 0) {
        foreach ($res['data'] as $row) {
            $name = $row['name'];
            $url = $base . "passport/" . urlencode($name);
            $xml .= "\t<url>\n\t\t<loc>" . htmlspecialchars($url) . "</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.6</priority>\n\t</url>\n";
        }
    }
    if ($res_rpo['count'] > 0) {
        foreach ($res_rpo['data'] as $row) {
            $rpo = $row['rpo'];
            $url = $base . "passport/" . urlencode($rpo);
            $xml .= "\t<url>\n\t\t<loc>" . htmlspecialchars($url) . "</loc>\n\t\t<changefreq>weekly</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";
        }
    }
    $xml .= '</urlset>';
    file_put_contents(__DIR__ . '/sitemap-passport.xml', $xml);
    $total = $res['count'] + $res_rpo['count'] + 1;
    return ['count' => $total, 'file' => 'sitemap-passport.xml', 'message' => "Passport sitemap generated with $total URLs."];
}

function generate_ifsc_sitemap_xml($base = null) {
    if (!$base) $base = get_sitemap_base_url();
    $sql = "SELECT DISTINCT ifsc FROM bank_ifsc WHERE ifsc IS NOT NULL AND ifsc != ''";
    $res = direct_sql($sql);
    $total = 0;
    if ($res['count'] > 0) {
        $total = $res['count'];
        $chunk_size = 45000;
        $chunks = array_chunk($res['data'], $chunk_size);
        $sitemaps = [];

        foreach ($chunks as $index => $chunk) {
            $part = $index + 1;
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            foreach ($chunk as $row) {
                $ifsc = $row['ifsc'];
                $url = $base . "bank-ifsc/" . $ifsc;
                $xml .= "\t<url>\n\t\t<loc>" . htmlspecialchars($url) . "</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.7</priority>\n\t</url>\n";
            }
            $xml .= '</urlset>';
            $filename = "sitemap-ifsc-part$part.xml";
            file_put_contents(__DIR__ . '/' . $filename, $xml);
            $sitemaps[] = $filename;
        }

        // Index for IFSC
        $xml_index = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml_index .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($sitemaps as $map) {
            $map_url = $base . $map;
            $xml_index .= "\t<sitemap>\n\t\t<loc>" . htmlspecialchars($map_url) . "</loc>\n\t\t<lastmod>" . date('Y-m-d') . "</lastmod>\n\t</sitemap>\n";
        }
        $xml_index .= '</sitemapindex>';
        file_put_contents(__DIR__ . '/sitemap-ifsc.xml', $xml_index);
    }
    return ['count' => $total, 'file' => 'sitemap-ifsc.xml', 'message' => "Bank IFSC sitemap generated with $total URLs across multiple parts."];
}

function generate_micr_sitemap_xml($base = null) {
    if (!$base) $base = get_sitemap_base_url();
    $sql = "SELECT DISTINCT micr FROM bank_ifsc WHERE micr IS NOT NULL AND micr != ''";
    $res = direct_sql($sql);
    $total = 0;
    if ($res['count'] > 0) {
        $total = $res['count'];
        $chunk_size = 45000;
        $chunks = array_chunk($res['data'], $chunk_size);
        $sitemaps = [];

        foreach ($chunks as $index => $chunk) {
            $part = $index + 1;
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            foreach ($chunk as $row) {
                $micr = $row['micr'];
                $url = $base . "bank-micr/" . $micr;
                $xml .= "\t<url>\n\t\t<loc>" . htmlspecialchars($url) . "</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.7</priority>\n\t</url>\n";
            }
            $xml .= '</urlset>';
            $filename = "sitemap-micr-part$part.xml";
            file_put_contents(__DIR__ . '/' . $filename, $xml);
            $sitemaps[] = $filename;
        }

        // Index for MICR
        $xml_index = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml_index .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($sitemaps as $map) {
            $map_url = $base . $map;
            $xml_index .= "\t<sitemap>\n\t\t<loc>" . htmlspecialchars($map_url) . "</loc>\n\t\t<lastmod>" . date('Y-m-d') . "</lastmod>\n\t</sitemap>\n";
        }
        $xml_index .= '</sitemapindex>';
        file_put_contents(__DIR__ . '/sitemap-micr.xml', $xml_index);
    }
    return ['count' => $total, 'file' => 'sitemap-micr.xml', 'message' => "Bank MICR sitemap generated with $total URLs across multiple parts."];
}

function generate_gst_sitemap_xml($base = null) {
    if (!$base) $base = get_sitemap_base_url();
    $sql = "SELECT DISTINCT code FROM hsnsac WHERE code IS NOT NULL AND code != ''";
    $res = direct_sql($sql);
    $total = 0;
    if ($res['count'] > 0) {
        $total = $res['count'];
        $chunk_size = 45000;
        $chunks = array_chunk($res['data'], $chunk_size);
        $sitemaps = [];

        foreach ($chunks as $index => $chunk) {
            $part = $index + 1;
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            
            if ($part == 1) {
                $xml .= "\t<url>\n\t\t<loc>" . $base . "gst-hsn-sac</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>1.0</priority>\n\t</url>\n";
            }

            foreach ($chunk as $row) {
                $code = $row['code'];
                $url = $base . "gst-hsn-sac/" . urlencode($code);
                $xml .= "\t<url>\n\t\t<loc>" . htmlspecialchars($url) . "</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.6</priority>\n\t</url>\n";
            }
            $xml .= '</urlset>';
            $filename = "sitemap-gst-part$part.xml";
            file_put_contents(__DIR__ . '/' . $filename, $xml);
            $sitemaps[] = $filename;
        }

        // Index for GST
        $xml_index = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml_index .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($sitemaps as $map) {
            $map_url = $base . $map;
            $xml_index .= "\t<sitemap>\n\t\t<loc>" . htmlspecialchars($map_url) . "</loc>\n\t\t<lastmod>" . date('Y-m-d') . "</lastmod>\n\t</sitemap>\n";
        }
        $xml_index .= '</sitemapindex>';
        file_put_contents(__DIR__ . '/sitemap-gst.xml', $xml_index);
    }
    return ['count' => $total, 'file' => 'sitemap-gst.xml', 'message' => "GST HSN/SAC sitemap generated with $total URLs."];
}

function generate_acts_sitemap_xml($base = null) {
    if (!$base) $base = get_sitemap_base_url();
    $sql = "SELECT DISTINCT name FROM acts WHERE name IS NOT NULL AND name != '' ORDER BY name ASC";
    $res = direct_sql($sql);
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    $xml .= "\t<url>\n\t\t<loc>" . $base . "acts</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>1.0</priority>\n\t</url>\n";

    if ($res['count'] > 0) {
        foreach ($res['data'] as $row) {
            $name = $row['name'];
            $url = $base . "acts/" . urlencode($name);
            $xml .= "\t<url>\n\t\t<loc>" . htmlspecialchars($url) . "</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.7</priority>\n\t</url>\n";
        }
    }
    $xml .= '</urlset>';
    file_put_contents(__DIR__ . '/sitemap-acts.xml', $xml);
    $total = $res['count'] + 1;
    return ['count' => $total, 'file' => 'sitemap-acts.xml', 'message' => "Bare Acts sitemap generated with $total URLs."];
}

function generate_nic_sitemap_xml($base = null) {
    if (!$base) $base = get_sitemap_base_url();
    $sql = "SELECT DISTINCT code FROM nic WHERE code IS NOT NULL AND code != '' ORDER BY code ASC";
    $res = direct_sql($sql);
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    $xml .= "\t<url>\n\t\t<loc>" . $base . "nic</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>1.0</priority>\n\t</url>\n";

    if ($res['count'] > 0) {
        foreach ($res['data'] as $row) {
            $code = $row['code'];
            $url = $base . "nic/" . urlencode($code);
            $xml .= "\t<url>\n\t\t<loc>" . htmlspecialchars($url) . "</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";
        }
    }
    $xml .= '</urlset>';
    file_put_contents(__DIR__ . '/sitemap-nic.xml', $xml);
    $total = $res['count'] + 1;
    return ['count' => $total, 'file' => 'sitemap-nic.xml', 'message' => "NIC 2025 sitemap generated with $total URLs."];
}

function generate_company_sitemap_xml($base = null) {
    if (!$base) $base = get_sitemap_base_url();
    $sql = "SELECT DISTINCT cin, company_name FROM companies_incorporated WHERE cin IS NOT NULL AND cin != '' ORDER BY cin ASC";
    $res = direct_sql($sql);
    $total = 0;
    if ($res['count'] > 0) {
        $total = $res['count'];
        $chunk_size = 45000;
        $chunks = array_chunk($res['data'], $chunk_size);
        $sitemaps = [];

        foreach ($chunks as $index => $chunk) {
            $part = $index + 1;
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            
            if ($part == 1) {
                $xml .= "\t<url>\n\t\t<loc>" . $base . "company-incorporation</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>1.0</priority>\n\t</url>\n";
            }

            foreach ($chunk as $row) {
                $slug = company_slug($row['company_name'], $row['cin']);
                $url = $base . "company-incorporation/" . $slug;
                $xml .= "\t<url>\n\t\t<loc>" . htmlspecialchars($url) . "</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";
            }
            $xml .= '</urlset>';
            $filename = "sitemap-company-part$part.xml";
            file_put_contents(__DIR__ . '/' . $filename, $xml);
            $sitemaps[] = $filename;
        }

        // If multiple parts, create index file sitemap-company.xml
        if (count($sitemaps) > 1) {
            $xml_index = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml_index .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            foreach ($sitemaps as $map) {
                $map_url = $base . $map;
                $xml_index .= "\t<sitemap>\n\t\t<loc>" . htmlspecialchars($map_url) . "</loc>\n\t\t<lastmod>" . date('Y-m-d') . "</lastmod>\n\t</sitemap>\n";
            }
            $xml_index .= '</sitemapindex>';
            file_put_contents(__DIR__ . '/sitemap-company.xml', $xml_index);
        } else {
            // Single part rename/copy to sitemap-company.xml
            file_put_contents(__DIR__ . '/sitemap-company.xml', file_get_contents(__DIR__ . '/sitemap-company-part1.xml'));
        }
    }
    return ['count' => $total + 1, 'file' => 'sitemap-company.xml', 'message' => "Company Incorporation sitemap generated with " . ($total + 1) . " URLs across " . count($sitemaps ?? [1]) . " part(s) (Company Name + CIN slugs)."];
}

function generate_all_sitemaps_unified($base = null) {
    if (!$base) $base = get_sitemap_base_url();
    $results = [];
    $results['main'] = generate_main_sitemap_xml($base);
    $results['pincode'] = generate_pincode_sitemap_xml($base);
    $results['passport'] = generate_passport_sitemap_xml($base);
    $results['ifsc'] = generate_ifsc_sitemap_xml($base);
    $results['micr'] = generate_micr_sitemap_xml($base);
    $results['gst'] = generate_gst_sitemap_xml($base);
    $results['acts'] = generate_acts_sitemap_xml($base);
    $results['nic'] = generate_nic_sitemap_xml($base);
    $results['company'] = generate_company_sitemap_xml($base);
    return $results;
}

// If executed via CLI directly (e.g. php generate_sitemap.php)
if (php_sapi_name() === 'cli' && isset($argv[0]) && realpath($argv[0]) === __FILE__) {
    echo "Starting Sitemap Generation...\n";
    echo "Base URL: " . get_sitemap_base_url() . "\n\n";
    $res = generate_all_sitemaps_unified();
    foreach ($res as $key => $info) {
        echo "✓ " . $info['message'] . "\n";
    }
    echo "\nAll sitemaps generated successfully!\n";
}
