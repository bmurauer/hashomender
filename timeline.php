<?php
//  Copyright Benjamin Murauer 2012
//      
//  This file is part of the Hash-O-Mender program.
//
//  Hash-O-Mender is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  Hash-O-Mender is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Hash-O-Mender (gpl.txt).
//  If not, see <http://www.gnu.org/licenses/>.

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
                        "image" => $tweet['user']['profile_image_url']
		);
	}
	print(json_encode($response));
?>
