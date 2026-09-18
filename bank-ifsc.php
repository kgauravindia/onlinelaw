<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tag -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
    // SEO & URL Parameter Handling
    require_once ('includes/function.php'); 
    $ifsccode = "";
    if(isset($_GET['ifsc'])) {
        $ifsccode = strtoupper(trim($_GET['ifsc']));
        $_REQUEST['ifsccode'] = $ifsccode;
    } elseif(isset($_POST['ifsccode'])) {
        $ifsccode = strtoupper(trim($_POST['ifsccode']));
    }

    $schema_graphs = [];
    $row_seo = null;
    $faq_items = [];

    if(!empty($ifsccode)) {
        // Query database for a premium SEO title
        $safe_ifsc_query = addslashes($ifsccode);
        $res_seo = direct_sql("select * from bank_ifsc where ifsc = '$safe_ifsc_query' limit 1");
        if($res_seo['count'] > 0) {
            $row_seo = $res_seo['data'][0];
            $bank_name = $row_seo['bank'];
            $branch_name = $row_seo['branch'];
            $city_name = $row_seo['city2'];
            $state_name = $row_seo['state'];
            $micr_val = (!empty($row_seo['micr']) && $row_seo['micr'] != 'NA' && $row_seo['micr'] != '-') ? $row_seo['micr'] : 'N/A';
            
            $canonical_url = $base_url . "bank-ifsc/" . $ifsccode;
            $page_title = "$ifsccode IFSC Code - $bank_name, $branch_name Branch ($city_name, $state_name) | Online Law";
            $meta_desc = "Find $bank_name $branch_name branch IFSC Code: $ifsccode (MICR Code: $micr_val). Address: " . $row_seo['address'] . ", $city_name, $state_name. 100% verified RBI code for NEFT, RTGS & IMPS transfers.";
            $meta_keywords = "$ifsccode, $ifsccode IFSC Code, $bank_name $branch_name IFSC, $bank_name IFSC Code, $city_name Bank Branch, NEFT RTGS IMPS Code, RBI IFSC Code $ifsccode, Online Law";

            // 1. BreadcrumbList Schema
            $schema_graphs[] = [
                "@context" => "https://schema.org",
                "@type" => "BreadcrumbList",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => "Home",
                        "item" => $base_url
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 2,
                        "name" => "Bank IFSC Code Search",
                        "item" => $base_url . "bank-ifsc"
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 3,
                        "name" => "$bank_name ($branch_name)",
                        "item" => $canonical_url
                    ]
                ]
            ];

            // 2. BankOrCreditUnion / FinancialService Entity Schema
            $bank_schema = [
                "@context" => "https://schema.org",
                "@type" => ["BankOrCreditUnion", "FinancialService"],
                "name" => "$bank_name, $branch_name Branch",
                "alternateName" => "$ifsccode Branch",
                "url" => $canonical_url,
                "branchOf" => [
                    "@type" => "BankOrCreditUnion",
                    "name" => $bank_name
                ],
                "identifier" => $ifsccode,
                "address" => [
                    "@type" => "PostalAddress",
                    "streetAddress" => $row_seo['address'],
                    "addressLocality" => $city_name,
                    "addressRegion" => $state_name,
                    "addressCountry" => "IN"
                ],
                "paymentAccepted" => ["NEFT", "RTGS", "IMPS", "UPI"],
                "currenciesAccepted" => "INR"
            ];
            if (!empty($row_seo['phone']) && $row_seo['phone'] != '0') {
                $bank_schema["telephone"] = (!empty($row_seo['stdcode']) ? '+' . $row_seo['stdcode'] . '-' : '') . $row_seo['phone'];
            }
            $schema_graphs[] = $bank_schema;

            // 3. FAQ Items for on-page and FAQPage schema
            $faq_items = [
                [
                    "q" => "What is the IFSC Code for $bank_name $branch_name branch?",
                    "a" => "The IFSC code for $bank_name $branch_name branch in $city_name, $state_name is $ifsccode. It is used for NEFT, RTGS, and IMPS electronic money transfers."
                ],
                [
                    "q" => "What is the MICR Code for $bank_name $branch_name branch?",
                    "a" => ($micr_val !== 'N/A') 
                        ? "The MICR code of $bank_name $branch_name branch is $micr_val, used for magnetic ink character recognition during cheque clearance."
                        : "The MICR code for $bank_name $branch_name branch is not allocated or is non-MICR in official RBI records."
                ],
                [
                    "q" => "Where is the $bank_name $branch_name branch located?",
                    "a" => "The complete branch address is: " . $row_seo['address'] . ", District: $city_name, State: $state_name, India."
                ],
                [
                    "q" => "Can I transfer money using IFSC $ifsccode?",
                    "a" => "Yes, IFSC code $ifsccode can be used 24x7 for online money transfers via NEFT, RTGS, IMPS, and UPI apps to any account at $bank_name $branch_name branch."
                ]
            ];

            $faq_schema = [
                "@context" => "https://schema.org",
                "@type" => "FAQPage",
                "mainEntity" => []
            ];
            foreach ($faq_items as $item) {
                $faq_schema["mainEntity"][] = [
                    "@type" => "Question",
                    "name" => $item["q"],
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => $item["a"]
                    ]
                ];
            }
            $schema_graphs[] = $faq_schema;

        } else {
            $canonical_url = $base_url . "bank-ifsc";
            $page_title = "IFSC Code $ifsccode Details - Online Law";
            $meta_desc = "Find details for IFSC Code $ifsccode on Online Law. Fully updated RBI bank branch directory.";
            $meta_keywords = "IFSC Code $ifsccode, Bank IFSC Search, Online Law";

            $schema_graphs[] = [
                "@context" => "https://schema.org",
                "@type" => "BreadcrumbList",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => "Home",
                        "item" => $base_url
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 2,
                        "name" => "Bank IFSC Code Search",
                        "item" => $canonical_url
                    ]
                ]
            ];
        }
    } else {
        $canonical_url = $base_url . "bank-ifsc";
        $page_title = "Bank IFSC Code Search 2026 | Find Any Branch IFSC & MICR in India - Online Law";
        $meta_desc = "Quickly search and verify IFSC & MICR codes for all 180,000+ bank branches across India. Find branch addresses, contact info, and NEFT, RTGS & IMPS details updated for 2026.";
        $meta_keywords = "IFSC Code Search, Find Bank IFSC, RBI IFSC List 2026, NEFT RTGS Codes, Bank Branch Finder India, MICR Code Search, Net Banking Codes, Online Law";

        $schema_graphs[] = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                [
                    "@type" => "ListItem",
                    "position" => 1,
                    "name" => "Home",
                    "item" => $base_url
                ],
                [
                    "@type" => "ListItem",
                    "position" => 2,
                    "name" => "Bank IFSC Code Search",
                    "item" => $canonical_url
                ]
            ]
        ];
    }

    $seo_schema = "";
    foreach($schema_graphs as $sg) {
        $seo_schema .= '<script type="application/ld+json">' . "\n" . json_encode($sg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n" . '</script>' . "\n";
    }
?>
<meta name="description" content="<?php echo htmlspecialchars($meta_desc); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
<meta name="author" content="OfferPlant">
<title><?php echo htmlspecialchars($page_title); ?></title>

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="Online Law">
<meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($meta_desc); ?>">
<meta property="og:image" content="<?php echo $base_url; ?>images/favicon.png">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($meta_desc); ?>">
<meta name="twitter:image" content="<?php echo $base_url; ?>images/favicon.png">

<!-- Schema.org JSON-LD -->
<?php echo $seo_schema; ?>

<style>
    @media (max-width: 767px) {
        .banner-tittle { font-size: 26px !important; }
        .detail-area { padding: 25px 15px !important; }
        .desktop-only { display: none !important; }
        .mobile-only { display: block !important; }
    }
    @media (min-width: 768px) {
        .mobile-only { display: none !important; }
        .desktop-only { display: block !important; }
    }
    .bank-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 20px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .bank-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .copy-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .ifsc-badge-code {
        font-family: monospace, var(--font-body);
        letter-spacing: 0.5px;
    }
    .faq-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        overflow: hidden;
    }
    .faq-btn {
        text-decoration: none !important;
        font-weight: 600;
        color: #1e293b;
        font-size: 15px;
    }
    .faq-btn:hover {
        color: var(--primary-color);
    }
