

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LocalConnect</title>
</head>
<style>
  .header {
    background: linear-gradient(135deg,
        rgba(30, 64, 175, 0.95) 0%,
        rgba(59, 130, 246, 0.95) 50%,
        rgba(37, 99, 235, 0.95) 100%);
    backdrop-filter: blur(20px);
    border-bottom: none;
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: all 0.3s ease;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
  }

  .header.scrolled {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(30px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    border-bottom: none;
  }

  .header.scrolled .nav-container {
    padding: 0.75rem 1.5rem;
  }

  .header.scrolled .logo {
    color: #1e293b;
  }

  .header.scrolled .nav-links a {
    color: #475569;
  }

  .header.scrolled .auth-buttons .login-btn {
    color: #475569;
  }

  .nav-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s ease;
    gap: 1rem;
  }

  .logo-section {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
  }

  .logo-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .logo-icon::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transform: rotate(45deg);
    transition: all 0.6s ease;
    opacity: 0;
  }

  .logo-icon:hover::before {
    opacity: 1;
    animation: shine 0.6s ease-in-out;
  }

  .logo-icon:hover {
    transform: scale(1.1) rotate(10deg);
    box-shadow: 0 10px 30px rgba(59, 130, 246, 0.6);
  }

  @keyframes shine {
    to {
      transform: rotate(45deg) translate(100%, 100%);
    }
  }

  .logo-icon svg {
    width: 20px;
    height: 20px;
    color: white;
    z-index: 2;
  }

  .logo {
    font-size: 1.25rem;
    font-weight: 700;
    color: white;
    text-decoration: none;
    transition: color 0.3s ease;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    white-space: nowrap;
  }

  .nav-links {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex: 1;
    justify-content: center;
  }

  .nav-links a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-weight: 500;
    position: relative;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    white-space: nowrap;
    font-size: 0.9rem;
  }

  .nav-links a:hover {
    color: white;
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
  }

  .nav-links a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #60a5fa, #3b82f6, #2563eb);
    transition: all 0.3s ease;
    transform: translateX(-50%);
  }

  .nav-links a:hover::after {
    width: 80%;
  }

  .auth-section {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
  }

  .location-indicator {
    display: flex;
    align-items: center;
    padding: 0.4rem 0.75rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    white-space: nowrap;
    min-width: 0;
  }

  .location-indicator:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
  }

  .location-indicator svg {
    width: 14px;
    height: 14px;
    color: rgba(255, 255, 255, 0.9);
    flex-shrink: 0;
  }

  .location-text {
    margin-left: 6px;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.8rem;
    font-weight: 500;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 120px;
  }

  .auth-buttons {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .login-btn {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-weight: 500;
    padding: 0.6rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
  }

  .login-btn:hover {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
  }

  .signup-btn {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    text-decoration: none;
    font-weight: 600;
    padding: 0.6rem 1rem;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    white-space: nowrap;
    font-size: 0.85rem;
    min-width: fit-content;
  }

  .signup-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
  }

  .signup-btn:hover::before {
    left: 100%;
  }

  .signup-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(59, 130, 246, 0.6);
    background: linear-gradient(135deg, #2563eb, #1e40af);
  }

  .mobile-menu-btn {
    display: none;
    flex-direction: column;
    gap: 4px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
  }

  .mobile-menu-btn span {
    width: 24px;
    height: 2px;
    background: white;
    border-radius: 2px;
    transition: all 0.3s ease;
  }

  .mobile-menu-btn.active span:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
  }

  .mobile-menu-btn.active span:nth-child(2) {
    opacity: 0;
  }

  .mobile-menu-btn.active span:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -6px);
  }

  .mobile-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: linear-gradient(135deg,
        rgba(255, 255, 255, 0.98) 0%,
        rgba(248, 250, 252, 0.98) 100%);
    backdrop-filter: blur(30px);
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    padding: 2rem;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
  }

  .mobile-menu.active {
    display: block;
    animation: slideDown 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .mobile-menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  }

  .mobile-menu-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
  }

  .mobile-menu-close {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .mobile-menu-close:hover {
    background: rgba(0, 0, 0, 0.05);
    transform: scale(1.1);
  }

  .mobile-menu-close svg {
    width: 20px;
    height: 20px;
    color: #64748b;
  }

  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-30px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .mobile-nav-links {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-bottom: 2rem;
  }

  .mobile-nav-links a {
    color: #1e293b;
    text-decoration: none;
    font-weight: 500;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(0, 0, 0, 0.05);
  }

  .mobile-nav-links a:hover {
    color: #3b82f6;
    background: rgba(59, 130, 246, 0.1);
    transform: translateX(8px);
  }

  .mobile-auth-buttons {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .mobile-auth-buttons .login-btn {
    color: #475569;
    text-align: center;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.8);
  }

  .mobile-auth-buttons .signup-btn {
    text-align: center;
  }

  /* Scrolled header adjustments */
  .header.scrolled .location-indicator {
    background: rgba(0, 0, 0, 0.05);
    border-color: rgba(0, 0, 0, 0.1);
  }

  .header.scrolled .location-indicator:hover {
    background: rgba(0, 0, 0, 0.1);
    border-color: rgba(0, 0, 0, 0.2);
  }

  .header.scrolled .location-indicator svg,
  .header.scrolled .location-text {
    color: #475569;
  }

  /* Modal styling */
  #locationModal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 9999;
    animation: fadeIn 0.3s ease-in-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }

  #locationModal .modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 2rem;
    border-radius: 16px;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
    animation: scaleIn 0.3s ease;
  }

  @keyframes scaleIn {
    from {
      transform: scale(0.8) translate(-50%, -50%);
      opacity: 0;
    }

    to {
      transform: scale(1) translate(-50%, -50%);
      opacity: 1;
    }
  }

  #locationInput {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 1rem;
    font-size: 1rem;
    box-sizing: border-box;
  }

  .modal-buttons {
    display: flex;
    gap: 1rem;
  }

  .modal-buttons button {
    flex: 1;
    padding: 0.75rem;
    font-size: 1rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .modal-buttons .set-btn {
    background: #4f46e5;
    color: white;
  }

  .modal-buttons .set-btn:hover {
    background: #4338ca;
  }

  .modal-buttons .cancel-btn {
    background: #e2e8f0;
    color: #475569;
  }

  .modal-buttons .cancel-btn:hover {
    background: #cbd5e1;
  }

  /* Responsive design */
  @media (max-width: 1024px) {
    .nav-container {
      padding: 1rem;
      gap: 0.75rem;
    }
    
    .nav-links {
      gap: 1rem;
    }
    
    .nav-links a {
      font-size: 0.85rem;
      padding: 0.4rem 0.6rem;
    }
    
    .location-text {
      max-width: 90px;
      font-size: 0.75rem;
    }
    
    .signup-btn {
      font-size: 0.8rem;
      padding: 0.5rem 0.8rem;
    }
  }

  @media (max-width: 900px) {
    .nav-links {
      display: none;
    }
    
    .auth-section {
      gap: 0.5rem;
    }
  }

  @media (max-width: 768px) {
    .nav-container {
      padding: 1rem 0.75rem;
    }

    .nav-links,
    .auth-buttons {
      display: none;
    }

    .mobile-menu-btn {
      display: flex;
    }

    .header.scrolled .mobile-menu-btn span {
      background: #1e293b;
    }

    .location-indicator {
      padding: 0.35rem 0.6rem;
    }

    .location-text {
      font-size: 0.7rem;
      max-width: 80px;
    }

    .location-indicator svg {
      width: 12px;
      height: 12px;
    }

    .logo {
      font-size: 1.1rem;
    }

    .logo-icon {
      width: 32px;
      height: 32px;
    }

    .logo-icon svg {
      width: 18px;
      height: 18px;
    }

    /* Mobile menu improvements */
    .mobile-menu {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 1001;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(20px);
      display: flex;
      flex-direction: column;
      padding: 1rem;
    }

    .mobile-menu.active {
      display: flex;
    }

    .mobile-menu-header {
      margin-bottom: 2rem;
      padding: 1rem 0;
    }

    .mobile-nav-links {
      flex: 1;
      gap: 1rem;
    }

    .mobile-nav-links a {
      padding: 1rem;
      font-size: 1.1rem;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.8);
      border: 1px solid rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }

    .mobile-nav-links a:hover {
      background: rgba(59, 130, 246, 0.1);
      transform: translateX(8px);
      color: #3b82f6;
    }

    .mobile-auth-buttons {
      margin-top: auto;
      padding-top: 2rem;
      border-top: 1px solid rgba(0, 0, 0, 0.1);
    }
  }

  @media (max-width: 480px) {
    .nav-container {
      padding: 0.75rem 0.5rem;
    }
    
    .logo-section {
      gap: 0.4rem;
    }
    
    .logo {
      font-size: 1rem;
    }
    
    .location-text {
      max-width: 60px;
    }
  }

  .language-selector .goog-te-gadget-simple {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 1px 6px !important;
    font-size: 0.88rem !important;
    color: #1e293b !important;
    min-height: 24px !important;
    min-width: 40px !important;
    box-shadow: 0 1px 4px rgba(59,130,246,0.07);
    cursor: pointer;
    display: flex !important;
    align-items: center;
    height: 26px !important;
    line-height: 1.1 !important;
    margin-left: 0.5rem;
  }
  .language-selector .goog-te-gadget-simple:hover {
    box-shadow: 0 2px 8px rgba(59,130,246,0.13);
  }
  .language-selector .goog-te-menu-value span {
    color: #3b82f6 !important;
    font-weight: 500;
  }
  .language-selector .goog-te-menu-value {
    padding-right: 8px !important;
  }
  .language-selector .goog-te-gadget-icon {
    display: none !important;
  }
  .language-selector select.goog-te-combo {
    font-size: 0.88rem !important;
    padding: 1px 4px !important;
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    background: #f8fafc !important;
    min-height: 20px !important;
    min-width: 35px !important;
    width: 50px !important;
  }

  .goog-logo-link, .goog-te-gadget span {
    display: none !important;
  }
