<?php


require_once __DIR__ . '/../src/mysqli_compat.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - LocalConnect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
        }

        /* Main Content Styles */
        .main-content {
            min-height: 100vh;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        
            padding: 12.2rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto 2rem;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            flex-wrap: wrap;
            margin-top: 3rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.8;
        }

        /* About Section */
        .about-section {
            padding: 5rem 0;
            background: white;
        }

        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            margin-bottom: 4rem;
        }

        .about-content h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
        }

        .about-content p {
            font-size: 1.1rem;
            color: #64748b;
            margin-bottom: 1.5rem;
            line-height: 1.8;
        }

        .about-image {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .about-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
        }

        /* Mission Section */
        .mission-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .mission-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            text-align: center;
        }

        .mission-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2rem;
        }

        .mission-content {
            max-width: 800px;
            margin: 0 auto;
            font-size: 1.2rem;
            color: #64748b;
            line-height: 1.8;
            margin-bottom: 3rem;
        }

        .mission-values {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .value-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .value-card:hover {
            transform: translateY(-5px);
        }

        .value-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .value-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .value-description {
            color: #64748b;
            font-size: 1rem;
        }

        /* How It Works Section */
        .how-it-works-section {
            padding: 5rem 0;
            background: white;
        }

        .how-it-works-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
        }

        .step-card {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            margin: 0 auto 2rem;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .step-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .step-description {
            color: #64748b;
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Benefits Section */
        .benefits-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .benefits-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            margin-top: 3rem;
        }

        .benefits-column h3 {
            font-size: 2rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2rem;
            text-align: center;
        }

        .benefit-item {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .benefit-icon {
            font-size: 1.5rem;
            min-width: 40px;
        }

        .benefit-text {
            color: #64748b;
            font-weight: 500;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5rem 0;
            text-align: center;
        }

        .cta-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 3rem;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-button {
            background: white;
            color: #667eea;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .cta-button:hover {
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255,255,255,0.3);
        }

        .cta-button.secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .cta-button.secondary:hover {
            background: white;
            color: #667eea;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .hero-stats {
                gap: 2rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .about-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .about-content h2 {
                font-size: 2rem;
            }

            .mission-title,
            .section-title {
                font-size: 2rem;
            }

            .benefits-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .cta-title {
                font-size: 2rem;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .cta-button {
                width: 100%;
                max-width: 300px;
            }
        }

        /* Loading Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.8s ease forwards;
        }
    </style>
</head>
<body>
    <!-- Header -->
 <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
include_once __DIR__ . '/../templates/new_header.php'; ?>
  <div class="main-content">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-container">
        <h1 class="hero-title">About LocalConnect</h1>
        <p class="hero-subtitle">
          We aim to help customers easily find local services in their area and support small service providers.
        </p>

       
      </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
      <div class="about-container">
        <div class="about-grid">
          <div class="about-content fade-in">
            <h2>What is LocalConnect?</h2>
            <p>
              LocalConnect is a web-based platform built to make it easier for people to find nearby service providers like electricians, tutors, and repair professionals.
            </p>
            <p>
              Our goal is to reduce the time spent searching and help small businesses reach more people in their own community.
            </p>
            <p>
              This project is designed and developed as part of our academic learning to showcase real-world application development.
            </p>
          </div>
          <div class="about-image fade-in">
            <div class="about-icon">🤝</div>
            <h3>Connecting Locally</h3>
          </div>
        </div>
      </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section">
      <div class="mission-container">
        <h2 class="mission-title">Our Mission</h2>
        <p class="mission-content">
          To support easy access to local services and provide a platform where small providers can reach people in their area.
        </p>

        <div class="mission-values">
          <div class="value-card">
            <div class="value-icon">🎯</div>
            <h3 class="value-title">User-Friendly</h3>
            <p class="value-description">
              Simple interface that anyone can use easily.
            </p>
          </div>
          <div class="value-card">
            <div class="value-icon">🔒</div>
            <h3 class="value-title">Transparency</h3>
            <p class="value-description">
              Providers and their services are clearly listed to help users choose confidently.
            </p>
          </div>
          <div class="value-card">
            <div class="value-icon">🌟</div>
            <h3 class="value-title">Local Support</h3>
            <p class="value-description">
              Helping local providers reach more people nearby.
            </p>
          </div>
          <div class="value-card">
            <div class="value-icon">💡</div>
            <h3 class="value-title">Learning Purpose</h3>
            <p class="value-description">
              Built by students to understand how real-world websites are planned and developed.
            </p>
          </div>
        </div>
      </div>
    </section>

    
    <!-- CTA Section -->
    <section class="cta-section">
      <div class="cta-container">
        <h2 class="cta-title">Want to Explore?</h2>
        <p class="cta-subtitle">Start browsing or register to list your service</p>
        <div class="cta-buttons">
          <a href="new_services.php" class="cta-button">Browse Services</a>
          <a href="new_register_prov.php" class="cta-button secondary">Register as Provider</a>
        </div>
      </div>
    </section>
  </div>

    <!-- Footer -->
    <div id="include-footer"></div>

    <script>
        // Include header and footer
        async function includeHTML(elementId, file) {
            try {
                const response = await fetch(file);
                const data = await response.text();
                document.getElementById(elementId).innerHTML = data;
            } catch (error) {
                console.error('Error loading ' + file + ':', error);
            }
        }

        // Load header and footer
        async function loadIncludes() {
            // await includeHTML('include-header', 'header.html');
            await includeHTML('include-footer', 'footer.html');
            
            // Setup header behavior after header is loaded
            setTimeout(() => {
                setupHeaderBehavior();
            }, 200);
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', loadIncludes);

        // Header behavior setup
        function setupHeaderBehavior() {
            const header = document.getElementById("header");
            const mobileMenuBtn = document.getElementById("mobileMenuBtn");
            const mobileMenu = document.getElementById("mobileMenu");
            const locationIndicator = document.querySelector(".location-indicator");

            console.log("Setting up header behavior...");
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
                
                mobileMenuBtn.addEventListener("click", (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log("Mobile menu button clicked");
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

        // Add scroll animations
        function animateOnScroll() {
            const elements = document.querySelectorAll('.fade-in');
            
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('fade-in');
                }
            });
        }

        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            animateOnScroll();
            
            // Add scroll event listener
            window.addEventListener('scroll', animateOnScroll);
            
            // Add smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });

        // Add counter animation for stats
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
                const increment = target / 50;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    
                    // Format the number
                    let formattedNumber = Math.floor(current).toLocaleString();
                    if (counter.textContent.includes('+')) {
                        formattedNumber += '+';
                    }
                    if (counter.textContent.includes('%')) {
                        formattedNumber += '%';
                    }
                    
                    counter.textContent = formattedNumber;
                }, 50);
            });
        }

        // Trigger counter animation when stats section is visible
        const statsSection = document.querySelector('.hero-stats');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        });

        if (statsSection) {
            observer.observe(statsSection);
        }
    </script>
      
          <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
       
</body>
</html>

