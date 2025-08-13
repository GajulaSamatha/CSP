<?php
$conn = new mysqli("localhost", "root", "1234", "nandyal_dial");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all providers
$sql = "SELECT * FROM services";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Service Cards</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f7fa;
      padding: 20px;
    }

    .card {
      display: flex;
      flex-wrap: wrap;
      background: white;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      padding: 15px;
      align-items: center;
      gap: 20px;
    }

    .card img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 8px;
    }

    .card-body {
      flex: 1;
      min-width: 220px;
    }

    .card-body h2 {
      margin: 0;
      font-size: 18px;
      font-weight: 600;
    }

    .card-body p {
      margin: 4px 0;
      font-size: 14px;
      color: #555;
    }

    .services {
      font-size: 13px;
      color: #666;
    }

    .phone-icons a {
      text-decoration: none;
      margin-right: 10px;
      font-size: 15px;
    }

    .phone-icons a i {
      margin-right: 5px;
    }

    .rating {
      color: #f1c40f;
      font-size: 14px;
    }

    .status {
      background: #e3fcec;
      color: green;
      display: inline-block;
      font-size: 13px;
      padding: 3px 10px;
      border-radius: 20px;
      margin-bottom: 5px;
    }

    .actions {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .btn {
      padding: 6px 10px;
      border: none;
      border-radius: 20px;
      font-size: 13px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .view-btn { background: #007bff; color: white; }
    .map-btn  { background: #e74c3c; color: white; }
    .share-btn{ background: #ddd; color: #333; }

    @media (max-width: 600px) {
      .card {
        flex-direction: column;
        align-items: flex-start;
      }
      .actions {
        flex-direction: row;
        width: 100%;
        justify-content: space-between;
      }
      .card img {
        width: 100%;
        max-width: 100%;
      }
    }
  </style>
</head>
<body>

<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Decode image JSON
        $images = json_decode($row['image_names'], true);
        $image = isset($images[0]) ? $images[0] : 'default.jpg'; // Fallback if no image

        // $fullName = $row['first_name'] . " " . $row['last_name'];
        $services = explode(',', $row['service']); // Or another field if you use 'description'
?>
    <div class="card">
        <img src="./uploads/<?= htmlspecialchars($image) ?>" alt="Image">

        <div class="card-body">
            <div class="status">Open</div>
            <h2><?= htmlspecialchars($row['bussiness_name']) ?></h2>
            
            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($row['location']) ?></p>
            <p class="services">
                <?= implode(' • ', array_map('trim', $services)) ?>
            </p>
            <div class="phone-icons">
                <a href="tel:+91<?= $row['telephone_number'] ?>"><i class="fas fa-phone"></i> +91 <?= $row['telephone_number'] ?></a>
                <a href="https://wa.me/91<?= $row['whatsapp_number'] ?>" target="_blank"><i class="fab fa-whatsapp" style="color:#25D366;"></i></a>
            </div>
            <!-- <div class="rating">
                <i class="fas fa-star"></i>  //number_format($row['rating'] ?? 4.0, 1) ?> ( //$row['reviews'] ?? 127 ?>)
            </div> -->
        </div>

        <div class="actions">
            <button class="btn view-btn"><i class="fas fa-info-circle"></i></button>
            <button class="btn map-btn"><i class="fas fa-map-marker-alt"></i></button>
            <button class="btn share-btn"><i class="fas fa-share-alt"></i></button>
        </div>
    </div>
<?php
    }
} else {
    echo "<p>No providers found.</p>";
}

$conn->close();
?>

</body>
</html>






    