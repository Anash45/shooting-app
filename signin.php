<?php
require_once 'vendor/autoload.php';
require 'db_conn.php'; // Include the database connection file

// Initialize the Google client
$client = new Google_Client(['client_id' => '816473627869-i3cqai8sstv6e0ppiq5cbq86k4rbp06l.apps.googleusercontent.com']); // Replace with your actual Client ID

// Get the JWT token from the POST request
$token = json_decode(file_get_contents('php://input'), true)['token'];

// Verify the token
try {
    $payload = $client->verifyIdToken($token);
    if ($payload) {
        // Token is valid, process the payload
        $userid = $payload['sub']; // Google user ID
        $email = $payload['email'];
        $name = $payload['name'];
        $picture = $payload['picture'];

        // Prepare SQL statements
        $stmt = $conn->prepare("SELECT * FROM users WHERE google_id = ?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User already exists, update their information
            $stmt = $conn->prepare("UPDATE users SET email = ?, name = ?, picture = ? WHERE google_id = ?");
            $stmt->bind_param("ssss", $email, $name, $picture, $userid);
            $stmt->execute();
        } else {
            // User does not exist, insert new record
            $stmt = $conn->prepare("INSERT INTO users (google_id, email, name, picture) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $userid, $email, $name, $picture);
            $stmt->execute();
        }

        // Set session variables
        $_SESSION['user_id'] = $userid;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_picture'] = $picture;

        // Close the statement
        $stmt->close();

        // Send success response and redirect to dashboard
        echo json_encode([
            'status' => 'success',
            'message' => 'User authenticated successfully, redirecting to dashboard',
            'redirect' => 'dashboard.php'
        ]);
    } else {
        // Invalid token
        echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
    }
} catch (Exception $e) {
    // Token verification failed
    echo json_encode(['status' => 'error', 'message' => 'Token verification failed: ' . $e->getMessage()]);
}

// Close the database connection
$conn->close();
?>
