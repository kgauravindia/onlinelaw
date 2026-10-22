<?php
/**
 * Online Law - Disclaimer
 * Published: 15 August 2026 | Last Updated: 01 October 2026
 */
require_once('includes/config.php');
require_once('includes/function.php');

$page_title = "Disclaimer : Online Law - For Every Citizen";
$meta_desc = "Disclaimer for Online Law (https://olaw.in), an initiative of OfferPlant Technologies Private Limited. Effective from 15 August 2026, Last Updated 01 October 2026.";
$meta_keywords = "advocate, agency, attorney, business law, consulting, corporate, justice, law firm, law office, lawyer, legal assistant, disclaimer";
$canonical_url = $base_url . "disclaimer";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    <meta name="author" content="OfferPlant">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">

    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
        .legal-hero-section {
            background-color: #030712 !important;
            background: radial-gradient(135% 100% at 50% 0%, #030712 0%, #0f172a 45%, #1e1b4b 100%) !important;
            padding: 55px 0 65px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .legal-glow-orb {
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(217, 119, 6, 0.22) 0%, transparent 70%);
            top: -100px;
            right: -60px;
            pointer-events: none;
            filter: blur(55px);
        }

        .legal-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(217, 119, 6, 0.18);
            border: 1px solid rgba(251, 191, 36, 0.35);
            color: #fde68a;
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        /* Hero Title Gradient */
        .hero-title-disclaimer {
            background: linear-gradient(135deg, #ffffff 0%, #fbbf24 45%, #f97316 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            filter: drop-shadow(0 2px 10px rgba(245, 158, 11, 0.25));
        }

        .legal-doc-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 45px;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.04);
            line-height: 1.85;
            font-size: 15px;
            color: #334155;
        }
        .legal-doc-card h4 {
            color: #0f172a;
            font-weight: 800;
            font-size: 17px;
            margin-top: 28px;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #f1f5f9;
        }
        .legal-doc-card p {
            margin-bottom: 14px;
        }

        .date-chip-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 18px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        @media (max-width: 767px) {
            .legal-doc-card { padding: 24px 18px; }
        }
    </style>
</head>
<body>

<?php require_once('includes/header.php'); ?>

