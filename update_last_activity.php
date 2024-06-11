<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    // Update the last activity time in the database or session
    // You may need to modify this based on your specific implementation
    $_SESSION['last_activity'] = time();

    echo json_encode(['status' => 'success', 'message' => 'Last activity updated.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
}
?>