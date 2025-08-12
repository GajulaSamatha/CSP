<?php
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
        // Query provider by email
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, business_name, email, password_hash FROM providers WHERE email = ?");
        $stmt->execute([$email]);
        $provider = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password (assuming password is stored as plain text based on register_provider.php)
        if ($provider && $password === $provider['password_hash']) {
            // Set session
            $_SESSION['provider_id'] = $provider['id'];
            $_SESSION['provider_email'] = $provider['email'];
            $_SESSION['provider_name'] = $provider['first_name'] . ' ' . $provider['last_name'];
            $_SESSION['business_name'] = $provider['business_name'];
            $_SESSION['user_type'] = 'provider';

            // Set active=1 for this provider
            $update = $pdo->prepare("UPDATE providers SET active=1 WHERE id=?");
            $update->execute([$provider['id']]);

            echo json_encode(['success' => true, 'redirect' => 'index.html', 'message' => 'Successfully logged in']);
            header(Location:index.html);
            exit();
        } else {
            // Set active=0 for this provider if email exists
            if ($provider) {
                $update = $pdo->prepare("UPDATE providers SET active=0 WHERE id=?");
                $update->execute([$provider['id']]);
            }
            echo json_encode(['success' => false, 'message' => 'Login unsuccessful']);
            exit();
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Server error. Please try again later.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
?>