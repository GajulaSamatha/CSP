<?php


require_once __DIR__ . '/../src/mysqli_compat.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
    $conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
    if ($conn->connect_error) die("Connection failed");

    // Check if session username exists
    if (!isset($_SESSION['user_name'])) {
        die("User not logged in.");
    }

    //Split session username into first and last name
   //Split session username safely
$nameParts = explode(' ', $_SESSION['user_name'], 2);
$first = $nameParts[0];
$last = isset($nameParts[1]) ? $nameParts[1] : ''; // if no last name, use empty string

    echo($first);
    echo($last);
        $user_type = $_SESSION['user_type'];
        $sql_customer = "SELECT email, password FROM " . $user_type . "s WHERE first_name='$first' AND last_name='$last'";
        $resp=$conn->query($sql_customer);
          if (!$resp) {
              die("Customer query failed: " . $conn->error);
          }

        $re=mysqli_fetch_assoc($resp);

if(isset($_POST['add-new-service'])){
    $desc = $_POST['description'];
    $bname = $_POST['business_name'];
    // $cat = $_POST['category'];
    $phone = $_POST['phone_number'];
    $wa = $_POST['whatsapp_number'];

    $mon_fri_start = $_POST['mon_fri_start'];
    $mon_fri_end = $_POST['mon_fri_end'];
    $sat_start = $_POST['sat_start'];
    $sat_end = $_POST['sat_end'];
    $sun_start = !empty($_POST['sun_start']) ? $_POST['sun_start'] : '00:00';
    $sun_end = !empty($_POST['sun_end']) ? $_POST['sun_end'] : '00:00';



    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    $loc = $_POST['location'];
    $category = ($_POST['category'] === 'Other') ? $_POST['other_category'] : $_POST['category'];
if($_POST['category'] === 'Other') {
    // First check if category exists
    $checkStmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
    $checkStmt->bind_param("s", $category);
    $checkStmt->execute();
    $checkStmt->store_result(); // Store the result set
    
    if ($checkStmt->num_rows > 0) {
        // Category exists - increment count
        $updateStmt = $conn->prepare(
            "UPDATE categories SET category_count = category_count + 1 
             WHERE name = ?"
        );
        $updateStmt->bind_param("s", $category);
        $updateStmt->execute();
        $updateStmt->close();
        $checkStmt->close();
        
    } else {
        // New category - insert with count 1
        $insertStmt = $conn->prepare(
            "INSERT INTO categories (name, description, category_count) 
             VALUES (?, ?, 1)"
        );
        $insertStmt->bind_param("ss", $category, $desc);
        $insertStmt->execute();
        $insertStmt->close();
        $checkStmt->close();
        
    }
}
      
    


    // ✅ Handle multiple image uploads
    $uploadedFiles = [];
    $total = count($_FILES['images']['name']);

    $firstname = strtolower(trim($first)); // assuming this comes from form
    $lastname  = strtolower(trim($last));

    // Sanitize to remove spaces/special chars from name
    $cleanName = preg_replace("/[^a-z0-9]/", "", $firstname . "_" . $lastname);

    for ($i = 0; $i < $total; $i++) {
        $tmp = $_FILES['images']['tmp_name'][$i];
        $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION); // get file extension

        $newName = $cleanName . "_img" . $i . "." . $ext;
        $target = "uploads/" . $newName;

        if (move_uploaded_file($tmp, $target)) {
            $uploadedFiles[] = $newName;
        }
    }

    $image_names = json_encode($uploadedFiles);

