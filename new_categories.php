<?php
$conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
if ($conn->connect_error) die("Connection failed");
session_start();

// Get categories with standardized names
$sql = "SELECT 
            CASE 
                WHEN LOWER(name) LIKE '%shopping mall%' THEN 'Shopping Mall'
                WHEN LOWER(name) LIKE '%shopping%' THEN 'Shopping'
                WHEN LOWER(name) LIKE '%electrician%' THEN 'Electrician'
                WHEN LOWER(name) LIKE '%plumb%' THEN 'Plumbing'
                WHEN LOWER(name) LIKE '%clean%' THEN 'Cleaning'
                WHEN LOWER(name) LIKE '%carpent%' THEN 'Carpentry'
                WHEN LOWER(name) LIKE '%paint%' THEN 'Painting'
                WHEN LOWER(name) LIKE '%ac repair%' THEN 'AC Services'
                WHEN LOWER(name) LIKE '%car wash%' THEN 'Automotive'
                WHEN LOWER(name) LIKE '%pest control%' THEN 'Pest Control'
                WHEN LOWER(name) LIKE '%beauty%' THEN 'Beauty Parlour'
                WHEN LOWER(name) LIKE '%health%' THEN 'Health'
                WHEN LOWER(name) LIKE '%education%' THEN 'Education'
                WHEN LOWER(name) LIKE '%event%' THEN 'Events'
                WHEN LOWER(name) LIKE '%transport%' THEN 'Transport'
                WHEN LOWER(name) LIKE '%food%' THEN 'Food'
                ELSE name 
            END AS category_name,
            COUNT(*) AS category_count,
            SUM(category_count) AS total_services
        FROM categories
        GROUP BY category_name
        ORDER BY category_name";
$result = mysqli_query($conn, $sql);

// Updated Category logo mapping (using Font Awesome icons)
$categoryLogos = [
    'Shopping' => 'fa-shopping-bag',
    'Beauty' => 'fa-spa',
    'Computer store' => 'fa-laptop',
    'electronics' => 'fa-plug',
    'fruit shop' => 'fa-apple-alt',
    'hospital' => 'fa-hospital',
    'hotel' => 'fa-hotel',
    'mechanics' => 'fa-tools',
    'pet' => 'fa-paw',
    'Restaurant' => 'fa-utensils',
    'Electrician' => 'fa-bolt',
    'Plumbing' => 'fa-faucet',
    'Cleaning' => 'fa-broom',
    'Carpentry' => 'fa-hammer',
    'Painting' => 'fa-paint-roller',
    'AC Services' => 'fa-snowflake',
    'Automotive' => 'fa-car',
    'Pest Control' => 'fa-bug-spray',
    'Health' => 'fa-heartbeat',
    'Education' => 'fa-graduation-cap',
    'Events' => 'fa-calendar-check',
    'Transport' => 'fa-bus-alt',
    'Food' => 'fa-utensils',
    'default' => 'fa-store' // Updated default icon
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Categories | Nandyal Dial</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e0e7ff;
            --secondary: #3f37c9;
            --dark: #1e1e24;
            --light: #f8f9fa;
            --gray: #6c757d;
            --white: #ffffff;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        .category-section {
            padding: 4rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }

        .section-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }

        .section-header p {
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
            font-size: 1.1rem;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .category-card {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            z-index: 1;
            cursor: pointer;
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            opacity: 0;
            transition: var(--transition);
            z-index: -1;
        }

        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }

        .category-card:hover::before {
            opacity: 1;
        }

        .category-card:hover .category-content h3,
        .category-card:hover .category-content p,
        .category-card:hover .service-count {
            color: var(--white);
        }

        .category-card:hover .service-count {
            background: rgba(255, 255, 255, 0.2);
        }

        .category-icon {
            padding: 2rem 2rem 1rem;
            text-align: center;
        }

        .category-icon i {
            font-size: 3rem;
            color: var(--primary);
            transition: var(--transition);
        }

        .category-card:hover .category-icon i {
            color: var(--white);
            transform: scale(1.1);
        }

        .category-content {
            padding: 0 2rem 2rem;
            text-align: center;
        }

        .category-content h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
            transition: var(--transition);
        }

        .category-content p {
            color: var(--gray);
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .service-count {
            display: inline-block;
            background-color: var(--primary-light);
            color: var(--primary);
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .search-container {
            max-width: 600px;
            margin: 0 auto 3rem;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            border: none;
            box-shadow: var(--shadow);
            font-size: 1rem;
            padding-left: 3rem;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        @media (max-width: 768px) {
            .category-section {
                padding: 3rem 1.5rem;
            }
            
            .section-header h2 {
                font-size: 2rem;
            }
            
            .categories-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .category-section {
                padding: 2rem 1rem;
            }
            
            .section-header h2 {
                font-size: 1.8rem;
            }
            
            .section-header p {
                font-size: 1rem;
            }
            
            .categories-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'new_header.php'; ?>
    
    <div class="category-section">
        <div class="section-header">
            <h2>Explore Service Categories</h2>
            <p>Discover the perfect service for your needs from our comprehensive categories</p>
        </div>
        
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search categories..." id="categorySearch">
        </div>
        
        <div class="categories-grid">
            <?php while ($row = mysqli_fetch_assoc($result)) { 
                $categoryName = $row['category_name'];
                $icon = $categoryLogos[$categoryName] ?? $categoryLogos['default'];
                ?>
                <div class="category-card" onclick="window.location='new_services.php?category=<?= urlencode($categoryName) ?>'">
                    <div class="category-icon">
                        <i class="fas <?= $icon ?>"></i>
                    </div>
                    <div class="category-content">
                        <h3><?= htmlspecialchars($categoryName) ?></h3>
                        <p>Professional services for all your needs</p>
                        <span class="service-count"><?= $row['total_services'] ?> Services Available</span>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    
    <?php include "footer.html"; ?>
    
    <script>
        // Search functionality
        document.getElementById('categorySearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.category-card');
            
            cards.forEach(card => {
                const categoryName = card.querySelector('h3').textContent.toLowerCase();
                card.style.display = categoryName.includes(searchTerm) ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>