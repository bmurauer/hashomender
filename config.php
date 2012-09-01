<?php
	/**
	 * This is the main configuration file.
	 */

	/** 
	 * the max amount of tweets that the solr-server will return to 
	 * php. 
	 */
	define("TWEET_LIMIT", 100);

	/**
 	 * The amount of hashtags that will be returned to the application
	 * via Ajax. 
	 */
	define("TAG_LIMIT", 10);

	/**
	 * The url to the solr server
	 */
	define("SOLR_URL", "http://localhost:8983/solr/select/");

	/**
	 * The max amount of tweets appearing in the timeline on the left
	 */
	define("TIMELINE_LIMIT", 5);
	
	define("FACTOR_SCORE", 0.8);
	define("FACTOR_COUNT", 0.2);
?>
