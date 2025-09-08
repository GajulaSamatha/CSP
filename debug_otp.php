<?php
// Debug OTP issues
// Set timezone to ensure consistency
date_default_timezone_set('Asia/Kolkata');

session_start();

$conn = new mysqli("localhost", "root", "1234", "nandyal_dial");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set MySQL timezone to match PHP
$conn->query("SET time_zone = '+05:30'");

echo "<h2>OTP Debug Information</h2>";

// Check session data
echo "<h3>Session Data:</h3>";
if (isset($_SESSION['reset_email'])) {
    echo "Reset email in session: " . $_SESSION['reset_email'] . "<br>";
} else {
    echo "❌ No reset email in session<br>";
}

if (isset($_SESSION['otp_verified'])) {
    echo "OTP verified: " . ($_SESSION['otp_verified'] ? 'Yes' : 'No') . "<br>";
} else {
    echo "OTP verification status: Not set<br>";
}

// Check OTP records in database
echo "<h3>Current OTP Records:</h3>";
$sql = "SELECT email, otp, expiry_time, created_at, 
        (expiry_time > NOW()) as is_valid,
        TIMESTAMPDIFF(MINUTE, NOW(), expiry_time) as minutes_remaining
        FROM password_reset_otp 
        ORDER BY created_at DESC 
        LIMIT 5";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Email</th><th>OTP</th><th>Expiry Time</th><th>Created</th><th>Valid</th><th>Minutes Left</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        $validStyle = $row['is_valid'] ? 'color: green;' : 'color: red;';
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td style='font-weight: bold; font-size: 16px;'>" . $row['otp'] . "</td>";
        echo "<td>" . $row['expiry_time'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "<td style='$validStyle'>" . ($row['is_valid'] ? 'Yes' : 'No') . "</td>";
        echo "<td style='$validStyle'>" . $row['minutes_remaining'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ No OTP records found<br>";
}

// Check recent OTP log
echo "<h3>Recent OTP Log:</h3>";
if (file_exists('otp_log.txt')) {
    $logContent = file_get_contents('otp_log.txt');
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -5);
    foreach ($recentLines as $line) {
        if (!empty(trim($line))) {
            echo htmlspecialchars($line) . "<br>";
        }
    }
} else {
    echo "No OTP log file found.<br>";
}

// Test OTP validation with manual input
if (isset($_POST['test_otp'])) {
    $testEmail = $_POST['test_email'];
    $testOTP = trim($_POST['test_otp_value']);
    
    echo "<h3>Testing OTP Validation:</h3>";
    echo "Email: $testEmail<br>";
    echo "OTP to test: '$testOTP'<br>";
    
    $sql = "SELECT otp, expiry_time, 
            NOW() as current_db_time,
            (expiry_time > NOW()) as is_valid,
            TIMESTAMPDIFF(MINUTE, NOW(), expiry_time) as minutes_remaining
            FROM password_reset_otp 
            WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $testEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $dbOTP = trim($row['otp']);
        echo "OTP in database: '$dbOTP'<br>";
        echo "Current time: " . $row['current_db_time'] . "<br>";
        echo "Expiry time: " . $row['expiry_time'] . "<br>";
        echo "Is valid (not expired): " . ($row['is_valid'] ? 'YES' : 'NO') . "<br>";
        echo "Minutes remaining: " . $row['minutes_remaining'] . "<br>";
        echo "OTP comparison: " . ($testOTP === $dbOTP ? '✅ MATCH' : '❌ NO MATCH') . "<br>";
        echo "String lengths - Input: " . strlen($testOTP) . ", DB: " . strlen($dbOTP) . "<br>";
        
        if (!$row['is_valid']) {
            echo "<strong style='color: red;'>❌ OTP is EXPIRED by " . abs($row['minutes_remaining']) . " minutes</strong><br>";
        } elseif ($testOTP === $dbOTP) {
            echo "<strong style='color: green;'>✅ OTP is VALID and MATCHES</strong><br>";
        } else {
            echo "<strong style='color: orange;'>⚠️ OTP is VALID but does NOT MATCH</strong><br>";
        }
    } else {
        echo "❌ No valid OTP found for this email<br>";
    }
}

$conn->close();
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { margin: 10px 0; }
    th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
    th { background-color: #f2f2f2; }
</style>

<h3>Test OTP Validation:</h3>
<form method="POST">
    <input type="email" name="test_email" placeholder="Email address" required style="margin: 5px; padding: 8px;">
    <input type="text" name="test_otp_value" placeholder="OTP to test" required style="margin: 5px; padding: 8px;">
    <input type="submit" name="test_otp" value="Test OTP" style="margin: 5px; padding: 8px;">
</form>

<br><br>
<a href="forgotpassword.php">Go to Forgot Password</a> | 
<a href="new_login.php">Go to Login</a>
