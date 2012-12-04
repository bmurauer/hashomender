<?php
//  Copyright Benjamin Murauer 2012
//      
//  This file is part of the Hash-O-Mender program.
//
//  Hash-O-Mender is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  Hash-O-Mender is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Hash-O-Mender (gpl.txt).
//  If not, see <http://www.gnu.org/licenses/>.

	include_once("config.php");

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
