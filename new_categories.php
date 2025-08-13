<?php
$conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
if ($conn->connect_error) die("Connection failed");
session_start();
//c.icon add it in sql statement
$sql = "SELECT DISTINCT c.name, c.category_count FROM categories c";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <style>
        .category-section {
            padding: 3rem;
            background: #f8fafc;
            text-align: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .category-section h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #111827;
        }

        .category-section p {
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }

        .category-card {
            background: white;
            padding: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-icon img {
            width: 64px;
            height: 64px;
            margin-bottom: 1rem;
        }

        .category-card h3 {
            font-size: 1.25rem;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .category-card p {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .service-count {
            background-color: #e0e7ff;
            color: #4338ca;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
        }

    </style>
</head>
<body>
    <?php include 'new_header.php'; ?>
    <div class="category-section">
    <h2>Browse All Categories</h2>
    <p>Choose from our comprehensive list of service categories to find the perfect professional.</p>
    <div class="categories-grid">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="category-card">
                <div class="category-icon">
                    <!-- <img src="assets/icons/<?php //echo $row['icon']; ?>" alt="<?php //echo $row['name']; ?>"> -->
                </div>
                <h3><?php echo $row['name']; ?></h3>
                <!-- <p><?php //echo $row['description']; ?></p> -->
                <span class="service-count"><?php echo $row['category_count']; ?> services</span>
            </div>
        <?php } ?>
    </div>
</div>
</body>
</html>
