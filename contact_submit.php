<?php
header('Content-Type: application/json');

// 1. Connect to your database (update credentials as needed)
$conn = new mysqli("localhost", "root", "", "nandyal_dial"); // <-- Change to your DB name

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// 2. Get and sanitize input
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$subject || !$message) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// 3. Insert into database
$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $subject, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been received.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save your message.']);
}
$stmt->close();
$conn->close();
?>