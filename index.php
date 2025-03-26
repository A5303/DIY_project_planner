<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle new project submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_project'])) {
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $status = htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8');
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO projects (title, description, status, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $description, $status, $user_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: index.php?message=Project added successfully!");
    exit();
}

// Fetch projects
$stmt = $conn->prepare("SELECT * FROM projects WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIY Project Hub</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/script.js" defer></script>
</head>
<body>

<h2>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
<button onclick="logout()">Logout</button>

<h3>Add New Project</h3>
<form method="POST">
    <input type="text" name="title" placeholder="Project Title" required>
    <textarea name="description" placeholder="Project Description" required></textarea>
    <select name="status" required>
        <option value="Pending">Pending</option>
        <option value="Completed">Completed</option>
    </select>
    <button type="submit" name="add_project">Add Project</button>
</form>

<h3>Your Projects</h3>
<?php while ($row = $result->fetch_assoc()) { ?>
    <div class="project">
        <h4><?= htmlspecialchars($row['title']); ?></h4>
        <p><?= nl2br(htmlspecialchars($row['description'])); ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($row['status']); ?></p>
        <button onclick="window.location.href='edit.php?id=<?= $row['id']; ?>'">Edit</button>
        <button onclick="confirmDelete(<?= $row['id']; ?>)">Delete</button>
    </div>
<?php } ?>

</body>
</html>
