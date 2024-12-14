<?php
use UserProps as UP;

require_once "UserDO.php";


final class LinkedInMicroservice{
   private $clientID; 
   private $clientSecret; 
   private $redirectUri; 
   private $loginUri; 

   function __construct(){
      define('LI_CLIENT_ID', ''); 
      define('LI_CLIENT_SECRET', ''); 
      $scope1 = 'r_liteprofile r_emailaddress';
      $scope2 = 'openid profile email';
      define('LI_CLIENT_SCOPES', urlencode($scope2)); 
      define('LI_REDIRECT_URL', 'http://localhost/3rdPartyLogin/index.linkedin.php');
      define('LI_LOGIN_URL', 'https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=' . LI_CLIENT_ID . '&redirect_uri=' . urlencode(LI_REDIRECT_URL) . '&scope='.LI_CLIENT_SCOPES); 

      $this->clientID = LI_CLIENT_ID;
      $this->clientSecret = LI_CLIENT_SECRET;
      $this->redirectUri = LI_REDIRECT_URL;
      $this->loginUri = LI_LOGIN_URL;
   }
   public function LinkedInAuthUrl(){
      return $this->loginUri;
   }

   public function GetUserDetails($code){
      $userDetails = [];

      if(!empty($code)){
          $token_url = "https://www.linkedin.com/oauth/v2/accessToken";
          $token_data = [
              "grant_type" => "authorization_code",
              "code" => $code,
              "client_id" => $this->clientID,
              "client_secret" => $this->clientSecret,
              "redirect_uri" => $this->redirectUri,
          ];

          $curl = curl_init($token_url);
          curl_setopt($curl, CURLOPT_POST, true);
          curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($token_data));
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          $response = curl_exec($curl);
          curl_close($curl);
          $token_info = json_decode($response, true);

          if (isset($token_info['access_token'])) {
              
              $email_url = "https://api.linkedin.com/v2/userinfo";
              $curl = curl_init($email_url);
              curl_setopt($curl, CURLOPT_HTTPHEADER, [
                  "Authorization: Bearer " . $token_info['access_token'],
                  "Content-Type: application/json"
              ]);
              curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
              $email_response = curl_exec($curl);
              curl_close($curl);

              $email_info = json_decode($email_response, true);
              
              $linkedin_id = $email_info['sub'];
              $full_name = $email_info['name'] ?? $email_info['given_name'];
              $email = $email_info['email'];
              $profile_picture = $email_info['picture'];
              $location = isset($email_info['country']) ? $email_info['country'] : NULL;
              $isVerified = $email_info['email_verified'] ?? FALSE;
              if(boolval($isVerified) == TRUE){
                $userDetails = new UP\UserDO();
                $userDetails->id = $linkedin_id;
                $userDetails->provider = 'LinkedIn';
                $userDetails->name = $full_name; 
                $userDetails->email = $email;
                $userDetails->photo = $profile_picture;
                $userDetails->location = $location;
              }
          }
      }
      return $userDetails;
   }

   function __destruct(){
    $this->clientID = $this->clientSecret = $this->redirectUri = $this->loginUri = NULL;
   }
}

