<?php
session_start();
include 'include/config.php';

if (empty($_SESSION['name']) || empty($_SESSION['email'])) {
    hms_flash('danger', 'Please verify your registered details first.');
    hms_redirect('forgot-password.php');
}

if (isset($_POST['change'])) {
    hms_verify_csrf();
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
    $confirmPassword = isset($_POST['password_again']) ? (string) $_POST['password_again'] : '';

    if ($password === '' || $password !== $confirmPassword) {
        hms_flash('danger', 'Password and confirm password must match.');
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $updated = hms_execute(
            $con,
            'UPDATE users SET password = ? WHERE fullName = ? AND email = ?',
            'sss',
            array($hash, $_SESSION['name'], $_SESSION['email'])
        );
        unset($_SESSION['name'], $_SESSION['email']);
        hms_flash($updated !== false ? 'success' : 'danger', $updated !== false ? 'Password updated successfully. Please log in.' : 'Unable to update password.');
        hms_redirect('user-login.php');
    }
}

hms_auth_header('Reset Patient Password', 'Set New Password', 'Choose a new password for your patient account.', '../index.php');
?>
<form method="post" class="mt-4">
    <?php echo hms_csrf_field(); ?>
    <div class="mb-3">
        <label class="form-label" for="password">New password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" minlength="6" required>
        </div>
    </div>
    <div class="mb-4">
        <label class="form-label" for="password_again">Confirm password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" class="form-control" id="password_again" name="password_again" minlength="6" required>
        </div>
    </div>
    <button type="submit" class="btn btn-primary w-100" name="change">
        <i class="bi bi-check2-circle me-1"></i>Update Password
    </button>
</form>
<?php hms_auth_footer(); ?>
