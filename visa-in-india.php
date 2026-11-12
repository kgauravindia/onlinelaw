<?php
/**
 * Online Law - Visa in India: Latest Amendments & 2026 Regulatory Guide
 * Covers the Immigration and Foreigners (Exemption) (Amendment) Order, 2026 (Nov 2026),
 * MHA Visa Regime Rationalization (9 Main Categories & 23 Sub-Categories),
 * Visitor Visa (V-1/V-2), e-Visa Updates, and Statutory Compliance.
 */
require_once('includes/config.php');
require_once('includes/function.php');

$page_title = "Visa in India: Today's & 2026 Amendments, New Visa Categories & MHA Rules | Online Law";
$meta_desc = "Complete legal & practical guide to India's latest Visa Amendments (November 2026): Immigration and Foreigners (Exemption) Amendment Order 2026, 9 rationalized categories, Visitor Visa (V-1/V-2), 70% simpler forms, biometrics & compliance.";
$meta_keywords = "visa in India amendment, India visa rules 2026, Immigration and Foreigners Exemption Amendment Order 2026, Visitor Visa V-1 V-2 India, MHA visa rationalization 9 categories, e-Visa India latest rules, Nepal Bhutan border visa rules";
$canonical_url = $base_url . "visa-in-india";

$faq_items = [
    [
        "q" => "What is the biggest change under the 2026 India Visa Regime Rationalization?",
        "a" => "The Ministry of Home Affairs (MHA) consolidated India's visa system from 13 main categories (and 49 sub-categories) down to 9 core categories and 23 sub-categories, while cutting required application form fields by 65% to 70% to eliminate overlaps and speed up approvals."
    ],
    [
        "q" => "Did the Visitor Visa replace the traditional Tourist Visa in India?",
        "a" => "Yes. Under the rationalized MHA structure, the traditional Tourist Visa has been re-engineered into the Visitor Visa with two streams: V-1 (short-term up to 30 days, multiple entry, covering tourism, short business meetings, medical check-ups, wellness/yoga, and short courses) and V-2 (long-term 1, 5, or 10 years, including mountaineering)."
    ],
    [
        "q" => "What did the November 2026 Immigration and Foreigners (Exemption) Amendment Order change?",
        "a" => "Notified on November 5, 2026 under the Immigration and Foreigners Act, the Amendment Order formally incorporates the Central Government's 'Visa Manual' as a statutory reference, clarifies Nepal/Bhutan cross-border and third-country transit rules, requires persecuted minority entrants (arrived on or before Dec 31, 2024) to hold an appropriate visa for continued stay, and updates conditional relief for eligible long-term Pakistani nationals (arrived on or before Dec 31, 2009), Sri Lankan Tamil refugees, and Tibetan refugees."
    ],
    [
        "q" => "What are the updated visa rules for travelers moving via Nepal and Bhutan borders?",
        "a" => "Indian, Nepali, and Bhutanese citizens traveling directly between India, Nepal, and Bhutan can use approved identity documents listed in the official Visa Manual. However, valid passports and appropriate Indian visas are strictly mandatory if arriving from or transiting through third countries or restricted regions such as China, Hong Kong, Macau, or Pakistan."
    ],
    [
        "q" => "How have Medical and Student Visas been restructured?",
        "a" => "Medical Visas now clearly distinguish standard modern medical treatment (M-1, default 1-year multiple entry), medical attendants (M-2), and traditional Indian systems of medicine like Ayurveda, Yoga, Unani, Siddha, and Homeopathy (M-3 AYUSH). Student Visas are organized into S-1 (regular school and higher education) and S-2 (PhD, research, internships, and academic exchange)."
    ],
    [
        "q" => "What biometric and document rules apply on arrival under the Immigration and Foreigners Act?",
        "a" => "Universal biometric capture (fingerprints, facial scan, and iris biometrics) applies at Indian ports of entry. Passports must have at least 6 months validity and 2 blank pages. e-Visas can be applied for up to 120 days in advance across 51 approved airports and seaports, exclusively via the official government portal indianvisaonline.gov.in."
    ]
];

$schema_graphs = [
    [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => $base_url],
            ["@type" => "ListItem", "position" => 2, "name" => "Passport & Visa Tools", "item" => $base_url . "passport"],
            ["@type" => "ListItem", "position" => 3, "name" => "Visa in India: 2026 Amendments", "item" => $canonical_url]
        ]
    ],
    [
        "@context" => "https://schema.org",
        "@type" => "Article",
        "headline" => $page_title,
        "description" => $meta_desc,
        "datePublished" => "2026-11-05",
        "dateModified" => "2026-11-12",
        "author" => [
            "@type" => "Organization",
            "name" => "Online Law",
            "url" => $base_url
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => "Online Law",
            "logo" => [
                "@type" => "ImageObject",
                "url" => $base_url . "images/favicon.png"
            ]
        ],
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => $canonical_url
        ]
    ],
    [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => array_map(function($item) {
            return [
                "@type" => "Question",
                "name" => $item["q"],
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => $item["a"]
                ]
            ];
        }, $faq_items)
    ]
];

