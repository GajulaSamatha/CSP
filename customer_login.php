<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
session_start();
require_once 'db.php'; // Contains your DB connection: $pdo

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Check for missing fields
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    try {
        // Query user by email (using 'password' column, not 'password_hash')
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password_hash FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
//         foreach ($user as $key => $value) {
//     echo "$key: $value<br>";
// }

        // Verify password (plain text comparison since passwords are stored as plain text)
        if ($user && $password === $user['password_hash']) {
            // Set session
            $_SESSION['customer_id'] = $user['id'];
            $_SESSION['customer_email'] = $user['email'];
            $_SESSION['customer_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_type'] = 'customer';
            $_SESSION['userID']=$user['id'];

            // Set active=1 for this user
            $update = $pdo->prepare("UPDATE customers SET active=1 WHERE id=?");
            $update->execute([$user['id']]);
            
            echo json_encode(['success' => true, 'redirect' => 'categories.php']);
            // header('Location: index.html');

            exit;
        } else {
            // Set active=0 for this user if email exists
            if ($user) {
                $update = $pdo->prepare("UPDATE customers SET active=0 WHERE id=?");
                $update->execute([$user['id']]);
            }
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
            exit;
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Server error. Please try again later.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
