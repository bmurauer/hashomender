<?php

function check_algorithms($verbose) {
	$folder_path = "algorithms";
	if (!is_dir($folder_path)) {
		if($verbose)
			error_print('no algorithm directory found. Read the README for more info.');
		return;
	} else {
		$contents = scandir($folder_path);
		foreach ($contents as $file) {
			if (!endsWith($file, ".php"))
				continue;
			include_once($folder_path . '/' . $file);
			if (!function_exists("extract_tags")) {
				if($verbose)
			error_print('scanned php file did not have an "extract_tags"
				function. Fallback to default_algorithms.php. Read the README 
				for more informations.');
				use_default;
				return;
			}
			if (!function_exists("rank_tags")) {
				if($verbose)
			error_print('scanned php file did not have a "rank_tags" function.
				Fallback to default_algorithms.php. Read the README for more
				informations.');
				use_default;
				return;
			}
			if($verbose)
				print("using file $file for functions extract_tags and rank_tags.");
			return;
		}
	}
}

function use_default() {
	include_once('default_algorithm.php');
}

function error_print($string) {
	print('<p class="error">' . $string . '</p>');
}

// http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
function endsWith($haystack, $needle) {
	$length = strlen($needle);
	if ($length == 0) {
		return true;
	}

	return (substr($haystack, -$length) === $needle);
}

?>
