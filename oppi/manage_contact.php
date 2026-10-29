<?php
require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../includes/function.php');

// Admin Auth Check
check_admin_auth(true);

// Auto-create contact table if not available
mysqli_query($con, "CREATE TABLE IF NOT EXISTS `contact` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(20) NOT NULL,
  `area` varchar(100) DEFAULT 'General Inquiry',
  `message` text NOT NULL,
  `status` varchar(50) DEFAULT 'NEW',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT 0,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Auto-upgrade contact table schema with email reply tracking columns
$col_check = mysqli_query($con, "SHOW COLUMNS FROM `contact` LIKE 'reply_message'");
if ($col_check && mysqli_num_rows($col_check) == 0) {
    mysqli_query($con, "ALTER TABLE `contact` 
        ADD COLUMN `reply_message` text NULL AFTER `message`,
        ADD COLUMN `reply_subject` varchar(255) NULL AFTER `reply_message`,
        ADD COLUMN `replied_at` datetime NULL AFTER `reply_subject`,
        ADD COLUMN `replied_by` varchar(100) NULL AFTER `replied_at`");
}

// Helper to build query URL with preserved parameters
function get_contact_page_url($target_page, $custom_limit = null) {
    $params = $_GET;
    $params['p'] = max(1, intval($target_page));
    if ($custom_limit !== null) {
        $params['limit'] = max(5, intval($custom_limit));
        $params['p'] = 1; // reset to first page when changing page size
    }
    return 'manage_contact.php?' . http_build_query($params);
}

// Helper to build redirect URL
function get_contact_redirect_url() {
    $params = [];
    if (isset($_REQUEST['tab']) && $_REQUEST['tab'] !== '' && $_REQUEST['tab'] !== 'all') {
        $params['tab'] = $_REQUEST['tab'];
    }
    if (isset($_REQUEST['q']) && $_REQUEST['q'] !== '') {
        $params['q'] = $_REQUEST['q'];
    }
    if (isset($_REQUEST['limit']) && intval($_REQUEST['limit']) > 0 && intval($_REQUEST['limit']) != 20) {
        $params['limit'] = intval($_REQUEST['limit']);
    }
    if (isset($_REQUEST['p']) && intval($_REQUEST['p']) > 1) {
        $params['p'] = intval($_REQUEST['p']);
    }
    return 'manage_contact.php' . (!empty($params) ? '?' . http_build_query($params) : '');
}

// Handle Status Update
if (isset($_GET['update_status']) && isset($_GET['id'])) {
    $c_id = intval($_GET['id']);
    $new_status = mysqli_real_escape_string($con, trim($_GET['update_status']));
    mysqli_query($con, "UPDATE contact SET status = '$new_status' WHERE id = $c_id");
    
    $_SESSION['flash_msg'] = "Contact #$c_id status updated to $new_status.";
    $_SESSION['flash_type'] = "success";
    
    header("Location: " . get_contact_redirect_url());
    exit();
}

// Handle Single Deletion
if (isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    delete_data('contact', $del_id);
    
    $_SESSION['flash_msg'] = "Contact #$del_id deleted successfully.";
    $_SESSION['flash_type'] = "success";
    
    header("Location: " . get_contact_redirect_url());
    exit();
}

// Handle Bulk Actions (Multi-delete, Multi-resolve, Multi-mark new)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    $bulk_action = trim($_POST['bulk_action']);
    $selected_ids = isset($_POST['selected_ids']) && is_array($_POST['selected_ids']) 
        ? array_filter(array_map('intval', $_POST['selected_ids']), function($v) { return $v > 0; }) 
        : [];
    
    if (!empty($selected_ids)) {
        $id_list = implode(',', $selected_ids);
        $count = count($selected_ids);
        
        if ($bulk_action === 'delete') {
            mysqli_query($con, "DELETE FROM contact WHERE id IN ($id_list)");
            $_SESSION['flash_msg'] = "Successfully deleted $count contact inquiry record(s).";
            $_SESSION['flash_type'] = "success";
        } elseif ($bulk_action === 'resolve') {
            mysqli_query($con, "UPDATE contact SET status = 'RESOLVED' WHERE id IN ($id_list)");
            $_SESSION['flash_msg'] = "Marked $count contact record(s) as RESOLVED.";
            $_SESSION['flash_type'] = "success";
        } elseif ($bulk_action === 'new') {
            mysqli_query($con, "UPDATE contact SET status = 'NEW' WHERE id IN ($id_list)");
            $_SESSION['flash_msg'] = "Marked $count contact record(s) as NEW.";
            $_SESSION['flash_type'] = "info";
        }
    } else {
        $_SESSION['flash_msg'] = "No contact records selected for action.";
        $_SESSION['flash_type'] = "warning";
    }
    
    // Redirect preserving filter / pagination state
    if (isset($_POST['redirect_tab'])) $_REQUEST['tab'] = $_POST['redirect_tab'];
    if (isset($_POST['redirect_q'])) $_REQUEST['q'] = $_POST['redirect_q'];
    if (isset($_POST['redirect_limit'])) $_REQUEST['limit'] = $_POST['redirect_limit'];
    if (isset($_POST['redirect_p'])) $_REQUEST['p'] = $_POST['redirect_p'];
    
    header("Location: " . get_contact_redirect_url());
    exit();
}

// Handle Send Email Reply to Contact Inquiry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_contact_email_reply') {
    $contact_id = intval($_POST['contact_id'] ?? 0);
    $recipient_email = trim($_POST['recipient_email'] ?? '');
    $email_subject = trim($_POST['email_subject'] ?? '');
    $reply_message = trim($_POST['reply_message'] ?? '');
    $auto_resolve = isset($_POST['auto_resolve']) && $_POST['auto_resolve'] == '1';
    $send_bcc = isset($_POST['send_bcc']) && $_POST['send_bcc'] == '1';

    if ($contact_id > 0 && !empty($recipient_email) && filter_var($recipient_email, FILTER_VALIDATE_EMAIL) && !empty($reply_message)) {
        // Fetch original contact record
        $inq_res = direct_sql("SELECT * FROM contact WHERE id = $contact_id LIMIT 1");
        if (!empty($inq_res['count']) && $inq_res['count'] > 0) {
            $inq = $inq_res['data'][0];
            $sender_name = $inq['name'] ?? 'User';
            $orig_msg = $inq['message'] ?? '';
            $orig_area = $inq['area'] ?? 'Inquiry';

            if (empty($email_subject)) {
                $email_subject = "Re: Your Inquiry to Online Law (#$contact_id)";
            }

            // High-deliverability branded HTML Email Template
            $from_mail = !empty($noreply_email) ? $noreply_email : 'care@olaw.in';
            $support_mail = !empty($inst_email) ? $inst_email : 'care@olaw.in';
            $company_name = !empty($inst_name) ? $inst_name : 'Online Law';

            $html_content = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #1e293b; background-color: #f1f5f9; margin: 0; padding: 24px 12px; }
                    .email-wrapper { max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); }
                    .email-header { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #1d4ed8 100%); color: #ffffff; padding: 32px 28px; text-align: left; }
                    .email-header h2 { margin: 0 0 6px 0; font-size: 22px; font-weight: 700; letter-spacing: -0.02em; color: #ffffff; }
                    .email-header p { margin: 0; color: #93c5fd; font-size: 13.5px; }
                    .email-body { padding: 30px 28px; color: #334155; font-size: 15px; }
                    .salutation { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 18px; }
                    .reply-text { font-size: 15px; color: #1e293b; white-space: pre-line; line-height: 1.7; margin-bottom: 24px; padding: 18px 20px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; border-left: 4px solid #2563eb; }
                    .quote-header { font-size: 12px; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.5px; margin-bottom: 8px; }
                    .quote-box { background: #ffffff; border-left: 3px solid #94a3b8; padding: 14px 18px; color: #64748b; font-size: 13.5px; margin-bottom: 24px; border-radius: 0 8px 8px 0; border-top: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
                    .email-footer { background: #f8fafc; padding: 22px 28px; border-top: 1px solid #e2e8f0; font-size: 12.5px; color: #64748b; line-height: 1.7; }
                    .email-footer a { color: #2563eb; text-decoration: none; font-weight: 600; }
                </style>
            </head>
            <body>
                <div class='email-wrapper'>
                    <div class='email-header'>
                        <h2>{$company_name} &bull; Response Desk</h2>
                        <p>Inquiry Ticket Reference #{$contact_id} &bull; " . htmlspecialchars($orig_area) . "</p>
                    </div>
                    <div class='email-body'>
                        <div class='salutation'>Dear " . htmlspecialchars($sender_name) . ",</div>
                        <p>Thank you for contacting <strong>{$company_name}</strong>. Our administration desk has reviewed your submission and provided the following official response:</p>
                        
                        <div class='reply-text'>" . nl2br(htmlspecialchars($reply_message)) . "</div>

                        <div class='quote-header'>Reference &mdash; Your Original Submission:</div>
                        <div class='quote-box'>
                            <em>\"" . nl2br(htmlspecialchars($orig_msg)) . "\"</em>
                        </div>

                        <p style='margin-bottom: 0;'>If you have any further questions or require additional clarifications, feel free to reply directly to this email or reach our support team at <a href='mailto:{$support_mail}' style='color: #2563eb; text-decoration: none; font-weight: 600;'>{$support_mail}</a>.</p>
                    </div>
                    <div class='email-footer'>
                        <strong>{$company_name}</strong> &bull; India's Open Legal &amp; Corporate Directory Platform<br>
                        Address: {$inst_address1}, {$inst_address2}<br>
                        Official Helpdesk: <a href='mailto:{$support_mail}'>{$support_mail}</a> &bull; Web: <a href='{$inst_url}'>{$inst_domain}</a>
                    </div>
                </div>
            </body>
            </html>
            ";

            // Standard HTML Email Headers
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: {$company_name} <{$from_mail}>\r\n";
            $headers .= "Reply-To: {$company_name} <{$support_mail}>\r\n";
            if ($send_bcc && !empty($support_mail)) {
                $headers .= "Bcc: {$support_mail}\r\n";
            }
            $headers .= "X-Mailer: PHP/" . phpversion();

            // Dispatch email via send_system_email() with fallback to mail()
            $mail_sent = false;
            if (function_exists('send_system_email')) {
                $smtp_res = send_system_email($recipient_email, '', $email_subject, $html_content);
                $mail_sent = !empty($smtp_res['sent']);
            }
            if (!$mail_sent) {
                $mail_sent = @mail($recipient_email, $email_subject, $html_content, $headers);
            }

            // Record Reply into Database
            $safe_reply = mysqli_real_escape_string($con, $reply_message);
            $safe_subj = mysqli_real_escape_string($con, $email_subject);
            $admin_user = mysqli_real_escape_string($con, strval($_SESSION['user_id'] ?? 'Admin'));
            $status_update_sql = $auto_resolve ? ", status = 'RESOLVED'" : "";

            mysqli_query($con, "UPDATE contact SET 
                reply_message = '$safe_reply',
                reply_subject = '$safe_subj',
                replied_at = NOW(),
                replied_by = '$admin_user',
                updated_at = NOW()
                $status_update_sql
                WHERE id = $contact_id");

            unset($_SESSION['unread_contact_count']);

            if ($mail_sent) {
                $_SESSION['flash_msg'] = "Email reply sent successfully to " . htmlspecialchars($recipient_email) . ($auto_resolve ? " and inquiry marked as RESOLVED." : ".");
                $_SESSION['flash_type'] = "success";
            } else {
                $_SESSION['flash_msg'] = "Reply saved to inquiry #{$contact_id}, but the server mail transport reported an issue delivering to " . htmlspecialchars($recipient_email) . ". (Tip: Ensure sendmail/SMTP is active on server).";
                $_SESSION['flash_type'] = "warning";
            }
        } else {
            $_SESSION['flash_msg'] = "Inquiry record #$contact_id not found.";
            $_SESSION['flash_type'] = "danger";
        }
    } else {
        $_SESSION['flash_msg'] = "Please provide a valid recipient email address and response message.";
        $_SESSION['flash_type'] = "danger";
    }

    if (isset($_POST['redirect_tab'])) $_REQUEST['tab'] = $_POST['redirect_tab'];
    if (isset($_POST['redirect_q'])) $_REQUEST['q'] = $_POST['redirect_q'];
    if (isset($_POST['redirect_limit'])) $_REQUEST['limit'] = $_POST['redirect_limit'];
    if (isset($_POST['redirect_p'])) $_REQUEST['p'] = $_POST['redirect_p'];

    header("Location: " . get_contact_redirect_url());
    exit();
}

$tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'all';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$where_clauses = ["1=1"];
if ($tab === 'complaints') {
    $where_clauses[] = "(area LIKE '%Complain%' OR area LIKE '%Delete%' OR message LIKE '%CIN%' OR message LIKE '%DELIST%')";
} elseif ($tab === 'new') {
    $where_clauses[] = "(status = 'NEW' OR status = '' OR status IS NULL)";
} elseif ($tab === 'resolved') {
    $where_clauses[] = "status = 'RESOLVED'";
}

if (!empty($q)) {
    $sq = mysqli_real_escape_string($con, $q);
    $words = array_filter(preg_split('/\s+/', trim($q)), function($w) { return strlen($w) > 0; });
    if (count($words) > 1) {
        $word_conditions = [];
        foreach ($words as $w) {
            $sw = mysqli_real_escape_string($con, $w);
            $word_conditions[] = "(name LIKE '%$sw%' OR email LIKE '%$sw%' OR mobile LIKE '%$sw%' OR area LIKE '%$sw%' OR message LIKE '%$sw%')";
        }
        $where_clauses[] = "((" . implode(' AND ', $word_conditions) . ") OR (name LIKE '%$sq%' OR message LIKE '%$sq%'))";
    } else {
        $where_clauses[] = "(name LIKE '%$sq%' OR email LIKE '%$sq%' OR mobile LIKE '%$sq%' OR area LIKE '%$sq%' OR message LIKE '%$sq%')";
    }
}

$where_sql = implode(' AND ', $where_clauses);

// Limit and Pagination
$limit = isset($_GET['limit']) ? max(5, min(200, intval($_GET['limit']))) : 20;
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;

$count_res = direct_sql("SELECT COUNT(*) as total FROM contact WHERE $where_sql");
$total_rows = isset($count_res['data'][0]['total']) ? (int)$count_res['data'][0]['total'] : 0;
$total_pages = max(1, ceil($total_rows / $limit));

if ($page > $total_pages && $total_rows > 0) {
    $page = $total_pages;
}
$offset = ($page - 1) * $limit;

$start_entry = ($total_rows > 0) ? ($offset + 1) : 0;
$end_entry = min($offset + $limit, $total_rows);

$inquiries_res = direct_sql("SELECT * FROM contact WHERE $where_sql ORDER BY id DESC LIMIT $limit OFFSET $offset");
$inquiries = $inquiries_res['data'] ?? [];

// Counts for tabs
$cnt_all = direct_sql("SELECT COUNT(*) as total FROM contact")['data'][0]['total'] ?? 0;
$cnt_complaints = direct_sql("SELECT COUNT(*) as total FROM contact WHERE area LIKE '%Complain%' OR area LIKE '%Delete%' OR message LIKE '%CIN%' OR message LIKE '%DELIST%'")['data'][0]['total'] ?? 0;
$cnt_new = direct_sql("SELECT COUNT(*) as total FROM contact WHERE status = 'NEW' OR status = '' OR status IS NULL")['data'][0]['total'] ?? 0;
$cnt_resolved = direct_sql("SELECT COUNT(*) as total FROM contact WHERE status = 'RESOLVED'")['data'][0]['total'] ?? 0;

require_once('includes/header.php');
?>

<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Inquiries &amp; Complaints Desk</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Review contact messages, user feedback, and corporate data removal / delisting requests.</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2">
        <a href="../contact.php" target="_blank" class="btn btn-outline-primary px-3 shadow-sm font-weight-bold" style="border-radius: var(--radius-sm); font-size: 0.88rem;">
            <i class="fas fa-external-link-alt mr-1"></i> Public Contact Form
        </a>
    </div>
</div>

<!-- Flash Message Alert -->
<?php if (isset($_SESSION['flash_msg'])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash_type'] ?? 'success'); ?> alert-dismissible fade show shadow-sm border-0 d-flex align-items-center mb-4" role="alert" style="border-radius: var(--radius-md); gap: 10px;">
        <i class="fas <?php echo (($_SESSION['flash_type'] ?? 'success') === 'danger' || ($_SESSION['flash_type'] ?? 'success') === 'warning') ? 'fa-exclamation-circle' : 'fa-check-circle'; ?> fa-lg"></i>
        <div class="flex-grow-1 font-weight-500">
            <?php echo htmlspecialchars($_SESSION['flash_msg']); ?>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline: none;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php 
    unset($_SESSION['flash_msg']);
    unset($_SESSION['flash_type']);
    ?>
<?php endif; ?>

<!-- Tabs & Search -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-md);">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            
            <ul class="nav nav-pills" style="gap: 6px;">
                <li class="nav-item">
                    <a class="nav-link font-weight-bold px-3 py-2 <?php echo ($tab === 'all') ? 'active' : 'text-dark bg-light'; ?>" href="manage_contact.php<?php echo ($limit != 20) ? '?limit='.$limit : ''; ?>" style="border-radius: 20px; font-size: 0.88rem;">
                        <i class="fas fa-inbox mr-1"></i> All Messages <span class="badge <?php echo ($tab === 'all') ? 'badge-light text-primary' : 'badge-secondary'; ?> ml-1"><?php echo $cnt_all; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold px-3 py-2 <?php echo ($tab === 'complaints') ? 'active bg-danger text-white' : 'text-danger bg-light border border-danger'; ?>" href="manage_contact.php?tab=complaints<?php echo ($limit != 20) ? '&limit='.$limit : ''; ?>" style="border-radius: 20px; font-size: 0.88rem;">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Data Removal Complaints <span class="badge <?php echo ($tab === 'complaints') ? 'badge-light text-danger' : 'badge-danger'; ?> ml-1"><?php echo $cnt_complaints; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold px-3 py-2 <?php echo ($tab === 'new') ? 'active bg-warning text-dark' : 'text-dark bg-light'; ?>" href="manage_contact.php?tab=new<?php echo ($limit != 20) ? '&limit='.$limit : ''; ?>" style="border-radius: 20px; font-size: 0.88rem;">
                        <i class="fas fa-clock mr-1"></i> New &bull; Unresolved <span class="badge <?php echo ($tab === 'new') ? 'badge-dark' : 'badge-warning text-dark'; ?> ml-1"><?php echo $cnt_new; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold px-3 py-2 <?php echo ($tab === 'resolved') ? 'active bg-success text-white' : 'text-dark bg-light'; ?>" href="manage_contact.php?tab=resolved<?php echo ($limit != 20) ? '&limit='.$limit : ''; ?>" style="border-radius: 20px; font-size: 0.88rem;">
                        <i class="fas fa-check mr-1"></i> Resolved <span class="badge <?php echo ($tab === 'resolved') ? 'badge-light text-success' : 'badge-secondary'; ?> ml-1"><?php echo $cnt_resolved; ?></span>
                    </a>
                </li>
            </ul>

            <form method="get" action="manage_contact.php" class="d-flex align-items-center" style="min-width: 260px;">
                <?php if ($tab !== 'all'): ?>
                    <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab); ?>">
                <?php endif; ?>
                <?php if ($limit != 20): ?>
                    <input type="hidden" name="limit" value="<?php echo htmlspecialchars($limit); ?>">
                <?php endif; ?>
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search inquiry or CIN..." value="<?php echo htmlspecialchars($q); ?>" style="border-radius: 20px 0 0 20px; font-size: 0.88rem;">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit" style="border-radius: 0 20px 20px 0;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Multi-Delete / Bulk Actions Form -->
<form method="POST" action="manage_contact.php" id="bulkContactForm">
    <input type="hidden" name="redirect_tab" value="<?php echo htmlspecialchars($tab); ?>">
    <input type="hidden" name="redirect_q" value="<?php echo htmlspecialchars($q); ?>">
    <input type="hidden" name="redirect_limit" value="<?php echo htmlspecialchars($limit); ?>">
    <input type="hidden" name="redirect_p" value="<?php echo htmlspecialchars($page); ?>">
    <input type="hidden" name="bulk_action" id="bulkActionInput" value="">

    <!-- Bulk Action Toolbar (Active on selection) -->
    <div id="bulkActionBar" class="card border-0 shadow-sm mb-3 bg-white" style="border-radius: var(--radius-md); display: none; border-left: 4px solid var(--primary-color) !important;">
        <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <span class="badge badge-primary px-3 py-2 font-weight-bold" style="font-size: 0.9rem; border-radius: 8px;">
                    <i class="fas fa-check-square mr-1"></i> <span id="selectedCounter">0</span> Selected
                </span>
                <button type="button" class="btn btn-sm btn-link text-muted p-0 font-weight-bold" id="clearSelectionBtn" style="text-decoration: underline;">
                    Deselect All
                </button>
            </div>
            
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-danger font-weight-bold px-3 shadow-sm" id="btnBulkDelete" style="border-radius: var(--radius-sm);">
                    <i class="fas fa-trash-alt mr-1"></i> Delete Selected (<span class="selected-count-label">0</span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-success font-weight-bold px-3" id="btnBulkResolve" style="border-radius: var(--radius-sm);">
                    <i class="fas fa-check mr-1"></i> Mark Resolved
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold px-3" id="btnBulkNew" style="border-radius: var(--radius-sm);">
                    <i class="fas fa-clock mr-1"></i> Mark as New
                </button>
            </div>
        </div>
    </div>

    <!-- Inquiries Table Card -->
    <div class="card border-0 shadow-sm" style="border-radius: var(--radius-md); overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="contactTable">
                    <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                        <tr>
                            <th class="px-3 py-3 border-0 text-center" style="width: 45px;">
                                <div class="custom-control custom-checkbox d-inline-block">
                                    <input type="checkbox" class="custom-control-input" id="selectAllCheckbox">
                                    <label class="custom-control-label cursor-pointer" for="selectAllCheckbox"></label>
                                </div>
                            </th>
                            <th class="px-3 py-3 border-0" style="width: 70px;">ID</th>
                            <th class="px-4 py-3 border-0" style="width: 170px;">Sender</th>
                            <th class="px-4 py-3 border-0" style="width: 150px;">Category</th>
                            <th class="px-4 py-3 border-0">Message &amp; Extraction</th>
                            <th class="px-4 py-3 border-0" style="width: 130px;">Status</th>
                            <th class="px-4 py-3 border-0 text-right" style="width: 220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($inquiries) > 0): ?>
                            <?php foreach ($inquiries as $row): ?>
                                <?php
                                $is_complaint = (stripos($row['area'], 'Complain') !== false || stripos($row['area'], 'Delete') !== false || stripos($row['message'], 'DELETE') !== false);
                                
                                // Extract possible CIN from message
                                $cin_match = '';
                                if (preg_match('/[ULul][0-9]{5}[A-Za-z]{2}[0-9]{4}[A-Za-z]{3}[0-9]{6}/', $row['message'], $m)) {
                                    $cin_match = strtoupper($m[0]);
                                } elseif (preg_match('/[A-Za-z]{3}-[0-9]{4}/', $row['message'], $m)) {
                                    $cin_match = strtoupper($m[0]);
                                }
                                ?>
                                <tr id="row-<?php echo $row['id']; ?>" class="contact-row" style="<?php echo $is_complaint ? 'background-color: #fffbfa;' : ''; ?>">
                                    <td class="px-3 py-3 text-center align-middle">
                                        <div class="custom-control custom-checkbox d-inline-block">
                                            <input type="checkbox" name="selected_ids[]" value="<?php echo $row['id']; ?>" class="custom-control-input contact-item-checkbox" id="chk-<?php echo $row['id']; ?>">
                                            <label class="custom-control-label cursor-pointer" for="chk-<?php echo $row['id']; ?>"></label>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 font-weight-bold text-muted">
                                        #<?php echo $row['id']; ?>
                                        <div class="small text-muted font-weight-normal" style="font-size: 11px;">
                                            <?php echo !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : ''; ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-weight-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></div>
                                        <?php if (!empty($row['email'])): ?>
                                            <div class="small"><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" class="text-muted"><i class="fas fa-envelope mr-1 text-primary"></i> <?php echo htmlspecialchars($row['email']); ?></a></div>
                                        <?php endif; ?>
                                        <?php if (!empty($row['mobile'])): ?>
                                            <div class="small"><a href="tel:<?php echo htmlspecialchars($row['mobile']); ?>" class="text-muted"><i class="fas fa-phone mr-1 text-success"></i> <?php echo htmlspecialchars($row['mobile']); ?></a></div>
                                        <?php endif; ?>
                                        <?php if (!empty($row['replied_at'])): ?>
                                            <div class="mt-1">
                                                <span class="badge badge-primary px-2 py-0.5 text-white" style="font-size: 10.5px; border-radius: 10px;" title="Replied on <?php echo date('d M Y, h:i A', strtotime($row['replied_at'])); ?> by <?php echo htmlspecialchars($row['replied_by'] ?? 'Admin'); ?>">
                                                    <i class="fas fa-reply mr-1"></i> Replied <?php echo date('d M', strtotime($row['replied_at'])); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if ($is_complaint): ?>
                                            <span class="badge badge-danger px-2.5 py-1 text-uppercase font-weight-bold" style="font-size: 11px;">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Data Removal
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-light border text-dark px-2.5 py-1" style="font-size: 11px;">
                                                <?php echo htmlspecialchars($row['area']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="small text-dark" style="max-width: 480px; white-space: pre-line; line-height: 1.5;">
                                            <?php echo htmlspecialchars($row['message']); ?>
                                        </div>
                                        
                                        <?php if (!empty($cin_match)): ?>
                                            <div class="mt-2 p-2 rounded bg-light border d-inline-flex align-items-center gap-2" style="font-size: 12px; gap: 8px;">
                                                <span class="text-muted">Detected CIN:</span>
                                                <code class="font-weight-bold text-primary"><?php echo $cin_match; ?></code>
                                                <a href="manage_companies.php?q=<?php echo urlencode($cin_match); ?>" target="_blank" class="btn btn-xs btn-primary py-0 px-2 font-weight-bold" style="font-size: 11px;">
                                                    <i class="fas fa-search mr-1"></i> Manage Company &rarr;
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($row['reply_message'])): ?>
                                            <div class="mt-2 p-2 rounded small" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; font-size: 12px;">
                                                <div class="font-weight-bold d-flex align-items-center justify-content-between">
                                                    <span><i class="fas fa-check-circle mr-1 text-success"></i> Sent Email Reply (<?php echo !empty($row['replied_at']) ? date('d M Y, h:i A', strtotime($row['replied_at'])) : ''; ?>):</span>
                                                </div>
                                                <div class="text-muted mt-1 font-italic" style="white-space: pre-line; max-height: 70px; overflow-y: auto;"><?php echo htmlspecialchars($row['reply_message']); ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php 
                                        $st = strtoupper($row['status'] ?? 'NEW');
                                        if ($st === 'RESOLVED') {
                                            echo '<span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Resolved</span>';
                                        } elseif ($st === 'IN_PROGRESS') {
                                            echo '<span class="badge badge-info px-2 py-1">In Progress</span>';
                                        } elseif ($st === 'REJECTED') {
                                            echo '<span class="badge badge-secondary px-2 py-1">Rejected</span>';
                                        } else {
                                            echo '<span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> New</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="btn-group btn-group-sm">
                                            <?php if (!empty($row['email'])): ?>
                                                <button type="button" class="btn btn-primary font-weight-bold btn-reply-contact" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                                    data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                                    data-mobile="<?php echo htmlspecialchars($row['mobile']); ?>"
                                                    data-area="<?php echo htmlspecialchars($row['area']); ?>"
                                                    data-message="<?php echo htmlspecialchars($row['message']); ?>"
                                                    data-cin="<?php echo htmlspecialchars($cin_match); ?>"
                                                    data-replied="<?php echo !empty($row['replied_at']) ? '1' : '0'; ?>"
                                                    data-last-reply="<?php echo htmlspecialchars($row['reply_message'] ?? ''); ?>"
                                                    data-last-subject="<?php echo htmlspecialchars($row['reply_subject'] ?? ''); ?>"
                                                    data-last-replied-at="<?php echo !empty($row['replied_at']) ? date('d M Y, h:i A', strtotime($row['replied_at'])) : ''; ?>"
                                                    title="Reply via Email">
                                                    <i class="fas fa-reply mr-1"></i> Reply
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-secondary" disabled title="No email address provided by sender">
                                                    <i class="fas fa-envelope-slash"></i>
                                                </button>
                                            <?php endif; ?>

                                            <?php if (strtoupper($row['status'] ?? '') !== 'RESOLVED'): ?>
                                                <a href="manage_contact.php?id=<?php echo $row['id']; ?>&update_status=RESOLVED<?php echo ($tab !== 'all') ? '&tab='.urlencode($tab) : ''; ?><?php echo (!empty($q)) ? '&q='.urlencode($q) : ''; ?><?php echo ($limit != 20) ? '&limit='.urlencode($limit) : ''; ?><?php echo ($page > 1) ? '&p='.urlencode($page) : ''; ?>" class="btn btn-outline-success font-weight-bold" title="Mark as Resolved">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="manage_contact.php?id=<?php echo $row['id']; ?>&update_status=NEW<?php echo ($tab !== 'all') ? '&tab='.urlencode($tab) : ''; ?><?php echo (!empty($q)) ? '&q='.urlencode($q) : ''; ?><?php echo ($limit != 20) ? '&limit='.urlencode($limit) : ''; ?><?php echo ($page > 1) ? '&p='.urlencode($page) : ''; ?>" class="btn btn-outline-secondary" title="Reopen as New">
                                                    <i class="fas fa-redo"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="manage_contact.php?del=<?php echo $row['id']; ?><?php echo ($tab !== 'all') ? '&tab='.urlencode($tab) : ''; ?><?php echo (!empty($q)) ? '&q='.urlencode($q) : ''; ?><?php echo ($limit != 20) ? '&limit='.urlencode($limit) : ''; ?><?php echo ($page > 1) ? '&p='.urlencode($page) : ''; ?>" onclick="return confirm('Delete this inquiry permanently?');" class="btn btn-outline-danger" title="Delete Inquiry">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                    <div class="font-weight-bold">No Inquiries Found</div>
                                    <div class="small">No contact submissions match the selected filter.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Complete Pagination & Records Info Footer -->
        <div class="card-footer bg-white border-top py-3 px-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                
                <!-- Left: Info & Per-Page Selector -->
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="small text-muted mb-1 mb-md-0">
                        Showing <span class="font-weight-bold text-dark"><?php echo number_format($start_entry); ?></span>
                        to <span class="font-weight-bold text-dark"><?php echo number_format($end_entry); ?></span>
                        of <span class="font-weight-bold text-dark"><?php echo number_format($total_rows); ?></span> inquiries
                        <?php if ($total_pages > 1): ?>
                            <span class="text-muted">(Page <?php echo $page; ?> of <?php echo $total_pages; ?>)</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex align-items-center gap-1 small text-muted">
                        <label for="limitSelect" class="mb-0 mr-1 font-weight-500">Show:</label>
                        <select id="limitSelect" class="custom-select custom-select-sm" style="width: 78px; height: 32px; font-size: 0.82rem; border-radius: var(--radius-sm);" onchange="window.location.href=this.value;">
                            <?php foreach ([10, 20, 50, 100] as $l_opt): ?>
                                <option value="<?php echo htmlspecialchars(get_contact_page_url(1, $l_opt)); ?>" <?php echo ($limit == $l_opt) ? 'selected' : ''; ?>>
                                    <?php echo $l_opt; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Right: Full Pagination Navigation & Jump To -->
                <?php if ($total_pages > 1): ?>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <nav aria-label="Contact pagination">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- First Page -->
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($page > 1) ? htmlspecialchars(get_contact_page_url(1)) : '#'; ?>" title="First Page">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                            </li>

                            <!-- Previous Page -->
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($page > 1) ? htmlspecialchars(get_contact_page_url($page - 1)) : '#'; ?>" title="Previous Page">
                                    <i class="fas fa-angle-left"></i> Prev
                                </a>
                            </li>

                            <!-- Dynamic Smart Page Numbers -->
                            <?php
                            $range = 2; // window of numbers around current page
                            if ($total_pages <= 7) {
                                for ($i = 1; $i <= $total_pages; $i++) {
                                    $active = ($i == $page) ? 'active font-weight-bold' : '';
                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . htmlspecialchars(get_contact_page_url($i)) . '">' . $i . '</a></li>';
                                }
                            } else {
                                // Page 1
                                echo '<li class="page-item ' . ($page == 1 ? 'active font-weight-bold' : '') . '"><a class="page-link" href="' . htmlspecialchars(get_contact_page_url(1)) . '">1</a></li>';

                                if ($page > 4) {
                                    echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                                }

                                $start = max(2, $page - $range);
                                $end = min($total_pages - 1, $page + $range);

                                for ($i = $start; $i <= $end; $i++) {
                                    $active = ($i == $page) ? 'active font-weight-bold' : '';
                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . htmlspecialchars(get_contact_page_url($i)) . '">' . $i . '</a></li>';
                                }

                                if ($page < $total_pages - 3) {
                                    echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                                }

                                // Last Page
                                echo '<li class="page-item ' . ($page == $total_pages ? 'active font-weight-bold' : '') . '"><a class="page-link" href="' . htmlspecialchars(get_contact_page_url($total_pages)) . '">' . $total_pages . '</a></li>';
                            }
                            ?>

                            <!-- Next Page -->
                            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($page < $total_pages) ? htmlspecialchars(get_contact_page_url($page + 1)) : '#'; ?>" title="Next Page">
                                    Next <i class="fas fa-angle-right"></i>
                                </a>
                            </li>

                            <!-- Last Page -->
                            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($page < $total_pages) ? htmlspecialchars(get_contact_page_url($total_pages)) : '#'; ?>" title="Last Page">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <!-- Quick Jump to Page -->
                    <form method="get" action="manage_contact.php" class="d-inline-flex align-items-center ml-2">
                        <?php foreach ($_GET as $gk => $gv): if ($gk !== 'p'): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($gk); ?>" value="<?php echo htmlspecialchars($gv); ?>">
                        <?php endif; endforeach; ?>
                        <div class="input-group input-group-sm" style="width: 105px;">
                            <input type="number" name="p" min="1" max="<?php echo $total_pages; ?>" class="form-control text-center px-1" style="height: 32px; font-size: 0.82rem;" placeholder="Page" value="<?php echo $page; ?>" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit" title="Jump to page" style="height: 32px; font-size: 0.82rem;">Go</button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</form>

<!-- Email Reply Modal -->
<div class="modal fade" id="replyContactModal" tabindex="-1" role="dialog" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg, 16px); overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #1d4ed8 100%); padding: 18px 24px;">
                <div class="d-flex align-items-center">
                    <div class="mr-3" style="width: 40px; height: 40px; border-radius: 10px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fas fa-reply"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0 text-white" id="replyModalLabel" style="font-size: 1.15rem;">
                            Reply to Inquiry <span id="modalInquiryIdBadge" class="badge badge-light text-primary font-weight-bold ml-1"></span>
                        </h5>
                        <p class="mb-0 text-white-50 small">Official email dispatch to sender from <?php echo htmlspecialchars($noreply_email ?: 'care@olaw.in'); ?></p>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form method="POST" action="manage_contact.php" id="replyContactForm">
                <input type="hidden" name="action" value="send_contact_email_reply">
                <input type="hidden" name="contact_id" id="modalContactIdInput" value="">
                <input type="hidden" name="redirect_tab" value="<?php echo htmlspecialchars($tab); ?>">
                <input type="hidden" name="redirect_q" value="<?php echo htmlspecialchars($q); ?>">
                <input type="hidden" name="redirect_limit" value="<?php echo htmlspecialchars($limit); ?>">
                <input type="hidden" name="redirect_p" value="<?php echo htmlspecialchars($page); ?>">

                <div class="modal-body p-4" style="background: #ffffff;">
                    
                    <!-- Sender & Inquiry Summary Strip -->
                    <div class="p-3 mb-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="row small align-items-center">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <span class="text-muted d-block font-weight-bold text-uppercase" style="font-size: 11px;">Sender Name:</span>
                                <strong class="text-dark font-weight-bold" id="modalSenderNameDisplay">-</strong>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0">
                                <span class="text-muted d-block font-weight-bold text-uppercase" style="font-size: 11px;">Mobile / Phone:</span>
                                <span class="text-dark font-weight-500" id="modalSenderMobileDisplay">-</span>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted d-block font-weight-bold text-uppercase" style="font-size: 11px;">Inquiry Area:</span>
                                <span class="badge badge-primary px-2.5 py-1 font-weight-bold" id="modalSenderCategoryDisplay">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Collapsible Original Message -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="small text-muted font-weight-bold text-uppercase mb-0">
                                <i class="fas fa-quote-left mr-1 text-primary"></i> Original Message Received:
                            </label>
                            <a class="small text-primary font-weight-bold" data-toggle="collapse" href="#modalOriginalMessageCollapse" role="button" aria-expanded="true" aria-controls="modalOriginalMessageCollapse">
                                Toggle Details
                            </a>
                        </div>
                        <div class="collapse show" id="modalOriginalMessageCollapse">
                            <div class="p-3 rounded small text-dark" id="modalOriginalMessageText" style="background: #f8fafc; border-left: 3px solid #3b82f6; max-height: 120px; overflow-y: auto; white-space: pre-line; line-height: 1.55; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                                -
                            </div>
                        </div>
                    </div>

                    <!-- Previous Reply Notice (shown if already replied) -->
                    <div id="modalPreviousReplyBox" class="alert alert-info border-0 p-3 mb-3 d-none" style="border-radius: var(--radius-sm, 8px); background: #eff6ff; border-left: 4px solid #2563eb !important;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <strong class="text-primary small font-weight-bold">
                                <i class="fas fa-history mr-1"></i> Previously Replied on <span id="modalPreviousRepliedAt"></span>
                            </strong>
                            <a class="small text-primary font-weight-bold" data-toggle="collapse" href="#modalPreviousReplyCollapse" role="button">
                                View Previous Sent Reply
                            </a>
                        </div>
                        <div class="collapse" id="modalPreviousReplyCollapse">
                            <div class="p-2 mt-2 bg-white rounded border small text-dark font-italic" id="modalPreviousReplyText" style="white-space: pre-line; max-height: 100px; overflow-y: auto;"></div>
                        </div>
                    </div>

                    <!-- Quick Template Dropdown -->
                    <div class="form-group mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="quickTemplateSelect" class="small font-weight-bold text-uppercase text-muted mb-0">
                                <i class="fas fa-magic mr-1 text-primary"></i> Load Response Template:
                            </label>
                            <span class="badge badge-light text-muted border font-weight-normal">Auto-populates subject &amp; message</span>
                        </div>
                        <select id="quickTemplateSelect" class="custom-select custom-select-sm" style="height: 38px; font-size: 0.88rem; border-radius: var(--radius-sm, 8px);">
                            <option value="">-- Choose a standard template or compose custom reply --</option>
                            <option value="data_removal">1. Company Delisting / Data Removal - Confirmed &amp; Completed</option>
                            <option value="need_docs">2. Company Delisting / Data Removal - Proof &amp; Documents Required</option>
                            <option value="general_help">3. General Public Civic Data &amp; Platform Guidance</option>
                            <option value="api_inquiry">4. Developer REST API Hub &amp; Integration Access</option>
                            <option value="blank">5. Blank / Clear Template</option>
                        </select>
                    </div>

                    <!-- Recipient Email & Subject -->
                    <div class="form-row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label for="modalRecipientEmail" class="small font-weight-bold text-dark mb-1">
                                Recipient Email Address <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" name="recipient_email" id="modalRecipientEmail" class="form-control" required style="height: 38px; font-size: 0.88rem;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="modalEmailSubject" class="small font-weight-bold text-dark mb-1">
                                Email Subject Line <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="email_subject" id="modalEmailSubject" class="form-control form-control-sm" required style="height: 38px; font-size: 0.88rem;" placeholder="Re: Your Inquiry to Online Law">
                        </div>
                    </div>

                    <!-- Reply Message Textarea -->
                    <div class="form-group mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="modalReplyMessage" class="small font-weight-bold text-dark mb-0">
                                Reply Message Body <span class="text-danger">*</span>
                            </label>
                            <span class="small text-muted font-italic">Formatted into official branded HTML email</span>
                        </div>
                        <textarea name="reply_message" id="modalReplyMessage" rows="7" class="form-control" required placeholder="Type your response message here..." style="font-size: 0.9rem; line-height: 1.6; border-radius: var(--radius-sm, 8px);"></textarea>
                    </div>

                    <!-- Options -->
                    <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" name="auto_resolve" value="1" class="custom-control-input" id="checkAutoResolve" checked>
                            <label class="custom-control-label font-weight-bold text-dark cursor-pointer" for="checkAutoResolve" style="font-size: 0.88rem;">
                                Automatically mark this inquiry as <span class="badge badge-success px-2 py-0.5">RESOLVED</span> upon sending email
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="send_bcc" value="1" class="custom-control-input" id="checkSendBcc" checked>
                            <label class="custom-control-label text-muted cursor-pointer" for="checkSendBcc" style="font-size: 0.85rem;">
                                Send audit copy (BCC) to helpdesk (<?php echo htmlspecialchars($inst_email ?: 'care@olaw.in'); ?>)
                            </label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary font-weight-bold px-3" data-dismiss="modal" style="border-radius: var(--radius-sm, 8px);">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm" id="btnSubmitReply" style="border-radius: var(--radius-sm, 8px);">
                        <i class="fas fa-paper-plane mr-1.5"></i> Send Email Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.cursor-pointer {
    cursor: pointer;
}
.contact-row.selected-row {
    background-color: #eff6ff !important;
}
.pagination .page-link {
    color: #4b5563;
    border-color: #e5e7eb;
    padding: 0.35rem 0.65rem;
    font-size: 0.82rem;
}
.pagination .page-item.active .page-link {
    background-color: var(--primary-color, #2563eb);
    border-color: var(--primary-color, #2563eb);
    color: #ffffff;
}
.pagination .page-item.disabled .page-link {
    color: #9ca3af;
    background-color: #f9fafb;
    border-color: #e5e7eb;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const itemCheckboxes = document.querySelectorAll('.contact-item-checkbox');
    const bulkActionBar = document.getElementById('bulkActionBar');
    const selectedCounter = document.getElementById('selectedCounter');
    const selectedCountLabels = document.querySelectorAll('.selected-count-label');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');
    const bulkActionForm = document.getElementById('bulkContactForm');
    const bulkActionInput = document.getElementById('bulkActionInput');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const btnBulkResolve = document.getElementById('btnBulkResolve');
    const btnBulkNew = document.getElementById('btnBulkNew');

    function updateSelectionState() {
        const checkedBoxes = document.querySelectorAll('.contact-item-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (selectedCounter) selectedCounter.textContent = count;
        selectedCountLabels.forEach(el => el.textContent = count);
        
        if (count > 0) {
            if (bulkActionBar) bulkActionBar.style.display = 'block';
        } else {
            if (bulkActionBar) bulkActionBar.style.display = 'none';
        }

        // Update select all checkbox state
        if (selectAllCheckbox) {
            if (itemCheckboxes.length > 0 && count === itemCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else if (count > 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
        }

        // Highlight table rows
        itemCheckboxes.forEach(chk => {
            const tr = chk.closest('tr');
            if (tr) {
                if (chk.checked) {
                    tr.classList.add('selected-row');
                } else {
                    tr.classList.remove('selected-row');
                }
            }
        });
    }

    // Select All toggle
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(chk => {
                chk.checked = selectAllCheckbox.checked;
            });
            updateSelectionState();
        });
    }

    // Individual checkbox change & Shift + Click support
    let lastChecked = null;
    itemCheckboxes.forEach((chk) => {
        chk.addEventListener('click', function(e) {
            if (e.shiftKey && lastChecked && lastChecked !== this) {
                let inRange = false;
                itemCheckboxes.forEach(item => {
                    if (item === this || item === lastChecked) {
                        inRange = !inRange;
                    }
                    if (inRange || item === this || item === lastChecked) {
                        item.checked = lastChecked.checked;
                    }
                });
            }
            lastChecked = this;
            updateSelectionState();
        });
    });

    // Clear selection button
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function(e) {
            e.preventDefault();
            itemCheckboxes.forEach(chk => { chk.checked = false; });
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            updateSelectionState();
        });
    }

    // Bulk Delete Action Handler
    if (btnBulkDelete) {
        btnBulkDelete.addEventListener('click', function() {
            const count = document.querySelectorAll('.contact-item-checkbox:checked').length;
            if (count === 0) {
                alert('Please select at least one contact inquiry to delete.');
                return;
            }
            if (confirm(`Are you sure you want to permanently delete ${count} selected contact message(s)? This action cannot be undone.`)) {
                bulkActionInput.value = 'delete';
                bulkActionForm.submit();
            }
        });
    }

    // Bulk Resolve Action Handler
    if (btnBulkResolve) {
        btnBulkResolve.addEventListener('click', function() {
            const count = document.querySelectorAll('.contact-item-checkbox:checked').length;
            if (count === 0) {
                alert('Please select at least one contact inquiry.');
                return;
            }
            if (confirm(`Mark ${count} selected contact message(s) as RESOLVED?`)) {
                bulkActionInput.value = 'resolve';
                bulkActionForm.submit();
            }
        });
    }

    // Bulk Mark as New Action Handler
    if (btnBulkNew) {
        btnBulkNew.addEventListener('click', function() {
            const count = document.querySelectorAll('.contact-item-checkbox:checked').length;
            if (count === 0) {
                alert('Please select at least one contact inquiry.');
                return;
            }
            if (confirm(`Mark ${count} selected contact message(s) as NEW?`)) {
                bulkActionInput.value = 'new';
                bulkActionForm.submit();
            }
        });
    }

    // =========================================================================
    // Email Reply Modal Handlers
    // =========================================================================
    $('.btn-reply-contact').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name') || 'User';
        const email = $(this).data('email') || '';
        const mobile = $(this).data('mobile') || 'N/A';
        const area = $(this).data('area') || 'General Inquiry';
        const message = $(this).data('message') || '';
        const cin = $(this).data('cin') || '';
        const replied = $(this).data('replied') == '1';
        const lastReply = $(this).data('last-reply') || '';
        const lastSubject = $(this).data('last-subject') || '';
        const lastRepliedAt = $(this).data('last-replied-at') || '';

        $('#modalContactIdInput').val(id);
        $('#modalInquiryIdBadge').text('#' + id);
        $('#modalSenderNameDisplay').text(name);
        $('#modalSenderMobileDisplay').text(mobile);
        $('#modalSenderCategoryDisplay').text(area);
        $('#modalOriginalMessageText').text(message);
        $('#modalRecipientEmail').val(email);

        // Store active item data on the modal for quick templates
        $('#replyContactModal').data('active-item', {
            id: id,
            name: name,
            email: email,
            mobile: mobile,
            area: area,
            message: message,
            cin: cin
        });

        // Set previous reply state
        if (replied && lastRepliedAt) {
            $('#modalPreviousReplyBox').removeClass('d-none');
            $('#modalPreviousRepliedAt').text(lastRepliedAt);
            $('#modalPreviousReplyText').text(lastReply || '(Empty message)');
        } else {
            $('#modalPreviousReplyBox').addClass('d-none');
        }

        // Reset template selector
        $('#quickTemplateSelect').val('');

        // Set contextual default subject & greeting
        const isRemoval = area.toLowerCase().includes('complain') || area.toLowerCase().includes('delete') || message.toUpperCase().includes('DELIST') || message.toUpperCase().includes('DELETE') || cin !== '';
        if (isRemoval && cin) {
            $('#modalEmailSubject').val(`Re: Request regarding ${cin} [#${id}] - Online Law Support`);
            $('#quickTemplateSelect').val('data_removal').trigger('change');
        } else {
            $('#modalEmailSubject').val(`Re: Inquiry regarding ${area} [#${id}] - Online Law Support`);
            $('#modalReplyMessage').val(`Dear ${name},\n\nThank you for contacting Online Law regarding ${area}.\n\n`);
        }

        $('#replyContactModal').modal('show');
    });

    // Quick Template Selector Change Handler
    $('#quickTemplateSelect').on('change', function() {
        const tpl = $(this).val();
        const item = $('#replyContactModal').data('active-item') || {};
        const name = item.name || 'User';
        const area = item.area || 'your inquiry';
        const cin = item.cin || '';
        const id = item.id || '';

        if (tpl === 'data_removal') {
            $('#modalEmailSubject').val(`Re: Corporate Listing Delisting Request regarding ${cin || 'Company'} [#${id}] - Online Law`);
            $('#modalReplyMessage').val(
`Dear ${name},

Thank you for contacting Online Law regarding the corporate listing for ${cin ? '(' + cin + ')' : 'your company'}.

We have verified your request and records. The specified corporate record has been delisted and unlinked from our public website search directory.

Please allow a brief interval for external third-party search engines (such as Google and Bing) to refresh their automated search index caches.

If you have any further questions or require any additional assistance, please feel free to reply directly to this email.

Warm regards,
Online Law Support Desk
Website: https://olaw.in`
            );
        } else if (tpl === 'need_docs') {
            $('#modalEmailSubject').val(`Re: Verification Documents Required for Company Record Request [#${id}] - Online Law`);
            $('#modalReplyMessage').val(
`Dear ${name},

Thank you for contacting Online Law regarding ${cin ? '(' + cin + ')' : 'the company record'}.

In order to verify authorization and process your listing modification or data removal request, please reply directly to this email attaching the following documents:

1. Copy of the official Certificate of Incorporation / MCA Master Data filing.
2. Official request letter on the company's letterhead authorized by a Director.
3. Government-issued photo ID of the authorized director or signatory.

Upon receipt and review of these documents, our administration desk will process your request within 24–48 business hours.

Warm regards,
Online Law Compliance Desk
Website: https://olaw.in`
            );
        } else if (tpl === 'general_help') {
            $('#modalEmailSubject').val(`Re: Information regarding ${area} [#${id}] - Online Law Support`);
            $('#modalReplyMessage').val(
`Dear ${name},

Thank you for contacting Online Law support regarding ${area}.

Our platform is a free open digital directory dedicated to democratizing legal, banking, and public civic identifiers for every citizen across India. We synchronize data with official government and regulatory authorities including the Ministry of Corporate Affairs (MCA), Reserve Bank of India (RBI), and India Post.

If you require specific lookup assistance or details regarding any identifier (such as Bank IFSC, MICR, PIN Code, or NIC Code), please reply with the exact details and we will be delighted to guide you.

Warm regards,
Online Law Support Desk
Website: https://olaw.in`
            );
        } else if (tpl === 'api_inquiry') {
            $('#modalEmailSubject').val(`Re: Developer REST API Hub & Data Integration [#${id}] - Online Law`);
            $('#modalReplyMessage').val(
`Dear ${name},

Thank you for your interest in the Online Law Developer API Hub.

We offer high-performance RESTful JSON APIs for Bank IFSC/MICR, Postal PIN Codes, GST HSN/SAC, and MCA Company Incorporations.

You can learn more and review all available endpoints at our documentation page:
https://olaw.in/api

If you require a custom API Key, increased rate limits, or custom bulk enterprise endpoints, please reply to this email with your use case.

Warm regards,
Online Law API Operations
Website: https://olaw.in`
            );
        } else if (tpl === 'blank') {
            $('#modalReplyMessage').val(`Dear ${name},\n\n`);
        }
    });

    // Form submit loading state
    $('#replyContactForm').on('submit', function() {
        const btn = $('#btnSubmitReply');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1.5"></i> Sending Email...');
    });
});
</script>

<?php require_once('includes/footer.php'); ?>
