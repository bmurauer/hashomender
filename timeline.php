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
/**/
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
/**/
	$Twitter = new EpiTwitter(CONSUMER_KEY, CONSUMER_SECRET);
	$Twitter->setToken($_COOKIE['oauth_token'],$_COOKIE['oauth_token_secret']);
	$Twitter->get_accountVerify_credentials();

	$timeline = $Twitter->get_statusesHome_timeline(array(
		"count" => TIMELINE_LIMIT));
	$response = array();
	
    // we have to modify the result, as not all of it is needed by the client.
	foreach($timeline->response as $tweet){
        // twitter time format: Fri Sep 14 12:00:03 +0000 2012
        // desired format: 14 Sep 2012 12:00
		$date = split(" ", $tweet['created_at']);
        $day = $date[2];
        $month = $date[1];
        $year = $date[5];
        $time = substr($date[3], 0, 5);
		$response[] = array(
			"name" => $tweet['user']['name'],
            "screen_name" => $tweet['user']['screen_name'],
			"text" => $tweet['text'],
			"date" => $day.'. '.$month.' '.$year.' '.$time,
		);
	}
	print(json_encode($response));
?>
