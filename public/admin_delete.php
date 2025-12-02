<?php

require_once __DIR__ . '/../src/mysqli_compat.php';
session_start();
// Check admin login
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: admin_login.php');
//     exit();
// }
// 9494626126
// 7947110938
// S Venkappa Ayurvedic Store
// 15.4903034,78.4856139
// 1/119a, Opposite Ansar Jeweller's, Nandyal Urban, Main Bazaar, Nandyal Ho-518501
// require_once __DIR__ . '/../config/db.php';

$conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete
if (isset($_POST['delete_service'])) {
    $delete_id = $_POST['delete_id'];
    
   // First delete the image if exists
    $image_paths = [];
            $stmt = $conn->prepare("SELECT image_names,service FROM services WHERE id = ?");
            $stmt->bind_param("i", $delete_id);
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
     foreach ($image_paths as $image_path) {
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $stmt4 = $conn->prepare("SELECT category_count FROM categories WHERE name = ?");
                $stmt4->bind_param("s", $category_name);
                $stmt4->execute();
                $result = $stmt4->get_result();
    
            if ($result->num_rows > 0) {
                $category = $result->fetch_assoc();
                $category_count = $category['category_count'];
                
                if ($category_count == 1) {
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

    
    // Then delete the service
    // $stmt = $conn->prepare("DELETE FROM admin_grant WHERE id = ?");
    // $stmt->bind_param("i", $delete_id);
    // $stmt->execute();
    $stmt2 = $conn->prepare("DELETE FROM providers WHERE id = ?");
    $stmt2->bind_param("i", $delete_id);
    $stmt1 = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt1->bind_param("i", $delete_id);
    
    if ($stmt1->execute() && $stmt2->execute()) {
        $_SESSION['message'] = "Service deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting service: " . $conn->error;
    }
    
    header('Location: admin_delete.php');
    exit();
}
}
}

// Fetch all services with categories
$services = [];
$query = "SELECT * FROM services" ;
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
    $result->free();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
            :root {
                --primary-color: #4361ee;
                --secondary-color: #3f37c9;
                --accent-color: #4895ef;
                --light-color: #f8f9fa;
                --dark-color: #212529;
            }
            
            body {
                background-color: #f5f7fa;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            /* Navbar */
            .navbar {
                background-color: #2c3e50;
                overflow: hidden;
                display: flex;
                justify-content: center;
            }

            .navbar a {
                display: block;
                color: white;
                text-align: center;
                padding: 14px 20px;
                text-decoration: none;
                transition: background 0.3s;
            }

            .navbar a:hover {
                background-color: #1abc9c;
            }
            
            .service-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 2rem;
            }
            
            .service-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
            }
            
            .service-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                gap: 2rem;
            }
            
            .service-card {
                background: white;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                position: relative;
            }
            
            .service-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            }
            
            .service-image {
                height: 200px;
                width: 100%;
                object-fit: cover;
            }
            
            .service-content {
                padding: 1.5rem;
            }
            
            .service-category {
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: var(--primary-color);
                color: white;
                padding: 0.25rem 0.75rem;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
            }
            
            .service-title {
                font-size: 1.25rem;
                font-weight: 600;
                margin-bottom: 0.5rem;
                color: var(--dark-color);
            }
            
            .service-description {
                color: #6c757d;
                margin-bottom: 1rem;
                line-height: 1.5;
            }
            
            .service-actions {
                display: flex;
                justify-content: flex-end;
                padding: 1rem;
                border-top: 1px solid #eee;
            }
            
            .delete-btn {
                background: #ff4757;
                color: white;
                border: none;
                padding: 0.5rem 1rem;
                border-radius: 6px;
                cursor: pointer;
                transition: background 0.2s;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            
            .delete-btn:hover {
                background: #ff6b81;
            }
            
            .empty-state {
                text-align: center;
                padding: 4rem;
                grid-column: 1 / -1;
            }
            
            .empty-state i {
                font-size: 3rem;
                color: #adb5bd;
                margin-bottom: 1rem;
            }
            
            .btn-primary {
                background: var(--primary-color);
                border: none;
                padding: 0.5rem 1.5rem;
            }
            
            .btn-primary:hover {
                background: var(--secondary-color);
            }
</style>
</head>
<body>
    <div class="navbar">
        <a href="admin_dashboard.php">Dashboard Home</a>
        <a href="admin.php">Grant Service</a>
        <a href="upload.php">Bulk Upload Services</a>
        <a href="admin_contact_msg.php">User Messages</a>
        <a href="admin_logout.php" class="logout-btn">Logout</a>
        </div>
    <div class="service-container">
        <div class="service-header">
            <h1>Manage Services</h1>
            <a href="upload.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Service
            </a>
        </div>

        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
unset($_SESSION['message']); ?>
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
        
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
unset($_SESSION['error']); ?>
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>

        <div class="service-grid">
            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (empty($services)): ?>
                <div class="empty-state">
                    <i class="fas fa-concierge-bell"></i>
                    <h3>No Services Found</h3>
                    <p>You haven't added any services yet. Get started by adding your first service.</p>
                    <a href="upload.php" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Add Service
                    </a>
                </div>
            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
else: ?>
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
foreach ($services as $service): ?>
                    <div class="service-card">
                     
                         <?php  
require_once __DIR__ . '/../src/mysqli_compat.php';
$data = json_decode($service['image_names'], true); ?>
                         <div class="service-image" style="background: #eee; display: flex; align-items: center; justify-content: center;">
                                <img src="./uploads/<?= htmlspecialchars($data[0]); ?>" alt="<?= htmlspecialchars($service['bussiness_name']); ?>" class="service-image">
                                <!-- <i class="fas fa-image" style="font-size: 3rem; color: #aaa;"></i> -->
                            </div>

                        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (!empty($service['service'])): ?>
                            <span class="service-category"><?= htmlspecialchars($service['service']); ?></span>
                        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
                        
                        <div class="service-content">
                            <h3 class="service-title"><?= htmlspecialchars($service['bussiness_name']); ?></h3>
                            <p class="service-description"><?= htmlspecialchars($service['about']); ?></p>
                        </div>
                        
                        <div class="service-actions">
                            <form method="POST" action="admin_delete.php">
                                <input type="hidden" name="delete_id" value="<?= $service['id']; ?>">
                                <button type="submit" class="delete-btn" name="delete_service">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endforeach; ?>
            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

