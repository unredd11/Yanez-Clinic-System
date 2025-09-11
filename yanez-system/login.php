<?php

session_start();

$login_error = '';
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and trim input values
    $user_input = trim($_POST['username'] ?? '');
    $password_input = $_POST['password'] ?? '';

    if (!empty($user_input) && !empty($password_input)) {
        // Prepared statement to prevent SQL injection
        $stmt = $conn->prepare("
            SELECT patient_id, username, password 
            FROM patient 
            WHERE username = ? OR email = ?
        ");
        $stmt->bind_param("ss", $user_input, $user_input);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password_input, $user['password'])) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);

                // Set session variables
                $_SESSION['user_logged_in'] = true;
                $_SESSION['patient_id'] = $user['patient_id'];
                $_SESSION['username'] = $user['username'];

                header('Location:yanezindex.php');
                exit();
            }
        }
    }

    // Generic error message
    $login_error = 'Invalid username or password.';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Yañez X-Ray Medical Clinic</title>
  <link rel="stylesheet" href="yanezstyle.css" />
</head>
   <header>
     <nav class="navbar">
 <div class="nav-left">
    <div class="logo-name">
      <a href="yanezindex.html"><img src= "yanez1 logo.jpg" alt="Yañez Clinic Logo" style="width:42px;height:42px;"></a>
      <h1>Yañez X-Ray Medical Clinic and Laboratory</h1>
    </div>
  </div>
  <div class="nav-right">
    <ul class="nav-menu">
      <li><a href="#services">Services</a></li>
      <li><a href="#hours">Hours</a></li>
      <li><a href="#about">About Us</a></li>
    </ul>
    <div class="auth-buttons">
      <a href="login.html" class="btn-login">Login</a>
      <a href="register.html" class="btn-register">Register</a>
    </div>
  </div>
</nav>
   </header>
<body>

  <div class="login-wrapper">
    <div class="side-login-text">
      <h1>Yañez X-Ray Medical Clinic and Laboratory</h1>
      <p>Your health is our priority. Please log in to access your account.</p>
    </div>

    <div class="login-container">
      <h2>Login</h2>
      <form>
        <div class="form-group">
          <label for="username">Username or Email</label>
          <input type="text" id="username" placeholder="Enter your username" required />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" placeholder="Enter your password" required />
        </div>
        <button type="submit" class="btn-login">Login</button>
        <p class="register-link">Don't have an account? <a href="register.html">Register</a></p>
      </form>
    </div>
  </div>
</body>

</html>
