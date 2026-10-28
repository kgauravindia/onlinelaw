<?php
require_once(__DIR__ . '/../../includes/config.php');
require_once(__DIR__ . '/../../includes/security.php');
require_once(__DIR__ . '/../../includes/function.php');

// Admin Token Authentication Check against op_user table
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page != 'login.php') {
    $current_admin = check_admin_auth(true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Dashboard - <?php echo htmlspecialchars($inst_name); ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="../css/bootstrap-4.1.3.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="../js/jquery-3.3.1.min.js"></script>
    <?php if ($current_page == 'index.php'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <?php endif; ?>
</head>
<body class="<?php echo ($current_page == 'login.php') ? 'login-page' : 'admin-dashboard'; ?>">
<?php if ($current_page != 'login.php'): ?>
<div class="admin-layout" id="adminLayout">
    <!-- Sidebar -->
    <aside class="sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-brand">
                <div class="sidebar-brand-badge">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <span>Online Law</span>
            </a>
            <button class="sidebar-toggle-btn d-none d-lg-flex" id="sidebarCollapseBtn" title="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <button class="btn-close-custom d-lg-none" id="sidebarCloseBtn" aria-label="Close Sidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <nav class="flex-grow-1">
            <div class="sidebar-nav-section">Main Menu</div>
            <a href="index.php" class="nav-link-custom <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" title="Dashboard Overview">
                <i class="fas fa-home"></i> <span>Dashboard</span>
            </a>
            
            <div class="sidebar-nav-section mt-3">MCA Registry</div>
            <a href="manage_companies.php" class="nav-link-custom <?php echo in_array($current_page, ['manage_companies.php', 'edit_company.php']) ? 'active' : ''; ?>" title="Companies Incorporated (MCA)">
                <i class="fas fa-building"></i> <span>MCA Companies</span>
            </a>
            <a href="upload_companies.php" class="nav-link-custom <?php echo ($current_page == 'upload_companies.php') ? 'active' : ''; ?>" title="Batch Upload MCA Excel">
                <i class="fas fa-file-excel"></i> <span>Upload MCA Excel</span>
            </a>

            <div class="sidebar-nav-section mt-3">Directories &amp; Data</div>
            <a href="manage_bank.php" class="nav-link-custom <?php echo in_array($current_page, ['manage_bank.php', 'edit_bank.php']) ? 'active' : ''; ?>" title="Bank IFSC & MICR Directory">
                <i class="fas fa-university"></i> <span>Bank IFSC &amp; MICR</span>
            </a>
            <a href="manage_pincode.php" class="nav-link-custom <?php echo in_array($current_page, ['manage_pincode.php', 'edit_pincode.php']) ? 'active' : ''; ?>" title="Postal PIN Codes Directory">
                <i class="fas fa-map-pin"></i> <span>PIN Codes</span>
            </a>
            <a href="manage_acts.php" class="nav-link-custom <?php echo in_array($current_page, ['manage_acts.php', 'edit_act.php']) ? 'active' : ''; ?>" title="Bare Acts Registry">
                <i class="fas fa-book"></i> <span>Bare Acts</span>
            </a>
            <a href="manage_data.php" class="nav-link-custom <?php echo ($current_page == 'manage_data.php') ? 'active' : ''; ?>" title="Master Database Tables">
                <i class="fas fa-database"></i> <span>Master Data Hub</span>
            </a>

            <div class="sidebar-nav-section mt-3">Helpdesk &amp; Inquiries</div>
            <?php
                $now = time();
                if (!isset($_SESSION['unread_contact_count']) || !isset($_SESSION['unread_contact_time']) || ($now - $_SESSION['unread_contact_time'] > 60)) {
                    $unread_contact_res = direct_sql("SELECT COUNT(*) as total FROM contact WHERE status = 'NEW' OR status = '' OR status IS NULL");
                    $_SESSION['unread_contact_count'] = isset($unread_contact_res['data'][0]['total']) ? (int)$unread_contact_res['data'][0]['total'] : 0;
                    $_SESSION['unread_contact_time'] = $now;
                }
                $unread_contact_count = $_SESSION['unread_contact_count'];
            ?>
            <a href="manage_contact.php" class="nav-link-custom <?php echo ($current_page == 'manage_contact.php') ? 'active' : ''; ?>" title="User Inquiries &amp; Delisting Complaints">
                <i class="fas fa-envelope-open-text"></i> <span>Inquiries &amp; Complaints</span>
                <?php if ($unread_contact_count > 0): ?>
                <span class="badge bg-danger text-white ml-auto" style="font-size: 10px; border-radius: 10px; padding: 2px 7px;"><?php echo $unread_contact_count; ?></span>
                <?php endif; ?>
            </a>

            <div class="sidebar-nav-section mt-3">Integrations &amp; APIs</div>
            <a href="manage_api_keys.php" class="nav-link-custom <?php echo in_array($current_page, ['manage_api_keys.php', 'edit_api_key.php']) ? 'active' : ''; ?>" title="API Access Control &amp; Key Manager">
                <i class="fas fa-key"></i> <span>API Access Control</span>
            </a>

            <div class="sidebar-nav-section mt-3">Administration &amp; SEO</div>
            <a href="manage_sitemap.php" class="nav-link-custom <?php echo ($current_page == 'manage_sitemap.php') ? 'active' : ''; ?>" title="Sitemap & SEO Engine">
                <i class="fas fa-sitemap"></i> <span>Sitemap Manager</span>
            </a>
            <a href="manage_settings.php" class="nav-link-custom <?php echo ($current_page == 'manage_settings.php') ? 'active' : ''; ?>" title="Site Settings &amp; Analytics">
                <i class="fas fa-sliders-h"></i> <span>Site Settings &amp; Analytics</span>
            </a>
            <a href="manage_users.php" class="nav-link-custom <?php echo in_array($current_page, ['manage_users.php', 'edit_user.php']) ? 'active' : ''; ?>" title="Platform Users">
                <i class="fas fa-users"></i> <span>Users</span>
            </a>
        </nav>
        
        <div class="mt-auto px-2">
            <a href="logout.php" class="nav-link-custom text-danger" title="Sign Out">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </aside>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Main Content Wrapper -->
    <main class="main-content">
        <!-- Top Bar Header -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="btn btn-light d-lg-none rounded-circle border-0 shadow-sm" id="mobileHamburger" onclick="toggleMobileSidebar()" style="width: 38px; height: 38px; background: #f1f5f9;">
                    <i class="fas fa-bars text-primary"></i>
                </button>
                <div class="d-none d-lg-block">
                    <h5 class="mb-0" style="font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 1.05rem; color: var(--text-main);">
                        Welcome back, <span class="text-primary"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Administrator'); ?></span>
                    </h5>
                </div>
            </div>
            
            <div class="top-bar-right">
                <a href="../index.php" class="top-bar-link" title="Visit Public Website" target="_blank">
                    <i class="fas fa-globe"></i>
                </a>
                
                <div class="admin-profile-dropdown">
                    <div class="admin-avatar">
                        <?php echo strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)); ?>
                    </div>
                    <div class="admin-info d-none d-md-flex">
                        <span class="admin-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Administrator'); ?></span>
                        <span class="admin-role">Super Admin</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-body">
<?php endif; ?>
