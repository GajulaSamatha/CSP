<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = "localhost";
$dbUser = "root";
$dbPass = "1234";
$dbName = "nandyal_dial";

$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in as customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: new_login.php");
    exit();
}

// Get customer data
$customer_id = $_SESSION['user_id'];
$sql = "SELECT * FROM customers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Customer not found");
}

$customer = $result->fetch_assoc();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle profile update
    if (isset($_POST['update_profile'])) {
        $firstName = $conn->real_escape_string($_POST['first_name'] ?? '');
        $lastName = $conn->real_escape_string($_POST['last_name'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        $phone = $conn->real_escape_string($_POST['phoneNumber'] ?? '');
        $location = $conn->real_escape_string($_POST['location'] ?? '');
        $lat = $conn->real_escape_string($_POST['lat'] ?? '');
        $lon = $conn->real_escape_string($_POST['lon'] ?? '');

        // Update query
        $update_sql = "UPDATE customers SET 
                      first_name = ?, 
                      last_name = ?, 
                      email = ?, 
                      phoneNumber = ?, 
                      location = ?, 
                      lat = ?, 
                      lon = ? 
                      WHERE id = ?";
        
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssssssi", $firstName, $lastName, $email, $phone, $location, $lat, $lon, $customer_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['success_message'] = "Profile updated successfully!";
            $_SESSION['user_name'] = $firstName . " " . $lastName;
        } else {
            $_SESSION['error_message'] = "Error updating profile: " . $update_stmt->error;
        }
        
        $update_stmt->close();
        header("Location: customer_profile.php");
        exit();
    }
    
    // Handle password change
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Verify current password
        if (!($current_password === $customer['password'])) {
            $_SESSION['error_message'] = "Current password is incorrect";
        } elseif ($new_password !== $confirm_password) {
            $_SESSION['error_message'] = "New passwords do not match";
        } else {
            // Update password
            $hashed_password = $new_password;
            $update_sql = "UPDATE customers SET password = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $hashed_password, $customer_id);
            
            if ($update_stmt->execute()) {
                $_SESSION['success_message'] = "Password changed successfully!";
            } else {
                $_SESSION['error_message'] = "Error changing password: " . $update_stmt->error;
            }
            
            $update_stmt->close();
        }
        
        header("Location: customer_profile.php");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile - LocalConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="google" content="notranslate">
    <style>
        :root {
            --primary: #4A00E0;
            --secondary: #8E2DE2;
            --accent: #3498db;
            --text-dark: #333;
            --text-light: #ffffff;
            --bg-light: #f9f9f9;
            --border-color: #e0e0e0;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            margin: 0;
            padding-top: 80px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .profile-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .profile-name {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-email {
            color: #666;
            margin-bottom: 15px;
        }

        .profile-status {
            display: inline-block;
            padding: 5px 15px;
            background-color: #4CAF50;
            color: white;
            border-radius: 20px;
            font-size: 14px;
        }

        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title i {
            color: var(--accent);
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 16px;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        .form-control[disabled] {
            background-color: #f5f5f5;
            cursor: not-allowed;
            border-color: #e0e0e0;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #3a00b3, #6e1dc2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn-edit {
            background: transparent;
            color: var(--accent);
            border: 1px solid var(--accent);
            padding: 8px 16px;
            font-size: 14px;
        }

        .btn-edit:hover {
            background: rgba(52, 152, 219, 0.1);
        }

        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .profile-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .password-form {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--accent);
        }
        
        .password-input-container {
            position: relative;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .profile-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php 
    // Check if header exists and include it
    $header_path = __DIR__ . '/new_header.php';
    if (file_exists($header_path)) {
        include($header_path);
    } else {
        echo '<header><h1>LocalConnect</h1></header>';
    }
    ?>

    <div class="container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="profile-header">
            <div class="profile-avatar">
                <?= strtoupper(substr($customer['first_name'], 0, 1)) . strtoupper(substr($customer['last_name'], 0, 1)) ?>
            </div>
            <h1 class="profile-name"><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></h1>
            <p class="profile-email"><?= htmlspecialchars($customer['email']) ?></p>
            <span class="profile-status">Active</span>
        </div>

        <div class="profile-card">
            <h2 class="card-title">
                <span><i class="fas fa-user-circle"></i> Personal Information</span>
            </h2>
            
            <form id="profileForm" action="customer_profile.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" 
                               value="<?= htmlspecialchars($customer['first_name']) ?>" disabled required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" 
                               value="<?= htmlspecialchars($customer['last_name']) ?>" disabled required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?= htmlspecialchars($customer['email']) ?>" disabled required>
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">Phone Number</label>
                        <input type="tel" id="phoneNumber" name="phoneNumber" class="form-control" 
                               value="<?= htmlspecialchars($customer['phoneNumber']) ?>" disabled required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-control" 
                           value="<?= htmlspecialchars($customer['location']) ?>" disabled required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="lat">Latitude</label>
                        <input type="text" id="lat" name="lat" class="form-control" 
                               value="<?= htmlspecialchars($customer['lat']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="lon">Longitude</label>
                        <input type="text" id="lon" name="lon" class="form-control" 
                               value="<?= htmlspecialchars($customer['lon']) ?>" readonly>
                    </div>
                </div>

                <div class="profile-actions">
                    <button type="button" id="editProfileBtn" class="btn btn-edit">Edit Profile</button>
                    <button type="submit" id="saveProfileBtn" name="update_profile" class="btn btn-primary" style="display: none;">Save Changes</button>
                    <button type="button" id="cancelEditBtn" class="btn btn-edit" style="display: none;">Cancel</button>
                    <button type="button" id="showPasswordForm" class="btn btn-edit">Change Password</button>
                </div>
            </form>

            <!-- Separate form for password change -->
            <form id="passwordForm" action="customer_profile.php" method="POST" style="display: none;">
                <h2 class="card-title">
                    <span><i class="fas fa-lock"></i> Change Password</span>
                </h2>
                
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="password-input-container">
                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                        <i class="fas fa-eye password-toggle" onclick="togglePassword('current_password')"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="password-input-container">
                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                        <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password')"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="password-input-container">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="change_password" class="btn btn-primary">Update Password</button>
                    <button type="button" id="hidePasswordForm" class="btn btn-edit">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <?php include "footer.html"; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Edit/Save Profile Toggle
            const editProfileBtn = document.getElementById('editProfileBtn');
            const saveProfileBtn = document.getElementById('saveProfileBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const formInputs = document.querySelectorAll('#profileForm .form-control:not([readonly])');
            
            editProfileBtn.addEventListener('click', function() {
                // Enable all form inputs
                formInputs.forEach(input => {
                    input.disabled = false;
                });
                
                // Show save/cancel buttons, hide edit button
                editProfileBtn.style.display = 'none';
                saveProfileBtn.style.display = 'inline-block';
                cancelEditBtn.style.display = 'inline-block';
            });
            
            cancelEditBtn.addEventListener('click', function() {
                // Disable all form inputs
                formInputs.forEach(input => {
                    input.disabled = true;
                });
                
                // Reset form values to original
                document.getElementById('first_name').value = '<?= htmlspecialchars($customer['first_name']) ?>';
                document.getElementById('last_name').value = '<?= htmlspecialchars($customer['last_name']) ?>';
                document.getElementById('email').value = '<?= htmlspecialchars($customer['email']) ?>';
                document.getElementById('phoneNumber').value = '<?= htmlspecialchars($customer['phoneNumber']) ?>';
                document.getElementById('location').value = '<?= htmlspecialchars($customer['location']) ?>';
                
                // Show edit button, hide save/cancel buttons
                editProfileBtn.style.display = 'inline-block';
                saveProfileBtn.style.display = 'none';
                cancelEditBtn.style.display = 'none';
            });

            // Password form toggle
            const showPasswordForm = document.getElementById('showPasswordForm');
            const passwordForm = document.getElementById('passwordForm');
            const hidePasswordForm = document.getElementById('hidePasswordForm');
            
            showPasswordForm.addEventListener('click', function() {
                passwordForm.style.display = 'block';
                this.style.display = 'none';
            });
            
            hidePasswordForm.addEventListener('click', function() {
                passwordForm.style.display = 'none';
                showPasswordForm.style.display = 'inline-block';
                // Reset password form
                passwordForm.reset();
            });

            // Auto-fill location using browser geolocation
            const locationInput = document.getElementById('location');
            const latInput = document.getElementById('lat');
            const lonInput = document.getElementById('lon');
            
            if ((!latInput.value || !lonInput.value) && navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        latInput.value = position.coords.latitude.toFixed(6);
                        lonInput.value = position.coords.longitude.toFixed(6);
                        
                        if (!locationInput.value) {
                            locationInput.value = "Near me (" + latInput.value + ", " + lonInput.value + ")";
                        }
                    },
                    function(error) {
                        console.error("Error getting location: ", error);
                    }
                );
            }
            
            // Allow user to update location by clicking on the location field
            locationInput.addEventListener('click', function() {
                if (!locationInput.disabled && confirm("Would you like to update your location using your current position?")) {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                latInput.value = position.coords.latitude.toFixed(6);
                                lonInput.value = position.coords.longitude.toFixed(6);
                                locationInput.value = "Near me (" + latInput.value + ", " + lonInput.value + ")";
                            },
                            function(error) {
                                alert("Error getting location: " + error.message);
                            }
                        );
                    } else {
                        alert("Geolocation is not supported by your browser.");
                    }
                }
            });
        });

        // Password visibility toggle
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>