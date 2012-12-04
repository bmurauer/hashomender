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
