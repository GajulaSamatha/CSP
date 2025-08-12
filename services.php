<?php
require_once 'db.php';

// Get filters from GET parameters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$location = $_GET['location'] ?? '';
$sort = $_GET['sort'] ?? '';

// Build SQL query
$sql = "SELECT * FROM services WHERE 1";
$params = [];

if ($search) {
    $sql .= " AND (name LIKE ? OR description LIKE ? OR category LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}
if ($location) {
    $sql .= " AND location = ?";
    $params[] = $location;
}

// Sorting
switch ($sort) {
    case 'rating-high':
        $sql .= " ORDER BY rating DESC";
        break;
    case 'rating-low':
        $sql .= " ORDER BY rating ASC";
        break;
    case 'price-low':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price-high':
        $sql .= " ORDER BY price DESC";
        break;
    default:
        $sql .= " ORDER BY id DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For filter dropdowns
$categories = $pdo->query("SELECT DISTINCT category FROM services")->fetchAll(PDO::FETCH_COLUMN);
$locations = $pdo->query("SELECT DISTINCT location FROM services")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Services - LocalConnect</title>
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        overflow-x:hidden;
      }

      body {
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI",
          Roboto, sans-serif;
        line-height: 1.6;
        color: #333;
        background-color: #f8fafc;
      }
      /* Hero Section */
      .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12.2rem 0;
        text-align: center;
      }

      .hero-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
      }

      .hero-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .hero-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
      }

      /* Filter Section */
      .filter-section {
        background: white;
        padding: 2rem 0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      }

      .filter-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
      }

      .filter-row {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
      }

      .search-box {
        flex: 1;
        min-width: 300px;
        max-width: 400px;
        position: relative;
      }

      .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 50px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
      }

      .search-input:focus {
        outline: none;
        border-color: #667eea;
      }

      .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1.2rem;
      }

      .filter-select {
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 50px;
        font-size: 1rem;
        background: white;
        min-width: 150px;
        transition: border-color 0.3s ease;
      }

      .filter-select:focus {
        outline: none;
        border-color: #667eea;
      }

      .filter-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
      }

      .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
      }

      /* Services Grid */
      .services-section {
        padding: 3rem 0;
      }

      .services-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
      }

      .section-header {
        text-align: center;
        margin-bottom: 3rem;
      }

      .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
      }

      .section-subtitle {
        font-size: 1.1rem;
        color: #64748b;
      }

      .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
      }

      .service-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        border: 1px solid #f1f5f9;
      }

      .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
      }

      .card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
      }

      .service-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: 600;
      }

      .service-info h3 {
        font-size: 1.3rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
      }

      .service-category {
        color: #667eea;
        font-size: 0.9rem;
        font-weight: 500;
      }

      .service-description {
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 1rem;
      }

      .service-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
      }

      .service-tag {
        background: #f1f5f9;
        color: #475569;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
      }

      .service-tag.available {
        background: #dcfce7;
        color: #16a34a;
      }

      .service-tag.top-rated {
        background: #fef3c7;
        color: #d97706;
      }

      .service-tag.new {
        background: #e0e7ff;
        color: #4f46e5;
      }

      .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid #f1f5f9;
        gap: 1rem;
      }

      .footer-left {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }

      .service-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }

      .stars {
        color: #fbbf24;
        font-size: 1.1rem;
      }

      .rating-text {
        color: #64748b;
        font-size: 0.9rem;
      }

      .service-price {
        color: #667eea;
        font-weight: 600;
        font-size: 1.1rem;
      }

      .footer-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.5rem;
      }

      .book-now-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 0.5rem 1.25rem;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        white-space: nowrap;
      }

      .book-now-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
      }

      .book-now-btn:active {
        transform: translateY(0);
      }

      /* Pagination */
      .pagination-section {
        text-align: center;
        padding: 2rem 0;
      }

      .load-more-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 1rem 3rem;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
      }

      .load-more-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
      }

      .pagination-info {
        margin-top: 1rem;
        color: #64748b;
        font-size: 0.9rem;
      }

      /* Responsive Design */
      @media (max-width: 768px) {
        .hero-title {
          font-size: 2rem;
        }

        .hero-subtitle {
          font-size: 1rem;
        }

        .filter-row {
          flex-direction: column;
          gap: 1rem;
        }

        .search-box {
          min-width: auto;
          max-width: none;
          width: 100%;
        }

        .filter-select {
          width: 100%;
        }

        .services-grid {
          grid-template-columns: 1fr;
          gap: 1.5rem;
        }

        .card-footer {
          flex-direction: column;
          align-items: stretch;
          gap: 1rem;
        }

        .footer-left {
          align-items: center;
        }

        .footer-right {
          align-items: center;
        }

        .book-now-btn {
          width: 100%;
        }

        .nav-links {
          display: none;
        }

        .section-title {
          font-size: 2rem;
        }
      }

      @media (min-width: 769px) and (max-width: 1024px) {
        .services-grid {
          grid-template-columns: repeat(2, 1fr);
        }
      }

      @media (min-width: 1025px) {
        .services-grid {
          grid-template-columns: repeat(4, 1fr);
        }
      }

      /* Loading state */
      .loading {
        text-align: center;
        padding: 2rem;
      }

      .loading-spinner {
        border: 4px solid #f3f4f6;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
      }

      @keyframes spin {
        0% {
          transform: rotate(0deg);
        }
        100% {
          transform: rotate(360deg);
        }
      }

      .view-details-btn {
          background: transparent;
          color: #667eea;
          border: none;
          text-decoration: underline;
          cursor: pointer;
          font-size: 0.85rem;
          font-weight: 500;
          padding: 0;
        }

        .view-details-btn:hover {
          color: #4f46e5;
        }


    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  </head>
  <body>
    <!-- Header -->
    <!-- <div id="include-header"></div> -->
 

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-container">
        <h1 class="hero-title">Find Local Services</h1>
        <p class="hero-subtitle">
          Discover trusted service providers in your area. From home repairs to
          personal care, we connect you with the best local professionals.
        </p>
      </div>
    </section>

    <!-- Price Comparison Tool -->
    <!-- <section class="filter-section" style="padding-bottom:0;">
      <div class="filter-container" style="padding-bottom:0;">
        <?php
          $bestService = null;
          if (count($services) > 0) {
            $minPrice = min(array_column($services, 'price'));
            foreach ($services as $srv) {
              if ($srv['price'] == $minPrice) {
                $bestService = $srv;
                break;
              }
            }
          }
        ?>
        <?php if ($bestService): ?>
          <div style="background: #e0f7fa; border-radius: 12px; padding: 1rem 2rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1.5rem; justify-content: center;">
            <span style="font-size:1.2rem;font-weight:600;color:#16a34a;">Best Low Cost Service:</span>
            <span style="font-size:1.1rem;font-weight:500; color:#1e293b;">
              <?php echo htmlspecialchars($bestService['name']); ?> (₹<?php echo htmlspecialchars($bestService['price']); ?>)
            </span>
            <span style="background:#16a34a;color:white;padding:0.3rem 1rem;border-radius:20px;font-size:0.95rem;">Lowest Price</span>
          </div>
        <?php endif; ?>
      </div>
    </section> -->

    <!-- Filter Section -->
    <section class="filter-section">
      <div class="filter-container">
        <form method="get" class="filter-row" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;justify-content:center;">
          <div class="search-box">
            <span class="search-icon">🔍</span>
            <input
              type="text"
              class="search-input"
              placeholder="Search services..."
              name="search"
              value="<?php echo htmlspecialchars($search); ?>"
            />
          </div>
          <select class="filter-select" name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>" <?php if ($cat == $category) echo 'selected'; ?>>
                    <?php echo ucfirst($cat); ?>
                </option>
            <?php endforeach; ?>
          </select>
          <select class="filter-select" name="location">
            <option value="">All Locations</option>
            <?php foreach ($locations as $loc): ?>
                <option value="<?php echo htmlspecialchars($loc); ?>" <?php if ($loc == $location) echo 'selected'; ?>>
                    <?php echo ucfirst($loc); ?>
                </option>
            <?php endforeach; ?>
          </select>
          <select class="filter-select" name="sort">
            <option value="">Sort by</option>
            <option value="rating-high" <?php if ($sort=='rating-high') echo 'selected'; ?>>Rating (High to Low)</option>
            <option value="rating-low" <?php if ($sort=='rating-low') echo 'selected'; ?>>Rating (Low to High)</option>
            <option value="price-low" <?php if ($sort=='price-low') echo 'selected'; ?>>Price (Low to High)</option>
            <option value="price-high" <?php if ($sort=='price-high') echo 'selected'; ?>>Price (High to Low)</option>
          </select>
          <button class="filter-btn" type="submit">Filter</button>
        </form>
      </div>
    </section>

    <!-- Services Section -->
    <section class="services-section">
      <div class="services-container">
        <div class="section-header">
          <h2 class="section-title">Available Services</h2>
          <p class="section-subtitle">
            Browse through our verified service providers
          </p>
        </div>
        <div class="services-grid" id="servicesGrid">
          <?php if (count($services) === 0): ?>
            <p>No services found.</p>
          <?php else: ?>
            <?php foreach ($services as $index => $service): ?>
              <div class="service-card"
                data-category="<?php echo htmlspecialchars($service['category']); ?>"
                data-location="<?php echo htmlspecialchars($service['location']); ?>"
                data-rating="<?php echo htmlspecialchars($service['rating']); ?>"
                data-price="<?php echo htmlspecialchars($service['price']); ?>"
                data-available="<?php echo $service['available'] ? 'true' : 'false'; ?>"
                style="<?php echo $index >= 8 ? 'display:none;' : ''; ?>">
                <div class="card-header">
                  <div class="service-avatar">
                    <?php echo strtoupper(substr($service['name'], 0, 2)); ?>
                  </div>
                  <div class="service-info">
                    <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                    <div class="service-category"><?php echo htmlspecialchars($service['category']); ?></div>
                  </div>
                </div>
                <p class="service-description">
                  <?php echo htmlspecialchars($service['description']); ?>
                </p>
                <div class="service-tags">
                  <?php if ($service['available']): ?>
                    <span class="service-tag available">Available Now</span>
                  <?php endif; ?>
                  <?php if ($service['rating'] !== null && $service['rating'] >= 4.8): ?>
                    <span class="service-tag top-rated">Top Rated</span>
                  <?php endif; ?>
                </div>
                <div class="card-footer">
                  <div class="footer-left">
                    <div class="service-rating" style="display:flex;flex-direction:column;align-items:flex-start;gap:0.2rem;">
                      <?php if ($service['rating'] !== null): ?>
                        <span class="rating-text" style="font-size:1.1rem;font-weight:600;color:#1e293b;">Ratings :<?php echo htmlspecialchars($service['rating']); ?></span>
                      <?php else: ?>
                        <span class="rating-text" style="font-size:1.1rem;font-weight:600;color:#1e293b;">No rating</span>
                      <?php endif; ?>
                      <!-- <span class="stars"><?php echo $service['rating'] !== null ? str_repeat('★', round($service['rating'])) : ''; ?></span> -->
                    </div>
                    <!-- <div class="service-price">₹<?php echo htmlspecialchars($service['price']); ?></div> -->
                  </div>
                  <div class="footer-right" style="display:flex;gap:0.5rem;align-items:center;">
                    <!-- Call and WhatsApp icons -->
                    <?php if (!empty($service['PhoneNumber'])): ?>
                      <?php 
                        // Extract the first number if multiple numbers are present
                        $numbers = preg_split('/[\s,;\/]+/', $service['PhoneNumber']);
                        $firstNumber = $numbers[0];
                        $waNumber = preg_replace('/\D/', '', $firstNumber);
                      ?>
                      <div id="phoneCard" style="display:none; position:fixed; bottom:20px; right:20px; background:#fff; padding:15px 20px; border-radius:10px; box-shadow:0 4px 8px rgba(0,0,0,0.2); font-family:Arial; z-index:999;">
                          <p style="margin:0; font-size:1.1rem; color:#333;"><strong>Phone:</strong> <span id="phoneText"></span></p>
                          <button onclick="closePhoneCard()" style="margin-top:10px; padding:8px 12px; background:#16a34a; color:#fff; border:none; border-radius:5px; cursor:pointer;">Close</button>
                      </div>
                      <a href="tel:<?php echo htmlspecialchars($firstNumber); ?>" 
   title="Call" 
   style="color:#16a34a;font-size:1.3rem;" 
   onclick="displayMobileNumber(event)">
   <i class="fa fa-phone"></i>
