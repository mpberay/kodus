<?php
session_start();

// Check if the user is logged in, otherwise return an error
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'User not logged in!']);
    exit();
}

// Delete the 'lastViewedTime' cookie
setcookie('lastViewedTime', '', time() - 3600, '/');

// Simulate some delay to show the loading screen (you can adjust the sleep duration)
sleep(2);

// Destroy the session
session_destroy();

// Return success response
echo json_encode(['status' => 'success', 'message' => 'Logged out successfully!']);
?>