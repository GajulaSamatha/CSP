<?php
$conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
if ($conn->connect_error) die("Connection failed");
session_start();


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LocalConnect | Categories</title>
  <link rel="stylesheet" href="styles.css" />
  <!-- Bootstrap CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for additional icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
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
            padding: 8rem 0 4rem;
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
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Categories Section */
        .categories-section {
            padding: 4rem 0;
            background-color: #f8fafc;
        }

        .categories-container {
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
            max-width: 600px;
            margin: 0 auto;
        }

        /* Categories Grid */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .category-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid #f1f5f9;
            text-align: center;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(102, 126, 234, 0.2);
            text-decoration: none;
            color: inherit;
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .category-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            transition: transform 0.3s ease;
        }

        .category-card:hover .category-icon {
            transform: scale(1.1);
        }

        .category-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .category-description {
            color: #64748b;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .category-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1.5rem;
            border-top: 1px solid #f1f5f9;
        }

        .service-count {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.2);
        }

        .category-arrow {
            color: #667eea;
            font-size: 1.5rem;
            transition: transform 0.3s ease;
            font-weight: bold;
        }

        .category-card:hover .category-arrow {
            transform: translateX(8px);
        }

        /* Featured Categories */
        .featured-section {
            background: white;
            padding: 4rem 0;
            margin-top: 2rem;
        }

        .featured-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .featured-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .featured-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 15px;
            padding: 2rem 1.5rem;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .featured-card:hover {
            border-color: #667eea;
            background: white;
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.15);
            text-decoration: none;
            color: inherit;
        }

        .featured-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .featured-card:hover .featured-icon {
            transform: scale(1.1);
        }

        .featured-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .featured-count {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
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
            margin-bottom: 2rem;
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
            padding: 1rem 2rem;
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
            color: #667eea;
            text-decoration: none;
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

        /* Loading Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .category-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .category-card:nth-child(1) { animation-delay: 0.1s; }
        .category-card:nth-child(2) { animation-delay: 0.2s; }
        .category-card:nth-child(3) { animation-delay: 0.3s; }
        .category-card:nth-child(4) { animation-delay: 0.4s; }
        .category-card:nth-child(5) { animation-delay: 0.5s; }
        .category-card:nth-child(6) { animation-delay: 0.6s; }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .categories-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .featured-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .category-card {
                padding: 2rem 1.5rem;
            }

            .category-icon {
                width: 80px;
                height: 80px;
                font-size: 2.2rem;
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

        @media (min-width: 769px) and (max-width: 1024px) {
            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .featured-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1025px) {
            .categories-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .featured-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Accessibility improvements */
        .category-card:focus,
        .featured-card:focus {
            outline: 2px solid #667eea;
            outline-offset: 2px;
        }

        .category-card:focus-visible,
        .featured-card:focus-visible {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.2);
        }
