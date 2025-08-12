<?php
require_once 'db.php';

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $rating = trim($_POST['rating'] ?? '');
    $available = isset($_POST['available']) ? 1 : 0;
    $phone = trim($_POST['PhoneNumber'] ?? '');

    // Handle image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid('service_', true) . '.' . $ext;
        // Make sure the uploads directory exists and is writable
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$image");
    }

    // Validate required fields
    if ($name && $category && $location && $price && $phone) {
        $sql = "INSERT INTO services (name, category, location, price, description, rating, available, image, PhoneNumber)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([
                $name,
                $category,
                $location,
                $price,
                $description ?: null,
                $rating ?: null,
                $available,
                $image,
                $phone
            ]);
            $success = "Service added successfully!";
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<?php if ($success): ?><div class="success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

