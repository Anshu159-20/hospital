<?php
include('include/config.php');

if (!empty($_POST['specilizationid'])) {
    $rows = hms_fetch_all($con, 'SELECT doctorName, id FROM doctors WHERE specilization = ? ORDER BY doctorName ASC', 's', array(hms_post('specilizationid')));
    echo '<option selected="selected" value="">Select Doctor</option>';
    foreach ($rows as $row) {
        echo '<option value="' . hms_e($row['id']) . '">' . hms_e($row['doctorName']) . '</option>';
    }
}

if (!empty($_POST['doctor'])) {
    $row = hms_fetch_one($con, 'SELECT docFees FROM doctors WHERE id = ?', 'i', array(hms_int(hms_post('doctor'))));
    if ($row) {
        echo '<option value="' . hms_e($row['docFees']) . '">' . hms_e($row['docFees']) . '</option>';
    }
}
?>
