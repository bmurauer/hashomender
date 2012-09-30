<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf8">
		<title>Twitter Client with automatic Hashtag recommodation</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css" />
		<script type="text/javascript" src="scripts/jquery.js"></script>
		<script type="text/javascript" src="scripts/jquery-ui.js"></script>
		<script type="text/javascript" src="scripts/jquery-caret.js"></script>
		<script type="text/javascript" src="scripts/script.js"></script>
	</head>
	<body>
		<div class="caption">
			<h1>Hash-O-Mender</h1>
			<div class="username">
                logged in as: <?php echo $user->screen_name; ?>|
                <a href="logout.php" tabindex="-2">logout</a>
            </div>
			<div id="error"></div>
		</div>

		<div class="wrapper">
			<div class="content">
				<h2>Input Text:</h2>
				<textarea rows="4" width="200" id="text" name="message" tabindex="1"></textarea><br>
				<span id="counter"></span>
				<input class="button" type="submit" value="Send!" id="submit" onclick="checkAndSend()" tabindex="3"/>
			</div>
			<div class="taglist">	
				<h2>Tags recommended</h2>
				<div id="list" tabindex="2">
				</div>
			</div>
		</div>
		<div id="timeline">
		</div>
		<div id="tooltip-wrapper">
			<img src="images/arrow.png"/>
			<div id="tooltip">
			</div>
		</div>
	</body>
</html>
