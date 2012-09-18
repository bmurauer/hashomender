<?php

include_once('config.php');

/**
 * Default implementation of the filter module. Just slices off excess tweets.
 *
 * @author benjamin
 */
class defaultFilter implements iFilter{

    /**
     * Slices off excess tags. See config.php for the amount of tags returned 
     * (TAG_LIMIT).
     * 
     * @param string[] $tags
     *      The tag list to filter
     * @return string[]
     *      The resulting tag list that has at most TAG_LIST elements.
     */
	public function filterTags($tags) {
		return array_slice($tags, 0, TAG_LIMIT);
	}
}

?>
