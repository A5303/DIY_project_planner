<?php
session_start();
include("../includes/config.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if project_id is set
if (!isset($_GET['project_id']) || empty($_GET['project_id'])) {
    header("Location: view_projects.php");
    exit();
}

$project_id = intval($_GET['project_id']);
$message = "";

// Fetch project details
$sql = "SELECT project_name, project_description FROM projects WHERE project_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $project_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "⚠️ Project not found!";
    header("Location: view_projects.php");
    exit();
}

$project = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_project_name = htmlspecialchars(trim($_POST['project_name']));
    $new_description = htmlspecialchars(trim($_POST['project_description']));

    if (!empty($new_project_name)) {
        $update_sql = "UPDATE projects SET project_name = ?, project_description = ? WHERE project_id = ? AND user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssii", $new_project_name, $new_description, $project_id, $_SESSION['user_id']);

        if ($update_stmt->execute()) {
            $_SESSION['success_message'] = "✅ Project updated successfully!";
            header("Location: view_projects.php");
            exit();
        } else {
            $message = "❌ Error updating project: " . $conn->error;
        }
    } else {
        $message = "⚠️ Project name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
    
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #0a1931; /* Dark blue background */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container */
        .container {
            background: #162447; /* Slightly lighter blue */
            padding: 25px;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: #fff; /* White text */
        }

        /* Headings */
        h1 {
            color: #f8f9fa;
            margin-bottom: 15px;
        }

        /* Messages */
        .message {
            color: #ffcc00;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #1f4068;
            background-color: #1b1f3b;
            color: white;
            border-radius: 5px;
            font-size: 16px;
        }

        input:focus, textarea:focus {
            outline: none;
            border: 1px solid #00a8cc;
        }

        /* Button */
        button {
            margin-top: 15px;
            padding: 10px;
            font-size: 16px;
            background-color: #00a8cc;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
        }

        button:hover {
            background-color: #007ea7;
        }

        /* Links */
        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #00a8cc;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
            color: #007ea7;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Edit Project</h1>
        
        <?php if (!empty($message)): ?>
            <p class="message"><?= $message; ?></p>
        <?php endif; ?>

        <form action="" method="post">
            <label for="project_name">Project Name:</label>
            <input type="text" name="project_name" value="<?= htmlspecialchars($project['project_name']); ?>" required>

            <label for="project_description">Project Description:</label>
            <textarea name="project_description" rows="3" required><?= htmlspecialchars($project['project_description']); ?></textarea>

            <button type="submit">Update Project</button>
        </form>

        <a href="view_projects.php">Back to Projects</a> | 
        <a href="../dashboard.php">Dashboard</a>
    </div>

</body>
</html>
