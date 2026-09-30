<?php
require_once('include/config.php');
if (!empty($_POST['email'])) {
    $email = hms_post('email');
    $count = (int) hms_scalar($con, 'SELECT COUNT(*) FROM users WHERE email = ?', 's', array($email));
    if ($count > 0) {
        echo "<span style='color:red'> Email already exists.</span>";
        echo "<script>$('#submit').prop('disabled',true);</script>";
    } else {
        echo "<span style='color:green'> Email available for registration.</span>";
        echo "<script>$('#submit').prop('disabled',false);</script>";
    }
}
?>
