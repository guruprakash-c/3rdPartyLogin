<?php 
 require_once "github.config.php";  

 $gitHubClient = new GitHubMicroservice();
 $userDetails = NULL;
 if(!empty($_GET['state']) && !empty($_GET['code'])){
    $state = trim(strip_tags($_GET['state']));
    $code = trim(strip_tags($_GET['code']));
    $userDetails = $gitHubClient->GetUserDetails($state, $code);
    echo json_encode($userDetails);
 } else { 
  echo "Unable to login with your GitHub account"; 
}