var_dump($first, $last, $re['email'], $re['password'], $bname, $category, $desc, $phone, $wa, $image_names, $lat, $lon, $loc);
    $stmt = $conn->prepare("INSERT INTO providers 
        (first_name, last_name, email, password, business_name, category, description, phone_number, whatsapp_number, image_names, lat, lon, location,mon_fri_start,mon_fri_end,sat_start,sat_end,sun_start,sun_end)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssssssddsssssss", $first, $last, $re['email'], $re['password'], $bname, $category, $desc, $phone, $wa, $image_names, $lat, $lon, $loc,$mon_fri_start,$mon_fri_end,$sat_start,$sat_end,$sun_start,$sun_end);
    

    if ($stmt->execute()) {
      $last_id = $conn->insert_id;
      $stmt_admin = $conn->prepare("INSERT INTO admin_grant 
        (id,first_name, last_name, email, business_name, category, description, phone_number, whatsapp_number, image_names, lat, lon, location)
        VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt_admin->bind_param("isssssssssdds", $last_id,$first, $last, $re['email'], $bname, $category, $desc, $phone, $wa, $image_names, $lat, $lon, $loc);
        $stmt_admin->execute();
        // $_SESSION['user_name']=$first.$last;
        $stmt->close();
        $stmt_admin->close();
        $conn->close();
        // $insertStmt->close();
        // $updateStmt->close();
        // $checkStmt->close();
        // echo "<script>alert('Provider registered successfully!');</script>";
        header("Location: index.php");
        exit();

    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
    $conn->close();
  }
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Service</title>
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
      background: linear-gradient(135deg, #f8fafc, #e2e8f0); /* Light gradient background */
      min-height: 100vh;
    }

    /* Header-matching color scheme */
    :root {
      --primary: #4A00E0;
      --secondary: #8E2DE2;
      --accent: #3498db;
      --light: #f8f9fa;
      --dark: #2c3e50;
      --text-light: #ffffff;
      --text-dark: #2c3e50;
    }

    .main_content {
      max-width: 900px;
      margin: 2rem auto;
      padding: 2rem;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    h1 {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 2rem;
      color: var(--primary);
      position: relative;
      padding-bottom: 10px;
    }

    h1::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 3px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      border-radius: 3px;
    }

    fieldset {
      border: none;
      margin-bottom: 2rem;
      padding: 0;
    }

    legend {
      font-weight: 600;
      font-size: 1.2rem;
      margin-bottom: 1rem;
      color: var(--primary);
      padding-left: 10px;
      border-left: 4px solid var(--secondary);
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--dark);
    }

    input, select, textarea {
      width: 100%;
      padding: 12px 16px;
      font-size: 1rem;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      background: #f9fafb;
      transition: all 0.3s ease;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    input:focus, select:focus, textarea:focus {
      border-color: var(--secondary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(138, 99, 210, 0.2);
      background: #fff;
    }

    textarea {
      resize: vertical;
      min-height: 120px;
    }

    .availability-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }

    .availability-grid label {
      background: #f9fafb;
      padding: 12px;
      border-radius: 8px;
      border: 1px solid #e5e7eb;
    }

    .availability-grid input {
      margin-top: 8px;
      background: #fff;
    }

    button[type="submit"] {
      width: 100%;
      padding: 16px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: var(--text-light);
      font-size: 1.1rem;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.4s ease;
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(74, 0, 224, 0.3);
    }

    button[type="submit"]:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(74, 0, 224, 0.4);
    }

    button[type="submit"]::after {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: 0.5s;
    }

    button[type="submit"]:hover::after {
      left: 100%;
    }

    #imagePreview {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 1rem;
    }

    #imagePreview img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #ddd;
      transition: transform 0.3s ease;
    }

    #imagePreview img:hover {
      transform: scale(1.1);
    }

    /* Ethnic pattern overlay */
    .main_content::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54 50.77c-3.07 2.67-7.8 3.96-14.14 3.96-11.05 0-20-6.45-20-14.4 0-7.95 8.95-14.4 20-14.4 6.34 0 11.07 1.3 14.14 3.96C57.61 31.8 60 36.15 60 40.33c0 4.18-2.39 8.53-6 10.44zM6 29.23c3.07-2.67 7.8-3.96 14.14-3.96 11.05 0 20 6.45 20 14.4 0 7.95-8.95 14.4-20 14.4-6.34 0-11.07-1.3-14.14-3.96C2.39 48.2 0 43.85 0 39.67c0-4.18 2.39-8.53 6-10.44z' fill='%234A00E0' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
      opacity: 0.5;
      pointer-events: none;
      z-index: -1;
      border-radius: 16px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .main_content {
        margin: 1rem;
        padding: 1.5rem;
      }
      
      .form-grid {
        grid-template-columns: 1fr;
      }
      
      .availability-grid {
        grid-template-columns: 1fr;
      }
      
      h1 {
        font-size: 1.5rem;
      }
    }

    @media (max-width: 480px) {
      .main_content {
        padding: 1rem;
      }
      
      input, select, textarea {
        padding: 10px 12px;
      }
    }
  </style>
