<?php
session_start();
if(!isset($_SESSION['clientid']) && !isset($_SESSION['clientsecret']) && !isset($_SESSION['url'])){
	echo 'Please try again.';
}
require_once 'Instagram.php';
$config = array(
        'client_id' => $_SESSION['clientid'],
        'client_secret' => $_SESSION['clientsecret'],
        'grant_type' => 'authorization_code',
        'redirect_uri' => $_SESSION['url'],
     );

$instagram = new Instagram($config);

if($_REQUEST['code']=='')
{
	$instagram->openAuthorizationUrl();
}
$accessToken = $instagram->getAccessToken();


?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Instagram Access Token | The Logical Coder</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<div class="container">
  <h1>Your Instagram Access Token & User ID: </h1>
  <div class="alert alert-success"><p><strong>Access Token: </strong><?php echo $accessToken;?></p>
  <p><strong>User ID: </strong><?php echo strstr($accessToken,'.',true);?></p>
  </div>
  <p class="description">Copy and paste this Instagram Access Token into the input field.</p>
</div>
</body>
</html>
