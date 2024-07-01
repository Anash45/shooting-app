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

// Function to get locations
function getLocations()
{
    global $conn;
    $query = "SELECT * FROM locations";
    $result = $conn->query($query);
    $locations = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $locations[] = $row;
        }
    }
    return $locations;
}

// Function to get ammo options
function getAmmo()
{
    global $conn;
    $query = "SELECT * FROM ammo";
    $result = $conn->query($query);
    $ammo = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $ammo[] = $row;
        }
    }
    return $ammo;
}

// Function to get POI options
function getPOI()
{
    global $conn;
    $query = "SELECT * FROM poi";
    $result = $conn->query($query);
    $poi = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $poi[] = $row;
        }
    }
    return $poi;
}

// Function to get glasses options
function getGlasses()
{
    global $conn;
    $query = "SELECT * FROM glasses";
    $result = $conn->query($query);
    $glasses = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $glasses[] = $row;
        }
    }
    return $glasses;
}

// Function to get ears options
function getEars()
{
    global $conn;
    $query = "SELECT * FROM ears";
    $result = $conn->query($query);
    $ears = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $ears[] = $row;
        }
    }
    return $ears;
}

// Function to get event types
function getEventTypes()
{
    global $conn;
    $query = "SELECT * FROM type";
    $result = $conn->query($query);
    $types = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $types[] = $row;
        }
    }
    return $types;
}

function getBgImg()
{
    global $conn;
    // SQL query to fetch image_name from backgroundimage table
    $query = "SELECT image_name FROM backgroundimage ORDER BY id DESC LIMIT 1";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if query executed successfully
    if ($result) {
        // Fetch the image_name from the result set
        $row = mysqli_fetch_assoc($result);

        // Free result set
        mysqli_free_result($result);

        // Return the image_name (or null if no result found)
        return ($row) ? $row['image_name'] : null;
    } else {
        // Query execution failed
        return null;
    }
}

$dbTypes = getEventTypes();
$dbEars = getEars();
$dbPOIs = getPOI();
$dbGlasses = getGlasses();
$dbLocations = getLocations();
$dbAmmos = getAmmo();
// This file can be included in other PHP files to use the $conn object for database operations
?>