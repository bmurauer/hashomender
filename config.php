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
 * This is the main configuration file.
 */

/**
 * Consumer key and secret for the twitter-O-auth user authorization
 */
define("CONSUMER_KEY", 'FkfCY0lvePEc0pdJtwgA1A');

define("CONSUMER_SECRET", 'wvEBZg7SjbUM4IbrmMPsiA3TEy1fIMIq8wSEJ8');

/**
 * the max amount of tweets that the solr-server will return to 
 * php. 
 */
define("TWEET_LIMIT", 100);

/**
 * The amount of hashtags that will be returned to the application
 * via Ajax. 
 */
define("TAG_LIMIT", 10);

/**
 * how many tags will be shown for autocompletion
 */
define("AUTOCOMPLETE_LIMIT", 10);

/**
 * The url to the solr server
 */
define("SOLR_URL", "http://localhost:8983/solr/select/");

/**
 * The max amount of tweets appearing in the timeline on the left
 */
define("TIMELINE_LIMIT", 50);

/**
 * The factor the SOLR score will be multiplied with in the default
 * algorithm. This is needed for the total score calculation.
 */
define("FACTOR_SCORE", 0.8);

/**
 * The factor the tag occurance will be multiplied with in the default
 * algorithm. This is needed for the total score calculation.
 */
define("FACTOR_COUNT", 0.2);


/**
 * Location for the temporary json file that will be created if update.php is 
 * called. This is needed for updating the Solr index
 */
define("TMP_LOCATION", "/tmp/tmp_outut.json");

/**
 * Location for logs that are made by update.php
 */
define("LOG_LOCATION", "/tmp/hashomender.log");

/**
 * How many times this function will fetch more tags if the final amount is not
 * reached yet
 */
define("AUTOCOMPLETE_TIMEOUT", 10);

/**
 * How many tags will be fetched at once
 */
define("AUTOCOMPLETE_FETCH_SIZE", 20);


define("TIME_LOG_PATH", "/tmp/");

$modules = array(
    'queryConstructor' => 'defaultQueryConstructor',
    'fetcher' => 'defaultFetcher',
    'extractor' => 'defaultExtractor',
    'sorter' => 'defaultSorter',
    'filter' => 'defaultFilter'
);

?>
