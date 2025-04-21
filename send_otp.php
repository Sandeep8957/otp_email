<?php
session_start();
include 'config.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method!";
    header('Location: register.php');
    exit;
}

// Validate input
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Basic validation
if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
    $_SESSION['error'] = "All fields are required!";
    header('Location: register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format!";
    header('Location: register.php');
    exit;
}

if ($password !== $confirm_password) {
    $_SESSION['error'] = "Passwords do not match!";
    header('Location: register.php');
    exit;
}

if (strlen($password) < 8) {
    $_SESSION['error'] = "Password must be at least 8 characters long!";
    header('Location: register.php');
    exit;
}

// Generate OTP
$otp = rand(100000, 999999);
$otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $userExists = $stmt->fetch();

    if ($userExists) {
        // Update existing user with new OTP
        $stmt = $pdo->prepare("UPDATE users SET otp = ?, otp_expiry = ? WHERE email = ?");
        $stmt->execute([$otp, $otp_expiry, $email]);
        $_SESSION['message'] = "This email is already registered. We've sent a new OTP.";
    } else {
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, otp, otp_expiry) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password, $otp, $otp_expiry]);
    }

    // Send OTP email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'isandeep00511@gmail.com';
    $mail->Password = 'hgqk sqsm bjxd gwaz';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->setFrom('your_email@gmail.com', 'OTP Verification');
    $mail->addAddress($email, $name);
    $mail->Subject = 'Your OTP for Registration';
    $mail->Body = "Hello $name,\n\nYour OTP is: $otp\n\nValid for 10 minutes.";
    
    $mail->send();
    
    $_SESSION['email'] = $email;
    header('Location: verify_otp.php');
    exit();
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error'] = "A database error occurred. Please try again.";
    header('Location: register.php');
    exit;
} catch (Exception $e) {
    error_log("Mailer error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to send OTP. Please try again.";
    header('Location: register.php');
    exit;
}
?>