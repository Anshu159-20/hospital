<?php
session_start();
include('include/config.php');
include('include/checklogin.php');
check_login();
hms_require_role('patient', 'logout.php');

if (!hms_table_exists($con, 'prescriptions')) {
    hms_flash('error', 'Prescription downloads are not available until the SQL migration is applied.');
    hms_redirect('appointment-history.php');
}

$userId = (int) $_SESSION['id'];
$id = hms_int(hms_get('id'));
$prescription = hms_fetch_one(
    $con,
    'SELECT p.*, u.fullName, u.email, d.doctorName, d.specilization
     FROM prescriptions p
     JOIN users u ON u.id = p.patient_user_id
     JOIN doctors d ON d.id = p.doctor_id
     WHERE p.id = ? AND p.patient_user_id = ?',
    'ii',
    array($id, $userId)
);

if (!$prescription) {
    hms_flash('error', 'Prescription not found.');
    hms_redirect('appointment-history.php');
}

$lines = array(
    'Patient: ' . $prescription['fullName'] . ' (' . $prescription['email'] . ')',
    'Doctor: Dr. ' . $prescription['doctorName'] . ' - ' . $prescription['specilization'],
    'Appointment ID: ' . $prescription['appointment_id'],
    'Created: ' . $prescription['created_at'],
    '',
    'Diagnosis:',
    $prescription['diagnosis'],
    '',
    'Treatment:',
    $prescription['treatment'],
    '',
    'Medicines:',
    $prescription['medicines'],
    '',
    'Notes:',
    $prescription['notes'],
    '',
    'Follow-up Date: ' . ($prescription['followup_date'] ?: 'Not set')
);
hms_send_basic_pdf('prescription-' . $id . '.pdf', 'Hospital Management System - Prescription', $lines);
?>
