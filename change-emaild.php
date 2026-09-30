<?php
session_start();
include 'include/config.php';
hms_require_role('patient', 'logout.php');

$patientId = (int) $_SESSION['id'];
$profile = hms_fetch_one($con, 'SELECT email FROM users WHERE id = ?', 'i', array($patientId));

if (isset($_POST['submit'])) {
    hms_verify_csrf();
    $email = hms_post('email');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        hms_flash('danger', 'Please enter a valid email address.');
    } elseif ((int) hms_scalar($con, 'SELECT COUNT(*) FROM users WHERE email = ? AND id <> ?', 'si', array($email, $patientId)) > 0) {
        hms_flash('danger', 'This email is already used by another account.');
    } else {
        $updated = hms_execute($con, 'UPDATE users SET email = ?, updationDate = NOW() WHERE id = ?', 'si', array($email, $patientId));
        if ($updated !== false) {
            $_SESSION['login'] = $email;
        }
        hms_flash($updated !== false ? 'success' : 'danger', $updated !== false ? 'Email updated successfully.' : 'Unable to update email.');
        hms_redirect('edit-profile.php');
    }
}

hms_layout_header('Update Email', 'patient', 'profile');
?>
<div class="hms-page-title">
    <div>
        <h1>Update Email</h1>
        <p class="text-muted mb-0">This email is used for patient login and appointment communication.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 col-xl-6">
        <div class="hms-card p-4">
            <form method="post">
                <?php echo hms_csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label" for="current_email">Current email</label>
                    <input type="email" class="form-control" id="current_email" value="<?php echo hms_e($profile['email'] ?? ''); ?>" readonly>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="email">New email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="bi bi-envelope-check me-1"></i>Update Email
                </button>
                <a class="btn btn-light ms-2" href="edit-profile.php">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php hms_layout_footer(); ?>
