<?php // session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }
        body {
        font-family: 'Segoe UI', sans-serif;
        }

        header {
        background-color: #1e293b; /* dark blue-gray */
        color: white;
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        }

        .logo {
        font-size: 1.8rem;
        font-weight: bold;
        color: #38bdf8; /* light blue */
        text-decoration: none;
        }

        nav {
        display: flex;
        gap: 1.2rem;
        flex-wrap: wrap;
        }

        nav a {
        color: white;
        text-decoration: none;
        font-size: 1rem;
        transition: color 0.3s ease;
        }

        nav a:hover {
        color: #38bdf8;
        }

        @media (max-width: 600px) {
        header {
            flex-direction: column;
            align-items: flex-start;
        }

        nav {
            margin-top: 0.5rem;
            flex-direction: column;
            gap: 0.6rem;
        }
        }
    </style>
</head>
<body>
    <div id="include-header">
    
  <header>
    <a href="index.php" class="logo">LocalConnect</a>
    <nav>
      <!-- <a href="index.php">Home</a> -->
      <a href="about.php">About</a>
      <a href="new_services.php">Services</a>
      <a href="new_categories.php">Categories</a>
      <a href="new_add_services.php">Add Service</a>
      <?php
      if(isset($_SESSION['user_name'])){
        ?>
        <h1><?php echo $_SESSION['user_name'] ?></h1>
        <a href="logout.php">Logout</a>
        <?php
      }else{
        ?>
      <a href="new_register_cust.php">Login</a>
      <?php
      }
      ?>
    </nav>
  </header>



  </div>
</body>
</html>
