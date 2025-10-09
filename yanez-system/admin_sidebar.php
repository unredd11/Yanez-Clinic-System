
<?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
<aside class="sidebar" id="sidebar">
    <ul class="nav-links">
        <li onclick=hideSidebar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" fill= "#FFF" height="26" viewBox="0 96 960 960" width="26"><path d="m249 849-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z"/></svg></a></li>
        <li><a href="admin_dashboard.php">🏠︎ Dashboard</a></li>
        <li><a href="admin_viewappointment.php">📅 View Appointments</a></li>
        <li><a href="admin_payments.php">💳 Billing and Payments</a></li>
        <li><a href="admin_usermanagement.php">👥 User Management</a></li>
        <li><a href="admin_reports.php">📊 Reports</a></li>
    </ul>
</aside>
<?php endif; ?>
