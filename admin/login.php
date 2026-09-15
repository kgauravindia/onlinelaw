<?php
require_once('../includes/config.php');
require_once('../includes/function.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Check credentials against op_user table or fallback
    $check = direct_sql("SELECT * FROM op_user WHERE user_name = '" . mysqli_real_escape_string($con, $username) . "'");
    if (isset($check['data'][0])) {
        $u = $check['data'][0];
        $stored_pass = $u['user_pass'] ?? '';
        if (password_verify($password, $stored_pass) || md5($password) === $stored_pass || $password === $stored_pass) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['username'] = $u['user_name'] ?? $username;
            $_SESSION['full_name'] = $u['full_name'] ?? $username;
            $_SESSION['user_type'] = $u['user_type'] ?? 'ADMIN';
            header("Location: index.php");
            exit();
        }
    }
    
    // Fallback default admin
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = 'admin';
        $_SESSION['full_name'] = 'Administrator';
        $_SESSION['user_type'] = 'ADMIN';
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password!";
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

    <form method="post" action="">
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
