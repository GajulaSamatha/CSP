<?php
// DB connection
$host = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "nandyal_dial"; // replace this with your DB name

$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch form data
$firstName = $_POST['first_name'] ?? '';
$lastName = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$location = $_POST['primary-location'] ?? '';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validate required fields
if (
    empty($firstName) || empty($lastName) || empty($email) ||
    empty($phone) || empty($location) || empty($password) || empty($confirmPassword)
) {
    die("All fields are required.");
}

// Check if passwords match
if ($password !== $confirmPassword) {
    die("Passwords do not match.");
}


// Insert into database
$sql = "INSERT INTO customers (first_name, last_name, email, phone, location, password_hash) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $firstName, $lastName, $email, $phone, $location, $password);

if ($stmt->execute()) {
    // Redirect to index page after successful registration
    $stmt->close();
    $conn->close();
    header('Location: index.html');
    exit();
} else {
    echo("Statement not executed");
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>




