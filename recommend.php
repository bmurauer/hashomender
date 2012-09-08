<?php
  include_once('core/interfaces.php');
  include_once('core/defaultQueryConstructor.php');
  include_once('core/defaultFetcher.php');
  include_once('core/defaultExtractor.php');
  include_once('core/defaultSorter.php');
  include_once('core/defaultFilter.php');

	$queryConstructor = new defaultQueryConstructor();
	$fetcher = new defaultFetcher();
	$extractor = new defaultExtractor();
	$sorter = new defaultSorter();
	$filter = new defaultFilter();
	
	$query = $queryConstructor->getRecommendQuery($_POST);
	$tweets = $fetcher->getTweets($query);
	$tags = $extractor->extractTags($tweets);
	$sorted_tags = $sorter->sortTags($tags);
	$filtered_tags = $filter->filterTags($sorted_tags);
	
	// sending back json, so we have to modify the headers.
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	
	// print back result
	print(json_encode($filtered_tags));	
?>