<?php
session_start();
require 'connect.php'; // DB connection

// Restrict access to admins only
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Query joins appointment with patient and doctor tables
$query = "
SELECT 
    a.appointment_id,
    a.service,
    a.appointment_date,
    a.appointment_time,
    a.status,
    CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
    p.phone_number,
    p.email,
    d.name AS doctor_name,
    a.appointment_details,
    DATE_FORMAT(a.appointment_date, '%M %d, %Y') AS formatted_date,
    DATE_FORMAT(a.appointment_time, '%h:%i %p') AS formatted_time
FROM appointment a
JOIN patient p ON a.patient_id = p.patient_id
JOIN doctor d ON a.doctor_id = d.doctor_id
ORDER BY a.appointment_date ASC, a.appointment_time ASC
";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$appointment_count = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - View Appointments</title>
<link rel="stylesheet" href="yanezstyle.css">
</head>
<body>

<?php include 'admin_header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="admin-table-container">
    <h2>All Appointments</h2>
    
    <div class="admin-stats">
        <div class="stat-item">
            <div class="stat-number"><?php echo $appointment_count; ?></div>
            <div class="stat-label">Total Appointments</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">
                <?php 
                mysqli_data_seek($result, 0);
                $pending_count = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    if ($row['status'] == 'Pending') $pending_count++;
                }
                echo $pending_count;
                ?>
            </div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">
                <?php 
                mysqli_data_seek($result, 0);
                $today_count = 0;
                $today = date('Y-m-d');
                while ($row = mysqli_fetch_assoc($result)) {
                    if ($row['appointment_date'] == $today) $today_count++;
                }
                echo $today_count;
                ?>
            </div>
            <div class="stat-label">Today's Appointments</div>
        </div>
    </div>

    <?php if ($appointment_count > 0): ?>
        <table class="appointments-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Patient Name</th>
                    <th>Doctor</th>
                    <th>Contact</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                mysqli_data_seek($result, 0);
                while ($row = mysqli_fetch_assoc($result)): 
                ?>
                <tr>
                    <td class="appointment-id">#<?= htmlspecialchars($row['appointment_id']); ?></td>
                    <td><?= htmlspecialchars($row['service']); ?></td>
                    <td><?= htmlspecialchars($row['formatted_date']); ?></td>
                    <td><?= htmlspecialchars($row['formatted_time']); ?></td>
                    <td class="status-<?= strtolower($row['status']); ?>">
                        <?= htmlspecialchars($row['status']); ?>
                    </td>
                    <td><?= htmlspecialchars($row['patient_name']); ?></td>
                    <td><?= htmlspecialchars($row['doctor_name']); ?></td>
                    <td>
                        <div><?= htmlspecialchars($row['phone_number']); ?></div>
                        <div><?= htmlspecialchars($row['email']); ?></div>
                    </td>
                    <td>
                        <?php if (!empty($row['appointment_details'])): ?>
                            <?= htmlspecialchars($row['appointment_details']); ?>
                        <?php else: ?>
                            No notes
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-appointments">
            <h3>No appointments found</h3>
            <p>There are currently no appointments in the system.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>