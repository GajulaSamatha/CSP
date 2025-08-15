<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
    $conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
    if ($conn->connect_error) die("Connection failed");

    // Check if session username exists
    if (!isset($_SESSION['user_name'])) {
        die("User not logged in.");
    }

    // Split session username into first and last name
   // Split session username safely
$nameParts = explode(' ', $_SESSION['user_name'], 2);
$first = $nameParts[0];
$last = isset($nameParts[1]) ? $nameParts[1] : ''; // if no last name, use empty string

    // echo($first.$last);
        $sql_customer="SELECT email,password FROM customers WHERE first_name='$first' AND last_name='$last'";
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
echo $first, $last, $re['email'], $re['password'], $bname, $category, $desc, $phone, $wa, $image_names, $lat, $lon, $loc,$mon_fri_start,$mon_fri_end,$sat_start,$sat_end,$sun_start,$sun_end;
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
  <title>Add New Service</title>
  <link rel="stylesheet" href="styles.css">
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

      .main_content {
        max-width: 800px;
        margin: 2rem auto 0 auto; /* top margin added */
        padding: 2rem 2.5rem;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }


      h1 {
        text-align: center;
        margin-bottom: 2rem;
        font-size: 2rem;
        color: #1877f2;
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
        color: #1877f2;
      }

      input, select, textarea {
        width: 100%;
        padding: 12px 14px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 8px;
        margin-bottom: 1rem;
        background: #f0f2f5;
        transition: 0.3s;
      }

      input:focus, select:focus, textarea:focus {
        border-color: #1877f2;
        outline: none;
        box-shadow: 0 0 5px rgba(24, 119, 242, 0.3);
      }

      textarea {
        resize: vertical;
        min-height: 100px;
      }

      .form-row {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
      }

      .form-row input {
        flex: 1;
        min-width: 240px;
      }

      .availability-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
      }

      button[type="submit"] {
        width: 100%;
        padding: 14px;
        background-color: #1877f2;
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s ease;
      }

      button[type="submit"]:hover {
        background-color: #145dbf;
      }

      #imagePreview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
      }

      #imagePreview img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
      }

      @media (max-width: 600px) {
        .form-row {
          flex-direction: column;
        }
      }
</style>

<body>
<?php include"new_header.php"; ?>
<main class="main_content">
  <form method="POST" enctype="multipart/form-data" action="new_add_services.php">
    <h1>Add New Service</h1>


    <!-- Business Info -->
    <fieldset>
      <legend>Business Details</legend>
      <input type="text" name="business_name" placeholder="Business Name">
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
      <div id="otherCategoryDiv" style="display:none;">
        <input type="text" name="other_category" placeholder="Specify Other Category">
      </div>
      <textarea name="description" placeholder="Describe your service..." required></textarea>
    </fieldset>

    <!-- Contact -->
    <fieldset>
      <legend>Contact</legend>
      <div class="form-row">
        <input type="text" name="phone_number" placeholder="Phone Number" required>
        <input type="text" name="whatsapp_number" placeholder="WhatsApp Number">
      </div>
    </fieldset>

    <!-- Images -->
    <fieldset>
      <legend>Images</legend>
      <label>Upload Images (multiple):</label>
      <input type="file" name="images[]" id="images" multiple accept="image/*">
      <div id="imagePreview"></div>
    </fieldset>

    <!-- Availability -->
    <fieldset>
      <legend>Availability</legend>
      <div class="availability-grid">
        <label>Mon–Fri Start <input type="time" name="mon_fri_start" required></label>
        <label>Mon–Fri End <input type="time" name="mon_fri_end" required></label>
        <label>Saturday Start <input type="time" name="sat_start" required></label>
        <label>Saturday End <input type="time" name="sat_end" required></label>
        <label>Sunday Start <input type="time" name="sun_start" ></label>
        <label>Sunday End <input type="time" name="sun_end" ></label>
      </div>
    </fieldset>

    <!-- Location -->
    <fieldset>
      <legend>Location</legend>
      <input type="text" name="location" id="location" placeholder="Location (Auto-filled)" readonly>
      <input type="hidden" name="lat" id="lat">
      <input type="hidden" name="lon" id="lon">
    </fieldset>

    <button type="submit" name="add-new-service">Add Service</button>
  </form>
</main>

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
