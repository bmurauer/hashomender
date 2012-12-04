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
