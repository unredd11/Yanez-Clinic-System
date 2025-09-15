<?php
require 'connect.php';

$service = $_POST['service'];
$date = $_POST['date'];
$time = $_POST['time'];
$full_name = $_POST['full_name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$notes = $_POST['notes'];

$stmt = $conn->prepare("INSERT INTO appointments (service, appointment_date, appointment_time, full_name, phone_number, email, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $service, $date, $time, $full_name, $phone, $email, $notes);

if ($stmt->execute()) {
    header("Location: appointment_success.php");
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
