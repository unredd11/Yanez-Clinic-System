<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">
    <div class="nav-left">
      <div class="logo-name">
        <a href="yanezindex.php"><img src="yanez1 logo.jpg" alt="Yañez Clinic Logo" style="width:42px;height:42px;"></a>
        <h1>Yañez X-Ray Medical Clinic and Laboratory</h1>
      </div>
    </div>
</ul>

      </ul>
      <div class="auth-buttons">
    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    <?php else: ?>
        <a href="login.php" class="btn-login">Login</a>
        <a href="register.php" class="btn-register">Register</a>
    <?php endif; ?>
</div>

    </div>
  </nav>