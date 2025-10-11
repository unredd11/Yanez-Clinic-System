
<?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>

<aside class="sidebar" id="sidebar">
    <ul class="nav-links">
        <li><a href="admin_dashboard.php">🏠︎ Dashboard</a></li>
        <li><a href="admin_viewappointment.php">📅 View Appointments</a></li>
        <li><a href="admin_payments.php">💳 Billing and Payments</a></li>
        <li><a href="admin_usermanagement.php">👥 User Management</a></li>
        <li><a href="admin_reports.php">📊 Reports</a></li>
    </ul>
</aside>
<?php endif; ?>
