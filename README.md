Tesla-Mobile-Web-App
=====================

This is a web accessable package written in PHP/MYSQL used to retrieve data and set parameters
 on a compatible Model S (remote access enabled).

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
