<?php
session_start();
include("../includes/config.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Initialize message variable
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_name = htmlspecialchars(trim($_POST['project_name']));
    $description = htmlspecialchars(trim($_POST['description']));
    $user_id = $_SESSION['user_id'];

    if (!empty($project_name)) {
        // Check for duplicate project names
        $check_sql = "SELECT project_id FROM projects WHERE project_name = ? AND user_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $project_name, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "⚠️ A project with this name already exists!";
        } else {
            // Insert new project into database
            $sql = "INSERT INTO projects (project_name, project_description, user_id) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $project_name, $description, $user_id);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "✅ Project added successfully!";
                header("Location: view_projects.php"); // Redirect to project list
                exit();
            } else {
                $message = "❌ Error adding project: " . $conn->error;
            }
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
    <title>Add Project</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../js/script.js" defer></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #2c3e50;
            color: #ecf0f1;
            text-align: center;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 50%;
            margin: auto;
            background: #34495e;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
        }

        h1 {
            font-weight: 600;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.2);
            color: #1abc9c;
        }

        .message {
            color: #e74c3c;
            font-weight: bold;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0 5px;
            color: #ecf0f1;
        }

        input, textarea {
            width: 90%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            background: #ecf0f1;
            color: #333;
        }

        button {
            background: #1abc9c;
            color: white;
            font-size: 16px;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #16a085;
        }

        .links {
            margin-top: 20px;
        }

        .links a {
            text-decoration: none;
            color: white;
            background: #2f80ed;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
            font-weight: bold;
            margin: 5px;
        }

        .links a:hover {
            background: #2568c4;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Add a New Project</h1>
        
        <?php if (!empty($message)): ?>
            <p class="message"><?= $message; ?></p>
        <?php endif; ?>

        <form action="" method="post">
            <label for="project_name">Project Name:</label>
            <input type="text" name="project_name" required>

            <label for="description">Project Description:</label>
            <textarea name="description" rows="3" required></textarea>

            <button type="submit">Add Project</button>
        </form>

        <div class="links">
            <a href="view_projects.php">View All Projects</a> 
            <a href="../dashboard.php">Back to Dashboard</a>
        </div>
    </div>

</body>
</html>
