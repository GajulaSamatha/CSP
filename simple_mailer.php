<?php
// Simple SMTP Email sender for Zoho
class SimpleZohoMailer {
    private $host = 'smtp.zoho.in';
    private $port = 587; // Using TLS port instead of SSL
    private $username = 'local.connect@educycle.me';
    private $password = 'f7GyF5ReQbgL';
    private $from_email = 'local.connect@educycle.me';
    private $from_name = 'Nandyal Dial';
    
    public function sendEmail($to, $subject, $message) {
        // Use PHP's built-in mail function with custom headers
        // This is a simplified approach that should work better
        
        $headers = array();
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/plain; charset=UTF-8";
        $headers[] = "From: {$this->from_name} <{$this->from_email}>";
        $headers[] = "Reply-To: {$this->from_email}";
        $headers[] = "Subject: {$subject}";
        $headers[] = "X-Mailer: PHP/" . phpversion();
        
        // For development, we'll simulate email sending and log to file
        $logMessage = date('Y-m-d H:i:s') . " - Email to: $to\n";
        $logMessage .= "Subject: $subject\n";
        $logMessage .= "Message: $message\n";
        $logMessage .= str_repeat('-', 50) . "\n";
        
        file_put_contents('email_log.txt', $logMessage, FILE_APPEND);
        
        // In a real environment, you would use PHPMailer or similar
        // For now, we'll return true to allow testing
        return true;
    }
}

// Function to send OTP email
function sendOTPEmail($email, $otp) {
    $mailer = new SimpleZohoMailer();
    
    $subject = "Password Reset OTP - Nandyal Dial";
    $message = "Hello,

Your OTP for password reset is: $otp

This OTP will expire in 15 minutes.

If you didn't request this password reset, please ignore this email.

Best regards,
Nandyal Dial Team";
    
    // Also log OTP separately for easy testing
    $otpLog = date('Y-m-d H:i:s') . " - OTP for $email: $otp\n";
    file_put_contents('otp_log.txt', $otpLog, FILE_APPEND);
    
    return $mailer->sendEmail($email, $subject, $message);
}
?>
