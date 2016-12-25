<?php
$server = "YOUR_SERVER_NAME";
$username = "YOUR_USER_NAME";
$password = "YOUR_PASSWORD";
$database = "YOUR_DATABASE_NAME";

$connId = mysql_connect($server,$username,$password) or die("Cannot connect to server");
$selectDb = mysql_select_db($database,$connId) or die("Cannot connect to database");
?>