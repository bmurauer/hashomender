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
 * Description of defaultSorter
 *
 * @author benjamin
 */
class defaultSorter implements iSorter {

    /**
     * This function sorts the tags. It gets a list of tags from extract_tags,
     * which are sorted. In this default algorithm, the amount of occurrences 
     * and the score delivered by solr are used for the final score.
     * 
     * @param mixed[] $tags
     * 		the tags to be sorted
     * @return string[]
     * 		The strings in this array are later displayed unmodified to the 
     *      user.
     */
    public function sortTags($tags) {
        $sorted = array();
        foreach ($tags as $tag => $values) {
            $total_score = FACTOR_COUNT * $values['count'] +
                    FACTOR_SCORE * $values['maxScore'];
            $sorted[$values['original']] = $total_score;
        }
        // asort sorts an array descending by its value
        arsort($sorted);
        return array_keys($sorted);
    }

}

?>
