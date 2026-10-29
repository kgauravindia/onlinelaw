<?php
require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../includes/function.php');
require_once(__DIR__ . '/../generate_sitemap.php');

// Admin Auth Check
check_admin_auth(true);

$base = rtrim($inst_url, '/') . '/';
$root_dir = realpath(dirname(__DIR__));

// Handle AJAX Sitemap Generation
if (isset($_POST['action']) || isset($_GET['action'])) {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $target = $_POST['target'] ?? $_GET['target'] ?? 'all';
    $is_ajax = isset($_POST['ajax']) || isset($_GET['ajax']);

    $response = ['status' => 'success', 'results' => [], 'message' => ''];

    try {
        if ($action === 'generate_all') {
            $response['results'] = generate_all_sitemaps_unified($base);
            $response['message'] = 'All sitemaps successfully regenerated!';
        } elseif ($action === 'generate_single') {
            switch ($target) {
                case 'company_indian':
                    $response['results']['company_indian'] = generate_company_indian_sitemap_xml($base);
                    $response['message'] = 'Indian Companies sitemap successfully regenerated!';
                    break;
                case 'company_llp':
                    $response['results']['company_llp'] = generate_company_llp_sitemap_xml($base);
                    $response['message'] = 'LLP Entities sitemap successfully regenerated!';
                    break;
                case 'company_foreign':
                    $response['results']['company_foreign'] = generate_company_foreign_sitemap_xml($base);
                    $response['message'] = 'Foreign Companies sitemap successfully regenerated!';
                    break;
                case 'company':
                    $response['results']['company'] = generate_company_sitemap_xml($base);
                    $response['message'] = 'All Company Incorporations sitemaps (Indian, LLP, Foreign) successfully regenerated!';
                    break;
                case 'main':
                    $response['results']['main'] = generate_main_sitemap_xml($base);
                    $response['message'] = 'Main Pages sitemap successfully regenerated!';
                    break;
                case 'nic':
                    $response['results']['nic'] = generate_nic_sitemap_xml($base);
                    $response['message'] = 'NIC 2025 sitemap successfully regenerated!';
                    break;
                case 'pincode':
                    $response['results']['pincode'] = generate_pincode_sitemap_xml($base);
                    $response['message'] = 'PIN Code sitemap successfully regenerated!';
                    break;
                case 'ifsc':
                    $response['results']['ifsc'] = generate_ifsc_sitemap_xml($base);
                    $response['message'] = 'Bank IFSC sitemap successfully regenerated!';
                    break;
                case 'micr':
                    $response['results']['micr'] = generate_micr_sitemap_xml($base);
                    $response['message'] = 'Bank MICR sitemap successfully regenerated!';
                    break;
                case 'gst':
                    $response['results']['gst'] = generate_gst_sitemap_xml($base);
                    $response['message'] = 'GST HSN/SAC sitemap successfully regenerated!';
                    break;
                case 'acts':
                    $response['results']['acts'] = generate_acts_sitemap_xml($base);
                    $response['message'] = 'Bare Acts sitemap successfully regenerated!';
                    break;
                case 'passport':
                    $response['results']['passport'] = generate_passport_sitemap_xml($base);
                    $response['message'] = 'Passport Offices sitemap successfully regenerated!';
                    break;
                default:
                    throw new Exception("Invalid target sitemap specified.");
            }
        }
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
    }

    if ($is_ajax) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit();
    }
}

