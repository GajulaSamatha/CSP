<?php

require_once __DIR__ . '/../src/mysqli_compat.php';
session_start();
require_once __DIR__ . '/../config/db.php'; // Your DB connection

// Get logged-in customer ID from session
$customer_id = $_SESSION['customer_id'] ?? null;
if (!$customer_id) {
    die("Please login to rate a service.");
}

// Get form data
$service_id = intval($_POST['service_id']);
$rating = intval($_POST['rating']);
$message = trim($_POST['message']);

if ($rating < 1 || $rating > 5) {
    die("Invalid rating value.");
}

// Check if customer already rated this service
$sql = "SELECT id FROM ratings WHERE service_id = ? AND customer_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$service_id, $customer_id]);

if ($stmt->rowCount() > 0) {
    // Update existing rating
    $sql = "UPDATE ratings SET rating = ?, message = ?, updated_at = NOW()
            WHERE service_id = ? AND customer_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$rating, $message, $service_id, $customer_id]);
    echo "Rating updated successfully!";
    header('Location: alt_new_service_profile.php?id=' . $service_id);

} else {
    // Insert new rating
    $sql = "INSERT INTO ratings (service_id, customer_id, rating, message)
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$service_id, $customer_id, $rating, $message]);
    echo "Rating submitted successfully!";
    header('Location: alt_new_service_profile.php?id=' . $service_id);

}
?>


