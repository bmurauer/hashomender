<?php
include_once 'EpiTwitter/EpiCurl.php';
include_once 'EpiTwitter/EpiOAuth.php';
include_once 'EpiTwitter/EpiTwitter.php';
include_once 'config.php';

$Twitter = new EpiTwitter(CONSUMER_KEY, CONSUMER_SECRET);

?>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="styles/style.css" />
	</head>
	<body>
	<div class="caption">
		<h1>Hash-O-Mender</h1>
	</div>
	<div class="wrapper-start">
		You don't seem to have logged in yet. Do you want to do so now?
		<div class="center">
			<a href="<?php echo $Twitter->getAuthenticateUrl();?>">Sign in!</a>
		</div>
	</div>
	</body>
</html>
