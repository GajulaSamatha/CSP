<?php
// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=nandyal_dial", "root", "1234");

// Get service ID from URL
$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch service details
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    die("Service not found.");
}

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'])) {
    $user_name = $_POST['user_name'];
    $rating = intval($_POST['rating']);
    $comment = $_POST['comment'];

    $stmt = $pdo->prepare("INSERT INTO ratings (service_id, user_name, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->execute([$service_id, $user_name, $rating, $comment]);

    header("Location: service_profile.php?id=" . $service_id);
    exit;
}

// Fetch ratings
// $stmt = $pdo->prepare("SELECT * FROM ratings WHERE service_id = ? ORDER BY created_at DESC");
// $stmt->execute([$service_id]);
// $ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Decode images JSON
$images = json_decode($service['image_names'], true);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($service['name']); ?> - Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
        }
        h1 { color: #1877f2; }
        .images img {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .opening-hours, .contact {
            margin: 10px 0;
            padding: 10px;
            background: #f0f2f5;
            border-radius: 5px;
        }
        .rating-form {
            margin-top: 20px;
            padding: 15px;
            background: #f0f2f5;
            border-radius: 5px;
        }
        .rating-list {
            margin-top: 20px;
        }
        .rating-item {
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 5px;
        }
        button {
            background: #1877f2;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background: #145dbf;
        }
        @media (max-width: 600px) {
            .container { padding: 10px; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1><?php echo htmlspecialchars($service['bussiness_name']); ?></h1>
    <p><?php echo nl2br(htmlspecialchars($service['about'])); ?></p>

    <div class="images">
        <?php if (!empty($images)) {
            foreach ($images as $img) {
                echo "<img src='uploads/".htmlspecialchars($img)."' alt='Service Image' height='50'>";
            }
        } ?>
    </div>

    <div class="opening-hours">
        <strong>Opening Hours:</strong><br>
        <?php echo nl2br(htmlspecialchars($service['mon_fri_start'])); ?>
    </div>

    <div class="contact">
        <strong>Contact:</strong><br>
        Phone: <?php echo htmlspecialchars($service['telephone_number']); ?><br>
        Email: <?php echo htmlspecialchars($service['email']); ?>
    </div>

    <!-- Navigation Button -->
    <button onclick="navigateToService()">Navigate to Location</button>

    <script>
    function navigateToService() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var userLat = position.coords.latitude;
                var userLng = position.coords.longitude;
                var destLat = <?php echo $service['latitude']; ?>;
                var destLng = <?php echo $service['longitude']; ?>;
                var url = `https://www.google.com/maps/dir/${userLat},${userLng}/${destLat},${destLng}`;
                window.open(url, '_blank');
            });
        } else {
            alert("Geolocation is not supported by your browser.");
        }
    }
    </script>

    <!-- Rating Form -->
    <!-- <div class="rating-form">
        <h3>Leave a Rating</h3>
        <form method="POST">
            <input type="text" name="user_name" placeholder="Your Name" required><br><br>
            <label>Rating (1-5):</label>
            <input type="number" name="rating" min="1" max="5" required><br><br>
            <textarea name="comment" placeholder="Your Comment" required></textarea><br><br>
            <button type="submit">Submit Rating</button>
        </form>
    </div> -->

    <!-- Ratings List -->
    <!-- <div class="rating-list">
        <h3>Ratings</h3>
        <?php //foreach ($ratings as $r) { ?>
            <div class="rating-item">
                <strong><?php // echo htmlspecialchars($r['user_name']); ?></strong> 
                (<?php // echo $r['rating']; ?>/5) - 
                <?php // echo htmlspecialchars($r['comment']); ?><br>
                <small><?php //echo $r['created_at']; ?></small>
            </div>
        <?php //} ?>
    </div> -->
</div>
</body>
</html>
