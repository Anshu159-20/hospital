<?php
session_start();
if (!isset($con) || !($con instanceof mysqli)) {
    require_once __DIR__ . '/include/config.php';
}

if (isset($_POST['submit'])) {
    hms_verify_csrf();
    $email = hms_post('username');
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
    $user = hms_fetch_one($con, 'SELECT * FROM users WHERE email = ?', 's', array($email));
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

    if ($user && hms_password_matches($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['login'] = $email;
        $_SESSION['id'] = (int) $user['id'];
        $_SESSION['role'] = 'patient';

        if (hms_password_needs_upgrade($user['password'])) {
            hms_upgrade_password($con, 'users', 'id', (int) $user['id'], $password);
        }

        hms_execute($con, 'INSERT INTO userlog(uid, username, userip, status) VALUES(?, ?, ?, ?)', 'issi', array((int) $user['id'], $email, $ip, 1));
        hms_redirect('dashboard.php');
    }

    hms_execute($con, 'INSERT INTO userlog(username, userip, status) VALUES(?, ?, ?)', 'ssi', array($email, $ip, 0));
    hms_flash('danger', 'Invalid email or password.');
    hms_redirect('user-login.php');
}

hms_auth_header('Patient Login', 'Patient Login', 'Access appointments, prescriptions, and your medical history.', '../index.php');
?>
<form method="post" class="mt-4">
    <?php echo hms_csrf_field(); ?>
    <div class="mb-3">
        <label class="form-label" for="username">Email address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="username" name="username" autocomplete="email" required>
        </div>
    </div>
    <div class="mb-3">
        <div class="d-flex justify-content-between gap-3">
            <label class="form-label" for="password">Password</label>
            <a class="small" href="forgot-password.php">Forgot password?</a>
        </div>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
        </div>
    </div>
    <button type="submit" class="btn btn-primary w-100" name="submit">
        <i class="bi bi-box-arrow-in-right me-1"></i>Log In
    </button>
    <p class="text-center auth-muted mt-4 mb-0">
        New patient? <a href="registration.php">Create an account</a>
    </p>
</form>
<?php hms_auth_footer(); ?>
