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
     * 			might be helpful for the recommendation algorithm. In This 
     *          example, the score of the tweets and their amount of 
     *          occurrences, which is calculated separatly, are being used.
     * @return mixed[]
     * 			the return value of this function just needs to be compatible 
     *          with the rank_tags function, as the result of this function is 
     *          passed to rank_tags().
     */
    public function extractTags($tweets) {
        $tags = array();
        if (isset($tweets->QTime)){
          include_once('../time.php');
          logTime($tweets->QTime, "Solr-Recommend");
        }
        if (isset($tweets->response->docs)) {
            foreach ($tweets->response->docs as $tweet) {
                $score = $tweet->score;
                foreach (explode(" ", $tweet->hashtags) as $tag) {
                    if (!isset($tags[strtolower($tag)])) {
                        $tags[strtolower($tag)] = array('original' => $tag, 'count' => 1, 'maxScore' => $score);
                    } else {
                        $tags[strtolower($tag)]['count']++;
                        $old = $tags[strtolower($tag)]['maxScore'];
                        $tags[strtolower($tag)]['maxScore'] = max(array($old, $score));
                    }
                }
            }
        }
        return $tags;
    }
}

?>
