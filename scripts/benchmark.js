//  Copyright Benjamin Murauer 2012
//      
//  This file is part of the Hash-O-Mender program.
//
//  Hash-O-Mender is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  Hash-O-Mender is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Hash-O-Mender (gpl.txt).
//  If not, see <http://www.gnu.org/licenses/>.


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
	} else if (benchmarkRuns < 2*tries){
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
