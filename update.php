<?php

/**
 * this file can be used for automatic index updates. It gets the path to a json
 * file as an argument, flattens it and then sends it to Solr for an index
 * update. 
 */

require_once 'flatten.php';
require_once 'config.php';

if($argc != 2){
    print("usage: php update.php inputfile.json\n");
    exit();
}

$filepath = $argv[1];
if(!file_exists($filepath)){
    print("file $filepath not found\n");
    exit();
}


$tmp_file = TMP_LOCATION;
parseToJSON($filepath, $tmp_file, 0);
if(!file_exists($tmp_file)){
    print("unable to write to $tmp_file\n");
    exit();
}

$log = array();
$cmd = "curl 'http://localhost:8983/solr/update/json?commit=true' --data-binary @$tmp_file -H 'Content-type:application/json'";
exec($cmd,$log);
$last = $log[count($log)-1];

// if the operation is successful, a xml file will be returned that ends with
// the string "</response>". otherwise, a html file will be returned that does
// not contain the word "</response>".
if(strpos($last,"</response>") === FALSE){
    log_error($log);
    print("an error has occurred, please see ".LOG_LOCATION." for more information\n");
    print("You may also inspect the temporary json file ".TMP_LOCATION." for structure errors.\n");
} else {
    // deletes the tmp file
    unlink($tmp_file);
}


/**
 * writes an array to a file, separating the elements by a newline.
 * @param string[] $log
 */
function log_error($log){
    $lp = fopen(LOG_LOCATION, "w");
    if($lp){
        fwrite($lp, implode("\n",$log));
        fclose($lp);
    }
}
?>