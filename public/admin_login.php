<?php

require_once __DIR__ . '/../src/mysqli_compat.php';
session_start();

// Hardcoded admin credentials
$ADMIN_USERNAME = 'admin';
$ADMIN_PASSWORD = '11011'; // In production, never use simple passwords like this

// Brute force protection
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Maximum allowed attempts
$MAX_ATTEMPTS = 5;

if ($_SESSION['login_attempts'] >= $MAX_ATTEMPTS) {
    die("Too many login attempts. Please try again later.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Input validation
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        // Verify credentials
        if ($username === $ADMIN_USERNAME && $password === $ADMIN_PASSWORD) {
            // Successful login
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_attempts'] = 0; // Reset attempts
            $_SESSION['username'] = $username;
            header("Location: admin.php");
            exit();
        } else {
            // Failed attempt
            $_SESSION['login_attempts']++;
            $error = "Invalid username or password. Attempts remaining: " . ($MAX_ATTEMPTS - $_SESSION['login_attempts']);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-form {
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        .login-form h2 {
            margin-top: 0;
            color: #333;
        }
        .login-form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .login-form button {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 4px;
            cursor: pointer;
        }
        .login-form button:hover {
            background: #1abc9c;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Admin Login</h2>
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>

