<?php

require_once('functions.php');
require_once('db.config');

// Eliminate old records
mysqli_query($con,"Delete FROM battery WHERE Date < (NOW() - INTERVAL 1 WEEK)");

$status = tesla_read('vehicles');

if (!$status) {
	tesla_login($user,$passwd);
	$status = tesla_read('vehicles');
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
		tesla_read("vehicles/" . $status[0]['id'] . "/command/set_charge_limit?state=set&percent=" . $row['Desired']);
		mysqli_query($con, "UPDATE settings SET Desired = 0");
	}
	
	$charge = tesla_read("vehicles/" . $status[0]['id'] . "/command/charge_state");
	$limit = mysqli_real_escape_string($con, $charge['charge_limit_soc']);
	$miles = mysqli_real_escape_string($con, $charge['battery_range']);
	$battery_level = mysqli_real_escape_string($con, $charge['battery_level']);
	mysqli_query($con,"INSERT INTO battery (`Range`, Percent) VALUES ($miles, $battery_level)");
	$location = tesla_read("vehicles/" . $status[0]['id'] . "/command/drive_state");
	$lat = mysqli_real_escape_string($con, $location['latitude']);
	$lon = mysqli_real_escape_string($con, $location['longitude']);

	// Must uncomment following line for first time run
	// mysqli_query($con,"INSERT INTO settings (Level, Latitude, Longitude) VALUES ($limit, $lat, $lon)");
	mysqli_query($con, "UPDATE settings SET Level = '$limit', Latitude = '$lat', Longitude = '$lon'");

}

mysqli_close($con);
?>