$visa_categories = [
    [
        "code" => "V-1 / V-2",
        "name" => "Visitor Visa",
        "tag" => "Formerly Tourist Visa",
        "badge_bg" => "#fff7ed",
        "badge_color" => "#c2410c",
        "icon" => "fa-plane-departure",
        "filter" => "visitor",
        "summary" => "Replaces the traditional Tourist Visa with a broad multi-purpose entry lane covering leisure, short business meetings, wellness/yoga retreats, medical consultations, and short cultural/language courses.",
        "subtypes" => [
            "V-1 (Short-Term): Up to 30 days, multiple entry for tourism, wellness & casual business.",
            "V-2 (Long-Term): 1, 5, or 10 years validity, including mountaineering (6-month multi-entry)."
        ]
    ],
    [
        "code" => "B-1 / B-2",
        "name" => "Business Visa",
        "tag" => "Commerce & Trade",
        "badge_bg" => "#eff6ff",
        "badge_color" => "#1d4ed8",
        "icon" => "fa-briefcase",
        "filter" => "business",
        "summary" => "Separates long-term industrial and commercial venture setups from short-term commercial, trade, sports, cultural, and film engagements.",
        "subtypes" => [
            "B-1: Establishing industrial/business ventures, board meetings & recurring trade.",
            "B-2: Short-term business contracts, conferences, sports events & commercial film shoots."
        ]
    ],
    [
        "code" => "M-1 / M-2 / M-3",
        "name" => "Medical Visa",
        "tag" => "Healthcare & AYUSH",
        "badge_bg" => "#f0fdf4",
        "badge_color" => "#046a38",
        "icon" => "fa-hospital-user",
        "filter" => "medical",
        "summary" => "Streamlined medical value travel framework offering rapid clearance for hospital treatments as well as traditional Indian wellness systems.",
        "subtypes" => [
            "M-1: Standard medical treatment at recognized Indian hospitals (default 1-year multi-entry).",
            "M-2: Medical Attendant visa for accompanying family members/caregivers.",
            "M-3 (AYUSH): Specialized lane for Ayurveda, Yoga, Unani, Siddha & Homeopathy."
        ]
    ],
    [
        "code" => "S-1 / S-2",
        "name" => "Student Visa",
        "tag" => "Education & Research",
        "badge_bg" => "#faf5ff",
        "badge_color" => "#7e22ce",
        "icon" => "fa-user-graduate",
        "filter" => "student",
        "summary" => "Supports India's international education hubs across formal schooling, higher degree programs, doctoral research, and internships.",
        "subtypes" => [
            "S-1: Full-time school, undergraduate, postgraduate & recognized university courses.",
            "S-2: PhD research scholars, academic exchanges, faculty fellowships & internships."
        ]
    ],
    [
        "code" => "W / E",
        "name" => "Work / Employment Visa",
        "tag" => "Skilled Professionals",
        "badge_bg" => "#fefce8",
        "badge_color" => "#a16207",
        "icon" => "fa-user-tie",
        "filter" => "work",
        "summary" => "Granted to foreign skilled professionals, intra-company transferees, project specialists, and technical experts employed by registered Indian entities.",
        "subtypes" => [
            "Skilled Employment & Project execution subject to statutory salary thresholds.",
            "Mandatory e-FRRO registration for stays exceeding 180 days."
        ]
    ],
    [
        "code" => "J",
        "name" => "Journalist Visa",
        "tag" => "Media & Press",
        "badge_bg" => "#fef2f2",
        "badge_color" => "#b91c1c",
        "icon" => "fa-newspaper",
        "filter" => "official",
        "summary" => "Issued to accredited foreign journalists, foreign correspondents stationed in India, and documentary production crews.",
        "subtypes" => [
            "Regular accredited press correspondents & visiting media representatives.",
            "Special clearance protocols for restricted/protected border zones."
        ]
    ],
    [
        "code" => "D",
        "name" => "Diplomatic Visa",
        "tag" => "Diplomatic Missions",
        "badge_bg" => "#f1f5f9",
        "badge_color" => "#0f172a",
        "icon" => "fa-landmark",
        "filter" => "official",
        "summary" => "Reserved for holders of diplomatic passports, heads of missions, diplomats posted to Embassies/High Commissions, and designated UN officials.",
        "subtypes" => [
            "Diplomatic assignment & official bilateral delegation privileges.",
            "Reciprocal Vienna Convention immunities and streamlined mission entry."
        ]
    ],
    [
        "code" => "O",
        "name" => "Official Visa",
        "tag" => "Government Officials",
        "badge_bg" => "#ecfeff",
        "badge_color" => "#0e7490",
        "icon" => "fa-id-badge",
        "filter" => "official",
        "summary" => "Granted to foreign government officials holding official/service passports visiting India on intergovernmental assignments or official duties.",
        "subtypes" => [
            "Non-diplomatic official missions, consular staff & bilateral agency delegations.",
            "Gratis/reciprocal processing based on bilateral agreements."
        ]
    ],
    [
        "code" => "X / Misc",
        "name" => "Miscellaneous Visa",
        "tag" => "Entry, Transit & Special",
        "badge_bg" => "#f8fafc",
        "badge_color" => "#475569",
        "icon" => "fa-passport",
        "filter" => "misc",
        "summary" => "Consolidates specialized non-standard travel needs including Entry (X) visas for Indian-origin families, Transit, Missionary, and Exemption-order stay regularization.",
        "subtypes" => [
            "Entry (X) visas for spouses/dependents of Indian citizens or OCI cardholders.",
            "Transit visas (up to 15 days / 72 hrs stay) and statutory special category clearances."
        ]
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
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">

    <!-- Open Graph / Social -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:image" content="<?php echo $base_url; ?>images/favicon.png">

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($meta_desc); ?>">

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    <?php echo json_encode(["@context" => "https://schema.org", "@graph" => $schema_graphs], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>

    <?php require_once('includes/header.php'); ?>

    <style>
        .visa-hero-section {
            background: linear-gradient(180deg, #ffffff 0%, #fafbfc 55%, #f1f5f9 100%);
            padding: 54px 0 72px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid #e2e8f0;
        }
        .visa-glow-1 {
            position: absolute;
            width: 460px;
            height: 460px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 103, 31, 0.1) 0%, transparent 70%);
            top: -120px;
            left: -80px;
            pointer-events: none;
        }
        .visa-glow-2 {
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(4, 106, 56, 0.08) 0%, transparent 70%);
            bottom: -140px;
            right: -60px;
            pointer-events: none;
        }
        .visa-live-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border: 1.5px solid #fed7aa;
            color: #c2410c;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.7px;
            text-transform: uppercase;
            box-shadow: 0 4px 14px rgba(255, 103, 31, 0.08);
        }
        .pulse-dot-saffron {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6);
            animation: visaPulse 2s infinite;
        }
        @keyframes visaPulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 9px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .visa-stat-strip {
            margin-top: -36px;
            position: relative;
            z-index: 10;
        }
        .visa-stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.06);
            height: 100%;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .visa-stat-card:hover {
            transform: translateY(-3px);
            border-color: #fdba74;
        }
        .amendment-banner-card {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 20px;
            padding: 28px 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.25);
        }
        .amendment-point-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 22px;
            height: 100%;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }
        .amendment-point-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.06);
        }
        .visa-cat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 22px;
            height: 100%;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
        }
        .visa-cat-card:hover {
            transform: translateY(-3px);
            border-color: #fdba74;
            box-shadow: 0 12px 24px rgba(255, 103, 31, 0.08);
        }
        .visa-filter-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 15px;
            border-radius: 50px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .visa-filter-pill:hover,
        .visa-filter-pill.active {
            background: #ff671f;
            border-color: #ff671f;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(255, 103, 31, 0.25);
        }
        .visa-faq-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .visa-faq-btn {
            width: 100%;
            text-align: left;
            background: #ffffff;
            border: none;
            padding: 16px 20px;
            font-weight: 700;
            font-size: 15px;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }
        .visa-faq-btn:focus {
            outline: none;
        }
        .visa-code-pill {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 8px;
        }
    </style>

