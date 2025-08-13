<?php
// db.php - make sure to configure your DB connection
$conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
if ($conn->connect_error) die("Connection failed");
session_start();

function haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // km
    $latFrom = deg2rad($lat1);
    $lonFrom = deg2rad($lon1);
    $latTo = deg2rad($lat2);
    $lonTo = deg2rad($lon2);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    return $earthRadius * $angle;
}

$businessName = $_GET['business_name'] ?? '';
$category = $_GET['category'] ?? '';
$userLat = $_GET['lat'] ?? '';
$userLon = $_GET['lon'] ?? '';

// Get all categories for the filter dropdown
$categories = [];
$catResult = $conn->query("SELECT DISTINCT service FROM services WHERE service IS NOT NULL");
while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row['service'];
}

// Build the main query
$sql = "SELECT * FROM services WHERE 1=1";
$params = [];
$types = '';

if (!empty($businessName)) {
    $sql .= " AND bussiness_name LIKE ?";
    $params[] = "%" . $businessName . "%";
    $types .= "s";
}

if (!empty($category)) {
    $sql .= " AND service = ?";
    $params[] = $category;
    $types .= "s";
}

// Prepare and execute the query
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$services = [];
while ($row = $result->fetch_assoc()) {
    $ratingStmt = $conn->prepare("
        SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
        FROM ratings 
        WHERE service_id = ?
    ");
    $ratingStmt->bind_param("i", $row['id']);
    $ratingStmt->execute();
    $ratingResult = $ratingStmt->get_result();
    $ratingData = $ratingResult->fetch_assoc();
    
    // Round to nearest 0.5
    $rawRating = (float)$ratingData['avg_rating'];
    $roundedRating = round($rawRating * 2) / 2;
    
    // Add to service data
    $row['rating'] = $roundedRating;
    $row['review_count'] = $ratingData['review_count'] ?? 0;

    if ($userLat && $userLon) {
        $row['distance'] = haversineGreatCircleDistance($userLat, $userLon, $row['lat'], $row['lon']);
    } else {
        $row['distance'] = null;
    }
    $services[] = $row;
}




if ($userLat && $userLon) {
    usort($services, fn($a, $b) => $a['distance'] <=> $b['distance']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Services Nearby</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./test_s.css">
</head>

<body>
    <?php include "new_header.php"; ?>
    <article>
        <h1>🌟 LocalConnect Services 🌟</h1>
    </article>

    <div class="container">
        <form id="searchForm" action="new_services.php">
            <input type="text" name="business_name" placeholder="Search by Business Name" value="<?= htmlspecialchars($businessName) ?>">
            
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $category == $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="hidden" name="lat" id="lat">
            <input type="hidden" name="lon" id="lon">
            <button type="submit">Search Services</button>
        </form>

        <div class="service-grid">
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div class="service-badge">
                        <?= htmlspecialchars($service['service']) ?>
                    </div>
                    
                    <div class="service-image">
                        <?php
                        $images = json_decode($service['image_names'], true);
                        if (!empty($images) && is_array($images)) {
                            echo '<img src="uploads/' . htmlspecialchars($images[0]) . '" alt="Service Image">';
                        } else {
                            echo '<img src="default.jpg" alt="No Image">';
                        }
                        ?>
                    </div>

                    <div class="service-info">
                        <h3>Service Name : <?= htmlspecialchars($service['bussiness_name']) ?></h3>
                        <p class="service-category">Category :<?= htmlspecialchars($service['service']) ?></p>
                        
                        <div class="service-rating">Ratings :
                            <?php 
                            $fullStars = floor($service['rating']);
                            $halfStar = ($service['rating'] - $fullStars) >= 0.5;
                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                            
                            // Full stars
                            for ($i = 0; $i < $fullStars; $i++): ?>
                                <i class="fas fa-star active"></i>
                            <?php endfor; ?>
                            
                            <!-- Half star if needed -->
                            <?php if ($halfStar): ?>
                                <i class="fas fa-star-half-alt active"></i>
                            <?php endif; ?>
                            
                            <!-- Empty stars -->
                            <?php for ($i = 0; $i < $emptyStars; $i++): ?>
                                <i class="far fa-star"></i>
                            <?php endfor; ?>
                            
                            <span>(<?= htmlspecialchars($service['review_count']) ?>)</span>
                        </div>

                        <p class="service-description">
                           About: <?= htmlspecialchars($service['about']) ?>
                        </p>

                        <?php if (!empty($service['distance'])): ?>
                            <p class="service-distance">
                                <i class="fas fa-map-marker-alt"></i> <?= round($service['distance'], 2) ?> km away
                            </p>
                        <?php endif; ?>

                        <div class="service-links">
                            <a href="tel:<?= htmlspecialchars($service['telephone_number']) ?>">
                                <i class="fas fa-phone"></i> Call
                            </a>
                            <?php if (!empty($service['whatsapp_number'])): ?>
                            <a href="https://wa.me/<?= htmlspecialchars($service['whatsapp_number']) ?>" target="_blank">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <?php endif; ?>
                            <a href="alt_new_service_profile.php?id=<?= htmlspecialchars($service['id']) ?>">
                                <i class="fas fa-info-circle"></i> Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Auto-fill location
        window.onload = () => {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition((position) => {
                    document.getElementById("lat").value = position.coords.latitude;
                    document.getElementById("lon").value = position.coords.longitude;
                }, () => {
                    console.warn("Location access denied");
                }, {
                    timeout: 10000,
                    enableHighAccuracy: true,
                    maximumAge: 30000
                });
            } else {
                alert("Geolocation not available");
            }
        };
    </script>
</body>
</html>