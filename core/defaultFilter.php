<?php

include_once('config.php');

/**
 * Description of defaultFilter
 *
 * @author benjamin
 */
class defaultFilter implements iFilter{

	public function filterTags($tags) {
		return array_slice($tags, 0, TAG_LIMIT);
	}
}

?>
