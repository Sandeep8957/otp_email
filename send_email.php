<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Get form data
$from_email = isset($_POST['from_email']) ? filter_var($_POST['from_email'], FILTER_SANITIZE_EMAIL) : '';
$to_email = isset($_POST['to_email']) ? filter_var($_POST['to_email'], FILTER_SANITIZE_EMAIL) : '';
$subject = isset($_POST['subject']) ? filter_var($_POST['subject'], FILTER_SANITIZE_STRING) : '';
$message = isset($_POST['message']) ? filter_var($_POST['message'], FILTER_SANITIZE_STRING) : '';

// Validate inputs
if (!filter_var($from_email, FILTER_VALIDATE_EMAIL)) {
    header('Location: email_form.php?error=Invalid sender email');
    exit();
}

if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
    header('Location: email_form.php?error=Invalid recipient email');
    exit();
}

if (empty($subject) || empty($message)) {
    header('Location: email_form.php?error=Subject and message are required');
    exit();
}

try {
    $mail = new PHPMailer(true);
    
    // SMTP Configuration (using Gmail example)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'isandeep00511@gmail.com'; // Your Gmail
    $mail->Password = 'hgqk sqsm bjxd gwaz';   // App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Email content
    $mail->setFrom($from_email);
    $mail->addAddress($to_email);
    $mail->Subject = $subject;
    $mail->Body = $message;
    
    if ($mail->send()) {
        header('Location: email_form.php?success=1');
    } else {
        header('Location: email_form.php?error=Failed to send email');
    }
    exit();
    
} catch (Exception $e) {
    error_log("Mailer Error: " . $e->getMessage());
    header('Location: email_form.php?error=Failed to send email. Please try again.');
    exit();
}
?>