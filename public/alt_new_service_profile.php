<?php

require_once __DIR__ . '/../src/mysqli_compat.php';
session_start();
require_once __DIR__ . '/../config/db.php'; // DB connection

// Validate service ID
$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($service_id <= 0) {
    die("Invalid service ID");
}

// Initialize customer variables safely
$customer_id = null;
if (isset($_SESSION['user_name']) && !empty(trim($_SESSION['user_name']))) {
    $name_parts = explode(' ', trim($_SESSION['user_name']), 2);
    $first = $name_parts[0] ?? '';
    $last = $name_parts[1] ?? '';
    
    if (!empty($first)) {
        $cs = "SELECT id FROM customers WHERE first_name = ? AND last_name = ?";
        $st = $pdo->prepare($cs);
        $st->execute([$first, $last]);
        $res = $st->fetch(PDO::FETCH_ASSOC);
        
        if ($res && isset($res['id'])) {
            $customer_id = $res['id'];
            $_SESSION['customer_id'] = $customer_id;
        }
    }
}

// Fetch service details
$sql = "SELECT p.bussiness_name, p.about, p.telephone_number, 
               p.whatsapp_number, p.lat, p.lon, p.image_names, p.service
        FROM services p
        WHERE p.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    die("Service not found.");
}

// Decode images JSON safely
$images = [];
if (!empty($service['image_names'])) {
    $decoded_images = json_decode($service['image_names'], true);
    if (is_array($decoded_images)) {
        $images = $decoded_images;
    }
}


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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4A00E0;
            --secondary: #8E2DE2;
            --accent: #3498db;
            --text-dark: #2d3748;
            --text-medium: #4a5568;
            --text-light: #718096;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .profile-container {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin: 30px auto;
        }

        /* Header Section */
        .business-header {
            padding: 30px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .business-info {
            flex: 1;
        }

        .business-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .provider-name {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 15px;
        }

        .status-badge {
            position: absolute;
            top: 30px;
            right: 30px;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .status-open {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }

        .status-closed {
            background-color: rgba(0,0,0,0.2);
            color: white;
        }

        .status-badge i {
            font-size: 1rem;
        }

        /* Image Gallery */
        .gallery-container {
            position: relative;
            height: 400px;
            overflow: hidden;
        }

        .gallery-slides {
            display: flex;
            height: 100%;
            transition: transform 0.5s ease;
        }

        .gallery-slide {
            min-width: 100%;
            height: 100%;
        }

        .gallery-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-nav {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            transform: translateY(-50%);
        }

        .gallery-nav-btn {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .gallery-nav-btn:hover {
            background: white;
            transform: scale(1.1);
        }

        .gallery-dots {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }

        .gallery-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .gallery-dot.active {
            background: white;
            transform: scale(1.2);
        }

        /* Content Sections */
        .content-section {
            padding: 30px;
            border-bottom: 1px solid var(--border-color);
        }

        .content-section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--accent);
        }

        .description-text {
            color: white;
            line-height: 1.8;
        }

        /* Contact Info */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(74, 0, 224, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .contact-details a {
            color: var(--primary);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .contact-details a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        .contact-label {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        /* Hours Section */
        .hours-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .hours-item {
            background: var(--bg-light);
            padding: 15px;
            border-radius: var(--radius-sm);
            display: flex;
            justify-content: space-between;
        }

        .hours-day {
            font-weight: 500;
        }

        .hours-time {
            text-align: right;
        }

        .closed {
            color: var(--danger);
            font-weight: 500;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: var(--radius-md);
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: white;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-secondary:hover {
            background: rgba(74, 0, 224, 0.05);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        /* Rating System */
        .rating-container {
            background: var(--bg-light);
            padding: 25px;
            border-radius: var(--radius-md);
        }

        .rating-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: var(--text-dark);
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .star-rating input { 
            display: none; 
        }
        
        .star-rating label { 
            color: #ddd; 
            font-size: 2rem; 
            cursor: pointer; 
            margin-right: 5px;
            transition: all 0.2s;
        }
        
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label { 
            color: var(--warning); 
        }

        .rating-form textarea {
            width: 100%;
            padding: 15px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            margin-top: 15px;
            min-height: 120px;
            font-family: inherit;
            resize: vertical;
            transition: all 0.3s ease;
        }

        .rating-form textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(74, 0, 224, 0.1);
        }

        /* Reviews Section */
        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .reviews-count {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .review-card {
            background: white;
            padding: 20px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .review-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .reviewer {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .reviewer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .reviewer-name {
            font-weight: 600;
            color: var(--text-dark);
        }

        .review-date {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        .review-stars {
            color: var(--warning);
        }

        .review-message {
            color: var(--text-medium);
            margin-top: 10px;
        }

        .no-reviews {
            text-align: center;
            padding: 40px;
            color: var(--text-light);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .business-header {
                flex-direction: column;
                padding: 20px;
            }

            .status-badge {
                position: static;
                margin-top: 15px;
                align-self: flex-start;
            }

            .gallery-container {
                height: 300px;
            }

            .contact-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .business-name {
                font-size: 1.5rem;
            }

            .gallery-container {
                height: 250px;
            }

            .content-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
include_once __DIR__ . '/../templates/new_header.php'; ?>

<div class="container">
    <div class="profile-container">
        <!-- Business Header -->
        <div class="business-header">
            <div class="business-info">
                <h1 class="business-name"><?= htmlspecialchars($service['bussiness_name']) ?></h1>
                <p class="provider-name">Category: <?= htmlspecialchars($service['service']) ?></p>
                <p class="description-text"><?= nl2br(htmlspecialchars($service['about'])) ?></p>
            </div>
            <div class="status-badge <?= $isOpen ? 'status-open' : 'status-closed' ?>">
                <i class="fas fa-<?= $isOpen ? 'check-circle' : 'times-circle' ?>"></i>
                <?= $isOpen ? 'OPEN NOW' : 'CLOSED' ?>
            </div>
        </div>

        <!-- Image Gallery -->
        <div class="gallery-container">
            <div class="gallery-slides" id="gallerySlides">
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
foreach ($images as $index => $img): ?>
                    <div class="gallery-slide">
                        <img src="uploads/<?= htmlspecialchars($img) ?>" alt="Service Image <?= $index + 1 ?>">
                    </div>
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endforeach; ?>
            </div>
            <div class="gallery-nav">
                <div class="gallery-nav-btn" onclick="prevSlide()"><i class="fas fa-chevron-left"></i></div>
                <div class="gallery-nav-btn" onclick="nextSlide()"><i class="fas fa-chevron-right"></i></div>
            </div>
            <div class="gallery-dots" id="galleryDots">
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
foreach ($images as $index => $img): ?>
                    <div class="gallery-dot <?= $index === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $index ?>)"></div>
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endforeach; ?>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="content-section">
            <h2 class="section-title"><i class="fas fa-phone-alt"></i> Contact Information</h2>
            <div class="contact-grid">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-details">
                        <span class="contact-label">Phone Number</span>
                        <a href="tel:<?= htmlspecialchars($service['telephone_number']) ?>"><?= htmlspecialchars($service['telephone_number']) ?></a>
                    </div>
                </div>
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (!empty($service['whatsapp_number'])): ?>
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="contact-details">
                        <span class="contact-label">WhatsApp</span>
                        <a href="https://wa.me/+91<?= preg_replace('/\\D/', '', $service['whatsapp_number']) ?>" target="_blank">Chat on WhatsApp</a>
                    </div>
                </div>
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-details">
                        <span class="contact-label">Location</span>
                        <a href="#" onclick="navigateToService(); return false;">Get Directions</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Opening Hours -->
        <div class="content-section">
            <h2 class="section-title"><i class="far fa-clock"></i> Opening Hours</h2>
            <div class="hours-grid">
                <div class="hours-item">
                    <span class="hours-day">Monday - Friday</span>
                    <span class="hours-time"><?= formatHours($s1res['mon_fri_start'], $s1res['mon_fri_end']) ?></span>
                </div>
                <div class="hours-item">
                    <span class="hours-day">Saturday</span>
                    <span class="hours-time"><?= formatHours($s1res['sat_start'], $s1res['sat_end']) ?></span>
                </div>
                <div class="hours-item">
                    <span class="hours-day">Sunday</span>
                    <span class="hours-time"><?= formatHours($s1res['sun_start'], $s1res['sun_end']) ?></span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="content-section">
            <div class="action-buttons">
                <button class="btn btn-primary" onclick="navigateToService()" <?= !$isOpen ? 'disabled' : '' ?>>
                    <i class="fas fa-directions"></i> Get Directions
                </button>
                <button class="btn btn-secondary" onclick="window.open('tel:<?= htmlspecialchars($service['telephone_number']) ?>', '_self')">
                    <i class="fas fa-phone"></i> Call Now
                </button>
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (!empty($service['whatsapp_number'])): ?>
                <button class="btn btn-secondary" onclick="window.open('https://wa.me/+91<?= preg_replace('/\\D/', '', $service['whatsapp_number']) ?>', '_blank')">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </button>
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
            </div>
        </div>

        <!-- Rating System -->
        <div class="content-section">
            <h2 class="section-title"><i class="far fa-star"></i> <?= $existing ? 'Edit Your Rating' : 'Rate This Service' ?></h2>
            <div class="rating-container">
                <form action="rate_service.php" method="POST" class="rating-form">
                    <input type="hidden" name="service_id" value="<?= $service_id ?>">

                    <div class="star-rating">
                        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>"
                                   <?= (isset($existing['rating']) && $existing['rating'] == $i) ? 'checked' : '' ?> required>
                            <label for="star<?= $i ?>" title="<?= $i ?> stars">
                                <i class="fas fa-star"></i>
                            </label>
                        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endfor; ?>
                    </div>

                    <textarea name="message" placeholder="Share your experience (What did you like? What could be improved?)..." required><?= $existing['message'] ?? '' ?></textarea>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> <?= $existing ? 'Update Rating' : 'Submit Review' ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Customer Reviews -->
        <div class="content-section">
            <div class="reviews-header">
                <h2 class="section-title"><i class="far fa-comment-alt"></i> Customer Reviews</h2>
                <span class="reviews-count"><?= count($ratings) ?> review<?= count($ratings) !== 1 ? 's' : '' ?></span>
            </div>

            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (empty($ratings)): ?>
                <div class="no-reviews">
                    <i class="far fa-comment-dots" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p>No reviews yet. Be the first to review this service!</p>
                </div>
            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
else: ?>
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
foreach ($ratings as $row): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="reviewer">
                                <div class="reviewer-avatar">
                                    <?= strtoupper(substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="reviewer-name"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></div>
                                    <div class="review-date"><?= date('F j, Y', strtotime($row['updated_at'])) ?></div>
                                </div>
                            </div>
                            <div class="review-stars">
                                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= ($i <= $row['rating']) ? '' : 'far' ?>"></i>
                                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endfor; ?>
                            </div>
                        </div>
                        <div class="review-message">
                            <?= nl2br(htmlspecialchars($row['message'])) ?>
                        </div>
                    </div>
                <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endforeach; ?>
            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
        </div>
    </div>
</div>
  <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
include_once __DIR__ . '/../templates/footer.html'; ?>
<script>
    // Image Gallery
    let currentSlide = 0;
    const slides = document.querySelectorAll('.gallery-slide');
    const dots = document.querySelectorAll('.gallery-dot');

    function showSlide(n) {
        currentSlide = (n + slides.length) % slides.length;
        document.getElementById('gallerySlides').style.transform = `translateX(-${currentSlide * 100}%)`;
        
        // Update dots
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    function prevSlide() {
        showSlide(currentSlide - 1);
    }

    function goToSlide(n) {
        showSlide(n);
    }

    // Auto-rotate slides every 5 seconds
    let slideInterval = setInterval(nextSlide, 5000);

    // Pause auto-rotation when hovering over gallery
    document.querySelector('.gallery-container').addEventListener('mouseenter', () => {
        clearInterval(slideInterval);
    });

    // Resume auto-rotation when leaving gallery
    document.querySelector('.gallery-container').addEventListener('mouseleave', () => {
        slideInterval = setInterval(nextSlide, 5000);
    });

    // Navigation function
    function navigateToService() {
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if ($isOpen): ?>
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    let customerLat = position.coords.latitude;
                    let customerLon = position.coords.longitude;
                    let serviceLat = <?= $service['lat'] ?>;
                    let serviceLon = <?= $service['lon'] ?>;
                    window.open(`https://www.google.com/maps/dir/${customerLat},${customerLon}/${serviceLat},${serviceLon}`, '_blank');
                }, function(error) {
                    alert("Could not get your location. Please enable location services.");
                });
            } else {
                alert("Geolocation is not supported by your browser.");
            }
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
else: ?>
            alert("This service is currently closed. Please check the opening hours.");
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
    }
</script>

</body>
</html>

