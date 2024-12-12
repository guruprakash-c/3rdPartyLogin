<?php
session_start();
require_once 'config.php';

if (isset($_GET['code'])) {
    $token_url = "https://www.linkedin.com/oauth/v2/accessToken";
    $token_data = [
        "grant_type" => "authorization_code",
        "code" => $_GET['code'],
        "client_id" => CLIENT_ID,
        "client_secret" => CLIENT_SECRET,
        "redirect_uri" => REDIRECT_URI,
    ];

    $curl = curl_init($token_url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($token_data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    $token_info = json_decode($response, true);

    if (isset($token_info['access_token'])) {
        // Fetch user profile
        $profile_url = "https://api.linkedin.com/v2/me?projection=(id,localizedFirstName,localizedLastName,profilePicture(displayImage~:playableStreams))";
        $curl = curl_init($profile_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $token_info['access_token'],
            "Content-Type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $profile_response = curl_exec($curl);
        curl_close($curl);

        $profile_info = json_decode($profile_response, true);

        // Fetch user email
        $email_url = "https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))";
        $curl = curl_init($email_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $token_info['access_token'],
            "Content-Type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $email_response = curl_exec($curl);
        curl_close($curl);

        $email_info = json_decode($email_response, true);

        $linkedin_id = $profile_info['id'];
        $full_name = $profile_info['localizedFirstName'] . ' ' . $profile_info['localizedLastName'];
        $email = $email_info['elements'][0]['handle~']['emailAddress'];

        // Get profile picture
        $profile_picture = "";
        if (isset($profile_info['profilePicture']['displayImage~']['elements'])) {
            $pictures = $profile_info['profilePicture']['displayImage~']['elements'];
            $profile_picture = end($pictures)['identifiers'][0]['identifier'];
        }

        // Check if user exists in database
        $stmt = $conn->prepare("SELECT * FROM users WHERE linkedin_id = ?");
        $stmt->bind_param("s", $linkedin_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (linkedin_id, full_name, email, profile_picture) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $linkedin_id, $full_name, $email, $profile_picture);
        } else {
            // Update existing user
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, profile_picture = ? WHERE linkedin_id = ?");
            $stmt->bind_param("ssss", $full_name, $email, $profile_picture, $linkedin_id);
        }
        $stmt->execute();

        $_SESSION['user'] = [
            'linkedin_id' => $linkedin_id,
            'full_name' => $full_name,
            'email' => $email,
            'profile_picture' => $profile_picture
        ];

        header("Location: index.php");
        exit();
    }
}

header("Location: index.php");
exit();