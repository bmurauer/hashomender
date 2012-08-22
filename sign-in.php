<?php
include 'EpiCurl.php';
include 'EpiOAuth.php';
include 'EpiTwitter.php';
include 'keys.php';

$Twitter = new EpiTwitter($consumerKey, $consumerSecret);

?>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css" />
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
