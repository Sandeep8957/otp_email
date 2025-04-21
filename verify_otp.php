<?php
session_start();
include 'config.php';

if (!isset($_SESSION['email'])) {
    header('Location: register.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = $_POST['otp'];
    $email = $_SESSION['email'];
    
    // डेटाबेस से OTP चेक करें
    $stmt = $pdo->prepare("SELECT otp, otp_expiry FROM users WHERE email = ? AND is_verified = 0");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && $user['otp'] == $otp && strtotime($user['otp_expiry']) > time()) {
        // OTP सही है, यूजर को वेरिफाई करें
        $update = $pdo->prepare("UPDATE users SET is_verified = 1, otp = NULL WHERE email = ?");
        $update->execute([$email]);
        
        unset($_SESSION['email']);
        header('Location: success.php');
        exit();
    } else {
        $error = "Invalid OTP or OTP expired";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 400px; margin: 50px auto; }
        .form-group { margin-bottom: 15px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; cursor: pointer; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verify OTP</h2>
        <p>We've sent an OTP to your email. Please check and enter it below:</p>
        
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <input type="text" name="otp" placeholder="Enter 6-digit OTP" required>
            </div>
            <button type="submit">Verify OTP</button>
        </form>
    </div>
</body>
</html>