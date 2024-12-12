<html>
<?php 
if(isset($_SESSION)) session_start();

require_once "google.config.php";
require_once "github.config.php";
require_once "linkedin.config.php";
require_once "microsoft.config.php";

$googleClient = new GoogleMicroService();
$gitHubClient = new GitHubMicroservice();
$linkedInClient = new LinkedInMicroservice();
$msClient = new MicrosoftMicroservices();
?>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>3rd Party Login</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'/>
  <link async href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link async href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
 </head>
 <body class="container">
    <div class="row">
        <section class="col-md-6 mx-auto py-4">
            <div class="card shadow text-center">
                <header class="card-header bg-secondary-subtle text-center">
                    <h1 class="align-middle"><i class="bi bi-box-arrow-in-right me-1"></i>3rd Party Login</h1>
                </header>
                <main class="card-body py-5">
                    <?php 
                        if(isset($_SESSION)) print_r($_SESSION);
                        // if(isset($_SESSION['userData'])){ 
                        //     var_dump($_SESSION['userData']);
                        //     echo 1;
                        // } else { echo -1;
                    ?>
                    <p class="lead">Join our community of friendly folks discovering and sharing the latest products in tech.</p>
                    <?php 
                        $loginServices = array(
                            'Google:bi-google' => $googleClient->GetGoogleAuthUrl(NULL,NULL),
                            'GitHub:bi-github' => $gitHubClient->GitHubAuthUrl(),
                            'Linkedin:bi-linkedin' => $linkedInClient->LinkedInAuthUrl(),
                            'Microsoft:bi-microsoft' => $msClient->MicrosoftAuthUrl()
                        );
                    ?>
                    <div class="btn-group">
                        <?php 
                            foreach ($loginServices as $serviceKey => $loginService) { 
                                    $iconTxt = explode(':', $serviceKey)[0];
                                    $icon = explode(':', $serviceKey)[1];
                        ?>  
                        <a href="<?=(!empty($loginService) ? $loginService : 'javascript:void(0);') ?>" class="btn btn-light me-1 ms-1<?=(empty($loginService) ? ' disabled' : '') ?>" data-bs-toggle="tooltip" title="Signin with <?=($iconTxt) ?>">
                            <i class="bi <?=($icon) ?> me-1"></i><?=($iconTxt) ?>
                        </a>
                      <?php } ?>
                    </div>
                    <?php //} ?>
                </main>
                <footer class="card-footer bg-secondary-subtle">We'll never post to any of your accounts without your permission.</footer>
            </div>
        </section>
    </div>
    
 </body>
 <script defer src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript">
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
  </script>
</html>