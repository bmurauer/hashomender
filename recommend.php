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


include_once('time.php');
$start = microtime(true);
// sending back json, so we have to modify the headers.
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

/**
 * this file includes the main process of recommending hashtags, depending on 
 * which modules are used.
 */

global $modules;

include_once('config.php');
include_once('core/interfaces.php');
include_once('core/'.$modules['queryConstructor'].'.php');
include_once('core/'.$modules['fetcher'].'.php');
include_once('core/'.$modules['extractor'].'.php');
include_once('core/'.$modules['sorter'].'.php');
include_once('core/'.$modules['filter'].'.php');

$queryConstructor = new $modules['queryConstructor'];
$fetcher = new $modules['fetcher'];
$extractor = new $modules['extractor'];
$sorter = new $modules['sorter'];
$filter = new $modules['filter'];

$elements = array($queryConstructor, $fetcher, $extractor, $sorter, $filter);
$interfaces = array('iQueryConstructor', 'iFetcher', 'iExtractor', 'iSorter', 'iFilter');

checkInterfaces($elements, $interfaces);

$query = $queryConstructor->getRecommendQuery($_POST);
$tweets = $fetcher->getTweets($query);
$tags = $extractor->extractTags($tweets);
$sorted_tags = $sorter->sortTags($tags);
$filtered_tags = $filter->filterTags($sorted_tags);


// print back result
$end = microtime(true);
$time = $end - $start;
logTime($time*1000, "recommend");
print(json_encode($filtered_tags));


function checkInterfaces($elements, $interfaces){
    for($i=0;$i<count($elements);$i++){
        if(!$elements[$i] instanceof $interfaces[$i]){
            print(json_encode(array("Error", "$interfaces[$i] is not impemented.")));
            exit();
        }
    }
}


?>

