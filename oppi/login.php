<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../includes/security.php');
require_once(__DIR__ . '/../includes/function.php');

$error = '';
$success = '';

// Check IP Rate Limiting (10 failed attempts per 15-minute window)
$rate_status = function_exists('check_rate_limit') ? check_rate_limit('admin_login', 10, 900) : ['allowed' => true, 'remaining_seconds' => 0];
if (!$rate_status['allowed']) {
    $mins_left = max(1, ceil($rate_status['remaining_seconds'] / 60));
    $error = "Too many failed sign-in attempts. For security, login is temporarily locked for {$mins_left} more minute(s). Please try again later.";
}

// Handle cancel action from OTP step
if (isset($_GET['cancel'])) {
    unset($_SESSION['pending_otp']);
    header("Location: login.php");
    exit();
}

// Handle redirect messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'session_invalid') {
        $error = "Session expired or logged in from another browser. Please sign in.";
    } elseif ($_GET['msg'] === 'account_blocked') {
        $error = "Your account is deactivated. Please contact support.";
    } elseif ($_GET['msg'] === 'logged_out') {
        $success = "You have been logged out successfully.";
    }
}

// If already logged in with a valid active token, redirect to dashboard
if (check_admin_auth(false) !== false) {
    header("Location: index.php");
    exit();
}

// Helper: Mask mobile number for display
function mask_mobile_num($phone) {
    $digits = preg_replace('/[^0-9]/', '', (string)$phone);
    if (strlen($digits) >= 10) {
        return '+91 ' . substr($digits, 0, 2) . '******' . substr($digits, -2);
    }
    return '******' . substr($digits, -2);
}

// Helper: Mask email address for display
function mask_email_addr($email) {
    if (empty($email) || strpos($email, '@') === false) return 'registered email';
    list($user, $domain) = explode('@', $email, 2);
    $len = strlen($user);
    if ($len <= 2) {
        $masked_user = substr($user, 0, 1) . '*';
    } else {
        $masked_user = substr($user, 0, 2) . str_repeat('*', min(5, max(1, $len - 3))) . substr($user, -1);
    }
    return $masked_user . '@' . $domain;
}

// Handle Resend OTP Request
if (isset($_GET['resend']) && !empty($_SESSION['pending_otp'])) {
    $now = time();
    $last_sent = $_SESSION['pending_otp']['last_sent'] ?? 0;
    if ($now - $last_sent < 30) {
        $wait_sec = 30 - ($now - $last_sent);
        $error = "Please wait {$wait_sec} second(s) before requesting another verification code.";
    } else {
        $p_uid = intval($_SESSION['pending_otp']['user_id']);
        $p_check = direct_sql("SELECT * FROM op_user WHERE id = $p_uid LIMIT 1");
        if (!empty($p_check['data'][0])) {
            $pu = $p_check['data'][0];
            $new_otp = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            $_SESSION['pending_otp']['otp'] = $new_otp;
            $_SESSION['pending_otp']['expires'] = $now + 300;
            $_SESSION['pending_otp']['last_sent'] = $now;

            direct_sql("UPDATE op_user SET otp_code = '$new_otp', otp_expiry = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE id = $p_uid", 'set');
            send_admin_otp($pu, $new_otp);
            $success = "A new verification code has been dispatched to your mobile and email.";
        }
    }
}

