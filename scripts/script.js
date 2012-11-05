// currently selected recomended hashtag
var selection = 0;

// list of currently recommended tags
var tagList;


var recommendation_time_counter = 0;
var recommendation_time_value = 0;

var autocompleteList;
var autocompleteSelection = 0;

var autocomplete_time_counter = 0;
var autocomplete_time_value = 0;


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
27, // ESCAPE
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
        var lastw = selectedWord($('#text').val());
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
        } else if (e.which == 27){ // ecsape closes tooltip
            hideTooltip();
        }
    } else if($('*:focus').attr('id') == 'list'){ // Recommendation
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

function autocompleteTag(tag){
    var text = $('#text').val();
 //   var tag = autocompleteList[autocompleteSelection];
    var caret = $('#text').caret().start;
    
    var start = caret-1;
    var end = caret;
    while(text.charAt(start) != ' ' && start > 0){
        start--;
    }
    while(text.charAt(end) != ' ' && end < text.length){
        end++;
    }
    var part1 = $.trim(text.substring(0,start));
    var part2 = $.trim(text.substring(end));
    if(part1.length >= 1)
        part1 += " ";
    return part1 + tag + " " + part2;
}

function insertTagIntoText(tag){
    var old_tweet = $('#text').val();

    // check if we are inserting a recommendation or an autocompletion. in the
    // latter case, we should complete the word rather than replace it
    var lastw = selectedWord(old_tweet);
    var new_tweet = '';
    if(lastw.charAt(0) == '#'){
        new_tweet = autocompleteTag(tag);
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
    }
    var pos = $('#text').val().length;
    $('#text').setCursorPosition(pos);
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
    var lastw = selectedWord();

    // following lines check if the user is currently typing a 
    // hashtag. after 3 symbols, the system collects all hashtags with the same
    // letters already typed and displays them to the user. Here, no
    // recommendation is being used, just a plain dictionary-style
    // autocompletion.
    if(lastw.charAt(0) == '#' && lastw.length > 3){
        mode=AUTOCOMPLETE;
        var start_autocomplete = new Date().getTime();
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
            autocomplete_time_counter++;
            autocomplete_time_value += ((new Date().getTime()) - start_autocomplete );
        });
    } else {
        hideTooltip();
        mode=RECOMMEND;
        // Following lines are for the actual recommendation.
        start_recommendation = new Date().getTime();
        var httpReq = $.ajax({
            url: "recommend.php",
            type: "POST",
            data: {
                msg: val
            }
        });

        httpReq.done(function(msg){
            if(!msg){
                set_error("No response from Solr");
                return;
            } else if(msg.length == 2 && msg[0] == "Error"){
                set_error(msg[1]);
                return;
            }
            clear_error();
            // clear old entries
            tagList = [];
		
            // print all items into list
            for(var i=0;i<msg.length;i++){
                tagList.push("#"+msg[i]);
            }
            selection = 0;
            $('#text').focus();
            drawList();
            recommendation_time_counter++;
            recommendation_time_value += ((new Date().getTime())-start_recommendation);
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

function selectedWord(){
    var text = $('#text').val();
    var caret = $('#text').caret().start;
    
    if(caret == 0){
        return "";
    }
    
    for(var i=caret-1; i>=0; i--){
        if(i == 0)
            return $.trim(text.substring(i, caret));
        else if (text.charAt(i) == ' ')
            return $.trim(text.substring(i, caret));
    }    
    return "";
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
    $('#timeline').html("");
    $('#timeline').css('background', '#eee url(images/loading.gif) no-repeat center center');
    $.post("timeline.php",
        function(response){
            timeline = response;
            $('#timeline').css('background', '#eee');
            $('#timeline').html("");
            for(var i=0;i<response.length;i++){
                var reg_exUrl = /(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/;
                var linked = response[i].text.replace(reg_exUrl, function(url){
                    return '<a href="'+url+'">'+url+'</a>';
                });
                $('#timeline').append(
                    '<div class="past-tweet">'
                    + '<img src="'+response[i].image+'"/>'
                    + '<div class="date">'+response[i].date+'</div>'
                    +'<div class="timeline-user">'+response[i].name+'</div>'
                    +linked+'<br>'
                    +'<input type="submit" class="button" value="Retweet" onClick="retweet('+i+');"/>'
                    +'<input type="submit" class="button" value="Reply" onClick="reply('+i+');"/></div>');
            }
			
        });
    console.log("AUTOCOMPLETION: "+ (autocomplete_time_value/autocomplete_time_counter));
    console.log("RECOMMENDATION: "+ (recommendation_time_value/recommendation_time_counter));
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


$(document).ready(function(){
    $('#text').bind("keyup", findRecommendedHashtags);
    $(document).bind("keydown", keyHandler);
    setEventHandlers();
    findRecommendedHashtags();
    getTimeline();
    $(window).resize(function(){
        $('#tooltip-wrapper').css('left', $('#list').offset().left-50 + 'px');
        $('#tooltip-wrapper').css('top', $('#list').offset().top+30 + 'px');
    });
    $(window).resize();
});

function set_error(msg){
    $('#error').html(msg);
    $('#error').show();
}

function clear_error(){
    $('#error').hide();
}
