<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar-admin">
    <div class="nav-left-admin">
      <div class="logo-name">
        <img src="yanez1 logo.jpg" alt="Yañez Clinic Logo" style="width:42px;height:42px;"></a>
        <h1>Yañez X-Ray Medical Clinic and Laboratory</h1>
      </div>
    </div>
</ul>

      </ul>
      <div class="auth-buttons">
    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="admin_logout.php" class="logout-btn">Logout</a>
    <?php else: ?>
    <?php endif; ?>
      </div>
    </div>
  </nav>