<!-- Hero Section -->
<section class="legal-hero-section">
    <div class="legal-glow-orb"></div>
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10 col-12">
                <div class="legal-badge-pill mb-3">
                    <i class="fas fa-shield-halved mr-1.5" style="color: #fbbf24;"></i> PUBLIC NOTICE &bull; STATUTORY DISCLAIMER
                </div>
                <h1 class="hero-title-disclaimer mb-2 font-weight-bold" style="font-size: clamp(28px, 4.2vw, 42px); letter-spacing: -0.5px;">
                    Disclaimer
                </h1>
                <p class="mb-3" style="font-size: 15.5px; max-width: 720px; margin: 0 auto; color: #cbd5e1; line-height: 1.7;">
                    Important disclosures regarding our open data indexes, professional disclaimers, and terms of use for Online Law (<a href="https://olaw.in" target="_blank" style="color: #fbbf24; font-weight: 600; text-decoration: none;">https://olaw.in</a>).
                </p>
                <ul class="list-inline mb-0" style="color: #94a3b8; font-size: 13.5px;">
                    <li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: #cbd5e1; text-decoration: none;"><i class="fas fa-home mr-1"></i> Home</a></li>
                    <li class="list-inline-item mx-2 text-muted">/</li>
                    <li class="list-inline-item text-white font-weight-bold">Disclaimer</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-12">
                <div class="legal-doc-card">
                    
                    <!-- Both Dates Display -->
                    <div class="date-chip-box mb-4">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-warning text-dark px-3 py-1.5 mr-2 font-weight-bold" style="font-size: 12px; border-radius: 6px;">
                                <i class="fas fa-sync-alt mr-1"></i> Last Update on 01 Oct 2026
                            </span>
                            <span class="badge badge-light border text-muted px-3 py-1.5 font-weight-bold" style="font-size: 12px; border-radius: 6px;">
                                <i class="fas fa-calendar-alt mr-1"></i> Published on 15 August 2026
                            </span>
                        </div>
                        <div class="text-muted small">
                            Governing Entity: <strong>OfferPlant Technologies Pvt Ltd</strong>
                        </div>
                    </div>

                    <h4>WEBSITE DISCLAIMER</h4>
                    <p>The information provided by <b>Online Law </b> an initiative of <b> OfferPlant Technologies Private Limited</b> (“Company”, “we”, “our”, “us”) on <b>https://olaw.in</b> (the “Site”) is for general informational purposes only. All information on the Site is provided in good faith, however we make no representation or warranty of any kind, express or implied, regarding the accuracy, adequacy, validity, reliability, availability, or completeness of any information on the Site.</p>
                    <p>UNDER NO CIRCUMSTANCE SHALL WE HAVE ANY LIABILITY TO YOU FOR ANY LOSS OR DAMAGE OF ANY KIND INCURRED AS A RESULT OF THE USE OF THE SITE OR RELIANCE ON ANY INFORMATION PROVIDED ON THE SITE. YOUR USE OF THE SITE AND YOUR RELIANCE ON ANY INFORMATION ON THE SITE IS SOLELY AT YOUR OWN RISK.</p>

                    <h4>EXTERNAL LINKS DISCLAIMER</h4>
                    <p>The Site may contain (or you may be sent through the Site) links to other websites or content belonging to or originating from third parties or links to websites and features. Such external links are not investigated, monitored, or checked for accuracy, adequacy, validity, reliability, availability or completeness by us.</p>
                    <p>WE DO NOT WARRANT, ENDORSE, GUARANTEE, OR ASSUME RESPONSIBILITY FOR THE ACCURACY OR RELIABILITY OF ANY INFORMATION OFFERED BY THIRD-PARTY WEBSITES LINKED THROUGH THE SITE OR ANY WEBSITE OR FEATURE LINKED IN ANY BANNER OR OTHER ADVERTISING. WE WILL NOT BE A PARTY TO OR IN ANY WAY BE RESPONSIBLE FOR MONITORING ANY TRANSACTION BETWEEN YOU AND THIRD-PARTY PROVIDERS OF PRODUCTS OR SERVICES.</p>

                    <h4>PROFESSIONAL DISCLAIMER</h4>
                    <p>The Site can not and does not contain legal advice. The information is provided for general informational and educational purposes only and is not a substitute for professional legal advice. Accordingly, before taking any actions based upon such information, we encourage you to consult with the appropriate professionals. We do not provide any kind of legal advice.</p>
                    <p>Content published on https://olaw.in is intended to be used and must be used for informational purposes only. It is very important to do your own analysis before making any decision based on your own personal circumstances. You should take independent legal advice from a professional or independently research and verify any information that you find on our Website and wish to rely upon.</p>
                    <p>THE USE OR RELIANCE OF ANY INFORMATION CONTAINED ON THIS SITE IS SOLELY AT YOUR OWN RISK.</p>

                    <h4>AFFILIATES DISCLAIMER</h4>
                    <p>The Site may contain links to affiliate websites, and we may receive an affiliate commission for any purchases or actions made by you on the affiliate websites using such links.</p>

                    <h4>TESTIMONIALS DISCLAIMER</h4>
                    <p>The Site may contain testimonials by users of our products and/or services. These testimonials reflect the real-life experiences and opinions of such users. However, the experiences are personal to those particular users, and may not necessarily be representative of all users of our products and/or services. We do not claim, and you should not assume that all users will have the same experiences.</p>
                    <p>YOUR INDIVIDUAL RESULTS MAY VARY.</p>
                    <p>The testimonials on the Site are submitted in various forms such as text, audio and/or video, and are reviewed by us before being posted. They appear on the Site verbatim as given by the users, except for the correction of grammar or typing errors. Some testimonials may have been shortened for the sake of brevity, where the full testimonial contained extraneous information not relevant to the general public.</p>
                    <p>The views and opinions contained in the testimonials belong solely to the individual user and do not reflect our views and opinions.</p>

                    <h4>ERRORS AND OMISSIONS DISCLAIMER</h4>
                    <p>While we have made every attempt to ensure that the information contained in this site has been obtained from reliable sources, Online Law an initiative of OfferPlant Technologies Private Limited is not responsible for any errors or omissions or for the results obtained from the use of this information. All information in this site is provided “as is”, with no guarantee of completeness, accuracy, timeliness or of the results obtained from the use of this information, and without warranty of any kind, express or implied, including, but not limited to warranties of performance, merchantability, and fitness for a particular purpose.</p>
                    <p>In no event will Online Law an initiative of OfferPlant Technologies Private Limited, its related partnerships or corporations, or the partners, agents or employees thereof be liable to you or anyone else for any decision made or action taken in reliance on the information in this Site or for any consequential, special or similar damages, even if advised of the possibility of such damages.</p>

                    <h4>GUEST CONTRIBUTORS DISCLAIMER</h4>
                    <p>This Site may include content from guest contributors and any views or opinions expressed in such posts are personal and do not represent those of Online Law an initiative of OfferPlant Technologies Private Limited or any of its staff or affiliates unless explicitly stated.</p>

                    <h4>LOGOS AND TRADEMARKS DISCLAIMER</h4>
                    <p>All logos and trademarks of third parties referenced on https://olaw.in are the trademarks and logos of their respective owners. Any inclusion of such trademarks or logos does not imply or constitute any approval, endorsement or sponsorship of Online Law an initiative of OfferPlant Technologies Private Limited by such owners.</p>

                    <h4>CONTACT US</h4>
                    <p>Should you have any feedback, comments, requests for technical support or other inquiries, please contact us by email: <b>care@olaw.in</b>.</p>

                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>
</body>
</html>
