<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">
  <div class="nav-left">
    <div class="logo-name">
      <!-- Use root-relative path so it works everywhere -->
    <a href="index.php">
      <img src="images/yanezlogo.png" alt="Yañez Clinic Logo" style="width:42px;height:42px;">
    </a>
    </div>

    <li class="hamburger">
      <a href="#" onclick="toggleNav()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="#FFF" height="35" viewBox="0 96 960 960" width="35">
          <path d="M120 816v-60h720v60H120Zm0-210v-60h720v60H120Zm0-210v-60h720v60H120Z"/>
        </svg>
      </a>
    </li>
    <h1>Yañez X-Ray Medical Clinic and Laboratory</h1>
  </div>

  <div class="nav-right">
    <!-- Nav menu -->
  <ul class="nav-menu" id="navMenu">
    <li><a href="patient_profile.php">Profile</a></li>
    <li><a href="index.php#services">Services</a></li>
    <li><a href="index.php#hours">Hours</a></li>
    <li><a href="index.php#about">About Us</a></li>
  </ul>

    <!-- Auth buttons -->
    <div class="auth-buttons">
      <?php if (!empty($_SESSION['user_logged_in'])): ?>
        <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn-login">Login</a>
        <a href="register.php" class="btn-register">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