</style>

<body>
  <header class="header" id="header">
    <div class="nav-container">
      <div class="logo-section">
        <div class="logo-icon">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
          </svg>
        </div>
        <a href="#" class="logo">LocalConnect</a>
      </div>

      <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="services.php">Services</a>
        <a href="new_categories.php">Categories</a>
        <a href="about.php">About</a>
        <a href="contact.html">Contact</a>
        <a href="add_service.html">Add Service</a>
      </nav>
 <!-- Language Selector (Google Translate) after Contact link -->
 <div class="language-selector" style="display:inline-flex; align-items:center; vertical-align:middle; margin-left:0.5rem; gap:0.3rem; min-width:0;">
  <div id="google_translate_element"></div>
</div>
      <div class="auth-section">
        <div class="location-indicator">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
          </svg>
          <span class="location-text">Detecting...</span>
        </div>

        <div class="auth-buttons">
          <a href="login_register.html" class="signup-btn">Get Started</a>
          <a href="account.php" class="login-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" width="20" height="20">
              <path
                d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2V19.2c0-3.2-6.4-4.8-9.6-4.8z" />
            </svg>
          </a>
        </div>
      </div>

      <button class="mobile-menu-btn" id="mobileMenuBtn">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>

    <div class="mobile-menu" id="mobileMenu">
      <div class="mobile-menu-header">
        <div class="mobile-menu-title">Menu</div>
        <button class="mobile-menu-close" id="mobileMenuClose">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
          </svg>
        </button>
      </div>
      <div class="mobile-nav-links">
        <a href="index.html">Home</a>
        <a href="services.php">Services</a>
        <a href="categories.php">Categories</a>
        <a href="about.html">About</a>
        <a href="contact.html">Contact</a>
        <a href="add_service.html">Add Service</a>
      </div>
           
      <?php
        if(isset($_SESSION['user_name'])){
?>
<h1><?php echo $_SESSION['user_name'] ?></h1>
<?php
        }else{
          ?>

        
      <div class="mobile-auth-buttons">
        <a href="new_register_cust.php" class="signup-btn">Get Started</a>
        <a href="account.php" class="login-btn">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
          </svg>
        </a>
      </div>
      <?php
      }
      ?>
    </div>

    <!-- Location Modal -->
    <div id="locationModal" onclick="outsideClickClose(event)">
      <div class="modal-content" onclick="event.stopPropagation()">
        <h3 style="margin-bottom: 1rem; color: #1e293b">Select Your Location</h3>
        <input type="text" id="locationInput" placeholder="Enter city name..." />
        <div class="modal-buttons">
          <button class="set-btn" onclick="setManualLocation()">Set Location</button>
          <button class="cancel-btn" onclick="closeLocationModal()">Cancel</button>
        </div>
      </div>
    </div>

   
  </header>

  <script>
    // Initialize when page loads
    window.addEventListener("load", () => {
      // Try to get user's location automatically
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          async (position) => {
            const { latitude, longitude } = position.coords;
            await updateLocationFromCoords(latitude, longitude);
          },
          (error) => {
            console.log("Location access denied or failed:", error.message);
            // Show manual location picker if geolocation fails
            showLocationPicker();
          },
          {
            timeout: 10000,
            enableHighAccuracy: true,
            maximumAge: 300000 // 5 minutes
          }
        );
      } else {
        console.log("Geolocation not supported by this browser");
        showLocationPicker();
      }
    });

    // Header scroll effect and mobile menu
    document.addEventListener("DOMContentLoaded", () => {
      const header = document.getElementById("header");
      const mobileMenuBtn = document.getElementById("mobileMenuBtn");
      const mobileMenu = document.getElementById("mobileMenu");
      const locationIndicator = document.querySelector(".location-indicator");

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
        mobileMenuBtn.addEventListener("click", () => {
          mobileMenuBtn.classList.toggle("active");
          mobileMenu.classList.toggle("active");
        });

        // Close mobile menu when clicking on links
        document.querySelectorAll(".mobile-nav-links a, .mobile-auth-buttons a").forEach((link) => {
          link.addEventListener("click", () => {
            mobileMenuBtn.classList.remove("active");
            mobileMenu.classList.remove("active");
          });
        });

        // Close mobile menu with close button
        const mobileMenuClose = document.getElementById("mobileMenuClose");
        if (mobileMenuClose) {
          mobileMenuClose.addEventListener("click", () => {
            mobileMenuBtn.classList.remove("active");
            mobileMenu.classList.remove("active");
          });
        }

        // Close mobile menu when clicking outside
        document.addEventListener("click", (event) => {
          if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
            mobileMenuBtn.classList.remove("active");
            mobileMenu.classList.remove("active");
          }
        });
      }

      // Location indicator click handler
      if (locationIndicator) {
        locationIndicator.addEventListener("click", () => {
          showLocationPicker();
        });
      }
    });

    // Location functions
    async function updateLocationFromCoords(lat, lng) {
      try {
        const response = await fetch(
          `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=en`
        );

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        const city = data.city || data.locality || data.principalSubdivision;
        const state = data.principalSubdivision || data.region;
        const locationElement = document.querySelector(".location-text");

        if (locationElement) {
          if (city && state) {
            locationElement.textContent = `${city}, ${state}`;
          } else if (city) {
            locationElement.textContent = city;
          } else {
            locationElement.textContent = "Location detected";
          }
        }
      } catch (error) {
        console.error("Location fetch failed:", error);
        const locationElement = document.querySelector(".location-text");
        if (locationElement) {
          locationElement.textContent = "Click to set location";
        }
        // Show location picker as fallback
        setTimeout(() => {
          showLocationPicker();
        }, 1000);
      }
    }

    function showLocationPicker() {
      const modal = document.getElementById("locationModal");
      const locationInput = document.getElementById("locationInput");

      if (modal) {
        modal.style.display = "block";
        // Focus on input field when modal opens
        setTimeout(() => {
          if (locationInput) {
            locationInput.focus();
          }
        }, 100);
      }
    }

    function closeLocationModal() {
      const modal = document.getElementById("locationModal");
      const locationInput = document.getElementById("locationInput");

      if (modal) {
        modal.style.display = "none";
      }

      // Clear input field
      if (locationInput) {
        locationInput.value = "";
      }
    }

    function setManualLocation() {
      const locationInput = document.getElementById("locationInput");
      const locationElement = document.querySelector(".location-text");

      if (locationInput && locationElement) {
        const location = locationInput.value.trim();

        if (location) {
          locationElement.textContent = location;
          closeLocationModal();
        } else {
          // Show error if empty
          locationInput.style.borderColor = "#ef4444";
          locationInput.placeholder = "Please enter a location";
          setTimeout(() => {
            locationInput.style.borderColor = "#e2e8f0";
            locationInput.placeholder = "Enter city name...";
          }, 2000);
        }
      }
    }

    // Close modal when clicking outside
    function outsideClickClose(event) {
      const modal = document.getElementById("locationModal");

      if (event.target === modal) {
        closeLocationModal();
      }
    }

    // Handle Enter key in location input
    document.addEventListener("DOMContentLoaded", () => {
      const locationInput = document.getElementById("locationInput");

      if (locationInput) {
        locationInput.addEventListener("keypress", (event) => {
          if (event.key === "Enter") {
            setManualLocation();
          }
        });

        // Clear error styling when user starts typing
        locationInput.addEventListener("input", () => {
          locationInput.style.borderColor = "#e2e8f0";
        });
      }
    });

    // Close modal with Escape key
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        const modal = document.getElementById("locationModal");
        if (modal && modal.style.display === "block") {
          closeLocationModal();
        }
      }
    });
  </script>

 </body>

</html>