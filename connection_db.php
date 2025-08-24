<?php
$host = "db-calculia.mysql.database.azure.com";
$user = "mim";
$password = "Maria5066$";
$dbname = "calculiaworld";


// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
