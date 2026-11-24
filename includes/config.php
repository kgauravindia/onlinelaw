<?php
// Global Security & File Upload Interceptor (Blocks .php, .bat, .exe, scripts & executables across whole site)
require_once(__DIR__ . '/security.php');

// Load environment configuration (.env) if present (keeps secrets safe from public repository)
if (file_exists(__DIR__ . '/../.env')) {
    $env_file = @file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($env_file) {
        foreach ($env_file as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($env_k, $env_v) = explode('=', $line, 2);
                $env_k = trim($env_k);
                $env_v = trim($env_v, " \t\n\r\0\x0B\"'");
                if (!isset($_ENV[$env_k])) $_ENV[$env_k] = $env_v;
                if (getenv($env_k) === false) putenv("$env_k=$env_v");
            }
        }
    }
}

// Automatic Output Buffering with GZIP Compression (Compresses HTML responses by 75-85%, speeding up load times)
if (!ob_get_level() && !headers_sent()) {
    if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
        ob_start('ob_gzhandler');
    } else {
        ob_start();
    }
}

// Determine if request is via HTTPS
$is_https = (
    (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1')) ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
);

// Enforce Session Security Settings before session starts
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_samesite', 'Lax');
if ($is_https) {
    ini_set('session.cookie_secure', '1');
}

// Runtime Security Headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    if ($is_https) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// Session Initialization (Skip on stateless REST API calls to avoid file locking and memory consumption)
$is_api_request = defined('REST_API') || (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], 'api.php') !== false);
if (!$is_api_request && session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
$token = session_id();
ini_set('max_execution_time', 120);
date_default_timezone_set('Asia/Kolkata');
/*-------Some Basic Details (Global Variables) ---------*/
if(isset($_SESSION['user_id']))
{
	$user_id = $_SESSION['user_id'];
}

$inst_name      = getenv('INST_NAME') ?: "Online Law";
$inst_tag       = getenv('INST_TAG') ?: "for every citizen ";
$inst_address1  = getenv('INST_ADDRESS1') ?: "";
$inst_address2  = getenv('INST_ADDRESS2') ?: "";
$inst_contact   = getenv('INST_CONTACT') ?: "";
$inst_support   = getenv('INST_SUPPORT') ?: "";
$inst_contact2  = getenv('INST_CONTACT2') ?: "";
$inst_email     = getenv('INST_EMAIL') ?: "";
$inst_logo      = getenv('INST_LOGO') ?: "/images/logo/logo.png";
$white_logo     = getenv('INST_WHITE_LOGO') ?: "/images/logo/logo-white.png";
$inst_url       = getenv('INST_URL') ?: "https://olaw.in";
$inst_domain    = getenv('INST_DOMAIN') ?: "olaw.in";
$inst_type      = "Organization";

$inst_facebook  = getenv('INST_FACEBOOK') ?: "";
$inst_twitter   = getenv('INST_TWITTER') ?: "";
$inst_instagram = getenv('INST_INSTAGRAM') ?: "";
$inst_youtube   = getenv('INST_YOUTUBE') ?: "";
$inst_linkedin  = getenv('INST_LINKEDIN') ?: "";
$inst_pinterest = getenv('INST_PINTEREST') ?: "";
$inst_app       = getenv('INST_APP') ?: "";
$sender_id      = getenv('SMS_SENDER_ID') ?: "";
$noreply_email  = getenv('NOREPLY_EMAIL') ?: "";
$auth_key_msg   = getenv('SMS_AUTH_KEY') ?: '';
$auth_key_sms   = getenv('SMS_AUTH_KEY') ?: '';

// Hostinger SMTP Email Configuration (Environment-driven, no secrets in repository)
if (!defined('SMTP_HOST')) define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.hostinger.com');
if (!defined('SMTP_PORT')) define('SMTP_PORT', getenv('SMTP_PORT') ? (int)getenv('SMTP_PORT') : 587);
if (!defined('SMTP_USER')) define('SMTP_USER', getenv('SMTP_USER') ?: '');
if (!defined('SMTP_PASS')) define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
if (!defined('SMTP_SECURE')) define('SMTP_SECURE', getenv('SMTP_SECURE') ?: 'tls');
if (!defined('SYSTEM_FROM_EMAIL')) define('SYSTEM_FROM_EMAIL', getenv('SYSTEM_FROM_EMAIL') ?: '');
if (!defined('SYSTEM_FROM_NAME')) define('SYSTEM_FROM_NAME', getenv('SYSTEM_FROM_NAME') ?: 'Online Law Security');
// Determine if the request is over HTTPS (support Cloudflare, reverse proxies, and direct SSL)
$protocol = "http";
if (
    (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1')) ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
) {
    $protocol = "https";
} elseif (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost' && $_SERVER['HTTP_HOST'] !== '127.0.0.1' && !str_starts_with($_SERVER['HTTP_HOST'], 'localhost:')) {
    // Default to https for online/production hostnames
    $protocol = "https";
}

// Host detection (fallback to olaw.in if CLI)
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'olaw.in';

// Calculate base path from project root directory relative to DOCUMENT_ROOT
$doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])) : '';
$app_root = str_replace('\\', '/', realpath(dirname(__DIR__)));

$subpath = '';
if (!empty($doc_root) && !empty($app_root) && strpos($app_root, $doc_root) === 0) {
    $subpath = substr($app_root, strlen($doc_root));
} else {
    // Fallback based on script name if document root realpath mismatch
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $subpath = preg_replace('#/(oppi|admin|bank|includes)$#i', '', rtrim($script_dir, '/'));
}

