<?php

require_once __DIR__ . '/../src/mysqli_compat.php';
// db.php - Database connection
$conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
if ($conn->connect_error) die("Connection failed");
session_start();
date_default_timezone_set("Asia/Kolkata");


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
$wrongName = $_GET['wrong_name'] ?? '';
$category = $_GET['category'] ?? '';
$userLat = $_GET['lat'] ?? '';
$userLon = $_GET['lon'] ?? '';



// Get all categories for the filter dropdown
$categories = [];
$catResult = $conn->query("SELECT DISTINCT service FROM services WHERE service IS NOT NULL");
while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row['service'];
}
// User input (with typo, e.g. "restarant")
if(!empty($wrongName)){
    $input = strtolower($wrongName); // you can replace with $_GET['q'] or $_POST['q']

$closest = null;
$shortest = -1;
$categ=$categories;
foreach ($categ as $c) {
    // Calculate Levenshtein distance
    $distance = levenshtein($input, strtolower($c));

    // Check for exact match first
    if ($distance == 0) {
        $closest = $c;
        $shortest = 0;
        break;
    }

    // If this distance is less than the shortest distance found so far
    if ($distance < $shortest || $shortest < 0) {
        $closest = $c;
        $shortest = $distance;
    }
}

if ((!$shortest == 0)) {
    $category=$closest;
}
}

$sql1="SELECT * FROM services WHERE 1=1";
$params1 = [];
$types1= '';
if (!empty($businessName)) {
    $sql1 .= " AND bussiness_name LIKE ?";
    $params1[] = "%" . $businessName . "%";
    $types1 .= "s";
}

if (!empty($category)) {
    $sql1 .= " AND service = ?";
    $params1[] = $category;
    $types1 .= "s";
}

// Add sorting by distance if location is provided
// if ($userLat && $userLon) {
//     $sql1 .= " ORDER BY (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lon) - radians(?) + sin(radians(?)) * sin(radians(lat))))";
//     $params1[] = $userLat;
//     $params1[] = $userLon;
//     $params1[] = $userLat;
//     $types1 .= "ddd";
// }




// ✅ Fetch all services


// ✅ Functions
function buildServiceHours($service) {
    return [    
        "mon" => [$service["mon_fri_start"], $service["mon_fri_end"]],
        "tue" => [$service["mon_fri_start"], $service["mon_fri_end"]],
        "wed" => [$service["mon_fri_start"], $service["mon_fri_end"]],
        "thu" => [$service["mon_fri_start"], $service["mon_fri_end"]],
        "fri" => [$service["mon_fri_start"], $service["mon_fri_end"]],
        "sat" => [$service["sat_start"], $service["sat_end"]],
        "sun" => [$service["sun_start"], $service["sun_end"]]
    ];
}

function getServiceStatus($hours) {
    $day = strtolower(date("D")); // e.g. "mon"
    $now = new DateTime();

    list($startTime, $endTime) = $hours[$day];

    // If closed all day (00:00–00:00)
    if ($startTime === "00:00:00" && $endTime === "00:00:00") {
        return findNextOpen($now, $hours);
    }

    $start = new DateTime(date("Y-m-d") . " " . $startTime);
    $end   = new DateTime(date("Y-m-d") . " " . $endTime);

    if ($now >= $start && $now <= $end) {
        $remaining = $now->diff($end);
        return "Open now (closes in {$remaining->h}h {$remaining->i}m)";
    } elseif ($now < $start) {
        $wait = $now->diff($start);
        return "Closed, opens in {$wait->h}h {$wait->i}m";
    } else {
        return findNextOpen($now, $hours);
    }
}

function findNextOpen($now, $hours) {
    $nextDay = clone $now;
    for ($i = 1; $i <= 7; $i++) {
        $nextDay->modify("+1 day");
        $nextDayKey = strtolower($nextDay->format("D"));

        list($s, $e) = $hours[$nextDayKey];
        if (!($s === "00:00:00" && $e === "00:00:00")) {
            $nextOpen = new DateTime($nextDay->format("Y-m-d") . " " . $s);
            $wait = $now->diff($nextOpen);
            return "Closed, opens in {$wait->d}d {$wait->h}h {$wait->i}m";
        }
    }
    return "Closed (no schedule)";
}

