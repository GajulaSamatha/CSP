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
        body {
            background-color: #e9ebee;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }
        .login-container h2 {
            color: #1877f2;
            text-align: center;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        input[type="submit"] {
            background-color: #1877f2;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <form class="login-container" action="new_login.php" method="POST">
        <h2>Customer Login</h2>
        
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="login" value="Log In">
        <p><a href="new_register_cust.php">Don't Have an account?Register</a></p>
      <p><a href="new_provider_login.php">Login as Provider</a></p>
    </form>
</body>
</html>
