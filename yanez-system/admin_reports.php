<?php
session_start();
require 'connect.php';

// Security check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// --- Total Appointments ---
$totalAppointmentsQuery = "SELECT COUNT(*) AS total FROM appointment";
$totalAppointments = $conn->query($totalAppointmentsQuery)->fetch_assoc()['total'];

// --- Today’s Appointments ---
$todaysAppointmentsQuery = "SELECT COUNT(*) AS today_total 
                            FROM appointment 
                            WHERE DATE(appointment_date) = CURDATE()";
$todaysAppointments = $conn->query($todaysAppointmentsQuery)->fetch_assoc()['today_total'];


// --- Pending Approvals ---
$pendingApprovalsQuery = "SELECT COUNT(*) AS pending 
                          FROM appointment 
                          WHERE status = 'Pending'";
$pendingApprovals = $conn->query($pendingApprovalsQuery)->fetch_assoc()['pending'];

// --- Completed Today ---
$completedQuery = "SELECT COUNT(*) AS completed 
                   FROM appointment 
                   WHERE status = 'Completed' 
                   AND DATE(updated_at) = CURDATE()";
$completedToday = $conn->query($completedQuery)->fetch_assoc()['completed'];

// --- Upcoming Appointments List ---
$upcomingQuery = "
  SELECT a.appointment_id, a.service, a.appointment_date, a.appointment_time, 
         p.first_name, p.last_name
  FROM appointment a
  JOIN patient p ON a.patient_id = p.patient_id
  WHERE a.appointment_date >= CURDATE()
  ORDER BY a.appointment_date, a.appointment_time
  LIMIT 5";
$upcomingResult = $conn->query($upcomingQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <title>Admin Reports</title>
  <link rel="stylesheet" href="css/yanezstyle.css">
</head>
<body>
  <?php include 'admin_header.php'; ?>
  <?php include 'admin_sidebar.php'; ?>


  <div class="report-dashboard">
    <h2>Admin Reports</h2>

  <div class="report-stats">
    <div class="report-box">
      <h4>Total Appointments</h4>
      <h2><?= $totalAppointments ?></h2>
      <p>This week</p>
    </div>

    <div class="report-box">
      <h4>Today's Appointments</h4>
      <h2><?= $todaysAppointments ?></h2>
      <p>Upcoming today</p>
    </div>

    <div class="report-box">
      <h4>Pending Approvals</h4>
      <h2><?= $pendingApprovals ?></h2>
      <p>Overall Pending Approvals</p>
    </div>

    <div class="report-box">
      <h4>Completed Today</h4>
      <h2><?= $completedToday ?></h2>
      <p>Completed Today</p>
    </div>
  </div>

  <div class="report-section">
    <h3>Upcoming Appointments</h3>
    <div class="report-appointments">
      <?php if ($upcomingResult->num_rows > 0): ?>
        <?php while($row = $upcomingResult->fetch_assoc()): ?>
          <div class="report-appointment-item">
            <strong><?= $row['first_name'] . ' ' . $row['last_name']; ?></strong><br>
            <?= $row['service']; ?> <br>
            <?= date("h:i A", strtotime($row['appointment_time'])); ?> • <?= $row['appointment_date']; ?>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No upcoming appointments.</p>
      <?php endif; ?>
    </div>
  </div>

</div>
  </div>
<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('show');
}
</script>
</body>
</html>

<?php $conn->close(); ?>
