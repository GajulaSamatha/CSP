<?php

require_once __DIR__ . '/../src/mysqli_compat.php';
// Database connection
$host = "localhost";
$dbname = "nandyal_dial";
$username = "root";
$password = "1234";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

if(isset($_POST['import'])){
// Check if file uploaded
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    die("Error: Please upload a valid CSV file.");
}

// Open CSV file
$file = fopen($_FILES['csv_file']['tmp_name'], 'r');
if (!$file) {
    die("Error: Unable to open uploaded CSV file.");
}

// Skip header row
$headers = fgetcsv($file);

// Prepare SQL insert
$sql = "INSERT INTO providers 
(first_name, last_name, email, password, business_name, category, description, phone_number, whatsapp_number, image_names, mon_fri_start, mon_fri_end, sat_start, sat_end, sun_start, sun_end, lat, lon,location) 
VALUES 
(:first_name, :last_name, :email, :password, :business_name, :category, :description, :phone_number, :whatsapp_number, :image_names, :mon_fri_start, :mon_fri_end, :sat_start, :sat_end, :sun_start, :sun_end, :lat, :lon,:location)";
$sql1="INSERT INTO admin_grant (id,first_name, last_name, email,business_name, category, description, phone_number,whatsapp_number, image_names,lat, lon,location)
VALUES
(:id,:first_name, :last_name, :email, :business_name, :category, :description, :phone_number, :whatsapp_number, :image_names,:lat, :lon,:location)";
$stmt = $pdo->prepare($sql);
$stmt2=$pdo->prepare($sql1);

while (($row = fgetcsv($file)) !== false) {
    list(
        $first_name,
        $last_name,
        $email,
        $password,
        $phone_number,
        $whatsapp_number,
        $business_name,
        $description,
        $category,
        $mon_fri_start,
        $mon_fri_end,
        $sat_start,
        $sat_end,
        $sun_start,
        $sun_end,
        $lat,
        $lon,
        $location,
        $image_names
    ) = $row;

    // Read image names from CSV JSON
    $imageArray = json_decode($image_names, true);
    if (!is_array($imageArray)) {
        $imageArray = [];
    }

    // Prefix firstname+lastname to each image name
    foreach ($imageArray as $key => $imgName) {
        $imgName = trim($imgName);
        $extension = pathinfo($imgName, PATHINFO_EXTENSION);
        $namePart = pathinfo($imgName, PATHINFO_FILENAME);
        $imageArray[$key] = "{$first_name}{$last_name}_{$namePart}.{$extension}";
    }

    // Convert back to JSON
    $imageNamesJson = json_encode($imageArray);

    // Execute insert
    $stmt->execute([
        ':first_name'    => $first_name,
        ':last_name'     => $last_name,
        ':email'         => $email,
        ':password'      => $password,
        ':business_name' => $business_name,
        ':category'      => $category,
        ':description'   => $description,
        ':phone_number'  => $phone_number,
        ':whatsapp_number'=> $whatsapp_number,
        ':image_names'   => $imageNamesJson,
        ':mon_fri_start' => $mon_fri_start,
        ':mon_fri_end'   => $mon_fri_end,
        ':sat_start'     => $sat_start,
        ':sat_end'       => $sat_end,
        ':sun_start'     => $sun_start,
        ':sun_end'       => $sun_end,
        ':lat'           => $lat,
        ':lon'           => $lon,
        ':location'      =>$location
    ]);
     $providerId = $pdo->lastInsertId();
    $stmt2->execute([
        ':id'            => $providerId,
        ':first_name'    => $first_name,
        ':last_name'     => $last_name,
        ':email'         => $email,
        ':business_name' => $business_name,
        ':category'      => $category,
        ':description'   => $description,
        ':phone_number'  => $phone_number,
        ':whatsapp_number'=> $whatsapp_number,
        ':image_names'   => $imageNamesJson,
        ':lat'           => $lat,
        ':lon'           => $lon,
        ':location'      =>$location
    ]);
}

fclose($file);

echo "CSV data imported successfully.";
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>CSV Import</title>
    <style>
        body {
             font-family: Arial, sans-serif; max-width: 100vw; margin: 20px auto; padding: 20px; text-align:center;}
        .container { width:500px; margin: 10px auto; padding: 20px; border: none; border-radius: 5px;box-shadow:0 0 6px black; }
        .result { margin: 20px 0; padding: 15px; background: #f0f8ff; border-radius: 5px; }
        .back-link { display: block; margin-top: 20px; }
        .error { color: #d00; }
        /* Navbar */
        .navbar {
            background-color: #2c3e50;
            overflow: hidden;
            display: flex;
            justify-content: center;
        }

        .navbar a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .navbar a:hover {
            background-color: #1abc9c;
        }
        input{
            text-align:center;
            width:70%;
            height:10%;
        }
        input[type="submit"]{
            color:white;
            background:orange;
            border:none;
            outline:none;
            padding:10px;
            font-size:1.2rem;
            font-weight:bold;
            cursor:pointer;
        }
    </style>
</head>
<body>
    <div class="navbar">
    <a href="admin_dashboard.php">Dashboard Home</a>
    <a href="admin.php">Grant Service</a>
    <a href="admin_delete.php">Delete Services</a>
    <a href="admin_contact_msg.php">User Messages</a>
    <a href="admin_logout.php" class="logout-btn">Logout</a>
</div>


    <h2>Service Provider List Upload</h2>
    <div class="container">
        <h2>Import User Data</h2>
        
        
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <label >Upload CSV file:</label>
            <input type="file" name="csv_file" accept=".csv" required><br><br>
            <input type="submit" name="import" value="Import CSV">
        </form>
    </div>
</body>
</html>

