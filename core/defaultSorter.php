<?php

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
            $sorted[$tag] = $total_score;
        }
        // asort sorts an array descending by its value
        arsort($sorted);
        return array_keys($sorted);
    }

}

?>