<!-- ==========================================================================
     HERO BANNER
     ========================================================================== -->
<section class="visa-hero-section">
    <div class="visa-glow-1"></div>
    <div class="visa-glow-2"></div>
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10 col-12">
                <div class="visa-live-badge mb-3">
                    <span class="pulse-dot-saffron"></span>
                    MHA &amp; GAZETTE UPDATES &bull; NOVEMBER 2026 AMENDMENT
                </div>
                <h1 class="font-weight-bold text-dark mb-3" style="font-size: clamp(28px, 4.2vw, 44px); line-height: 1.2; letter-spacing: -0.6px;">
                    Visa in India: <span style="background: linear-gradient(135deg, #ff671f 0%, #ea580c 45%, #046a38 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Latest Amendments &amp; 2026 Rules</span>
                </h1>
                <p class="mb-3 mx-auto" style="color: #475569; font-size: 16px; max-width: 780px; line-height: 1.65;">
                    Comprehensive legal &amp; procedural brief covering the <strong>Immigration and Foreigners (Exemption) (Amendment) Order, 2026</strong>, the MHA Visa Regime Rationalization (<strong>9 Categories / 23 Sub-Categories</strong>), the new <strong>Visitor Visa (V-1 &amp; V-2)</strong>, and updated cross-border compliance rules.
                </p>
                <div class="d-flex flex-wrap justify-content-center align-items-center" style="gap: 10px;">
                    <a href="#nov-2026-order" class="btn btn-primary font-weight-bold px-4 py-2.5 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #ff671f, #ea580c); border: none;">
                        <i class="fas fa-gavel mr-1.5"></i> Read Latest Amendment
                    </a>
                    <a href="#visa-categories" class="btn btn-outline-dark font-weight-bold px-4 py-2.5" style="border-radius: 12px; background: #ffffff;">
                        <i class="fas fa-layer-group mr-1.5"></i> Explore 9 New Categories
                    </a>
                    <a href="<?php echo $base_url; ?>passport" class="btn btn-light border font-weight-bold px-3 py-2.5 text-dark" style="border-radius: 12px;">
                        <i class="fas fa-passport text-danger mr-1.5"></i> Passport Office Locator
                    </a>
                </div>
                <nav class="mt-4 d-flex justify-content-center" aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 m-0" style="font-size: 13.5px;">
                        <li class="breadcrumb-item"><a href="<?php echo $base_url; ?>index" class="text-secondary">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo $base_url; ?>passport" class="text-secondary">Passport &amp; Immigration</a></li>
                        <li class="breadcrumb-item active font-weight-bold text-dark" aria-current="page">Visa in India (2026 Amendment)</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     KEY METRICS STRIP
     ========================================================================== -->
