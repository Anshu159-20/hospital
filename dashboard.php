<?php
session_start();
include('include/config.php');
include('include/checklogin.php');
check_login();
hms_require_role('patient', 'logout.php');

$userId = (int) $_SESSION['id'];
$statusExpr = hms_appointment_status_expr($con, 'a');
$stats = array(
    'appointments' => (int) hms_scalar($con, 'SELECT COUNT(*) FROM appointment WHERE userId = ?', 'i', array($userId)),
    'pending' => (int) hms_scalar($con, 'SELECT COUNT(*) FROM appointment a WHERE a.userId = ? AND ' . $statusExpr . ' = "Pending"', 'i', array($userId)),
    'completed' => (int) hms_scalar($con, 'SELECT COUNT(*) FROM appointment a WHERE a.userId = ? AND ' . $statusExpr . ' = "Completed"', 'i', array($userId)),
    'history' => (int) hms_scalar($con, 'SELECT COUNT(*) FROM tblpatient p JOIN users u ON u.email = p.PatientEmail WHERE u.id = ?', 'i', array($userId))
);
$stats['prescriptions'] = hms_table_exists($con, 'prescriptions')
    ? (int) hms_scalar($con, 'SELECT COUNT(*) FROM prescriptions WHERE patient_user_id = ?', 'i', array($userId))
    : 0;

$nextAppointment = hms_fetch_one(
    $con,
    'SELECT a.id, a.appointmentDate, a.appointmentTime, a.doctorSpecialization, ' . $statusExpr . ' AS status_name, d.doctorName
     FROM appointment a
     JOIN doctors d ON d.id = a.doctorId
     WHERE a.userId = ? AND a.appointmentDate >= CURDATE() AND ' . $statusExpr . ' <> "Cancelled"
     ORDER BY a.appointmentDate ASC, a.appointmentTime ASC
     LIMIT 1',
    'i',
    array($userId)
);

$recentAppointments = hms_fetch_all(
    $con,
    'SELECT a.id, a.appointmentDate, a.appointmentTime, a.doctorSpecialization, a.consultancyFees, ' . $statusExpr . ' AS status_name, d.doctorName
     FROM appointment a
     JOIN doctors d ON d.id = a.doctorId
     WHERE a.userId = ?
     ORDER BY a.id DESC
     LIMIT 5',
    'i',
    array($userId)
);

hms_layout_header('Patient Dashboard', 'patient', 'dashboard');
?>
<div class="hms-page-title">
    <div>
        <h1>Patient Dashboard</h1>
        <div class="text-muted">Track appointments, prescriptions, and medical history.</div>
    </div>
    <a class="btn btn-primary" href="book-appointment.php"><i class="bi bi-calendar-plus me-1"></i>Book Appointment</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <a class="hms-card hms-stat d-block text-reset" href="appointment-history.php">
            <span class="icon"><i class="bi bi-calendar-check"></i></span>
            <div class="value"><?php echo hms_e($stats['appointments']); ?></div>
            <div class="label">Total Appointments</div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="hms-card hms-stat">
            <span class="icon"><i class="bi bi-hourglass-split"></i></span>
            <div class="value"><?php echo hms_e($stats['pending']); ?></div>
            <div class="label">Pending</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="hms-card hms-stat">
            <span class="icon"><i class="bi bi-check2-circle"></i></span>
            <div class="value"><?php echo hms_e($stats['completed']); ?></div>
            <div class="label">Completed</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a class="hms-card hms-stat d-block text-reset" href="manage-medhistory.php">
            <span class="icon"><i class="bi bi-journal-medical"></i></span>
            <div class="value"><?php echo hms_e($stats['history']); ?></div>
            <div class="label">Medical Records</div>
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="hms-card p-3 h-100">
            <h2 class="h5 mb-3">Next Appointment</h2>
            <?php if ($nextAppointment) { ?>
                <div class="d-flex align-items-start gap-3">
                    <div class="hms-stat p-0" style="min-height:auto">
                        <span class="icon"><i class="bi bi-calendar-event"></i></span>
                    </div>
                    <div>
                        <div class="fw-semibold"><?php echo hms_e($nextAppointment['doctorName']); ?></div>
                        <div class="text-muted"><?php echo hms_e($nextAppointment['doctorSpecialization']); ?></div>
                        <div class="mt-2"><?php echo hms_e($nextAppointment['appointmentDate']); ?> at <?php echo hms_e($nextAppointment['appointmentTime']); ?></div>
                        <div class="mt-2"><?php echo hms_status_badge($nextAppointment['status_name']); ?></div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="text-muted">No upcoming appointment. You can book one when you are ready.</div>
            <?php } ?>
            <div class="mt-4 d-grid gap-2">
                <a class="btn btn-outline-primary" href="appointment-history.php">Track Status</a>
                <a class="btn btn-outline-secondary" href="manage-medhistory.php">Medical History Timeline</a>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="hms-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Recent Appointments</h2>
                <span class="badge text-bg-light"><?php echo hms_e($stats['prescriptions']); ?> prescriptions</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" data-sortable>
                    <thead>
                    <tr>
                        <th data-sort>Doctor</th>
                        <th data-sort>Date</th>
                        <th data-sort>Status</th>
                        <th class="text-end">Fee</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentAppointments as $appointment) { ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?php echo hms_e($appointment['doctorName']); ?></div>
                                <div class="small text-muted"><?php echo hms_e($appointment['doctorSpecialization']); ?></div>
                            </td>
                            <td><?php echo hms_e($appointment['appointmentDate']); ?> <?php echo hms_e($appointment['appointmentTime']); ?></td>
                            <td><?php echo hms_status_badge($appointment['status_name']); ?></td>
                            <td class="text-end"><?php echo hms_e($appointment['consultancyFees']); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (!$recentAppointments) { ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No appointments found.</td></tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php hms_layout_footer(); ?>
