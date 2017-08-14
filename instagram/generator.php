<?php
session_start();
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
/*$url = $protocol . $_SERVER['HTTP_HOST'];*/
$url=$protocol.$_SERVER['HTTP_HOST'].strtok($_SERVER["REQUEST_URI"],'?');
$url = str_replace('generator.php','',$url);
if($_REQUEST['clientid']!="" || $_REQUEST['clientsecret']!=""){
	$_SESSION['clientid']=@$_REQUEST['clientid'];
	$_SESSION['clientsecret']=@$_REQUEST['clientsecret'];
	$_SESSION['url']=$url;
	header("Location:./");
}