// Prepare and execute the query
$stmt = $conn->prepare($sql1);
if (!empty($params1)) {
    $stmt->bind_param($types1, ...$params1);
}
$stmt->execute();
$result = $stmt->get_result();

$services = [];
$row=[];
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

    $sql = $conn->prepare("SELECT mon_fri_start,mon_fri_end,sat_start,sat_end,sun_start,sun_end FROM providers where status='approved' AND id=?");
    $sql->bind_param("i", $row['id']);
    $sql->execute();
    $result3 = $sql->get_result();
    $result3 = $result3->fetch_assoc();

        // while ($service = $result3->fetch_assoc()) {
            $hours = buildServiceHours($result3);
            $row['status_time'] = getServiceStatus($hours); 
        // }
    
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




// ✅ Display Service Cards

}




$wrongName='';

$conn->close();

if ($userLat && $userLon) {
    usort($services, fn($a, $b) => $a['distance'] <=> $b['distance']);
}
// print_r($services);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Services Nearby</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #8e44ad;
            --secondary: #6c3483;
            --accent: #e67e22;
            --light: #f9f9f9;
            --dark: #2c3e50;
            --text: #333333;
            --border: #e0e0e0;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        select option{
            text-transform: uppercase;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--text);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
            color: var(--dark);
        }
        
        article h1 {
            text-align: center;
            margin: 2rem 0;
            font-size: 2.5rem;
            color: var(--primary);
            position: relative;
            padding-bottom: 1rem;
        }

        article h1::after {
           
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: var(--accent);
        }

        #searchForm {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        #searchForm input,
        #searchForm select {
            flex: 1;
            min-width: 200px;
            padding: 0.8rem 1.2rem;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 1rem;
            transition: var(--transition);
        }

        #searchForm input:focus,
        #searchForm select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(142, 68, 173, 0.2);
        }

        #searchForm button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: var(--transition);
        }

        #searchForm button:hover {
            background: var(--secondary);
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .service-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            opacity: 0;
            transform: translateY(30px);
            animation: none;
        }

        .service-card.animate {
            animation: slideInUp 0.8s ease-out forwards;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .service-card.animate-scale {
            animation: fadeInScale 0.8s ease-out forwards;
        }

        .service-card.animate-left {
            animation: slideInLeft 0.8s ease-out forwards;
        }

        .service-card.animate-right {
            animation: slideInRight 0.8s ease-out forwards;
        }

        /* Skeleton Loading Cards */
        .skeleton-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
            position: relative;
        }

        .skeleton-image {
            height: 180px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
        }

        .skeleton-content {
            padding: 1.5rem;
        }

        .skeleton-title {
            height: 20px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .skeleton-text {
            height: 14px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .skeleton-text.short {
            width: 60%;
        }

        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }

        /* Improved hover effects */
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            transition: all 0.3s ease;
        }

        .service-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--accent);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 1;
        }

        .service-image {
            height: 180px;
            overflow: hidden;
        }

        .service-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .service-card:hover .service-image img {
            transform: scale(1.05);
        }

        .service-info {
            padding: 1.5rem;
        }

        .service-info h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--primary);
        }

        .service-category {
            color: var(--secondary);
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .service-category::before {
            content: '\f02b';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 0.8rem;
        }

        .service-rating {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            margin-bottom: 0.8rem;
        }

        .service-rating i.active {
            color: #f1c40f;
        }

        .service-rating span {
            font-size: 0.8rem;
            color: var(--text);
            margin-left: 0.3rem;
        }

        .service-description {
            font-size: 0.9rem;
            color: var(--text);
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .service-distance {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--secondary);
            margin-bottom: 1rem;
        }

        .service-links {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .service-links a {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.5rem 0.8rem;
            background: var(--light);
            color: var(--primary);
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            transition: var(--transition);
        }

        .service-links a:hover {
            background: var(--primary);
            color: white;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
            gap: 0.5rem;
        }

        .pagination a, 
        .pagination span {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
        }

        .pagination a {
            background: white;
            color: var(--primary);
            border: 1px solid var(--border);
            transition: var(--transition);
        }

        .pagination a:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination .current {
            background: var(--primary);
            color: white;
            border: 1px solid var(--primary);
        }

        .load-more {
            text-align: center;
            margin: 2rem 0;
        }

        .load-more button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: var(--transition);
        }

        .load-more button:hover {
            background: var(--secondary);
        }

        @media (max-width: 768px) {
            .service-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }

            article h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .service-grid {
                grid-template-columns: 1fr;
            }

            #searchForm {
                flex-direction: column;
            }

            article h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>
    <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
