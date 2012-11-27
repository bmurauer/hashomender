<?php
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
