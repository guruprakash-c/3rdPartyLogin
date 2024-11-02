<?php

//logout.php

require_once "google.config.php";
require_once "fb.config.php";

//Reset OAuth access token
$google_client->revokeToken();

if(isset($_SESSION['facebook_access_token'])) unset($_SESSION['facebook_access_token']);

// Remove user data from session
if(isset($_SESSION['userData'])) unset($_SESSION['userData']);

//Destroy entire session data.
session_destroy();

//redirect page to index.php
header('location:index.php');