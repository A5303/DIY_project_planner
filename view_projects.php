<?php
session_start();
include("../includes/config.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch projects for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM projects WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIY Project Portfolio</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #001f3f; /* Dark Blue */
            color: #fff;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        h2 {
            font-weight: 600;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.2);
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            color: #333;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #2f80ed;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        tr:hover {
            background: #ddd;
        }
        .edit {
            color: #fff;
            background: #27ae60;
            padding: 6px 10px;
            border-radius: 5px;
        }
        .edit:hover {
            background: #219150;
        }
        .delete {
            color: #fff;
            background: #e74c3c;
            padding: 6px 10px;
            border-radius: 5px;
        }
        .delete:hover {
            background: #c0392b;
        }
        .links {
            margin-top: 20px;
        }
        .links a {
            text-decoration: none;
            color: #fff;
            background: #2f80ed;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 5px;
            display: inline-block;
            transition: 0.3s;
        }
        .links a:hover {
            background: #2568c4;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">DIY Project Portfolio</h2>

        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Project Name</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr id="project-<?= $row['project_id'] ?>">
                            <td><?= htmlspecialchars($row['project_id']) ?></td>
                            <td><?= htmlspecialchars($row['project_name']) ?></td>
                            <td><?= htmlspecialchars($row['project_description']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td class="text-center">
                                <a href="edit_project.php?project_id=<?= $row['project_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="setDeleteId(<?= $row['project_id'] ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No projects found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-between">
            <a href="add_project.php" class="btn btn-success">➕ Add New Project</a>
            <a href="../dashboard.php" class="btn btn-secondary">🔙 Back to Dashboard</a>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-dark">
                        Are you sure you want to delete this project? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="deleteProject()">Delete</button>
                    </div>
                </div>
            </div>
        </div>

    <script>
        let deleteProjectId = null;

        function setDeleteId(projectId) {
            deleteProjectId = projectId;
        }

        function deleteProject() {
            if (deleteProjectId) {
                $.ajax({
                    url: "delete_project.php",
                    type: "POST",
                    data: { project_id: deleteProjectId },
                    success: function(response) {
                        $("#deleteModal").modal("hide");
                        $("#project-" + deleteProjectId).fadeOut();
                    },
                    error: function() {
                        alert("❌ Error deleting project. Please try again.");
                    }
                });
            }
        }
        
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