$base_path = '/' . trim($subpath, '/') . (trim($subpath, '/') !== '' ? '/' : '');
$base_url = $protocol . "://" . $host . $base_path;


$docs_fee = 200;
$google_map = getenv('GOOGLE_MAP_EMBED') ?: '';
$google_map_url = getenv('GOOGLE_MAP_URL') ?: '';
/*---------Social Link ----------*/

$facebook  = $inst_facebook;
$twitter   = $inst_twitter;
$linkedin  = $inst_linkedin;
$youtube   = $inst_youtube;
$pinterest = $inst_pinterest;
$instagram = $inst_instagram;

$app_name    = 'OL 1.1';
$dev_company = getenv('DEV_COMPANY') ?: "";
$dev_by      = getenv('DEV_BY') ?: "";
$dev_url     = getenv('DEV_URL') ?: "";
$dev_email   = getenv('DEV_EMAIL') ?: "";
$dev_contact = getenv('DEV_CONTACT') ?: "";
$api_key     = getenv('API_KEY') ?: '';

$host_name = getenv('DB_HOST') ?: 'localhost';
$is_local = false;
if (isset($_SERVER['HTTP_HOST'])) {
    $h = strtolower($_SERVER['HTTP_HOST']);
    if ($h === 'localhost' || str_starts_with($h, 'localhost:') || $h === '127.0.0.1' || str_starts_with($h, '127.0.0.1:') || str_ends_with($h, '.test') || str_ends_with($h, '.local')) {
        $is_local = true;
    }
}
if ($is_local) {
    $db_user = getenv('DB_USER') ?: 'root';
    $db_password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
    $db_name = getenv('DB_NAME') ?: 'u305984835_onlinelaw';
} else {
    $db_user = getenv('DB_USER') ?: 'u305984835_onlinelaw';
    $db_password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
    $db_name = getenv('DB_NAME') ?: 'u305984835_onlinelaw';
}

$gender_list =array('','MALE','FEMALE','OTHER');
$duration_list =array('',1,2,3,4,6,9,12,15,18,24);

$status_list =array('','ACTIVE','BLOCK');
$option_list =array('','A','B','C','D','E');
$unit_list =array('Days','Months','Hours','Years');
	
$month = array('April','May','June','July','August','September','October','November','December','January','February','March');
/* ---- MyAdv List ------- */ 
$blood_list =array('A+'=>'A+', 'B+'=>'B+','AB+'=>'AB+','O+'=>'O+','A-'=>'A-', 'B-'=>'B-','AB-'=>'AB-','O-'=>'O-');
$case_list =array('1'=>'civil case', '2'=>'criminal case');
$category_list =array('GEN'=>'General', 'OBC'=>'OBC','SC'=>'SC', 'ST'=>'ST');
$court_list =array(''=>'Select Court','CC'=>'Civil Court', 'HC'=>'High Court','SC'=>'Supreme Court of India', 'EC'=>'Executive Court','OC'=>'Other Court');
$gender_list =array(''=>'Gender','M'=>'Male', 'F'=>'Female','O'=>'Other');
$marital_list =array('U'=>'Unmarried', 'M'=>'Married','D'=>'Divorced', 'W'=>'Widow');
$edu_list =array(''=>'Select Education','1'=>'Illiterate', '2'=>'Literate','10'=>'10th','12'=>'12th', '15'=>'Graduate','17'=>'Post Graduate', '18'=>'Doctorate', '19'=> 'Professional');
$adv_type_list = array('PENDING', 'ACTIVE', 'VERIFIED','PREMIUM' ,'BLOCK','DIED');
$parea_list = array('Admirality Suit','Anticipatory Bail','Arbitration Petition','Bail Petition','Banking','Certificate Appeal','Civil Appeal','Civil Miscellaneous','Civil Reference','Civil Review','Civil Revision','Civil Writ','Commercial Appeal','Company Appeal','Company Petition','Compensation Appeal','Complaint','Consumer Case','Contempt Petition','Criminal Appeal','Criminal Case','Criminal Miscellaneous','Criminal Reference','Criminal Revision','Criminal Trail','Criminal Writ','Curative Petition','Death Reference Case','Debt Recovery','Divorce','Election Petition','Enquiry','Eviction Suit','Excise Case','Execution','Executive Appeal','Executive Case','Final Decree','First Appeal','Govt. Appeal','Guardianship','Juvenile Appeal','Juvenile Enquiry','Juvenile Trial','Land Acquisition','Land Dispute','Letters Patent Appeal','Maintenance','Matrimonial Ref','Matrimonial Suit','Miscellaneous Appeal','Miscellaneous Application','Miscellaneous Case','Money Suit','Motor Vehicle Claim','Original Suit','Other Cases','Other Civil Cases','Other Criminal Cases','Partition Suit','PASCO','Patent Case','Probate','Recovery Case','Registration','Request Case','Restitution','Review Petition','RTI','SC And ST','Second Appeal','Session Trial','Special Leave Petition','Special Reference Case','Stamp Case','Succession','Suo Moto Petition','Tax Cases','Title Appeal','Title Suit','Transfer Petition','Transferred Case','Under Trial');


$admin_role =array('add_center' , 'add_course' , 'add_paper' , 'add_student' , 'pay_fee');


$client_role = array('config','conn','function','client_index','add_student','txn_history');
/*-------End of Basic Details ---------*/

?>
