<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    if (empty($_POST['patientName']) || empty($_POST['patientPhone']) || empty($_POST['patientEmail']) || 
        empty($_POST['selectedDate']) || empty($_POST['selectedTime']) || empty($_POST['selectedService'])) {
        echo "<script>alert('Please fill in all required fields and make your selections.'); window.history.back();</script>";
        exit();
    }

    $name    = mysqli_real_escape_string($conn, trim($_POST['patientName']));
    $phone   = mysqli_real_escape_string($conn, trim($_POST['patientPhone']));
    $email   = mysqli_real_escape_string($conn, trim($_POST['patientEmail']));
    $notes   = mysqli_real_escape_string($conn, trim($_POST['notes']));
    $date    = mysqli_real_escape_string($conn, $_POST['selectedDate']);
    $time    = mysqli_real_escape_string($conn, $_POST['selectedTime']);
    $service = mysqli_real_escape_string($conn, $_POST['selectedService']);

    // Convert 12-hour time format to 24-hour format for database storage
    $time_24hr = date("H:i:s", strtotime($time));

    // 1. Check if time slot is already booked
    $check_slot = "SELECT appointment_id FROM appointment 
                   WHERE appointment_date = '$date' 
                   AND appointment_time = '$time_24hr'";
    $slot_result = mysqli_query($conn, $check_slot);

    if (!$slot_result) {
        echo "<script>alert('Database error occurred. Please try again.'); window.history.back();</script>";
        exit();
    }

    if (mysqli_num_rows($slot_result) > 0) {
        echo "<script>alert('Sorry, this time slot is already booked. Please choose another time.'); window.history.back();</script>";
        exit();
    }

    // 2. Check if patient already exists or create new
    $sql_patient = "SELECT patient_id FROM patient WHERE email='$email'";
    $result = mysqli_query($conn, $sql_patient);

    if (!$result) {
        echo "<script>alert('Database error occurred. Please try again.'); window.history.back();</script>";
        exit();
    }

    if (mysqli_num_rows($result) > 0) {
        // Patient exists, get their ID and update their info
        $row = mysqli_fetch_assoc($result);
        $patient_id = $row['patient_id'];
        
        // Update patient info in case it changed
        $update_patient = "UPDATE patient SET 
                          first_name = SUBSTRING_INDEX('$name', ' ', 1),
                          last_name = SUBSTRING_INDEX('$name', ' ', -1),
                          phone_number = '$phone' 
                          WHERE patient_id = '$patient_id'";
        mysqli_query($conn, $update_patient);
    } else {
        // Create new patient - split full name into first and last name
        $name_parts = explode(' ', $name, 2);
        $first_name = $name_parts[0];
        $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
        
        $insert_patient = "INSERT INTO patient (first_name, last_name, phone_number, email) 
                           VALUES ('$first_name', '$last_name', '$phone', '$email')";
        
        if (!mysqli_query($conn, $insert_patient)) {
            echo "<script>alert('Error creating patient record: " . mysqli_error($conn) . "'); window.history.back();</script>";
            exit();
        }
        $patient_id = mysqli_insert_id($conn);
    }

    // 3. Assign doctor (you can modify this to select based on service or make it dynamic)
    $doctor_id = 1; // Make sure this doctor_id exists in your doctor table

    // 4. Insert appointment
    $insert_appointment = "INSERT INTO appointment 
        (patient_id, doctor_id, service, appointment_date, appointment_time, status, appointment_details) 
        VALUES ('$patient_id', '$doctor_id', '$service', '$date', '$time_24hr', 'Pending', '$notes')";

    if (mysqli_query($conn, $insert_appointment)) {
        $appointment_id = mysqli_insert_id($conn);
        echo "<script>
            alert('Booking Request Sent! Your appointment has been successfully recorded. Appointment ID: #$appointment_id')</script>";
            require_once 'book_appointment.php';
        exit();
    } else {
        echo "<script>alert('Error booking appointment: " . mysqli_error($conn) . "'); window.history.back();</script>";
        exit();
    }
}
?>