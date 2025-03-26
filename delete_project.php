<?php
session_start();
include("../includes/config.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit();
    }

    if (!isset($_POST['project_id']) || empty($_POST['project_id'])) {
        echo json_encode(["status" => "error", "message" => "Invalid request"]);
        exit();
    }

    $project_id = intval($_POST['project_id']);
    $user_id = $_SESSION['user_id'];

    // Delete project only if it belongs to the logged-in user
    $sql = "DELETE FROM projects WHERE project_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $project_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Project deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete project"]);
    }

    $stmt->close();
    $conn->close();
}
?>
