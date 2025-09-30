<?php
session_start();
require 'connect.php'; // DB connection

// --- LOGIN CHECK ---
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// --- DASHBOARD QUERIES ---
$total_patients = 0;
$total_appointments = 0;
$monthly_revenue = 0;

// Count patients
$sql_patients = "SELECT COUNT(*) AS total FROM patient";
$res_patients = mysqli_query($conn, $sql_patients);
if ($res_patients && $row = mysqli_fetch_assoc($res_patients)) {
    $total_patients = $row['total'];
}

// Count appointments
$sql_appts = "SELECT COUNT(*) AS total FROM appointment";
$res_appts = mysqli_query($conn, $sql_appts);
if ($res_appts && $row = mysqli_fetch_assoc($res_appts)) {
    $total_appointments = $row['total'];
}


// Get admin username from session
$admin_name = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Yañez X-Ray Medical Clinic</title>
  <link rel="stylesheet" href="yanezstyle.css" />
</head>
<body>

<header>
   <?php include 'admin_header.php'; ?>
</header>

<!-- Side Bar -->
<aside class="sidebar" id="sidebar">
    <?php include 'admin_sidebar.php'; ?>
</aside>

<!-- Main Content -->
<main class="main-content" id="mainContent">
<div id="dashboard" class="content-view active">
    <div class="content-header fade-in">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Welcome back, <?= htmlspecialchars($admin_name) ?>!</p>
        </div>
        <div class="breadcrumb">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </div>
    </div>

<!-- Dashboard Statistics -->
<div class="dashboard-grid fade-in">
    <div class="stat-card primary">
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value"><?= $total_patients ?></div>
        <div class="stat-label">Total Patients</div>
    </div>

    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <div class="stat-value"><?= $total_appointments ?></div>
        <div class="stat-label">Overall Appointments</div>
    </div>

</div>
</div>
</main>

</body>
</html>
