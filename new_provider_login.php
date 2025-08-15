<?php
session_start();

// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=nandyal_dial", "root", "1234");

// Handle login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM providers WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password===$user['password']) {
             $_SESSION['user_name'] = $user['first_name']." ".$user['last_name'];
             $_SESSION['user_id'] = $user['id'];
            // $_SESSION['service_provider_id'] = $user['id'];
            // $_SESSION['service_provider_name'] = $user['business_name'];
            header("Location: index.php");
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Provider Login</title>
   <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
    }
    .login-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 400px;
        padding: 30px;
        text-align: center;
        animation: fadeIn 0.6s ease-in-out;
    }
    h1 {
        color: #6a11cb;
        margin-bottom: 20px;
        font-size: 24px;
    }
    .form-group {
        margin-bottom: 20px;
        text-align: left;
    }
    label {
        display: block;
        margin-bottom: 8px;
        color: #555;
        font-weight: 500;
    }
    input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 16px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    input:focus {
        border-color: #6a11cb;
        box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        outline: none;
    }
    .error {
        color: #e74c3c;
        margin-bottom: 15px;
        font-size: 14px;
    }
    .btn {
        background: linear-gradient(90deg, #6a11cb, #2575fc);
        color: white;
        border: none;
        padding: 12px;
        width: 100%;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.4s ease;
    }
    .btn:hover {
        background: linear-gradient(90deg, #5b0db8, #1f64d9);
    }
    .links {
        margin-top: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .links a {
        color: #6a11cb;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s;
    }
    .links a:hover {
        color: #2575fc;
        text-decoration: underline;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    /* Mobile adjustments */
    @media (max-width: 480px) {
        .login-container {
            padding: 20px;
        }
        h1 {
            font-size: 20px;
        }
    }
</style>

</head>
<body>
    <div class="login-container">
        <!-- <div class="logo">
            <img src="logo.png" alt="Nandyal Dial Logo">
        </div> -->
        
        <h1>Service Provider Login</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="new_provider_login.php">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" name="login" class="btn">Login</button>
        </form>
        
        <div class="divider"></div>
        
        <div class="links">
            <!-- <a href="forgot_password.php">Forgot Password?</a> -->
            <a href="new_register_prov.php">Don't have an account? Register</a>
            <a href="new_login.php">Login as Customer</a>
        </div>
    </div>
</body>
</html>