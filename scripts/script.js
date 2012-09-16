// currently selected recomended hashtag
var selection = 0;

// list of currently recommended tags
var tagList;

var autocompleteList;
var autocompleteSelection = 0;

var mode;

var timeline;
var popup = false;
var AUTOCOMPLETE = 0;
var RECOMMEND = 1;

var ignoredKeycodes = [
38, // DOWN
40, // UP
13, // ENTER
9,  // TAB
];

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
	/*$('#text').focus(function(){
		// set caret to the end of the tweet
		var pos = $('#text').val().length;
		$('#text').setCursorPosition(pos);
	});
	*/
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
}
/**
 * prevents default functionality of UP, DOWN and RETURN
 *
 */
function preventDefaults(e){
	if(e.which == 40 || e.which == 38 || e.which == 13){
		e.preventDefault();	
	}
}	

function keyHandler(e){
	if(mode == AUTOCOMPLETE){
		var lastw = lastWord($('#text').val());
		preventDefaults(e);
		if(e.which == 40){
			autocompleteSelection++;
			if(autocompleteSelection >= autocompleteList.length){
				autocompleteSelection -= autocompleteList.length;
			}
			drawTooltip(lastw.length);
		} else if (e.which == 38){
			autocompleteSelection--;
			if(autocompleteSelection < 0){
				autocompleteSelection += autocompleteList.length;
			}
			drawTooltip(lastw.length);
		} else if (e.which == 13){
			var tag = autocompleteList[autocompleteSelection];
			if(tag && tag != ''){
				insertTagIntoText(tag);
			} else {
				$('#text').val($('#text').val() + ' ');
			}
			hideTooltip();
		}
	} else if($('*:focus').attr('id') == 'list'){
		preventDefaults(e);
		if (e.which == 40){ // DOWN, move in tag list
			selection ++;
			if(selection > tagList.length)
				selection -= tagList.length;
		} else if (e.which == 38){ // UP
			selection --;
			if(selection <= 0)
				selection += tagList.length;
		}	else if (e.which == 13){ // RETURN insert tag
			var tag = tagList[selection-1];
			if(tag && tag != ""){
				insertTagIntoText(tag);
			}
		}
		drawList();
	}
		
}

function removeLastWord(text){
	var index = text.lastIndexOf("#");
	return text.substring(0, index);
}

function insertTagIntoText(tag){
	var old_tweet = $('#text').val();

	// check if we are inserting a recommendation or an autocompletion. in the
	// latter case, we should complete the word rather than replace it
	var lastw = lastWord(old_tweet);
	var new_tweet = '';
	if(lastw.charAt(0) == '#'){
		new_tweet = removeLastWord(old_tweet) + tag + ' ';
	} else {
		// this value will be -1 if the tag is not contained in the text.
		// search here is case insensitive and ignores the hash symbol
		var tag_position = old_tweet.toLowerCase().indexOf(tag.substring(1)
			.toLowerCase());
		var tag_length = tag.length;
		new_tweet = "";
	
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
			new_tweet += tag + " ";
		}
		var pos = $('#text').val().length;
		$('#text').setCursorPosition(pos);
	}	
	$('#text').val(new_tweet);
	calcLength();
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

	// this handler hast to be installed every time
	$('li').mousedown(function(){
		var tag = $(this).html();
		$('#list').html('');
		insertTagIntoText(tag);
	});
}

function findRecommendedHashtags(e) {
	if(e && ignoredKeycodes.indexOf(e.which) != -1){
		return;	
	}
	var val = $("#text").val();
	calcLength();
	var lastw = lastWord(val);

	// following lines check if the user is currently typing a 
	// hashtag. after 3 symbols, the system collects all hashtags with the same
	// letters already typed and displays them to the user. Here, no
	// recommendation is being used, just a plain dictionary-style
	// autocompletion.
	if(lastw.charAt(0) == '#' &&
		lastw.length > 3 && val.charAt(val.length-1) != ' '){
		mode=AUTOCOMPLETE;
		var hashReq = $.ajax({
			url: "tags.php",
			type: "POST",
			data: {
				lastWord: lastw.substring(1)
			}
		});
		
		hashReq.done(function(msg){
			autocompleteList = msg;
			// print all items into list
			if(!tooltip)
				autocompleteSelection = 0;
			drawTooltip(lastw.length);
		});
	} else {
		hideTooltip();
		mode=RECOMMEND;
		// Following lines are for the actual recommendation.
		var httpReq = $.ajax({
			url: "recommend.php",
			type: "POST",
			data: {
				msg: val
			}
		});

		httpReq.done(function(msg){
				console.log(msg);
			// clear old entries
			tagList = [];
		
			// print all items into list
			for(var i=0;i<msg.length;i++){
				tagList.push("#"+msg[i]);
			}
			selection = 0;
			$('#text').focus();
			drawList();
		});
	}

}

