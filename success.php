<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: register.php');
    exit();
}
unset($_SESSION['email']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration Successful</title>
</head>
<body>
    <h2>Registration Successful!</h2>
    <p>Your account has been verified and created successfully.</p>
    <p><a href="login.php">Click here to login</a></p>
</body>
</html>