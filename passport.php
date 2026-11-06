<?php
/**
 * Online Law - Passport Seva Kendra (PSK) & RPO Directory
 * Modernized 2026 UI with Instant Search, 1-Click Copy & National Helpline Box
 */
require_once('includes/config.php');
require_once('includes/function.php'); 
require_once('includes/captcha.php'); 

$q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';

if (!empty($q)) {
    $meta_desc = "Quickly locate Passport Seva Kendra (PSK) details for $q. Get office addresses, contact numbers, and Regional Passport Office (RPO) jurisdiction. Updated 2026.";
    $page_title = "Passport Office $q | PSK & RPO Contact Details (Updated 2026) - Online Law";
} else {
    $meta_desc = "Find your nearest Passport Seva Kendra (PSK), Post Office PSK (POPSK), or Regional Passport Office (RPO) in India. Full directory of passport office addresses and helplines.";
    $page_title = "Passport Office Finder | Locate PSK & RPO Offices India (Updated 2026) - Online Law";
}
$canonical_url = $base_url . "passport" . (!empty($q) ? "?q=" . urlencode($q) : "");

// Schema.org Structured Data
$passport_schema = [
    "@context" => "https://schema.org",
    "@type" => "GovernmentBuilding",
    "name" => !empty($q) ? "Passport Office - " . $q : "Passport Seva Kendra Directory India",
    "description" => $meta_desc,
    "url" => $canonical_url,
    "department" => [
        "@type" => "GovernmentOrganization",
        "name" => "Consular, Passport & Visa (CPV) Division, Ministry of External Affairs",
        "url" => "https://www.passportindia.gov.in/"
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="OfferPlant">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta name="keywords" content="Passport Office Search, Find PSK Near Me, Regional Passport Office India, Passport Seva Kendra Address, POPSK Locations, Passport Help Number, <?php echo htmlspecialchars($q); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">

    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">

    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    <?php echo json_encode($passport_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-code: 'JetBrains Mono', monospace;
        }

        body {
            font-family: var(--font-main) !important;
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* Hero Banner */
        .psk-hero-section {
            background-color: #ffffff !important;
            background: linear-gradient(180deg, #ffffff 0%, #fafbfc 55%, #f1f5f9 100%) !important;
            color: #0f172a !important;
            padding: 50px 0 60px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid #e2e8f0;
        }
        .psk-glow-orb {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 103, 31, 0.08) 0%, transparent 70%);
            top: -100px;
            left: -60px;
            pointer-events: none;
            filter: blur(50px);
        }

        .psk-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border: 1.5px solid #fed7aa;
            color: #c2410c;
            padding: 5px 16px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            box-shadow: 0 4px 14px rgba(255, 103, 31, 0.08);
        }

        .search-card-container {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            padding: 32px;
            margin-top: -35px;
            position: relative;
            z-index: 5;
        }

        .copy-btn-mini {
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 2px 6px;
            font-size: 12px;
            transition: color 0.15s ease;
        }
        .copy-btn-mini:hover {
            color: #ff671f;
        }

        .psk-card-mobile {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }
        .psk-card-mobile:hover {
            border-color: #fdba74;
            transform: translateY(-2px);
        }

        @media (max-width: 767px) {
            .search-card-container { padding: 20px 16px; margin-top: -20px; }
            .desktop-only { display: none !important; }
            .mobile-only { display: block !important; }
        }
        @media (min-width: 768px) {
            .mobile-only { display: none !important; }
            .desktop-only { display: block !important; }
        }
    </style>
</head>
<body>

<?php require_once('includes/header.php'); ?>

<!-- Hero Section -->
<section class="psk-hero-section">
    <div class="psk-glow-orb"></div>
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10 col-12">
                <div class="psk-badge-pill mb-3">
                    <i class="fas fa-passport mr-1.5"></i> PASSPORT SEVA &bull; PSK &amp; RPO DIRECTORY
                </div>
                <h1 class="font-weight-bold mb-2" style="font-family: var(--font-heading); font-size: clamp(26px, 3.8vw, 42px); line-height: 1.22; letter-spacing: -0.5px; background: linear-gradient(135deg, #ff671f 0%, #ea580c 45%, #046a38 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">
                    Passport Office Finder (PSK / POPSK / RPO)
                </h1>
                <p class="mb-3" style="color: #475569 !important; font-size: 15px; max-width: 700px; margin: 0 auto;">
                    Locate Passport Seva Kendras, Post Office PSKs, Regional Passport Offices, addresses, and official contact numbers.
                </p>
                <ul class="list-inline mb-0" style="color: #64748b; font-size: 13.5px;">
                    <li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: #475569; text-decoration: none;"><i class="fas fa-home mr-1"></i> Home</a></li>
                    <li class="list-inline-item mx-2 text-muted">/</li>
                    <li class="list-inline-item text-dark font-weight-bold">Passport Offices</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Search Console -->
<section class="py-4" style="background: #f8fafc; padding-bottom: 70px !important;">
    <div class="container">
        <div class="search-card-container">
            <form method="get" action="<?php echo $base_url; ?>passport" class="mb-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-8 col-12 mb-2 mb-md-0">
                        <div class="position-relative">
                            <input class="form-control" style="padding-left: 18px; height: 52px; border-radius: 12px; font-size: 15px; border: 1.5px solid #cbd5e1;" type="text" placeholder="Search by City, Office Name (e.g. PSK Patna, Ahmedabad), or RPO..." name="q" value="<?php echo htmlentities($q); ?>" required>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-12">
                        <button type="submit" class="btn btn-primary w-100 font-weight-bold" style="height: 52px; font-size: 15px; border-radius: 12px; background: linear-gradient(135deg, #ff671f, #ea580c); border: none;">
                            <i class="fas fa-search mr-1.5"></i> Find Office
                        </button>
                    </div>
                    <div class="col-12 mt-2 pt-2 d-flex align-items-center justify-content-center flex-wrap" style="gap: 8px;">
                        <span class="small font-weight-bold text-muted"><i class="fas fa-shield-alt text-primary mr-1"></i> Security Code:</span>
                        <?php echo olaw_render_search_captcha_widget('_passport'); ?>
                    </div>
                </div>
            </form>

            <!-- Official Helpline Notice -->
            <div class="p-3 mb-4 rounded-xl border d-flex flex-wrap align-items-center justify-content-between" style="background: #fdfefe; border-color: #e2e8f0; border-radius: 14px;">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <div class="mr-3" style="width: 40px; height: 40px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fas fa-building-columns"></i>
                    </div>
                    <div>
                        <strong class="d-block text-dark" style="font-size: 14px;">Official Ministry Portal:</strong>
                        <span class="text-muted small">For appointment bookings, fee payment, and tracking, visit <a href="https://www.passportindia.gov.in/" target="_blank" class="text-primary font-weight-bold">passportindia.gov.in</a>.</span>
                    </div>
                </div>
                <div>
                    <a href="tel:18002581800" class="badge badge-primary px-3 py-2 font-weight-bold" style="font-size: 13px; border-radius: 8px; text-decoration: none; background: #0284c7;">
                        <i class="fas fa-phone-alt mr-1"></i> 1800-258-1800 (National Call Center)
                    </a>
                </div>
            </div>

            <!-- Table Results -->
            <?php 
                $captcha_error = false;
                $page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
                $limit = 20;
                $offset = ($page - 1) * $limit;
                $total_records = 0;
                $total_pages = 1;

                if (empty($q)) {
                    $count_res = direct_sql("SELECT COUNT(*) as cnt FROM passport");
                    $total_records = (int)($count_res['data'][0]['cnt'] ?? 0);
                    $total_pages = max(1, ceil($total_records / $limit));
                    $sql = "SELECT * FROM passport ORDER BY name ASC LIMIT $offset, $limit";
                    $res = direct_sql($sql);
                    $results_title = "Featured Passport Offices";
                } else {
                    if (!olaw_validate_search_captcha()) {
                        $captcha_error = true;
                        $res = ['count' => 0, 'data' => []];
                        $results_title = "Security Verification Required";
                    } else {
                        $safe_q = mysqli_real_escape_string($con, $q);
                        $where_q = "(name LIKE '%$safe_q%' OR address LIKE '%$safe_q%' OR rpo LIKE '%$safe_q%')";
                        $count_res = direct_sql("SELECT COUNT(*) as cnt FROM passport WHERE $where_q");
                        $total_records = (int)($count_res['data'][0]['cnt'] ?? 0);
                        $total_pages = max(1, ceil($total_records / $limit));
                        $sql = "SELECT * FROM passport WHERE $where_q ORDER BY name ASC LIMIT $offset, $limit";
                        $res = direct_sql($sql);
                        $results_title = 'Results for "' . htmlentities($q) . '"';
                    }
                }
                
                $count = $res['count'] ?? 0;
            ?>

            <?php if (!empty($captcha_error)): ?>
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4 mt-4" style="border-radius: 14px; background: #fffbeb; color: #b45309; border-left: 5px solid #f59e0b !important; padding: 18px 24px;">
                <i class="fas fa-shield-alt fa-2x mr-3 text-warning"></i>
                <div>
                    <h6 class="font-weight-bold mb-1" style="color: #92400e;">Security Verification Required</h6>
                    <p class="small mb-0 text-muted">Please enter the security verification code displayed above to view passport office records.</p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($count > 0) { ?>
                <div class="mt-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">
                            <?php echo $results_title; ?> <span class="badge badge-light border ml-1"><?php echo $count; ?> Offices</span>
                        </h4>
                    </div>

                    <!-- Desktop View -->
                    <div class="table-responsive desktop-only">
                        <table class="table table-hover border" id="passport_table" style="border-radius: 12px; overflow: hidden;">
                            <thead style="background: #f1f5f9;">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Office Name</th>
                                    <th>Address</th>
                                    <th style="width: 160px;">Phone</th>
                                    <th style="width: 140px;">RPO Jurisdiction</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $idx = 1; foreach ($res['data'] as $row): ?>
                                    <tr>
                                        <td><span class="text-muted" style="font-size: 13px;"><?php echo $idx++; ?></span></td>
                                        <td>
                                            <strong class="text-dark" style="font-size: 14.5px;"><?php echo htmlentities($row['name']); ?></strong>
                                        </td>
                                        <td style="font-size: 13.5px; color: #475569; line-height: 1.5; min-width: 280px;">
                                            <?php echo htmlentities($row['address']); ?>
                                            <button class="copy-btn-mini" onclick="copyText('<?php echo addslashes($row['address']); ?>', this)" title="Copy Address"><i class="fas fa-copy"></i></button>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['phone']) && $row['phone'] !== '-'): ?>
                                                <a href="tel:<?php echo htmlspecialchars($row['phone']); ?>" class="text-primary font-weight-bold" style="text-decoration: none; font-size: 13.5px;">
                                                    <i class="fas fa-phone mr-1 small"></i> <?php echo htmlentities($row['phone']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: #eff6ff; color: #1e40af; font-size: 12px; font-weight: 700; border-radius: 6px; padding: 5px 8px;">
                                                <?php echo htmlentities($row['rpo']); ?> RPO
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="mobile-only">
                        <?php foreach ($res['data'] as $row): ?>
                            <div class="psk-card-mobile">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <span class="badge" style="background: #eff6ff; color: #1e40af; font-size: 12px; font-weight: 700; border-radius: 6px;">
                                        <i class="fas fa-building mr-1"></i> <?php echo htmlentities($row['rpo']); ?> RPO
                                    </span>
                                </div>
                                <h5 class="font-weight-bold text-dark mb-2" style="font-size: 16px; line-height: 1.4;">
                                    <?php echo htmlentities($row['name']); ?>
                                </h5>
                                <p class="text-muted small mb-3" style="line-height: 1.5;">
                                    <i class="fas fa-map-marker-alt text-danger mr-1"></i> <?php echo htmlentities($row['address']); ?>
                                </p>
                                <?php if (!empty($row['phone']) && $row['phone'] !== '-'): ?>
                                    <a href="tel:<?php echo htmlspecialchars($row['phone']); ?>" class="btn btn-outline-primary btn-sm btn-block font-weight-bold" style="border-radius: 8px;">
                                        <i class="fas fa-phone-alt mr-1"></i> Call <?php echo htmlentities($row['phone']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div class="d-flex justify-content-center mt-3 mb-4">
                        <nav aria-label="Page navigation">
                            <ul class="pagination" style="gap: 5px;">
                                <?php
                                $queryParams = $_GET;
                                if ($page > 1) {
                                    $queryParams['p'] = $page - 1;
                                    echo '<li class="page-item"><a class="page-link rounded-pill px-3 shadow-xs" href="?' . http_build_query($queryParams) . '">&laquo; Prev</a></li>';
                                }
                                $startPage = max(1, $page - 2);
                                $endPage = min($total_pages, $page + 2);
                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    $queryParams['p'] = $i;
                                    $active = ($i == $page) ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '"><a class="page-link rounded-circle text-center font-weight-bold" style="width: 38px; height: 38px; line-height: 24px;" href="?' . http_build_query($queryParams) . '">' . $i . '</a></li>';
                                }
                                if ($page < $total_pages) {
                                    $queryParams['p'] = $page + 1;
                                    echo '<li class="page-item"><a class="page-link rounded-pill px-3 shadow-xs" href="?' . http_build_query($queryParams) . '">Next &raquo;</a></li>';
                                }
                                ?>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            <?php } else { ?>
                <?php if (!empty($q)): ?>
                    <div class="alert mt-4" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; border-radius: 12px; padding: 20px;">
                        <i class="fas fa-exclamation-circle mr-2"></i> No Passport offices found matching "<b><?php echo htmlentities($q); ?></b>". Please try another city or office keyword.
                    </div>
                <?php endif; ?>
            <?php } ?>
        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>

<script>
$(document).ready(function() {
    if ($('#passport_table').length) {
        $('#passport_table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            pageLength: 25
        });
    }
});

function copyText(text, btn) {
    if (!text) return;
    navigator.clipboard.writeText(text).then(function() {
        var original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-success"></i>';
        setTimeout(function() { btn.innerHTML = original; }, 1500);
    });
}
</script>
</body>
</html>
