var recommendation_count = 0;
var recommendation_sum = 0;
var autocompletion_count = 0;
var autocompletion_sum = 0;


function getRandomWords(amount, tagged){
    var randomwords = ["olympic", "earthquake", "new", "york", "boston", 
    "music", "stuff", "legend", "avengers", "food", "orange", "yellow", "kim"];
    var result = new Array(amount);
    for(var i=0; i<amount; i++){
        var rand = Math.round(Math.random()*randomwords.length);        
        var toPush = (tagged)?'#'+randomwords[rand]:randomwords[rand];
        result.push(toPush);
    }
    return result.join(" ");
}

function benchmark(){
    var amount = 100;
    for(var i=0; i<amount; i++){
        $('#text').val(getRandomWords(15, false));
        var start = new Date().getTime();
        findRecommendedHashtags();
        recommendation_count++;
        recommendation_sum += (new Date().getTime() - start);
    }
       
    for(var i=0; i<amount; i++){
    $('#text').val(getRandomWords(15, true));
        var start = new Date().getTime();
        findRecommendedHashtags();
        autocompletion_count++;
        autocompletion_sum += (new Date().getTime() - start);
    }
    
    console.log("average recommendation time: "+recommendation_sum/recommendation_count+" ms");
    console.log("average autocompletion time: "+autocompletion_sum/autocompletion_count+" ms");
}
