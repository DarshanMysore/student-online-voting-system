<?php
// Database configuration
$host = "localhost";     // usually localhost
$user = "root";          // change if you set a MySQL username
$pass = "";              // change if you set a MySQL password
$db   = "college_voting"; // database name

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("❌ Database Connection Failed: " . $conn->connect_error);
} else {

}
?>
