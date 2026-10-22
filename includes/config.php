<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
$token = session_id();
ini_set('max_execution_time', 300);
set_time_limit(300);
date_default_timezone_set('Asia/Kolkata');
/*-------Some Basic Details (Global Variables) ---------*/
if(isset($_SESSION['user_id']))
{
	$user_id = $_SESSION['user_id'];
}

$inst_name ="Online Law";
$inst_tag ="for every citizen ";
$inst_address1 ="2B Umanagar, Chapra ";
$inst_address2 ="Saran, Bihar, India 841301";
$inst_contact ="+91 9430 55 2117";
$inst_support ="8102930609";
$inst_contact2 ="+91 9431 42 6600";
$inst_email ="care@olaw.in";
$inst_logo ="/images/logo/logo.png";
$white_logo ="/images/logo/logo-white.png";
$inst_url ="https://olaw.in";
$inst_domain ="olaw.in";
$inst_type ="Organization";

$inst_facebook ='http://facebook.com/offerplant';
$inst_twitter ='http://twitter.com/myofferplant';
$inst_instagram ='http://instagram.com/offerplant';
$inst_youtube ='http://youtube.com/offerplanttechnonogiesprivatelimited';
$inst_linkedin ='http://linkedin.com/company/offerplant';
$inst_pinterest ='http://pinterest.com/offerplant';
$inst_app='https://play.google.com/store/apps/details?id=com.offerplant.myadvindia'; 
$sender_id ="OLAWIN"; // OFFAPP
$noreply_email ="noreply@olaw.in";
$auth_key_msg ="b0e99bea1fa7d15e27e1c5fd8e3c868"; // MSG CLUB myadvindia updated on 12 jan 2023 
$auth_key_sms ="180367At8cchpCRSTV59ed9c10"; // MSG 91
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
    $subpath = preg_replace('#/(admin|bank|includes)$#i', '', rtrim($script_dir, '/'));
}

$base_path = '/' . trim($subpath, '/') . (trim($subpath, '/') !== '' ? '/' : '');
$base_url = $protocol . "://" . $host . $base_path;


$docs_fee =200;
$google_map = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3592.2970631671963!2d84.74506377610148!3d25.7937490072049!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3992bb0071acc88d%3A0x667cb9b46cda2f4f!2sOnline%20Law!5e0!3m2!1sen!2sin!4v1743079268383!5m2!1sen!2sin" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
$google_map_url = 'https://maps.app.goo.gl/8TbKDXy7N8iQRWpr7';
/*---------Social Link ----------*/

$facebook ='http://facebook.com/offerplant';
$twitter ='http://twitter.com/myofferplant';
$linkedin ='http://linkedin.com/company/offerplant';
$youtube ='http://youtube.com/offerplanttechnonogiesprivatelimited';
$pinterest ='http://pinterest.com/offerplant';
$instagram ='http://instagram.com/offerplant';


$app_name ='OL 1.1';
$dev_company ="OfferPlant Technologies Private Limited";
$dev_by ="OfferPlant";
$dev_url ="http://offerplant.com";
$dev_email ="ask@offerplant.com";
$dev_contact ="+91 9431 42 6600";
$api_key = '';

$host_name ='localhost';
if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'localhost') {
    $db_user ='root';
    $db_password ='';
    $db_name ='u305984835_web'; // Ensure this matches your local database name
} else {
    $db_user ='u305984835_web';
    $db_password ='@User_2001';
    $db_name ='u305984835_web';
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