// Sitemap Registry Definition
$sitemaps_def = [
    'company_indian' => [
        'name' => 'Indian Companies (Multi-part)',
        'file' => 'sitemap-company.xml',
        'desc' => 'MCA Indian Registered Companies (Pvt Ltd, Public Ltd, OPC, Section 8) with SEO slugs',
        'icon' => 'fas fa-building',
        'color' => '#2563eb',
        'badge' => 'Indian Registry (~831k)',
        'has_parts' => true,
        'parts_pattern' => 'sitemap-company-part*.xml'
    ],
    'company_llp' => [
        'name' => 'LLP Entities (Multi-part)',
        'file' => 'sitemap-company-llp.xml',
        'desc' => 'Limited Liability Partnerships registered under Ministry of Corporate Affairs India',
        'icon' => 'fas fa-handshake',
        'color' => '#0284c7',
        'badge' => 'LLP Hub (~266k)',
        'has_parts' => true,
        'parts_pattern' => 'sitemap-company-llp-part*.xml'
    ],
    'company_foreign' => [
        'name' => 'Foreign Companies Registry',
        'file' => 'sitemap-company-foreign.xml',
        'desc' => 'Foreign Companies (FCRN) established in India and active branch offices',
        'icon' => 'fas fa-globe-americas',
        'color' => '#7c3aed',
        'badge' => 'Foreign (~13k)',
        'has_parts' => true,
        'parts_pattern' => 'sitemap-company-foreign-part*.xml'
    ],
    'main' => [
        'name' => 'Main Core Pages & Tools',
        'file' => 'sitemap-main.xml',
        'desc' => 'Homepage, about, legal search tools, statutory calculators, disclaimer, terms',
        'icon' => 'fas fa-globe',
        'color' => '#0284c7',
        'badge' => 'Core Pages',
        'has_parts' => false
    ],
    'nic' => [
        'name' => 'National Industrial Classification (NIC 2025)',
        'file' => 'sitemap-nic.xml',
        'desc' => 'Official MoSPI 2025 industry divisions, classes, and sub-classes hierarchy',
        'icon' => 'fas fa-industry',
        'color' => '#d97706',
        'badge' => 'MoSPI Registry',
        'has_parts' => false
    ],
    'ifsc' => [
        'name' => 'Bank IFSC Directory (Multi-part)',
        'file' => 'sitemap-ifsc.xml',
        'desc' => 'RBI bank branches, RTGS/NEFT routing codes (split into 5 XML parts)',
        'icon' => 'fas fa-university',
        'color' => '#059669',
        'badge' => 'Banking Hub',
        'has_parts' => true,
        'parts_pattern' => 'sitemap-ifsc-part*.xml'
    ],
    'micr' => [
        'name' => 'Bank MICR Clearing Codes (Multi-part)',
        'file' => 'sitemap-micr.xml',
        'desc' => 'Magnetic Ink Character Recognition codes for cheque processing (4 parts)',
        'icon' => 'fas fa-money-check-alt',
        'color' => '#0d9488',
        'badge' => 'Cheque Clearing',
        'has_parts' => true,
        'parts_pattern' => 'sitemap-micr-part*.xml'
    ],
    'pincode' => [
        'name' => 'Postal PIN Codes Directory',
        'file' => 'sitemap-pincode.xml',
        'desc' => 'Indian Postal Department 6-digit postal index numbers & localities',
        'icon' => 'fas fa-map-pin',
        'color' => '#10b981',
        'badge' => 'India Post',
        'has_parts' => true,
        'parts_pattern' => 'sitemap-pincode-part*.xml'
    ],
    'gst' => [
        'name' => 'GST HSN & SAC Directory',
        'file' => 'sitemap-gst.xml',
        'desc' => 'Harmonized System of Nomenclature & Services Accounting Codes',
        'icon' => 'fas fa-file-invoice-dollar',
        'color' => '#7c3aed',
        'badge' => 'Taxation',
        'has_parts' => true,
        'parts_pattern' => 'sitemap-gst-part*.xml'
    ],
    'acts' => [
        'name' => 'Bare Acts & Legal Codes',
        'file' => 'sitemap-acts.xml',
        'desc' => 'Indian statutory enactments, criminal codes, civil procedures, and amendments',
        'icon' => 'fas fa-book',
        'color' => '#dc2626',
        'badge' => 'Legal Acts',
        'has_parts' => false
    ],
    'passport' => [
        'name' => 'Passport Seva Kendras & RPOs',
        'file' => 'sitemap-passport.xml',
        'desc' => 'Regional Passport Offices (RPO), PSK centers, and post office passport centers',
        'icon' => 'fas fa-passport',
        'color' => '#ca8a04',
        'badge' => 'MEA Directory',
        'has_parts' => false
    ]
];

