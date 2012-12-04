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


include_once 'EpiTwitter/EpiCurl.php';
include_once 'EpiTwitter/EpiOAuth.php';
include_once 'EpiTwitter/EpiTwitter.php';
include_once 'config.php';

$Twitter = new EpiTwitter(CONSUMER_KEY, CONSUMER_SECRET);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="styles/style.css" />
	</head>
	<body>
	<div class="caption">
		<img src="images/header.png"/>
	</div>
	<div class="wrapper-start">
		You don't seem to have logged in yet. Do you want to do so now?
		<div class="center">
			<a href="<?php echo $Twitter->getAuthenticateUrl();?>">Sign in!</a>
		</div>
	</div>
	</body>
</html>
