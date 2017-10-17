Tesla-Mobile-Web-App
=====================

This is a web accessable package written in PHP/MYSQL used to retrieve data and set parameters
 on a compatible Tesla vehicle.

Features:
	- Track SOC data over time and monitor vampire losses (logger.php)
	- View offline range without having to wake the car
	- Set the desired charger % without having to wake the car
	- Calculates corresponding rated miles for given SOC % using real data!
	- View the car's last known location without having to wake the car
	- Track a supercharge or any other charge session
	- View streaming data in real-time

The beauty is that all of these features output to the browser instead of having to launch a 
program on your desktop.

Setup:
	- Configure an appropriate MYSQL db
	- logger.php will need to be executed by cron or task scheduler in 30 min increments

Note:
	- Graphing features will require a PHP graphing library (i.e. PHPGraphLib)
	- I am an amateur programmer with ZERO formal training. Use at your own risk!

You will need to create the appropriate MYSQL tables manually:

// Create 3 MYSQL tables if necessary
$result = mysqli_query($con, "SELECT * FROM battery");
if($result == NULL) {
	mysqli_query($con, "CREATE TABLE battery(`Date` TIMESTAMP, `Range` DECIMAL(5,2), `Percent` TINYINT(3), `Sleep` TINYINT(1))");
}

$result2 = mysqli_query($con, "SELECT * FROM settings");
if($result2 == NULL) {
	mysqli_query($con, "CREATE TABLE settings(`Level` TINYINT(2), `Desired` TINYINT(2), `Latitude` FLOAT(10,6), `Longitude` FLOAT(10,6))");
}

$result3 = mysqli_query($con, "SELECT * FROM stream");
if($result3 == NULL) {
	mysqli_query($con, "CREATE TABLE stream(`Time` Decimal(5,3), `Speed` TINYINT(3), `Power` SMALLINT(3))");
}
