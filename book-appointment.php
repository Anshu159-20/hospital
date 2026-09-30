<?php
session_start();
include('include/config.php');
include('include/checklogin.php');
check_login();
hms_require_role('patient', 'logout.php');

$userId = (int) $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    hms_verify_csrf();
    $doctorId = hms_int(hms_post('doctor'));
    $appDate = hms_post('appdate');
    $appTime = hms_normalize_time(hms_post('apptime'));

    $doctor = hms_fetch_one(
        $con,
        'SELECT id, doctorName, specilization, docFees FROM doctors WHERE id = ?',
        'i',
        array($doctorId)
    );

    $reason = '';
    if (!$doctor) {
        hms_flash('error', 'Please select a valid doctor.');
        hms_redirect('book-appointment.php');
    }
    if (!hms_doctor_available($con, $doctorId, $appDate, $appTime, $reason)) {
        hms_flash('error', $reason);
        hms_redirect('book-appointment.php');
    }

    $fields = array('doctorSpecialization', 'doctorId', 'userId', 'consultancyFees', 'appointmentDate', 'appointmentTime', 'userStatus', 'doctorStatus');
    $placeholders = array('?', '?', '?', '?', '?', '?', '1', '1');
    $types = 'siisss';
    $params = array($doctor['specilization'], $doctorId, $userId, (string) $doctor['docFees'], $appDate, $appTime);
    if (hms_column_exists($con, 'appointment', 'status')) {
        $fields[] = 'status';
        $placeholders[] = '?';
        $types .= 's';
        $params[] = 'Pending';
    }

    hms_execute(
        $con,
        'INSERT INTO appointment(' . implode(',', $fields) . ') VALUES(' . implode(',', $placeholders) . ')',
        $types,
        $params
    );
    $appointmentId = mysqli_insert_id($con);
    hms_log_audit($con, 'patient', $userId, 'appointment_created', 'appointment', $appointmentId, 'Booked with Dr. ' . $doctor['doctorName']);
    hms_notify($con, 'admin', null, 'New appointment request', 'A patient booked appointment #' . $appointmentId . '.');
    hms_notify($con, 'doctor', $doctorId, 'New appointment request', 'A patient booked appointment #' . $appointmentId . '.');
    hms_flash('success', 'Your appointment request was submitted. You can track its status from appointment history.');
    hms_redirect('appointment-history.php');
}

$specialties = hms_fetch_all($con, 'SELECT DISTINCT specilization FROM doctorspecilization ORDER BY specilization ASC');
$doctors = hms_fetch_all($con, 'SELECT id, doctorName, specilization, docFees FROM doctors ORDER BY doctorName ASC');

hms_layout_header('Book Appointment', 'patient', 'book');
?>
<div class="hms-page-title">
    <div>
        <h1>Book Appointment</h1>
        <div class="text-muted">Choose a doctor, review availability, and submit a pending appointment request.</div>
    </div>
    <a class="btn btn-outline-secondary" href="appointment-history.php"><i class="bi bi-calendar-check me-1"></i>My Appointments</a>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="hms-card p-3">
            <form method="post" id="bookingForm">
                <?php echo hms_csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Doctor Specialization</label>
                    <select class="form-select" name="Doctorspecialization" id="specialization" required>
                        <option value="">Select specialization</option>
                        <?php foreach ($specialties as $specialty) { ?>
                            <option value="<?php echo hms_e($specialty['specilization']); ?>"><?php echo hms_e($specialty['specilization']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Doctor</label>
                    <select class="form-select" name="doctor" id="doctor" required>
                        <option value="">Select doctor</option>
                        <?php foreach ($doctors as $doctor) { ?>
                            <option value="<?php echo hms_e($doctor['id']); ?>" data-specialization="<?php echo hms_e($doctor['specilization']); ?>" data-fee="<?php echo hms_e($doctor['docFees']); ?>" data-availability="<?php echo hms_e(hms_availability_summary($con, (int) $doctor['id'])); ?>">
                                <?php echo hms_e($doctor['doctorName']); ?>
                            </option>
                        <?php } ?>
                    </select>
                    <div class="form-text" id="availabilityText">Select a doctor to view availability.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Consultancy Fee</label>
                    <input class="form-control" id="fees" value="" readonly>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label">Appointment Date</label>
                        <input class="form-control" type="date" name="appdate" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Appointment Time</label>
                        <input class="form-control" type="time" name="apptime" required>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a class="btn btn-light" href="dashboard.php">Cancel</a>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-send me-1"></i>Submit Request</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="hms-card p-3 h-100">
            <h2 class="h5">Before Booking</h2>
            <div class="text-muted mb-3">Availability is checked again when you submit, so another patient cannot take the same slot silently.</div>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="fw-semibold">Pending</div>
                    <div class="small text-muted">New requests start here.</div>
                </div>
                <div class="timeline-item">
                    <div class="fw-semibold">Approved</div>
                    <div class="small text-muted">Admin or doctor confirms the visit.</div>
                </div>
                <div class="timeline-item">
                    <div class="fw-semibold">Completed</div>
                    <div class="small text-muted">Doctor adds diagnosis and prescription.</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$script = '<script>
var specialization = document.getElementById("specialization");
var doctor = document.getElementById("doctor");
var fee = document.getElementById("fees");
var availability = document.getElementById("availabilityText");
var allOptions = Array.from(doctor.querySelectorAll("option")).slice(1);

function filterDoctors() {
    var selected = specialization.value;
    doctor.value = "";
    fee.value = "";
    availability.textContent = "Select a doctor to view availability.";
    allOptions.forEach(function(option) {
        option.hidden = selected && option.dataset.specialization !== selected;
    });
}
function updateDoctorMeta() {
    var option = doctor.options[doctor.selectedIndex];
    fee.value = option && option.dataset.fee ? option.dataset.fee : "";
    availability.textContent = option && option.dataset.availability ? option.dataset.availability : "Select a doctor to view availability.";
}
specialization.addEventListener("change", filterDoctors);
doctor.addEventListener("change", updateDoctorMeta);
</script>';
hms_layout_footer($script);
?>
