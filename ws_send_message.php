<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone_number = $_POST['phone_number'];
    $message = $_POST['message'];

    $url = "https://graph.facebook.com/v13.0/" . WHATSAPP_PHONE_NUMBER_ID . "/messages";
    
    $data = [
        "messaging_product" => "whatsapp",
        "to" => $phone_number,
        "type" => "text",
        "text" => [
            "body" => $message
        ]
    ];

    $headers = [
        "Authorization: Bearer " . WHATSAPP_TOKEN,
        "Content-Type: application/json"
    ];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    curl_close($curl);

    if ($http_code == 200) {
        $status = 'sent';
        $_SESSION['message'] = "Message sent successfully!";
    } else {
        $status = 'failed';
        $_SESSION['message'] = "Failed to send message. Error: " . $response;
    }

    // Log the message in the database
    $stmt = $conn->prepare("INSERT INTO message_logs (phone_number, message, status) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $phone_number, $message, $status);
    $stmt->execute();

    header("Location: index.php");
    exit();
}