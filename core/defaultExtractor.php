<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of defaultExtractor
 *
 * @author benjamin
 */
class defaultExtractor implements iExtractor {

	/**
	 * This function extracts the hashtags from a set of tweets. 
	 * @param stdObj $tweets
	 * 			$tweets is an obect containing information about the tweet that
	 * 			might be helpful for the recommendation algorithm. In This example,
	 * 			the score of the tweets and their amount of occurrences, which is 
	 * 			calculated separatly, are being used.
	 * @return mixed[]
	 * 			the return value of this function just needs to be compatible with
	 * 			the rank_tags function, as the result of this function is passed to 
	 * 			rank_tags.
	 */
	public function extractTags($tweets) {
		$tags = array();
		if (isset($tweets->response->docs)) {
			foreach ($tweets->response->docs as $tweet) {
				$score = $tweet->score;
				foreach (explode(" ", $tweet->hashtags) as $tag) {
					if (!isset($tags[$tag])) {
						$tags[$tag] = array('count' => 1, 'maxScore' => $score);
					} else {
						$tags[$tag]['count']++;
						$old = $tags[$tag]['maxScore'];
						$tags[$tag]['maxScore'] = max(array($old, $score));
					}
				}
			}
		}
		return $tags;
	}

}

?>