// Helper: Format Bytes
function format_bytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Compute File Stats
$total_sitemap_files = 0;
$total_sitemap_size = 0;
$total_estimated_urls = 0;
$latest_mtime = 0;

$sitemap_rows = [];
foreach ($sitemaps_def as $key => $meta) {
    $filepath = $root_dir . '/' . $meta['file'];
    $exists = file_exists($filepath);
    $size = $exists ? filesize($filepath) : 0;
    $mtime = $exists ? filemtime($filepath) : 0;
    if ($mtime > $latest_mtime) $latest_mtime = $mtime;

    $parts_files = [];
    $parts_size = 0;
    if (!empty($meta['has_parts']) && !empty($meta['parts_pattern'])) {
        $found_parts = glob($root_dir . '/' . $meta['parts_pattern']);
        if ($found_parts) {
            natsort($found_parts);
            foreach ($found_parts as $fp) {
                $parts_files[] = basename($fp);
                $parts_size += filesize($fp);
            }
        }
    }

    $url_count = 0;
    if ($exists) {
        $total_sitemap_files++;
        $total_sitemap_size += $size + $parts_size;

        // Count <loc> entries in XML
        $content = file_get_contents($filepath);
        $url_count = substr_count($content, '<loc>');
        
        if (!empty($parts_files)) {
            $total_sitemap_files += count($parts_files);
            $part_urls = 0;
            foreach ($parts_files as $pf) {
                $pcontent = file_get_contents($root_dir . '/' . $pf);
                $part_urls += substr_count($pcontent, '<loc>');
            }
            $url_count = $part_urls;
        }
    }

    $total_estimated_urls += $url_count;

    $sitemap_rows[$key] = array_merge($meta, [
        'exists' => $exists,
        'size' => $size + $parts_size,
        'mtime' => $mtime,
        'url_count' => $url_count,
        'parts_files' => $parts_files,
        'public_url' => $base . $meta['file']
    ]);
}

require_once(__DIR__ . '/includes/header.php');
?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge badge-primary px-3 py-1 rounded-pill font-weight-bold" style="background: #eff6ff; color: #1d4ed8; font-size: 0.8rem;">
                <i class="fas fa-sitemap mr-1"></i> XML SITEMAP PROTOCOL 0.9
            </span>
            <span class="badge badge-success px-2.5 py-1 rounded-pill font-weight-bold" style="background: #ecfdf5; color: #047857; font-size: 0.8rem;">
                <i class="fas fa-check-circle mr-1"></i> Dynamic Index Active
            </span>
        </div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Sitemap &amp; SEO Engine</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">
            Generate, inspect, and update XML sitemaps for search engines (Google, Bing, Yahoo, DuckDuckGo).
        </p>
    </div>
    <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2" style="gap: 8px;">
        <a href="<?php echo $base; ?>sitemap.php" target="_blank" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-external-link-alt mr-1"></i> Master Sitemap Index
        </a>
        <a href="<?php echo $base; ?>robots.txt" target="_blank" class="btn btn-outline-secondary font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-robot mr-1"></i> View robots.txt
        </a>
        <button type="button" class="btn btn-primary font-weight-bold shadow-sm" id="btnRegenerateAll" onclick="triggerRegenerateAll();" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-sync-alt mr-1" id="iconRegenerateAll"></i> Regenerate All Sitemaps
        </button>
    </div>
</div>

