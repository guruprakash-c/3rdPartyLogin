<?php 
 if(!isset($_SESSION)) session_start();

 require_once "google.config.php";
  
 $googleClient = new GoogleMicroService();
 $userDetails = $_SESSION['userData'] = NULL;
 if(!empty($_GET['code'])){
    $code = trim(strip_tags($_GET['code']));
    $userDetails = $googleClient->GetGoogleUserDetails($code);
    $_SESSION['userData'] = $userDetails;
    //print_r($_SESSION);
    header('Location: index.php');
 }
?>