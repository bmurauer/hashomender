<?php
	include_once("config.php");
/**
 * 
 *
 * @author benjamin
 */	
class defaultQueryConstructor implements iQueryConstructor{
    
    /**
     * This function constructs the query for the Solr server. The result is a 
     * complete query that does not need to be modified. 
     * If hashtags are in the $post array, they will be considered as well (the
     * hash symbol will be cut away for simplification though).
     * The structure of the resulting query is:
     * SOLR_URL?q=tweet:word1%20tweet:word2%20...tweet:wordn&fl=score&wt=json
     * the query will produce json output.
     * 
     * @param string[] $post
     *      The array of words the user typed and are used by Solr for 
     *      recommending. This can be the unaltered $_POST-array.
     * @return strign
     *      the complete Solr query
     */
	public function getRecommendQuery($post) {
		$msg = trim($post['msg']);
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
				$query .= '%20tweet:'.self::escapeSolrValue($val);
			} else {
				$query .= 'tweet:'.self::escapeSolrValue($val);
			}
			$i++;
		}
		return SOLR_URL.'?q='.$query.'&rows='.TWEET_LIMIT.'&fl=score&wt=json';
	}
	// from http://e-mats.org/2010/01/escaping-characters-in-a-solr-query-solr-url/
	private function escapeSolrValue($string){
        $match = array('\\', '+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':', '"', ';', ' ');
        $replace = array('\\\\', '\\+', '\\-', '\\&', '\\|', '\\!', '\\(', '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\~', '\\*', '\\?', '\\:', '\\"', '\\;', '\\ ');
        $string = str_replace($match, $replace, $string);
 
        return $string;
    }
}

?>
