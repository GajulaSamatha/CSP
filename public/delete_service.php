<?php

require_once __DIR__ . '/../src/mysqli_compat.php';
// delete_service.php
session_start();

// Check admin login (uncomment in production)
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: admin_login.php');
//     exit();
// }

require_once __DIR__ . '/../config/db.php';

// Create database connection
$conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID parameter exists in URL
if (isset($_GET['id'])) {
    $service_id = intval($_GET['id']);
    
    if ($service_id > 0) {
        // First delete the image if exists
         $image_paths = [];
            $stmt = $conn->prepare("SELECT image_names,service FROM services WHERE id = ?");
            $stmt->bind_param("i", $service_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $category_name;
            if ($result->num_rows > 0) {
                $service = $result->fetch_assoc();
                $category_name=$service['service'];
                if (!empty($service['image_names'])) {
                    $image_data = json_decode($service['image_names'], true);
                    if (is_array($image_data)) {
                        foreach ($image_data as $image_name) {
                            $image_path = "./uploads/" . $image_name;
                            if (file_exists($image_path)) {
                                $image_paths[] = $image_path;
                            }
                        }
                    }
                }
                
                
                $stmt4 = $conn->prepare("SELECT category_count FROM categories WHERE name = ?");
                $stmt4->bind_param("s", $category_name);
                $stmt4->execute();
                $result = $stmt4->get_result();
    
            if ($result->num_rows > 0) {
                $category = $result->fetch_assoc();
                $category_count = $category['category_count'];
                
                if ($category_count == 0) {
                    // Delete the category if count is 0
                    $delete_stmt = $conn->prepare("DELETE FROM categories WHERE name=?");
                    $delete_stmt->bind_param("s", $category_name);
                    $success = $delete_stmt->execute();
                    $delete_stmt->close();
                    
                    return $success ? "Category deleted successfully" : "Error deleting category";
                } else {
                    // Decrease the count if it's greater than 0
                    $update_stmt = $conn->prepare("UPDATE categories SET category_count = category_count - 1 WHERE name=?");
                    $update_stmt->bind_param("s", $category_name);
                    $success = $update_stmt->execute();
                    $update_stmt->close();
                }

                $stmt1 = $conn->prepare("DELETE FROM admin_grant WHERE id = ?");
                $stmt1->bind_param("i", $service_id);
                $stmt1->execute();
                $stmt1->close();
            
            
            // 3. Delete from providers table
            $stmt2 = $conn->prepare("DELETE FROM providers WHERE id = ?");
            $stmt2->bind_param("i", $service_id);
            $stmt2->execute();
            
           
            $stmt2->close();
            
            // 4. Delete from services table
            $stmt3 = $conn->prepare("DELETE FROM services WHERE id = ?");
            $stmt3->bind_param("i", $service_id);
            $stmt3->execute();
           
            $stmt3->close();
            
            // 5. Delete associated image files
            foreach ($image_paths as $image_path) {
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            // Commit the transaction if all queries succeeded
           
            
            $_SESSION['message'] = "Service deleted successfully";
   
    }
 } else {
        $_SESSION['error'] = "Invalid service ID";
    }
} else {
    $_SESSION['error'] = "No service ID provided";
}

// Close connection
$conn->close();

// Redirect to dashboard
header('Location: dash.php');
exit();
?>