include_once __DIR__ . '/../templates/new_header.php'; ?>
    <article>
        <h1 >LocalConnect Services</h1>
    </article>

    <div class="container">
        <form id="searchForm" action="new_services.php" method="get">
            <input type="text" name="business_name" placeholder="Search by Business Name" value="<?= htmlspecialchars($businessName) ?>">
            <input type="text" name="wrong_name" placeholder="Search if spell not known" value="<?= htmlspecialchars($wrongName) ?>">
            
            <select name="category">
                <option value="">All Categories</option>
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $category == $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endforeach; ?>
            </select>
            
            <input type="hidden" name="lat" id="lat">
            <input type="hidden" name="lon" id="lon">
            <button type="submit">Search Services</button>
        </form>

        <div class="service-grid">
            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
$cardIndex = 0; ?>
            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
foreach ($services as $service): ?>
                <div class="service-card" data-card="<?= $cardIndex ?>">
                    <div class="service-badge">
                        <?= htmlspecialchars($service['service']) ?>
                    </div>
                    
                    <div class="service-image">
                        <?php
                        
require_once __DIR__ . '/../src/mysqli_compat.php';
$images = json_decode($service['image_names'], true);
                        if (!empty($images) && is_array($images)) {
                            echo '<img src="uploads/' . htmlspecialchars($images[0]) . '" alt="' . htmlspecialchars($service['bussiness_name']) . '">';
                        } else {
                            echo '<img src="default.jpg" alt="No Image Available">';
                        }
                        ?>
                    </div>

                    <div class="service-info">
                        <h3><?= htmlspecialchars($service['bussiness_name']) ?></h3>
                        <p class="service-category"><?= htmlspecialchars($service['service']) ?></p>
                        
                        <div class="service-rating">
                            <?php 
                            
require_once __DIR__ . '/../src/mysqli_compat.php';
$fullStars = floor($service['rating']);
                            $halfStar = ($service['rating'] - $fullStars) >= 0.5;
                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                            
                            for ($i = 0; $i < $fullStars; $i++): ?>
                                <i class="fas fa-star active"></i>
                            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endfor; ?>
                            
                            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if ($halfStar): ?>
                                <i class="fas fa-star-half-alt active"></i>
                            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
                            
                            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