<!-- Quick Alert Notice -->
<div id="ajaxAlertBox" style="display: none;" class="alert alert-dismissible fade show mb-4 shadow-sm" role="alert">
    <span id="ajaxAlertMsg"></span>
    <button type="button" class="close" onclick="document.getElementById('ajaxAlertBox').style.display='none';">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<!-- Metric Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="stat-card-v2" style="border-top: 4px solid #2563eb;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Total Indexed URLs</div>
                    <div class="stat-value text-primary" id="statTotalUrls"><?php echo number_format($total_estimated_urls); ?></div>
                    <div class="small text-muted mt-1">Across all XML sitemaps</div>
                </div>
                <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                    <i class="fas fa-link"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top d-flex justify-content-between text-muted small" style="font-size: 0.78rem;">
                <span>Ind: <strong><?php echo number_format($sitemap_rows['company_indian']['url_count'] ?? 0); ?></strong></span>
                <span>LLP: <strong><?php echo number_format($sitemap_rows['company_llp']['url_count'] ?? 0); ?></strong></span>
                <span>For: <strong><?php echo number_format($sitemap_rows['company_foreign']['url_count'] ?? 0); ?></strong></span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="stat-card-v2" style="border-top: 4px solid #10b981;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Total Sitemap Files</div>
                    <div class="stat-value text-success"><?php echo $total_sitemap_files; ?> Files</div>
                    <div class="small text-muted mt-1">Total Size: <strong><?php echo format_bytes($total_sitemap_size); ?></strong></div>
                </div>
                <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                    <i class="fas fa-file-code"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top text-muted small">
                <span>Multi-part chunk size: 45,000 URLs</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
        <div class="stat-card-v2" style="border-top: 4px solid #f59e0b;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Last Generated</div>
                    <div class="stat-value text-dark" style="font-size: 1.25rem;">
                        <?php echo $latest_mtime > 0 ? date('d M Y, h:i A', $latest_mtime) : 'Never'; ?>
                    </div>
                    <div class="small text-muted mt-1">
                        <?php echo $latest_mtime > 0 ? date('e (T)', $latest_mtime) : ''; ?>
                    </div>
                </div>
                <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                    <i class="far fa-clock"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top text-muted small">
                <span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Auto-synced with master index</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card-v2" style="border-top: 4px solid #8b5cf6;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Search Engine URL</div>
                    <div class="stat-value text-dark font-monospace" style="font-size: 1.1rem; word-break: break-all;">
                        /sitemap.xml
                    </div>
                    <div class="small text-muted mt-1">Submitted in Google Search Console</div>
                </div>
                <div class="stat-icon" style="background: #f5f3ff; color: #8b5cf6;">
                    <i class="fas fa-search-location"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top d-flex justify-content-between text-muted small">
                <a href="https://search.google.com/search-console" target="_blank" class="text-primary font-weight-bold">Google GSC &rarr;</a>
                <a href="https://www.bing.com/webmasters" target="_blank" class="text-info font-weight-bold">Bing Webmaster &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Sitemaps List Table -->
