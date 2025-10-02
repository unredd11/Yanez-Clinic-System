<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $appointment_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'accept') {
        $sql = "UPDATE appointment SET status = 'Accepted' WHERE appointment_id = ?";
    } elseif ($action === 'reject') {
        $sql = "UPDATE appointment SET status = 'Rejected' WHERE appointment_id = ?";
    } elseif ($action === 'delete') {
        $sql = "DELETE FROM appointment WHERE appointment_id = ?";
    } else {
        die("Invalid action.");
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);

    if ($stmt->execute()) {
        header("Location: admin_viewappointment.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
