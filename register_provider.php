<?php
// Start session
session_start();

// DB config
require_once 'db.php';



// Check if POST data is received
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo"<h1>Hello</h1>";
    // Sanitize and collect inputs
    $first_name     = $_POST['first_name'] ?? '';
    $last_name      = $_POST['last_name'] ?? '';
    $email          = $_POST['email'] ?? '';
    $phone          = $_POST['phone'] ?? '';
    $category       = $_POST['category'] ?? '';
    $other_category = $_POST['other_category'] ?? '';
    $business_name  = $_POST['business_name'] ?? '';
    $description    = $_POST['description'] ?? '';
    $location       = $_POST['secondary-location'] ?? '';
    $password_raw   = $_POST['password'] ?? '';

    // Choose correct service
    $service = ($category === 'other') ? $other_category : $category;

    // Hash password
    // $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

    // Handle file upload (photos)
    $uploaded_photos = [];
    if (!empty($_FILES['photos']['name'][0])) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
            $filename = basename($_FILES['photos']['name'][$key]);
            $target_path = $upload_dir . time() . "_" . $filename;

            if (move_uploaded_file($tmp_name, $target_path)) {
                $uploaded_photos[] = $target_path;
            }
        }
    }

    // Convert photo paths to JSON string
    $photos_json = json_encode($uploaded_photos);

    // Insert into admin_grant table (pending approval)
    $sql = "INSERT INTO providers 
            (first_name, last_name,  business_name,email, phone, category, other_category,description, location, password_hash, photos, status) 
            VALUES 
            (:first_name, :last_name, :business_name,:email, :phone, :service, $other_category, :description, :location, :password, :photos, 'pending')";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':first_name'    => $first_name,
        ':last_name'     => $last_name,
        ':business_name' => $business_name,
        ':email'         => $email,
        ':phone'         => $phone,
        ':service'       => $service,
        ':description'   => $description,
        ':location'      => $location,
        ':password'      => $password_raw,
        ':photos'        => $photos_json
    ]);

    // Respond with success
    echo "success";
} else {
    echo "Invalid request.";
}