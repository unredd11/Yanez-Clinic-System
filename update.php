<?php

    include 'connect.php';

    $patient_id = $_GET['patient_id'];

   $sql = "DELETE FROM patient WHERE id='$patient_id'";

    if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
    } else {
    echo "Error updating record: " . $conn->error;
    }

?>