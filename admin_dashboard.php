<?php
session_start();
include 'includes/config.php'; // Database connection

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch admin details
$admin_id = $_SESSION['user_id'];
$admin_name = $_SESSION['username'];

// Logout logic
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #34495e;
            color: white;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background: #2c3e50;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
        }
        .header {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background: #2f80ed;
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .nav {
            padding: 10px;
            background: #1a252f;
        }
        .nav a {
            color: white;
            text-decoration: none;
            margin: 10px;
            font-weight: bold;
        }
        .nav a:hover {
            text-decoration: underline;
        }
        .btn-logout {
            background: red;
            padding: 10px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn-logout:hover {
            background: darkred;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h2>Welcome, <?= htmlspecialchars($admin_name); ?> (Admin)</h2>
            <a href="admin_dashboard.php?logout=true" class="btn-logout">Logout</a>
        </div>

        <div class="nav">
            <a href="#">Manage Users</a>
            <a href="#">View Reports</a>
            <a href="#">Settings</a>
        </div>

        <h3>Admin Dashboard Overview</h3>
        <p>Manage users, monitor activity, and update settings.</p>
    </div>

</body>
</html>
