<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader and our App namespace
require_once __DIR__ . '/../vendor/autoload.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Set timezone to ensure consistency
date_default_timezone_set('Asia/Kolkata');

session_start();

// --- Email Sending Function ---
// We define this here to use environment variables securely
function sendOTPEmail($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        //Server settings from .env file
        $mail->isSMTP();
        $mail->Host       = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['MAIL_USERNAME'];
        $mail->Password   = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $_ENV['MAIL_PORT'];

        //Recipients
        $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
        $mail->addAddress($email);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Password Reset OTP';
        $mail->Body    = 'Your One-Time Password (OTP) for password reset is: <b>' . $otp . '</b>. It is valid for 15 minutes.';
        $mail->AltBody = 'Your One-Time Password (OTP) for password reset is: ' . $otp . '. It is valid for 15 minutes.';

        return $mail->send();
    } catch (Exception $e) {
        // Log the detailed error message for debugging, but don't show it to the user.
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

// Use the secure database connection from Db.php
$conn = App\Db::getConnection();

// Set MySQL timezone to match PHP
$conn->query("SET time_zone = '+05:30'");

$step = isset($_GET['step']) ? $_GET['step'] : 1;
$error = '';
$success = '';
$role = isset($_GET['role']) ? $_GET['role'] : 'customers';
$_SESSION['reset_role'] = $role;

// Step 1: Enter email and send OTP
if($step == 1 && isset($_POST['send_otp'])) {
    $email = $_POST['email'];

    // Check if email exists
        // Check if email exists in the selected role table
        if ($role == 'customer') {
            $sql = "SELECT id FROM customers WHERE email = ?";
        } else {
            $sql = "SELECT id FROM providers WHERE email = ?";
        }
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $result = $stmt->fetch();

    if ($result) {
        // Generate 6-digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));

        // Store OTP in database (create table if needed)
        // First check if table exists, if not create it
        $checkTable = "SHOW TABLES LIKE 'password_reset_otp'";
        $tableExists = $conn->query($checkTable);

        if($tableExists->num_rows == 0) {
            $createTable = "CREATE TABLE `password_reset_otp` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `email` varchar(255) NOT NULL,
                `otp` varchar(6) NOT NULL,
                `expiry_time` datetime NOT NULL,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $conn->query($createTable);
        }

        // Use MySQL's DATE_ADD function for consistent timezone handling
        $sql = "INSERT INTO password_reset_otp (email, otp, expiry_time, created_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE), NOW()) ON DUPLICATE KEY UPDATE otp=VALUES(otp), expiry_time=VALUES(expiry_time), created_at=NOW()";
        $stmt = $conn->prepare($sql);

        if(!$stmt) {
            // PDO throws exceptions, so this 'if' is less likely to be hit.
            $error = "Database prepare error.";
        } else {
            try {
                $stmt->execute([$email, $otp]);

                // Try to send email
                try {
                    if(sendOTPEmail($email, $otp)) {
                        $_SESSION['reset_email'] = $email;
                        header("Location: forgotpassword.php?step=2");
                        exit();
                    } else {
                        $error = "Failed to send OTP email. Please check your email address and try again.";
                    }
                } catch (Exception $e) {
                    $error = "Email sending error: " . $e->getMessage();
                }
            } catch (\PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                $error = "A database error occurred.";
            }
        }
    } else {
        $error = "Email address not found.";
    }
}

// Step 2: Verify OTP
if($step == 2 && isset($_POST['verify_otp'])) {
    if(!isset($_SESSION['reset_email'])) {
        header("Location: forgotpassword.php?step=1");
        exit();
    }

    $otp = trim($_POST['otp']);
    $email = $_SESSION['reset_email'];

    $sql = "SELECT otp, expiry_time,
            NOW() as current_db_time,
            (expiry_time > NOW()) as is_valid,
            TIMESTAMPDIFF(MINUTE, NOW(), expiry_time) as minutes_remaining FROM password_reset_otp WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $row = $stmt->fetch();


    if ($row) {
        $dbOTP = trim($row['otp']);
        $inputOTP = trim($otp);

        // Debug logging with more detailed information
        $debugLog = date('Y-m-d H:i:s') . " - OTP Debug for $email:\n";
        $debugLog .= "Input OTP: '$inputOTP' (length: " . strlen($inputOTP) . ")\n";
        $debugLog .= "DB OTP: '$dbOTP' (length: " . strlen($dbOTP) . ")\n";
        $debugLog .= "OTP Match: " . ($inputOTP === $dbOTP ? 'YES' : 'NO') . "\n";
        $debugLog .= "Expiry time: " . $row['expiry_time'] . "\n";
        $debugLog .= "Current time: " . $row['current_db_time'] . "\n";
        $debugLog .= "Is valid (not expired): " . ($row['is_valid'] ? 'YES' : 'NO') . "\n";
        $debugLog .= "Minutes remaining: " . $row['minutes_remaining'] . "\n\n";
        file_put_contents('otp_debug.txt', $debugLog, FILE_APPEND);

        // Check if OTP is expired first
        if (!$row['is_valid']) {
            $error = "OTP has expired. Please request a new one. (Expired " . abs($row['minutes_remaining']) . " minutes ago)";
        } elseif($inputOTP === $dbOTP) {
            $_SESSION['otp_verified'] = true;
            header("Location: forgotpassword.php?step=3");
            exit();
        } else {
            $error = "Invalid OTP. Please check and try again.";
        }
    } else {
        $error = "OTP expired or invalid. Please request a new one.";
    }
}

// Resend OTP
if($step == 2 && isset($_POST['resend_otp'])) {
    if(!isset($_SESSION['reset_email'])) {
        header("Location: forgotpassword.php?step=1");
        exit();
    }

    $email = $_SESSION['reset_email'];

    // Generate new OTP
    $otp = sprintf("%06d", mt_rand(1, 999999));

    // Update OTP in database using MySQL time functions
    $sql = "UPDATE password_reset_otp SET otp = ?, expiry_time = DATE_ADD(NOW(), INTERVAL 15 MINUTE), created_at = NOW() WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if($stmt->execute([$otp, $email])) {
        if(sendOTPEmail($email, $otp)) {
            $success = "New OTP has been sent to your email.";
        } else {
            $error = "Failed to send new OTP. Please try again.";
        }
    } else {
        $error = "Database error. Please try again.";
    }
}

