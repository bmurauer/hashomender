<?php
	include_once("config.php");

	$msg = trim($_POST['msg']);
	
	// sending back json, so we have to modify the headers.
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	

	// These are the main steps of the alrorithm:

	// retrieve the tweets from the solr server

	$tweets = get_tweets($msg, $tweet_limit, $solr_url);

	// extract the tags from the tweets
	$tags = extract_tags($tweets);
	
	// rank the tags according to solr score and # of occurrances
	$tags = rank_tags($tags);

	// we just need some tags, cut off the rest
	$filtered_tags = filter($tags, $tag_limit);

	// print back result
	print(json_encode($filtered_tags));	



	// MAIN FUNCTIONS
	
	// retrieves the tweets from SOLR
	function get_tweets($msg, $tweet_limit, $solr_url){
		$array = explode(" ",$msg);
		
		// removes words that already are hashtags
		$filtered = array();
		foreach($array as $word){
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
		$req = new HttpRequest($solr_url.'?q='.$query.'&rows='.$tweet_limit.'&fl=score&group=true&group.field=hashtags&wt=json&indent=true', HTTP_METH_POST);
		$result = json_decode($req->send()->getBody());

		return $result;
	}
	
	// extracts the hashtags from a set of tweets
	function extract_tags($tweets){
		$tags = array();
		if(isset($tweets->grouped->hashtags->groups)){
		  foreach($tweets->grouped->hashtags->groups as $tag_group){
			  $tags_combined = explode(" ",$tag_group->groupValue);
			  foreach($tags_combined as $tag){			  
			    $tags[] = '#'.$tag;
			  }
		  }
		}
		return $tags;
	}
	
	// ranks the tweets according to Score and Hashtag Count
	function rank_tags($tags){
		return $tags;
	}
	
	// applies a filter (e.g. limit the set size)
	function filter($tags, $tag_limit){
		return array_slice($tags, 0, $tag_limit);
	}
?>
