<?php
session_start();

function check_login() {
    if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
        header("Location: login.php");
        exit();
    }
}

check_login();
require 'connect.php'; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName      = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastName       = mysqli_real_escape_string($conn, $_POST['lastname']);
    $birthdate      = mysqli_real_escape_string($conn, $_POST['birthdate']);
    $email          = mysqli_real_escape_string($conn, $_POST['email']);
    $username       = mysqli_real_escape_string($conn, $_POST['username']);
    $password       = mysqli_real_escape_string($conn, $_POST['password']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);

    // Validate age is at least 16
    $birthDateObj = DateTime::createFromFormat('Y-m-d', $birthdate);
    $today = new DateTime();
    $age = $birthDateObj->diff($today)->y;

    if ($age < 16) {
        echo "<script>alert('You must be at least 16 years old to register.'); window.location.href='register.php';</script>";
        exit();
    }

    // Check if username or email already exists in patient table
    $check_query = "SELECT * FROM patient WHERE username='$username' OR email='$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Username or Email already exists!'); window.location.href='register.php';</script>";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert into patient table
        $insert_query = "INSERT INTO patient (first_name, last_name, email, phone_number, birthdate, username, password)
                         VALUES ('$firstName', '$lastName', '$email', '$contact_number', '$birthdate', '$username', '$hashedPassword')";

        if (mysqli_query($conn, $insert_query)) {
            echo "<script>alert('Registered successfully!'); window.location.href='user_login.php';</script>";
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Yañez X-Ray Medical Clinic</title>
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
  <div class="register-wrapper">
    <div class="login-container">
      <h2>Registration</h2>
      <form>
        <div class="form-group">
          <label for="firstName">First Name</label>
          <input type="text" id="firstName" placeholder="Enter your first name" required />
        </div>
        <div class="form-group">
          <label for="lastName">Last Name</label>
          <input type="text" id="lastName" placeholder="Enter your last name" required />
        </div>
        <div class="form-group">
          <label for="number">Mobile Number</label>
          <input type="tel" id="number" placeholder="Enter your number" required />
        </div>
        <div class="form-group">
          <label for="birthdate">Birthdate</label>
          <input type="date" id="birthdate" required />
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" placeholder="Enter your email" required />
        </div>
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" placeholder="Choose a username" required />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" placeholder="Create a password" required />
        </div>

        <button type="submit" class="btn-login">Register</button>
        <p class="register-link">Already have an account? <a href="login.html">Login</a></p>
      </form>
    </div>
  </div>
</body>
</html>
