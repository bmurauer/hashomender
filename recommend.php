<?php
	include_once("config.php");

	$msg = trim($_POST['msg']);
	
	// sending back json, so we have to modify the headers.
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	

	// These are the main steps of the alrorithm:

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
				$query .= '%20tweet:'.$val;
			} else {
				$query .= 'tweet:'.$val;
			}
			$i++;
		}
		$req = new HttpRequest(SOLR_URL.'?q='.$query.'&rows='.TWEET_LIMIT.'&fl=score&wt=json&indent=true', HTTP_METH_POST);
		// &group=true&group.field=hashtags
		$result = json_decode($req->send()->getBody());

		return $result;
	}
	
	// extracts the hashtags from a set of tweets
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
	
	// ranks the tweets according to Score and Hashtag Count
	function rank_tags($tags){
		$sorted = array();
		foreach($tags as $tag => $values){
			$total_score = FACTOR_COUNT * $values['count'] + 
				FACTOR_SCORE * $values['maxScore'];
			$sorted[$tag] = $total_score;
		}
		arsort($sorted);
		return $sorted;
	}
	
	// applies a filter (e.g. limit the set size)
	function filter($tags){
		return array_slice($tags, 0, TAG_LIMIT);
	}
?>
