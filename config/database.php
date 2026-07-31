<?php
$servername = "sql203.infinityfree.com";
$username = "if0_39254443";
$password = "QGy5gnbenqPMWnZ";
$dbname = "if0_39254443_inventory";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