</style>
<body>
  <!-- Header Include -->
  <!-- <div id="include-header"></div> -->
 <?php require "new_header.php"; ?>
  <!-- Page Content -->
  <div class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-container">
                <h1 class="hero-title">Service Categories</h1>
                <p class="hero-subtitle">Explore our wide range of professional services organized by category. Find exactly what you need for your home, business, and personal care.</p>
            </div>
        </section>

        <!-- Main Categories Section -->
        <section class="categories-section">
            <div class="categories-container">
              <div class="section-header">
                <h2 class="section-title">Browse All Categories</h2>
                <p class="section-subtitle">Choose from our comprehensive list of service categories to find the perfect professional for your needs</p>
              </div>
          
              <div class="categories-grid">
                <a href="services.php?category=cleaning" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">🧹</div>
                  <h3 class="category-title">Cleaning</h3>
                  <p class="category-description">Home & Office Cleaning Services</p>
                  <div class="category-stats">
                    <span class="service-count">3 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
          
                <a href="services.php?category=hospital" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">🩺</div>
                  <h3 class="category-title">Hospital</h3>
                  <p class="category-description">Doctors, Clinics, Nurses</p>
                  <div class="category-stats">
                    <span class="service-count">4 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
          
                <a href="services.php?category=home-repair" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">🔧</div>
                  <h3 class="category-title">Home Repair</h3>
                  <p class="category-description">Carpenters, Masons, Painters</p>
                  <div class="category-stats">
                    <span class="service-count">2 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
          
                <a href="services.php?category=plumbing" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">🚰</div>
                  <h3 class="category-title">Plumbing</h3>
                  <p class="category-description">Leak Fixing, Tap Installations</p>
                  <div class="category-stats">
                    <span class="service-count">2 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
          
                <a href="services.php?category=electrical" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">💡</div>
                  <h3 class="category-title">Electrical</h3>
                  <p class="category-description">Wiring, Fans, Switch Repairs</p>
                  <div class="category-stats">
                    <span class="service-count">2 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
          
                <a href="services.php?category=mechanics" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">🛠</div>
                  <h3 class="category-title">Mechanics</h3>
                  <p class="category-description">Bike & Car Repairs</p>
                  <div class="category-stats">
                    <span class="service-count">2 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
          
                <a href="services.php?category=restaurant" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">🍴</div>
                  <h3 class="category-title">Restaurant</h3>
                  <p class="category-description">Dine-in & Delivery Services</p>
                  <div class="category-stats">
                    <span class="service-count">4 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
          
                <a href="services.php?category=hotel" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">🏨</div>
                  <h3 class="category-title">Hotel</h3>
                  <p class="category-description">Local Stays & Lodges</p>
                  <div class="category-stats">
                    <span class="service-count">2 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
          
                <a href="services.php?category=pet-care" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">🐾</div>
                  <h3 class="category-title">Pet Care</h3>
                  <p class="category-description">Vets, Grooming, Walkers</p>
                  <div class="category-stats">
                    <span class="service-count">2 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
          
                <a href="services.php?category=gardening" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">🌿</div>
                  <h3 class="category-title">Gardening</h3>
                  <p class="category-description">Garden Setup & Maintenance</p>
                  <div class="category-stats">
                    <span class="service-count">2 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
          
                <a href="services.php?category=beauty" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">🧖</div>
                  <h3 class="category-title">Personal Services</h3>
                  <p class="category-description">Beauticians, Spa, Massage</p>
                  <div class="category-stats">
                    <span class="service-count">2 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
          
                <a href="services.php?category=tutoring" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">📘</div>
                  <h3 class="category-title">Tutoring</h3>
                  <p class="category-description">Home & Online Tutors</p>
                  <div class="category-stats">
                    <span class="service-count">2 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
          
                <a href="services.php?category=beauty" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">💅</div>
                  <h3 class="category-title">Beauty & Wellness</h3>
                  <p class="category-description">Salons, Makeup Artists</p>
                  <div class="category-stats">
                    <span class="service-count">6 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
          
                <a href="services.php?category=company" class="category-card" role="button" tabindex="0">
                  <div class="category-icon">🏢</div>
                  <h3 class="category-title">Company</h3>
                  <p class="category-description">Business Services, Agencies</p>
                  <div class="category-stats">
                    <span class="service-count">3 Services Available</span>
                    <span class="category-arrow">→</span>
                  </div>
                </a>
              </div>
            </div>
          </section>
    </div>

  <!-- Footer Include -->
  <div id="include-footer"></div>

  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

  <!-- Include Script to Load Header and Footer -->
  <script>
    async function includeHTML(id, file) {
      try {
        const res = await fetch(file);
        const html = await res.text();
        document.getElementById(id).innerHTML = html;
      } catch (error) {
        console.error(`Error loading ${file}:`, error);
      }
    }

    document.addEventListener("DOMContentLoaded", async () => {
      // await includeHTML("include-header", "header.html");
      await includeHTML("include-footer", "footer.html");
      
      // Wait a bit for DOM to be ready, then setup header behavior
      setTimeout(() => {
        setupHeaderBehavior();
      }, 200);
      
      // Initialize page interactions after DOM is loaded
      initializePageInteractions();
    });

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

    // Enhanced page interactions
    function initializePageInteractions() {
        // Add click animations for all interactive elements
        const interactiveElements = document.querySelectorAll('.category-card, .featured-card, .cta-button');
        
        interactiveElements.forEach(element => {
            // Mouse down effect
            element.addEventListener('mousedown', function(e) {
                if (!e.target.closest('a')) return;
                this.style.transform = 'scale(0.96)';
            });
            
            // Mouse up/leave effect
            element.addEventListener('mouseup', function() {
                this.style.transform = '';
            });
            
            element.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });

            // Add ripple effect on click
            element.addEventListener('click', function(e) {
                createRippleEffect(e, this);
            });
        });

        // Keyboard navigation support
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                const focusedElement = document.activeElement;
                if (focusedElement.classList.contains('category-card') || 
                    focusedElement.classList.contains('featured-card')) {
                    e.preventDefault();
                    
                    // Get the href from the focused element
                    const href = focusedElement.getAttribute('href');
                    if (href) {
                        window.location.href = href;
                    }
                }
            }
        });

        // Add loading states
        const categoryLinks = document.querySelectorAll('a[href*="services.php"]');
        categoryLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Add loading state
                this.style.opacity = '0.7';
                this.style.pointerEvents = 'none';
                
                // Add loading text (optional)
                const originalText = this.querySelector('.service-count') || this.querySelector('.featured-count');
                if (originalText) {
                    const originalContent = originalText.textContent;
                    originalText.textContent = 'Loading...';
                    
                    // Reset after a short delay if navigation fails
                    setTimeout(() => {
                        originalText.textContent = originalContent;
                        this.style.opacity = '1';
                        this.style.pointerEvents = 'auto';
                    }, 3000);
                }
            });
        });
    }

    // Create ripple effect for better user feedback
    function createRippleEffect(e, element) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(102, 126, 234, 0.3);
            transform: scale(0);
            animation: ripple 0.6s linear;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            pointer-events: none;
            z-index: 1000;
        `;
        
        // Add ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
        
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);
        
        // Remove ripple after animation
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    // Smooth scroll enhancement
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Observe all cards for scroll animations
        const cards = document.querySelectorAll('.category-card, .featured-card');
        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    });

    // Service count animation on page load
    function animateServiceCounts() {
        const counters = document.querySelectorAll('.service-count, .featured-count');
        
        counters.forEach(counter => {
            const text = counter.textContent;
            const numbers = text.match(/\d+/);
            if (numbers) {
                const finalNumber = parseInt(numbers[0]);
                let currentNumber = 0;
                const increment = finalNumber / 30; // Animation duration
                
                const timer = setInterval(() => {
                    currentNumber += increment;
                    if (currentNumber >= finalNumber) {
                        currentNumber = finalNumber;
                        clearInterval(timer);
                    }
                    
                    counter.textContent = text.replace(/\d+/, Math.floor(currentNumber));
                }, 50);
            }
        });
    }

    // Run count animation after a short delay
    setTimeout(animateServiceCounts, 1000);

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