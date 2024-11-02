<?php 
use UserProps as UP; 

require_once "UserDO.php";
require_once 'modules/vendor/autoload.php';

final class GoogleMicroservice{
  public $googleClient = NULL;

  public function __construct(){
    define('GL_CLIENT_ID', ''); 
    define('GL_CLIENT_SECRET', ''); 
    define('GL_REDIRECT_URL', 'http://localhost/3rdPartyLogin/index.google.php'); 

    //Make object of Google API Client for call Google API
    $this->googleClient = new Google_Client();

    //Set the OAuth 2.0 Client ID
    $this->googleClient->setClientId(GL_CLIENT_ID);

    //Set the OAuth 2.0 Client Secret key
    $this->googleClient->setClientSecret(GL_CLIENT_SECRET);

    //Set the OAuth 2.0 Redirect URI
    $this->googleClient->setRedirectUri(GL_REDIRECT_URL);

    //
    $this->googleClient->addScope('email');

    $this->googleClient->addScope('profile');
  }
  public function GetGoogleUserDetails($code){
    $userData = NULL;
    
    $googleLoginBttn = ''; 
    if (isset($code) && !empty($code)) {
      $token = $this->googleClient->fetchAccessTokenWithAuthCode($code);
      $this->googleClient->setAccessToken($token['access_token']);
      
      // get profile info 
      /*$userDetails = Array(
        'email' => '',
        'name' => '',
        'gender' => '',
        'picture' => '' 
      );*/
      
      $google_oauth = new Google_Service_Oauth2($this->googleClient);
      $google_account_info = $google_oauth->userinfo->get();

      if(!empty($google_account_info)){
        $userData = [];
        $userDetails = new UP\UserDO();
        $userDetails->id = uniqid();
        $userDetails->provider = 'Google';
        $userDetails->name = $google_account_info->name;
        $userDetails->email = $google_account_info->email;
        $userDetails->photo = $google_account_info->picture;
        $userDetails->gender = $google_account_info->gender;
        array_push($userData, $userDetails);
      }       
    }
    return $userData;
  }
  public function GetGoogleAuthUrl(){
    return $this->googleClient->createAuthUrl();
  }
  public function __destruct(){
      $this->googleClient = NULL;
   }

}