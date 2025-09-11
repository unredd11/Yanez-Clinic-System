<?php
session_start();
check_login_redirect();
handle_logout();

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort   = $_GET['sort'] ?? '';

$sql = "SELECT 
            patient_id, 
            first_name,
            last_name,
            email,
            phone_number,
            birthdate
        FROM patient";

// Search condition
$conditions = [];
if (!empty($search)) {
    $conditions[] = "(first_name LIKE '%$search%' 
                      OR last_name LIKE '%$search%' 
                      OR email LIKE '%$search%' 
                      OR phone_number LIKE '%$search%' 
                      OR patient_id LIKE '%$search%')";
}

// Append WHERE clause if any conditions
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Sorting
switch ($sort) {
    case 'id_asc':
        $order = "ORDER BY patient_id ASC";
        break;
    case 'id_desc':
        $order = "ORDER BY patient_id DESC";
        break;
    case 'name_asc':
        $order = "ORDER BY last_name ASC, first_name ASC";
        break;
    case 'name_desc':
        $order = "ORDER BY last_name DESC, first_name DESC";
        break;
    case 'email_asc':
        $order = "ORDER BY email ASC";
        break;
    case 'email_desc':
        $order = "ORDER BY email DESC";
        break;
    default:
        $order = "ORDER BY patient_id ASC";
        break;
}

$sql .= " $order";
$result = $conn->query($sql);
?>
HTML Table with ID Column
html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin - Patient List</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="yanezstyle.css" />
</head>
<body>
  
  <div class="content">
    <div class="listings">
      <h3>Registered Patients</h3>
      <div class="top-bar">

        <!-- Search Form -->
        <form method="get" class="search-bar" style="display:inline-block;">
          <input 
            type="text" 
            name="search" 
            class="search-input" 
            placeholder="Search" 
            value="<?php echo htmlspecialchars($search); ?>"
          >
          <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
          <button type="submit">Search</button>
        </form>

        <!-- Sort Dropdown -->
        <div class="sort-dropdown">
          <label for="sort">Sort by:</label>
          <form method="get">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
            <select id="sort" name="sort" onchange="this.form.submit()">
              <option value="id_asc" <?php if ($sort == 'id_asc') echo 'selected'; ?>>ID (Low–High)</option>
              <option value="id_desc" <?php if ($sort == 'id_desc') echo 'selected'; ?>>ID (High–Low)</option>
              <option value="name_asc" <?php if ($sort == 'name_asc') echo 'selected'; ?>>Name (A–Z)</option>
              <option value="name_desc" <?php if ($sort == 'name_desc') echo 'selected'; ?>>Name (Z–A)</option>
              <option value="email_asc" <?php if ($sort == 'email_asc') echo 'selected'; ?>>Email (A–Z)</option>
              <option value="email_desc" <?php if ($sort == 'email_desc') echo 'selected'; ?>>Email (Z–A)</option>
            </select>
          </form>
        </div>
      </div>
      
      <!-- Patient Table -->
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Birthdate</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['patient_id']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                <td><?php echo htmlspecialchars($row['birthdate']); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5">No patients found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>

<?php
$conn->close();
?>