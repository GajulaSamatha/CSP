<?php
session_start();
// session_
require_once 'db.php';

// Check authentication
if (!isset($_SESSION['user_name'])) {
    header("Location: new_provider_login.php");
    exit();
}

// Get service ID
if(!isset($_SESSION['service_id'])){
    $_SESSION['service_id'] = $_GET['id'] ?? 13;
}   
echo($_SESSION['service_id']);
// Verify the service belongs to this provider
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$_SESSION['service_id']]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    // header("Location: dash.php");
    // exit();
    echo($service);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $business_name = trim($_POST['business_name']);
    $category = trim($_POST['category']);
    $about = trim($_POST['about']);
    $telephone = trim($_POST['telephone_number']);
    $whatsapp = trim($_POST['whatsapp_number']);
    $address = trim($_POST['address']);
    $lat = trim($_POST['lat']);
    $lon = trim($_POST['lon']);

    // Update service
    $stmt = $pdo->prepare("
        UPDATE services SET 
        bussiness_name = ?, 
        service = ?, 
        about = ?, 
        telephone_number = ?, 
        whatsapp_number = ?, 
        location = ?, 
        lat = ?, 
        lon = ? 
        WHERE id = ?
    ");

    $stmt1 = $pdo->prepare("
        UPDATE providers SET 
        business_name = ?, 
        category = ?, 
        description = ?, 
        phone_number = ?, 
        whatsapp_number = ?, 
        location = ?, 
        lat = ?, 
        lon = ? 
        WHERE id = ?
    ");
    
    $success = $stmt->execute([
        $business_name,
        $category,
        $about,
        $telephone,
        $whatsapp,
        $address,
        $lat,
        $lon,
        $_SESSION['service_id']
    ]);

    $stmt1->execute([
        $business_name,
        $category,
        $about,
        $telephone,
        $whatsapp,
        $address,
        $lat,
        $lon,
        $_SESSION['service_id']
    ]);

    if ($success) {
        $_SESSION['success_message'] = "Service updated successfully!";
        header("Location: edit_service.php?id=".$_SESSION['service_id']);
        exit();
    } else {
        $error = "Failed to update service. Please try again.";
    }
}

// Get all categories for dropdown
$categories = $pdo->query("SELECT DISTINCT service FROM services WHERE service IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Service</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./provider.css">
    <style>
        select option{
            text-transform:uppercase;
        }
    </style>
</head>
<body>
    <?php include 'new_header.php'; ?>
    
    <div class="provider-container">
        <h1>Edit Service</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="service-form" action="edit_service.php">
            <div class="form-group">
                <label for="business_name">Business Name*</label>
                <input type="text" id="business_name" name="business_name" 
                       value="<?= htmlspecialchars($service['bussiness_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="category">Category*</label>
                <select id="category" name="category" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" 
                            <?= $cat === $service['service'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="about">Description*</label>
                <textarea id="about" name="about" required><?= htmlspecialchars($service['about']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="telephone_number">Phone Number*</label>
                <input type="tel" id="telephone_number" name="telephone_number" 
                       value="<?= htmlspecialchars($service['telephone_number']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="whatsapp_number">WhatsApp Number</label>
                <input type="tel" id="whatsapp_number" name="whatsapp_number" 
                       value="<?= htmlspecialchars($service['whatsapp_number']) ?>">
            </div>
            
            <div class="form-group">
                <label for="address">Business Address*</label>
                <input type="text" id="address" name="address" 
                       value="<?= htmlspecialchars($service['location']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="lat">Latitude</label>
                <input type="text" id="lat" name="lat" 
                       value="<?= htmlspecialchars($service['lat']) ?>">
            </div>
            
            <div class="form-group">
                <label for="lon">Longitude</label>
                <input type="text" id="lon" name="lon" 
                       value="<?= htmlspecialchars($service['lon']) ?>">
            </div>
            
            <button type="submit" class="save-btn">Save Changes</button>
            <a href="dash.php" class="cancel-btn">Cancel</a>
        </form>
    </div>
    
    <?php include 'footer.html'; ?>
    
    <script>
        // Simple address to coordinates conversion (you might want to use Google Maps API)
        document.getElementById('address').addEventListener('blur', function() {
            // In a real app, you would call a geocoding API here
            console.log('Address changed - would geocode here');
        });

        
    </script>
</body>
</html>