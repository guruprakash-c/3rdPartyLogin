<?php 
require_once "linkedin.config.php";

if (isset($_GET['code'])) {
    $linObj = new LinkedInMicroservice();
    $userDetails = $linObj->GetUserDetails($_GET['code']);
    if(!empty($userDetails)){
        echo json_encode($userDetails);
    }else{
        echo "Unable to login with your LinkedIn account, please check have you verified your LinkedIn account."; 
    }
} else { 
  echo "Unable to login with your LinkedIn account"; 
}