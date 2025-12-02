﻿<?php

require_once __DIR__ . '/../vendor/autoload.php';
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Use the secure PDO connection from Db.php
$pdo = App\Db::getConnection();

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password']; // Don't escape passwords - it can corrupt them

    // Prepare query to get customer data
    $sql = "SELECT id, first_name, last_name, password FROM customers WHERE email=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        // SECURE PASSWORD VERIFICATION
        if (password_verify($password, $row['password'])) {
            // Password is correct!
            
            // Check if password needs rehashing (if algorithm/cost changed)
            if (password_needs_rehash($row['password'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateSql = "UPDATE customers SET password=? WHERE id=?";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([$newHash, $row['id']]);
            }
            
            // Set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['first_name']." ".$row['last_name'];
            $_SESSION['user_type'] = 'customer';
            $_SESSION['logged_in'] = true;
            
            // Redirect to index
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid email or password"; // Generic message for security
        }
    } else {
        $error = "Invalid email or password"; // Same message to prevent user enumeration
    }
    
    // If we got here, login failed
    header("Location: new_login.php?error=".urlencode($error));
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Login</title>
    <style>
        /* Page Background */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            padding: 20px;
        }

        /* Login Card */
        .login-container {
            background: #fff;
            padding: 35px 30px;
            border-radius: 20px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Heading */
        .login-container h2 {
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .subtitle {
            font-size: 14px;
            color: #777;
            margin-bottom: 25px;
        }

        /* Input Fields */
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 2px solid #4caf50;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            box-shadow: 0 0 6px rgba(76, 175, 80, 0.5);
            border-color: #45a049;
        }

        /* Submit Button */
        input[type="submit"] {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }

        input[type="submit"]:hover {
            opacity: 0.9;
        }

        /* Links */
        p {
            margin-top: 15px;
            font-size: 14px;
        }

        p a {
            color: #2575fc;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        /* Error Message */
        .error-message {
            color: #d32f2f;
            background-color: #fde8e8;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 25px 20px;
            }
            .login-container h2 {
                font-size: 22px;
            }
            input[type="submit"] {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <form class="login-container" action="new_login.php" method="POST">
        <h2>Customer Login</h2>
        <div class="subtitle">Welcome back! Please log in to continue.</div>
        
        <?php 

if(isset($_GET['error'])): ?>
            <div class="error-message"><?php 

echo htmlspecialchars($_GET['error']); ?></div>
        <?php 

endif; ?>
        
        <input type="email" name="email" placeholder="Enter your Email" required>
        <input type="password" name="password" placeholder="Enter your Password" required>
        <input type="submit" name="login" value="Log In">
        
        <p><a href="forgotpassword.php?role=customers">Forgot Password?</a></p>
        <p><a href="new_register_cust.php">Don't have an account? Register</a></p>
        <p><a href="new_provider_login.php">Login as Service Provider</a></p>
    </form>
</body>
</html>
