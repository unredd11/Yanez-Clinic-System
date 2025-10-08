<?php
session_start();
require 'connect.php';

// Ensure patient is logged in

$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$username = $_SESSION['username'] ?? '';
$patient_id = $_SESSION['patient_id'];

// Fetch patient info
$sql_patient = "SELECT first_name, last_name, email, phone_number FROM patient WHERE patient_id=?";
$stmt = $conn->prepare($sql_patient);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

// Fetch results
$sql_results = "SELECT r.result_id, r.result_text, r.result_file, r.created_at,
                       a.service, a.appointment_date, a.appointment_time
                FROM results r
                JOIN appointment a ON r.appointment_id = a.appointment_id
                WHERE r.patient_id=? ORDER BY r.created_at DESC";
$stmt = $conn->prepare($sql_results);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$results = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile - Results</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/yanezstyle.css">
</head>
<body>
<?php include 'header.php'; ?>

<main class="profile-container">
  <section class="profile-info">
    <h3>My Information</h3>
    <p><strong>Name:</strong> <?= htmlspecialchars($patient['first_name'].' '.$patient['last_name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($patient['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($patient['phone_number']) ?></p>
  </section>

  <section class="results-section">
    <h3>My Results</h3>
    <?php if ($results->num_rows > 0): ?>
      <table class="results-table">
        <thead>
          <tr>
            <th>Service</th>
            <th>Date</th>
            <th>Time</th>
            <th>Result</th>
            <th>File</th>
            <th>Released</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $results->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['service']) ?></td>
              <td><?= htmlspecialchars($row['appointment_date']) ?></td>
              <td><?= htmlspecialchars($row['appointment_time']) ?></td>
              <td><?= nl2br(htmlspecialchars($row['result_text'])) ?></td>
              <td>
                <?php if ($row['result_file']): ?>
                  <a href="uploads/<?= htmlspecialchars($row['result_file']) ?>" target="_blank">Download</a>
                <?php else: ?>
                  N/A
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($row['created_at']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No results available yet.</p>
    <?php endif; ?>
  </section>

</main>
<?php include 'footer.php'; ?>
</body>
</html>
