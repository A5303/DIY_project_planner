<?php
session_start();
include("includes/config.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if 'role' is set before using it
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <a href="admin.php">Admin Panel</a>
<?php endif;

// Fetch projects from database
$sql = "SELECT project_id, project_name, project_description, completion_percentage FROM projects WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("❌ SQL Prepare Error: " . $conn->error); // Debugging output
}

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIY Project Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
        font-family: 'Poppins', sans-serif;
        background: #f4f4f4;
        margin: 0;
        padding: 20px;
        text-align: center;
    }
    .sidebar {
        width: 250px;
        background: #2c3e50;
        color: white;
        padding: 20px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
    }
    .sidebar h2 {
        text-align: center;
    }
    .sidebar a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 12px;
        margin: 5px 0;
        background: #34495e;
        border-radius: 5px;
        text-align: center;
        transition: 0.3s;
    }
    .sidebar a:hover {
        background: #1abc9c;
    }
    .content {
        margin-left: 270px;
        padding: 20px;
    }
    .welcome-message {
        color: #2c3e50; /* Dark blue text */
        font-size: 28px;
        font-weight: bold;
        text-align: center;
        margin-top: 20px;
        margin-bottom: 10px;
        text-transform: uppercase;
    }
    .search-bar {
        width: 80%;
        max-width: 400px;
        padding: 12px;
        border: 2px solid #2c3e50;
        border-radius: 25px;
        font-size: 16px;
        outline: none;
        text-align: center;
        transition: 0.3s;
    }
    .search-bar:focus {
        border-color: #1abc9c;
        box-shadow: 0px 0px 10px rgba(26, 188, 156, 0.5);
    }
    .project-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        padding: 20px;
    }
    .project-card {
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        transition: 0.3s;
        position: relative;
    }
    .project-card:hover {
        transform: scale(1.02);
    }
    .project-title {
        font-size: 18px;
        font-weight: bold;
    }
    .project-desc {
        font-size: 14px;
        color: gray;
        margin-top: 5px;
    }
    .progress-bar {
        width: 100%;
        height: 8px;
        background: #ddd;
        border-radius: 5px;
        margin-top: 10px;
        position: relative;
    }
    .progress-fill {
        height: 100%;
        border-radius: 5px;
        background: #1abc9c;
    }
    .progress-btn {
        background: #3498db;
        color: white;
        padding: 8px 12px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        transition: 0.3s;
        margin-top: 10px;
    }
    .progress-btn:hover {
        background: #2980b9;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
    }
    .modal-content {
        background-color: white;
        margin: 10% auto;
        padding: 20px;
        border-radius: 10px;
        width: 40%;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        text-align: center;
    }
    .close {
        float: right;
        font-size: 24px;
        cursor: pointer;
    }
    .close:hover {
        color: red;
    }

    /* Modal Styling */
    #progressModal {
        display: none; /* Initially hidden */
        position: fixed;
        z-index: 1000;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 40%;
        background-color: white;
        padding: 20px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
    }

    /* Close button */
    #progressModal .close {
        position: absolute;
        right: 15px;
        top: 10px;
        font-size: 20px;
        cursor: pointer;
    }

    /* Input field */
    #progressModal input {
        width: 50px;
        text-align: center;
        padding: 5px;
        margin-right: 10px;
    }

    /* Save button */
    #progressModal button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 5px;
    }

    #progressModal button:hover {
        background-color: #0056b3;
    }

    </style>
</head>
<body>
    <div class="sidebar">
        <h2>DIY Project Hub</h2>
        <a href="actions/add_project.php">Add Project</a>
        <a href="actions/view_projects.php">View Projects</a>
        <a href="actions/logout.php">Logout</a>
    </div>
    <div class="content">

    <h1 class="welcome-message">
    Welcome to DIY Project Hub, <?= strtoupper(htmlspecialchars($_SESSION['username'] ?? 'GUEST')); ?>
    </h1>
    <input type="text" id="search" class="search-bar" placeholder="Search projects..." onkeyup="filterProjects()">

        <h2>DIY Project Portfolio</h2>
        <div class="project-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="project-card" data-name="<?= strtolower(htmlspecialchars($row['project_name'])) ?>">
                    <div class="project-title"><?= htmlspecialchars($row['project_name']) ?></div>
                    <div class="project-desc"><?= htmlspecialchars($row['project_description']) ?></div>

                    <!-- Progress Bar -->
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $row['completion_percentage'] ?>%;"></div>
                    </div>
                    <p><?= $row['completion_percentage'] ?>% Completed</p>

                    <!-- Progress Button -->
                    <button class="progress-btn" onclick="openProgressModal(<?= $row['project_id'] ?>)">Progress</button>

                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Progress Modal -->
    <div id="progressModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeProgressModal()">&times;</span>
            <h2>Update Progress</h2>
            <form id="progressForm" action="actions/update_project.php" method="POST">
                <input type="hidden" name="project_id" id="progress_project_id">
                <label>Completion (%):</label>
                <input type="number" name="completion_percentage" id="progress_completion" min="0" max="100" required>
                <button type="submit">Save Progress</button>
            </form>
        </div>
    </div>
    <script src="js/favorite.js"></script>
    <script>
        function openProgressModal(id) {
            document.getElementById('progress_project_id').value = id;
            document.getElementById('progressModal').style.display = 'block';
        }
        function closeProgressModal() {
            document.getElementById('progressModal').style.display = 'none';
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("search").addEventListener("keyup", filterProjects);
        });
    </script>
        <script>
            function filterProjects() {
    let searchInput = document.getElementById("search").value.toLowerCase();
    let projectCards = document.querySelectorAll(".project-card");

    projectCards.forEach(card => {
        let projectName = card.querySelector(".project-title").innerText.toLowerCase();
        if (projectName.includes(searchInput)) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });
}
        </script>
</body>
</html>
