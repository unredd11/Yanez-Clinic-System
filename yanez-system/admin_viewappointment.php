<?php
session_start();
require 'connect.php'; // DB connection

// Restrict access to admins only
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// --- Stats Queries ---
$total_sql   = "SELECT COUNT(*) AS total FROM appointment";
$pending_sql = "SELECT COUNT(*) AS pending FROM appointment WHERE status = 'Pending'";
$today_sql   = "SELECT COUNT(*) AS today FROM appointment WHERE appointment_date = CURDATE()";

$total_count   = mysqli_fetch_assoc(mysqli_query($conn, $total_sql))['total'];
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, $pending_sql))['pending'];
$today_count   = mysqli_fetch_assoc(mysqli_query($conn, $today_sql))['today'];

// --- Search & Sort ---
$search = $_GET['search'] ?? '';
$sort   = $_GET['sort'] ?? 'date_asc';

// Base query
$query = "SELECT 
    a.appointment_id,
    a.service,
    a.appointment_date,
    a.appointment_time,
    a.status,
    CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
    p.phone_number,
    p.email,
    a.appointment_details,
    DATE_FORMAT(a.appointment_date, '%M %d, %Y') AS formatted_date,
    DATE_FORMAT(a.appointment_time, '%h:%i %p') AS formatted_time
FROM appointment a
JOIN patient p ON a.patient_id = p.patient_id
WHERE 1=1";

// Apply search filter
if (!empty($search)) {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $query .= " AND (
        p.first_name LIKE '%$searchEscaped%' 
        OR p.last_name LIKE '%$searchEscaped%'
        OR p.email LIKE '%$searchEscaped%'
        OR p.phone_number LIKE '%$searchEscaped%'
        OR a.service LIKE '%$searchEscaped%'
        OR a.status LIKE '%$searchEscaped%'
    )";
}

// Apply sorting
switch ($sort) {
    case 'id_asc':      
        $query .= " ORDER BY a.appointment_id ASC"; 
        break;
    case 'id_desc':     
        $query .= " ORDER BY a.appointment_id DESC"; 
        break;
    case 'name_asc':    
        $query .= " ORDER BY patient_name ASC"; 
        break;
    case 'name_desc':   
        $query .= " ORDER BY patient_name DESC"; 
        break;
    case 'status_asc':  
        $query .= " ORDER BY a.status ASC"; 
        break;
    case 'status_desc': 
        $query .= " ORDER BY a.status DESC"; 
        break;
    case 'date_desc':   
        $query .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC"; 
        break;
    case 'today':
        $query .= " AND a.appointment_date = CURDATE() 
                    ORDER BY a.appointment_time ASC";
        break;
    case 'next_day':
        $query .= " AND a.appointment_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                    ORDER BY a.appointment_time ASC";
        break;
    default:
        $query .= " ORDER BY a.appointment_date ASC, a.appointment_time ASC"; 
        break;
}

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
<title>Admin - View Appointments</title>
<link rel="stylesheet" href="css/yanezstyle.css"/>
</head>
<body>
<?php include 'admin_header.php'; ?>
<?php include 'admin_sidebar.php'; ?>

<div class="view-appointment-wrapper">
  <div class="admin-table-container">

    <!-- Dynamic title -->
    <h2>
      <?php
        if ($sort === 'today') {
            echo "Today's Appointments";
        } elseif ($sort === 'next_day') {
            echo "Next Day Appointments";
        } else {
            echo "View Appointments";
        }
      ?>
    </h2>

    <div class="admin-stats">
        <?php if ($total_count > 0): ?>
        <div class="stat-item">
            <div class="stat-number"><?php echo $total_count; ?></div>
            <div class="stat-label">Total Appointments</div>
        </div>
        <?php endif; ?>

        <?php if ($pending_count > 0): ?>
        <div class="stat-item">
            <div class="stat-number"><?php echo $pending_count; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <?php endif; ?>

        <?php if ($today_count > 0): ?>
        <div class="stat-item">
            <div class="stat-number"><?php echo $today_count; ?></div>
            <div class="stat-label">Today's Appointments</div>
        </div>
        <?php endif; ?>
    </div>

    <div class="admin-controls">
      <!-- Search Form -->
      <form method="get" class="search-bar">
        <input 
          type="text" 
          name="search" 
          class="search-input" 
          placeholder="Search appointments..." 
          value="<?php echo htmlspecialchars($search); ?>"
        >
        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
        <button type="submit">Search</button>
      </form>

      <!-- Sort Dropdown -->
      <form method="get">
        <select id="sort-btn" name="sort" onchange="this.form.submit()">
          <option value="id_asc"   <?php if ($sort == 'id_asc') echo 'selected'; ?>>ID (Low–High)</option>
          <option value="id_desc"  <?php if ($sort == 'id_desc') echo 'selected'; ?>>ID (High–Low)</option>
          <option value="name_asc" <?php if ($sort == 'name_asc') echo 'selected'; ?>>Patient (A–Z)</option>
          <option value="name_desc"<?php if ($sort == 'name_desc') echo 'selected'; ?>>Patient (Z–A)</option>
          <option value="date_asc" <?php if ($sort == 'date_asc') echo 'selected'; ?>>Date (Earliest First)</option>
          <option value="date_desc"<?php if ($sort == 'date_desc') echo 'selected'; ?>>Date (Latest First)</option>
          <option value="status_asc" <?php if ($sort == 'status_asc') echo 'selected'; ?>>Status (A–Z)</option>
          <option value="status_desc"<?php if ($sort == 'status_desc') echo 'selected'; ?>>Status (Z–A)</option>
          <option value="today" <?php if ($sort == 'today') echo 'selected'; ?>>Today's Appointments</option>
          <option value="next_day" <?php if ($sort == 'next_day') echo 'selected'; ?>>Next Day Appointments</option>
        </select>
      </form>
    </div>

    <table class="admin-users-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Patient</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Service</th>
          <th>Date</th>
          <th>Time</th>
          <th>Status</th>
          <th>Details</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['appointment_id']); ?></td>
              <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
              <td><?php echo htmlspecialchars($row['email']); ?></td>
              <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
              <td><?php echo htmlspecialchars($row['service']); ?></td>
              <td><?php echo htmlspecialchars($row['formatted_date']); ?></td>
              <td><?php echo htmlspecialchars($row['formatted_time']); ?></td>
              <td>
              <?php 
                $status = htmlspecialchars($row['status']);
                $statusClass = '';

                if ($status === 'Pending')   $statusClass = 'status-pending';
                if ($status === 'Accepted')  $statusClass = 'status-confirmed';
                if ($status === 'Completed') $statusClass = 'status-completed';
                if ($status === 'Rejected')  $statusClass = 'status-cancelled';
              ?>
              <span class="<?php echo $statusClass; ?>"><?php echo $status; ?></span>
            </td>
              <td><?php echo htmlspecialchars($row['appointment_details']); ?></td>
              <td>
                <div class="action-buttons">
                  <a href="appointment_action.php?id=<?= $row['appointment_id']; ?>&action=accept" class="btn-accept">Accept</a>
                  <a href="appointment_edit.php?id=<?= $row['appointment_id']; ?>" class="btn-edit">Edit</a>
                  <a href="appointment_action.php?id=<?= $row['appointment_id']; ?>&action=delete" class="btn-delete" onclick="return confirm('Delete this appointment?');">Delete</a>
                  <a href="admin_sendresult.php?id=<?= $row['appointment_id']; ?>" class="btn-result">Send Result</a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="10">No appointments found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>  
  </div>
</div>

<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('show');
}
</script>
</body>
</html>
