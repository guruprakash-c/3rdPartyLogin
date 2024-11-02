<html>
 <?php 
 if(isset($_SESSION)) session_start();
 ?>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>3rd Party Login</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'/>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
 </head>
 <body>
  <?php require_once "google.config.php"; ?>
  <div class="container">
   <br />
   <h2 align="center">Google Login</h2>
   <br />
   <div class="panel panel-default">
   <?php
   if($googleLoginBttn == '')
   {
   	//var_dump($_SESSION['user']);
   	$userInfo = $_SESSION['user'];
    echo '<div class="panel-heading">Welcome '.$userInfo['name'].'</div><div class="panel-body">';
    echo '<img src="'.$userInfo['picture'].'" class="img-responsive img-circle img-thumbnail" />';
    echo '<h3><b>Email :</b> '.$userInfo['email'].'</h3>';
    echo '<h3><b>Gender :</b> '.$userInfo['gender'].'</h3>';
    echo '<h3><a href="logout.php">Logout</h3></div>';
   }
   else
   {
    echo '<div align="center">'.$googleLoginBttn . '</div>';
   }
   ?>
   </div>
  </div>
 </body>
</html>