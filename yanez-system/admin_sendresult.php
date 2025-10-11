<?php
session_start();
require 'connect.php';

// Security check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Initialize
$appointment = null;

// Fetch appointment info based on ?id= in URL
if (isset($_GET['id'])) {
    $appointment_id = intval($_GET['id']);

    $sql = "SELECT a.appointment_id, a.patient_id,
                   CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
                   p.email, p.phone_number,
                   a.service, a.appointment_date, a.appointment_time
            FROM appointment a
            JOIN patient p ON a.patient_id = p.patient_id
            WHERE a.appointment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $appointment = $stmt->get_result()->fetch_assoc();
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = intval($_POST['appointment_id']);
    $patient_id     = intval($_POST['patient_id']);
    $result_text    = trim($_POST['result_text']);
    $result_file    = null;

    // Handle file upload
    if (!empty($_FILES['result_file']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $filename = time() . "_" . basename($_FILES["result_file"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["result_file"]["tmp_name"], $target_file)) {
            $result_file = $filename;
        }
    }

    // Insert into results table (result_id auto, created_at default)
    $sql = "INSERT INTO results (appointment_id, patient_id, result_text, result_file)
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $appointment_id, $patient_id, $result_text, $result_file);

    if ($stmt->execute()) {
        // Update appointment status to Completed
        $update = $conn->prepare("UPDATE appointment SET status='Completed' WHERE appointment_id=?");
        $update->bind_param("i", $appointment_id);
        $update->execute();

        header("Location: admin_viewappointment.php?result_sent=1");
        exit();
    } else {
        echo "<p style='color:red;'>Database Error: " . $conn->error . "</p>";
    }

    if ($stmt->execute()) {
    // If status is Completed, ensure a result record exists
    if ($status === 'Completed') {
        $check = $conn->prepare("SELECT result_id FROM results WHERE appointment_id=?");
        $check->bind_param("i", $appointment_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $insert = $conn->prepare("INSERT INTO results (appointment_id, patient_id, result_text, created_at) VALUES (?, ?, ?, NOW())");
            $default_text = "Marked as completed by admin.";
            $insert->bind_param("iis", $appointment_id, $appointment['patient_id'], $default_text);
            $insert->execute();
        }
    }

    header("Location: admin_viewappointment.php?updated=1");
    exit();
}

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Send Result to Patient</title>
  <link rel="stylesheet" href="css/yanezstyle.css">
</head>
<body>
  <?php include 'admin_header.php'; ?>
  <?php include 'admin_sidebar.php'; ?>

<main class="main-content">
  <div class="edit-appointment-container">
    <h2>Send Result to Patient</h2>

    <?php if ($appointment): ?>
      <div class="appointment-info">
        <p><strong>Patient:</strong> <?= htmlspecialchars($appointment['patient_name']) ?></p>
        <p><strong>Service:</strong> <?= htmlspecialchars($appointment['service']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($appointment['appointment_date']) ?> at <?= htmlspecialchars($appointment['appointment_time']) ?></p>
      </div>

      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
        <input type="hidden" name="patient_id" value="<?= $appointment['patient_id'] ?>">

        <label for="result_text">Result / Notes</label>
        <textarea id="result_text" name="result_text" placeholder="Write your result or diagnosis here..." required></textarea>

        <label for="result_file">Attach File (optional)</label>
        <input type="file" id="result_file" name="result_file">

        <div class="form-actions">
          <button type="submit">Send Result</button>
          <a href="admin_viewappointment.php" class="btn-cancel">Cancel</a>
        </div>
      </form>
    <?php else: ?>
      <p style="color:red;">⚠ No appointment found or invalid ID.</p>
      <a href="admin_viewappointment.php" class="btn-cancel">Go Back</a>
    <?php endif; ?>
  </div>
</main>
<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('show');
}
</script>
</body>
</html>
