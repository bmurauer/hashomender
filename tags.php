<?php

/**
 * This file is called by AHAH when the user types a hashtag him/herself. It 
 * represents a simple dictionary-style autocompletion. A query to SOLR is made
 * and the results are checked for duplicates, then added to a list. As long as 
 * the list has not the final size, more results are fetched. 
 */

include_once 'config.php';

$tag_pool = array();

/**
 * How many tags will be returned as a result
 */
$pool_size = AUTOCOMPLETE_LIMIT;

/**
 * How many times this function will fetch more tags if the final amount is not
 * reached yet
 */
$timeout = 10;

/**
 * How many tags will be fetched at once
 */
$fetch_size = 20;

$word = escapeSolrValue(strtolower($_POST['lastWord']));

for ($i = 0; $i < $timeout; $i++) {
	$req = new HttpRequest(
					SOLR_URL . '?q=hashtags:' . trim($word) . '*&fl=hashtags&start=' . $i * $fetch_size . '&rows=' . $fetch_size . '&wt=json&indent=true');
	$result = json_decode($req->send()->getBody());
	foreach ($result->response->docs as $tweet) {
		$tags = explode(" ", $tweet->hashtags);
		foreach ($tags as $tag) {
			$lc = strtolower(trim($tag));
			if ((!in_array("#" . $lc, $tag_pool)) && strpos($lc, $word) === 0) {
				$tag_pool[] = "#" . $lc;
			}
		}
	}
	if (count($tag_pool) >= $pool_size){
		break;
	}
}

$tag_pool_reduced = array_splice($tag_pool, 0, $pool_size);

/**/
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
/**/
print(json_encode($tag_pool_reduced));

// from http://e-mats.org/2010/01/escaping-characters-in-a-solr-query-solr-url/
	function escapeSolrValue($string){
        $match = array('\\', '+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':', '"', ';', ' ');
        $replace = array('\\\\', '\\+', '\\-', '\\&', '\\|', '\\!', '\\(', '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\~', '\\*', '\\?', '\\:', '\\"', '\\;', '\\ ');
        $string = str_replace($match, $replace, $string);
 
        return $string;
    }
?>
