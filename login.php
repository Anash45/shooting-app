<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$google_id = $input['google_id'];
$name = $input['name'];
$email = $input['email'];
$profile_picture = $input['profile_picture'];

$servername = "your_server";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Check if user exists
$sql = "SELECT * FROM users WHERE google_id = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $google_id, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User exists, update information
    $sql = "UPDATE users SET name = ?, profile_picture = ?, signin_method = 'google', updated_at = CURRENT_TIMESTAMP WHERE google_id = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $profile_picture, $google_id, $email);
    $stmt->execute();
} else {
    // User does not exist, insert new record
    $sql = "INSERT INTO users (google_id, name, email, profile_picture, signin_method, created_at, updated_at) VALUES (?, ?, ?, ?, 'google', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $google_id, $name, $email, $profile_picture);
    $stmt->execute();
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true]);
?>