<div class="bg-white rounded-xl shadow-sm border mb-4 overflow-hidden">
    <div class="p-3.5 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2" style="background: #f8fafc;">
        <div>
            <h5 class="font-weight-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">
                <i class="fas fa-list-ul mr-2 text-primary"></i> Sitemap Index Registry
            </h5>
        </div>
        <div class="text-muted small">
            Click <strong>Regenerate</strong> on any specific module to rebuild only that dataset instantly.
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted">Sitemap Module</th>
                    <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted">XML File</th>
                    <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted">URLs Indexed</th>
                    <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted">File Size</th>
                    <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted">Last Modified</th>
                    <th class="border-0 px-4 py-3 small text-uppercase font-weight-bold text-muted text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sitemap_rows as $target => $row): ?>
                    <tr id="row-<?php echo $target; ?>">
                        <td class="px-4 py-3.5">
                            <div class="d-flex align-items-start gap-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-lg mt-0.5" style="width: 38px; height: 38px; min-width: 38px; background: <?php echo $row['color']; ?>15; color: <?php echo $row['color']; ?>; font-size: 16px;">
                                    <i class="<?php echo $row['icon']; ?>"></i>
                                </span>
                                <div>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.82rem; max-width: 420px;">
                                        <?php echo htmlspecialchars($row['desc']); ?>
                                    </div>
                                    <?php if (!empty($row['parts_files'])): ?>
                                        <div class="mt-2 pt-2 border-top">
                                            <div class="d-flex align-items-center mb-1.5">
                                                <span class="badge badge-light border text-primary font-weight-bold" style="font-size: 0.73rem; background: #eff6ff; border-color: #bfdbfe !important;">
                                                    <i class="fas fa-layer-group mr-1"></i> <?php echo count($row['parts_files']); ?> XML Parts:
                                                </span>
                                            </div>
                                            <div class="d-flex flex-wrap" style="gap: 5px; max-width: 480px;">
                                                <?php foreach ($row['parts_files'] as $part_file): ?>
                                                    <a href="<?php echo $base . $part_file; ?>" target="_blank" class="badge badge-light border text-dark text-decoration-none py-1 px-2 d-inline-flex align-items-center" style="font-size: 0.76rem; font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace; background: #f8fafc; border-color: #e2e8f0 !important;" title="Open <?php echo htmlspecialchars($part_file); ?>">
                                                        <i class="fas fa-file-code text-primary mr-1" style="font-size: 10px;"></i>
                                                        <?php echo htmlspecialchars($part_file); ?>
                                                        <i class="fas fa-external-link-alt ml-1 text-muted" style="font-size: 8px;"></i>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-3.5">
                            <code class="font-weight-bold text-primary font-monospace" style="font-size: 0.88rem;">
                                <?php echo htmlspecialchars($row['file']); ?>
                            </code>
                        </td>

                        <td class="px-4 py-3.5">
                            <span class="badge bg-light text-dark font-weight-bold border px-2.5 py-1.5" style="font-size: 0.85rem;" id="count-<?php echo $target; ?>">
                                <?php echo number_format($row['url_count']); ?> URLs
                            </span>
                        </td>

                        <td class="px-4 py-3.5 small text-muted font-weight-semibold" id="size-<?php echo $target; ?>">
                            <?php echo $row['exists'] ? format_bytes($row['size']) : '<span class="text-danger">Missing</span>'; ?>
                        </td>

                        <td class="px-4 py-3.5 small text-muted" id="time-<?php echo $target; ?>">
                            <?php echo $row['mtime'] > 0 ? date('d M Y, h:i A', $row['mtime']) : 'Never'; ?>
                        </td>

                        <td class="px-4 py-3.5 text-right">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary font-weight-bold px-2.5" onclick="triggerRegenerateSingle('<?php echo $target; ?>');" id="btn-<?php echo $target; ?>" title="Regenerate this sitemap">
                                    <i class="fas fa-sync-alt mr-1"></i> Update
                                </button>
                                <a href="<?php echo $row['public_url']; ?>" target="_blank" class="btn btn-light border text-muted px-2.5" title="View XML in new tab">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-light border text-muted px-2.5" onclick="navigator.clipboard.writeText('<?php echo $row['public_url']; ?>'); alert('Copied sitemap URL to clipboard:\n<?php echo $row['public_url']; ?>');" title="Copy XML Link">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Search Engine Webmaster Submission Hub -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="bg-white rounded-xl shadow-sm border p-4 h-100">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge badge-primary rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="fas fa-globe"></i>
                </span>
                <h5 class="font-weight-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">Search Engine Webmaster Submission</h5>
            </div>
            <p class="text-muted small mb-3">
                Submit the master sitemap index URL to search engine webmaster consoles for rapid indexing of new company incorporations and legal data.
            </p>

            <div class="p-3 rounded-lg border bg-light mb-3">
                <label class="small text-muted font-weight-bold mb-1">Master Sitemap Index URL:</label>
                <div class="input-group">
                    <input type="text" class="form-control font-monospace" style="height: 40px; font-size: 0.88rem; background: #fff;" readonly value="<?php echo $base; ?>sitemap.php" id="masterSitemapUrlInput">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary font-weight-bold px-3" type="button" onclick="navigator.clipboard.writeText(document.getElementById('masterSitemapUrlInput').value); alert('Copied master sitemap URL!');">
                            <i class="far fa-copy mr-1"></i> Copy
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="https://search.google.com/search-console" target="_blank" class="btn btn-sm btn-outline-primary font-weight-bold px-3 py-2" style="border-radius: var(--radius-sm);">
                    <i class="fab fa-google mr-1"></i> Google Search Console
                </a>
                <a href="https://www.bing.com/webmasters" target="_blank" class="btn btn-sm btn-outline-info font-weight-bold px-3 py-2" style="border-radius: var(--radius-sm);">
                    <i class="fab fa-microsoft mr-1"></i> Bing Webmaster
                </a>
                <a href="https://webmaster.yandex.com/" target="_blank" class="btn btn-sm btn-outline-warning font-weight-bold px-3 py-2" style="border-radius: var(--radius-sm);">
                    <i class="fab fa-yandex mr-1"></i> Yandex Webmaster
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="bg-white rounded-xl shadow-sm border p-4 h-100">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge badge-success rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="fas fa-robot"></i>
                </span>
                <h5 class="font-weight-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">robots.txt Configuration Preview</h5>
            </div>
            <p class="text-muted small mb-2">
                Crawler instructions from your live <code>robots.txt</code> file informing bots of the sitemap location.
            </p>

            <pre class="p-3 rounded-lg border small text-dark mb-0" style="background: #f8fafc; font-family: monospace; max-height: 140px; overflow-y: auto;"><?php 
                $robots_file = $root_dir . '/robots.txt';
                echo htmlspecialchars(file_exists($robots_file) ? file_get_contents($robots_file) : 'No robots.txt found.');
            ?></pre>
        </div>
    </div>
