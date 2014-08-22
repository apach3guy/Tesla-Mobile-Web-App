<style>
	table,th,td {
		padding:15px;
		border:1px solid black;
		border-collapse:collapse
	}
	
	#col1 {
		float:left;
		position:relative;
		overflow:hidden;
	}
	#col2 {
		float:right;
		position:relative;
		overflow:hidden;
	}
</style>
<div id="col1">
Displays streaming data while the car is in motion.<p>
<?php

require_once('db.config');

mysqli_query($con, "TRUNCATE stream");

// Required for real-time output
	ini_set('output_buffering', 'off');
	ini_set('zlib.output_compression', false);
	while (@ob_end_flush());
	ini_set('implicit_flush', true);
	ob_implicit_flush(true);

require_once('functions.php');
set_time_limit(60);

$status = tesla_read('vehicles');

if ($status[0]['state'] != 'online') {
	echo 'Car is sleeping';
} else {
	$token = $status[0]['tokens'];
	if(!isset($token[0])) {
		die('Error');
	}
	
	echo "<table><tr><td>Time (s)</td><td>Speed</td><td>Power</td></tr>";
	
	function writeCallback($ch, $data) {
		
		$array = array();
		$array[] = str_getcsv($data);
			static $first_call = true;
			global $time_initial;
			if($first_call == true) {
				$time_initial = $array[0][0];
			}
		$time = ($array[0][0] - $time_initial) / 1000;
		echo "<tr><td>" . $time . "</td><td>" . $array[0][1] . "</td><td>" . $array[0][2] . "</td></tr>";
		global $con;
		$content = "$time," . $array[0][1] . "," . $array[0][2];
		mysqli_query($con, "INSERT INTO stream VALUES ($content)");
			// Make the browser happy
			echo str_pad('',1024,' ');
			flush();
		$first_call = false;
		return strlen($data);
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://streaming.vn.teslamotors.com/stream/" . $status[0]['vehicle_id'] . "/?values=speed,power");
	curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $token[0]);
	curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'writeCallback');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_BUFFERSIZE, 256);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_exec($ch); // commence streaming
	curl_close($ch);
	mysqli_close($con);
	echo "</table>";
	echo '</div><div id="col2">';
	echo '<img src="stream_graph.php" style="height:auto; width:inherit; max-height:100%; max-width:100%">';
	echo '</div>';
}

?>