// ----------------------------------------------------------------------------
// POST FORM HANDLER
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? 'login';

    // ------------------------------------------------------------------------
    // CASE A: VERIFY OTP CODE
    // ------------------------------------------------------------------------
    if ($action === 'verify_otp') {
        if (empty($_SESSION['pending_otp'])) {
            $error = "Your verification session has expired. Please sign in again.";
        } elseif (function_exists('validate_csrf_token') && !validate_csrf_token($_POST['csrf_token'] ?? '', 'admin_otp')) {
            $error = "Security token expired or invalid. Please try submitting again.";
        } else {
            $now = time();
            if ($now > ($_SESSION['pending_otp']['expires'] ?? 0)) {
                $error = "Verification code expired. Click 'Resend Code' to receive a new one.";
            } else {
                $entered_otp = preg_replace('/[^0-9]/', '', trim($_POST['otp_code'] ?? ''));
                $p_uid = intval($_SESSION['pending_otp']['user_id']);
                
                $db_check = direct_sql("SELECT * FROM op_user WHERE id = $p_uid LIMIT 1");
                $pu = $db_check['data'][0] ?? null;

                $session_otp = (string)($_SESSION['pending_otp']['otp'] ?? '');
                $db_otp = (string)($pu['otp_code'] ?? '');
                $is_db_active = !empty($pu['otp_expiry']) && (strtotime($pu['otp_expiry']) >= $now);

                $is_valid_otp = (!empty($entered_otp) && strlen($entered_otp) === 6) && 
                                ($entered_otp === $session_otp || ($entered_otp === $db_otp && $is_db_active));

                if ($is_valid_otp && $pu) {
                    // Authentication Succeeded!
                    $auth_token = bin2hex(random_bytes(32));

                    // Clear OTP fields in DB
                    direct_sql("UPDATE op_user SET token = '$auth_token', last_login = NOW(), status = 'ACTIVE', otp_code = NULL, otp_expiry = NULL WHERE id = $p_uid", 'set');

                    // If password needs rehash, update
                    if (!empty($_SESSION['pending_otp']['needs_rehash']) && !empty($_SESSION['pending_otp']['plain_pass'])) {
                        $new_hash = mysqli_real_escape_string($con, password_hash($_SESSION['pending_otp']['plain_pass'], PASSWORD_DEFAULT));
                        direct_sql("UPDATE op_user SET user_pass = '$new_hash' WHERE id = $p_uid", 'set');
                    }

                    // Clear rate limiting & OTP session state
                    if (function_exists('clear_rate_limit')) {
                        clear_rate_limit('admin_login');
                        clear_rate_limit('admin_otp');
                    }
                    unset($_SESSION['pending_otp']);

                    // Regenerate session and set auth payload
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['user_id'] = $p_uid;
                    $_SESSION['admin_token'] = $auth_token;
                    $_SESSION['username'] = $pu['user_name'];
                    $_SESSION['full_name'] = $pu['full_name'] ?: $pu['user_name'];
                    $_SESSION['user_type'] = $pu['user_type'] ?: 'ADMIN';

                    session_write_close();
                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['pending_otp']['attempts'] = ($_SESSION['pending_otp']['attempts'] ?? 0) + 1;
                    if (function_exists('record_rate_limit_failure')) {
                        record_rate_limit_failure('admin_otp', 5, 300);
                    }
                    if ($_SESSION['pending_otp']['attempts'] >= 5) {
                        unset($_SESSION['pending_otp']);
                        $error = "Too many failed OTP attempts. Please sign in again.";
                    } else {
                        $remain = 5 - $_SESSION['pending_otp']['attempts'];
                        $error = "Invalid 6-digit verification code. {$remain} attempt(s) remaining.";
                    }
                }
            }
        }
    }

    // ------------------------------------------------------------------------
    // CASE B: USERNAME & PASSWORD SIGN-IN
    // ------------------------------------------------------------------------
    else {
        // 1. Verify Rate Limiting
        if (!$rate_status['allowed']) {
            $mins_left = max(1, ceil($rate_status['remaining_seconds'] / 60));
            $error = "Too many failed sign-in attempts. For security, login is temporarily locked for {$mins_left} more minute(s).";
        }
        // 2. Verify CSRF Token
        elseif (function_exists('validate_csrf_token') && !validate_csrf_token($_POST['csrf_token'] ?? '', 'admin_login')) {
            $error = "Security token expired or invalid. Please try submitting again.";
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            
            // Check credentials strictly against op_user table in database
            $safe_username = mysqli_real_escape_string($con, $username);
            $check = direct_sql("SELECT * FROM op_user WHERE user_name = '$safe_username' LIMIT 1");
            if (isset($check['data'][0])) {
                $u = $check['data'][0];
                if (!empty($u['status']) && strtoupper($u['status']) === 'BLOCK') {
                    $error = "Your account is blocked. Please contact support.";
                    if (function_exists('record_rate_limit_failure')) {
                        record_rate_limit_failure('admin_login', 10, 900);
                    }
                } else {
                    $stored_pass = trim($u['user_pass'] ?? '');
                    $is_password_valid = false;
                    $needs_rehash = false;

                    if (!empty($stored_pass) && !empty($password)) {
                        if (password_verify($password, $stored_pass)) {
                            $is_password_valid = true;
                            if (password_needs_rehash($stored_pass, PASSWORD_DEFAULT)) {
                                $needs_rehash = true;
                            }
                        } elseif (md5($password) === $stored_pass || $password === $stored_pass) {
                            $is_password_valid = true;
                            $needs_rehash = true;
                        }
                    }

                    if ($is_password_valid) {
                        $user_id = intval($u['id']);
                        $require_otp = (!empty($u['allow_otp']) && strtoupper($u['allow_otp']) === 'YES');

                        // ----------------------------------------------------
                        // REQUIRE 2FA OTP VERIFICATION
                        // ----------------------------------------------------
                        if ($require_otp) {
                            $otp = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
                            $now = time();

                            $_SESSION['pending_otp'] = [
                                'user_id' => $user_id,
                                'otp' => $otp,
                                'expires' => $now + 300,
                                'attempts' => 0,
                                'last_sent' => $now,
                                'needs_rehash' => $needs_rehash,
                                'plain_pass' => $password,
                                'mobile' => $u['user_mobile'],
                                'email' => $u['user_email'],
                                'name' => $u['full_name'] ?: $u['user_name']
                            ];

                            // Store in DB for reliability
                            direct_sql("UPDATE op_user SET otp_code = '$otp', otp_expiry = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE id = $user_id", 'set');

                            // Dispatch via DLT SMS & HTML Email
                            send_admin_otp($u, $otp);

                            $success = "Verification code has been sent to your registered mobile and email.";
                        }
                        // ----------------------------------------------------
                        // DIRECT SIGN-IN (OTP DISABLED)
                        // ----------------------------------------------------
                        else {
                            $auth_token = bin2hex(random_bytes(32));

                            if ($needs_rehash) {
                                $new_hash = mysqli_real_escape_string($con, password_hash($password, PASSWORD_DEFAULT));
                                direct_sql("UPDATE op_user SET user_pass = '$new_hash' WHERE id = $user_id", 'set');
                            }

                            direct_sql("UPDATE op_user SET token = '$auth_token', last_login = NOW(), status = 'ACTIVE' WHERE id = $user_id", 'set');

                            if (function_exists('clear_rate_limit')) {
                                clear_rate_limit('admin_login');
                            }

                            session_regenerate_id(true);
                            $_SESSION['admin_logged_in'] = true;
                            $_SESSION['user_id'] = $user_id;
                            $_SESSION['admin_token'] = $auth_token;
                            $_SESSION['username'] = $u['user_name'];
                            $_SESSION['full_name'] = $u['full_name'] ?: $u['user_name'];
                            $_SESSION['user_type'] = $u['user_type'] ?: 'ADMIN';

                            session_write_close();
                            header("Location: index.php");
                            exit();
                        }
                    } else {
                        $error = "Invalid username or password!";
                        if (function_exists('record_rate_limit_failure')) {
                            record_rate_limit_failure('admin_login', 10, 900);
                        }
                    }
                }
            } else {
                $error = "Invalid username or password!";
                if (function_exists('record_rate_limit_failure')) {
                    record_rate_limit_failure('admin_login', 10, 900);
                }
            }
        }
    }
}

