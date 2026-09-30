<?php
session_start();
include 'include/config.php';
hms_require_role('patient', 'logout.php');

$patientId = (int) $_SESSION['id'];

if (isset($_POST['submit'])) {
    hms_verify_csrf();
    $name = hms_post('fname');
    $address = hms_post('address');
    $city = hms_post('city');
    $gender = hms_post('gender');

    if ($name === '' || $city === '' || $gender === '') {
        hms_flash('danger', 'Please complete the required profile fields.');
    } else {
        $updated = hms_execute(
            $con,
            'UPDATE users SET fullName = ?, address = ?, city = ?, gender = ?, updationDate = NOW() WHERE id = ?',
            'ssssi',
            array($name, $address, $city, $gender, $patientId)
        );
        hms_flash($updated !== false ? 'success' : 'danger', $updated !== false ? 'Profile updated successfully.' : 'Unable to update profile.');
        hms_redirect('edit-profile.php');
    }
}

$profile = hms_fetch_one($con, 'SELECT * FROM users WHERE id = ?', 'i', array($patientId));
hms_layout_header('Edit Profile', 'patient', 'profile');
?>
<div class="hms-page-title">
    <div>
        <h1>Edit Profile</h1>
        <p class="text-muted mb-0">Keep your patient contact and address details current.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="hms-card p-4">
            <form method="post">
                <?php echo hms_csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="fname">Full name</label>
                        <input type="text" class="form-control" id="fname" name="fname" value="<?php echo hms_e($profile['fullName'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="gender">Gender</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <?php foreach (array('male' => 'Male', 'female' => 'Female', 'other' => 'Other') as $value => $label) { ?>
                                <option value="<?php echo hms_e($value); ?>" <?php echo ($profile['gender'] ?? '') === $value ? 'selected' : ''; ?>><?php echo hms_e($label); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?php echo hms_e($profile['city'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" value="<?php echo hms_e($profile['email'] ?? ''); ?>" readonly>
                        <div class="form-text"><a href="change-emaild.php">Update email address</a></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="4"><?php echo hms_e($profile['address'] ?? ''); ?></textarea>
                    </div>
                </div>
                <button type="submit" name="submit" class="btn btn-primary mt-4">
                    <i class="bi bi-save me-1"></i>Save Profile
                </button>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="hms-card p-4">
            <h2 class="h5">Profile Activity</h2>
            <dl class="mb-0">
                <dt class="text-muted small mt-3">Registered</dt>
                <dd><?php echo hms_e($profile['regDate'] ?? 'Not available'); ?></dd>
                <dt class="text-muted small mt-3">Last updated</dt>
                <dd><?php echo hms_e(!empty($profile['updationDate']) ? $profile['updationDate'] : 'Not updated yet'); ?></dd>
            </dl>
        </div>
    </div>
</div>
<?php hms_layout_footer(); ?>
