<?php

/**
 * This file contains a dummy version of the algorithm. It just extracts the
 * tags with no sorting whatsoever.
 */

 /**
  * This function extracts the hashtags from a set of tweets. 
  * @param stdObj $tweets
  *			$tweets is an obect containing information about the tweet that
  *			might be helpful for the recommendation algorithm. In This example,
  *			no sorting will be done.
  * @return mixed[]
  *			the return value of this function just needs to be compatible with
  *			the rank_tags function, as the result of this function is passed to 
  *			rank_tags.
  */
function extract_tags($tweets){
	$tags = array();
	if(isset($tweets->response->docs)){
		foreach($tweets->response->docs as $tweet){
			foreach(explode(" ", $tweet->hashtags) as $tag){
				$tags[] = $tag;
			}
		}
	}
	return $tags;
}
	
/**
 * This function sorts the tags. It gets a list of tags from extract_tags,
 * which are sorted. In this default algorithm, the amount of occurrences and
 * the score delivered by solr are used for the final score.
 * 
 * @param mixed[] $tags
 * @return string[]
 *		this function must return an array of strings, nothing more. Score
 *		values or something similar are not allowed. The strings in this array
 *		are later displayed unmodified to the user.
 */
function rank_tags($tags){
	return $tags;
}
?>