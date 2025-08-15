<?php
session_start();
require 'db.php'; // DB connection

$service_id = intval($_GET['id']);
list($first, $last) = explode(' ', $_SESSION['user_name'], 2);
$cs = "SELECT id FROM customers WHERE first_name = ? AND last_name = ?";
$st=$pdo->prepare($cs);
$st->execute([$first,$last]);
$res=$st->fetch(PDO::FETCH_ASSOC);
$customer_id = $res['id'] ?? null;
$_SESSION['customer_id']=$res['id'];

// Fetch service details
$sql = "SELECT p.bussiness_name, 
               p.about, p.telephone_number, p.whatsapp_number, 
               p.lat, p.lon, p.image_names,p.service
        FROM services p
        WHERE p.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    die("Service not found.");
}

// Decode images JSON
$images = json_decode($service['image_names'], true) ?? [];

// Fetch existing rating for logged-in customer
$existing = null;
if ($customer_id) {
    $sql = "SELECT rating, message FROM ratings WHERE service_id = ? AND customer_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$service_id, $customer_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all ratings for display
$sql = "SELECT c.first_name, c.last_name, r.rating, r.message, r.updated_at
        FROM ratings r
        JOIN customers c ON r.customer_id = c.id
        WHERE r.service_id = ?
        ORDER BY r.updated_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$service_id]);
$ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$s1 = $pdo->prepare("SELECT 
    TIME_FORMAT(mon_fri_start, '%H:%i') AS mon_fri_start,
    TIME_FORMAT(mon_fri_end, '%H:%i') AS mon_fri_end,
    TIME_FORMAT(sat_start, '%H:%i') AS sat_start,
    TIME_FORMAT(sat_end, '%H:%i') AS sat_end,
    TIME_FORMAT(sun_start, '%H:%i') AS sun_start,
    TIME_FORMAT(sun_end, '%H:%i') AS sun_end
FROM providers WHERE id = ?");
$s1->execute([$service_id]);
$s1res = $s1->fetch(PDO::FETCH_ASSOC);

function formatHours($start, $end) {
    if ($start === "00:00" && $end === "00:00") {
        return "<span class='closed'>Closed</span>";
    }
    return date("g:i A", strtotime($start)) . " - " . date("g:i A", strtotime($end));
}

// Determine if service is currently open
function isOpenNow($hours) {
    $timezone = new DateTimeZone('Asia/Kolkata');
    $now = new DateTime('now', $timezone);
    $dayOfWeek = $now->format('N');
    $currentTime = $now->format('H:i');
    // echo("<h1>$currentTime</h1>"); // 1 (Mon) to 7 (Sun)
    
    if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Mon-Fri
        $start = $hours['mon_fri_start'];
        $end = $hours['mon_fri_end'];
    } elseif ($dayOfWeek == 6) { // Sat
        $start = $hours['sat_start'];
        $end = $hours['sat_end'];
    } else { // Sun
        $start = $hours['sun_start'];
        $end = $hours['sun_end'];
    }
    
    if ($start === "00:00" && $end === "00:00") {
        return false;
    }
    
    return ($currentTime >= $start && $currentTime <= $end);
}

