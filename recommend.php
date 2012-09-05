<?php
	include_once("config.php");
	include_once("algorithm_checker.php");
	check_algorithms(FALSE);
	$msg = trim($_POST['msg']);
/**/
	// sending back json, so we have to modify the headers.
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
/**/	
	// These are the main steps of the alrorithm:

	if(!function_exists("extract_tags")){
		print(json_encode("No function \"extract tags\" provided"));
	}
	
	// retrieve the tweets from the solr server
	$tweets = get_tweets($msg);

	// extract the tags from the tweets
	$tags = extract_tags($tweets);
	
	// rank the tags according to solr score and # of occurrances
	$tags_ranked = rank_tags($tags);

	// we just need some tags, cut off the rest
	$filtered_tags = filter($tags_ranked);

	// print back result
	print(json_encode($filtered_tags));	



	// MAIN FUNCTIONS
	
	// retrieves the tweets from SOLR
	function get_tweets($msg){
		$array = explode(" ",$msg);
		
		// removes words that already are hashtags
		$filtered = array();
		foreach($array as $word){
			if(trim($word) == ""){
				continue;
			}
			if(substr($word, 0, 1) === '#'){
				$filtered[] = substr($word, 1);			
			} else {
				$filtered[] = $word;
			}
		}
		
		$query = '';
		$i=0;
	
		foreach($filtered as $val){
			if($i!=0){
				$query .= '%20tweet:'.escapeSolrValue($val);
			} else {
				$query .= 'tweet:'.escapeSolrValue($val);
			}
			$i++;
		}
		
		$req = new HttpRequest(SOLR_URL.'?q='.$query.'&rows='.TWEET_LIMIT.'&fl=score&wt=json&indent=true', HTTP_METH_POST);
		// &group=true&group.field=hashtags
		$result = json_decode($req->send()->getBody());

		return $result;
	}
		
	// applies a filter (e.g. limit the set size)
	function filter($tags){
		return array_slice($tags, 0, TAG_LIMIT);
	}
	
	// from http://e-mats.org/2010/01/escaping-characters-in-a-solr-query-solr-url/
	function escapeSolrValue($string){
        $match = array('\\', '+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':', '"', ';', ' ');
        $replace = array('\\\\', '\\+', '\\-', '\\&', '\\|', '\\!', '\\(', '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\~', '\\*', '\\?', '\\:', '\\"', '\\;', '\\ ');
        $string = str_replace($match, $replace, $string);
 
        return $string;
    }

?>
