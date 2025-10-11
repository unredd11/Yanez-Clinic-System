<?php
session_start();
require 'connect.php';

// Ensure patient is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$username   = $_SESSION['username'] ?? '';

// --- Handle update form submissions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['first_name'], $_POST['last_name'])) {
        $stmt = $conn->prepare("UPDATE patient SET first_name=?, last_name=? WHERE patient_id=?");
        $stmt->bind_param("ssi", $_POST['first_name'], $_POST['last_name'], $patient_id);
    } elseif (isset($_POST['email'])) {
        $stmt = $conn->prepare("UPDATE patient SET email=? WHERE patient_id=?");
        $stmt->bind_param("si", $_POST['email'], $patient_id);
    } elseif (isset($_POST['phone_number'])) {
        $stmt = $conn->prepare("UPDATE patient SET phone_number=? WHERE patient_id=?");
        $stmt->bind_param("si", $_POST['phone_number'], $patient_id);
    }

    if (isset($stmt) && $stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='patient_profile.php';</script>";
        exit();
    } else {
        echo "<script>alert('Update failed. Please try again.'); window.location.href='patient_profile.php';</script>";
        exit();
    }
}

// --- Fetch patient info ---
$sql_patient = "SELECT first_name, last_name, email, phone_number FROM patient WHERE patient_id=?";
$stmt = $conn->prepare($sql_patient);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

// --- Fetch results ---
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

<main class="profile-dashboard">

  <!-- Profile Info -->
  <section class="profile-info">
    <h3>My Information</h3>

    <div class="profile-field">
      <strong>Name:</strong>
      <span id="name-display"><?= htmlspecialchars($patient['first_name'].' '.$patient['last_name']) ?></span>
      <button type="button" id="name-edit" class="updt-info-edit" onclick="editField('name')">Edit</button>
      
      <form id="name-form" class="edit-form" method="POST" style="display:none;">
        <input type="text" name="first_name" class="form-update" value="<?= htmlspecialchars($patient['first_name']) ?>" required>
        <input type="text" name="last_name" class="form-update" value="<?= htmlspecialchars($patient['last_name']) ?>" required>
        <button type="submit" class="updt-info-save">Save</button>
        <button type="button" class="updt-info-cancel" onclick="cancelEdit('name')">Cancel</button>
      </form>
    </div>

    <div class="profile-field">
      <strong>Email:</strong>
      <span id="email-display"><?= htmlspecialchars($patient['email']) ?></span>
      <button type="button" id="email-edit" class="updt-info-edit" onclick="editField('email')">Edit</button>

      <form id="email-form" class="edit-form" method="POST" style="display:none;">
        <input type="email" name="email" class="form-update" value="<?= htmlspecialchars($patient['email']) ?>" required>
        <button type="submit" class="updt-info-save">Save</button>
        <button type="button" class="updt-info-cancel" onclick="cancelEdit('email')">Cancel</button>
      </form>
    </div>

    <div class="profile-field">
      <strong>Phone:</strong>
      <span id="phone-display"><?= htmlspecialchars($patient['phone_number']) ?></span>
      <button type="button" id="phone-edit" class="updt-info-edit" onclick="editField('phone')">Edit</button>

      <form id="phone-form" class="edit-form" method="POST" style="display:none;">
        <input type="text" name="phone_number" class="form-update" value="<?= htmlspecialchars($patient['phone_number']) ?>" required>
        <button type="submit" class="updt-info-save">Save</button>
        <button type="button" class="updt-info-cancel" onclick="cancelEdit('phone')">Cancel</button>
      </form>
    </div>
  </section>

  <!-- Results -->
  <section class="results-section">
    <h3>My Results</h3>
    <?php if ($results->num_rows > 0): ?>
      <table class="results-table">
        <thead>
          <tr>
            <th>Service</th>
            <th>Date</th>
            <th>Prescription</th>
            <th>Result</th>
            <th>Released</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $results->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['service']) ?></td>
              <td><?= htmlspecialchars($row['appointment_date']) ?></td>
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

<script>
function editField(field) {
  // Hide the display text
  document.getElementById(field + "-display").style.display = "none";
  // Hide the edit button
  document.querySelector("#" + field + "-edit").style.display = "none";
  // Show the form
  document.querySelector("#" + field + "-form").style.display = "block";
}

function cancelEdit(field) {
  // Show the display text
  document.getElementById(field + "-display").style.display = "inline";
  // Show the edit button again
  document.querySelector("#" + field + "-edit").style.display = "inline-block";
  // Hide the form
  document.querySelector("#" + field + "-form").style.display = "none";
}
</script>


</body>
</html>