</div>

<script>
function showAlert(msg, isSuccess) {
    const box = document.getElementById('ajaxAlertBox');
    const txt = document.getElementById('ajaxAlertMsg');
    box.className = 'alert alert-dismissible fade show mb-4 shadow-sm ' + (isSuccess ? 'alert-success' : 'alert-danger');
    txt.innerHTML = (isSuccess ? '<i class="fas fa-check-circle mr-2"></i>' : '<i class="fas fa-exclamation-circle mr-2"></i>') + msg;
    box.style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function triggerRegenerateAll() {
    const btn = document.getElementById('btnRegenerateAll');
    const icon = document.getElementById('iconRegenerateAll');
    if (!confirm('Regenerate all 9 XML sitemap categories (this includes ~200,000+ URLs)?')) {
        return;
    }

    btn.disabled = true;
    icon.className = 'fas fa-spinner fa-spin mr-1';

    fetch('manage_sitemap.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=generate_all&ajax=1'
    })
    .then(r => r.text())
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch(e) {
            console.error('Server output:', text);
            throw new Error(text.replace(/<[^>]*>/g, '').trim() || 'Invalid server response');
        }
        btn.disabled = false;
        icon.className = 'fas fa-sync-alt mr-1';
        if (data.status === 'success') {
            showAlert('<strong>Success!</strong> ' + data.message + ' Page will refresh with updated stats.', true);
            setTimeout(() => { window.location.reload(); }, 1200);
        } else {
            showAlert('<strong>Error:</strong> ' + (data.message || 'Generation failed'), false);
        }
    })
    .catch(err => {
        btn.disabled = false;
        icon.className = 'fas fa-sync-alt mr-1';
        showAlert('<strong>Error:</strong> ' + err.message, false);
    });
}

function triggerRegenerateSingle(target) {
    const btn = document.getElementById('btn-' + target);
    if (!btn) return;

    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Updating...';

    fetch('manage_sitemap.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=generate_single&target=' + encodeURIComponent(target) + '&ajax=1'
    })
    .then(r => r.text())
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch(e) {
            console.error('Server output:', text);
            throw new Error(text.replace(/<[^>]*>/g, '').trim() || 'Invalid server response');
        }
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        if (data.status === 'success') {
            showAlert('<strong>Updated!</strong> ' + data.message, true);
            setTimeout(() => { window.location.reload(); }, 1000);
        } else {
            showAlert('<strong>Error:</strong> ' + (data.message || 'Update failed'), false);
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        showAlert('<strong>Error:</strong> ' + err.message, false);
    });
}
</script>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
