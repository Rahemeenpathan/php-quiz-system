<?php
// db_connect.php

$servername = "localhost";
$username = "root";
$password = "rahemeen23";  // Your MySQL root password
$dbname = "quiz_db_final";
$port = 3308;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    // In a real environment, you should log this error, not expose it publicly
    die("Connection failed: " . $conn->connect_error);
}
?>