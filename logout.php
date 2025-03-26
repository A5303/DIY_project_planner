<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #34495e;
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
            background: #2c3e50;
            padding: 20px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
        }
        h2 {
            font-weight: 600;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.2);
        }
        p {
            font-size: 16px;
            margin-top: 10px;
        }
        .redirect {
            margin-top: 20px;
            font-size: 14px;
            color: #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>✅ Logged Out Successfully</h2>
        <p>You have been logged out. Redirecting to the login page...</p>
        <p class="redirect">If you are not redirected, <a href="../login.php" style="color: #2f80ed;">click here</a>.</p>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = '../login.php';
        }, 0); // Redirect after 3 seconds
    </script>
</body>
</html>
