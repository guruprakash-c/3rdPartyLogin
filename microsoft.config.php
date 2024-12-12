<?php
use UserProps as UP; 

require_once "UserDO.php";

final class MicrosoftMicroservices{
   private $clientID;
   private $clientSecret;
   private $redirectUrl;
   private $loginUrl;

   function __construct(){
    define('MS_CLIENT_ID',''); 
    define('MS_CLIENT_SECRET', ''); 
    define('MS_REDIRECT_URL', 'http://localhost/3rdPartyLogin/index.microsoft.php'); 
    define('MS_SCOPES', urlencode('openid profile email User.Read'));
    define('MS_LOGIN_URI', "https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id=" . MS_CLIENT_ID . "&response_type=code&redirect_uri=" . urlencode(MS_REDIRECT_URL) . "&scope=" . MS_SCOPES);
    define('MS_TENANT_ID', '');
    $this->clientID = MS_CLIENT_ID;
    $this->clientSecret = MS_CLIENT_SECRET;
    $this->redirectUrl = MS_REDIRECT_URL;
    $this->loginUrl = MS_LOGIN_URI;
   }
   
   public function MicrosoftAuthUrl(){
     return $this->loginUrl;
   }
   
   public function GetUserDetails($code){
      $userDetails = [];
      if(!empty($code)){
         $token_url = "https://login.microsoftonline.com/common/oauth2/v2.0/token";
         $token_data = [
           "grant_type" => "authorization_code",
           "code" => $code,
           "client_id" => $this->clientID,
           "client_secret" => $this->clientSecret,
           "redirect_uri" => $this->redirectUrl,
         ];

         $curl = curl_init($token_url);
         curl_setopt($curl, CURLOPT_POST, true);
         curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($token_data));
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
         $response = curl_exec($curl);
         curl_close($curl);

         $token_info = json_decode($response, true);

         if (isset($token_info['access_token'])) {
           $graph_url = "https://graph.microsoft.com/v1.0/me";
           $curl = curl_init($graph_url);
           curl_setopt($curl, CURLOPT_HTTPHEADER, [
               "Authorization: Bearer " . $token_info['access_token'],
               "Content-Type: application/json"
           ]);
           curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
           $response = curl_exec($curl);
           curl_close($curl);

           $user_info = json_decode($response, true);

           $microsoft_id = $user_info['id'];
           $full_name = $user_info['displayName'];
           $email = $user_info['mail'] ?? $user_info['userPrincipalName'];

           // Fetch profile picture
           $photo_url = "https://graph.microsoft.com/v1.0/me/photo/\$value";
           $curl = curl_init($photo_url);
           curl_setopt($curl, CURLOPT_HTTPHEADER, [
               "Authorization: Bearer " . $token_info['access_token']
           ]);
           curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
           $photo_data = curl_exec($curl);
           $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
           curl_close($curl);

           $profile_picture = "";
           if ($http_code == 200) {
               $photo_filename = "profile_pictures/" . $microsoft_id . ".jpg";
               file_put_contents($photo_filename, $photo_data);
               $profile_picture = $photo_filename;
           }
           $userDetails = new UP\UserDO();
           $userDetails->id = $microsoft_id;
           $userDetails->provider = 'Microsoft';
           $userDetails->name = $full_name; 
           $userDetails->email = $email;
           $userDetails->photo = $profile_picture;
         }
      }
      return $userDetails;
   }

   function __destruct(){
    $this->clientID = $this->clientSecret = $this->redirectUri = $this->loginUri = NULL;
   }
}