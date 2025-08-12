<?php
// Database configuration
$host = 'localhost';             // Database host (usually localhost)
$db   = 'nandyal_dial';         // Your database name
$user = 'root';          // Your MySQL username
$pass = '1234';      // Your MySQL password
$charset = 'utf8mb4';            // Character set

// Create DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO options for security and performance
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Return results as associative arrays
];

try {
    // Create PDO instance (connect to DB)
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Handle connection error
    die("Database connection failed: " . $e->getMessage());
}
