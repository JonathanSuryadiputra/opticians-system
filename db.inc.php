<?php
$hostname = "localhost";
$username = "ITCarlow";
$password = "password";

$dbname = "Opticians";

$con = mysqli_connect($hostname,$username,$password, $dbname);

if (!$con)
{
	die ("Failed to connect to MySQL: " . mysqli_connect_error());
}
?>