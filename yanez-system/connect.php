<?php
$host = "localhost";
$user = "root"; // your DB username
$pass = "";     // your DB password
$dbname = "yanezclinic_system"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>