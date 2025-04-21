<?php
session_start();
include 'config.php';

// अगर लॉगिन नहीं है तो लॉगिन पेज पर भेजें
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// यूजर डिटेल्स डेटाबेस से फिर से लोड करें (ऑप्शनल)
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .header { background: #333; color: #fff; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .welcome { font-size: 24px; margin-bottom: 20px; }
        .user-info { margin-bottom: 20px; }
        .logout-btn { background: #d9534f; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
        .logout-btn:hover { background: #c9302c; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Welcome to Dashboard</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
    
    <div class="container">
        <div class="welcome">Hello, <?php echo htmlspecialchars($user['name']); ?>!</div>
        
        <div class="user-info">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Registered on:</strong> <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
        </div>
        
        <h3>Your Account Details</h3>
        <p>This is your secure dashboard area.</p>
    </div>
</body>
</html>