$is_otp_step = !empty($_SESSION['pending_otp']);
$pending_user_name = $_SESSION['pending_otp']['name'] ?? 'Admin';
$pending_mobile = $_SESSION['pending_otp']['mobile'] ?? '';
$pending_email = $_SESSION['pending_otp']['email'] ?? '';
$is_localhost = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1', 'localhost']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo $is_otp_step ? 'Security Verification (OTP)' : 'Admin Login'; ?> - <?php echo htmlspecialchars($inst_name); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap-4.1.3.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .otp-input-box {
            font-family: 'JetBrains Mono', monospace;
            font-size: 2rem;
            letter-spacing: 14px;
            text-align: center;
            font-weight: 700;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            border: 2px solid var(--border-soft);
            background: #ffffff;
            transition: all 0.25s ease;
        }
        .otp-input-box:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
            outline: none;
        }
        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body class="login-page">

<div class="login-card" style="max-width: <?php echo $is_otp_step ? '440px' : '400px'; ?>;">
    
    <!-- Header -->
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 58px; height: 58px; border-radius: 16px; background: <?php echo $is_otp_step ? 'linear-gradient(135deg, #059669, #10b981)' : 'linear-gradient(135deg, var(--primary), #38bdf8)'; ?>; color: white; font-size: 24px; box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);">
            <i class="fas <?php echo $is_otp_step ? 'fa-shield-alt' : 'fa-balance-scale'; ?>"></i>
        </div>
        <h3 class="font-weight-bold mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.55rem; color: var(--text-main);">
            <?php echo $is_otp_step ? 'Two-Factor Verification' : 'Online Law Admin'; ?>
        </h3>
        <p class="text-muted mb-0" style="font-size: 0.86rem;">
            <?php echo $is_otp_step ? 'Enter the security code to access your console' : 'Sign in to manage directory registries and data'; ?>
        </p>
    </div>

    <!-- Alerts -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 px-3 mb-3 rounded-lg shadow-sm border-0 d-flex align-items-center" style="font-size: 0.86rem; gap: 8px;">
            <i class="fas fa-exclamation-circle flex-shrink-0"></i>
            <div><?php echo htmlspecialchars($error); ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success py-2 px-3 mb-3 rounded-lg shadow-sm border-0 d-flex align-items-center" style="font-size: 0.86rem; gap: 8px;">
            <i class="fas fa-check-circle flex-shrink-0"></i>
            <div><?php echo htmlspecialchars($success); ?></div>
        </div>
    <?php endif; ?>

    <!-- STEP 2: OTP VERIFICATION VIEW -->
    <?php if ($is_otp_step): ?>
        <div class="mb-3 p-3 rounded-lg text-center" style="background: #f8fafc; border: 1px solid var(--border-soft);">
            <div class="mb-2">
                <span class="security-badge"><i class="fas fa-lock"></i> 2FA Protected</span>
            </div>
            <p class="small text-muted mb-1">
                A 6-digit verification code has been dispatched via <strong>OLAWIN SMS</strong> and <strong>Email</strong> to:
            </p>
            <?php if (!empty($pending_mobile)): ?>
                <div class="small font-weight-bold text-dark"><i class="fas fa-sms text-primary mr-1"></i> <?php echo mask_mobile_num($pending_mobile); ?></div>
            <?php endif; ?>
            <?php if (!empty($pending_email)): ?>
                <div class="small font-weight-bold text-dark"><i class="fas fa-envelope text-primary mr-1"></i> <?php echo mask_email_addr($pending_email); ?></div>
            <?php endif; ?>
        </div>

        <?php if ($is_localhost && !empty($_SESSION['pending_otp']['otp'])): ?>
            <div class="alert alert-info py-2 px-3 mb-3 rounded-lg border-0 d-flex align-items-center justify-content-between" style="font-size: 0.82rem;">
                <div><i class="fas fa-code mr-1"></i> <strong>Dev Mode OTP:</strong> <code class="font-weight-bold"><?php echo $_SESSION['pending_otp']['otp']; ?></code></div>
                <button type="button" class="btn btn-sm btn-outline-info py-0 px-2" onclick="document.getElementById('otpInput').value='<?php echo $_SESSION['pending_otp']['otp']; ?>';">Fill</button>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="action" value="verify_otp">
            <?php echo function_exists('get_csrf_input') ? get_csrf_input('admin_otp') : ''; ?>

            <div class="form-group mb-3 text-center">
                <label class="font-weight-bold text-muted small text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">6-Digit OTP Code</label>
                <input type="text" name="otp_code" id="otpInput" class="form-control otp-input-box" placeholder="••••••" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required autofocus autocomplete="one-time-code">
            </div>

            <button type="submit" class="btn btn-success btn-block py-2 font-weight-bold shadow-sm mb-3" style="border-radius: var(--radius-sm); font-size: 0.95rem; background: linear-gradient(135deg, #059669, #10b981); border: none;">
                Verify &amp; Enter Dashboard <i class="fas fa-arrow-right ml-2"></i>
            </button>

            <div class="d-flex justify-content-between align-items-center pt-2">
                <a href="login.php?cancel=1" class="text-muted small text-decoration-none">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Sign In
                </a>
                <a href="login.php?resend=1" class="text-primary small font-weight-bold text-decoration-none" id="resendLink">
                    <i class="fas fa-redo-alt mr-1"></i> Resend Code
                </a>
            </div>
        </form>

    <!-- STEP 1: USERNAME & PASSWORD SIGN-IN -->
    <?php else: ?>
        <form method="post" action="">
            <input type="hidden" name="action" value="login">
            <?php echo function_exists('get_csrf_input') ? get_csrf_input('admin_login') : ''; ?>
            
            <div class="form-group mb-3">
                <label class="font-weight-bold text-muted small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Username</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white text-muted border-right-0" style="border-color: var(--border-soft);"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" name="username" class="form-control border-left-0 pl-0" placeholder="oppi" required autofocus style="border-color: var(--border-soft); font-size: 0.92rem;">
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="font-weight-bold text-muted small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Password</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white text-muted border-right-0" style="border-color: var(--border-soft);"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" name="password" class="form-control border-left-0 pl-0" placeholder="••••••••" required style="border-color: var(--border-soft); font-size: 0.92rem;">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold shadow-sm" style="border-radius: var(--radius-sm); font-size: 0.95rem; background: var(--primary-gradient); border: none;">
                Sign In <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </form>
    <?php endif; ?>
    
    <div class="text-center mt-4 pt-2 border-top">
        <a href="<?php echo $base_url; ?>index" class="text-muted small text-decoration-none">
            <i class="fas fa-arrow-left mr-1"></i> Back to Public Website
        </a>
    </div>
</div>

</body>
</html>