// Step 3: Reset password
if($step == 3 && isset($_POST['reset_password'])) {
    if(!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
        header("Location: forgotpassword.php?step=1");
        exit();
    }

    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];

    if($newPassword === $confirmPassword) {
        if(strlen($newPassword) >= 6) {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
           if ($role == 'customer') {
                $sql = "UPDATE customers SET password = ? WHERE email = ?";
            } else {
                $sql = "UPDATE providers SET password = ? WHERE email = ?";
            }

            // Update password in database
            $stmt = $conn->prepare($sql);

            if($stmt->execute([$hashedPassword, $email])) {
                // Delete used OTP
                $sql = "DELETE FROM password_reset_otp WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute();

                // Clear session
                unset($_SESSION['reset_email']);
                unset($_SESSION['otp_verified']);
                
                $success = "Password reset successful! You can now login with your new password.";
                $step = 4; // Success step
            } else {
                $error = "Failed to update password. Please try again.";
            }
        } else {
            $error = "Password must be at least 6 characters long.";
        }
    } else {
        $error = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - Nandyal Dial</title>
    <style>
        /* Page Background */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            padding: 20px;
        }

        /* Container */
        .forgot-container {
            background: #fff;
            padding: 35px 30px;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Heading */
        .forgot-container h2 {
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .subtitle {
            font-size: 14px;
            color: #777;
            margin-bottom: 25px;
        }

        /* Input Fields */
        input[type="email"],
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 2px solid #4caf50;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        input[type="email"]:focus,
        input[type="text"]:focus,
        input[type="password"]:focus {
            box-shadow: 0 0 6px rgba(76, 175, 80, 0.5);
            border-color: #45a049;
        }

        /* OTP Input Boxes */
        .otp-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 8px;
        }

        .otp-box {
            width: 45px;
            height: 45px;
            border: 2px solid #4caf50;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .otp-box:focus {
            box-shadow: 0 0 6px rgba(76, 175, 80, 0.5);
            border-color: #45a049;
        }

        /* Password Container with Eye Icon */
        .password-container {
            position: relative;
            margin-bottom: 15px;
        }

        .password-container input {
            width: 100%;
            padding-right: 45px;
            margin-bottom: 0;
        }

        .eye-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            font-size: 18px;
            user-select: none;
        }

        .eye-icon:hover {
            color: #2575fc;
        }

        /* Submit Button */
        input[type="submit"] {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: opacity 0.3s ease;
            margin-bottom: 15px;
        }

        input[type="submit"]:hover {
            opacity: 0.9;
        }

        /* Links */
        p {
            margin-top: 15px;
            font-size: 14px;
        }

        p a {
            color: #2575fc;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        /* Error Message */
        .error-message {
            color: #d32f2f;
            background-color: #fde8e8;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        /* Success Message */
        .success-message {
            color: #2e7d32;
            background-color: #e8f5e8;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        /* Steps indicator */
        .steps {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #ddd;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            font-weight: bold;
        }

        .step.active {
            background-color: #2575fc;
            color: white;
        }

        .step.completed {
            background-color: #4caf50;
            color: white;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .forgot-container {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <!-- Steps indicator -->
        <div class="steps">
            <div class="step <?php

echo $step >= 1 ? ($step > 1 ? 'completed' : 'active') : ''; ?>">1</div>
            <div class="step <?php

echo $step >= 2 ? ($step > 2 ? 'completed' : 'active') : ''; ?>">2</div>
            <div class="step <?php

echo $step >= 3 ? ($step > 3 ? 'completed' : 'active') : ''; ?>">3</div>
        </div>

        <?php

if($step == 1): ?>
            <!-- Step 1: Enter Email -->
            <h2>Forgot Password</h2>
            <div class="subtitle">Enter your email address to receive an OTP</div>

            <?php

if($error): ?>
                <div class="error-message"><?php

echo htmlspecialchars($error); ?></div>
            <?php

endif; ?>

            <form method="POST">
                <input type="email" name="email" placeholder="Enter your Email" required>
                <input type="submit" name="send_otp" value="Send OTP">
            </form>

        <?php

elseif($step == 2): ?>
            <!-- Step 2: Enter OTP -->
            <h2>Verify OTP</h2>
            <div class="subtitle">Enter the 6-digit OTP sent to your email</div>

            <?php

if($error): ?>
                <div class="error-message"><?php

echo htmlspecialchars($error); ?></div>
            <?php

endif; ?>

            <?php

if(isset($success)): ?>
                <div class="success-message"><?php

echo htmlspecialchars($success); ?></div>
            <?php

endif; ?>

            <form method="POST" id="otpForm">
                <div class="otp-container">
                    <input type="text" class="otp-box" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-box" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-box" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-box" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-box" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="otp-box" maxlength="1" pattern="[0-9]" required>
                </div>
                <input type="hidden" name="otp" id="otpValue">
                <input type="submit" name="verify_otp" value="Verify OTP">
            </form>

            <form method="POST" style="margin-top: 10px;">
                <input type="submit" name="resend_otp" value="Resend OTP" style="background: #4caf50; font-size: 14px; padding: 10px;">
            </form>

            <p><a href="forgotpassword.php?step=1">Back to email entry</a></p>

        <?php

elseif($step == 3): ?>
            <!-- Step 3: Reset Password -->
            <h2>Reset Password</h2>
            <div class="subtitle">Enter your new password</div>

            <?php

if($error): ?>
                <div class="error-message"><?php

echo htmlspecialchars($error); ?></div>
            <?php

endif; ?>

            <form method="POST">
                <div class="password-container">
                    <input type="password" name="new_password" id="newPassword" placeholder="New Password" required>
                    <span class="eye-icon" onclick="togglePassword('newPassword', this)">👁️</span>
                </div>
                <div class="password-container">
                    <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm New Password" required>
                    <span class="eye-icon" onclick="togglePassword('confirmPassword', this)">👁️</span>
                </div>
                <input type="submit" name="reset_password" value="Reset Password">
            </form>

        <?php

elseif($step == 4): ?>
            <!-- Step 4: Success -->
            <h2>Password Reset Successful!</h2>

            <?php

if($success): ?>
                <div class="success-message"><?php

echo htmlspecialchars($success); ?></div>
            <?php

endif; ?>

            <p>
                <?php

if ($_SESSION['reset_role'] == 'customers'): ?>
                    <a href="new_login.php">Go to Customer Login</a>
                <?php

elseif ($_SESSION['reset_role'] == 'providers'): ?>
                    <a href="new_provider_login.php">Go to Provider Login</a>
                <?php

else: ?>
                    <a href="index.php">Go to Login</a>
                <?php

endif; ?>
            </p>
        <?php

endif; ?>
    </div>

    <script>
        // OTP Box functionality
        document.addEventListener('DOMContentLoaded', function() {
            const otpBoxes = document.querySelectorAll('.otp-box');
            const otpValue = document.getElementById('otpValue');

            otpBoxes.forEach((box, index) => {
                box.addEventListener('input', function(e) {
                    // Only allow numbers
                    this.value = this.value.replace(/[^0-9]/g, '');

                    // Move to next box if current box is filled
                    if (this.value.length === 1 && index < otpBoxes.length - 1) {
                        otpBoxes[index + 1].focus();
                    }

                    // Update hidden OTP value
                    updateOTPValue();
                });

                box.addEventListener('keydown', function(e) {
                    // Move to previous box on backspace
                    if (e.key === 'Backspace' && this.value === '' && index > 0) {
                        otpBoxes[index - 1].focus();
                    }
                });

                box.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');

                    // Fill boxes with pasted data
                    for (let i = 0; i < Math.min(pastedData.length, otpBoxes.length - index); i++) {
                        if (index + i < otpBoxes.length) {
                            otpBoxes[index + i].value = pastedData[i];
                        }
                    }

                    // Focus on next empty box or last box
                    const nextEmptyIndex = Math.min(index + pastedData.length, otpBoxes.length - 1);
                    otpBoxes[nextEmptyIndex].focus();

                    updateOTPValue();
                });
            });

            function updateOTPValue() {
                let otp = '';
                otpBoxes.forEach(box => {
                    otp += box.value;
                });
                if (otpValue) {
                    otpValue.value = otp;
                }
            }
        });

        // Password visibility toggle
        function togglePassword(fieldId, eyeIcon) {
            const passwordField = document.getElementById(fieldId);
            const isPassword = passwordField.type === 'password';

            passwordField.type = isPassword ? 'text' : 'password';
            eyeIcon.textContent = isPassword ? '🙈' : '👁️';
        }
    </script>
</body>
</html>
