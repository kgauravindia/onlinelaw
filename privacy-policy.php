<?php
/**
 * Online Law - Privacy Policy
 * Published: 15 August 2026 | Last Updated: 01 October 2026
 */
require_once('includes/config.php');
require_once('includes/function.php');

$page_title = "Privacy Policy : Online Law - For Every Citizen";
$meta_desc = "Privacy Policy of Online Law (https://olaw.in), an initiative of OfferPlant Technologies Private Limited. Effective from 15 August 2026, Last Updated 01 October 2026.";
$meta_keywords = "advocate, agency, attorney, business law, consulting, corporate, justice, law firm, law office, lawyer, legal assistant, privacy policy";
$canonical_url = $base_url . "privacy-policy";
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
            background: radial-gradient(circle, rgba(16, 185, 129, 0.22) 0%, transparent 70%);
            top: -100px;
            right: -60px;
            pointer-events: none;
            filter: blur(55px);
        }

        .legal-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.18);
            border: 1px solid rgba(52, 211, 153, 0.35);
            color: #a7f3d0;
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        /* Hero Title Gradient */
        .hero-title-privacy {
            background: linear-gradient(135deg, #ffffff 0%, #34d399 45%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            filter: drop-shadow(0 2px 10px rgba(52, 211, 153, 0.25));
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
                    <i class="fas fa-shield-halved mr-1.5" style="color: #34d399;"></i> PRIVACY &bull; DATA PROTECTION
                </div>
                <h1 class="hero-title-privacy mb-2 font-weight-bold" style="font-size: clamp(28px, 4.2vw, 42px); letter-spacing: -0.5px;">
                    Privacy Policy
                </h1>
                <p class="mb-3" style="font-size: 15.5px; max-width: 720px; margin: 0 auto; color: #cbd5e1; line-height: 1.7;">
                    Learn how Online Law (<a href="https://olaw.in" target="_blank" style="color: #34d399; font-weight: 600; text-decoration: none;">https://olaw.in</a>) collects, safeguards, and handles your personal data and directory usage.
                </p>
                <ul class="list-inline mb-0" style="color: #94a3b8; font-size: 13.5px;">
                    <li class="list-inline-item"><a href="<?php echo $base_url; ?>index" style="color: #cbd5e1; text-decoration: none;"><i class="fas fa-home mr-1"></i> Home</a></li>
                    <li class="list-inline-item mx-2 text-muted">/</li>
                    <li class="list-inline-item text-white font-weight-bold">Privacy Policy</li>
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
                            <span class="badge badge-success px-3 py-1.5 mr-2 font-weight-bold" style="font-size: 12px; border-radius: 6px;">
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

                    <h4>1. Introduction</h4>
                    <p>Welcome to <b>Online Law</b> an initiative of <b>OfferPlant Technologies Private Limited</b>.</p>
                    <p><b>Online Law</b> an initiative of <b>OfferPlant Technologies Private Limited</b> (“us”, “we”, or “our”) operates <b>https://olaw.in</b> (hereinafter referred to as <b>“Service”</b>).</p>
                    <p>Our Privacy Policy governs your visit to <b>https://olaw.in</b>, and explains how we collect, safeguard and disclose information that results from your use of our Service.</p>
                    <p>We use your data to provide and improve Service. By using Service, you agree to the collection and use of information in accordance with this policy. Unless otherwise defined in this Privacy Policy, the terms used in this Privacy Policy have the same meanings as in our Terms and Conditions.</p>
                    <p>Our Terms and Conditions (<b>“Terms”</b>) govern all use of our Service and together with the Privacy Policy constitutes your agreement with us (<b>“agreement”</b>).</p>

                    <h4>2. Definitions</h4>
                    <p><b>SERVICE</b> means the https://olaw.in website operated by Online Law an initiative of OfferPlant Technologies Private Limited.</p>
                    <p><b>PERSONAL DATA</b> means data about a living individual who can be identified from those data (or from those and other information either in our possession or likely to come into our possession).</p>
                    <p><b>USAGE DATA</b> is data collected automatically either generated by the use of Service or from Service infrastructure itself (for example, the duration of a page visit).</p>
                    <p><b>COOKIES</b> are small files stored on your device (computer or mobile device).</p>
                    <p><b>DATA CONTROLLER</b> means a natural or legal person who (either alone or jointly or in common with other persons) determines the purposes for which and the manner in which any personal data are, or are to be, processed. For the purpose of this Privacy Policy, we are a Data Controller of your data.</p>
                    <p><b>DATA PROCESSORS (OR SERVICE PROVIDERS)</b> means any natural or legal person who processes the data on behalf of the Data Controller. We may use the services of various Service Providers in order to process your data more effectively.</p>
                    <p><b>DATA SUBJECT</b> is any living individual who is the subject of Personal Data.</p>
                    <p><b>THE USER</b> is the individual using our Service. The User corresponds to the Data Subject, who is the subject of Personal Data.</p>

                    <h4>3. Information Collection and Use</h4>
                    <p>We collect several different types of information for various purposes to provide and improve our Service to you.</p>

                    <h4>4. Types of Data Collected</h4>
                    <p><b>Personal Data</b></p>
                    <p>While using our Service, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you (<b>“Personal Data”</b>). Personally identifiable information may include, but is not limited to:</p>
                    <p>0.1. Email address</p>
                    <p>0.2. First name and last name</p>
                    <p>0.3. Phone number</p>
                    <p>0.4. Address, Country, State, Province, ZIP/Postal code, City</p>
                    <p>0.5. Cookies and Usage Data</p>
                    <p>We may use your Personal Data to contact you with newsletters, marketing or promotional materials and other information that may be of interest to you. You may opt out of receiving any, or all, of these communications from us by following the unsubscribe link.</p>

                    <p><b>Usage Data</b></p>
                    <p>We may also collect information that your browser sends whenever you visit our Service or when you access Service by or through any device (<b>“Usage Data”</b>).</p>
                    <p>This Usage Data may include information such as your computer’s Internet Protocol address (e.g. IP address), browser type, browser version, the pages of our Service that you visit, the time and date of your visit, the time spent on those pages, unique device identifiers and other diagnostic data.</p>
                    <p>When you access Service with a device, this Usage Data may include information such as the type of device you use, your device unique ID, the IP address of your device, your device operating system, the type of Internet browser you use, unique device identifiers and other diagnostic data.</p>

                    <p><b>Location Data</b></p>
                    <p>We may use and store information about your location if you give us permission to do so (<b>“Location Data”</b>). We use this data to provide features of our Service, to improve and customize our Service.</p>
                    <p>You can enable or disable location services when you use our Service at any time by way of your device settings.</p>

                    <p><b>Tracking Cookies Data</b></p>
                    <p>We use cookies and similar tracking technologies to track the activity on our Service and we hold certain information.</p>
                    <p>Cookies are files with a small amount of data which may include an anonymous unique identifier. Cookies are sent to your browser from a website and stored on your device. Other tracking technologies are also used such as beacons, tags and scripts to collect and track information and to improve and analyze our Service.</p>
                    <p>You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our Service.</p>
                    <p>Examples of Cookies we use:</p>
                    <p>0.1. <b>Session Cookies:</b> We use Session Cookies to operate our Service.</p>
                    <p>0.2. <b>Preference Cookies:</b> We use Preference Cookies to remember your preferences and various settings.</p>
                    <p>0.3. <b>Security Cookies:</b> We use Security Cookies for security purposes.</p>
                    <p>0.4. <b>Advertising Cookies:</b> Advertising Cookies are used to serve you with advertisements that may be relevant to you and your interests.</p>

                    <p><b>Other Data</b></p>
                    <p>While using our Service, we may also collect voluntary contact information submitted for inquiries, record corrections, or partnership requests.</p>

                    <h4>5. Use of Data</h4>
                    <p>Online Law an initiative of OfferPlant Technologies Private Limited uses the collected data for various purposes:</p>
                    <p>0.1. to provide and maintain our Service;</p>
                    <p>0.2. to notify you about changes to our Service;</p>
                    <p>0.3. to allow you to participate in interactive features of our Service when you choose to do so;</p>
                    <p>0.4. to provide customer support;</p>
                    <p>0.5. to gather analysis or valuable information so that we can improve our Service;</p>
                    <p>0.6. to monitor the usage of our Service;</p>
                    <p>0.7. to detect, prevent and address technical issues;</p>
                    <p>0.8. to fulfil any other purpose for which you provide it;</p>
                    <p>0.9. to carry out our obligations and enforce our rights arising from any contracts entered into between you and us, including for billing and collection;</p>
                    <p>0.10. to provide you with notices about your account and/or subscription, including expiration and renewal notices, email-instructions, etc.;</p>
                    <p>0.11. to provide you with news, special offers and general information about other goods, services and events which we offer that are similar to those that you have already purchased or enquired about unless you have opted not to receive such information;</p>
                    <p>0.12. in any other way we may describe when you provide the information;</p>
                    <p>0.13. for any other purpose with your consent.</p>

                    <h4>6. Retention of Data</h4>
                    <p>We will retain your Personal Data only for as long as is necessary for the purposes set out in this Privacy Policy. We will retain and use your Personal Data to the extent necessary to comply with our legal obligations (for example, if we are required to retain your data to comply with applicable laws), resolve disputes, and enforce our legal agreements and policies.</p>
                    <p>We will also retain Usage Data for internal analysis purposes. Usage Data is generally retained for a shorter period, except when this data is used to strengthen the security or to improve the functionality of our Service, or we are legally obligated to retain this data for longer time periods.</p>

                    <h4>7. Transfer of Data</h4>
                    <p>Your information, including Personal Data, may be transferred to – and maintained on – computers located outside of your state, province, country or other governmental jurisdiction where the data protection laws may differ from those of your jurisdiction.</p>
                    <p>If you are located outside India and choose to provide information to us, please note that we transfer the data, including Personal Data, to India and process it there.</p>
                    <p>Your consent to this Privacy Policy followed by your submission of such information represents your agreement to that transfer.</p>
                    <p>Online Law an initiative of OfferPlant Technologies Private Limited will take all the steps reasonably necessary to ensure that your data is treated securely and in accordance with this Privacy Policy.</p>

                    <h4>8. Disclosure of Data</h4>
                    <p>We may disclose personal information that we collect, or you provide:</p>
                    <p>0.1. <b>Disclosure for Law Enforcement:</b> Under certain circumstances, we may be required to disclose your Personal Data if required to do so by law or in response to valid requests by public authorities.</p>
                    <p>0.2. <b>Business Transaction:</b> If we or our subsidiaries are involved in a merger, acquisition or asset sale, your Personal Data may be transferred.</p>
                    <p>0.3. <b>Other cases:</b> To our subsidiaries and affiliates; to contractors and service providers who support our business; to fulfill the purpose for which you provide it; or with your consent.</p>

                    <h4>9. Security of Data</h4>
                    <p>The security of your data is important to us but remember that no method of transmission over the Internet or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Data, we cannot guarantee its absolute security.</p>

                    <h4>10. Your Data Protection Rights Under GDPR</h4>
                    <p>If you are a resident of the European Union (EU) and European Economic Area (EEA), you have certain data protection rights, covered by GDPR.</p>
                    <p>We aim to take reasonable steps to allow you to correct, amend, delete, or limit the use of your Personal Data. If you wish to be informed what Personal Data we hold about you and if you want it to be removed from our systems, please email us at <b>care@olaw.in</b>.</p>

                    <h4>11. Your Data Protection Rights under CalOPPA</h4>
                    <p>CalOPPA is the first state law in the United States to require commercial websites and online services to post a privacy policy.</p>
                    <p>According to CalOPPA we agree to the following:</p>
                    <p>0.1. Users can visit our site anonymously.</p>
                    <p>0.2. Our Privacy Policy link includes the word “Privacy”, and can easily be found on the home page of our website.</p>
                    <p>0.3. Users will be notified of any privacy policy changes on our Privacy Policy Page.</p>
                    <p>0.4. Users are able to change their personal information by emailing us at <b>care@olaw.in</b>.</p>

                    <h4>12. Your Data Protection Rights under CCPA</h4>
                    <p>If you are a California resident, you are entitled to learn what data we collect about you, ask to delete your data and not to sell (share) it. We do not sell personal information for monetary consideration. To exercise your rights, please email <b>care@olaw.in</b>.</p>

                    <h4>13. Service Providers</h4>
                    <p>We may employ third party companies and individuals to facilitate our Service (<b>“Service Providers”</b>), provide Service on our behalf, perform Service-related services or assist us in analysing how our Service is used.</p>

                    <h4>14. Analytics</h4>
                    <p>We may use third-party Service Providers to monitor and analyze the use of our Service.</p>

                    <h4>15. CI/CD Tools</h4>
                    <p>We may use third-party Service Providers to automate the development process of our Service.</p>

                    <h4>16. Advertising</h4>
                    <p>We may use third-party Service Providers to show advertisements to you to help support and maintain our Service.</p>

                    <h4>17. Behavioral Remarketing</h4>
                    <p>We may use remarketing services to advertise on third party websites to you after you visited our Service.</p>

                    <h4>18. Payments</h4>
                    <p>We may provide paid products and/or services within Service. In that case, we use third-party services for payment processing.</p>

                    <h4>19. Links to Other Sites</h4>
                    <p>Our Service may contain links to other sites that are not operated by us. If you click a third party link, you will be directed to that third party’s site. We strongly advise you to review the Privacy Policy of every site you visit.</p>

                    <h4>20. Children’s Privacy</h4>
                    <p>Our Services are not intended for use by children under the age of 18 (<b>“Child”</b> or <b>“Children”</b>).</p>
                    <p>We do not knowingly collect personally identifiable information from Children under 18. If you become aware that a Child has provided us with Personal Data, please contact us at care@olaw.in.</p>

                    <h4>21. Changes to This Privacy Policy</h4>
                    <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page with an updated effective date.</p>

                    <h4>22. Contact Us</h4>
                    <p>If you have any questions about this Privacy Policy, please contact us by email: <b>care@olaw.in</b>.</p>

                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once('includes/footer.php'); ?>
</body>
</html>
