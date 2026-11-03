<?php
/**
 * Online Law - Indian Central Bare Acts Directory
 * Modernized 2026 UI with Instant Search, Filter Badges & 1-Click Copy
 */
require_once('includes/config.php');
require_once('includes/function.php'); 

$q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '';
$year_filter = isset($_REQUEST['year']) ? trim($_REQUEST['year']) : '';

if (!empty($q)) {
    $meta_desc = "Quickly search for Bare Acts and Central Acts in India: $q. Get official act numbers, years, categories, and legal citations. Updated 2026.";
    $page_title = "Bare Act: $q | Indian Central Acts Directory (Updated 2026) - Online Law";
} else {
    $meta_desc = "Complete directory of Indian Bare Acts and Central Acts. Search by act name, year, or act number. Free legal resource updated 2026.";
    $page_title = "Bare Acts Directory | Indian Law Central Acts Search (Updated 2026) - Online Law";
}
$canonical_url = $base_url . "acts" . (!empty($q) ? "?q=" . urlencode($q) : "");

// Schema.org Structured Data
$acts_schema = [
    "@context" => "https://schema.org",
    "@type" => "LegalAct",
    "name" => !empty($q) ? $q : 'Indian Central Acts Directory',
    "description" => $meta_desc,
    "url" => $canonical_url,
    "legislationType" => "Central Act",
    "jurisdiction" => "India",
    "publisher" => [
        "@type" => "GovernmentOrganization",
        "name" => "Ministry of Law & Justice, Legislative Department",
        "url" => "https://www.legislative.gov.in/"
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
    <meta name="keywords" content="Bare Acts India, Central Acts Search, Indian Law Acts, Legal Acts Directory, Government Acts List, Law Search Tool, act no, <?php echo htmlspecialchars($q); ?>">
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
    <?php echo json_encode($acts_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
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
        .acts-hero-section {
            background-color: #0b1120 !important;
            background: radial-gradient(135% 100% at 50% 0%, #0b1120 0%, #1e1b4b 55%, #0f172a 100%) !important;
            padding: 50px 0 60px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .acts-glow-orb {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.18) 0%, transparent 70%);
            top: -100px;
            right: -60px;
            pointer-events: none;
            filter: blur(50px);
        }

        .acts-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(220, 38, 38, 0.18);
            border: 1px solid rgba(248, 113, 113, 0.35);
            color: #fca5a5;
            padding: 5px 16px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
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

        .code-badge {
            font-family: var(--font-code);
            font-weight: 700;
            font-size: 13px;
            padding: 4px 10px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
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

        .act-card-mobile {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }
        .act-card-mobile:hover {
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
<section class="acts-hero-section">
    <div class="acts-glow-orb"></div>
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10 col-12">
                <div class="acts-badge-pill mb-3">
                    <i class="fas fa-gavel mr-1.5"></i> LEGISLATIVE ACTS &bull; UPDATED 2026
                </div>
                <h1 class="font-weight-bold mb-2" style="font-family: var(--font-heading); font-size: clamp(26px, 3.8vw, 42px); line-height: 1.22; letter-spacing: -0.5px; background: linear-gradient(135deg, #fb923c 0%, #f97316 45%, #f43f5e 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; filter: drop-shadow(0 2px 10px rgba(251, 146, 60, 0.4)); display: inline-block;">
                    Indian Central Bare Acts Directory
                </h1>
                <p class="text-white-50 mb-3" style="font-size: 15px; max-width: 700px; margin: 0 auto;">
                    Search official Bare Acts, Act Numbers, Year of Enactment, and Statutory Categories from the Ministry of Law &amp; Justice.
                </p>
                <ul class="list-inline mb-0" style="color: #94a3b8; font-size: 13.5px;">
                    <li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: #cbd5e1; text-decoration: none;"><i class="fas fa-home mr-1"></i> Home</a></li>
                    <li class="list-inline-item mx-2 text-muted">/</li>
                    <li class="list-inline-item text-white font-weight-bold">Bare Acts</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Search Console -->
<section class="py-4" style="background: #f8fafc; padding-bottom: 70px !important;">
    <div class="container">
        <div class="search-card-container">
            <form method="get" action="<?php echo $base_url; ?>acts" class="mb-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-8 col-12 mb-2 mb-md-0">
                        <div class="position-relative">
                            <input class="form-control" style="padding-left: 18px; height: 52px; border-radius: 12px; font-size: 15px; border: 1.5px solid #cbd5e1;" type="text" placeholder="Search by Act Name (e.g. Companies Act, IPC, Evidence), Act No, or Year..." name="q" value="<?php echo htmlentities($q); ?>" required>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-12">
                        <button type="submit" class="btn btn-primary w-100 font-weight-bold" style="height: 52px; font-size: 15px; border-radius: 12px; background: linear-gradient(135deg, #ff671f, #ea580c); border: none;">
                            <i class="fas fa-search mr-1.5"></i> Search Acts
                        </button>
                    </div>
                </div>
            </form>

            <!-- Official Helpline Notice -->
            <div class="p-3 mb-4 rounded-xl border d-flex flex-wrap align-items-center justify-content-between" style="background: #fdfefe; border-color: #e2e8f0; border-radius: 14px;">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <div class="mr-3" style="width: 40px; height: 40px; border-radius: 10px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fas fa-scale-balanced"></i>
                    </div>
                    <div>
                        <strong class="d-block text-dark" style="font-size: 14px;">Official Legal Resources &amp; Citations:</strong>
                        <span class="text-muted small">Refer to <a href="https://www.legislative.gov.in/" target="_blank" class="text-primary font-weight-bold">legislative.gov.in</a> &amp; <a href="https://egazette.gov.in/" target="_blank" class="text-primary font-weight-bold">eGazette</a> for full gazette notifications.</span>
                    </div>
                </div>
                <div>
                    <a href="tel:15100" class="badge badge-danger px-3 py-2 font-weight-bold" style="font-size: 13px; border-radius: 8px; text-decoration: none;">
                        <i class="fas fa-phone-alt mr-1"></i> 15100 (NALSA Legal Aid)
                    </a>
                </div>
            </div>

            <!-- Table Results -->
            <?php 
                if (empty($q)) {
                    $sql = "SELECT * FROM acts ORDER BY year DESC, name ASC LIMIT 100";
                    $res = direct_sql($sql);
                    $results_title = "Featured Central Acts (Showing latest 100)";
                } else {
                    $safe_q = addslashes($q);
                    $sql = "SELECT * FROM acts WHERE name LIKE '%$safe_q%' OR remarks LIKE '%$safe_q%' OR act_no LIKE '%$safe_q%' OR year LIKE '%$safe_q%' ORDER BY year DESC, name ASC LIMIT 500";
                    $res = direct_sql($sql);
                    $results_title = 'Results for "' . htmlentities($q) . '"';
                }
                
                $count = $res['count'] ?? 0;
                if ($count > 0) {
            ?>
                <div class="mt-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px;">
                            <?php echo $results_title; ?> <span class="badge badge-light border ml-1"><?php echo $count; ?> Records</span>
                        </h4>
                    </div>

                    <!-- Desktop View -->
                    <div class="table-responsive desktop-only">
                        <table class="table table-hover border" id="acts_table" style="border-radius: 12px; overflow: hidden;">
                            <thead style="background: #f1f5f9;">
                                <tr>
                                    <th style="width: 70px;">ID</th>
                                    <th>Act Name</th>
                                    <th style="width: 100px;">Year</th>
                                    <th style="width: 120px;">Act No</th>
                                    <th style="width: 140px;">Type</th>
                                    <th>Remarks / Subject</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($res['data'] as $row): ?>
                                    <tr>
                                        <td><span class="text-muted" style="font-family: var(--font-code); font-size: 12px;"><?php echo htmlentities($row['id']); ?></span></td>
                                        <td>
                                            <strong class="text-dark" style="font-size: 14.5px;"><?php echo htmlentities($row['name']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: #ecfdf5; color: #065f46; font-size: 12px; font-weight: 700; border-radius: 6px; padding: 5px 8px;">
                                                <?php echo htmlentities($row['year']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="code-badge">
                                                <span><?php echo htmlentities($row['act_no'] ?: '-'); ?></span>
                                                <?php if (!empty($row['act_no'])): ?>
                                                <button class="copy-btn-mini" onclick="copyText('<?php echo addslashes($row['act_no']); ?>', this)" title="Copy Act No"><i class="fas fa-copy"></i></button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: #fff7ed; color: #9a3412; font-size: 12px; font-weight: 700; border-radius: 6px; padding: 5px 8px;">
                                                <?php echo htmlentities($row['act_type'] ?: 'Central Act'); ?>
                                            </span>
                                        </td>
                                        <td style="font-size: 13.5px; color: #475569; line-height: 1.5;">
                                            <?php echo htmlentities($row['remarks'] ?: '-'); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="mobile-only">
                        <?php foreach ($res['data'] as $row): ?>
                            <div class="act-card-mobile">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <span class="badge" style="background: #ecfdf5; color: #065f46; font-size: 12px; font-weight: 700; border-radius: 6px;">
                                        <i class="fas fa-calendar-alt mr-1"></i> <?php echo htmlentities($row['year']); ?>
                                    </span>
                                    <span class="badge" style="background: #fff7ed; color: #9a3412; font-size: 11px; font-weight: 700; border-radius: 6px;">
                                        <?php echo htmlentities($row['act_type'] ?: 'Central Act'); ?>
                                    </span>
                                </div>
                                <h5 class="font-weight-bold text-dark mb-2" style="font-size: 16px; line-height: 1.4;">
                                    <?php echo htmlentities($row['name']); ?>
                                </h5>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-muted small mr-2">Act No:</span>
                                    <div class="code-badge">
                                        <span><?php echo htmlentities($row['act_no'] ?: '-'); ?></span>
                                        <?php if (!empty($row['act_no'])): ?>
                                        <button class="copy-btn-mini" onclick="copyText('<?php echo addslashes($row['act_no']); ?>', this)" title="Copy Act No"><i class="fas fa-copy"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!empty($row['remarks'])): ?>
                                    <p class="text-muted small mb-0 pt-2 border-top" style="line-height: 1.5;">
                                        <i class="fas fa-info-circle text-primary mr-1"></i> <?php echo htmlentities($row['remarks']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php } else { ?>
                <?php if (!empty($q)): ?>
                    <div class="alert mt-4" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; border-radius: 12px; padding: 20px;">
                        <i class="fas fa-exclamation-circle mr-2"></i> No Bare Acts found matching "<b><?php echo htmlentities($q); ?></b>". Please try another keyword or search by year.
                    </div>
                <?php endif; ?>
            <?php } ?>
        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>

<script>
$(document).ready(function() {
    if ($('#acts_table').length) {
        $('#acts_table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            pageLength: 25,
            order: [[2, "desc"]]
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
