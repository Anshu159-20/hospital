<?php
session_start();
$con = null;
include('include/config.php');
$_SESSION['login']=="";
date_default_timezone_set('Asia/Kolkata');
$ldate=date( 'd-m-Y h:i:s A', time () );
if (!empty($_SESSION['id'])) {
	hms_execute($con,"UPDATE userlog SET logout = ? WHERE uid = ? ORDER BY id DESC LIMIT 1", 'si', array($ldate, (int) $_SESSION['id']));
}
session_unset();
//session_destroy();
$_SESSION['errmsg']="You have successfully logout";
?>
<script language="javascript">
document.location="../index.php";
</script>
