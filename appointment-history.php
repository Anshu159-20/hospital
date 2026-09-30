<?php
session_start();
include('include/config.php');
include('include/checklogin.php');
check_login();
hms_require_role('patient', 'logout.php');

$userId = (int) $_SESSION['id'];
$statusExpr = hms_appointment_status_expr($con, 'a');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    hms_verify_csrf();
    $action = hms_post('action');
    $appointmentId = hms_int(hms_post('appointment_id'));
    $appointment = hms_fetch_one($con, 'SELECT * FROM appointment WHERE id = ? AND userId = ?', 'ii', array($appointmentId, $userId));

    if (!$appointment) {
        hms_flash('error', 'Appointment not found.');
        hms_redirect('appointment-history.php');
    }

    if ($action === 'cancel') {
        hms_set_appointment_status($con, $appointmentId, 'Cancelled', 'patient', $userId);
        hms_notify($con, 'admin', null, 'Appointment cancelled', 'Patient cancelled appointment #' . $appointmentId . '.');
        hms_notify($con, 'doctor', (int) $appointment['doctorId'], 'Appointment cancelled', 'Patient cancelled appointment #' . $appointmentId . '.');
        hms_flash('success', 'Appointment cancelled.');
        hms_redirect('appointment-history.php');
    }

    if ($action === 'reschedule') {
        $newDate = hms_post('appointment_date');
        $newTime = hms_normalize_time(hms_post('appointment_time'));
        $reason = '';
        if (!hms_doctor_available($con, (int) $appointment['doctorId'], $newDate, $newTime, $reason, $appointmentId)) {
            hms_flash('error', $reason);
            hms_redirect('appointment-history.php');
        }

        $fields = array('appointmentDate = ?', 'appointmentTime = ?', 'userStatus = 1', 'doctorStatus = 1');
        $types = 'ss';
        $params = array($newDate, $newTime);
        if (hms_column_exists($con, 'appointment', 'status')) {
            $fields[] = 'status = "Pending"';
        }
        if (hms_column_exists($con, 'appointment', 'reschedule_reason')) {
            $fields[] = 'reschedule_reason = ?';
            $types .= 's';
            $params[] = hms_post('reason');
        }
        $types .= 'i';
        $params[] = $appointmentId;
        hms_execute($con, 'UPDATE appointment SET ' . implode(', ', $fields) . ' WHERE id = ?', $types, $params);
        hms_log_audit($con, 'patient', $userId, 'appointment_rescheduled', 'appointment', $appointmentId, 'Rescheduled to ' . $newDate . ' ' . $newTime);
        hms_notify($con, 'admin', null, 'Appointment rescheduled', 'Patient requested a new time for appointment #' . $appointmentId . '.');
        hms_notify($con, 'doctor', (int) $appointment['doctorId'], 'Appointment rescheduled', 'Appointment #' . $appointmentId . ' was moved to ' . $newDate . ' ' . $newTime . '.');
        hms_flash('success', 'Appointment rescheduled and moved back to pending review.');
        hms_redirect('appointment-history.php');
    }
}

$appointments = hms_fetch_all(
    $con,
    'SELECT a.id, a.doctorSpecialization, a.consultancyFees, a.appointmentDate, a.appointmentTime, a.postingDate, ' . $statusExpr . ' AS status_name, d.doctorName, d.id AS doctor_id
     FROM appointment a
     JOIN doctors d ON d.id = a.doctorId
     WHERE a.userId = ?
     ORDER BY a.appointmentDate DESC, a.id DESC',
    'i',
    array($userId)
);

$prescriptionByAppointment = array();
if ($appointments && hms_table_exists($con, 'prescriptions')) {
    $ids = array_map('intval', array_column($appointments, 'id'));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $prescriptions = hms_fetch_all(
        $con,
        'SELECT id, appointment_id FROM prescriptions WHERE patient_user_id = ? AND appointment_id IN (' . $placeholders . ')',
        'i' . str_repeat('i', count($ids)),
        array_merge(array($userId), $ids)
    );
    foreach ($prescriptions as $prescription) {
        $prescriptionByAppointment[(int) $prescription['appointment_id']] = (int) $prescription['id'];
    }
}

