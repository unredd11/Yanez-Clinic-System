<?php
include 'connect.php';

if (isset($_GET['patient_id'])) {
    $patient_id = intval($_GET['patient_id']); // ensure it's an integer

    $sql = "DELETE FROM patient WHERE patient_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patient_id);

    if ($stmt->execute()) {
        echo '<script>alert("Record deleted successfully"); window.location.href="admin_viewappointment.php";</script>';
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "No patient ID provided.";
}

$conn->close();
?>
