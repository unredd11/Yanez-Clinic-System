<?php
session_start();
require 'components/connect.php';

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_input = trim($_POST['username'] ?? '');
    $password_input = $_POST['password'] ?? '';

    if (!empty($user_input) && !empty($password_input)) {
        $stmt = $conn->prepare("
            SELECT patient_id, username, email, password 
            FROM patient 
            WHERE username = ? OR email = ?
        ");
        $stmt->bind_param("ss", $user_input, $user_input);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password_input, $user['password'])) {
                session_regenerate_id(true);

                $_SESSION['user_logged_in'] = true;
                $_SESSION['patient_id'] = $user['patient_id'];
                $_SESSION['username'] = $user['username'];

                header('Location: index.php');
                exit();
            }
        }

        $login_error = 'Invalid username or password.';
    } else {
        $login_error = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Patient Login - Yañez X-Ray Medical Clinic</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/yanezstyle.css"/>
</head>
<body>
<header>
    <?php include 'header.php'; ?>
</header>

<div class="login-wrapper">
  <div class="side-login-text">
    <h1>Yañez X-Ray Medical Clinic and Laboratory</h1>
    <p>Log in to access your patient account.</p>
  </div>

  <div class="login-container">
    <h2>Patient Login</h2>

    <?php if (!empty($login_error)): ?>
      <p style="color:red;"><?php echo $login_error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="username">Username or Email</label>
        <input type="text" id="username" name="username" required />
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
      </div>
      <button type="submit" class="btn-login">Login</button>
      <p class="register-link">Don't have an account? <a href="register.php">Register</a></p>
    </form>
  </div>
</div>
</body>
</html>
