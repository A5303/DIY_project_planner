<?php
session_start();
include("../includes/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = intval($_POST['project_id']);
    $user_id = $_SESSION['user_id'];

    // Check if already favorited
    $checkQuery = "SELECT * FROM favorites WHERE user_id = ? AND project_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $user_id, $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Remove from favorites
        $deleteQuery = "DELETE FROM favorites WHERE user_id = ? AND project_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("ii", $user_id, $project_id);
        $stmt->execute();
        echo json_encode(["status" => "success", "favorited" => false]);
    } else {
        // Add to favorites
        $insertQuery = "INSERT INTO favorites (user_id, project_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ii", $user_id, $project_id);
        $stmt->execute();
        echo json_encode(["status" => "success", "favorited" => true]);
    }
}
?>