for ($i = 0; $i < $emptyStars; $i++): ?>
                                <i class="far fa-star"></i>
                            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endfor; ?>
                            
                            <span>(<?= htmlspecialchars($service['review_count']) ?>)</span>
                        </div>

                        <p class="service-description">
                            <?= htmlspecialchars($service['about']) ?>
                        </p>

                        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (!empty($service['distance'])): ?>
                            <p class="service-distance">
                                <i class="fas fa-map-marker-alt"></i> <?= round($service['distance'], 2) ?> km away
                            </p>
                        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>

                        <div class="service-links">
                            <a href="tel:<?= htmlspecialchars($service['telephone_number']) ?>">
                                <i class="fas fa-phone"></i> Call
                            </a>
                            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (!empty($service['whatsapp_number'])): ?>
                            <a href="https://wa.me/<?= htmlspecialchars($service['whatsapp_number']) ?>" target="_blank">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
                            <a href="alt_new_service_profile.php?id=<?= htmlspecialchars($service['id']) ?>">
                                <i class="fas fa-info-circle"></i> Details
                            </a>
                        </div>
                    </div>
                    <h4 style="margin-top:40px;background:black;color:white;padding:10px 20px;"><?= htmlspecialchars($service['status_time']) ?></h4>
                </div>
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
$cardIndex++; ?>
            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endforeach; ?>
        </div>
    </div>
    <div id="include-footer"></div>

    <script>
    async function includeHTML(id, file) {
        try {
            const res = await fetch(file);
            const data = await res.text();
            document.getElementById(id).innerHTML = data;
        } catch (error) {
            console.error(`Failed to load ${file}:`, error);
        }
    }

    window.onload = async () => {
        // Load footer
        await includeHTML("include-footer", "footer.html");

        // Geolocation
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    document.getElementById("lat").value = position.coords.latitude;
                    document.getElementById("lon").value = position.coords.longitude;
                },
                () => {
                    console.warn("Location access denied");
                },
                { timeout: 10000, enableHighAccuracy: true, maximumAge: 30000 }
            );
        } else {
            alert("Geolocation not available");
        }

        // Animate service cards
        animateServiceCards();
    };

    function animateServiceCards() {
        const serviceCards = document.querySelectorAll('.service-card');
        const animationTypes = ['animate', 'animate-scale', 'animate-left', 'animate-right'];
        
        serviceCards.forEach((card, index) => {
            // Calculate delay based on card position (staggered effect)
            const delay = index * 150; // 150ms delay between each card
            
            // Choose animation type based on card position
            let animationType;
            if (index % 3 === 0) {
                animationType = 'animate-left';   // Left column - slide from left
            } else if (index % 3 === 1) {
                animationType = 'animate-scale';  // Middle column - scale effect
            } else {
                animationType = 'animate-right';  // Right column - slide from right
            }
            
            // Apply animation with delay
            setTimeout(() => {
                card.classList.add(animationType);
                
                // Add a subtle bounce effect on hover after animation
                card.addEventListener('mouseenter', () => {
                    if (!card.classList.contains('hover-bounce')) {
                        card.style.transform = 'translateY(-8px) scale(1.02)';
                        card.style.transition = 'all 0.3s ease';
                    }
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0) scale(1)';
                });
                
            }, delay);
        });

        // Add loading skeleton effect for very slow connections
        if (serviceCards.length === 0) {
            showLoadingSkeletons();
        }
    }

    function showLoadingSkeletons() {
        const serviceGrid = document.querySelector('.service-grid');
        if (serviceGrid) {
            // Create 6 skeleton cards
            for (let i = 0; i < 6; i++) {
                const skeleton = document.createElement('div');
                skeleton.className = 'service-card skeleton-card';
                skeleton.innerHTML = `
                    <div class="skeleton-image"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-title"></div>
                        <div class="skeleton-text"></div>
                        <div class="skeleton-text short"></div>
                    </div>
                `;
                serviceGrid.appendChild(skeleton);
                
                // Animate skeleton cards
                setTimeout(() => {
                    skeleton.classList.add('animate');
                }, i * 100);
            }
        }
    }

    // Intersection Observer for cards that come into view later (for very long lists)
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('animate')) {
                    const card = entry.target;
                    const index = Array.from(document.querySelectorAll('.service-card')).indexOf(card);
                    const animationType = index % 3 === 0 ? 'animate-left' : 
                                        index % 3 === 1 ? 'animate-scale' : 'animate-right';
                    
                    setTimeout(() => {
                        card.classList.add(animationType);
                    }, 100);
                }
            });
        }, { threshold: 0.1 });

        // Observe all service cards
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.service-card').forEach(card => {
                observer.observe(card);
            });
        });
    }
    </script>
</body>
</html>