</style>

<?php require_once ('includes/header.php'); ?> 

<!-- Banner Section Start -->
<section class="bg-img-3 overlay p-0 full-row" style="padding: 20px 0 !important;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="banner-text text-center">
					<h2 class="text-white banner-tittle position-relative mb-1" style="font-family: var(--font-heading) !important; font-size: 32px; margin: 0;">Find Bank IFSC Code</h2>
					<ul class="list-inline mt-2 mb-0" style="color: #cbd5e1; font-family: var(--font-body); font-size: 15px;">
						<li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
						<li class="list-inline-item mx-2">/</li>
						<li class="list-inline-item" style="color: #fff; font-weight: 500;">Find IFSC Code</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Banner Section End --> 

<section class="full-row bg-body py-80" style="padding: 60px 0;">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="detail-area p-3 p-md-5" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); background: #fff;">
					<div class="d-flex flex-column align-items-center text-center mb-4">
                        <?php if(!empty($row_seo)): ?>
                            <h1 class="pb-2 mb-1" style="font-family: var(--font-heading); font-weight: 700; font-size: calc(1.3rem + 0.6vw); color: #0f172a;">
                                <?php echo htmlentities($row_seo['bank']); ?> (<?php echo htmlentities($row_seo['branch']); ?>) IFSC Code
                            </h1>
                            <div class="mb-3">
                                <span class="badge badge-primary py-2 px-3 ifsc-badge-code" style="font-size: 16px; font-weight: 700; border-radius: 8px;">
                                    <i class="fas fa-barcode mr-1"></i> <?php echo htmlentities($ifsccode); ?>
                                </span>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 14px; max-width: 750px;">
                                Verified Indian Financial System Code (IFSC) for <b><?php echo htmlentities($row_seo['bank']); ?></b>, <?php echo htmlentities($row_seo['branch']); ?> branch, <?php echo htmlentities($row_seo['city2']); ?>, <?php echo htmlentities($row_seo['state']); ?>.
                            </p>
                        <?php else: ?>
                            <h1 class="pb-2" style="font-family: var(--font-heading); font-weight: 700; font-size: calc(1.3rem + 0.6vw); color: #0f172a;">Indian Financial System Code (IFSC) Search</h1>
                            <span class="text-muted" style="font-size: 14px;">Enter an 11-digit alphanumeric IFSC code below to retrieve detailed branch information.</span>
                        <?php endif; ?>
						
						<div class="mt-3">
							<div class="d-inline-flex align-items-center justify-content-center p-2 px-3 text-center" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary-color); font-weight: 600; border-radius: 12px; font-size: 13px; border: 1px solid rgba(var(--primary-rgb), 0.2); line-height: 1.5; max-width: 100%;">
                                <div class="px-1">
								    <i class="fas fa-check-circle mr-1"></i> 181,913 Bank IFSC Added <span class="d-block d-md-inline-block text-muted" style="font-weight: 500; font-size: 12px;">(Last Update: 15 Aug 2026 - RBI Official)</span>
                                </div>
							</div>
						</div>
                        
                        <!-- News Ticker / Latest News -->
                        <div class="mt-4 w-100" style="background: #fff8f1; border: 1px dashed #fb923c; border-radius: 12px; padding: 12px 20px; overflow: hidden;">
                            <div class="d-flex align-items-center">
                                <span class="badge py-1 px-2 mr-3" style="background: #ea580c; color: #fff; font-size: 11px; font-weight: 700; border-radius: 6px; text-transform: uppercase;">Latest News</span>
                                <div style="font-size: 14px; color: #7c2d12; font-weight: 500;">
                                    <i class="fas fa-bullhorn mr-2 text-warning"></i> 1,096 bank branches have been recently inaugurated and updated in the RBI official record as of 15 Aug 2026.
                                </div>
                            </div>
                        </div>
					</div>
					
					<form method="post" action="<?php echo $base_url; ?>bank-ifsc" class="form-style-1 mb-5">
					  <div class="row justify-content-center">
						<div class="col-lg-5 col-md-8">
						  <div class="form-group mb-0 position-relative">
                            <i class="fas fa-university position-absolute" style="top: 50%; transform: translateY(-50%); left: 20px; color: var(--text-muted);"></i>
							<input class="form-control text-uppercase ifsc-badge-code" style="padding-left: 50px !important; height: 55px; border-radius: var(--radius-md); text-transform: uppercase;" type="text" placeholder="e.g. SBIN0000001" maxlength="11" minlength="11" name="ifsccode" value="<?php echo htmlentities($ifsccode); ?>" required>
						  </div>
						</div>
						<div class="col-lg-2 col-md-4 mt-3 mt-md-0">
						  <div class="form-group mb-0">
							<input id="submit_btn" class="btn-primary btn w-100" style="height: 55px; font-size: 16px; border-radius: var(--radius-md); padding-top: 0; padding-bottom: 0;" type="submit" value="Search">
						  </div>
						</div>
					  </div>
					</form>
					
					<!-- Table Results -->
					<?php 
                        if(!empty($ifsccode))
                        {
                            $safe_ifsc = addslashes($ifsccode);
                            $sql = "select * from bank_ifsc where ifsc ='$safe_ifsc' order by branch";
                            $res = direct_sql($sql);
                            $ct =$res['count'];
                            
                            if($ct > 0)
                            {
                                $i=1;
                    ?>
                                <div class="results-container mt-4">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                                        <h4 class="text-primary mb-2 mb-md-0" style="font-family: var(--font-heading); font-weight: 600;">
                                            Showing results for IFSC Code "<span class="ifsc-badge-code"><?php echo htmlentities($ifsccode); ?></span>"
                                        </h4>
                                        <div class="d-flex flex-wrap gap-2" style="gap: 8px;">
                                            <button type="button" class="btn btn-sm btn-outline-primary copy-btn" onclick="copyToClipboard('<?php echo $ifsccode; ?>', this)" title="Copy IFSC Code">
                                                <i class="far fa-copy mr-1"></i> Copy IFSC
                                            </button>
                                            <a href="<?php echo $base_url; ?>bank-micr?ifsc=<?php echo $ifsccode; ?>" class="btn btn-sm btn-outline-secondary" title="View MICR Details">
                                                <i class="fas fa-money-check-alt mr-1"></i> Check MICR
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Desktop Table View -->
                                    <div class="table-responsive desktop-only">
                                        <table class="display responsive nowrap w-100" id="ifsccode_table">
                                            <thead> 
                                            <tr>
                                                <th> No </th>
                                                <th> Bank Name </th>
                                                <th> IFSC / MICR </th>
                                                <th> Branch</th>
                                                <th> District</th>
                                                <th>State / UT</th>
                                                <th> Address</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach( $res['data'] as $row) { ?>
                                                <tr>
                                                    <td> <?php echo $i++; ?> </td>
                                                    <td> 
                                                        <span style="font-weight: 600; color: var(--text-main);"><?php echo htmlentities($row['bank']); ?></span>
                                                        <br>
                                                        <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name?branch=<?php echo urlencode($row['bank']); ?>" class="text-muted" style="font-size: 11px; text-decoration: none;">
                                                            <i class="fas fa-search mr-1"></i>All branches
                                                        </a>
                                                    </td>
                                                    <td> 
                                                        <div class="d-flex align-items-center mb-1">
                                                            <a href="<?php echo $base_url; ?>bank-ifsc/<?php echo $row['ifsc']; ?>" class="badge ifsc-badge-code" style="background: var(--primary-light); color: var(--primary-color); font-size: 14px; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-weight: 700;"><?php echo htmlentities($row['ifsc']); ?></a>
                                                            <button type="button" class="btn btn-sm btn-outline-primary ml-2 py-0 px-2 copy-btn" onclick="copyToClipboard('<?php echo $row['ifsc']; ?>', this)" title="Copy IFSC" style="height: 26px; font-size: 11px;">
                                                                <i class="far fa-copy"></i>
                                                            </button>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <span style="font-size: 11px; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0;">MICR: <?php echo !empty($row['micr']) && $row['micr'] != 'NA' && $row['micr'] != '-' ? htmlentities($row['micr']) : 'N/A'; ?></span> 
                                                            <?php if (!empty($row['micr']) && $row['micr'] != 'NA' && $row['micr'] != '-'): ?>
                                                                <button type="button" class="btn btn-sm btn-outline-secondary ml-1 py-0 px-1 copy-btn" onclick="copyToClipboard('<?php echo $row['micr']; ?>', this)" title="Copy MICR" style="height: 20px; font-size: 10px;">
                                                                    <i class="far fa-copy"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td> <?php echo htmlentities($row['branch']); ?> </td>
                                                    <td> <?php echo htmlentities($row['city2']); ?> </td>
                                                    <td> <?php echo htmlentities($row['state']); ?> </td>
                                                    <td style="white-space: normal; max-width: 250px;"> <?php echo htmlentities($row['address']); ?> </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Mobile Card View -->
                                    <div class="mobile-only">
                                        <?php 
                                        $mi = 1;
                                        foreach($res['data'] as $row) { ?>
                                            <div class="bank-card mb-4 p-4">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h5 class="mb-0 text-primary" style="font-weight: 800; font-size: 18px; line-height: 1.3; margin-right: 10px;"><?php echo htmlentities($row['bank']); ?></h5>
                                                    <div class="text-right">
                                                        <div class="d-flex align-items-center justify-content-end mb-1">
                                                            <a href="<?php echo $base_url; ?>bank-ifsc/<?php echo $row['ifsc']; ?>" class="badge ifsc-badge-code" style="background: #eff6ff; color: #1e40af; font-size: 12px; padding: 6px 12px; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-block;"><?php echo htmlentities($row['ifsc']); ?></a>
                                                            <button type="button" class="btn btn-sm btn-outline-primary ml-1 py-0 px-2 copy-btn" onclick="copyToClipboard('<?php echo $row['ifsc']; ?>', this)" title="Copy IFSC" style="height: 26px; font-size: 11px;">
                                                                <i class="far fa-copy"></i>
                                                            </button>
                                                        </div>
                                                        <div style="font-size: 10px; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0; display: inline-block;">MICR: <?php echo !empty($row['micr']) && $row['micr'] != 'NA' && $row['micr'] != '-' ? htmlentities($row['micr']) : 'N/A'; ?></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3 d-flex align-items-center">
                                                    <i class="fas fa-code-branch text-muted mr-2" style="font-size: 12px;"></i>
                                                    <span class="text-dark font-weight-bold" style="font-size: 14px;"><?php echo htmlentities($row['branch']); ?></span>
                                                </div>

                                                <div class="p-3 mb-3" style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px;">
                                                    <div class="d-flex">
                                                        <i class="fas fa-map-marker-alt text-danger mt-1 mr-2" style="font-size: 13px;"></i>
                                                        <p class="mb-0 text-dark" style="font-size: 13px; line-height: 1.6;"><?php echo htmlentities($row['address']); ?></p>
                                                    </div>
                                                    <div class="mt-2 text-muted px-4" style="font-size: 12px;">
                                                        <?php echo htmlentities($row['city2']); ?>, <?php echo htmlentities($row['state']); ?>
                                                    </div>
                                                </div>

                                                <!-- Quick Actions -->
                                                <div class="d-flex flex-wrap gap-2 pt-2" style="gap: 8px;">
                                                    <a href="<?php echo $base_url; ?>bank-micr?ifsc=<?php echo $row['ifsc']; ?>" class="btn btn-sm btn-light border text-secondary font-weight-bold" style="font-size: 12px; border-radius: 8px;">
                                                        <i class="fas fa-money-check-alt mr-1 text-primary"></i> MICR Code
                                                    </a>
                                                    <a href="<?php echo $base_url; ?>bank-ifsc-by-branch-name?branch=<?php echo urlencode($row['bank']); ?>" class="btn btn-sm btn-light border text-secondary font-weight-bold" style="font-size: 12px; border-radius: 8px;">
                                                        <i class="fas fa-university mr-1 text-primary"></i> All <?php echo htmlentities($row['bank']); ?> Branches
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                <?php
                                // Nearest Banks Logic derived from the first search result
                                $bank_ref = $res['data'][0]['bank'];
                                $city_ref = $res['data'][0]['city2'];
                                $state_ref = $res['data'][0]['state'];
                                
                                $safe_bank = addslashes($bank_ref);
                                $safe_city = addslashes($city_ref);
                                $safe_state = addslashes($state_ref);
                                $safe_ifsc = addslashes($res['data'][0]['ifsc']);

                                // Very Near (Same Bank in Same City)
                                $very_near_sql = "SELECT DISTINCT branch, ifsc, city2 FROM bank_ifsc WHERE bank = '$safe_bank' AND city2 = '$safe_city' AND ifsc != '$safe_ifsc' ORDER BY branch LIMIT 12";
                                $very_near_res = direct_sql($very_near_sql);

                                // Nearby (Same Bank in Same State, excluding Same City)
                                $nearby_sql = "SELECT DISTINCT branch, ifsc, city2 FROM bank_ifsc WHERE bank = '$safe_bank' AND state = '$safe_state' AND city2 != '$safe_city' ORDER BY branch LIMIT 12";
                                $nearby_res = direct_sql($nearby_sql);

                                if($very_near_res['count'] > 0 || $nearby_res['count'] > 0) {
                ?>
                                    <div class="nearby-banks mt-5 pt-4" style="border-top: 1px solid #eef2f6;">
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 40px; height: 40px; background: #fff7ed; color: #ea580c; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                                <i class="fas fa-university"></i>
                                            </div>
                                            <div>
                                                <h4 class="mb-0" style="font-family: var(--font-heading); font-weight: 700; color: #1e293b; font-size: 20px;">Other Nearby Branches</h4>
                                                <p class="mb-0 text-muted" style="font-size: 13px;">Find more branches of <?php echo htmlentities($bank_ref); ?> in this region</p>
                                            </div>
                                        </div>

                                        <?php if($very_near_res['count'] > 0) { ?>
                                            <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="font-size: 11px; letter-spacing: 1px;">In <?php echo htmlentities($city_ref); ?> District</h6>
                                            <div class="row mb-4">
                                                <?php foreach($very_near_res['data'] as $rel) { ?>
                                                    <div class="col-lg-3 col-md-4 col-6 mb-3">
                                                        <a href="<?php echo $base_url; ?>bank-ifsc/<?php echo $rel['ifsc']; ?>" class="d-block p-3 text-center text-decoration-none" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                                            <div style="font-weight: 700; color: #3b82f6; font-size: 15px;"><?php echo htmlentities($rel['branch']); ?></div>
                                                            <div class="text-muted ifsc-badge-code" style="font-size: 12px; font-weight: 600;"><?php echo htmlentities($rel['ifsc']); ?></div>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>

                                        <?php if($nearby_res['count'] > 0) { ?>
                                            <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="font-size: 11px; letter-spacing: 1px;">Other Branches in <?php echo htmlentities($state_ref); ?></h6>
                                            <div class="row">
                                                <?php foreach($nearby_res['data'] as $rel) { ?>
                                                    <div class="col-lg-3 col-md-4 col-6 mb-3">
                                                        <a href="<?php echo $base_url; ?>bank-ifsc/<?php echo $rel['ifsc']; ?>" class="d-block p-3 text-center text-decoration-none" style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; transition: all 0.2s ease;">
                                                            <div style="font-weight: 700; color: #64748b; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlentities($rel['branch']); ?></div>
                                                            <div class="text-muted ifsc-badge-code" style="font-size: 11px;"><?php echo htmlentities($rel['city2']); ?> - <?php echo htmlentities($rel['ifsc']); ?></div>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                <?php
                                }

                                // FAQ Section for High Search Ranking and Helpful Content
                                if(!empty($faq_items)) {
                ?>
                                    <div class="faq-section mt-5 pt-4" style="border-top: 1px solid #eef2f6;">
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 40px; height: 40px; background: #eff6ff; color: #2563eb; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                                <i class="fas fa-question-circle"></i>
                                            </div>
                                            <div>
                                                <h4 class="mb-0" style="font-family: var(--font-heading); font-weight: 700; color: #1e293b; font-size: 20px;">Frequently Asked Questions</h4>
                                                <p class="mb-0 text-muted" style="font-size: 13px;">Everything you need to know about <?php echo htmlentities($bank_name); ?> <?php echo htmlentities($branch_name); ?> branch</p>
                                            </div>
                                        </div>

                                        <div class="accordion" id="ifscFaqAccordion">
                                            <?php foreach ($faq_items as $index => $item): ?>
                                                <div class="card mb-3 border-0 faq-card">
                                                    <div class="card-header bg-white p-3 border-0" id="heading<?php echo $index; ?>">
                                                        <h5 class="mb-0">
                                                            <button class="btn btn-link text-left w-100 p-0 d-flex justify-content-between align-items-center faq-btn" type="button" data-toggle="collapse" data-target="#collapse<?php echo $index; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $index; ?>">
                                                                <span><i class="fas fa-info-circle text-primary mr-2"></i> <?php echo htmlentities($item['q']); ?></span>
                                                                <i class="fas fa-chevron-down text-muted ml-2" style="font-size: 12px;"></i>
                                                            </button>
                                                        </h5>
                                                    </div>
                                                    <div id="collapse<?php echo $index; ?>" class="collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $index; ?>" data-parent="#ifscFaqAccordion">
                                                        <div class="card-body pt-1 pb-3 px-3 text-secondary" style="font-size: 14px; line-height: 1.6;">
                                                            <?php echo htmlentities($item['a']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                <?php
                                }
                            }
                            else
                            {
                ?>
                                <div class="alert mt-4" style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: var(--radius-md); padding: 15px 20px;"> 
                                    <i class="fas fa-exclamation-circle mr-2"></i> Sorry! No results found for IFSC Code <b class="ifsc-badge-code"><?php echo htmlentities($ifsccode); ?></b>. Please verify the code and try again.
                                </div>
                <?php
                            }
                        }
                    ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php require_once ('includes/footer.php'); ?> 
<script>
    $(document).ready(function() {
        if ($('#ifsccode_table').length) {
            $('#ifsccode_table').DataTable({
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 10
            });
        }
    });

    function copyToClipboard(text, btn) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function() {
                showCopyFeedback(btn);
            });
        } else {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showCopyFeedback(btn);
            } catch (err) {
                console.error('Fallback copy failed', err);
            }
            document.body.removeChild(textArea);
        }
    }

    function showCopyFeedback(btn) {
        var origHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-success"></i> Copied!';
        setTimeout(function() {
            btn.innerHTML = origHtml;
        }, 1800);
    }
</script>
</body>
</html>
