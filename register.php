<?php
session_start();

// Optional: prevent logged-in users from accessing registration
function check_login() {
    if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
        header("Location: index.php");
        exit();
    }
}
check_login();

require 'components/connect.php';

$register_error = '';
$register_success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize input
    $firstName      = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastName       = mysqli_real_escape_string($conn, $_POST['lastname']);
    $birthdate      = mysqli_real_escape_string($conn, $_POST['birthdate']);
    $email          = mysqli_real_escape_string($conn, $_POST['email']);
    $username       = mysqli_real_escape_string($conn, $_POST['username']);
    $password       = $_POST['password'];
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);

    // Validate age
    $birthDateObj = DateTime::createFromFormat('Y-m-d', $birthdate);
    $today = new DateTime();
    $age = $birthDateObj->diff($today)->y;

    if ($age < 16) {
        $register_error = "You must be at least 16 years old to register.";
    } else {
        // Check if username or email already exists
        $check_query = "SELECT * FROM patient WHERE username='$username' OR email='$email'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $register_error = "Username or Email already exists!";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $insert_query = "INSERT INTO patient 
                (first_name, last_name, email, phone_number, birthdate, username, password)
                VALUES 
                ('$firstName', '$lastName', '$email', '$contact_number', '$birthdate', '$username', '$hashedPassword')";

            if (mysqli_query($conn, $insert_query)) {
                $register_success = "Registered successfully!";
                header("refresh:2;url=login.php");
            } else {
                $register_error = "Error: " . mysqli_error($conn);
            }
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
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/yanezstyle.css">
</head>
<body>
<header>
<?php include 'header.php'; ?>
</header>

<div class="register-wrapper">
  <div class="login-container">
    <h2>Registration</h2>

    <?php if (!empty($register_error)): ?>
      <p style="color:red;"><?php echo $register_error; ?></p>
    <?php endif; ?>

    <?php if (!empty($register_success)): ?>
      <p style="color:green;"><?php echo $register_success; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="firstName">First Name</label>
        <input type="text" id="firstName" name="firstname" placeholder="Enter your first name" required />
      </div>
      <div class="form-group">
        <label for="lastName">Last Name</label>
        <input type="text" id="lastName" name="lastname" placeholder="Enter your last name" required />
      </div>
      <div class="form-group">
        <label for="number">Mobile Number</label>
        <input type="tel" id="number" name="contact_number" placeholder="Enter your number" required />
      </div>
      <div class="form-group">
        <label for="birthdate">Birthdate</label>
        <input type="date" id="birthdate" name="birthdate" required />
      </div>
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required />
      </div>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Choose a username" required />
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Create a password" required />
      </div>

      <button type="submit" class="btn-login">Register</button>
      <p class="register-link">Already have an account? <a href="login.php">Login</a></p>
    </form>
  </div>
</div>
</body>
</html>
