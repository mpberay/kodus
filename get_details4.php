<?php
include "config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Fetch details based on the ID from your database using prepared statement
        $sql = "SELECT * FROM aatracker WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Return the HTML content for the details in a table form
            echo "<div>";
            echo "<strong>Tracking Number: " . htmlspecialchars($row['tracking_number2']) . "</strong>";
            echo "</div>";
            echo "<table id='details-table4' class='details-table' style='table-layout: fixed !important;'>";
            echo "<tr><td><strong>Outgoing Date:</strong></td><td type='date' class='editable-cell' id='outDate' name='outDate' data-date='" . (htmlspecialchars($row['outDate']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['outDate']))) . "'>" . (htmlspecialchars($row['outDate']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['outDate']))) . "</td></tr>";
            echo "<tr><td><strong>Description:</strong></td><td contenteditable='true' id='description' name='description'>" . (htmlspecialchars($row['description']) == "" ? "None" : (htmlspecialchars($row['description']))) . "</td></tr>";
            echo "<tr><td><strong>Receiving Office / Personnel:</strong></td><td contenteditable='true' id='personnel' name='personnel'>" . htmlspecialchars($row['personnel']) . "</td></tr>";
            echo "<tr><td><strong>Date Received:</strong></td><td type='date' class='editable-cell' id='dateReceived' name='dateReceived' data-date='" . (htmlspecialchars($row['dateReceived']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['dateReceived']))) . "'>" . (htmlspecialchars($row['dateReceived']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['dateReceived']))) . "</td></tr>";
            echo "<tr><td><strong>Remarks:</strong></td><td contenteditable='true' id='remarks2' name='remarks2'>" . (htmlspecialchars($row['remarks2']) == "" ? "None" : (htmlspecialchars($row['remarks2']))) . "</td></tr>";
            echo "<tr><td><strong>File Preview: (" . (empty(htmlspecialchars($row['file_name'])) ? "No file available." : htmlspecialchars($row['file_name'])) . ")</strong></td><td>";
            if (empty(htmlspecialchars($row['file_name']))) {
                echo "No file available.";
            } else {
                echo "<button class='primary' onclick=\"openFilePopup('data-tracking-uploads/" . htmlspecialchars($row['file_name']) . "')\">View File</button><br><br><iframe src='data-tracking-uploads/" . htmlspecialchars($row['file_name']) . "' width='480px' style='border: none'></iframe>";
            };
            echo "<br><br>".(empty($row['file_name']) ? "Upload File:" : "Replace File:")."<br><input type='file' id='file_name' name='file_name'></td></tr>";
            echo "</table>";

            // Check for update parameters and perform update
            if (isset($_POST['outDate'], $_POST['description'], $_POST['personnel'], $_POST['dateReceived'], $_POST['remarks2'])) {
                $outDate = $_POST['outDate'];
                $description = $_POST['description'];
                $personnel = $_POST['personnel'];
                $dateReceived = $_POST['dateReceived'];
                $remarks2 = $_POST['remarks2'];

                // Update the database with new values
                $updateSql = "UPDATE aatracker SET outDate = STR_TO_DATE(?, '%m-%d-%Y'), description = ?, personnel = ?, dateReceived = STR_TO_DATE(?, '%m-%d-%Y'), remarks2 = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("sssssi", $outDate, $description, $personnel, $dateReceived, $remarks2, $id);

                if ($updateStmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Data updated successfully' . $tracking_number2]);

                    // Handle file upload
                    if ($_FILES['file_name']['error'] === 0) {
                        handleFileUpload($conn, $id);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update data']);
                }

                $updateStmt->close();
            } else {
                // Handle non-update case if needed
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Details not found']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

$conn->close();

// Function to handle file upload
function handleFileUpload($conn, $id) {
    $originalFileName = basename($_FILES['file_name']['name']);
    $fileSize = $_FILES['file_name']['size'];
    $fileType = $_FILES['file_name']['type'];
    $uploadTime = date('Y-m-d H:i:s');

    // Generate a unique filename to avoid overwriting existing files
    $fileName = generateUniqueFileName('data-tracking-uploads/', $originalFileName);

    $uploadPath = 'data-tracking-uploads/' . $fileName;

    if (move_uploaded_file($_FILES['file_name']['tmp_name'], $uploadPath)) {
        // Update the database with the new file details
        $updateFileSql = "UPDATE aatracker SET file_name = ?, file_size = ?, file_type = ?, upload_time = ? WHERE id = ?";
        $updateFileStmt = $conn->prepare($updateFileSql);
        $updateFileStmt->bind_param("sissi", $fileName, $fileSize, $fileType, $uploadTime, $id);
        $updateFileStmt->execute();
        $updateFileStmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update data']);
    }
}

// Function to generate a unique filename
function generateUniqueFileName($directory, $originalFileName) {
    $fileName = $originalFileName;
    $counter = 1;

    // Check if the file already exists in the directory
    while (file_exists($directory . $fileName)) {
        $fileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '_' . $counter . '.' . pathinfo($originalFileName, PATHINFO_EXTENSION);
        $counter++;
    }

    return $fileName;
}
?>