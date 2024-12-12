<?php 
require_once "microsoft.config.php";

if (isset($_GET['code'])) {
    $msObj = new MicrosoftMicroservices();
    $authCode = trim(strip_tags($_GET['code']));
    $userDetails = $msObj->GetUserDetails($authCode);
    if(!empty($userDetails)){
        echo json_encode($userDetails);
    }
} else { 
  echo "Unable to login with your Microsoft account"; 
}