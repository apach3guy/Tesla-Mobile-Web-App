<?php

$con=mysqli_connect();

if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

if(isset($_REQUEST['limit'])) {
	$limit = mysqli_real_escape_string($con, $_REQUEST['limit']);
	mysqli_query($con, "UPDATE settings SET Level = '$limit', Desired = '$limit'");
	function linear_regression($x, $y) {

	// calculate number points
	$n = count($x);

	// ensure both arrays of points are the same size
	if ($n != count($y)) {

	trigger_error("linear_regression(): Number of elements in coordinate arrays do not match.", E_USER_ERROR);

	}

	// calculate sums
	$x_sum = array_sum($x);
	$y_sum = array_sum($y);

	$xx_sum = 0;
	$xy_sum = 0;

	for($i = 0; $i < $n; $i++) {

	$xy_sum+=($x[$i]*$y[$i]);
	$xx_sum+=($x[$i]*$x[$i]);

	}

	// calculate slope
	$m = (($n * $xy_sum) - ($x_sum * $y_sum)) / (($n * $xx_sum) - ($x_sum * $x_sum));
	// calculate intercept
	$b = ($y_sum - ($m * $x_sum)) / $n;
	// return result
	return array("m"=>$m, "b"=>$b);

	}

	$result = mysqli_query($con, "SELECT `Range`, `Percent` FROM battery WHERE Sleep = '0'");

	while($row = mysqli_fetch_array($result)) {
	$range[] = $row['Range'];
	$percent[] = $row['Percent'];
	}

	/* free result set */
	$result->close();

	if(count(array_unique($percent)) < 10) {
		$msg = 'Not enough data points in database';
	} else {
		$line = linear_regression($percent,$range);
		$msg = number_format($line['m'] * $_REQUEST['limit'] + $line['b'], 0) . ' miles<br>';
	}
}

$result = mysqli_query($con, "SELECT Level, Latitude, Longitude FROM settings");

$row = mysqli_fetch_array($result);

/* free result set */
$result->close();

?>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>	
<script type="text/javascript">
	$(document).ready(function () {
		// Define the latitude and longitude positions
		var latitude = parseFloat("<?php echo $row['Latitude']; ?>");
		var longitude = parseFloat("<?php echo $row['Longitude']; ?>");
		var latlngPos = new google.maps.LatLng(latitude, longitude);
		// Set up options for the Google map
		var myOptions = {
			zoom: 10,
			center: latlngPos,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		// Define the map
		map = new google.maps.Map(document.getElementById("map"), myOptions);
		// Add the marker
		var marker = new google.maps.Marker({
			position: latlngPos,
			map: map,
			title: "Location"
		});
	});
</script>
<article class="first">
	<h2>Charge Limit (50-99)</h2>
	<?php if ($row['Level'] > 90) echo '<img src="images/caution.png"><font color="red">MAX RANGE MODE</font>'; ?>
	<?php if (isset($msg)) echo $msg; ?>
	<form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
		<input type="text" class="textinput" id="battery" maxlength="2" name="limit" value="<?php echo $row['Level']; ?>">
		<input type="submit" class="button" value="Set Limit">
	</form>
</article>
<article class="first">
	<h2>Car Location</h2>
	<div id="map" style="height:300px;width:auto;max-width:600px;margin-top:10px;"></div>
</article>