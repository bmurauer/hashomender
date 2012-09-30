<?php

/**
 * Default implementation of the fetching class
 *
 * @author benjamin
 */
class defaultFetcher implements iFetcher {

    /**
     * fetches the json result object from the Solr server
     * @param string $query
     *      the complete query for the server, it will be sent unmodified
     * @return stdObj
     *      a parsed json object of the result
     */
    public function getTweets($query) {
        try {
            $req = new HttpRequest($query, HTTP_METH_POST);
            $result = json_decode($req->send()->getBody());
            return $result;
        } catch (Exception $e) {
            print(json_encode(array("Error", "Solr server not found, is it running?")));
            exit();
        }
    }

}

?>
