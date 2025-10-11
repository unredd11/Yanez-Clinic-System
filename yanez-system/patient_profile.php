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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<!-- Appointment Tabs Section -->
<section class="appointment-tabs-section">
  <h3>My Appointments</h3>

  <div class="tabs">
    <button class="tab-button active" onclick="showTab('request')">Request</button>
    <button class="tab-button" onclick="showTab('accepted')">Accepted</button>
    <button class="tab-button" onclick="showTab('declined')">Rejected</button>
  </div>

  <div id="request" class="tab-content active">
    <?php
      $sql_req = "SELECT service, appointment_date, appointment_time, status, appointment_details
                  FROM appointment WHERE patient_id = ? AND status = 'Pending'
                  ORDER BY appointment_date DESC, appointment_time DESC";
      $stmt = $conn->prepare($sql_req);
      $stmt->bind_param("i", $patient_id);
      $stmt->execute();
      $requests = $stmt->get_result();
    ?>
    <?php if ($requests->num_rows > 0): ?>
      <?php while ($row = $requests->fetch_assoc()): ?>
        <div class="appointment-card">
          <strong>Title:</strong> <?= htmlspecialchars($row['service']) ?><br>
          <span>Status: <?= htmlspecialchars($row['status']) ?></span><br>
          <small>Requested at: <?= htmlspecialchars($row['appointment_date'].' '.$row['appointment_time']) ?></small><br>
          <em><?= htmlspecialchars($row['appointment_details'] ?: '') ?></em>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No pending requests.</p>
    <?php endif; ?>
  </div>

  <div id="accepted" class="tab-content">
    <?php
      $sql_acc = "SELECT service, appointment_date, appointment_time, status, appointment_details
                  FROM appointment WHERE patient_id = ? AND status = 'Accepted'
                  ORDER BY appointment_date DESC, appointment_time DESC";
      $stmt = $conn->prepare($sql_acc);
      $stmt->bind_param("i", $patient_id);
      $stmt->execute();
      $accepted = $stmt->get_result();
    ?>
    <?php if ($accepted->num_rows > 0): ?>
      <?php while ($row = $accepted->fetch_assoc()): ?>
        <div class="appointment-card accepted">
          <strong>Title:</strong> <?= htmlspecialchars($row['service']) ?><br>
          <span>Status: <?= htmlspecialchars($row['status']) ?></span><br>
          <small>Accepted at: <?= htmlspecialchars($row['appointment_date'].' '.$row['appointment_time']) ?></small><br>
          <em><?= htmlspecialchars($row['appointment_details'] ?: '') ?></em>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No accepted appointments.</p>
    <?php endif; ?>
  </div>

  <div id="declined" class="tab-content">
    <?php
      $sql_dec = "SELECT service, appointment_date, appointment_time, status, appointment_details
                  FROM appointment WHERE patient_id = ? AND status IN ('Rejected', 'Declined')
                  ORDER BY appointment_date DESC, appointment_time DESC";
      $stmt = $conn->prepare($sql_dec);
      $stmt->bind_param("i", $patient_id);
      $stmt->execute();
      $declined = $stmt->get_result();
    ?>
    <?php if ($declined->num_rows > 0): ?>
      <?php while ($row = $declined->fetch_assoc()): ?>
        <div class="appointment-card declined">
          <strong>Title:</strong> <?= htmlspecialchars($row['service']) ?><br>
          <span>Status: <?= htmlspecialchars($row['status']) ?></span><br>
          <small>Updated at: <?= htmlspecialchars($row['appointment_date'].' '.$row['appointment_time']) ?></small><br>
          <em><?= htmlspecialchars($row['appointment_details'] ?: 'No reason provided') ?></em>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No Rejected appointments.</p>
    <?php endif; ?>
  </div>
</section>

<script>
function showTab(tabId) {
  document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
  document.querySelector(`[onclick="showTab('${tabId}')"]`).classList.add('active');
  document.getElementById(tabId).classList.add('active');
}
</script>

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
function toggleNav() {
  var navMenu = document.getElementById('navMenu');
  if (navMenu) navMenu.classList.toggle('show');
}

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