<section class="py-4" style="background: #f8fafc;">
    <div class="container">
        <div class="visa-stat-strip mb-5">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12 mb-3 mb-lg-0">
                    <div class="visa-stat-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small font-weight-bold text-uppercase text-muted">Gazette Amendment</span>
                            <span class="badge badge-warning px-2 py-1" style="background: #fff7ed; color: #c2410c;">Nov 2026</span>
                        </div>
                        <div class="h4 font-weight-bold text-dark mb-1">Exemption Order 2026</div>
                        <p class="small text-muted mb-0">Statutory Visa Manual, Nepal/Bhutan transit &amp; continued stay visa rules.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-3 mb-lg-0">
                    <div class="visa-stat-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small font-weight-bold text-uppercase text-muted">Categories Streamlined</span>
                            <span class="badge badge-success px-2 py-1" style="background: #f0fdf4; color: #046a38;">13 &rarr; 9</span>
                        </div>
                        <div class="h4 font-weight-bold text-dark mb-1">9 Core / 23 Sub-Types</div>
                        <p class="small text-muted mb-0">Sub-categories reduced from 49 to 23 to remove overlapping travel classes.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-3 mb-md-0">
                    <div class="visa-stat-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small font-weight-bold text-uppercase text-muted">Form Simplification</span>
                            <span class="badge badge-primary px-2 py-1" style="background: #eff6ff; color: #1d4ed8;">-70% Fields</span>
                        </div>
                        <div class="h4 font-weight-bold text-dark mb-1">65%–70% Leaner Forms</div>
                        <p class="small text-muted mb-0">Dramatically shorter application columns across all 23 sub-categories.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="visa-stat-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small font-weight-bold text-uppercase text-muted">e-Visa Network</span>
                            <span class="badge badge-info px-2 py-1" style="background: #ecfeff; color: #0e7490;">51 Gateways</span>
                        </div>
                        <div class="h4 font-weight-bold text-dark mb-1">Visitor Visa V-1 / V-2</div>
                        <p class="small text-muted mb-0">Apply up to 120 days ahead across 51 authorized airports &amp; seaports.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================================================================
             PART 1: NOVEMBER 2026 EXEMPTION AMENDMENT ORDER
             ================================================================== -->
        <div id="nov-2026-order" class="mb-5">
            <div class="amendment-banner-card mb-4">
                <div class="row align-items-center">
                    <div class="col-lg-9">
                        <span class="badge badge-pill px-3 py-1 mb-2 font-weight-bold" style="background: rgba(255, 103, 31, 0.22); color: #fdba74; font-size: 11.5px; letter-spacing: 0.6px;">
                            <i class="fas fa-bell mr-1"></i> LATEST STATUTORY NOTIFICATION &bull; NOVEMBER 5, 2026
                        </span>
                        <h2 class="text-white font-weight-bold mb-2" style="font-size: clamp(22px, 3vw, 28px);">
                            Immigration and Foreigners (Exemption) (Amendment) Order, 2026
                        </h2>
                        <p class="mb-0" style="color: #cbd5e1; font-size: 14.5px; line-height: 1.65;">
                            Notified by the Ministry of Home Affairs (MHA) under the <strong>Immigration and Foreigners Act</strong>, modifying the 2025 Exemption Order regarding statutory Visa Manual recognition, cross-border movement with Nepal &amp; Bhutan, and continued-stay documentation for protected/long-term groups.
                        </p>
                    </div>
                    <div class="col-lg-3 text-lg-right mt-3 mt-lg-0">
                        <a href="https://mha.gov.in/" target="_blank" rel="noopener noreferrer" class="btn btn-light font-weight-bold px-3 py-2 shadow-sm" style="border-radius: 10px; font-size: 13.5px;">
                            <i class="fas fa-external-link-alt text-primary mr-1"></i> Official MHA Portal
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-md-6 col-12 mb-4">
                    <div class="amendment-point-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; border-radius: 12px; background: #fff7ed; color: #ff671f; font-size: 18px;">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div>
                                <span class="small font-weight-bold text-uppercase" style="color: #c2410c;">Statutory Recognition</span>
                                <h3 class="h5 font-weight-bold text-dark mb-0">Incorporation of the Official "Visa Manual"</h3>
                            </div>
                        </div>
                        <p class="text-secondary small mb-0" style="line-height: 1.7;">
                            The November 2026 amendment formally defines and integrates the Central Government's <strong>Visa Manual</strong> as the authoritative statutory reference governing visa categories, permissible activities, valid border identity documents, and immigration clearance protocols at all Land, Air, and Sea checkpoints.
                        </p>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-12 mb-4">
                    <div class="amendment-point-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; border-radius: 12px; background: #eff6ff; color: #1d4ed8; font-size: 18px;">
                                <i class="fas fa-route"></i>
                            </div>
                            <div>
                                <span class="small font-weight-bold text-uppercase" style="color: #1d4ed8;">Cross-Border Travel</span>
                                <h3 class="h5 font-weight-bold text-dark mb-0">India &ndash; Nepal &ndash; Bhutan Movement &amp; Transit</h3>
                            </div>
                        </div>
                        <p class="text-secondary small mb-0" style="line-height: 1.7;">
                            Citizens of <strong>India, Nepal, and Bhutan</strong> traveling directly between these countries may rely on approved identity documents listed in the Visa Manual. However, a <strong>valid passport and Indian visa are strictly mandatory</strong> if travelers arrive from or transit via third countries or restricted territories including <strong>China, Hong Kong, Macau, or Pakistan</strong>.
                        </p>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-12 mb-4">
                    <div class="amendment-point-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; border-radius: 12px; background: #f0fdf4; color: #046a38; font-size: 18px;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <span class="small font-weight-bold text-uppercase" style="color: #046a38;">Continued Stay Clarification</span>
                                <h3 class="h5 font-weight-bold text-dark mb-0">Persecuted Minorities (Entry &le; Dec 31, 2024)</h3>
                            </div>
                        </div>
                        <p class="text-secondary small mb-0" style="line-height: 1.7;">
                            Clarifies rules for Hindus, Sikhs, Buddhists, Jains, Parsis, and Christians from <strong>Afghanistan, Bangladesh, and Pakistan</strong> who entered India on or before <strong>December 31, 2024</strong> due to religious persecution. While entry exemptions remain protected, the amended order specifies they <strong>must obtain an appropriate visa category</strong> (such as Long-Term Visa / LTV) for continued lawful stay in India.
                        </p>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-12 mb-4">
                    <div class="amendment-point-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; border-radius: 12px; background: #faf5ff; color: #7e22ce; font-size: 18px;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <span class="small font-weight-bold text-uppercase" style="color: #7e22ce;">Humanitarian Relief</span>
                                <h3 class="h5 font-weight-bold text-dark mb-0">Long-Term Pakistani, Tamil &amp; Tibetan Residents</h3>
                            </div>
                        </div>
                        <p class="text-secondary small mb-0" style="line-height: 1.7;">
                            Extends conditional passport/visa exemption relief to eligible elderly/long-term <strong>Pakistani nationals who arrived on or before December 31, 2009</strong> intending permanent residence but unable to renew passports without a CNIC (subject to security verification), alongside updated documentation protections for registered <strong>Sri Lankan Tamil refugees</strong> (arrived &le; Jan 9, 2015) and designated <strong>Tibetan refugees</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================================================================
             PART 2: BEFORE vs AFTER 2026 VISA OVERHAUL TABLE
             ================================================================== -->
        <div class="card border-0 shadow-sm mb-5" style="border-radius: 20px; overflow: hidden;">
            <div class="card-header bg-white border-bottom p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <span class="badge badge-light border text-primary font-weight-bold mb-1">SIDE-BY-SIDE COMPARISON</span>
                        <h2 class="h4 font-weight-bold text-dark mb-0">Before vs. After the 2026 Indian Visa Amendments</h2>
                    </div>
                    <span class="small text-muted mt-2 mt-md-0"><i class="fas fa-sync-alt text-success mr-1"></i> IT systems transitioning for full late-2026 rollout</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 14px;">
                    <thead style="background: #f8fafc; color: #475569; text-transform: uppercase; font-size: 12px;">
                        <tr>
                            <th class="py-3 px-4">Reform Area</th>
                            <th class="py-3 px-4">Previous Framework</th>
                            <th class="py-3 px-4 text-success">New 2026 Amended Framework</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-3 px-4 font-weight-bold text-dark">Core &amp; Sub-Categories</td>
                            <td class="py-3 px-4 text-muted">13 Main Categories &amp; 49 Sub-Categories (previously 22 &amp; 63)</td>
                            <td class="py-3 px-4 font-weight-semibold text-dark"><span class="badge badge-success mr-1">Streamlined</span> <strong>9 Core Categories</strong> &amp; <strong>23 Sub-Categories</strong></td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-weight-bold text-dark">Tourist Travel</td>
                            <td class="py-3 px-4 text-muted">Separate Tourist Visa, Yoga Visa, Wellness, and short course add-ons</td>
                            <td class="py-3 px-4 font-weight-semibold text-dark">Rebranded as <strong>Visitor Visa (V-1 up to 30 days &amp; V-2 for 1/5/10 years)</strong> covering tourism, yoga, wellness, short courses &amp; casual business</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-weight-bold text-dark">Application Form Fields</td>
                            <td class="py-3 px-4 text-muted">Lengthy multi-page forms with redundant background columns</td>
                            <td class="py-3 px-4 font-weight-semibold text-dark"><strong>65% to 70% fewer form fields</strong> across all 23 sub-categories</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-weight-bold text-dark">Medical &amp; AYUSH</td>
                            <td class="py-3 px-4 text-muted">Clubbed under general medical/tourist visits</td>
                            <td class="py-3 px-4 font-weight-semibold text-dark">Dedicated <strong>M-1 (Modern Care, 1-Yr Multi-Entry)</strong>, <strong>M-2 (Attendant)</strong> &amp; <strong>M-3 (AYUSH)</strong></td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-weight-bold text-dark">Business Visas</td>
                            <td class="py-3 px-4 text-muted">Single broad Business track causing confusion for short-term gigs</td>
                            <td class="py-3 px-4 font-weight-semibold text-dark">Split into <strong>B-1 (Industrial/Venture Setup)</strong> &amp; <strong>B-2 (Contracts, Conferences, Sports, Film)</strong></td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-weight-bold text-dark">Immigration Enforcement</td>
                            <td class="py-3 px-4 text-muted">Fragmented Foreigners Act 1946 &amp; Passport (Entry) Act 1920 rules</td>
                            <td class="py-3 px-4 font-weight-semibold text-dark">Unified <strong>Immigration and Foreigners Act</strong> + universal arrival biometrics &amp; Nov 2026 Exemption Order</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ==================================================================
             PART 3: INTERACTIVE 9 CORE VISA CATEGORIES EXPLORER
             ================================================================== -->
        <div id="visa-categories" class="mb-5">
            <div class="d-flex flex-wrap align-items-end justify-content-between mb-3">
                <div>
                    <span class="small font-weight-bold text-uppercase" style="color: #ff671f; letter-spacing: 0.7px;">RATIONALIZED STRUCTURE</span>
                    <h2 class="h3 font-weight-bold text-dark mb-1">The 9 Core Visa Categories in India (2026)</h2>
                    <p class="text-muted small mb-0">Filter or search below to inspect sub-categories, validities, and allowed activities.</p>
                </div>
                <div class="mt-3 mt-md-0" style="min-width: 280px;">
                    <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="visaCategorySearch" class="form-control border-left-0" placeholder="Filter by code or keyword (e.g. V-1, AYUSH, B-2, PhD)..." style="height: 44px; font-size: 14px;">
                    </div>
                </div>
            </div>

            <!-- Category Filter Pills -->
            <div class="d-flex flex-wrap mb-4" style="gap: 8px;" id="visaFilterPills">
                <button type="button" class="visa-filter-pill active" data-filter="all"><i class="fas fa-th-large"></i> All 9 Categories</button>
                <button type="button" class="visa-filter-pill" data-filter="visitor"><i class="fas fa-plane-departure"></i> Visitor (V-1 / V-2)</button>
                <button type="button" class="visa-filter-pill" data-filter="business"><i class="fas fa-briefcase"></i> Business (B-1 / B-2)</button>
                <button type="button" class="visa-filter-pill" data-filter="medical"><i class="fas fa-hospital-user"></i> Medical (M-1 / M-3)</button>
                <button type="button" class="visa-filter-pill" data-filter="student"><i class="fas fa-user-graduate"></i> Student (S-1 / S-2)</button>
                <button type="button" class="visa-filter-pill" data-filter="work"><i class="fas fa-user-tie"></i> Work / Employment</button>
                <button type="button" class="visa-filter-pill" data-filter="official"><i class="fas fa-landmark"></i> Diplomatic / Official / Press</button>
                <button type="button" class="visa-filter-pill" data-filter="misc"><i class="fas fa-passport"></i> Miscellaneous / Entry (X)</button>
            </div>

            <div class="row" id="visaCategoryGrid">
                <?php foreach ($visa_categories as $cat): ?>
                <div class="col-lg-4 col-md-6 col-12 mb-4 visa-cat-col"
                     data-filter="<?php echo htmlspecialchars($cat['filter']); ?>"
                     data-search="<?php echo htmlspecialchars(strtolower($cat['code'] . ' ' . $cat['name'] . ' ' . $cat['tag'] . ' ' . $cat['summary'] . ' ' . implode(' ', $cat['subtypes']))); ?>">
                    <div class="visa-cat-card d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="visa-code-pill" style="background: <?php echo $cat['badge_bg']; ?>; color: <?php echo $cat['badge_color']; ?>;">
                                <?php echo htmlspecialchars($cat['code']); ?>
                            </span>
                            <span class="small font-weight-semibold text-muted"><?php echo htmlspecialchars($cat['tag']); ?></span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas <?php echo $cat['icon']; ?> mr-2" style="color: <?php echo $cat['badge_color']; ?>; font-size: 18px;"></i>
                            <h3 class="h5 font-weight-bold text-dark mb-0"><?php echo htmlspecialchars($cat['name']); ?></h3>
                        </div>
                        <p class="text-secondary small mb-3" style="line-height: 1.6;"><?php echo htmlspecialchars($cat['summary']); ?></p>
                        <div class="mt-auto pt-3 border-top" style="border-color: #f1f5f9 !important;">
                            <ul class="pl-3 mb-0 small text-dark" style="line-height: 1.55;">
                                <?php foreach ($cat['subtypes'] as $sub): ?>
                                    <li class="mb-1"><?php echo htmlspecialchars($sub); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div id="visaNoMatchAlert" class="alert alert-light border text-center py-4 d-none" style="border-radius: 14px;">
                <i class="fas fa-search text-muted mr-2"></i> No visa categories matched your filter. <button type="button" class="btn btn-link p-0 font-weight-bold" onclick="resetVisaFilter()">Reset Filters</button>
            </div>
        </div>

        <!-- ==================================================================
             PART 4: COMPLIANCE, e-VISA CHECKLIST & OFFICIAL WARNING
             ================================================================== -->
        <div class="row mb-5">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm h-100 p-4" style="border-radius: 20px;">
                    <h3 class="h5 font-weight-bold text-dark mb-3">
                        <i class="fas fa-fingerprint text-primary mr-2"></i> e-Visa Rules, Biometrics &amp; Penalties Checklist
                    </h3>
                    <div class="list-group list-group-flush small">
                        <div class="list-group-item px-0 py-3 border-light">
                            <strong class="text-dark d-block mb-1"><i class="fas fa-check-circle text-success mr-1.5"></i> 120-Day Advance e-Visa Window &amp; Passport Rules</strong>
                            <span class="text-muted">Foreign applicants can file e-Visas up to <strong>120 days</strong> prior to arrival. Passports must retain at least <strong>6 months validity</strong> from the date of arrival and have a minimum of <strong>2 blank pages</strong>.</span>
                        </div>
                        <div class="list-group-item px-0 py-3 border-light">
                            <strong class="text-dark d-block mb-1"><i class="fas fa-check-circle text-success mr-1.5"></i> AI-Enabled Photo &amp; Document Screening</strong>
                            <span class="text-muted">BOI portals use automated verification for digital photos and passport bio-pages. Blurry scans, cropped edges, or casual selfies trigger automated rejections.</span>
                        </div>
                        <div class="list-group-item px-0 py-3 border-light">
                            <strong class="text-dark d-block mb-1"><i class="fas fa-check-circle text-success mr-1.5"></i> Mandatory Arrival Biometrics (Fingerprints, Face &amp; Iris)</strong>
                            <span class="text-muted">Under the unified <strong>Immigration and Foreigners Act</strong>, biometric capture at any of India's <strong>51 approved e-Visa gateways</strong> is legally mandatory for immigration clearance.</span>
                        </div>
                        <div class="list-group-item px-0 py-3 border-0 pb-0">
                            <strong class="text-dark d-block mb-1"><i class="fas fa-exclamation-triangle text-danger mr-1.5"></i> Strict Overstay &amp; Forgery Penalties</strong>
                            <span class="text-muted">Unauthorized overstay beyond visa validity, working on a Visitor Visa, or presenting altered travel records attracts heavy statutory fines, prosecution, imprisonment, and blacklisting via FRRO/BOI.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100 p-4" style="border-radius: 20px; background: linear-gradient(180deg, #fff7ed 0%, #ffffff 100%); border: 1px solid #fed7aa !important;">
                    <span class="badge badge-danger align-self-start px-3 py-1 mb-2" style="background: #dc2626;">OFFICIAL PORTALS ONLY</span>
                    <h3 class="h5 font-weight-bold text-dark mb-2">Verify &amp; Apply Without Third-Party Markups</h3>
                    <p class="small text-secondary mb-3" style="line-height: 1.65;">
                        Beware of unofficial commercial websites mimicking Indian government portals and charging inflated service fees. Always use official <code>.gov.in</code> gateways for visa filing, e-FRRO extensions, or Passport Seva services:
                    </p>
                    <div class="d-flex flex-column mb-3" style="gap: 10px;">
                        <a href="https://indianvisaonline.gov.in/" target="_blank" rel="noopener noreferrer" class="btn btn-white border text-left d-flex align-items-center justify-content-between py-2.5 px-3 shadow-xs" style="border-radius: 12px; background: #ffffff;">
                            <span><strong class="d-block text-dark small">Indian Visa Online (BOI / MHA)</strong><small class="text-muted">indianvisaonline.gov.in</small></span>
                            <i class="fas fa-external-link-alt text-primary"></i>
                        </a>
                        <a href="https://indianfrro.gov.in/" target="_blank" rel="noopener noreferrer" class="btn btn-white border text-left d-flex align-items-center justify-content-between py-2.5 px-3 shadow-xs" style="border-radius: 12px; background: #ffffff;">
                            <span><strong class="d-block text-dark small">e-FRRO Registration &amp; Visa Extension</strong><small class="text-muted">indianfrro.gov.in</small></span>
                            <i class="fas fa-external-link-alt text-success"></i>
                        </a>
                        <a href="<?php echo $base_url; ?>passport" class="btn btn-white border text-left d-flex align-items-center justify-content-between py-2.5 px-3 shadow-xs" style="border-radius: 12px; background: #ffffff;">
                            <span><strong class="d-block text-dark small">Locate Passport Seva Kendra (PSK / RPO)</strong><small class="text-muted">Search 784+ Centers on Online Law</small></span>
                            <i class="fas fa-arrow-right text-danger"></i>
                        </a>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 11.5px; line-height: 1.5;">
                        <i class="fas fa-info-circle mr-1"></i> <strong>Legal Disclaimer:</strong> This summary is provided for general legal awareness based on official MHA press releases and Gazette notifications up to November 2026. Always verify current bilateral fee schedules and port advisories on <code>indianvisaonline.gov.in</code> prior to travel.
                    </p>
                </div>
            </div>
        </div>

        <!-- ==================================================================
             PART 5: FREQUENTLY ASKED QUESTIONS (FAQ)
             ================================================================== -->
        <div class="card border-0 shadow-sm p-4 p-md-5" style="border-radius: 22px;">
            <div class="d-flex align-items-center mb-4">
                <div class="mr-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; border-radius: 12px; background: #fff7ed; color: #ff671f; font-size: 20px;">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div>
                    <h2 class="h4 font-weight-bold text-dark mb-0">Frequently Asked Questions: India Visa Amendments</h2>
                    <p class="text-muted small mb-0">Key answers on the November 2026 Exemption Order, Visitor Visas &amp; MHA reforms.</p>
                </div>
            </div>

            <div class="accordion" id="visaFaqAccordion">
                <?php foreach ($faq_items as $idx => $faq): ?>
                <div class="visa-faq-item">
                    <button class="visa-faq-btn" type="button" data-toggle="collapse" data-target="#visaFaq<?php echo $idx; ?>" aria-expanded="<?php echo $idx === 0 ? 'true' : 'false'; ?>">
                        <span><?php echo htmlspecialchars($faq['q']); ?></span>
                        <i class="fas fa-chevron-down text-muted small ml-2"></i>
                    </button>
                    <div id="visaFaq<?php echo $idx; ?>" class="collapse <?php echo $idx === 0 ? 'show' : ''; ?>" data-parent="#visaFaqAccordion">
                        <div class="px-4 pb-3 pt-1 text-secondary small" style="line-height: 1.7;">
                            <?php echo htmlspecialchars($faq['a']); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>

<script>
(function() {
    var currentFilter = 'all';
    var searchInput = document.getElementById('visaCategorySearch');
    var pillsContainer = document.getElementById('visaFilterPills');
    var cards = document.querySelectorAll('.visa-cat-col');
    var noMatch = document.getElementById('visaNoMatchAlert');

    function applyVisaFilter() {
        var q = (searchInput ? searchInput.value : '').trim().toLowerCase();
        var visibleCount = 0;

        cards.forEach(function(card) {
            var cat = card.getAttribute('data-filter') || '';
            var text = card.getAttribute('data-search') || '';
            var matchCat = (currentFilter === 'all' || cat === currentFilter);
            var matchText = (!q || text.indexOf(q) !== -1);

            if (matchCat && matchText) {
                card.classList.remove('d-none');
                visibleCount++;
            } else {
                card.classList.add('d-none');
            }
        });

        if (noMatch) {
            noMatch.classList.toggle('d-none', visibleCount > 0);
        }
    }

    if (pillsContainer) {
        pillsContainer.addEventListener('click', function(e) {
            var btn = e.target.closest('.visa-filter-pill');
            if (!btn) return;
            pillsContainer.querySelectorAll('.visa-filter-pill').forEach(function(p) {
                p.classList.remove('active');
            });
            btn.classList.add('active');
            currentFilter = btn.getAttribute('data-filter') || 'all';
            applyVisaFilter();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyVisaFilter);
    }

    window.resetVisaFilter = function() {
        currentFilter = 'all';
        if (searchInput) searchInput.value = '';
        if (pillsContainer) {
            pillsContainer.querySelectorAll('.visa-filter-pill').forEach(function(p) {
                p.classList.toggle('active', p.getAttribute('data-filter') === 'all');
            });
        }
        applyVisaFilter();
    };
})();
</script>

<?php require_once('includes/footer.php'); ?>
