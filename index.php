<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LocalConnect - Header</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI",
        Roboto, sans-serif;
      background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
      min-height: 100vh;
    }
   


    .features-section {
      background: linear-gradient(135deg,
          rgba(24, 29, 34, 0.9) 0%,
          rgba(219, 234, 254, 0.9) 50%,
          rgba(191, 219, 254, 0.9) 100%);
      padding: 80px 20px;
      position: relative;
      overflow: hidden;
    }

    .features-section::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
      opacity: 0.3;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      position: relative;
      z-index: 2;
    }

    .section-title {
      text-align: center;
      font-size: 2.5rem;
      margin-bottom: 3rem;
      color: #1e293b;
      font-weight: 700;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2.5rem;
    }

    .feature-card {
      background: rgba(255, 255, 255, 0.8);
      border-radius: 16px;
      padding: 2rem;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(20px);
      position: relative;
      overflow: hidden;
    }

    .feature-card::before {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg,
          transparent,
          rgba(255, 255, 255, 0.4),
          transparent);
      transition: left 0.6s ease;
    }

    .feature-card:hover::before {
      left: 100%;
    }

    .feature-card:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
      background: rgba(255, 255, 255, 0.9);
    }

    .feature-icon {
      font-size: 3rem;
      margin-bottom: 1.5rem;
      color: #3b82f6;
      display: inline-block;
    }

    .feature-title {
      font-size: 1.4rem;
      color: #1e293b;
      margin-bottom: 0.75rem;
      font-weight: 600;
    }

    .feature-text {
      color: #64748b;
      font-size: 1rem;
      line-height: 1.6;
    }

    .popular-categories-section {
      background: linear-gradient(135deg,
          #1e40af 0%,
          #3b82f6 50%,
          #2563eb 100%);
      padding: 80px 20px;
      position: relative;
      overflow: hidden;
    }

    .popular-categories-section::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(circle at 20% 80%,
          rgba(255, 255, 255, 0.1) 0%,
          transparent 50%),
        radial-gradient(circle at 80% 20%,
          rgba(255, 255, 255, 0.1) 0%,
          transparent 50%);
    }

    .popular-categories-section .section-title {
      text-align: center;
      font-size: 2.5rem;
      color: white;
      margin-bottom: 50px;
      font-weight: 700;
      text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
      position: relative;
      z-index: 2;
    }

    .categories-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
      position: relative;
      z-index: 2;
    }

    .category-card {
      background: rgba(255, 255, 255, 0.9);
      padding: 2rem;
      border-radius: 16px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      backdrop-filter: blur(20px);
      position: relative;
      overflow: hidden;
    }

    .category-card::after {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #3b82f6, #1d4ed8, #2563eb);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .category-card:hover::after {
      transform: scaleX(1);
    }

    .category-card:hover {
      transform: translateY(-15px) scale(1.03);
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
      background: rgba(255, 255, 255, 0.95);
    }

    .category-icon {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: #3b82f6;
      display: inline-block;
    }

    .category-title {
      font-size: 1.4rem;
      font-weight: 600;
      margin-bottom: 0.75rem;
      color: #1e293b;
    }

    .category-description {
      font-size: 1rem;
      color: #64748b;
      margin-bottom: 1rem;
      line-height: 1.5;
    }

    .status-indicator {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }

    .status-indicator.available {
      background: linear-gradient(135deg, #22c55e, #16a34a);
      box-shadow: 0 0 15px rgba(34, 197, 94, 0.5);
    }

    .status-indicator.busy {
      background: linear-gradient(135deg, #facc15, #eab308);
      box-shadow: 0 0 15px rgba(250, 204, 21, 0.5);
    }

    .status-indicator.offline {
      background: linear-gradient(135deg, #ef4444, #dc2626);
      box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
    }

    .category-status {
      font-weight: 600;
      color: #3b82f6;
      font-size: 1rem;
    }

    .map-section {
      padding: 80px 20px;
      background: linear-gradient(135deg,
          rgba(30, 64, 175, 0.9) 0%,
          rgba(37, 99, 235, 0.9) 50%,
          rgba(59, 130, 246, 0.9) 100%);
      position: relative;
      overflow: hidden;
    }

    .map-section::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23ffffff" stroke-width="0.5" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
      opacity: 0.3;
    }

    .map-container {
      width: 100%;
      height: 450px;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      position: relative;
      z-index: 2;
      border: 2px solid rgba(255, 255, 255, 0.1);
    }
  </style>
</head>

<body>
  
       <?php include "new_header.php"; ?>
  <section class="hero-section" style="
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        padding: 4rem 0;
        background: linear-gradient(to right, #667eea, #764ba2);
        color: white;
      ">
    <div style="flex: 1 1 400px; max-width: 600px; padding: 1rem">
      <h1 style="font-size: 3rem; margin-bottom: 1rem">
        Welcome to LocalConnect
      </h1>
      <p style="font-size: 1.2rem; margin-bottom: 2rem">
        Your trusted local directory for finding services like electricians,
        mechanics, IT support, and more — right in your area.
      </p>
      <a href="new_services.php" style="
            padding: 0.75rem 1.5rem;
            background: white;
            color: #667eea;
            font-weight: bold;
            border-radius: 8px;
            text-decoration: none;
          ">Explore Services</a>
    </div>

    <div style="flex: 1 1 300px; max-width: 500px; padding: 1rem">
      <img src="./assets/home.jpeg" alt="Local Services" style="
            width: 90%;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
          " />
    </div>
  </section>

  <!-- <section class="search-section" style="padding: 2rem 1rem; background: #f8fafc">
    <div style="
          max-width: 800px;
          margin: 0 auto;
          background: white;
          padding: 2rem;
          border-radius: 12px;
          box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        ">
      <form style="
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            justify-content: space-between;
          ">
        <div style="flex: 1 1 300px">
          <label for="service" style="display: block; font-weight: bold; margin-bottom: 0.5rem">Service</label>
          <input type="text" id="service" placeholder="e.g. Electrician, Mechanic" style="
                width: 100%;
                padding: 0.75rem;
                border: 1px solid #cbd5e1;
                border-radius: 8px;
              " />
        </div>

        <div style="flex: 1 1 300px; position: relative;">
          <label for="location" style="display: block; font-weight: bold; margin-bottom: 0.5rem">Location</label>
          <div style="position: relative; display: flex; align-items: center;">
            <input type="text" id="location" placeholder="e.g. Hyderabad, Anantapur" style="
                  width: 100%;
                  padding: 0.75rem 50px 0.75rem 0.75rem;
                  border: 1px solid #cbd5e1;
                  border-radius: 8px;
                " />
            <button type="button" id="use-live-location" title="Use Live Location" style="
                  position: absolute;
                  right: 8px;
                  top: 50%;
                  transform: translateY(-50%);
                  background: none;
                  border: none;
                  font-size: 1.2rem;
                  cursor: pointer;
                  padding: 4px;
                  border-radius: 4px;
                  color: #667eea;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                ">📍</button>
          </div>
        </div>

        <div style="flex: 1 1 100px; align-self: flex-end">
          <button type="submit" style="
                padding: 0.75rem 1.5rem;
                background: #667eea;
                color: white;
                font-weight: bold;
                border: none;
                border-radius: 8px;
                cursor: pointer;
              ">
            Search
          </button>
        </div>
      </form>
    </div>
  </section> -->

  <section class="features-section" id="features">
    <div class="container">
      <h2 class="section-title">Why Choose LocalConnect?</h2>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">⏱️</div>
          <h3 class="feature-title">Real-time Availability</h3>
          <p class="feature-text">
            See which providers are available right now in your area.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">💬</div>
          <h3 class="feature-title">Direct Messaging</h3>
          <p class="feature-text">
            Contact service providers instantly and securely.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">⭐</div>
          <h3 class="feature-title">Verified Reviews</h3>
          <p class="feature-text">
            Read honest feedback from real customers before you book.
          </p>
        </div>
       
        <div class="feature-card">
          <div class="feature-icon">🌐</div>
          <h3 class="feature-title">Multi-language Support</h3>
          <p class="feature-text">
            Use the platform in your preferred language with auto-translation.
          </p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">📍</div>
          <h3 class="feature-title">Location-Based Search</h3>
          <p class="feature-text">
            Find services near you using GPS or manual location input.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="popular-categories-section" id="services">
    <div class="container">
      <h2 class="section-title">Popular Categories</h2>
      <div class="categories-grid">
        <div class="category-card">
          <div class="category-icon">🧹</div>
          <h3 class="category-title">Cleaning</h3>
          <p class="category-description">Home & Office Cleaning Services</p>
          
        </div>
  
        <div class="category-card">
          <div class="category-icon">🩺</div>
          <h3 class="category-title">Healthcare</h3>
          <p class="category-description">Doctors, Clinics, Nurses</p>
        </div>
  
        <div class="category-card">
          <div class="category-icon">🔧</div>
          <h3 class="category-title">Home Repair</h3>
          <p class="category-description">Carpenters, Masons, Painters</p>
        </div>
  
        <div class="category-card">
          <div class="category-icon">🚰</div>
          <h3 class="category-title">Plumbing</h3>
          <p class="category-description">Leak Fixing, Tap Installations</p>
        </div>
  
        <div class="category-card">
          <div class="category-icon">💡</div>
          <h3 class="category-title">Electrical</h3>
          <p class="category-description">Wiring, Fans, Switch Repairs</p>
        </div>
  
        <div class="category-card">
          <div class="category-icon">🛠</div>
          <h3 class="category-title">Mechanics</h3>
          <p class="category-description">Bike & Car Repairs</p>
          <!-- <span class="status-indicator available"></span> -->
          <!-- <span class="category-status">41 Available Now</span> -->
        </div>
  
        <div class="category-card">
          <div class="category-icon">🍴</div>
          <h3 class="category-title">Restaurant</h3>
          <p class="category-description">Dine-in & Delivery Services</p>
          <!-- <span class="status-indicator available"></span> -->
          <!-- <span class="category-status">83 Available Now</span> -->
        </div>
  
        <div class="category-card">
          <div class="category-icon">🏨</div>
          <h3 class="category-title">Hotel</h3>
          <p class="category-description">Local Stays & Lodges</p>
          <!-- <span class="status-indicator available"></span> -->
          <!-- <span class="category-status">29 Available Now</span> -->
        </div>
  
        <div class="category-card">
          <div class="category-icon">🐾</div>
          <h3 class="category-title">Pet Care</h3>
          <p class="category-description">Vets, Grooming, Walkers</p>
          <!-- <span class="status-indicator busy"></span> -->
          <!-- <span class="category-status">18 Available Now</span> -->
        </div>
  
        <div class="category-card">
          <div class="category-icon">🌿</div>
          <h3 class="category-title">Gardening</h3>
          <p class="category-description">Garden Setup & Maintenance</p>
          <!-- <span class="status-indicator available"></span> -->
          <!-- <span class="category-status">22 Available Now</span> -->
        </div>
  
        <div class="category-card">
          <div class="category-icon">🧖</div>
          <h3 class="category-title">Personal Services</h3>
          <p class="category-description">Beauticians, Spa, Massage</p>
          <!-- <span class="status-indicator busy"></span> -->
          <!-- <span class="category-status">30 Available Now</span> -->
        </div>
  
        <div class="category-card">
          <div class="category-icon">📘</div>
          <h3 class="category-title">Tutoring</h3>
          <p class="category-description">Home & Online Tutors</p>
          <!-- <span class="status-indicator available"></span> -->
          <!-- <span class="category-status">40 Available Now</span> -->
        </div>
  
        <div class="category-card">
          <div class="category-icon">💅</div>
          <h3 class="category-title">Beauty & Wellness</h3>
          <p class="category-description">Salons, Makeup Artists</p>
          <!-- <span class="status-indicator available"></span> -->
          <!-- <span class="category-status">36 Available Now</span> -->
        </div>
  
        <div class="category-card">
          <div class="category-icon">🏢</div>
          <h3 class="category-title">Company</h3>
          <p class="category-description">Business Services, Agencies</p>
          <!-- <span class="status-indicator offline"></span> -->
          <!-- <span class="category-status">15 Available Now</span> -->
        </div>
      </div>
    </div>
  </section>

  <div id="include-footer"></div>

  <script>
    // Global variables to store location data
    let userLocation = { lat: null, lng: null, city: null, state: null };
    let locationRequested = false;

    // Load includes and then run app logic
    async function includeHTML(id, file) {
      try {
        const res = await fetch(file);
        const data = await res.text();
        document.getElementById(id).innerHTML = data;
      } catch (error) {
        console.error(`Failed to load ${file}:`, error);
      }
    }

    async function initializePage() {
      // Load header and footer first
      // await includeHTML("include-header", "header.php");
      await includeHTML("include-footer", "footer.html");

      // Wait for DOM elements to be available
      setTimeout(() => {
        setupLocationDetection();
        setupHeaderBehavior();
        setupMap();
      }, 100);
    }

    function setupLocationDetection() {
      // Only request location once
      if (!locationRequested && navigator.geolocation) {
        locationRequested = true;
        console.log("Requesting user location...");
        
        navigator.geolocation.getCurrentPosition(
          async (position) => {
            console.log("Location obtained successfully");
            const { latitude, longitude } = position.coords;
            userLocation.lat = latitude;
            userLocation.lng = longitude;
            await updateLocationFromCoords(latitude, longitude);
          },
          (error) => {
            console.log("Location access denied or failed:", error.message);
            // Set default location to Hyderabad
            setDefaultLocation();
          },
          { 
            timeout: 10000,
            enableHighAccuracy: true,
            maximumAge: 300000 // 5 minutes
          }
        );
      } else if (!navigator.geolocation) {
        console.log("Geolocation not supported");
        setDefaultLocation();
      }
    }

    function setDefaultLocation() {
      console.log("Setting default location");
      userLocation = { lat: 78.4867, lng: 17.3850, city: "Nandyal", state: "Andhra Pradesh" };
      updateLocationDisplay("Nandyal, Andhra Pradesh");
      updateLocationInput("Nandyal, Andhra Pradesh");
    }

    function setupHeaderBehavior() {
      const header = document.getElementById("header");
      const mobileMenuBtn = document.getElementById("mobileMenuBtn");
      const mobileMenu = document.getElementById("mobileMenu");

      if (header) {
        window.addEventListener("scroll", () => {
          if (window.scrollY > 100) {
            header.classList.add("scrolled");
          } else {
            header.classList.remove("scrolled");
          }
        });
      }

      if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener("click", () => {
          mobileMenuBtn.classList.toggle("active");
          mobileMenu.classList.toggle("active");
        });

        document.querySelectorAll(".mobile-nav-links a, .mobile-auth-buttons a").forEach((link) => {
          link.addEventListener("click", () => {
            mobileMenuBtn.classList.remove("active");
            mobileMenu.classList.remove("active");
          });
        });
      }
    }

    async function updateLocationFromCoords(lat, lng) {
      try {
        console.log(`Fetching location details for coordinates: ${lat}, ${lng}`);
        const response = await fetch(
          `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=en`
        );
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log("Geocoding response:", data);
        
        const city = data.city || data.locality || data.principalSubdivision || "Unknown City";
        const state = data.principalSubdivision || data.region || "Unknown State";
        
        userLocation.city = city;
        userLocation.state = state;
        
        const locationString = `${city}, ${state}`;
        console.log(`Location determined: ${locationString}`);
        
        updateLocationDisplay(locationString);
        updateLocationInput(locationString);
        
      } catch (error) {
        console.error("Failed to fetch location details:", error);
        setDefaultLocation();
      }
    }

    function updateLocationDisplay(locationString) {
      // Try to update location display in header
      const locationElements = [
        document.querySelector(".location-text"),
        document.querySelector(".current-location"),
        document.querySelector("#current-location")
      ];
      
      locationElements.forEach(element => {
        if (element) {
          element.textContent = locationString;
          console.log("Updated location display:", locationString);
        }
      });
    }

    function updateLocationInput(locationString) {
      // Update the location input field
      const locationInput = document.getElementById("location");
      if (locationInput) {
        locationInput.value = locationString;
        console.log("Updated location input:", locationString);
      }
    }
      const liveLocationBtn = document.getElementById("use-live-location");
      const locationInput = document.getElementById("location");
      
      if (liveLocationBtn && locationInput) {
        liveLocationBtn.addEventListener("click", () => {
          // Show loading state
          liveLocationBtn.innerHTML = "⏳";
          liveLocationBtn.style.color = "#fbbf24";
          liveLocationBtn.disabled = true;
          
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
              async (position) => {
                const { latitude, longitude } = position.coords;
                console.log("Live location requested:", latitude, longitude);
                
                try {
                  // Update the global location
                  userLocation.lat = latitude;
                  userLocation.lng = longitude;
                  
                  // Get address for these coordinates
                  await updateLocationFromCoords(latitude, longitude);
                  
                  // Success state
                  liveLocationBtn.innerHTML = "✅";
                  liveLocationBtn.style.color = "#22c55e";
                  
                  // Reset button after 2 seconds
                  setTimeout(() => {
                    liveLocationBtn.innerHTML = "📍";
                    liveLocationBtn.style.color = "#667eea";
                    liveLocationBtn.disabled = false;
                  }, 2000);
                  
                } catch (error) {
                  console.error("Failed to get address for live location:", error);
                  // Error state
                  liveLocationBtn.innerHTML = "❌";
                  liveLocationBtn.style.color = "#ef4444";
                  
                  // Reset button after 2 seconds
                  setTimeout(() => {
                    liveLocationBtn.innerHTML = "📍";
                    liveLocationBtn.style.color = "#667eea";
                    liveLocationBtn.disabled = false;
                  }, 2000);
                }
              },
              (error) => {
                console.error("Live location access denied:", error);
                // Error state
                liveLocationBtn.innerHTML = "❌";
                liveLocationBtn.style.color = "#ef4444";
                
                // Show error message briefly
                const originalPlaceholder = locationInput.placeholder;
                locationInput.placeholder = "Location access denied";
                
                setTimeout(() => {
                  liveLocationBtn.innerHTML = "📍";
                  liveLocationBtn.style.color = "#667eea";
                  liveLocationBtn.disabled = false;
                  locationInput.placeholder = originalPlaceholder;
                }, 2000);
              },
              {
                timeout: 10000,
                enableHighAccuracy: true,
                maximumAge: 60000 // 1 minute
              }
            );
          } else {
            // No geolocation support
            liveLocationBtn.innerHTML = "❌";
            liveLocationBtn.style.color = "#ef4444";
            
            setTimeout(() => {
              liveLocationBtn.innerHTML = "📍";
              liveLocationBtn.style.color = "#667eea";
              liveLocationBtn.disabled = false;
            }, 2000);
          }
        });
        
        console.log("Live location button setup complete");
      }


    function showLocationPicker() {
      const modal = document.getElementById("locationModal");
      if (modal) {
        modal.style.display = "block";
      }
    }

    function closeLocationModal() {
      const modal = document.getElementById("locationModal");
      if (modal) {
        modal.style.display = "none";
      }
    }

    function setManualLocation() {
      const locationInput = document.getElementById("locationInput");
      if (locationInput) {
        const location = locationInput.value.trim();
        if (location) {
          updateLocationDisplay(location);
          updateLocationInput(location);
          closeLocationModal();
        }
      }
    }

    function setupMap() {
      try {
        const mapElement = document.getElementById("map");
        if (!mapElement) {
          console.error("Map element not found");
          return;
        }

        // Use user location if available, otherwise default coordinates
        const coords = userLocation.lat && userLocation.lng 
          ? [userLocation.lat, userLocation.lng] 
          : [ 78.4867,17.3850]; // Hyderabad coordinates

        const map = L.map("map").setView(coords, 13);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        }).addTo(map);

        // Add marker for current location
        const locationName = userLocation.city && userLocation.state 
          ? `${userLocation.city}, ${userLocation.state}`
          : "Nandyal, Andhra Pradesh";

        L.marker(coords)
          .addTo(map)
          .bindPopup(`📍 ${locationName}`)
          .openPopup();

        console.log("Map initialized successfully");
      } catch (error) {
        console.error("Error setting up map:", error);
      }
    }

    // Start the initialization
    // document.addEventListener("DOMContentLoaded", initializePage);

    // Also try to initialize on window load as fallback
    window.addEventListener("load", () => {
      if (!locationRequested) {
        setupLocationDetection();
      }
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