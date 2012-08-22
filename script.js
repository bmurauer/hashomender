
// TODO: double spaces won't work for recommendation
// TODO: jquery's autocomplete is a mess

// currently selected recomended hashtag
var selection = 0;

// list of currently recommended tags
var tagList;

// elements that might get focus
var focusListeners = {"listeners":[
	{"selector":"#text"},
	{"selector":"#list"},
	{"selector":"#submit"}
]};

new function($) {
	$.fn.setCursorPosition = function(pos) {
		if ($(this).get(0).setSelectionRange) {
			$(this).get(0).setSelectionRange(pos, pos);
		} else if ($(this).get(0).createTextRange) {
			var range = $(this).get(0).createTextRange();
			range.collapse(true);
			range.moveEnd('character', pos);
			range.moveStart('character', pos);
			range.select();
		}
	}
}(jQuery);

function setEventHandlers(){
	$('#text').focus(function(){
		// set caret to the end of the tweet
		var pos = $('#text').val().length;
		$('#text').setCursorPosition(pos);
	});
	$('#list').focus(function(){
		if(!tagList || tagList.length < 1){
			$('#submit').focus();
			return;
		}
		selection = 1;
		drawList();
	});
	$('#list').blur(function(){
		selection = 0;
		drawList();
	});
	$('#submit').blur(function(){
		$('#text').focus();
	});
	$('#list li.unselected').click(function(){
		console.log("bla");
	});
	$('h1').click(function(){
		console.log("blabb");
	});
}


function keyHandler(e){
	if($('*:focus').attr("id") == "list"){
		if (e.which == 40){ // DOWN, move in tag list
			selection ++;
			if(selection > tagList.length)
				selection -= tagList.length;
			drawList();
		} else if (e.which == 38){ // UP
			selection --;
			if(selection <= 0)
				selection += tagList.length;
			drawList();
		}	else if (e.which == 13){ // RETURN insert tag
			var tag = tagList[selection-1];
			insertTagIntoText(tag);
		}
	} else {
		findRecommendedHashtags();
	}
}

function insertTagIntoText(tag){
	var old_tweet = $('#text').val();
	// this value will be -1 if the tag is not contained in the text.
	// search here is case insensitive and ignores the hash symbol
	var tag_position = old_tweet.toLowerCase().indexOf(tag.substring(1).toLowerCase());
	var tag_length = tag.length;
	var new_tweet = "";
	
	// the tag is already contained in the tweet, with a hashtag.
	if( old_tweet.toLowerCase().indexOf(tag.toLowerCase()) !== -1){
		new_tweet = old_tweet;
	// tag was found in text, replace it with the one from the server,
	// including the hashtag
	} else if( tag_position !== -1){
		new_tweet = old_tweet.substring(0, tag_position);
		new_tweet += tag + " ";
		new_tweet += old_tweet.substring(tag_position + tag_length);
	// tag was not found, append it to tweet
	} else {
		new_tweet = old_tweet;
		var length = old_tweet.length;
		if(old_tweet.charAt(length-1) != ' '){
			new_tweet += ' ';
		} 
		new_tweet += tag;
	}
	$('#text').val(new_tweet);
	selection = 0;
	drawList();
	calcLength();
	$('#text').focus();
	findRecommendedHashtags();
}

function drawList(){
	$('#list').html("");
	for(var i=0;i<tagList.length;i++){
		if(i+1 != selection){
			$('#list').append('<li class="unselected">'+tagList[i]+'</li>');
		} else {
			$('#list').append('<li class="selected">'+tagList[i]+'</li>');
		}
	}
}

function findRecommendedHashtags() {
	var val = $("#text").val();
	calcLength(val);
	var lastw = lastWord(val);

	// following lines check if the user is currently typing a 
	// hashtag. after 3 symbols, the system collects all hashtags with the same
	// letters already typed and displays them to the user. Here, no recommendation
	// is being used, just a plain dictionary-style autocompletion.

	if(lastw.charAt(0) == '#' && lastw.length > 3){
		var hashReq = $.ajax({
			url: "tags.php",
			type: "POST",
			data: {lastWord: lastw.substring(1)}
		});
		
		hashReq.done(function(msg){
			$( "#text" ).autocomplete({
				minLength: 0,
				source: function( request, response ) {
					// delegate back to autocomplete, but extract the last term
					response( $.ui.autocomplete.filter(
						msg, extractLast( request.term ) ) );
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {
					var terms = split( this.value );
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.value );
					// add placeholder to get the space at the end
					terms.push( "" );
					this.value = terms.join( " " );
					return false;
				}
			});
		});
	}

	// Following lines are for the actual recommendation.

	var httpReq = $.ajax({
		url: "recommend.php",
		type: "POST",
		data: {msg: val}
	});

	httpReq.done(function(msg){
		// clear old entries
		tagList = [];
		
		// print all items into list
		for(var i=0;i<msg.length;i++){
			tagList.push(msg[i]);
		}
		drawList();
	});
}

function calcLength(val) {
	var result = $("#text").val();
	var remaining = 140 - result.length;
	
	if(remaining < 0){
		$("#counter").addClass("error");
		$("#submit").attr("disabled", true);
		$("#submit").addClass("disabled");
		$("#counter").html((remaining* -1)+" characters too much! ("+result.length+" of 140)");
	} else {
		$("#counter").removeClass("error");
		$("#submit").attr("disabled", false);
		$("#submit").removeClass("disabled");
		$("#counter").html(remaining+" characters remaining ("+result.length+" of 140)");
	}
}

function lastWord(text){
	var last = text.trim().lastIndexOf(" ");
	return text.substring(last).trim();
}

function split( val ) {
	return val.split( / \s*/ );
}
function extractLast( term ) {
	return split( term ).pop();
}
function checkAndSend(){
	var text = $('#text').val();
	$.post("send.php",{
		status: text}, function(response){
		if(response == "OK"){
			$('#text').val("");
			tagList=[];
			drawList();
			setTimeout("getTimeline()", 3000);
		} else {
			$('#error').val('There has been some trouble tweeting your message!');
		}
	});
}

function getTimeline(){
	$('#timeline').html('<img src="loading.gif"/>');
	$.post("timeline.php",
		function(response){
			$('#timeline').html('<input type="submit" class="button" value="refresh" onclick="getTimeline()" tabindex="-1"/><br>');
			for(var i=0;i<response.length;i++){
				$('#timeline').append('<div class="past-tweet"><div class="date">'+response[i].date+'</div>'+response[i].text+'</div>');
			}
	});
}
