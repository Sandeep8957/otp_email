<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Sending Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 150px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .success {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>Send Email</h2>
    
    <?php if(isset($_GET['success'])): ?>
        <div class="success">Email sent successfully!</div>
    <?php endif; ?>
    
    <?php if(isset($_GET['error'])): ?>
        <div class="error">Error: <?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    
    <form action="send_email.php" method="post">
        <div class="form-group">
            <label for="from_email">Your Email:</label>
            <input type="email" id="from_email" name="from_email" required>
        </div>
        
        <div class="form-group">
            <label for="to_email">Recipient Email:</label>
            <input type="email" id="to_email" name="to_email" required>
        </div>
        
        <div class="form-group">
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>
        </div>
        
        <div class="form-group">
            <label for="message">Message:</label>
            <textarea id="message" name="message" required></textarea>
        </div>
        
        <button type="submit">Send Email</button>
    </form>
</body>
</html>