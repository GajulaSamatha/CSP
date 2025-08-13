<?php
session_start();
require_once 'db.php'; // Your database connection file

// Check if user is logged in and is a provider
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'provider') {
    header("Location: login.php");
    exit();
}

// Get provider's services
$provider_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM services WHERE provider_id = ?");
$stmt->execute([$provider_id]);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provider Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/provider.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="provider-container">
        <h1>My Services</h1>
        
        <a href="add_service.php" class="add-service-btn">
            <i class="fas fa-plus"></i> Add New Service
        </a>
        
        <div class="services-list">
            <?php if (empty($services)): ?>
                <p class="no-services">You haven't added any services yet.</p>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                    <div class="service-card">
                        <div class="service-header">
                            <h3><?= htmlspecialchars($service['business_name']) ?></h3>
                            <div class="service-actions">
                                <a href="edit_service.php?id=<?= $service['id'] ?>" class="edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete_service.php?id=<?= $service['id'] ?>" class="delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this service?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                        
                        <div class="service-details">
                            <p><strong>Category:</strong> <?= htmlspecialchars($service['category']) ?></p>
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
    
    <?php include 'footer.php'; ?>
</body>
</html>