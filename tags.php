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

/**
 * This file is called by AHAH when the user types a hashtag him/herself. It 
 * represents a simple dictionary-style autocompletion. A query to SOLR is made
 * and the results are checked for duplicates, then added to a list. As long as 
 * the list has not the final size, more results are fetched. 
 */
include_once 'config.php';
include_once 'time.php';
$start = microtime(true);
$tag_pool = array();

$word = escapeSolrValue(strtolower($_POST['lastWord']));

// this loop tries AUTOCOMPLETE_TIMEOUT times to fetch new hashtags
for ($i = 0; $i < AUTOCOMPLETE_TIMEOUT; $i++) {
    // note the 'start=' offset that increases each loop cycle
    $req = new HttpRequest(
                    SOLR_URL . '?q=hashtags:' . trim($word)
                    . '*&fl=hashtags&start=' . $i * AUTOCOMPLETE_FETCH_SIZE
                    . '&rows=' . AUTOCOMPLETE_FETCH_SIZE . '&wt=json&indent=true');
    $result = json_decode($req->send()->getBody());
    foreach ($result->response->docs as $tweet) {
        // tags are separated by spaces
        $tags = explode(" ", $tweet->hashtags);
        foreach ($tags as $tag) {
            $lc = strtolower(trim($tag));
            // don't add duplicates
            if ((!in_array("#" . $lc, $tag_pool)) && strpos($lc, $word) === 0) {
                $tag_pool[] = "#" . $lc;
            }
        }
    }
    if (count($tag_pool) >= AUTOCOMPLETE_LIMIT) {
        break;
    }
}


usort($tag_pool, "cmp_function");
// if we got more than wanted
$tag_pool_reduced = array_splice($tag_pool, 0, AUTOCOMPLETE_LIMIT);

/**/
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
/**/
print(json_encode($tag_pool_reduced));
$end = microtime(true);
logTime(1000*($end-$start), "autocomplete");
/**
 * this function escapes symbols that are special characters for the Solr query.
 * @author <a href="http://e-mats.org/2010/01/escaping-characters-in-a-solr-query-solr-url/">e-mats.org</a>
 * 
 * @param string $string
 *      the string we want to escape
 * @return string
 *      the escaped string
 */
function escapeSolrValue($string) {
    $match = array('\\', '+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':', '"', ';', ' ');
    $replace = array('\\\\', '\\+', '\\-', '\\&', '\\|', '\\!', '\\(', '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\~', '\\*', '\\?', '\\:', '\\"', '\\;', '\\ ');
    $string = str_replace($match, $replace, $string);
    return $string;
}

/** 
 * this helper function is used for usort. It is supposed to sort tags
 * according to their string length. shorter strings end up at the top of the
 * list. This is needed when a word the user typed is also a hashtag, because
 * it ensures that the tag is then the first entry in the list.
 * @param string $a the first string
 * @param string $b the second string
 * @return boolean
 */
function cmp_function($a, $b) {
    return strlen($a) - strlen($b);
}

?>
