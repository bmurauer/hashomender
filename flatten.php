<?php

/**
 * This file flattens the hierarchical tweet json file delivered by the 
 * mongoDB database. Flat structure is needed for SOLR, which can't handle
 * hierarchies in its index.
 */

if($argc != 5){
    error();
}

$input = $argv[1];
$output = $argv[2];
$format = $argv[3];
$limit = intval($argv[4]);

switch($format){
    case 'xml': parseToXML($input, $output, $limit);
        break;
    case 'json': parseToJSON($input, $output, $limit);
        break;
    default:
        error();
}


function error(){
    print('usage: php flatten.php <input.json> <output> <format> <limit>');
    print("\t<input.json>: the input file holding tweets");
    print("\t<output>: your output file");
    print("\t<format>: desired format (either 'xml' or 'json')");
    print("\t<limit>: limits the amount of output tweets");
    exit("invalid parameters");
}

function parseToXML($input, $output, $limit) {
    // input
    $fp = fopen($input, "r") or die("Could not open file");
    // output
    $re = fopen($output, "w") or die("Could not open result file");
    if ($fp && $re) {
        fputs($re, "<add>\n");
        $id = 0;
        while (!feof($fp) && ($limit == 0 || $id < $limit)) {
            $buffer = fgets($fp);
            $tweet = json_decode($buffer);
            
            // !feof($fp) is not enough, prevent 
            if(!isset($tweet)){
                print("stopped after $id tweets.");
                continue;
            }
            
            fputs($re, "\t<doc>\n");
            fputs($re, "\t\t<field name=\"id\">$id</field>\n");
            fputs($re, "\t\t" . '<field name="tweet">' . $tweet->text . '</field>' . "\n");
            fputs($re, "\t\t" . '<field name="hashtags">');
            foreach ($tweet->entities->hashtags as $tag) {
                fputs($re, $tag->text . " ");
            }
            fputs($re, "</field>\n");
            fputs($re, "\t</doc>\n");
            $id++;
        }
        fputs($re, "</add>\n");
        fclose($fp);
        fclose($re);
        print("Added $id tweets to result file\n");
    }
}

function parseToJSON($input, $output, $limit) {
    // input
    $fp = fopen($input, "r") or die("Could not open file");
    // output
    $re = fopen($output, "w") or die("Could not write result file");
    if ($fp && $re) {
        // result is an array, but it might not fit in the memory -> step
        // through the original data and writing one array element at a time.
        // in JSON, arrays start with a '[' symbol.
        fputs($re, "[");
        $id = 0;
        while (!feof($fp) && ($limit == 0 || $id < $limit)) {
            // fgets reads one line from a file pointer
            // the MongoDB data is stored in lines (one tweet = one line)
            $buffer = fgets($fp);
            $tweet = json_decode($buffer);
            $output = array();
            $output['id'] = $id;
            
            // !feof($fp) is not enough, prevent 
            if(!isset($tweet)){
                print("stopped after $id tweets.");
                continue;
            }
            
            $output['tweet'] = $tweet->text;
            $output['hashtags'] = '';
            foreach ($tweet->entities->hashtags as $tag) {
                $output['hashtags'] .= $tag->text . " ";
            }
            // remove trailing space
            $output['hashtags'] = trim($output['hashtags']);
            fputs($re, json_encode($output)."\n");
            $id++;
        }
        // this removes the last comma from the array => SOLR does not accept 
        // a trailing comma
        fseek($re, -1, SEEK_CUR);
        // close the array
        fputs($re, "]");
        // close file pointers
        fclose($fp);
        fclose($re);
        print("Added $id tweets to result file\n");
    }
}

?>
