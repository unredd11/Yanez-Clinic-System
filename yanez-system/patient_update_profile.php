<?php
session_start();
require 'connect.php';

$patient_id = $_SESSION['patient_id'];

if (isset($_POST['first_name'], $_POST['last_name'])) {
    $stmt = $conn->prepare("UPDATE patient SET first_name=?, last_name=? WHERE patient_id=?");
    $stmt->bind_param("ssi", $_POST['first_name'], $_POST['last_name'], $patient_id);
} elseif (isset($_POST['email'])) {
    $stmt = $conn->prepare("UPDATE patient SET email=? WHERE patient_id=?");
    $stmt->bind_param("si", $_POST['email'], $patient_id);
} elseif (isset($_POST['phone_number'])) {
    $stmt = $conn->prepare("UPDATE patient SET phone_number=? WHERE patient_id=?");
    $stmt->bind_param("si", $_POST['phone_number'], $patient_id);
}

if ($stmt->execute()) {
    header("Location: profile.php?success=1");
    exit();
} else {
    echo "Error updating profile: " . $conn->error;
}

