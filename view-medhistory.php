<?php
session_start();
if (!isset($con) || !($con instanceof mysqli)) {
    require_once __DIR__ . '/include/config.php';
}
require_once __DIR__ . '/include/checklogin.php';
check_login();
hms_require_role('patient', 'logout.php');

$userId = (int) $_SESSION['id'];
$viewId = hms_int(hms_get('viewid'));
$patient = hms_fetch_one(
    $con,
    'SELECT p.* FROM tblpatient p JOIN users u ON u.email = p.PatientEmail WHERE p.ID = ? AND u.id = ?',
    'ii',
    array($viewId, $userId)
);

if (!$patient) {
    hms_flash('error', 'Medical history record not found.');
    hms_redirect('manage-medhistory.php');
}

$history = hms_fetch_all(
    $con,
    'SELECT * FROM tblmedicalhistory WHERE PatientID = ? ORDER BY CreationDate DESC, ID DESC',
    'i',
    array($viewId)
);

hms_layout_header('Medical History Details', 'patient', 'history');
?>
<div class="hms-page-title">
    <div>
        <h1>Medical History Details</h1>
        <div class="text-muted">Read-only patient record and visit history.</div>
    </div>
    <a class="btn btn-outline-secondary" href="manage-medhistory.php">Back to Timeline</a>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="hms-card p-3">
            <h2 class="h5 mb-3">Patient Details</h2>
            <div class="mb-2"><span class="text-muted">Name</span><div class="fw-semibold"><?php echo hms_e($patient['PatientName']); ?></div></div>
            <div class="mb-2"><span class="text-muted">Email</span><div><?php echo hms_e($patient['PatientEmail']); ?></div></div>
            <div class="mb-2"><span class="text-muted">Contact</span><div><?php echo hms_e($patient['PatientContno']); ?></div></div>
            <div class="mb-2"><span class="text-muted">Gender / Age</span><div><?php echo hms_e($patient['PatientGender']); ?> / <?php echo hms_e($patient['PatientAge']); ?></div></div>
            <div class="mb-2"><span class="text-muted">Address</span><div><?php echo hms_e($patient['PatientAdd']); ?></div></div>
            <div class="mb-2"><span class="text-muted">Initial History</span><div><?php echo nl2br(hms_e($patient['PatientMedhis'])); ?></div></div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="hms-card p-3">
            <h2 class="h5 mb-3">Visits</h2>
            <div class="timeline">
                <?php foreach ($history as $item) { ?>
                    <div class="timeline-item">
                        <div class="fw-semibold"><?php echo hms_e($item['CreationDate']); ?></div>
                        <div class="small text-muted">BP: <?php echo hms_e($item['BloodPressure']); ?> | Sugar: <?php echo hms_e($item['BloodSugar']); ?> | Weight: <?php echo hms_e($item['Weight']); ?> | Temp: <?php echo hms_e($item['Temperature']); ?></div>
                        <div><?php echo nl2br(hms_e($item['MedicalPres'])); ?></div>
                    </div>
                <?php } ?>
                <?php if (!$history) { ?>
                    <div class="text-muted">No visit history has been recorded yet.</div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php hms_layout_footer(); ?>
