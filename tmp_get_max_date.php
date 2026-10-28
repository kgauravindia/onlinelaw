<?php
require_once('includes/config.php');
require_once('includes/function.php');

// Restrict execution to CLI or authenticated admins only
if (php_sapi_name() !== 'cli') {
    check_admin_auth(true);
}

$res = direct_sql("SELECT MAX(updated_at) as last_up FROM hsnsac");
echo $res['data'][0]['last_up'];
?>