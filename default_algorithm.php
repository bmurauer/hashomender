<?php

/**
 * This file contains the default behaviour for the hashomender recommendation.
 * Files located in README should work on the same input and deliver an output
 * of the same format as this function does. Specifications are located in the
 * README file.
 */

 /**
  * This function extracts the hashtags from a set of tweets. 
  * @param stdObj $tweets
  *			$tweets is an obect containing information about the tweet that
  *			might be helpful for the recommendation algorithm. In This example,
  *			the score of the tweets and their amount of occurrences, which is 
  *			calculated separatly, are being used.
  * @return mixed[]
  *			the return value of this function just needs to be compatible with
  *			the rank_tags function, as the result of this function is passed to 
  *			rank_tags.
  */
function extract_tags($tweets){
	$tags = array();
	if(isset($tweets->response->docs))
	foreach($tweets->response->docs as $tweet){
		$score = $tweet->score;
		foreach(explode(" ", $tweet->hashtags) as $tag){
			if(!isset($tags[$tag])){
				$tags[$tag] = array('count' => 1, 'maxScore' => $score);
			} else {
				$tags[$tag]['count']++;
				$old = $tags[$tag]['maxScore'];
				$tags[$tag]['maxScore'] = max(array($old, $score));
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
	$sorted = array();
	foreach($tags as $tag => $values){
		$total_score = FACTOR_COUNT * $values['count'] + 
			FACTOR_SCORE * $values['maxScore'];
		$sorted[$tag] = $total_score;
	}
	// asort sorts an array descending by its value
	arsort($sorted);
	return $sorted;
}
?>