hms_layout_header('My Appointments', 'patient', 'appointments');
?>
<div class="hms-page-title">
    <div>
        <h1>My Appointments</h1>
        <div class="text-muted">Track status, reschedule visits, cancel when needed, and download prescriptions.</div>
    </div>
    <a class="btn btn-primary" href="book-appointment.php"><i class="bi bi-calendar-plus me-1"></i>Book Appointment</a>
</div>

<div class="hms-card p-3">
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Appointment History</h2>
        <input class="form-control form-control-sm" style="max-width:260px" placeholder="Search appointments" data-table-search="#appointmentTable">
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="appointmentTable" data-sortable>
            <thead>
            <tr>
                <th data-sort>#</th>
                <th data-sort>Doctor</th>
                <th data-sort>Date / Time</th>
                <th data-sort>Status</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($appointments as $appointment) {
                $isClosed = in_array($appointment['status_name'], array('Cancelled', 'Completed'), true);
                ?>
                <tr>
                    <td><?php echo hms_e($appointment['id']); ?></td>
                    <td>
                        <div class="fw-semibold"><?php echo hms_e($appointment['doctorName']); ?></div>
                        <div class="small text-muted"><?php echo hms_e($appointment['doctorSpecialization']); ?> | Fee: <?php echo hms_e($appointment['consultancyFees']); ?></div>
                    </td>
                    <td><?php echo hms_e($appointment['appointmentDate']); ?> <?php echo hms_e($appointment['appointmentTime']); ?></td>
                    <td><?php echo hms_status_badge($appointment['status_name']); ?></td>
                    <td class="text-end">
                        <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
                            <?php if (isset($prescriptionByAppointment[(int) $appointment['id']])) { ?>
                                <a class="btn btn-sm btn-outline-success" href="prescription-download.php?id=<?php echo hms_e($prescriptionByAppointment[(int) $appointment['id']]); ?>"><i class="bi bi-download"></i></a>
                            <?php } ?>
                            <?php if (!$isClosed) { ?>
                                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#reschedule<?php echo hms_e($appointment['id']); ?>">Reschedule</button>
                                <form method="post" class="d-inline" onsubmit="return confirm('Cancel this appointment?');">
                                    <?php echo hms_csrf_field(); ?>
                                    <input type="hidden" name="action" value="cancel">
                                    <input type="hidden" name="appointment_id" value="<?php echo hms_e($appointment['id']); ?>">
                                    <button class="btn btn-sm btn-outline-secondary" type="submit">Cancel</button>
                                </form>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            <?php if (!$appointments) { ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No appointments found.</td></tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php foreach ($appointments as $appointment) {
    if (in_array($appointment['status_name'], array('Cancelled', 'Completed'), true)) {
        continue;
    }
    ?>
    <div class="modal fade" id="reschedule<?php echo hms_e($appointment['id']); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Reschedule Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo hms_csrf_field(); ?>
                    <input type="hidden" name="action" value="reschedule">
                    <input type="hidden" name="appointment_id" value="<?php echo hms_e($appointment['id']); ?>">
                    <div class="mb-3">
                        <label class="form-label">Doctor Availability</label>
                        <div class="form-text"><?php echo hms_e(hms_availability_summary($con, (int) $appointment['doctor_id'])); ?></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">New Date</label>
                            <input class="form-control" type="date" name="appointment_date" value="<?php echo hms_e($appointment['appointmentDate']); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">New Time</label>
                            <input class="form-control" type="time" name="appointment_time" value="<?php echo hms_e(substr($appointment['appointmentTime'], 0, 5)); ?>" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" name="reason" rows="3" maxlength="255"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
<?php } ?>
<?php hms_layout_footer(); ?>
