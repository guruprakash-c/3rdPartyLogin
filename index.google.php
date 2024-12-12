<?php 
 require_once "google.config.php";
  
 $googleClient = new GoogleMicroService();
 $userDetails = NULL;
 if(!empty($_GET['code'])){
    $code = trim(strip_tags($_GET['code']));
    $userDetails = $googleClient->GetGoogleUserDetails($code);
    echo json_encode($userDetails);
 } else { 
  echo "Unable to login with your Google account"; 
}