$isOpen = isOpenNow($s1res);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($service['bussiness_name']) ?> - Service Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f3f6;
            margin: 0;
            padding: 0;
        }

        .profile-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .business-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .business-name {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }

        .status-open {
            background-color: #e6f7ee;
            color: #28a745;
        }

        .status-closed {
            background-color: #fdecea;
            color: #dc3545;
        }

        .provider-name {
            color: #666;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .slideshow-container {
            position: relative;
            max-width: 100%;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .mySlides img {
            width: 100%;
            height: 350px;
            object-fit: cover;
        }

        .prev, .next {
            position: absolute;
            top: 50%;
            padding: 12px;
            background: rgba(0,0,0,0.6);
            color: white;
            cursor: pointer;
            border-radius: 50%;
            transform: translateY(-50%);
            transition: all 0.3s;
        }

        .prev:hover, .next:hover {
            background: rgba(0,0,0,0.8);
        }

        .prev { left: 15px; }
        .next { right: 15px; }

        .description, .contact-details, .navigate, .rating-container, .all-reviews {
            margin-top: 25px;
        }

        .description h3, .contact-details h3, .all-reviews h3, .rating-container h3 {
            color: #333;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .description p {
            line-height: 1.6;
            color: #555;
        }

        .contact-details p {
            font-size: 16px;
            margin: 8px 0;
        }

        .contact-details a {
            color: #0066cc;
            text-decoration: none;
            margin-right: 15px;
            display: inline-block;
        }

        .contact-details a:hover {
            text-decoration: underline;
        }

        .contact-details i {
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }

        .hours-section {
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            padding: 20px;
            margin-top: 25px;
            border-radius: 10px;
        }

        .hours-section h3 {
            margin-bottom: 15px;
            font-size: 18px;
            color: #333;
        }

        .hours-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .hours-section li {
            padding: 8px 0;
            font-size: 15px;
            display: flex;
            justify-content: space-between;
        }

        .hours-section li strong {
            width: 100px;
            display: inline-block;
        }

        .hours-section .closed {
            color: #dc3545;
            font-weight: bold;
        }

        .navigate button {
            background: #ff9f00;
            padding: 12px 20px;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
            width: 100%;
        }

        .navigate button:hover {
            background: #e68a00;
        }

        .navigate button:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }

        /* Rating System */
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .star-rating input { display: none; }
        .star-rating label { 
            color: #ccc; 
            font-size: 28px; 
            cursor: pointer; 
            margin-right: 5px;
            transition: color 0.2s;
        }
        .star-rating input:checked ~ label i,
        .star-rating label:hover i,
        .star-rating label:hover ~ label i { color: #ff9f00; }

        .rating-form textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-top: 15px;
            min-height: 100px;
            font-family: Arial, sans-serif;
            resize: vertical;
        }

        .submit-btn {
            margin-top: 15px;
            background-color: #ff9f00;
            padding: 12px 20px;
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 6px;
            transition: background 0.3s;
            width: 100%;
        }

        .submit-btn:hover { 
            background-color: #e68a00; 
        }

        /* Reviews */
        .all-reviews {
            margin-top: 30px;
        }

        .review-card {
            background: #fff;
            padding: 18px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .stars i { 
            color: #ccc; 
            font-size: 18px;
        }
        .stars .filled { color: #ff9f00; }

        .reviewer-name { 
            font-weight: bold;
            color: #333;
        }

        .review-message { 
            margin-top: 10px;
            color: #555;
            line-height: 1.5;
        }
        
        .review-date { 
            font-size: 13px; 
            color: #999; 
            margin-top: 8px;
            display: block;
        }

        @media (max-width: 768px) {
            .profile-container {
                margin: 10px;
                padding: 15px;
            }
            
            .business-name {
                font-size: 24px;
            }
            
            .mySlides img {
                height: 250px;
            }
        }
    </style>
</head>
<body>
<?php include"new_header.php"; ?>
<div class="profile-container">
    <!-- Business & Provider -->
    <div class="business-header">
        <div>
            <h1 class="business-name"><?= htmlspecialchars($service['bussiness_name']) ?></h1>
            <p class="provider-name">Category: <?= htmlspecialchars($service['service']) ?></p>
        </div>
        <div class="status-badge <?= $isOpen ? 'status-open' : 'status-closed' ?>">
            <?= $isOpen ? 'OPEN NOW' : 'CLOSED' ?>
        </div>
    </div>

    <!-- Slideshow -->
    <div class="slideshow-container">
        <?php foreach ($images as $index => $img): ?>
            <div class="mySlides fade">
                <img src="uploads/<?= htmlspecialchars($img) ?>" alt="Service Image">
            </div>
        <?php endforeach; ?>
        <a class="prev" onclick="plusSlides(-1)">❮</a>
        <a class="next" onclick="plusSlides(1)">❯</a>
    </div>

    <!-- Description -->
    <div class="description">
        <h3>Description</h3>
        <p><?= nl2br(htmlspecialchars($service['about'])) ?></p>
    </div>

    <!-- Contact -->
    <div class="contact-details">
        <h3>Contact</h3>
        <p>
            <a href="tel:<?= htmlspecialchars($service['telephone_number']) ?>"><i class="fas fa-phone"></i> <?= htmlspecialchars($service['telephone_number']) ?></a>
            <?php if (!empty($service['whatsapp_number'])): ?>
                <a href="https://wa.me/+91<?= preg_replace('/\\D/', '', $service['whatsapp_number']) ?>" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <?php endif; ?>
        </p>
    </div>

    <!-- Opening Hours -->
    <div class="hours-section">
        <h3>Opening Hours</h3>
        <ul>
            <li><strong>Mon–Fri:</strong> <?= formatHours($s1res['mon_fri_start'], $s1res['mon_fri_end']); ?></li>
            <li><strong>Saturday:</strong> <?= formatHours($s1res['sat_start'], $s1res['sat_end']); ?></li>
            <li><strong>Sunday:</strong> <?= formatHours($s1res['sun_start'], $s1res['sun_end']); ?></li>
        </ul>
        <?php echo($s1res['mon_fri_start'].$s1res['mon_fri_end']);  ?>
    </div>

    <!-- Navigate Button -->
    <div class="navigate">
        <button onclick="navigateToService()" <?= !$isOpen ? 'disabled title="Service is currently closed"' : '' ?>>
            <?= $isOpen ? 'Navigate to Service' : 'Currently Closed' ?>
        </button>
    </div>

    <!-- Rating System -->
    <div class="rating-container">
        <h3><?= $existing ? 'Edit Your Rating' : 'Rate this Service' ?></h3>
        <form action="rate_service.php" method="POST" class="rating-form">
            <input type="hidden" name="service_id" value="<?= $service_id ?>">

            <div class="star-rating">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>"
                           <?= (isset($existing['rating']) && $existing['rating'] == $i) ? 'checked' : '' ?> required>
                    <label for="star<?= $i ?>" title="<?= $i ?> stars">
                        <i class="fas fa-star"></i>
                    </label>
                <?php endfor; ?>
            </div>

            <textarea name="message" placeholder="Share your experience..." required><?= $existing['message'] ?? '' ?></textarea>

            <button type="submit" class="submit-btn">
                <?= $existing ? 'Update Rating' : 'Submit Rating' ?>
            </button>
        </form>
    </div>

    <!-- All Reviews -->
    <div class="all-reviews">
        <h3>Customer Reviews</h3>
        <?php if (empty($ratings)): ?>
            <p>No reviews yet. Be the first to review!</p>
        <?php else: ?>
            <?php foreach ($ratings as $row): ?>
                <div class="review-card">
                    <div class="review-header">
                        <span class="reviewer-name"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></span>
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?= ($i <= $row['rating']) ? 'filled' : '' ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <p class="review-message"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                    <small class="review-date">Posted on: <?= date('M j, Y', strtotime($row['updated_at'])) ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    let slideIndex = 1;
    showSlides(slideIndex);

    function plusSlides(n) { showSlides(slideIndex += n); }
    function showSlides(n) {
        let i;
        let slides = document.getElementsByClassName("mySlides");
        if (n > slides.length) { slideIndex = 1 }
        if (n < 1) { slideIndex = slides.length }
        for (i = 0; i < slides.length; i++) { slides[i].style.display = "none"; }
        slides[slideIndex - 1].style.display = "block";
    }

    function navigateToService() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                let customerLat = position.coords.latitude;
                let customerLon = position.coords.longitude;
                let serviceLat = <?= $service['lat'] ?>;
                let serviceLon = <?= $service['lon'] ?>;
                window.open(`https://www.google.com/maps/dir/${customerLat},${customerLon}/${serviceLat},${serviceLon}`, '_blank');
            });
        } else {
            alert("Geolocation not supported.");
        }
    }
</script>

</body>
</html>