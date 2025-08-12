<?php
$host = "localhost";
$dbUser = "root";
$dbPass = "1234";
$dbName = "nandyal_dial"; // replace this with your DB name

session_start();
$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['register_cust'])){
$firstName = $_POST['first_name'] ?? '';
$lastName = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phoneNumber'] ?? '';
$location = $_POST['location'] ?? '';
$password = $_POST['password'] ?? '';
$lat=$_POST['lat'];
$lon=$_POST['lon'];



// Insert into database
$sql = "INSERT INTO customers (first_name, last_name, email, password, phoneNumber,lat,lon,location) 
        VALUES (?, ?, ?, ?, ?, ?,?,?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $firstName, $lastName, $email, $password, $phone,$lat,$lon,$location);

if ($stmt->execute()) {
    // Redirect to index page after successful registration
    $_SESSION['user_name']=$firstName." ".$lastName;
    $stmt->close();
    $conn->close();
    header('Location: index.php');
    exit();
} else {
    echo("Statement not executed");
    echo "Error: " . $stmt->error;
}

$stmt->close();
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
      font-family: Arial, sans-serif;
    }

    body {
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .form-container {
      background: #fff;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 450px;
    }

    .form-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
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
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
      outline: none;
      transition: border 0.3s;
    }

    .form-group input:focus {
      border-color: #4CAF50;
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
      background: #4CAF50;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 18px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-submit:hover {
      background: #45a049;
    }

    /* Responsive Design */
    @media (max-width: 600px) {
      .form-row {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h2>Registration Form</h2>
    <form action="new_register_cust.php" method="POST">
      <div class="form-row">
        <div class="form-group">
          <label for="first_name">First Name</label>
          <input type="text" id="first_name" name="first_name" required />
        </div>
        <div class="form-group">
          <label for="last_name">Last Name</label>
          <input type="text" id="last_name" name="last_name" required />
        </div>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
      </div>
        <div class="form-group">
        <label for="phoneNumber">Phone Number</label>
        <input type="number" id="phoneNumber" name="phoneNumber" required />
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="lat">Latitude</label>
          <input type="text" id="lat" name="lat" readonly />
        </div>
        <div class="form-group">
          <label for="lon">Longitude</label>
          <input type="text" id="lon" name="lon" readonly />
        </div>
      </div>

      <div class="form-group">
        <label for="location">Location</label>
        <input type="text" id="location" name="location" required />
      </div>

      <button type="submit" class="btn-submit" name="register_cust">Register</button>
    </form>
  </div>

  <script>
    // Auto-fill location using browser geolocation
    window.onload = function () {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function (position) {
            document.getElementById("lat").value = position.coords.latitude.toFixed(6);
            document.getElementById("lon").value = position.coords.longitude.toFixed(6);
          },
          function (error) {
        let message = "Unknown error";

        switch (error.code) {
          case error.PERMISSION_DENIED:
            message = "Permission denied. Please allow location access.";
            break;
          case error.POSITION_UNAVAILABLE:
            message = "Please Turn On your location";
            break;
          case error.TIMEOUT:
            message = "The request to get your location timed out.";
            break;
          case error.UNKNOWN_ERROR:
            message = "An unknown error occurred.";
            break;
        }

        alert("Error getting location: " + message);
      },
      {
        timeout: 100000 // 10 seconds max
      }
    );
    }
}
  </script>

</body>
</html>
