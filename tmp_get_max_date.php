<?php
require_once('includes/config.php');
require_once('includes/function.php');
$res = direct_sql("SELECT MAX(updated_at) as last_up FROM hsnsac");
echo $res['data'][0]['last_up'];
?>
