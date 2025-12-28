<?php
// Database Configuration
$host = "127.0.0.1"; // Loopback address
$user = "root";      // Default XAMPP username
$pass = "";          // Default XAMPP password (empty)
$db   = "dikometa_db";
$port = 3307;        // YOUR CUSTOM PORT (Change to 3306 if you didn't change ports)

// Create Connection
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check Connection
if ($conn->connect_error) {
    die("<h3>Connection Failed: " . $conn->connect_error . "</h3>");
}
?>