<html>
	<head>
		<title>Twitter Client with automatic Hashtag recommodation</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script type="text/javascript" src="jquery.js"></script>
		<script type="text/javascript" src="jquery-ui.js"></script>
		<script type="text/javascript" src="jquery-caret.js"></script>
		<script type="text/javascript" src="script.js"></script>
	</head>
	<body>
			<div class="caption">
				<h1>Hash-O-Mender</h1>
		<div class="username">logged in as: <?php echo $user->screen_name;?>|<a href="logout.php" tabindex="-2">logout</a></div>
				
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
				<div id="dic-list" tabindex="4" style="display: none;">
				</div>
				<input id="hidden-text" type="text" style="display: block;"/>
			</div>
		</div>
		<div id="timeline">
		</div>
	</body>
	<script type="text/javascript">
		$(document).bind("keydown", keyDownHandler);
		$('#text').bind("keyup", findRecommendedHashtags);
		$('#list').bind("keyup", keyHandler);
		setEventHandlers();
		findRecommendedHashtags();
		getTimeline();
	</script>
</html>
