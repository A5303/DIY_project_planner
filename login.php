<?php
session_start();
include 'includes/config.php'; // Database connection

$message = ""; // Store messages

// CSRF Protection: Generate Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Check CSRF token validity
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $message = "❌ Invalid request. Please try again.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email format! Please enter a valid email.";
    } else {
        // Check if user exists
        $query = "SELECT id, username, password, role FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                // Secure session storage
                session_regenerate_id(true);
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                // Redirect based on role
                header("Location: " . ($row['role'] === 'admin' ? "admin_dashboard.php" : "dashboard.php"));
                exit();
            } else {
                $message = "❌ Incorrect password! Please try again.";
            }
        } else {
            $message = "❌ Email not found! Please register first.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: #fff;
            text-align: center;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            font-weight: 600;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.2);
        }

        .diy-message {
            font-size: 16px;
            font-weight: bold;
            color: #ffcc00;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            display: inline-block;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .input-container {
            position: relative;
            width: 100%;
            margin-bottom: 15px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            outline: none;
            background: #2c3e50;
            color: #fff;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #ffcc00;
        }

        button {
            background: #2f80ed;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: bold;
        }

        button:hover {
            background: #1c5ecf;
            transform: scale(1.05);
        }

        .message {
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            display: inline-block;
        }

        .success {
            color: #00ff00;
        }

        .error {
            color: #ff4d4d;
        }

        p a {
            color: #ffcc00;
            font-weight: bold;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <!-- DIY Message -->
        <p class="diy-message">DIY Project Hub: Plan your work and stay productive!</p>

        <!-- Display message -->
        <?php if (!empty($message)) : ?>
            <p class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>"><?= $message; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <div class="input-container">
                <input type="email" name="email" placeholder="Enter Email" required>
            </div>

            <div class="input-container">
                <input type="password" name="password" id="password" placeholder="Enter Password" required>
                <span class="toggle-password" onclick="togglePassword()"></span>
            </div>

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register Here</a></p>
    </div>

    <script>
        function togglePassword() {
            let passwordInput = document.getElementById("password");
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
