<?php
session_start();
require '../components/connect.php';
// --- Security check ---
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// --- Get appointment details ---
if (isset($_GET['id'])) {
    $appointment_id = intval($_GET['id']);

    $sql = "SELECT * FROM appointment WHERE appointment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();

    if (!$appointment) {
        die("Appointment not found!");
    }
}

// --- Update appointment ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id   = intval($_POST['appointment_id']);
    $service          = $_POST['service'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $status           = $_POST['status'];
    $details          = $_POST['appointment_details'];

    $sql = "UPDATE appointment 
            SET service=?, appointment_date=?, appointment_time=?, status=?, appointment_details=? 
            WHERE appointment_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $service, $appointment_date, $appointment_time, $status, $details, $appointment_id);

    if ($stmt->execute()) {
        header("Location: admin_viewappointment.php?updated=1");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <title>Edit Appointment</title>
  <link rel="stylesheet" href="../css/yanezstyle.css">
</head>
<body>
  
<?php include '../admin_side/admin_header.php'; ?>
<?php include '../admin_side/admin_sidebar.php'; ?>

<main class="main-content">
<div class="edit-appointment-container">
  <div class="content-header">
    <h2>Edit Appointment</h2>
  </div>
  <form method="POST">
    <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['appointment_id']) ?>">

   <label for="service">Service</label>
    <select id="service" name="service" required>
    <option value="X-ray & Diagnostics" <?= $appointment['service'] == 'X-ray & Diagnostics' ? 'selected' : '' ?>>X-ray & Diagnostics</option>
    <option value="Laboratory Testing" <?= $appointment['service'] == 'Laboratory Testing' ? 'selected' : '' ?>>Laboratory Testing</option>
    <option value="Physical Examination" <?= $appointment['service'] == 'Physical Examination' ? 'selected' : '' ?>>Physical Examination</option>
    </select>
    
        <div class="edit-appointment-row">
    <div>
        <label for="appointment_date">Appointment Date</label>
        <input type="date" id="appointment_date" name="appointment_date" value="<?= htmlspecialchars($appointment['appointment_date']) ?>" required>
    </div>
    <div>
        <label for="appointment_time">Appointment Time</label>
        <input type="time" id="appointment_time" name="appointment_time" value="<?= htmlspecialchars($appointment['appointment_time']) ?>" required>
    </div>
    </div>


    <label>Patient ID</label>
    <input type="text" value="<?= htmlspecialchars($appointment['patient_id']) ?>" disabled>

    <label>Status</label>
    <select name="status">
        <option value="Pending"   <?= $appointment['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
        <option value="Accepted"  <?= $appointment['status'] == 'Accepted' ? 'selected' : '' ?>>Accepted</option>
        <option value="Rejected"  <?= $appointment['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
        <option value="Completed" <?= $appointment['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
    </select>

    <label>Details / Notes</label>
    <textarea name="appointment_details"><?= htmlspecialchars($appointment['appointment_details']) ?></textarea>

    <div class= "form-actions">
    <button type="submit">Save Changes</button>
    <a href="admin_viewappointment.php" class="btn-cancel">Cancel</a>
    </div>
  </form>
</main>
<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('show');
}
</script>

</body>
</html>
