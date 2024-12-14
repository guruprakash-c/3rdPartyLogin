<?php

//logout.php

require_once "google.config.php";

//Reset OAuth access token
$google_cli = new GoogleMicroservice();
$google_cli->RevokeToken();


// Remove user data from session
if(isset($_SESSION['userData'])) unset($_SESSION['userData']);

//Destroy entire session data.
session_destroy();

//redirect page to index.php
header('location:index.php');