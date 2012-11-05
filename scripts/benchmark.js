var tries = 100;
var benchmarkRuns = 3*tries;
var start_a = new Date().getTime();
var start_r = new Date().getTime();

var a_count = 0;
var a_sum = 0;
var r_count = 0;
var r_sum = 0;


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

function benchmark_r(){
    benchmarkRuns++;
    $('#text').val(getRandomWords(15, false));
    findRecommendedHashtags();
}

function benchmark_a(){
    benchmarkRuns++;
    $('#text').val(getRandomWords(1,true));
    findRecommendedHashtags();

}

function bench_callback_r(){
	var time = new Date().getTime() - start_r;
	r_sum += time;
	r_count++;
	if(benchmarkRuns < tries){
		benchmark_r();
	} else {
		console.log("r finished. sum:"+r_sum+" count:"+r_count+" avg:"+r_sum/r_count);
		benchmark_a();
	}
}

function bench_callback_a(){
	var time = new Date().getTime() - start_a;
	a_sum += time;
	a_count++;
	if(benchmarkRuns < 2*tries){
		benchmark_a();
	} else {
		console.log("a finished: "+a_sum/a_count);
		benchmarkRuns = 3*tries;
	}
}
