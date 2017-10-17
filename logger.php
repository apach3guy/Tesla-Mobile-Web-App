<?php

require_once('functions.php');
require_once('db.php');

// Eliminate old records
mysqli_query($con,"Delete FROM battery WHERE Date < (NOW() - INTERVAL 1 WEEK)");

$auth = json_decode(file_get_contents("token.json"), true);

for($i = 0; $i < 5; $i++) {
		sleep(1);
		$status = tesla_read('vehicles', $auth['access_token']);
		if (isset($status[0]['state'])) break;
		if ($i==3) {
			file_put_contents("token.json", tesla_login($id,$secret,$user,$passwd));
			$auth = json_decode(file_get_contents("token.json"), true);
		}
}

if ($status[0]['state'] != 'online') {
	// Car is asleep
	mysqli_query($con,"INSERT INTO battery (Sleep) VALUES (1)");
} else {
	// Car is awake
	$result = mysqli_query($con, "SELECT Desired FROM settings");
	$row = mysqli_fetch_array($result);

	/* free result set */
	$result->close();
	
	if($row['Desired']	!= 0) {
		for($i=0; $i < 5; $i++) {
			$curl = tesla_set("vehicles/" . $status[0]['id'] . "/command/set_charge_limit",$row['Desired'],$auth['access_token']);
			if($curl['result'] || $curl['reason'] == 'already_set') {
				mysqli_query($con, "UPDATE settings SET Desired = 0");
				break;
			}
		}
	}
	
	for ($i = 0; $i < 5; $i++) {
		$charge = tesla_read("vehicles/" . $status[0]['id'] . "/data_request/charge_state", $auth['access_token']);
		if (is_numeric($charge['battery_range'])) break;
	}
	$limit = mysqli_real_escape_string($con, $charge['charge_limit_soc']);
	$miles = mysqli_real_escape_string($con, $charge['battery_range']);
	$battery_level = mysqli_real_escape_string($con, $charge['battery_level']);
	mysqli_query($con,"INSERT INTO battery (`Range`, Percent) VALUES ($miles, $battery_level)");
	$location = tesla_read("vehicles/" . $status[0]['id'] . "/data_request/drive_state", $auth['access_token']);
	$lat = mysqli_real_escape_string($con, $location['latitude']);
	$lon = mysqli_real_escape_string($con, $location['longitude']);

	// Must uncomment following line for first time run
	// mysqli_query($con,"INSERT INTO settings (Level, Latitude, Longitude) VALUES ($limit, $lat, $lon)");
	mysqli_query($con, "UPDATE settings SET Level = '$limit', Latitude = '$lat', Longitude = '$lon'");

}

mysqli_close($con);
?>