</a>

                      <a href="https://wa.me/<?php echo $waNumber; ?>" target="_blank" title="Chat on WhatsApp" style="color:#25d366;font-size:1.3rem; margin-left: 0.3rem;"><i class="fab fa-whatsapp"></i></a>
                    <?php else: ?>
                      <span style="color:#94a3b8;font-size:1.1rem;">No contact</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <!-- Load More Button -->
        <?php if (count($services) > 8): ?>
        <div style="text-align:center; margin-top:2rem;">
          <button id="loadMoreBtn" class="load-more-btn">Load More</button>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Footer -->
    <div id="include-footer"></div>

    <script>
      // Load includes and then run app logic
      async function includeHTML(id, file) {
        const res = await fetch(file);
        const data = await res.text();
        document.getElementById(id).innerHTML = data;
      }

      // Search and filter functionality
      let allServices = [];
      let filteredServices = [];
      let currentPage = 1;
      const servicesPerPage = 8;

      // Initialize everything when DOM is ready
      document.addEventListener("DOMContentLoaded", async function () {
        console.log("DOM Content Loaded - Starting initialization");
        
        // Load header and footer first
        // await includeHTML("include-header", "header.html");
        await includeHTML("include-footer", "footer.html");
        console.log("Header and footer loaded");

        // Initialize services data
        allServices = Array.from(document.querySelectorAll(".service-card"));
        filteredServices = [...allServices];
        displayFilteredServices();
        updatePaginationInfo();
        console.log("Services initialized");

        // Setup header behavior after a short delay
        setTimeout(() => {
          setupHeaderBehavior();
        }, 300);
      });

      function applyFilters() {
        const searchTerm = document.getElementById("searchInput").value.toLowerCase();

        const categoryFilter = document.getElementById("categoryFilter").value;
        const locationFilter = document.getElementById("locationFilter").value;
        const sortFilter = document.getElementById("sortFilter").value;

        filteredServices = allServices.filter((card) => {
          const cardText = card.textContent.toLowerCase();
          const cardCategory = card.getAttribute("data-category");
          const cardLocation = card.getAttribute("data-location");

          const matchesSearch = !searchTerm || cardText.includes(searchTerm);
          const matchesCategory =
            !categoryFilter || cardCategory === categoryFilter;
          const matchesLocation =
            !locationFilter || cardLocation === locationFilter;

          return matchesSearch && matchesCategory && matchesLocation;
        });

        // Apply sorting
        if (sortFilter) {
          applySorting(sortFilter);
        }

        displayFilteredServices();
        updatePaginationInfo();
      }

      function applySorting(sortType) {
        filteredServices.sort((a, b) => {
          switch (sortType) {
            case 'rating-high':
              return parseFloat(b.getAttribute('data-rating')) - parseFloat(a.getAttribute('data-rating'));
            case 'rating-low':
              return parseFloat(a.getAttribute('data-rating')) - parseFloat(b.getAttribute('data-rating'));
            case 'availability':
              const aAvailable = a.getAttribute('data-available') === 'true';
              const bAvailable = b.getAttribute('data-available') === 'true';
              return bAvailable - aAvailable;
            case 'proximity':
              // Simulate proximity sorting (in real app, would use actual coordinates)
              const locations = ['downtown', 'north', 'south', 'east', 'west', 'suburbs'];
              const aProximity = locations.indexOf(a.getAttribute('data-location'));
              const bProximity = locations.indexOf(b.getAttribute('data-location'));
              return aProximity - bProximity;
            case 'price-low':
              return parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price'));
            case 'price-high':
              return parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price'));
            default:
              return 0;
          }
        });
      }

      function displayFilteredServices() {
        const grid = document.getElementById("servicesGrid");

        // Hide all services first
        allServices.forEach((card) => {
          card.style.display = "none";
        });

        // Show filtered services
        const startIndex = 0;
        const endIndex = Math.min(
          currentPage * servicesPerPage,
          filteredServices.length
        );

        for (let i = startIndex; i < endIndex; i++) {
          if (filteredServices[i]) {
            filteredServices[i].style.display = "block";
          }
        }
      }

      function loadMoreServices() {
        const currentlyShown = currentPage * servicesPerPage;
        const totalServices = filteredServices.length;

        if (currentlyShown < totalServices) {
          currentPage++;

          // Show loading state
          const loadBtn = document.querySelector(".load-more-btn");
          const originalText = loadBtn.textContent;
          loadBtn.innerHTML = '<div class="loading-spinner"></div> Loading...';
          loadBtn.disabled = true;

          // Simulate loading delay
          setTimeout(() => {
            displayFilteredServices();
            updatePaginationInfo();

            // Reset button
            loadBtn.textContent = originalText;
            loadBtn.disabled = false;

            // Hide load more button if all services are shown
            if (currentPage * servicesPerPage >= totalServices) {
              loadBtn.style.display = "none";
            }
          }, 800);
        }
      }

      function updatePaginationInfo() {
        const totalServices = filteredServices.length;
        const shownServices = Math.min(
          currentPage * servicesPerPage,
          totalServices
        );
        const paginationInfo = document.querySelector(".pagination-info");
        const loadMoreBtn = document.querySelector(".load-more-btn");

        paginationInfo.textContent = `Showing ${shownServices} of ${totalServices} services`;

        // Show/hide load more button
        if (shownServices >= totalServices) {
          loadMoreBtn.style.display = "none";
        } else {
          loadMoreBtn.style.display = "inline-block";
        }
      }

      // Book service function
      function bookService(serviceName) {
        alert(`Booking ${serviceName}! This would open the booking modal or redirect to booking page.`);
      }

      // Header behavior setup
      function setupHeaderBehavior() {
        console.log("Setting up header behavior...");
        
        const header = document.getElementById("header");
        const mobileMenuBtn = document.getElementById("mobileMenuBtn");
        const mobileMenu = document.getElementById("mobileMenu");
        const locationIndicator = document.querySelector(".location-indicator");

        console.log("Mobile menu button:", mobileMenuBtn);
        console.log("Mobile menu:", mobileMenu);

        // Header scroll effect
        if (header) {
          window.addEventListener("scroll", () => {
            if (window.scrollY > 100) {
              header.classList.add("scrolled");
            } else {
              header.classList.remove("scrolled");
            }
          });
        }

        // Mobile menu toggle
        if (mobileMenuBtn && mobileMenu) {
          console.log("Setting up mobile menu functionality");
          
          // Remove any existing event listeners
          mobileMenuBtn.replaceWith(mobileMenuBtn.cloneNode(true));
          const newMobileMenuBtn = document.getElementById("mobileMenuBtn");
          
          newMobileMenuBtn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log("Mobile menu button clicked");
            newMobileMenuBtn.classList.toggle("active");
            mobileMenu.classList.toggle("active");
          });

          // Close mobile menu when clicking on links
          document.querySelectorAll(".mobile-nav-links a, .mobile-auth-buttons a").forEach((link) => {
            link.addEventListener("click", () => {
              newMobileMenuBtn.classList.remove("active");
              mobileMenu.classList.remove("active");
            });
          });

          // Close mobile menu with close button
          const mobileMenuClose = document.getElementById("mobileMenuClose");
          if (mobileMenuClose) {
            mobileMenuClose.addEventListener("click", () => {
              newMobileMenuBtn.classList.remove("active");
              mobileMenu.classList.remove("active");
            });
          }

          // Close mobile menu when clicking outside
          document.addEventListener("click", (event) => {
            if (!mobileMenu.contains(event.target) && !newMobileMenuBtn.contains(event.target)) {
              newMobileMenuBtn.classList.remove("active");
              mobileMenu.classList.remove("active");
            }
          });
        } else {
          console.log("Mobile menu elements not found, retrying in 500ms...");
          setTimeout(setupHeaderBehavior, 500);
        }

        // Location indicator click handler
        if (locationIndicator) {
          locationIndicator.addEventListener("click", () => {
            showLocationPicker();
          });
        }
      }

      // Real-time search
      document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchInput");
        if (searchInput) {
          searchInput.addEventListener("input", function () {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
              currentPage = 1;
              applyFilters();
            }, 300);
          });
        }
      });

      // Filter change handlers
      document.addEventListener("DOMContentLoaded", function() {
        const categoryFilter = document.getElementById("categoryFilter");
        const locationFilter = document.getElementById("locationFilter");
        const sortFilter = document.getElementById("sortFilter");

        if (categoryFilter) {
          categoryFilter.addEventListener("change", function () {
            currentPage = 1;
            applyFilters();
          });
        }

        if (locationFilter) {
          locationFilter.addEventListener("change", function () {
            currentPage = 1;
            applyFilters();
          });
        }

        if (sortFilter) {
          sortFilter.addEventListener("change", function () {
            currentPage = 1;
            applyFilters();
          });
        }
      });

      // Service card click handlers (excluding book button clicks)
      document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".service-card").forEach((card) => {
          card.addEventListener("click", function (e) {
            // Don't trigger card click if book button was clicked
            if (e.target.classList.contains('book-now-btn')) {
              return;
            }
            const serviceName = this.querySelector("h3").textContent;
            alert(
              `Clicked on ${serviceName}. Here you would navigate to the service detail page.`
            );
          });
        });
      });

      function displayMobileNumber(event) {
    event.preventDefault(); // Stop direct call

    // Get the phone number from the clicked link
    const phone = event.currentTarget.getAttribute('href').replace('tel:', '');

    // Set the phone number in the card
    document.getElementById('phoneText').textContent = phone;

    // Show the card
    document.getElementById('phoneCard').style.display = 'block';
}

function closePhoneCard() {
    document.getElementById('phoneCard').style.display = 'none';
}


      // Load More functionality
      document.addEventListener("DOMContentLoaded", function() {
        const loadMoreBtn = document.getElementById("loadMoreBtn");
        if (!loadMoreBtn) return;
        let shown = 8;
        const cards = document.querySelectorAll(".service-card");
        loadMoreBtn.addEventListener("click", function() {
          let count = 0;
          for (let i = shown; i < cards.length && count < 8; i++, count++) {
            cards[i].style.display = "block";
          }
          shown += count;
          if (shown >= cards.length) {
            loadMoreBtn.style.display = "none";
          }
        });
      });
    </script>
    <script type="text/javascript">
      function googleTranslateElementInit() {
        new google.translate.TranslateElement({
          pageLanguage: 'en',
          layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL,
          autoDisplay: false
        }, 'google_translate_element');
      }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
  </body>
</html>