</head>
<body>
<?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
include_once __DIR__ . '/../templates/new_header.php'; ?>
<main class="main_content">
  <form method="POST" enctype="multipart/form-data" action="new_add_services.php">
    <h1>Add New Service</h1>

    <div class="form-grid">
      <!-- Business Info -->
      <fieldset>
        <legend>Business Details</legend>
        <div class="form-group">
          <label for="business_name">Business Name</label>
          <input type="text" name="business_name" id="business_name" placeholder="Enter business name" required>
        </div>
        
        <div class="form-group">
          <label for="category">Category</label>
          <select name="category" id="category" required onchange="toggleOtherCategory(this.value)">
            <option value="">-- Select Category --</option>
            <option value="Plumber">Plumber</option>
            <option value="Electrician">Electrician</option>
            <option value="Mechanic">Mechanic</option>
            <option value="Beautician">Beautician</option>
            <option value="Grocery">Grocery</option>
            <option value="Tailor">Tailor</option>
            <option value="AC Technician">AC Technician</option>
            <option value="Water Supply">Water Supply</option>
            <option value="Tutor">Tutor</option>
            <option value="Other">Other</option>
          </select>
        </div>
        
        <div class="form-group" id="otherCategoryDiv" style="display:none;">
          <label for="other_category">Specify Category</label>
          <input type="text" name="other_category" id="other_category" placeholder="Enter your category">
        </div>
        
        <div class="form-group">
          <label for="description">Description</label>
          <textarea name="description" id="description" placeholder="Describe your service..." required></textarea>
        </div>
      </fieldset>

      <!-- Contact & Images -->
      <fieldset>
        <legend>Contact & Media</legend>
        <div class="form-group">
          <label for="phone_number">Phone Number</label>
          <input type="text" name="phone_number" id="phone_number" placeholder="Enter phone number" required>
        </div>
        
        <div class="form-group">
          <label for="whatsapp_number">WhatsApp Number</label>
          <input type="text" name="whatsapp_number" id="whatsapp_number" placeholder="Enter WhatsApp number">
        </div>
        
        <div class="form-group">
          <label for="images">Upload Images (multiple)</label>
          <input type="file" name="images[]" id="images" multiple accept="image/*">
          <div id="imagePreview"></div>
        </div>
      </fieldset>
    </div>

    <!-- Availability -->
    <fieldset>
      <legend>Availability</legend>
      <div class="availability-grid">
        <label>Mon–Fri Start <input type="time" name="mon_fri_start" required></label>
        <label>Mon–Fri End <input type="time" name="mon_fri_end" required></label>
        <label>Saturday Start <input type="time" name="sat_start" required></label>
        <label>Saturday End <input type="time" name="sat_end" required></label>
        <label>Sunday Start <input type="time" name="sun_start"></label>
        <label>Sunday End <input type="time" name="sun_end"></label>
      </div>
    </fieldset>

    <!-- Location -->
    <fieldset>
      <legend>Location</legend>
      <div class="form-group">
        <label for="location">Business Location</label>
        <input type="text" name="location" id="location" placeholder="Enter your business location" required>
        <input type="hidden" name="lat" id="lat">
        <input type="hidden" name="lon" id="lon">
      </div>
    </fieldset>

    <button type="submit" name="add-new-service">Add Service</button>
  </form>
</main>

<script>
  function toggleOtherCategory(value) {
    const otherDiv = document.getElementById('otherCategoryDiv');
    otherDiv.style.display = value === 'Other' ? 'block' : 'none';
    if (value !== 'Other') {
      document.getElementById('other_category').value = '';
    }
  }

  document.getElementById('images').addEventListener('change', function (e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    Array.from(e.target.files).forEach(file => {
      const reader = new FileReader();
      reader.onload = e => {
        const img = document.createElement('img');
        img.src = e.target.result;
        preview.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  });

  // Simple form validation
  document.querySelector('form').addEventListener('submit', function(e) {
    const category = document.getElementById('category').value;
    if (category === 'Other' && document.getElementById('other_category').value.trim() === '') {
      alert('Please specify your category');
      e.preventDefault();
    }
  });
</script>

</body>
</html>

