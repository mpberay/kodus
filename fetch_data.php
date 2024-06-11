<?php
include 'config.php';

// Use isset() to check if the 'table' parameter is set in the GET request
if (isset($_GET['table'])) {
    $table = $_GET['table'];

    // Use a switch statement for better readability
    switch ($table) {
        case 'provinces':
            // Fetch provinces from the database
            $result = $conn->query('SELECT id, province_name FROM provinces ORDER BY province_name ASC');
            break;
        case 'pdos':
            // Fetch PDOs based on the selected province
            if (isset($_GET['province_id'])) {
                $provinceId = $conn->real_escape_string($_GET['province_id']);
                $result = $conn->query("SELECT id, pdo FROM pdos WHERE province_id = '$provinceId'");
            } else {
                // Handle missing province_id parameter
                echo json_encode(['error' => 'Missing province_id parameter']);
                exit;
            }
            break;
        case 'municipality':
            // Fetch municipality based on the selected province
            if (isset($_GET['province_id'])) {
                $provinceId = $conn->real_escape_string($_GET['province_id']);
                $result = $conn->query("SELECT id, municipality_name FROM municipality WHERE province_id = '$provinceId' ORDER BY municipality_name ASC");
            } else {
                // Handle missing province_id parameter
                echo json_encode(['error' => 'Missing province_id parameter']);
                exit;
            }
            break;
        case 'barangay':
            // Fetch barangay based on the selected municipality
            if (isset($_GET['municipality_id'])) {
                $municipalityId = $conn->real_escape_string($_GET['municipality_id']);
                $result = $conn->query("SELECT id, brgy_name FROM barangay WHERE municipality_id = '$municipalityId' ORDER BY brgy_name ASC");
            } else {
                // Handle missing municipality_id parameter
                echo json_encode(['error' => 'Missing municipality_id parameter']);
                exit;
            }
            break;
        default:
            // Handle unknown table parameter
            echo json_encode(['error' => 'Unknown table parameter']);
            exit;
    }

    $data = [];

    // Check if the query was successful before fetching data
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        // Output the data as JSON
        echo json_encode($data);
    } else {
        // Handle query failure
        echo json_encode(['error' => 'Query failed']);
    }

    // Close the database connection
    $conn->close();
} else {
    // Handle missing 'table' parameter
    echo json_encode(['error' => 'Missing table parameter']);
}
?>
