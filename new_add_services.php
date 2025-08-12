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
    list($first, $last) = explode(' ', $_SESSION['user_name'], 2);
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
    if($_POST['category'] === 'Other'){
      $checkQuery = "SELECT id FROM categories WHERE name = '$category'";
      $checkResult = mysqli_query($conn, $checkQuery);

      if (mysqli_num_rows($checkResult) === 0) {
        $s=$conn->prepare("INSERT INTO categories(name,description) VALUES(?,?)");
        $s->bind_param("ss",$category,$desc);
        $s->execute();
        $s->close();
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
        $_SESSION['user_name']=$first.$last;
        $stmt->close();
        $stmt_admin->close();
        $conn->close();
        echo "<script>alert('Provider registered successfully!');</script>";
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
        box-sizing: border-box;
      }

      body {
        margin: 0;
        font-family: system-ui, sans-serif;
        background: #f9f9f9;
        color: #333;
        padding: 2rem;
      }

      .container {
        max-width: 800px;
        margin: 0 auto;
        background: lightblue;
        /* background: linear-gradient(to top right,lightgreen,yellow,white); */
        padding: 2rem 3rem;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
      }

      h1 {
        text-align: center;
        margin-bottom: 2rem;
        font-size: 1.8rem;
      }

      fieldset {
        border: none;
        margin-bottom: 2rem;
      }

      legend {
        font-weight: bold;
        font-size: 1.1rem;
        margin-bottom: 1rem;
      }

      input, select, textarea {
        width: 100%;
        padding: 10px 12px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        margin-bottom: 1rem;
        background: lightyellow;
      }

      input:focus, select:focus, textarea:focus {
        border-color: #0077ff;
        outline: none;
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
        background-color: #da48c4ff;
        color: white;
        font-size: 1rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.5s ease;
      }

      button:hover {
        background-color: #793accff;
      }

      #imagePreview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
      }

      #imagePreview img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
      }

</style>
<body>
<?php include"new_header.php"; ?>
<main class="container">
  <form method="POST" enctype="multipart/form-data" action="new_add_services.php">
    <h1>Add New Service</h1>

    <!-- Personal Info -->
    <!-- <fieldset>
      <legend>Personal Information</legend>
      <div class="form-row">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
      </div>
      <div class="form-row">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
      </div>
    </fieldset> -->

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
        <label>Mon–Fri Start <input type="time" name="mon_fri_start" required value="02:45"></label>
        <label>Mon–Fri End <input type="time" name="mon_fri_end" required value="02:45"></label>
        <label>Saturday Start <input type="time" name="sat_start" required value="02:45"></label>
        <label>Saturday End <input type="time" name="sat_end" required value="02:45"></label>
        <label>Sunday Start <input type="time" name="sun_start"  value="02:45"></label>
        <label>Sunday End <input type="time" name="sun_end"  value="02:45"></label>
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
