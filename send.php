<?php 
	include 'EpiTwitter/EpiCurl.php';
	include 'EpiTwitter/EpiOAuth.php';
	include 'EpiTwitter/EpiTwitter.php';
	include 'config.php';
	$Twitter = new EpiTwitter(CONSUMER_KEY, CONSUMER_SECRET);
	$Twitter->setToken($_COOKIE['oauth_token'],$_COOKIE['oauth_token_secret']);
	$Twitter->get_accountVerify_credentials();
	$text=$_POST['status'];
	$status=$Twitter->post_statusesUpdate(array('status' => $text));
	$status->response;
	echo "OK";
?>
