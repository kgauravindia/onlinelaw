<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../includes/function.php');

$error = '';
$success = '';

// Check IP Rate Limiting (10 failed attempts per 15-minute window)
$rate_status = check_rate_limit('admin_login', 10, 900);
if (!$rate_status['allowed']) {
    $mins_left = max(1, ceil($rate_status['remaining_seconds'] / 60));
    $error = "Too many failed sign-in attempts. For security, login is temporarily locked for {$mins_left} more minute(s). Please try again later.";
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Verify Rate Limiting
    if (!$rate_status['allowed']) {
        $mins_left = max(1, ceil($rate_status['remaining_seconds'] / 60));
        $error = "Too many failed sign-in attempts. For security, login is temporarily locked for {$mins_left} more minute(s).";
    }
    // 2. Verify CSRF Token
    elseif (!validate_csrf_token($_POST['csrf_token'] ?? '', 'admin_login')) {
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
                record_rate_limit_failure('admin_login', 10, 900);
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
                    
                    // Generate a cryptographically secure authentication token
                    $auth_token = bin2hex(random_bytes(32));

                    // Upgrade password hash in database if legacy format was detected
                    if ($needs_rehash) {
                        $new_hash = mysqli_real_escape_string($con, password_hash($password, PASSWORD_DEFAULT));
                        direct_sql("UPDATE op_user SET user_pass = '$new_hash' WHERE id = $user_id", 'set');
                    }

                    // Save token & last login in op_user table
                    direct_sql("UPDATE op_user SET token = '$auth_token', last_login = NOW(), status = 'ACTIVE' WHERE id = $user_id", 'set');

                    // Clear rate limiting counter upon successful authentication
                    clear_rate_limit('admin_login');

                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);

                    // Initialize token-backed session
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['admin_token'] = $auth_token;
                    $_SESSION['username'] = $u['user_name'];
                    $_SESSION['full_name'] = $u['full_name'] ?: $u['user_name'];
                    $_SESSION['user_type'] = $u['user_type'] ?: 'ADMIN';

                    // Ensure session is written and closed before HTTP redirect
                    session_write_close();

                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Invalid username or password!";
                    record_rate_limit_failure('admin_login', 10, 900);
                }
            }
        } else {
            $error = "Invalid username or password!";
            record_rate_limit_failure('admin_login', 10, 900);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login - <?php echo htmlspecialchars($inst_name); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap-4.1.3.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="login-page">

<div class="login-card">
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 58px; height: 58px; border-radius: 16px; background: linear-gradient(135deg, var(--primary), #38bdf8); color: white; font-size: 24px; box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);">
            <i class="fas fa-balance-scale"></i>
        </div>
        <h3 class="font-weight-bold mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.6rem; color: var(--text-main);">
            Online Law Admin
        </h3>
        <p class="text-muted mb-0" style="font-size: 0.88rem;">Sign in to manage directory registries and data</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 px-3 mb-4 rounded-lg shadow-sm border-0 d-flex align-items-center" style="font-size: 0.88rem; gap: 8px;">
            <i class="fas fa-exclamation-circle"></i>
            <div><?php echo htmlspecialchars($error); ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success py-2 px-3 mb-4 rounded-lg shadow-sm border-0 d-flex align-items-center" style="font-size: 0.88rem; gap: 8px;">
            <i class="fas fa-check-circle"></i>
            <div><?php echo htmlspecialchars($success); ?></div>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <?php echo get_csrf_input('admin_login'); ?>
        <div class="form-group mb-3">
            <label class="font-weight-bold text-muted small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Username</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white text-muted border-right-0" style="border-color: var(--border-soft);"><i class="fas fa-user"></i></span>
                </div>
                <input type="text" name="username" class="form-control border-left-0 pl-0" placeholder="admin" required autofocus style="border-color: var(--border-soft); font-size: 0.92rem;">
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
    
    <div class="text-center mt-4 pt-2 border-top">
        <a href="<?php echo $base_url; ?>index" class="text-muted small text-decoration-none">
            <i class="fas fa-arrow-left mr-1"></i> Back to Public Website
        </a>
    </div>
</div>

</body>
</html>
