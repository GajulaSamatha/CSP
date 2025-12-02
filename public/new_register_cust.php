<?php

require_once __DIR__ . '/../src/mysqli_compat.php';
$host = "localhost";
$dbUser = "root";
$dbPass = "1234";
$dbName = "nandyal_dial";

session_start();
$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error array
$errors = [];

if(isset($_POST['register_cust'])){
    // Sanitize and validate inputs
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phoneNumber'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $password = $_POST['password'] ?? '';
    $lat = $_POST['lat'] ?? '';
    $lon = $_POST['lon'] ?? '';

    // Validation
    if (empty($firstName)) {
        $errors['first_name'] = "First name is required";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $firstName)) {
        $errors['first_name'] = "Only letters and white space allowed";
    }

    if (empty($lastName)) {
        $errors['last_name'] = "Last name is required";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $lastName)) {
        $errors['last_name'] = "Only letters and white space allowed";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['email'] = "Email already registered";
        }
        $stmt->close();
    }

    if (empty($phone)) {
        $errors['phoneNumber'] = "Phone number is required";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors['phoneNumber'] = "Invalid phone number (10 digits required)";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters";
    }

    if (empty($location)) {
        $errors['location'] = "Location is required";
    }

    if (empty($lat) || empty($lon)) {
        $errors['geolocation'] = "Please enable location services";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO customers (first_name, last_name, email, password, phoneNumber, lat, lon, location) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $firstName, $lastName, $email, $hashedPassword, $phone, $lat, $lon, $location);

        if ($stmt->execute()) {
            $_SESSION['user_name'] = $firstName . " " . $lastName;
            $stmt->close();
            $conn->close();
            header('Location: new_login.php');
            exit();
        } else {
            $errors['database'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registration Form</title>
  <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
    }
    .form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 500px;
        padding: 30px;
        animation: fadeIn 0.6s ease-in-out;
    }
    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #6a11cb;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 15px;
    }
    .form-group label {
        font-weight: 500;
        margin-bottom: 5px;
        color: #555;
    }
    .form-group input {
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 16px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .form-group input:focus {
        border-color: #6a11cb;
        box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        outline: none;
    }
    .form-row {
        display: flex;
        gap: 10px;
    }
    .form-row .form-group {
        flex: 1;
    }
    .btn-submit {
        width: 100%;
        padding: 12px;
        background: linear-gradient(90deg, #6a11cb, #2575fc);
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 18px;
        cursor: pointer;
        transition: background 0.3s;
    }
    .btn-submit:hover {
        background: linear-gradient(90deg, #5b0db8, #1f64d9);
    }
    p {
        text-align: center;
        margin-top: 10px;
    }
    p a {
        color: #6a11cb;
        text-decoration: none;
        font-size: 14px;
    }
    p a:hover {
        text-decoration: underline;
        color: #2575fc;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 600px) {
        .form-row {
            flex-direction: column;
        }
    }
    .error {
        color: #e74c3c;
        font-size: 14px;
        margin-top: 5px;
    }
    .location-buttons {
        display: flex;
        gap: 10px;
        margin-top: 5px;
    }
    .location-btn {
        padding: 8px 12px;
        background: #6a11cb;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    .location-btn:hover {
        background: #5b0db8;
    }
    .map-container {
        margin-top: 15px;
        height: 200px;
        border-radius: 8px;
        overflow: hidden;
        display: none;
    }
    #map {
        height: 100%;
        width: 100%;
    }
</style>
</head>
<body>

  <div class="form-container">
    <h2>Registration Form</h2>
    <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (!empty($errors)): ?>
        <div style="color: red; margin-bottom: 15px; padding: 10px; background: #ffebee; border-radius: 4px;">
            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
foreach ($errors as $error): ?>
                <p><?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($error); ?></p>
            <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endforeach; ?>
        </div>
    <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
    
    <form action="new_register_cust.php" method="POST" id="registrationForm">
      <div class="form-row">
        <div class="form-group">
          <label for="first_name">First Name</label>
          <input type="text" id="first_name" name="first_name" value="<?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($firstName ?? ''); ?>" required />
          <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (isset($errors['first_name'])): ?>
              <span class="error"><?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($errors['first_name']); ?></span>
          <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
        </div>
        <div class="form-group">
          <label for="last_name">Last Name</label>
          <input type="text" id="last_name" name="last_name" value="<?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($lastName ?? ''); ?>" required />
          <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (isset($errors['last_name'])): ?>
              <span class="error"><?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($errors['last_name']); ?></span>
          <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
        </div>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($email ?? ''); ?>" required />
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (isset($errors['email'])): ?>
            <span class="error"><?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($errors['email']); ?></span>
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (isset($errors['password'])): ?>
            <span class="error"><?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($errors['password']); ?></span>
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
      </div>
      
      <div class="form-group">
        <label for="phoneNumber">Phone Number</label>
        <input type="tel" id="phoneNumber" name="phoneNumber" value="<?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($phone ?? ''); ?>" required />
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (isset($errors['phoneNumber'])): ?>
            <span class="error"><?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($errors['phoneNumber']); ?></span>
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="lat">Latitude</label>
          <input type="text" id="lat" name="lat" readonly required />
        </div>
        <div class="form-group">
          <label for="lon">Longitude</label>
          <input type="text" id="lon" name="lon" readonly required />
        </div>
      </div>
      <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (isset($errors['geolocation'])): ?>
          <span class="error"><?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($errors['geolocation']); ?></span>
      <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>

      <div class="form-group">
        <label for="location">Location</label>
        <input type="text" id="location" name="location" value="<?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($location ?? ''); ?>" required />
        <div class="location-buttons">
          <button type="button" class="location-btn" id="detectLocation">Detect My Location</button>
          <button type="button" class="location-btn" id="pickOnMap">Pick on Map</button>
        </div>
        <div class="map-container" id="mapContainer">
          <div id="map"></div>
        </div>
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
if (isset($errors['location'])): ?>
            <span class="error"><?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
echo htmlspecialchars($errors['location']); ?></span>
        <?php 
require_once __DIR__ . '/../src/mysqli_compat.php';
endif; ?>
      </div>

      <button type="submit" class="btn-submit" name="register_cust">Register</button>
    </form>
    <p><a href="new_login.php">Already Have an account? Login</a></p>
    <p><a href="new_register_prov.php">Register as Provider</a></p>
  </div>

  <!-- Load Leaflet CSS and JS for maps -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {

        document.getElementById('detectLocation').click();
        // Form validation
        const form = document.getElementById('registrationForm');
        form.addEventListener('submit', function(e) {
            let valid = true;
            
            // Validate phone number
            const phone = document.getElementById('phoneNumber');
            if (!/^\d{10}$/.test(phone.value)) {
                alert('Please enter a valid 10-digit phone number');
                valid = false;
            }
            
            // Validate password length
            const password = document.getElementById('password');
            if (password.value.length < 8) {
                alert('Password must be at least 8 characters long');
                valid = false;
            }
            
            // Check if location is detected
            const lat = document.getElementById('lat');
            const lon = document.getElementById('lon');
            if (!lat.value || !lon.value) {
                alert('Please detect your location or pick it on the map');
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });
        
        // Detect location button
        const detectBtn = document.getElementById('detectLocation');
        detectBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                document.getElementById('location').value = "Detecting...";
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude.toFixed(6);
                        const lon = position.coords.longitude.toFixed(6);
                        
                        document.getElementById('lat').value = lat;
                        document.getElementById('lon').value = lon;
                        
                        // Reverse geocode to get address
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                            .then(response => response.json())
                            .then(data => {
                                let address = '';
                                if (data.address) {
                                    if (data.address.road) address += data.address.road + ', ';
                                    if (data.address.neighbourhood) address += data.address.neighbourhood + ', ';
                                    if (data.address.suburb) address += data.address.suburb + ', ';
                                    if (data.address.city) address += data.address.city + ', ';
                                    if (data.address.state) address += data.address.state + ', ';
                                    if (data.address.country) address += data.address.country;
                                }
                                document.getElementById('location').value = address || 'Location detected';
                            })
                            .catch(() => {
                                document.getElementById('location').value = 'Location detected';
                            });
                    },
                    function(error) {
                        let message = "Unknown error";
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                message = "Permission denied. Please allow location access.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                message = "Location information is unavailable. Please ensure location services are enabled.";
                                break;
                            case error.TIMEOUT:
                                message = "The request to get your location timed out.";
                                break;
                        }
                        document.getElementById('location').value = "";
                        alert("Error: " + message);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        });
        
        // Pick on map functionality
        const pickOnMapBtn = document.getElementById('pickOnMap');
        const mapContainer = document.getElementById('mapContainer');
        let map, marker;
        
        pickOnMapBtn.addEventListener('click', function() {
            mapContainer.style.display = 'block';
            
            if (!map) {
                map = L.map('map').setView([15.4909, 78.4998], 13); // Default to Nandyal coordinates
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                
                // Add click event to map
                map.on('click', function(e) {
                    const { lat, lng } = e.latlng;
                    document.getElementById('lat').value = lat.toFixed(6);
                    document.getElementById('lon').value = lng.toFixed(6);
                    
                    // Update or add marker
                    if (marker) {
                        marker.setLatLng([lat, lng]);
                    } else {
                        marker = L.marker([lat, lng]).addTo(map);
                    }
                    
                    // Reverse geocode to get address
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                        .then(response => response.json())
                        .then(data => {
                            let address = '';
                            if (data.address) {
                                if (data.address.road) address += data.address.road + ', ';
                                if (data.address.neighbourhood) address += data.address.neighbourhood + ', ';
                                if (data.address.suburb) address += data.address.suburb + ', ';
                                if (data.address.city) address += data.address.city + ', ';
                                if (data.address.state) address += data.address.state + ', ';
                                if (data.address.country) address += data.address.country;
                            }
                            document.getElementById('location').value = address || 'Location selected on map';
                        })
                        .catch(() => {
                            document.getElementById('location').value = 'Location selected on map';
                        });
                });
            }
        });
        
        // Try to detect location automatically on page load
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(6);
                    const lon = position.coords.longitude.toFixed(6);
                    document.getElementById('lat').value = lat;
                    document.getElementById('lon').value = lon;
                    
                    // Reverse geocode to get address
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(response => response.json())
                        .then(data => {
                            let address = '';
                            if (data.address) {
                                if (data.address.road) address += data.address.road + ', ';
                                if (data.address.neighbourhood) address += data.address.neighbourhood + ', ';
                                if (data.address.suburb) address += data.address.suburb + ', ';
                                if (data.address.city) address += data.address.city + ', ';
                                if (data.address.state) address += data.address.state + ', ';
                                if (data.address.country) address += data.address.country;
                            }
                            document.getElementById('location').value = address || 'Location detected';
                        })
                        .catch(() => {
                            document.getElementById('location').value = 'Location detected';
                        });
                },
                function(error) {
                    // Silent fail - user can manually detect or pick on map
                },
                {
                    enableHighAccuracy: true,
                    timeout: 5000
                }
            );
        }
    });
  </script>
</body>
</html>

