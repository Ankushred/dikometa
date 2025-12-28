<?php
session_start();

// Check if session user exists
if (!isset($_SESSION['user_id'])) {
    // If not logged in, kick them to login page
    header("Location: login.php");
    exit();
}
?>