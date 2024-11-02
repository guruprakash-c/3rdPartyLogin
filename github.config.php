<?php
use UserProps as UP; 

require_once "UserDO.php";
require_once "modules/gh.config.php";

final class GitHubMicroservice{
   public $gitClient = NULL;

   public function __construct(){
      define('GH_CLIENT_ID', ''); 
      define('GH_CLIENT_SECRET', ''); 
      define('GH_REDIRECT_URL', 'http://localhost/3rdPartyLogin/index.github.php'); 

      $this->gitClient = new Github_OAuth_Client(array( 
         'client_id' => GH_CLIENT_ID, 
         'client_secret' => GH_CLIENT_SECRET, 
         'redirect_uri' => GH_REDIRECT_URL 
      )); 
   }
   public function GetUserDetails($state, $code){
      $userDetails = [];
      //echo $state . ' : ' . $code;
      $accessToken = self::GitAccessToken($state, $code);
      if(!empty($accessToken)){
        // Get the user profile data from Github 
        $gitUser = $this->gitClient->getAuthenticatedUser($accessToken); 
         
        if(!empty($gitUser)){ 
            // Getting user profile details 
            $gitUserData = new UP\UserDO();
            $gitUserData->id = $gitUser->id;
            $gitUserData->provider = 'GitHub';
            $gitUserData->name = $gitUser->name;
            $userEmail = NULL;
            if(empty($gitUser->email)){
               $gitUserEmails = $this->gitClient->getAuthenticatedUserEmail($accessToken);
               $idx = 0;
               foreach ($gitUserEmails as $email) {
                  if(isset($gitUserEmails[$idx]->primary) && isset($gitUserEmails[$idx]->verified)){
                     if($gitUserEmails[$idx]->primary == TRUE && $gitUserEmails[$idx]->verified == TRUE) 
                        $userEmail = $gitUserEmails[$idx]->email;
                  }
                  $idx+=1;
               }
            }
            $gitUserData->email = $userEmail;
            $gitUserData->photo = $gitUser->avatar_url;
            $gitUserData->location = $gitUser->location;
             
            /******* Insert or update user data to the database **************************
            $gitUserData['oauth_provider'] = 'github'; 
            $userData = $user->checkUser($gitUserData); */

            // Storing user data in the session 
            $userDetails = $gitUserData; 
        }
      }else{
         //var_dump($accessToken);
      }
      return $userDetails;
   }

   private function GitAccessToken($state, $code){
      $accessToken = NULL;
      // Verify the state matches the stored state 
     if(!empty($state) && !empty($code)) { 
         $accessToken = $this->gitClient->getAccessToken($state, $code);  
     }
     return $accessToken;
   }

   public function GitLogin(){
      $gitHubLoginUrl = NULL;
      // Generate a random hash and store in the session for security 
      $state = hash('sha256', microtime(TRUE) . rand() . $_SERVER['REMOTE_ADDR']);     
      // Get the URL to authorize 
      $gitHubLoginUrl = $this->gitClient->getAuthorizeURL($state); 
      return $gitHubLoginUrl;
   }

   public function __destruct(){
      $this->gitClient = NULL;
   }

}