<?php
session_start();
require 'connect.php';
$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_input = trim($_POST['email'] ?? '');
    $password_input = $_POST['password'] ?? '';

    $sql = "SELECT * FROM admin_users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email_input);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password_input, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $admin['name'];
        $_SESSION['admin_id'] = $admin['admin_id'];

        header('Location: admin_dashboard.php');
        exit();
    } else {
        $login_error = 'Invalid admin email or password.';
    }
    
    if (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
    $login_error = 'Please enter a valid email address.';
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Login - Yañez X-Ray Medical Clinic</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/yanezstyle.css"/>
</head>
<body>
<header>
  <?php include 'admin_header.php'; ?>
</header>

<div class="login-wrapper">
  <div class="side-login-text">
    <h1>Yañez X-Ray Medical Clinic and Laboratory</h1>
    <p>Administrator access only. Please log in.</p>
  </div>

  <div class="login-container">
    <h2>Admin Login</h2>

    <?php if (!empty($login_error)): ?>
      <p style="color:red;"><?php echo $login_error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="email">Admin Email</label>
        <input type="text" id="email" name="email" required />
      </div>
      <div class="form-group">
        <label for="password">Admin Password</label>
        <input type="password" id="password" name="password" required />
      </div>
      <button type="submit" class="btn-login">Login</button>
    </form>
  </div>
</div>
</body>
</html>
