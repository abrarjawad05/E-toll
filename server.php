<?php
$servername = "localhost";
$username = "root";  // Default username for XAMPP
$password = "";      // Leave blank for XAMPP
$dbname = "toll_db";  // Name of the database you created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
