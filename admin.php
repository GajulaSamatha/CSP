<?php
require 'db.php';

if (isset($_GET['approve'])) {
    $id = $_GET['approve'];

    // Fetch the provider info
    $stmt = $pdo->prepare("SELECT * FROM admin_grant WHERE id=?");
    $stmt->execute([$id]);
    $provider = $stmt->fetch();

    if ($provider) {
        // Insert into main service_providers table
        $update = $pdo->prepare("UPDATE providers SET status='approved' WHERE id=?");
        $update->execute([$id]);
        $update2 = $pdo->prepare("UPDATE admin_grant SET status='accepted' WHERE id=?");
        $update2->execute([$id]);
        $insert=$pdo->prepare("INSERT INTO services (
        id,  email, telephone_number, whatsapp_number,
        bussiness_name, service, about, location, lat, lon, image_names
    )
    SELECT 
        p.id, p.email, p.phone_number, p.whatsapp_number,
        p.business_name, p.category, p.description, p.location, p.lat, p.lon, p.image_names
    FROM providers p WHERE p.id=?");
    $insert->execute([$id]);

      
        $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $checkStmt->execute([$provider['category']]);
        
        if ($checkStmt->rowCount() > 0) {
            // Category exists - increment count
            $updateStmt = $pdo->prepare(
                "UPDATE categories SET category_count = category_count + 1 
                 WHERE name = ?"
            );
            $updateStmt->execute([$provider['category']]);
            return "Category count incremented successfully";
        } else {
            // New category - insert with count 1
            $insertStmt = $pdo->prepare(
                "INSERT INTO categories (name, description,category_count) 
                 VALUES (?, ?,1)"
            );
            $insertStmt->execute([$provider['category'],$provider['description']]);
            return "New category added successfully";
        }

        // Delete from pending table
        $pdo->prepare("DELETE FROM admin_grant WHERE id=?")->execute([$id]);
    }
}

if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $pdo->prepare("DELETE FROM admin_grant WHERE id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM providers WHERE id=?")->execute([$id]);
}

// Fetch all pending registrations
$pending = $pdo->query("SELECT * FROM admin_grant WHERE status='pending'")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Approval Panel</title>
    <style>
            body {
              font-family: Arial, sans-serif;
              background: #f4f4f4;
              padding: 20px;
            }

            h2 {
              color: #333;
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

            table {
              width: 100%;
              background: white;
              border-collapse: collapse;
              margin-top: 20px;
              box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }

            th, td {
              border: 1px solid #ddd;
              padding: 10px;
            }

            .approve-btn {
              color: green;
              text-decoration: none;
              font-weight: bold;
            }

            .reject-btn {
              color: red;
              text-decoration: none;
              font-weight: bold;
            }

    </style>
</head>
<body>
  <div class="navbar">
    <a href="admin_dashboard.php">Dashboard Home</a>
    <a href="admin.php">Grant Service</a>
    <a href="upload.php">Bulk Upload Services</a>
</div>

    <h2>Pending Service Provider Registrations</h2>
    <table>
        <tr><th>Name</th><th>Email</th><th>Service Type</th><th>Service Name</th><th>Action</th></tr>
        <?php foreach ($pending as $p): ?>
        <tr>
            <!-- <td><?= $p['id'] ?></td> -->
            <td><?= htmlspecialchars($p['first_name']) ?> <?= htmlspecialchars($p['last_name']) ?></td>
            <td><?= htmlspecialchars($p['email']) ?></td>
            <td><?= htmlspecialchars($p['category']) ?></td>
            <td><?= htmlspecialchars($p['business_name']) ?></td>
            <td>
                <a href="?approve=<?= $p['id'] ?>" class="approve-btn">✅ Approve</a> |
                <a href="?reject=<?= $p['id'] ?>" class="reject-btn">❌ Reject</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
