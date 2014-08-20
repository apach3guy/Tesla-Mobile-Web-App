<?php

// This script will hang on execution because of the infinite loop.  
// It's pretty simple to fix with an exec command to an external 
// script if anyone's interested in doing that.

require_once('functions.php');

set_time_limit(3600);

function start_trace() {
	while(1)
	{
		$log_path = 'charge.log';
		$status = tesla_read('vehicles');
		if ($status[0]['state'] != 'online') break;
		$charge = tesla_read("vehicles/" . $status[0]['id'] . "/command/charge_state");
		$line = $charge['battery_range'] . "\t" . $charge['battery_level'] . "\t" . $charge['charger_power'] . "\r\n";
		file_put_contents($log_path, $line, FILE_APPEND | LOCK_EX);
		sleep(120);
	}
	return false;
}

if(isset($_REQUEST['trace'])) {
	if(start_trace() == false) {
	echo 'Error: Car is sleeping';
	} else {
	echo 'Charge trace initialized';
	}
}

?>
<h2>Charge Diagnostic</h2>
Records charging data every 2 minutes for a total of 1 hour.
<p><form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
<input type="submit" class="button" name="trace" value="Begin Trace"></p>
<h2>Data</h2>
<img src="graph.php?charge=30" style="height:auto; width:inherit; max-height:100%; max-width:100%">