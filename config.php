<?php
	/**
	 * This is the main configuration file.
	 */

	/** 
	 * the max amount of tweets that the solr-server will return to 
	 * php. 
	 */
	$tweet_limit = 100;

	/**
 	 * The amount of hashtags that will be returned to the application
	 * via Ajax. 
	 */
	$tag_limit = 10;

	/**
	 * The url to the solr server
	 */
	$solr_url = "http://localhost:8983/solr/select/";

	/**
	 * The max amount of tweets appearing in the timeline on the left
	 */
	$timeline_max_count = 5;
?>
