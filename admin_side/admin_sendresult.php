<?php
session_start();
require '../components/connect.php';

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
    $patient_name   = $_POST['patient_name'];
    $service        = $_POST['service'];
    $result_text    = trim($_POST['result_text']);

    // --- Handle image upload first ---
    $image_path = null;
    if (isset($_FILES['result_image']) && $_FILES['result_image']['error'] === 0) {
        $target_dir = __DIR__ . "/../patient_side/uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $image_name = time() . "_" . basename($_FILES['result_image']['name']);
        $image_path = $target_dir . $image_name;

        move_uploaded_file($_FILES['result_image']['tmp_name'], $image_path);
    }

    // --- Generate PDF file ---
    require_once('../components/fpdf.php');
    $pdf = new FPDF();
    $pdf->AddPage();

    $pdf->SetFont('Times', 'B', 16);
    $pdf->Cell(0, 10, 'Yanez X-Ray Medical Clinic and Laboratory', 0, 1, 'C');
    $pdf->Ln(8);
    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(0, 8, "Patient Name: $patient_name", 0, 1);
    $pdf->Cell(0, 8, "Service: $service", 0, 1);
    $pdf->Cell(0, 8, "Date: " . date('F d, Y'), 0, 1);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 8, "Prescriptions / Notes:\n\n$result_text");
    $pdf->Ln(10);

    // --- Add image if uploaded ---
    if ($image_path && file_exists($image_path)) {
        $pdf->Image($image_path, 30, $pdf->GetY(), 150);
        $pdf->Ln(60); // space after image
    }

    // --- Save PDF file ---
    $target_dir = __DIR__ . "/../patient_side/uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $filename = time() . "_result.pdf";
    $target_file = $target_dir . $filename;

    $pdf->Output($target_file, 'F');

    // --- Save record in database ---
    $stmt = $conn->prepare("INSERT INTO results (patient_id, appointment_id, result_text, result_file, created_at) 
                            VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiss", $patient_id, $appointment_id, $result_text, $filename);

    if ($stmt->execute()) {
        // Update appointment status to Completed
        $update = $conn->prepare("UPDATE appointment SET status='Completed' WHERE appointment_id=?");
        $update->bind_param("i", $appointment_id);
        $update->execute();

        echo "<script>
        alert('✅ Result sent successfully to patient!');
        window.location.href = 'admin_viewappointment.php';
        </script>";
        exit();
    } else {
        echo "<p class='error-message'>Database Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Send Result to Patient</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/yanezstyle.css">
</head>
<body>
<?php include '../admin_side/admin_header.php'; ?>
<?php include '../admin_side/admin_sidebar.php'; ?>

<main class="main-content">
  <div class="send-result-container">
    <h2>Send Result / Prescription</h2>

    <?php if ($appointment): ?>
      <div class="appointment-info">
        <p><strong>Patient:</strong> <?= htmlspecialchars($appointment['patient_name']) ?></p>
        <p><strong>Service:</strong> <?= htmlspecialchars($appointment['service']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($appointment['appointment_date']) ?> at <?= htmlspecialchars($appointment['appointment_time']) ?></p>
      </div>

      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
        <input type="hidden" name="patient_id" value="<?= $appointment['patient_id'] ?>">
        <input type="hidden" name="patient_name" value="<?= htmlspecialchars($appointment['patient_name']) ?>">
        <input type="hidden" name="service" value="<?= htmlspecialchars($appointment['service']) ?>">

        <label for="result_text">Result / Notes</label>
        <textarea id="result_text" name="result_text" required></textarea>

        <label for="result_image">Attach Image (optional):</label>
        <input type="file" name="result_image" accept="image/*">

        <div class="form-actions">
          <button type="submit">Send</button>
          <a href="admin_viewappointment.php" class="btn-cancel">Cancel</a>
        </div>
      </form>

    <?php else: ?>
      <p class="error-message">⚠ No appointment found or invalid ID.</p>
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
