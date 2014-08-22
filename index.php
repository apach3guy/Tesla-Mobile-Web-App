<html>
<style type="text/css">
.meter { 
	height: 20px;  /* Can be anything */
	position: relative;
	background: #555;
	-moz-border-radius: 25px;
	-webkit-border-radius: 25px;
	border-radius: 25px;
	padding: 10px;
	-webkit-box-shadow: inset 0 -1px 1px rgba(255,255,255,0.3);
	-moz-box-shadow   : inset 0 -1px 1px rgba(255,255,255,0.3);
	box-shadow        : inset 0 -1px 1px rgba(255,255,255,0.3);
}

.meter > span {
	display: block;
	height: 100%;
	   -webkit-border-top-right-radius: 8px;
	-webkit-border-bottom-right-radius: 8px;
	       -moz-border-radius-topright: 8px;
	    -moz-border-radius-bottomright: 8px;
	           border-top-right-radius: 8px;
	        border-bottom-right-radius: 8px;
	    -webkit-border-top-left-radius: 20px;
	 -webkit-border-bottom-left-radius: 20px;
	        -moz-border-radius-topleft: 20px;
	     -moz-border-radius-bottomleft: 20px;
	            border-top-left-radius: 20px;
	         border-bottom-left-radius: 20px;
	background-color: rgb(43,194,83);
	background-image: -webkit-gradient(
	  linear,
	  left bottom,
	  left top,
	  color-stop(0, rgb(43,194,83)),
	  color-stop(1, rgb(84,240,84))
	 );
	background-image: -webkit-linear-gradient(
	  center bottom,
	  rgb(43,194,83) 37%,
	  rgb(84,240,84) 69%
	 );
	background-image: -moz-linear-gradient(
	  center bottom,
	  rgb(43,194,83) 37%,
	  rgb(84,240,84) 69%
	 );
	background-image: -ms-linear-gradient(
	  center bottom,
	  rgb(43,194,83) 37%,
	  rgb(84,240,84) 69%
	 );
	background-image: -o-linear-gradient(
	  center bottom,
	  rgb(43,194,83) 37%,
	  rgb(84,240,84) 69%
	 );
	-webkit-box-shadow: 
	  inset 0 2px 9px  rgba(255,255,255,0.3),
	  inset 0 -2px 6px rgba(0,0,0,0.4);
	-moz-box-shadow: 
	  inset 0 2px 9px  rgba(255,255,255,0.3),
	  inset 0 -2px 6px rgba(0,0,0,0.4);
	position: relative;
	overflow: hidden;
}
 </style>

<?php

require_once('functions.php');
require_once('db.config');

$status = tesla_read('vehicles');

if (!$status) {
	tesla_login($user,$passwd);
	$status = tesla_read('vehicles');
}

if ($status[0]['state'] != 'online') {
	echo '<h2>Car is sleeping</h2>';
	$result = mysqli_query($con, "SELECT `Range`, Percent FROM battery WHERE `Range` IS NOT NULL ORDER BY Date DESC LIMIT 1");
	$row = mysqli_fetch_array($result);
	echo "<h2>Range (offline): " . $row['Range'] . " miles, " . $row['Percent'] . " %</h2>";
	mysqli_close($con);
	} else {
	$charge = tesla_read("vehicles/" . $status[0]['id'] . "/command/charge_state");
	echo '<h2>';
	echo "Range: " . $charge['battery_range'] . " miles, " . $charge['battery_level'] . " %</h2>";
	$drive = tesla_read("vehicles/" . $status[0]['id'] . "/command/drive_state");
	if ($drive['shift_state'] == 'D') {
		echo 'Driving at ';
	} else {
		echo 'Parked at ';
	}
	echo date('H:i:s');
?>
<p>
<div class="meter">
	<span style="width: <?php echo $charge['battery_level']; ?>%"></span>
</div></p>
<?php } ?>
<p><img src="graph.php?range=1" style="height:auto; width:inherit; max-height:100%; max-width:100%"></p>