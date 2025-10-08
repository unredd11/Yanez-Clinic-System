<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar-admin">
    <div class="nav-left-admin">
      <div class="logo-name">
        <a href="admin_dashboard.php"><img src="images/yanezlogo.png" alt="Yañez Clinic Logo" style="width:42px;height:42px;"></a>
        <h1>Yañez X-Ray Medical Clinic and Laboratory</h1>
        <li class="hamburger" onclick=showSidebar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" fill= "#FFF" height="26" viewBox="0 96 960 960" width="26"><path d="M120 816v-60h720v60H120Zm0-210v-60h720v60H120Zm0-210v-60h720v60H120Z"/></svg></a></li>
    </ul>
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