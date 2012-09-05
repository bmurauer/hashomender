<?php
	/**
	 * This file flattens the hierarchical tweet json file delivered by the 
	 * mongoDB database. Flat structure is needed for SOLR, which can't handle
	 * hierarchies in its index.
	 */
	
	// limits the output
	$limit = 10;
	// input
	$fp = fopen("tweets/tweets_small.json", "r") or die("Could not open file");
	// output
	$re = fopen("tweets/result.xml", "w") or die("Could not write result file");
	if($fp && $re){
		fputs($re, "[");
		$id = 0;
		while(!feof($fp) && ($limit == 0 || $id < $limit)) {
			$buffer = fgets($fp);
			$tweet = json_decode($buffer);
			$output = array();
			$output['id'] = $id;
			$output['tweet'] = $tweet->text;
			foreach($tweet->entities->hashtags as $tag){
				$output['hashtags'] .= $tag->text." ";
			}
			$output['hashtags'] = trim($output['hashtags']);
			fputs($re,json_encode($output));
			$id++;
		}
		// this removes the last comma from the array => SOLR does not accept 
		// a trailing comma
		fseek($re, -1, SEEK_CUR);
		fputs($re, "]");
		fclose($fp);
		fclose($re);
		print("Added $id tweets to result file");
	}
	
?>
