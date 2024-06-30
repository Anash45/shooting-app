<?php
// db_conn.php

// Start or resume session
session_start();
// Database connection parameters
$servername = "localhost";
$username = "clay_shooting";
$password = "6B?ATRfFBU*c";
$dbname = "clay_shooting";

$info = '';
// $servername = "localhost";
// $username = "root";
// $password = "root";
// $dbname = "clay_shooting_db";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optionally, you can set the charset to ensure compatibility with different languages and special characters
$conn->set_charset("utf8");

function isUserLoggedIn()
{

    // Check if 'user_id' or any other relevant session variable exists
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {
        // User is logged in
        return true;
    } else {
        // User is not logged in
        return false;
    }
}
// This file can be included in other PHP files to use the $conn object for database operations
?>

