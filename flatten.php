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
 * This file flattens the hierarchical tweet json file delivered by the 
 * mongoDB database. Flat structure is needed for SOLR, which can't handle
 * hierarchies in its index.
 */

/**
 * parses a json tweet input file and writes to a flattened xml file.
 * The resulting XML will have following structure:<br>
 * <code>
  &lt;add&gt;<br>
  &emsp;&lt;doc&gt;<br>
  &emsp;&emsp;&lt;field name="id"&gt;0&lt;/field&gt;<br>
  &emsp;&emsp;&lt;field name="tweet"&gt;text #hashtag&lt;/field&gt;<br>
  &emsp;&emsp;&lt;field name="hashtags"&gt;hashtag&lt;/field&gt;<br>
  &emsp;&lt;/doc&gt;<br>
  &emsp;&lt;doc&gt;<br>
  &emsp;&emsp;&lt;field name="id"&gt;1&lt;/field&gt;<br>
  &emsp;&emsp;&lt;field name="tweet"&gt;no tags&lt;/field&gt;<br>
  &emsp;&emsp;&lt;field name="hashtags"&gt;&lt;/field&gt;<br>
  &emsp;&lt;/doc&gt;<br>
  ...<br>
  &lt;/add&gt;<br>
 * </code>
 * 
 * @param string $input
 *      The input file path
 * @param string $output
 *      The output file path
 * @param int $limit optional
 *      limits the output to a certain number of tweets (useful for testing)
 *      if this parameter is omitted, all input tweets are processed.
 *      
 */
function parseToXML($input, $output, $limit = 0) {
    // input
    $input_file = fopen($input, "r") or die("Could not open file");
    // output
    $output_file = fopen($output, "w") or die("Could not open result file");
    if ($input_file && $output_file) {
        fputs($output_file, "<add>\n");
        $id = 0;
        while (!feof($input_file) && ($limit == 0 || $id < $limit)) {
            $buffer = fgets($input_file);
 
            $tweet = json_decode($buffer);
            // some lines may be faulty
            if (!isset($tweet)) {
                continue;
            }

            fputs($output_file, "\t<doc>\n");
            fputs($output_file, "\t\t<field name=\"id\">$tweet->id_str</field>\n");
            fputs($output_file, "\t\t" . '<field name="tweet">' . $tweet->text . '</field>' . "\n");
            fputs($output_file, "\t\t" . '<field name="hashtags">');
            foreach ($tweet->entities->hashtags as $tag) {
                fputs($output_file, $tag->text . " ");
            }
            fputs($output_file, "</field>\n");
            fputs($output_file, "\t</doc>\n");
            $id++;
        }
        fputs($output_file, "</add>\n");
        fclose($input_file);
        fclose($output_file);
        print("Added $id tweets to result file\n");
    }
}

/**
 * parses a json tweet input file and writes to a flattened json file.
 * The resulting JSON will have following structure:
 * <code><br>
 *  [<br>
 *  &emsp;{"id":0, "tweet":"text #hashtag", "hashtags":"hashtag"},<br>
 *  &emsp;{"id":1, "tweet":"no tags", "hashtags":""}<br>
 * ...<br>
 *  ]<br>
 * </code>
 * 
 * @param string $input
 *      The input file path
 * @param string $output
 *      The output file path
 * @param int $limit optional
 *      limits the output to a certain number of tweets (useful for testing)
 *      if this parameter is omitted, all input tweets are processed.
 *      
 */
function parseToJSON($input, $output, $limit = 0) {
    $input_file = fopen($input, "r") or die("Could not open file");
    $output_file = fopen($output, "w") or die("Could not write result file");
    if ($input_file && $output_file) {
        // result is an array, but it might not fit in the memory -> step
        // through the original data and writing one array element at a time.
        // in JSON, arrays start with a '[' symbol.
        fputs($output_file, "[\n");
        $id = 0;
        while (!feof($input_file) && ($limit == 0 || $id < $limit)) {
            // fgets reads one line from a file pointer
            // the MongoDB data is stored in lines (one tweet = one line)
            $buffer = fgets($input_file);
            $output = array();

            $tweet = json_decode($buffer);
            // some lines may be faulty
            if (!isset($tweet)) {
                continue;
            }

            $output['id'] = $tweet->id_str;
            $output['tweet'] = $tweet->text;
            $output['hashtags'] = '';
            foreach ($tweet->entities->hashtags as $tag) {
                $output['hashtags'] .= $tag->text . " ";
            }

            // remove trailing space
            $output['hashtags'] = trim($output['hashtags']);

            // the comma is necessary for the syntax of the array (remember:
            // the array is too big to print as a whole, so just single elements
            // are printed), the newline is optional to better read the result
            fputs($output_file, json_encode($output) . ",\n");
            $id++;
        }
        // this moves the file pointer 2 charcters back (linebreak and a 
        // comma) => SOLR does not accept a trailing comma
        fseek($output_file, -2, SEEK_CUR);
        // close the array 
        fputs($output_file, "]");
        // close file pointers
        fclose($input_file);
        fclose($output_file);
        print("Added $id tweets to result file\n");
    }
}

?>
