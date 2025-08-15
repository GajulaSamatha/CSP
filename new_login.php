<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare query
    $sql = "SELECT first_name, last_name, password FROM customers WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($password === $row['password']) {
            $_SESSION['user_name'] = $row['first_name']." ".$row['last_name'];
            $_SESSION['user_id'] = $row['id'];
            header("Location: index.php");
            exit();
        } else {
            ?><script>alert("Password is incorrect");</script><?php
        }
    } else {
        ?><script>alert("Invalid email or password.");</script><?php
    }

    header("Location: new_login.php");
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
        
        <input type="email" name="email" placeholder="Enter your Email" required>
        <input type="password" name="password" placeholder="Enter your Password" required>
        <input type="submit" name="login" value="Log In">
        
        <p><a href="new_register_cust.php">Don’t have an account? Register</a></p>
        <p><a href="new_provider_login.php">Login as Provider</a></p>
    </form>
</body>
</html>
