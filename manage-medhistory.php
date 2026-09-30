<?php
session_start();
include('include/config.php');
include('include/checklogin.php');
check_login();
hms_require_role('patient', 'logout.php');

$userId = (int) $_SESSION['id'];
$user = hms_fetch_one($con, 'SELECT fullName, email FROM users WHERE id = ?', 'i', array($userId));
$records = $user
    ? hms_fetch_all(
        $con,
        'SELECT * FROM tblpatient WHERE PatientEmail = ? ORDER BY ID DESC',
        's',
        array($user['email'])
    )
    : array();

$timeline = array();
foreach ($records as $record) {
    $timeline[] = array(
        'date' => $record['CreationDate'],
        'title' => 'Patient record created',
        'body' => $record['PatientMedhis'],
        'meta' => $record['PatientName'] . ' | ' . $record['PatientGender'] . ' | Age ' . $record['PatientAge'],
        'download' => ''
    );
    $historyRows = hms_fetch_all(
        $con,
        'SELECT * FROM tblmedicalhistory WHERE PatientID = ? ORDER BY ID DESC',
        'i',
        array((int) $record['ID'])
    );
    foreach ($historyRows as $history) {
        $timeline[] = array(
            'date' => $history['CreationDate'],
            'title' => 'Medical visit',
            'body' => $history['MedicalPres'],
            'meta' => 'BP: ' . $history['BloodPressure'] . ' | Sugar: ' . $history['BloodSugar'] . ' | Weight: ' . $history['Weight'] . ' | Temp: ' . $history['Temperature'],
            'download' => ''
        );
    }
}

if (hms_table_exists($con, 'prescriptions')) {
    $prescriptions = hms_fetch_all(
        $con,
        'SELECT p.*, d.doctorName, d.specilization
         FROM prescriptions p
         JOIN doctors d ON d.id = p.doctor_id
         WHERE p.patient_user_id = ?
         ORDER BY p.created_at DESC',
        'i',
        array($userId)
    );
    foreach ($prescriptions as $prescription) {
        $timeline[] = array(
            'date' => $prescription['created_at'],
            'title' => 'Prescription',
            'body' => $prescription['diagnosis'] . "\n" . $prescription['treatment'],
            'meta' => 'Dr. ' . $prescription['doctorName'] . ' | ' . $prescription['specilization'] . ' | Follow-up: ' . ($prescription['followup_date'] ?: 'Not set'),
            'download' => 'prescription-download.php?id=' . (int) $prescription['id']
        );
    }
}

usort($timeline, function ($a, $b) {
    return strcmp((string) $b['date'], (string) $a['date']);
});

hms_layout_header('Medical History', 'patient', 'history');
?>
<div class="hms-page-title">
    <div>
        <h1>Medical History</h1>
        <div class="text-muted">Timeline of patient records, visits, and prescriptions.</div>
    </div>
    <a class="btn btn-outline-primary" href="appointment-history.php"><i class="bi bi-calendar-check me-1"></i>Appointments</a>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="hms-card p-3">
            <h2 class="h5 mb-3">Profile Summary</h2>
            <div class="mb-2"><span class="text-muted">Name</span><div class="fw-semibold"><?php echo hms_e($user['fullName'] ?? ''); ?></div></div>
            <div class="mb-2"><span class="text-muted">Email</span><div class="fw-semibold"><?php echo hms_e($user['email'] ?? ''); ?></div></div>
            <div class="mb-2"><span class="text-muted">Linked patient records</span><div class="fw-semibold"><?php echo hms_e(count($records)); ?></div></div>
            <a class="btn btn-primary w-100 mt-3" href="edit-profile.php">Update Profile</a>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="hms-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Timeline</h2>
                <input class="form-control form-control-sm" style="max-width:260px" placeholder="Search timeline" data-table-search="#timelineTable">
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="timelineTable" data-sortable>
                    <thead>
                    <tr>
                        <th data-sort>Date</th>
                        <th data-sort>Record</th>
                        <th data-sort>Details</th>
                        <th class="text-end">File</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($timeline as $item) { ?>
                        <tr>
                            <td><?php echo hms_e($item['date']); ?></td>
                            <td>
                                <div class="fw-semibold"><?php echo hms_e($item['title']); ?></div>
                                <div class="small text-muted"><?php echo hms_e($item['meta']); ?></div>
                            </td>
                            <td><?php echo nl2br(hms_e($item['body'])); ?></td>
                            <td class="text-end">
                                <?php if ($item['download']) { ?>
                                    <a class="btn btn-sm btn-outline-success" href="<?php echo hms_e($item['download']); ?>"><i class="bi bi-download"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!$timeline) { ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No medical history records found yet.</td></tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php hms_layout_footer(); ?>
