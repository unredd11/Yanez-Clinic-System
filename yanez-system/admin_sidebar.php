<?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin','staff','doctor'])): ?>
<aside class="sidebar" id="sidebar">
    <ul class="nav-links">
        <li><a href="admin_dashboard.php">🏠︎ Dashboard</a></li>
        <li><a href="admin_viewappointment.php">📅 View Appointments</a></li>
        <li><a href="admin_patients.php">🧑 Patients</a></li>
        <li><a href="admin_payments.php">💳 Billing and Payments</a></li>
        <li><a href="admin_usermanagement.php">👥 User Management</a></li>
        <li><a href="admin_generatereports.php">📊 Generate Reports</a></li>
    </ul>
</aside>
<?php endif; ?>
