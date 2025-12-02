<?php

require_once __DIR__ . '/../src/mysqli_compat.php';
session_start();
    $conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
    if ($conn->connect_error) die("Connection failed");
if(isset($_POST['register-prov'])){
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $email = $_POST['email'];
    $pass = $_POST['password']; // plain text password (⚠️ insecure)
    $desc = $_POST['description'];
    $bname = $_POST['business_name'];
    // $cat = $_POST['category'];
    $phone = $_POST['phone_number'];
    $wa = $_POST['whatsapp_number'];

    $mon_fri_start = $_POST['mon_fri_start'];
    $mon_fri_end = $_POST['mon_fri_end'];
    $sat_start = $_POST['sat_start'];
    $sat_end = $_POST['sat_end'];
    $sun_start = $_POST['sun_start'] || '00:00';
    $sun_end = $_POST['sun_end'] || '00:00';


    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    $loc = $_POST['location'];
    $category = ($_POST['category'] === 'Other') ? $_POST['other_category'] : $_POST['category'];
    if($_POST['category'] === 'Other'){
      $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $checkStmt->execute([$category]);
        
        if ($checkStmt->rowCount() > 0) {
            // Category exists - increment count
            $updateStmt = $pdo->prepare(
                "UPDATE categories SET category_count = category_count + 1 
                 WHERE name = ?"
            );
            $updateStmt->execute([$category]);
            return "Category count incremented successfully";
        } else {
            // New category - insert with count 1
            $insertStmt = $pdo->prepare(
                "INSERT INTO categories (name, description,category_count) 
                 VALUES (?, ?,1)"
            );
            $insertStmt->execute([$category,$desc]);
            return "New category added successfully";
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

var_dump($first, $last, $email, $pass, $bname, $category, $desc, $phone, $wa, $image_names, $lat, $lon, $loc);

    $stmt = $conn->prepare("INSERT INTO providers 
        (first_name, last_name, email, password, business_name, category, description, phone_number, whatsapp_number, image_names, lat, lon, location)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssdds", $first, $last, $email, $pass, $bname, $category, $desc, $phone, $wa, $image_names, $lat, $lon, $loc);
    

    if ($stmt->execute()) {
      $last_id = $conn->insert_id;
      $stmt_admin = $conn->prepare("INSERT INTO admin_grant 
        (id,first_name, last_name, email, business_name, category, description, phone_number, whatsapp_number, image_names, lat, lon, location)
        VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_admin->bind_param("isssssssssdds", $last_id,$first, $last, $email, $bname, $category, $desc, $phone, $wa, $image_names, $lat, $lon, $loc);
        $stmt_admin->execute();
        $_SESSION['user_name']=$first." ".$last;
        $stmt->close();
        $stmt_admin->close();
        $conn->close();
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
  <title>Provider Registration</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #4f46e5;
      --primary-light: #6366f1;
      --secondary: #10b981;
      --dark: #1e293b;
      --light: #f8fafc;
      --gray: #94a3b8;
      --danger: #ef4444;
      --success: #10b981;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      background-color: #f1f5f9;
      color: var(--dark);
      line-height: 1.6;
      padding: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .container {
      max-width: 900px;
      margin: 2rem auto;
      padding: 0 1rem;
      width: 100%;
    }

    .registration-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      margin-bottom: 2rem;
    }

    .card-header {
      background: var(--primary);
      color: white;
      padding: 1.5rem;
      text-align: center;
    }

    .card-header h1 {
      font-size: 1.8rem;
      font-weight: 600;
    }

    .card-body {
      padding: 2rem;
    }

    .form-section {
      margin-bottom: 2.5rem;
    }

    .section-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 1.2rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid #e2e8f0;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .section-title i {
      font-size: 1rem;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.2rem;
    }

    .form-group {
      margin-bottom: 1.2rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--dark);
    }

    .form-control {
      width: 100%;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      background-color: white;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary-light);
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }

    textarea.form-control {
      min-height: 120px;
      resize: vertical;
    }

    .input-group {
      position: relative;
    }

    .input-group i {
      position: absolute;
      top: 50%;
      left: 1rem;
      transform: translateY(-50%);
      color: var(--gray);
    }

    .input-group .form-control {
      padding-left: 2.8rem;
    }

    .btn {
      display: inline-block;
      padding: 0.8rem 1.5rem;
      font-size: 1rem;
      font-weight: 500;
      text-align: center;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background-color: var(--primary);
      color: white;
      width: 100%;
      padding: 1rem;
      font-size: 1.1rem;
    }
    

    .btn-primary:hover {
      background-color: var(--primary-light);
    }

    .btn-link {
      background: none;
      color: var(--primary);
      text-decoration: underline;
      padding: 0;
    }

    .btn-link:hover {
      color: var(--primary-light);
    }

    .image-upload {
      border: 2px dashed #cbd5e1;
      border-radius: 8px;
      padding: 1.5rem;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .image-upload:hover {
      border-color: var(--primary-light);
    }

    .image-upload i {
      font-size: 2rem;
      color: var(--gray);
      margin-bottom: 0.5rem;
    }

    .image-preview {
      display: flex;
      flex-wrap: wrap;
      gap: 0.8rem;
      margin-top: 1rem;
    }

    .preview-image {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid #e2e8f0;
    }

    .availability-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }

    .time-input {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .time-input label {
      flex: 1;
    }

    .footer-links {
      display: flex;
      justify-content: center;
      gap: 1.5rem;
      margin-top: 1.5rem;
    }

    @media (max-width: 768px) {
      .card-body {
        padding: 1.5rem;
      }
      
      .form-grid {
        grid-template-columns: 1fr;
      }
      
      .availability-grid {
        grid-template-columns: 1fr;
      }
    }

    /* Hide file input */
    #images {
      display: none;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="registration-card">
    <div class="card-header">
      <h1><i class="fas fa-user-tie"></i> Provider Registration</h1>
    </div>
    
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data" action="reg.php">
        <!-- Personal Information Section -->
        <div class="form-section">
          <h2 class="section-title"><i class="fas fa-user"></i> Personal Information</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="first_name">First Name</label>
              <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" id="first_name" name="first_name" class="form-control" placeholder="John" required>
              </div>
            </div>
            
            <div class="form-group">
              <label for="last_name">Last Name</label>
              <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Doe" required>
              </div>
            </div>
            
            <div class="form-group">
              <label for="email">Email</label>
              <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com" required>
              </div>
            </div>
            
            <div class="form-group">
              <label for="password">Password</label>
              <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
              </div>
            </div>
          </div>
        </div>

        <!-- Business Details Section -->
        <div class="form-section">
          <h2 class="section-title"><i class="fas fa-briefcase"></i> Business Details</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="business_name">Business Name</label>
              <div class="input-group">
                <i class="fas fa-store"></i>
                <input type="text" id="business_name" name="business_name" class="form-control" placeholder="My Business">
              </div>
            </div>
            
            <div class="form-group">
              <label for="category">Category</label>
              <select id="category" name="category" class="form-control" required onchange="toggleOtherCategory(this.value)">
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
              <input type="text" id="other_category" name="other_category" class="form-control" placeholder="Enter your category">
            </div>
          </div>
          
          <div class="form-group">
            <label for="description">Business Description</label>
            <textarea id="description" name="description" class="form-control" placeholder="Describe your services..."></textarea>
          </div>
        </div>

        <!-- Contact Information Section -->
        <div class="form-section">
          <h2 class="section-title"><i class="fas fa-phone-alt"></i> Contact Information</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="phone_number">Phone Number</label>
              <div class="input-group">
                <i class="fas fa-mobile-alt"></i>
                <input type="text" id="phone_number" name="phone_number" class="form-control" placeholder="+91 9876543210" required>
              </div>
            </div>
            
            <div class="form-group">
              <label for="whatsapp_number">WhatsApp Number</label>
              <div class="input-group">
                <i class="fab fa-whatsapp"></i>
                <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control" placeholder="+91 9876543210">
              </div>
            </div>
          </div>
        </div>

        <!-- Images Section -->
        <div class="form-section">
          <h2 class="section-title"><i class="fas fa-images"></i> Business Images</h2>
          <div class="image-upload" onclick="document.getElementById('images').click()">
            <i class="fas fa-cloud-upload-alt"></i>
            <p>Click to upload images (Max 5)</p>
            <p class="text-muted">Supports JPG, PNG</p>
          </div>
          <input type="file" name="images[]" id="images" multiple accept="image/*">
          <div class="image-preview" id="imagePreview"></div>
        </div>

        <div style="background: #ffffff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); padding: 25px; margin-bottom: 30px;">
  <h2 style="color: #2c3e50; font-size: 1.4rem; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
    <i style="color: #3498db;" class="fas fa-clock"></i> Business Hours
  </h2>
  
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
    <div style="margin-bottom: 15px;">
      <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #34495e; font-size: 0.95rem;">Monday - Friday</label>
      <div style="display: flex; align-items: center; gap: 10px;">
        <input type="time" name="mon_fri_start" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; transition: all 0.3s ease; background-color: #f9f9f9;">
        <span style="color: #7f8c8d; font-size: 0.9rem;">to</span>
        <input type="time" name="mon_fri_end" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; transition: all 0.3s ease; background-color: #f9f9f9;">
      </div>
    </div>
    
    <div style="margin-bottom: 15px;">
      <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #34495e; font-size: 0.95rem;">Saturday</label>
      <div style="display: flex; align-items: center; gap: 10px;">
        <input type="time" name="sat_start" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; transition: all 0.3s ease; background-color: #f9f9f9;">
        <span style="color: #7f8c8d; font-size: 0.9rem;">to</span>
        <input type="time" name="sat_end" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; transition: all 0.3s ease; background-color: #f9f9f9;">
      </div>
    </div>
    
    <div style="margin-bottom: 15px;">
      <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #34495e; font-size: 0.95rem;">Sunday</label>
      <div style="display: flex; align-items: center; gap: 10px;">
        <input type="time" name="sun_start" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; transition: all 0.3s ease; background-color: #f9f9f9;">
        <span style="color: #7f8c8d; font-size: 0.9rem;">to</span>
        <input type="time" name="sun_end" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; transition: all 0.3s ease; background-color: #f9f9f9;">
      </div>
    </div>
  </div>
</div>

        <!-- Location Section -->
        <div class="form-section">
          <h2 class="section-title"><i class="fas fa-map-marker-alt"></i> Business Location</h2>
          <div class="form-group">
            <label for="location">Address</label>
            <div class="input-group">
              <i class="fas fa-search-location"></i>
              <input type="text" name="location" id="location" class="form-control" placeholder="Detecting your location..." readonly>
            </div>
          </div>
          <input type="hidden" name="lat" id="lat">
          <input type="hidden" name="lon" id="lon">
          <p class="text-muted" style="font-size: 0.9rem; color: var(--gray); margin-top: 0.5rem;">
            <i class="fas fa-info-circle"></i> We use your current location to help customers find you
          </p>
        </div>

        <button type="submit" name="register-prov" class="btn btn-primary">
          <i class="fas fa-user-plus"></i> Register Now
        </button>
      </form>
      
      <div class="footer-links">
        <a href="new_provider_login.php" class="btn btn-link">
          <i class="fas fa-sign-in-alt"></i> Already have an account? Login
        </a>
        <a href="new_register_cust.php" class="btn btn-link">
          <i class="fas fa-user"></i> Register as customer
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  window.onload = function() {
    if ("geolocation" in navigator) {
      navigator.geolocation.getCurrentPosition((pos) => {
        document.getElementById("lat").value = pos.coords.latitude;
        document.getElementById("lon").value = pos.coords.longitude;

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.coords.latitude}&lon=${pos.coords.longitude}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById("location").value = data.display_name || "Detected";
        }).catch(() => {
          document.getElementById("location").value = "Detected";
        });

      }, () => {
        alert("Location access denied!");
      });
    } else {
      alert("Geolocation not supported!");
    }
  };
  function toggleOtherCategory(value) {
    const otherDiv = document.getElementById('otherCategoryDiv');
    otherDiv.style.display = value === 'Other' ? 'block' : 'none';
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
</script>

</body>
</html>

