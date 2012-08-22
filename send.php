<?php 
	include 'EpiCurl.php';
	include 'EpiOAuth.php';
	include 'EpiTwitter.php';
	include 'keys.php';
	$Twitter = new EpiTwitter($consumerKey, $consumerSecret);
	$Twitter->setToken($_COOKIE['oauth_token'],$_COOKIE['oauth_token_secret']);
	$user= $Twitter->get_accountVerify_credentials();
	//validate here
	$text=$_POST['status'];
	$status=$Twitter->post_statusesUpdate(array('status' => $text));
	$status->response;
	echo "OK";
?>
