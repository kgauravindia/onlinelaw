<?php
require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../includes/function.php');

if (!empty($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    // Invalidate the token in op_user table
    direct_sql("UPDATE op_user SET token = '', status = 'LOGOUT' WHERE id = $uid", 'set');
}

$_SESSION = [];
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

header("Location: login.php?msg=logged_out");
exit();
?>
