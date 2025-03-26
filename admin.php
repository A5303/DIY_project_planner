<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php"); // Redirect non-admins to admin login
    exit();
}
header("Location: admin_dashboard.php");
exit();
?>
