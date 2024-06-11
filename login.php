<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($userId, $dbUsername, $dbPassword);

    if ($stmt->fetch() && password_verify($password, $dbPassword)) {
        session_start();
        $_SESSION['username'] = $username;
        $_SESSION['last_activity'] = time(); // Set last activity time
        // Login successful
        echo json_encode(['status' => 'success', 'message' => 'Logged in successfully!']);
    } else {
        // Login failed
        echo json_encode(['status' => 'error', 'message' => 'Invalid credentials.', 'showLoginForm' => true]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>