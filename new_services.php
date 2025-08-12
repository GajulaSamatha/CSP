<?php
// db.php - make sure to configure your DB connection
$conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
if ($conn->connect_error) die("Connection failed");
session_start();
function haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2)
{
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

$category = $_GET['category'] ?? '';
$userLat = $_GET['lat'] ?? '';
$userLon = $_GET['lon'] ?? '';

$sql = "SELECT * FROM services";
if (!empty($category)) {
    $sql .= " WHERE service LIKE '%" . $conn->real_escape_string($category) . "%'";
}

$result = $conn->query($sql);
$services = [];

while ($row = $result->fetch_assoc()) {
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
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <?php include "new_header.php"; ?>
    <article>
        <h1>🌟 LocalConnect Services 🌟</h1>
    </article>

    <div class="container">
        <form id="searchForm" action="new_services.php">
            <input type="text" name="category" placeholder="Search by Category (e.g., Plumber, Electrician)">
            <input type="hidden" name="lat" id="lat">
            <input type="hidden" name="lon" id="lon">
            <button type="submit">Search Nearby</button>
        </form>

        <div class="service-grid">
<?php foreach ($services as $service): ?>
    <div class="service-card">
        
        <div class="service-image">
            <?php
            $images = json_decode($service['image_names'], true);
            if (!empty($images) && is_array($images)) {
                // Show first image as main
                echo '<img src="uploads/' . htmlspecialchars($images[0]) . '" alt="Service Image">';
            } else {
                echo '<img src="default.jpg" alt="No Image">';
            }
            ?>
        </div>

        <div class="service-info">
            <h3><?php echo htmlspecialchars($service['service']); ?></h3>
            <p class="service-description">
                <?php echo htmlspecialchars($service['about']); ?>
            </p>

            <?php if (!empty($service['distance'])): ?>
                <p class="service-distance">
                    📍 <?php echo round($service['distance'], 2); ?> km away
                </p>
            <?php endif; ?>

            <!-- Extra small images preview -->
            <?php if (!empty($images) && count($images) > 1): ?>
                <div class="service-thumbnails">
                    <?php foreach ($images as $img): ?>
                        <img src="uploads/<?php echo htmlspecialchars($img); ?>" alt="Service Image">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="service-links">
                <a href="tel:<?php echo htmlspecialchars($service['telephone_number']); ?>">
                    <i class="fas fa-phone"></i> Call
                </a>
                <?php if (!empty($service['whatsapp_number'])): ?>
                <a href="https://wa.me/<?php echo htmlspecialchars($service['whatsapp_number']); ?>" target="_blank">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <?php endif; ?>
                <a href="alt_new_service_profile.php?id=<?php echo htmlspecialchars($service['id']); ?>">
                    <i class="fas fa-map-marker-alt"></i> Details
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
                    console.log(position.coords.latitude,position.coords.longitude);
                }, () => {
                    console.warn("Location access denied");
                },{
                    timeout: 10000,
                    enableHighAccuracy: true,
                    maximumAge: 30000 // 5 minutes
                });
            } else {
                alert("Geolocation not available");
            }
        };
    </script>
</body>
</html>
