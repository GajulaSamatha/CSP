<?php
session_start();
require_once 'db.php'; // Your database connection file

// Check if user is logged in and is a provider
if (!isset($_SESSION['user_name'])) {
    header("Location: new_provider_login.php");
    exit();
}

// Get provider's services

// $name_parts = explode(" ", $_SESSION['user_name']);

// $first_name = $name_parts[0];
// $last_name = $name_parts[1] ?? ''; // Using null coalescing in case there's no last name

$stmt = $pdo->prepare("SELECT email FROM providers WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$provider_email = $stmt->fetchAll(PDO::FETCH_ASSOC);
$services='';
foreach ($provider_email as $s){

$stmt2 = $pdo->prepare("SELECT * FROM services WHERE email=?");
$stmt2->execute([$s['email']]);
$services = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provider Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./provider.css">
</head>
<body>
    <?php include 'new_header.php'; ?>
    
    <div class="provider-container">
        <h1>My Services</h1>
        
        <a href="new_add_services.php" class="add-service-btn">
            <i class="fas fa-plus"></i> Add New Service
        </a>
        
        <div class="services-list">
            <?php if (empty($services)): ?>
                <p class="no-services">You haven't added any services yet.</p>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                    <div class="service-card">
                        <div class="service-header">
                            <h3><?= htmlspecialchars($service['bussiness_name']) ?></h3>
                            <div class="service-actions">
                                <a href="edit_service.php?id=<?= $service['id'] ?>" class="edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete_service.php?id=<?= $service['id'] ?>" class="delete-btn" >
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                        
                        <div class="service-details">
                            <p><strong>Category:</strong> <?= htmlspecialchars($service['service']) ?></p>
                            <p><strong>Description:</strong> <?= htmlspecialchars($service['about']) ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($service['telephone_number']) ?></p>
                            <?php if (!empty($service['whatsapp_number'])): ?>
                                <p><strong>WhatsApp:</strong> <?= htmlspecialchars($service['whatsapp_number']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'footer.html'; ?>
</body>
</html>