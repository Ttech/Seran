<?php
error_Reporting(E_ALL);
//header('Content-Type: image/png');
/* The graph-it and make it nice php script
 	Not suitable for children under the age of 25
		Just because of how terribly designed it was..
			But don't blame me... It was the code monkeys 
*/
define("SITE_PATH","/srv/http/transcendence/");
define("COLLECTION_PATH","/srv/collection/usergraph/data/");
define("GEN_PATH","/srv/collection/usergraph/");


// line or boxes seems to work pretty good or impulses (but that strips box other stuff)
$graph_style = 'line';

$channel_list = array(
'botters',
'transcendence',
'club-ubuntu'
);

/* These need to be set for it to work */
 	/* Gosh why do they need variables... LIfe would be so much more...
		variable? */

function correct_ratios($channel){
	switch($channel){
		case 'club-ubuntu':
			$ratio = "[80:130]";
		break;
		case 'botters':
			$ratio = "[20:100]";
		break;
		case 'transcendence':
			$ratio = "[5:55]";
		break;
		default: 
			$ratio = "[0:30]";
		break;
	}
	return $ratio;
}

function make_db_filename($channel){
	$safe_channel = str_replace("#","",$channel);
	return COLLECTION_PATH . $safe_channel . '.txt';  
}

function get_dates($filename, &$start_date="",&$end_date=""){
	$filepointer = file($filename);
	$first_line = str_replace(array("\n","\t","\r"),"",$filepointer[0]);
	$last_line = count($filepointer);
	$last_line = str_replace(array("\n","\t","\r"),"",$filepointer[$last_line-1]);
	$formatted_start_date = explode(" ",$first_line);
	$formatted_end_date = explode(" ", $last_line);
	$completed_array = array(
		'start_full' => $first_line,
		'end_full' => $last_line,
		'start_format' => ($formatted_start_date[0]." ".$formatted_start_date[1]),
		'end_format' => ($formatted_end_date[0]." ".$formatted_end_date[1]),
		'end' => array(
			'date' => $formatted_end_date[0],
			'users' => intval($formatted_end_date[2])
		),
		'start' => array(
			'date' => $formatted_start_date[0],
			'users' => intval($formatted_start_date[2])
		)
	);
	return $completed_array;
}

function generate_graph($channel){
	$filename = make_db_filename($channel);
	$dates = get_dates($filename);
	
	$start_date = $dates['start']['date'];
	$end_date = $dates['end']['date'];
	$start = $dates['start_format'];
	$end = $dates['end_format'];
	return make_gnuplot_input($start_date,$end_date,$start,$end,$channel,$filename);
}


function make_gnuplot_input($start_date,$end_date,$start,$end,$channel,$file_path){
	global $graph_style;
	$nice_channel = str_replace("#","",$channel);
	$write_path = SITE_PATH . $nice_channel . ".png";
	// This is set to the gnuplot application
	$gnuplot_input_file = 
'reset
set terminal png size 612,400
set output "'.$write_path.'"
set xdata time
set timefmt "%Y-%m-%d %H:%M"
set format x "%m/%d"
set xlabel "time"
set ylabel "total users (30m)"
set xrange ["'.$start.'":"'.$end.'"]
set yrange '.correct_ratios($channel).'
set title "Total Users '.ucfirst($channel).' from '.$start_date.' to '.$end_date.'"
set key Left outside
set grid
set style data '.$graph_style.'
plot "'.$file_path.'" using 1:3 title "Users"';
	return $gnuplot_input_file;
}
function write_config($channel){
	$filenamee = GEN_PATH . "gnuplot.rg";
	if(!file_exists($filenamee)){
		touch($filenamee);
	}
	$config_contents = generate_graph($channel);
	if(file_put_contents($filenamee,$config_contents)){
	
	} else {
		die("COULD NOT CREATE CONFIG FOR $channel");
	}
}
function exec_config(){
	$filenamee = GEN_PATH . "gnuplot.rg";
	if(exec("cat $filenamee | gnuplot")){
		echo "PH";	
	}
}

function make_graphs($channel_list){
	foreach($channel_list as $channel){
		write_config($channel);
		exec_config();
	}
}

make_graphs($channel_list);
?>
