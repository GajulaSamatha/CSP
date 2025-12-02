<?php

require_once __DIR__ . '/../src/mysqli_compat.php';
session_start();

require_once __DIR__ . '/../src/Db.php';
use App\Db;

$db_ok = false;
$db_error_msg = '';

try {
    $pdo = Db::getConnection();
    $row = $pdo->query('SELECT 1 AS ok')->fetch();
    $db_ok = !empty($row['ok']);
} catch (Throwable $e) {
    $db_ok = false;
    $db_error_msg = $e->getMessage();
    error_log('DB ERROR: ' . $db_error_msg);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LocalConnect - Find Local Services</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
      min-height: 100vh;
      color: #1e293b;
      line-height: 1.6;
    }

    /* Hero Section */
    .hero-section {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      padding: 4rem 2rem;
      background: linear-gradient(to right, #667eea, #764ba2);
      color: white;
    }

    .hero-content {
      flex: 1 1 400px;
      max-width: 600px;
      padding: 1rem;
    }

    .hero-title {
      font-size: 2.8rem;
      margin-bottom: 1.5rem;
      line-height: 1.2;
    }

    .hero-description {
      font-size: 1.2rem;
      margin-bottom: 2rem;
      opacity: 0.9;
    }

    .hero-button {
      display: inline-block;
      padding: 0.75rem 1.5rem;
      background: white;
      color: #667eea;
      font-weight: 600;
      border-radius: 8px;
      text-decoration: none;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .hero-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .hero-image {
      flex: 1 1 300px;
      max-width: 500px;
      padding: 1rem;
      text-align: center;
    }

    .hero-image img {
      width: 100%;
      max-width: 400px;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    /* Features Section */
    .features-section {
      background: linear-gradient(135deg, rgba(24, 29, 34, 0.9) 0%, rgba(219, 234, 254, 0.9) 50%, rgba(191, 219, 254, 0.9) 100%);
      padding: 5rem 2rem;
      position: relative;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      position: relative;
    }

    .section-title {
      text-align: center;
      font-size: 2.5rem;
      margin-bottom: 3rem;
      color: #1e293b;
      font-weight: 700;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 2fr));
      gap: 2rem;
      align-items: stretch;
      row:grid 2;
    }

    .feature-card {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      height: 100%;
      display: flex;
      flex-direction: column;
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
      font-size: 3rem;
      margin-bottom: 1.5rem;
      color: #3b82f6;
    }

    .feature-title {
      font-size: 1.4rem;
      margin-bottom: 1rem;
      font-weight: 600;
    }

    .feature-text {
      color: #64748b;
      margin-top: auto;
    }

    /* Categories Section */
    .popular-categories-section {
      background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #2563eb 100%);
      padding: 5rem 2rem;
      position: relative;
    }

    .popular-categories-section .section-title {
      color: white;
      text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .categories-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2rem;
    }

    .category-card {
      background: rgba(255, 255, 255, 0.95);
      padding: 2rem;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .category-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }

    .category-icon {
      font-size: 3rem;
      margin-bottom: 1.5rem;
      color: #3b82f6;
    }

    .category-title {
      font-size: 1.4rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .category-description {
      color: #64748b;
      margin-top: auto;
    }

    /* Map Section */
    .map-section {
      padding: 5rem 2rem;
      background: linear-gradient(135deg, rgba(30, 64, 175, 0.9) 0%, rgba(37, 99, 235, 0.9) 100%);
    }

    .map-container {
      width: 100%;
      height: 450px;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
      border: 2px solid rgba(255, 255, 255, 0.2);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
      .hero-title {
        font-size: 2.2rem;
      }
      
      .section-title {
        font-size: 2rem;
      }
      
      .features-grid, .categories-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      }
    }

    @media (max-width: 480px) {
      .hero-section {
        padding: 3rem 1rem;
      }
      
      .hero-title {
        font-size: 1.8rem;
      }
      
      .section-title {
        font-size: 1.6rem;
        margin-bottom: 2rem;
      }
      
      .feature-card, .category-card {
        padding: 1.5rem;
      }
    }

    /* Database Status */
    .db-status {
      position: fixed;
      bottom: 8px;
      right: 8px;
      padding: 6px 10px;
      font-size: 12px;
      border-radius: 4px;
      color: #fff;
      z-index: 9999;
    }

    .db-ok {
      background: #2d9b2d;
    }

    .db-err {
      background: #c0392b;
      max-width: 320px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  </style>
</head>

<body>
  <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
include_once __DIR__ . '/../templates/new_header.php'; ?>

  <section class="hero-section">
    <div class="hero-content">
      <h1 class="hero-title">Welcome to LocalConnect</h1>
      <p class="hero-description">
        Your trusted local directory for finding services like electricians, mechanics, 
        IT support, and more — right in your area.
      </p>
      <a href="new_services.php" class="hero-button">Explore Services</a>
    </div>
    <div class="hero-image">
      <img src="../assets/home.jpeg" alt="Local Services">
    </div>
  </section>

  <section class="features-section" id="features">
    <div class="container">
      <h2 class="section-title">Why Choose LocalConnect?</h2>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">⏱️</div>
          <h3 class="feature-title">Real-time Availability</h3>
          <p class="feature-text">
            See which providers are available right now in your area with live status updates.
          </p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">💬</div>
          <h3 class="feature-title">Direct Messaging</h3>
          <p class="feature-text">
            Contact service providers instantly through our secure messaging system.
          </p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">⭐</div>
          <h3 class="feature-title">Verified Reviews</h3>
          <p class="feature-text">
            Read authentic feedback from real customers before making your choice.
          </p>
        </div>
        
        <div class="feature-card">
          <div class="feature-icon">🌐</div>
          <h3 class="feature-title">Multi-language</h3>
          <p class="feature-text">
            Use the platform in your preferred language with auto-translation features.
          </p>
        </div>
        
      </div>
    </div>
  </section>

  <section class="popular-categories-section" id="services">
    <div class="container">
      <h2 class="section-title">Popular Service Categories</h2>
      <div class="categories-grid">
        <div class="category-card">
          <div class="category-icon">🧹</div>
          <h3 class="category-title">Cleaning</h3>
          <p class="category-description">Home & office cleaning services</p>
        </div>
        
        <div class="category-card">
          <div class="category-icon">🩺</div>
          <h3 class="category-title">Healthcare</h3>
          <p class="category-description">Doctors, clinics, and nurses</p>
        </div>
        
        <div class="category-card">
          <div class="category-icon">🔧</div>
          <h3 class="category-title">Home Repair</h3>
          <p class="category-description">Carpenters, masons, painters</p>
        </div>
        
        <div class="category-card">
          <div class="category-icon">🚰</div>
          <h3 class="category-title">Plumbing</h3>
          <p class="category-description">Leak fixing, tap installations</p>
        </div>
        
        <div class="category-card">
          <div class="category-icon">💡</div>
          <h3 class="category-title">Electrical</h3>
          <p class="category-description">Wiring, fans, switch repairs</p>
        </div>
        
        <div class="category-card">
          <div class="category-icon">🛠️</div>
          <h3 class="category-title">Mechanics</h3>
          <p class="category-description">Bike & car repair services</p>
        </div>
        
        <div class="category-card">
          <div class="category-icon">🍴</div>
          <h3 class="category-title">Restaurants</h3>
          <p class="category-description">Dine-in & delivery options</p>
        </div>
        
        <div class="category-card">
          <div class="category-icon">🏨</div>
          <h3 class="category-title">Hotels</h3>
          <p class="category-description">Local stays & lodges</p>
        </div>
      </div>
    </div>
  </section>

  <section class="map-section">
    <div class="container">
      <h2 class="section-title" style="color: white;">Find Services Near You</h2>
      <div class="map-container" id="map"></div>
    </div>
  </section>

  <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
include_once __DIR__ . '/../templates/footer.html'; ?>

  <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if ($db_ok): ?>
    <div class="db-status db-ok">DB OK</div>
  <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
else: ?>
    <div class="db-status db-err" title="<?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($db_error_msg); ?>">DB ERROR</div>
  <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>

  <script>
    // Initialize the map
    function initMap() {
      const map = L.map('map').setView([17.3850, 78.4867], 13); // Default to Hyderabad coordinates
      
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);
      
      // Try to get user's location
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          (position) => {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            
            map.setView([userLat, userLng], 13);
            
            // Add marker for user's location
            L.marker([userLat, userLng])
              .addTo(map)
              .bindPopup('Your Location')
              .openPopup();
          },
          (error) => {
            console.log('Geolocation error:', error);
            // Add default marker if location access is denied
            L.marker([17.3850, 78.4867])
              .addTo(map)
              .bindPopup('LocalConnect Services')
              .openPopup();
          }
        );
      } else {
        // Add default marker if geolocation is not supported
        L.marker([17.3850, 78.4867])
          .addTo(map)
          .bindPopup('LocalConnect Services')
          .openPopup();
      }
    }
    
    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', initMap);
  </script>
</body>
</html>

