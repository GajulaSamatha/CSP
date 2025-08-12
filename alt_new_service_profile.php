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
$sql = "SELECT p.business_name, CONCAT(p.first_name, ' ', p.last_name) AS provider_name, 
               p.description, p.phone_number, p.whatsapp_number, 
               p.lat, p.lon, p.image_names
        FROM providers p
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

$s1 = $pdo->prepare("SELECT *, 
    TIME_FORMAT(mon_fri_start, '%H:%i') AS mon_fri_start,
    TIME_FORMAT(mon_fri_end, '%H:%i') AS mon_fri_end,
    TIME_FORMAT(sat_start, '%H:%i') AS sat_start,
    TIME_FORMAT(sat_end, '%H:%i') AS sat_end,
    TIME_FORMAT(sun_start, '%H:%i') AS sun_start,
    TIME_FORMAT(sun_end, '%H:%i') AS sun_end
FROM services WHERE id = ?");
$s1->execute([$service_id]);
$s1res = $s1->fetch(PDO::FETCH_ASSOC);

function formatHours($start, $end) {
    if ($start === "00:00" && $end === "00:00") {
        return "<span class='closed'>Closed</span>";
    }
    return date("g:i A", strtotime($start)) . " - " . date("g:i A", strtotime($end));
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($service['business_name']) ?> - Service Profile</title>
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
            margin: auto;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .business-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .provider-name {
            color: #555;
            margin-bottom: 15px;
        }

        .slideshow-container {
            position: relative;
            max-width: 100%;
            margin-bottom: 15px;
        }

        .mySlides img {
            width: 100%;
            border-radius: 8px;
            height: 300px;
            object-fit: cover;
        }

        .prev, .next {
            position: absolute;
            top: 50%;
            padding: 10px;
            background: rgba(0,0,0,0.5);
            color: white;
            cursor: pointer;
            border-radius: 50%;
            transform: translateY(-50%);
        }

        .prev { left: 10px; }
        .next { right: 10px; }

        .description, .contact-details, .navigate, .rating-container, .all-reviews {
            margin-top: 20px;
        }

        .contact-details p {
            font-size: 16px;
        }

        .contact-details i {
            color: #007bff;
            margin-right: 5px;
        }
        .hours-section {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 20px;
            border-radius: 10px;
        }
        .hours-section h3 {
            margin-bottom: 10px;
            font-size: 18px;
            color: #333;
        }
        .hours-section ul {
            list-style: none;
            padding: 0;
        }
        .hours-section li {
            padding: 5px 0;
            font-size: 15px;
        }
        .hours-section .closed {
            color: red;
            font-weight: bold;
        }

        .navigate button {
            background: #ff9f00;
            padding: 10px 15px;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .navigate button:hover {
            background: #e68a00;
        }

        /* Rating System */
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .star-rating input { display: none; }
        .star-rating label { color: #ccc; font-size: 24px; cursor: pointer; }
        .star-rating input:checked ~ label i,
        .star-rating label:hover i,
        .star-rating label:hover ~ label i { color: #ff9f00; }

        .rating-form textarea {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

        .submit-btn {
            margin-top: 10px;
            background-color: #ff9f00;
            padding: 10px;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .submit-btn:hover { background-color: #e68a00; }

        /* Reviews */
        .review-card {
            background: #fafafa;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 6px;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stars i { color: #ccc; }
        .stars .filled { color: #ff9f00; }

        .reviewer-name { font-weight: bold; }

        .review-message { margin-top: 8px; }
        .review-date { font-size: 12px; color: #777; }

    </style>
</head>
<body>
<?php include"new_header.php"; ?>
<div class="profile-container">
    <!-- Business & Provider -->
    <h1 class="business-name"><?= htmlspecialchars($service['business_name']) ?></h1>
    <p class="provider-name">Provided by: <?= htmlspecialchars($service['provider_name']) ?></p>

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
        <p><?= nl2br(htmlspecialchars($service['description'])) ?></p>
    </div>

    <!-- Contact -->
    <div class="contact-details">
            <strong>Contact:</strong>
            <a href="tel:<?= htmlspecialchars($service['phone_number']) ?>"><i class="fas fa-phone"></i> <?= htmlspecialchars($service['phone_number']) ?></a>
            <?php if (!empty($service['whatsapp_number'])): ?><a href="https://wa.me/+91<?= preg_replace('/\\D/', '', $service['whatsapp_number']) ?>" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a><?php endif; ?>
        </div>

    <div class="hours-section">
    <h3>Opening Hours</h3>
    <ul>
        <li><strong>Mon–Fri:</strong> <?= formatHours($s1res['mon_fri_start'], $s1res['mon_fri_end']); ?></li>
        <li><strong>Saturday:</strong> <?= formatHours($s1res['sat_start'], $s1res['sat_end']); ?></li>
        <li><strong>Sunday:</strong> <?= formatHours($s1res['sun_start'], $s1res['sun_end']); ?></li>
    </ul>
</div>

    <!-- Navigate Button -->
    <div class="navigate">
        <button onclick="navigateToService()">Navigate to Service</button>
    </div>
    <!-- All Reviews -->
    <div class="all-reviews">
        <h3>Customer Reviews</h3>
        <?php foreach ($ratings as $row): ?>
            <div class="review-card">
                <div class="review-header">
                    <div class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= ($i <= $row['rating']) ? 'filled' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="reviewer-name"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></span>
                </div>
                <p class="review-message"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                <small class="review-date">Updated: <?= $row['updated_at'] ?></small>
            </div>
        <?php endforeach; ?>
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
