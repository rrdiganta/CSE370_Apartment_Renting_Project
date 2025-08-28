<?php
$servername="localhost";
$username="root"; //mysql -u root -p
$password="";
$dbname="bharahobe";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error)
{
	die("Connection error: ".  $conn->connect_error); //die makes process die
}
?>