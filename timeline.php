<?php

	/**
	 * This file is called via AJAX and returns the
	 * timeline of the currently logged in user. The
	 * timeline is a set of the last recent tweets.
	 */

	include 'EpiCurl.php';
	include 'EpiOAuth.php';
	include 'EpiTwitter.php';
	include 'keys.php';
	include 'config.php';

	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	
	$Twitter = new EpiTwitter($consumerKey, $consumerSecret);
	$Twitter->setToken($_COOKIE['oauth_token'],$_COOKIE['oauth_token_secret']);
	$user= $Twitter->get_accountVerify_credentials();

	$timeline = $Twitter->get_statusesHome_timeline(array(
		"count" => TIMELINE_LIMIT));
	$response = array();
	
	foreach($timeline->response as $tweet){
		$response[] = array(
			"text" => $tweet['text'],
			"date" => $tweet['created_at']);
	}
	print(json_encode($response));


?>
