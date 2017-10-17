<?php

/* yes, this is redundant */
$mysqli = new mysqli("your db settings");
$con = new mysqli("your db settings");

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

?>