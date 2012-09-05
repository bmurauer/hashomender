<?php

	/**
	 * This file is called via AJAX and returns the
	 * timeline of the currently logged in user. The
	 * timeline is a set of the last recent tweets.
	 */

	include 'EpiTwitter/EpiCurl.php';
	include 'EpiTwitter/EpiOAuth.php';
	include 'EpiTwitter/EpiTwitter.php';
	include 'config.php';

	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');

	$Twitter = new EpiTwitter(CONSUMER_KEY, CONSUMER_SECRET);
	$Twitter->setToken($_COOKIE['oauth_token'],$_COOKIE['oauth_token_secret']);
	$Twitter->get_accountVerify_credentials();

	$timeline = $Twitter->get_statusesHome_timeline(array(
		"count" => TIMELINE_LIMIT));
	$response = array();
	
	foreach($timeline->response as $tweet){
		$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
		$tweet_result_text = $tweet['text'];
		if(preg_match($reg_exUrl, $tweet['text'], $url)){
			$tweet_result_text = preg_replace($reg_exUrl, '<a href="'.$url[0].'">'.$url[0].'</a>', $tweet['text']);
		}
		$response[] = array(
			"text" => $tweet_result_text,
			"date" => $tweet['created_at']
		);
	}
	print(json_encode($response));
?>
