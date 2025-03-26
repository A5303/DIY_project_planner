<?php
$servername = "localhost";  // Server is localhost when using XAMPP
$username = "root";         // Default MySQL username in XAMPP
$password = "";             // Default password is empty
$database = "diy_tracker";  // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
