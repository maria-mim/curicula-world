<?php
$host = "tcp:db-server-calculia.database.windows.net";
$user = "mim";
$password = "Maria5066$";
$dbname = "calculiaworld";


// Create connection
$conn = new mysqli($host, $user, $password, $dbname, 1433);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
