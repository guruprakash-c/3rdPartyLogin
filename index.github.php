<?php 
 if(!isset($_SESSION)) session_start();

 require_once "github.config.php";  

 $gitHubClient = new GitHubMicroservice();
 $userDetails = $_SESSION['userData'] = NULL;
 if(!empty($_GET['state']) && !empty($_GET['code'])){
    $state = trim(strip_tags($_GET['state']));
    $code = trim(strip_tags($_GET['code']));
    $userDetails = $gitHubClient->GetUserDetails($state, $code);
    $_SESSION['userData'] = $userDetails;
    //print_r($_SESSION);
    header('Location: index.php');
 }