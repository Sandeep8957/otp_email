<?php
session_start();
include 'config.php';

// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Brute force protection - limit login attempts
$max_attempts = 5;
$lockout_time = 300; // 5 minutes in seconds

if(isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $max_attempts) {
    if(!isset($_SESSION['last_attempt_time']) || (time() - $_SESSION['last_attempt_time']) < $lockout_time) {
        $remaining_time = $lockout_time - (time() - $_SESSION['last_attempt_time']);
        $error = "Too many failed attempts. Please try again in ".ceil($remaining_time/60)." minutes.";
    } else {
        // Reset attempts after lockout period
        unset($_SESSION['login_attempts']);
        unset($_SESSION['last_attempt_time']);
    }
}

// Process login
if($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($error)) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    try {
        // Get user with prepared statement
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_verified = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])) {
            // Successful login
            unset($_SESSION['login_attempts']);
            unset($_SESSION['last_attempt_time']);
            
            // Update last login
            $update = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $update->execute([$user['id']]);
            
            // Regenerate session ID to prevent fixation
            session_regenerate_id(true);
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = htmlspecialchars($user['name']);
            $_SESSION['user_email'] = htmlspecialchars($user['email']);
            $_SESSION['last_activity'] = time();
            
            header('Location: dashboard.php');
            exit;
        } else {
            // Failed login
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            $_SESSION['last_attempt_time'] = time();
            $error = "Invalid email or password! Attempt ".$_SESSION['login_attempts']." of $max_attempts";
        }
    } catch(PDOException $e) {
        error_log("Login error: ".$e->getMessage());
        $error = "A system error occurred. Please try again later.";
    }
}

// Display error from session if exists
$error = $error ?? ($_SESSION['error'] ?? null);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .container { max-width: 400px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #337ab7; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #286090; }
        .error { color: #d9534f; text-align: center; margin-bottom: 15px; }
        .register-link { text-align: center; margin-top: 15px; }
        .register-link a { color: #337ab7; text-decoration: none; }
        .register-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        
        <?php if(isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        
        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>