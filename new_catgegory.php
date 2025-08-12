<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
    </style>
</head>
<body>
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
          <span class="status-indicator available"></span> 
          <span class="category-status">41 Available Now</span> 
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
</body>
</html>