<?php
require_once 'config.php';

// Check if the username parameter exists in the POST request
if(isset($_POST['username'])) {
    // Get the username from the POST request
    $usernameToCheck = $_POST['username'];

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    // Bind the parameter
    $stmt->bind_param("s", $usernameToCheck);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if the query was successful and if any rows were returned
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $count = $row['count'];

        // Prepare the response as JSON
        $response = array('isAvailable' => $count == 0);

        // Set the Content-Type header
        header('Content-Type: application/json');

        // Output the JSON response
        echo json_encode($response);
    } else {
        // Handle the case when the query fails or no rows are returned
        $response = array('isAvailable' => false);

        // Set the Content-Type header
        header('Content-Type: application/json');

        // Output the JSON response
        echo json_encode($response);
    }

    // Close the prepared statement
    $stmt->close();
} else {
    // Handle the case when the 'username' parameter is not set in the POST request
    $response = array('error' => 'Username parameter missing');

    // Set the Content-Type header
    header('Content-Type: application/json');

    // Output the JSON response
    echo json_encode($response);
}

// Close the database connection
$conn->close();
?>