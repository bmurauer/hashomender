<?php
	$limit = 10;
	$fp = fopen("tweets/tweets_small.json", "r") or die("Could not open file");
	$re = fopen("tweets/result.xml", "w") or die("Could not open result file");
	if($fp && $re){
		fputs($re, "<add>");
		$id = 0;
		while(!feof($fp) && ($limit == 0 || $id < $limit)) {
			$buffer = fgets($fp);
			$tweet = json_decode($buffer);
			fputs($re, "\t<doc>\n");
			fputs($re, "\t\t<field name=\"id\">$id</field>\n");
			fputs($re, "\t\t".'<field name="tweet">'.$tweet->text.'</field>'."\n");
			fputs($re, "\t\t".'<field name="hashtags">');
			foreach($tweet->entities->hashtags as $tag){
				fputs($re, $tag->text." ");
			}
			fputs($re, "</field>\n");
			fputs($re,"\t</doc>\n");
			$id++;
		}
		fputs($re, "</add>");
		fclose($fp);
		fclose($re);
		print("Added $id tweets to result file");
	}
	
?>
