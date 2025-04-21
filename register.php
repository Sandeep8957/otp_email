<?php
session_start();
include 'config.php';

// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Error display from session
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; }
        .container { max-width: 400px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #45a049; }
        .error { color: #d9534f; text-align: center; margin-bottom: 15px; }
        .login-link { text-align: center; margin-top: 15px; }
        .login-link a { color: #337ab7; text-decoration: none; }
        .login-link a:hover { text-decoration: underline; }
        .password-strength { margin-top: 5px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        
        <?php if($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form action="send_otp.php" method="post" onsubmit="return validatePassword()">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required minlength="3" maxlength="100">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required minlength="8">
                <div class="password-strength" id="password-strength"></div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <div id="password-match"></div>
            </div>
            <button type="submit">Register & Send OTP</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthText = document.getElementById('password-strength');
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            const strengthLabels = ['Very Weak', 'Weak', 'Moderate', 'Strong', 'Very Strong'];
            const colors = ['#d9534f', '#f0ad4e', '#5bc0de', '#5cb85c', '#5cb85c'];
            
            strengthText.textContent = `Strength: ${strengthLabels[strength]}`;
            strengthText.style.color = colors[strength];
        });
        
        // Password match checker
        document.getElementById('confirm_password').addEventListener('input', function() {
            const matchText = document.getElementById('password-match');
            if (this.value !== document.getElementById('password').value) {
                matchText.textContent = 'Passwords do not match!';
                matchText.style.color = '#d9534f';
            } else {
                matchText.textContent = 'Passwords match!';
                matchText.style.color = '#5cb85c';
            }
        });
        
        // Form validation
        function validatePassword() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            
            if (password !== confirm) {
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 8) {
                alert('Password must be at least 8 characters long!');
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>