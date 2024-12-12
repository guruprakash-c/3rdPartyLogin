<?php
session_start();
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Messaging</title>
</head>
<body>
    <h1>Send WhatsApp Message</h1>
    <form action="send_message.php" method="POST">
        <label for="phone_number">Phone Number (with country code):</label>
        <input type="text" id="phone_number" name="phone_number" required><br><br>
        
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="4" cols="50" required></textarea><br><br>
        
        <input type="submit" value="Send Message">
    </form>
    
    <?php if (isset($_SESSION['message'])): ?>
        <p><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>
</body>
</html>