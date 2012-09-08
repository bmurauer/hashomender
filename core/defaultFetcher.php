<?php

/**
 * Default implementation of the fetching class: 
 *
 * @author benjamin
 */
class defaultFetcher implements iFetcher{
	public function getTweets($query) {
		$req = new HttpRequest($query, HTTP_METH_POST);
		$result = json_decode($req->send()->getBody());
		return $result;
	}
}

?>
