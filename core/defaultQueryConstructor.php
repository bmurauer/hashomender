<?php
	include_once("config.php");
/**
 * Description of defaultQueryConstructor
 *
 * @author benjamin
 */	
class defaultQueryConstructor implements iQueryConstructor{
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
		return SOLR_URL.'?q='.$query.'&rows='.TWEET_LIMIT.'&fl=score&wt=json&indent=true';
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
