<?php
session_start();
include("../includes/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['project_id']) && isset($_POST['completion_percentage'])) {
    $project_id = intval($_POST['project_id']);
    $completion_percentage = intval($_POST['completion_percentage']);
    
    if (!isset($_SESSION['user_id'])) {
        die("Unauthorized access");
    }
    
    $user_id = $_SESSION['user_id'];

    // Prepare and execute the update query
    $sql = "UPDATE projects SET completion_percentage = ? WHERE project_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("iii", $completion_percentage, $project_id, $user_id);

    if ($stmt->execute()) {
        // Redirect to the previous page or dashboard after success
        header("Location: " . $_SERVER['HTTP_REFERER']); // Redirect back to the previous page
        exit();
    } else {
        echo "Error updating project: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