function drawTooltip(length){
	$('#tooltip').html("");
	if(autocompleteList.length < 1){
		hideTooltip();
		return;
	}
	if(!tooltip){
		autocompleteSelection = 0;
	}
	for(var i=0;i<autocompleteList.length;i++){
		var strong = '';
		var rest = autocompleteList[i];
		if(length !== 0){
			strong = autocompleteList[i].substr(0, length);
			rest = autocompleteList[i].substr(length);
		}
		var elementClass = (i == autocompleteSelection)? 'tip selected':'tip';
		$('#tooltip').append(
			'<div class="'+elementClass+'"><strong>'+strong+'</strong>'+rest+'<br></div>'
		);
	}
	if(tooltip !== true){
		$('#tooltip-wrapper').show(300);
		tooltip = true;
	}
}
function hideTooltip(){
	if($('#tooltip-wrapper').is(":visible")){
		$('#tooltip-wrapper').hide(200);
		tooltip = false;
		autocompleteSelection = 0;
	}
}

function calcLength() {
	var result = $("#text").val();
	var remaining = 140 - result.length;
	
	if(remaining < 0){
		$("#counter").addClass("error");
		$("#submit").attr("disabled", true);
		$("#submit").addClass("disabled");
		$("#counter").html((remaining* -1)+" characters too much! ("
			+result.length+" of 140)");
	} else {
		$("#counter").removeClass("error");
		$("#submit").attr("disabled", false);
		$("#submit").removeClass("disabled");
		$("#counter").html(remaining+" characters remaining ("
			+result.length+" of 140)");
	}
}

function lastWord(text){
	if(text.charAt(text.length-1) == " "){
		return "";
	}
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
		status: text
	}, function(response){
		if(response == "OK"){
			$('#text').val("");
			tagList=[];
			drawList();
			setTimeout("getTimeline()", 3000);
		} else {
			$('#error')
			.val('There has been some trouble tweeting your message!');
		}
	});
}

function getTimeline(){
	$('#timeline').html('<img src="images/loading.gif"/>');
	$.post("timeline.php",
		function(response){
			timeline = response;
			$('#timeline').html('<input type="submit" class="button" value="refresh" onclick="getTimeline()" tabindex="-1"/><br>');
			for(var i=0;i<response.length;i++){
                console.log(response);
                var reg_exUrl = /(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/;
                var linked = response[i].text.replace(reg_exUrl, function(url){
                    return '<a href="'+url+'">'+url+'</a>';
                });
				$('#timeline').append('<div class="past-tweet"><div class="date">'+response[i].date+'</div>'
                    +'<div class="timeline-user">'+response[i].name+'</div>'
                    +linked+'<br>'
                    +'<input type="submit" class="button" value="Retweet" onClick="retweet('+i+');"/>'
					+'<input type="submit" class="button" value="Reply" onClick="reply('+i+');"/></div>');
			}
			
		});
}
function reply(i){
	var text = '@'+timeline[i].screen_name + ': ';
	$('#text').val(text);
	findRecommendedHashtags(null);
	var pos = $('#text').val().length-1;
	$('#text').setCursorPosition(pos);
}
function retweet(i){
	var text = 'RT @'+timeline[i].screen_name + ': ' + timeline[i].text;
	$('#text').val(text);
	findRecommendedHashtags(null);
	var pos = $('#text').val().length;
	$('#text').setCursorPosition(pos);
}