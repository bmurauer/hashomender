<?php

/**
 * The main interfaces 
 * @author Benjamin Murauer
 */

/**
 * The class constructing the query for the Solr server from the post
 * information
 */
interface iQueryConstructor {
	
	/**
	 * this function builds the Solr query from the words inside the $post
	 * array. 
	 * 
	 * @param string[] $post
	 *		The words the user typed into the text area
	 * @return string
	 *		The complete Solr query
	 */
	public function getRecommendQuery($post);
}

/**
 * This class is responsible for contacting Solr and retrieving the tweets. The
 * exact form and supported features of the tweets depend on the implementation.
 */
interface iFetcher {
	/**
	 * sends a http request with a given query to solr and returs a set of
	 * tweets
	 * 
	 * @param string $query
	 *		the complete Solr query
	 * @return mixed[]
	 *		a set of tweets
	 */
	public function getTweets($query);
}

/**
 * the exctractor class is responsible for extracting useful information out of
 * the tweet. At least the text of the tweet and the hashtags should be 
 * extracted. Which other features can be extracted depends on the
 * implementation of the queryConstructor and the fetcher classes. The resulting
 * set can contain additional information that is used for sorting later on.
 */
interface iExtractor {
	/**
	 * extracts the hashtags of the given tweets. Might also add additional 
	 * information for sorting.
	 * 
	 * @param mixed[] $tweets
	 *		a set of tweets
	 * @return mixed[]
	 *		a set of tags
	 */
	public function extractTags($tweets);
}

/**
 * sorts an array of tags. The sorting depends on what information is stored in
 * the tags array. The resulting array only contains the remaining necessary 
 * information for the user (no score etc.).
 */
interface iSorter {
	/**
	 * 
	 * @param mixed[] $tags
	 *		The hashtags
	 * @return mixed[]
	 *		An array of tags that is sorted
	 */
	public function sortTags($tags);
}

/**
 * filters the tags by reducing the amount, adding content filters,...
 */
interface iFilter {
	/**
	 * this function filters the given hashtag array. 
	 * 
	 * @param mixed[] $tags
	 *		The hashtags
	 * @return mixed[]
	 *		the filtered hashtag array
	 */
	public function filterTags($tags);
}
?>
