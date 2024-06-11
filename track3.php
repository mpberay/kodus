<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tracking_number2 = generateTrackingNumber2($conn);
    $aaDate = $_POST['aaDate'];
    $description = $_POST['description'];
    $remarks2 = $_POST['remarks2'];
    $aaType = $_POST['aaType'];
    $outDate = $_POST['outDate'];
    $personnel = $_POST['personnel'];
    $dateReceived = $_POST['dateReceived'];

    // Validate required fields
    if ($aaType == 2) {
        if (empty($personnel)) {
            echo json_encode(['status' => 'error', 'message' => 'Receiving Office / Personnel field is required.']);
            exit();
        }
    };

    $conn->begin_transaction(); // Start a transaction

    try {
        // Insert tracking information
        $stmt = $conn->prepare('INSERT INTO aatracker (tracking_number2, aaDate, description, remarks2, aaType, outDate, personnel, dateReceived) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssssss', $tracking_number2, $aaDate, $description, $remarks2, $aaType, $outDate, $personnel, $dateReceived);

        if ($stmt->execute()) {
            // Get the last inserted ID
            $trackingId = $conn->insert_id;

            // File upload logic
            if (!empty($_FILES['fileToUpload']['name'])) { // Check if file is uploaded
                $fileName = $_FILES['fileToUpload']['name'];
                $fileSize = $_FILES['fileToUpload']['size'];
                $fileType = $_FILES['fileToUpload']['type'];
                $uploadTime = date('Y-m-d H:i:s');

                $uploadDirectory = 'data-tracking-uploads/';
                $uploadPath = $uploadDirectory . $fileName;

                if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadPath)) {
                    // Update tracking information with the file-related information
                    $fileInsertStmt = $conn->prepare('UPDATE aatracker SET file_name=?, file_size=?, file_type=?, upload_time=? WHERE id=?');
                    $fileInsertStmt->bind_param('ssssi', $fileName, $fileSize, $fileType, $uploadTime, $trackingId);
                    $fileInsertStmt->execute();
                    $fileInsertStmt->close();
                } else {
                    // File upload failed
                    echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
                    exit(); // Stop execution if file upload fails
                }
            }

            $conn->commit(); // Commit the transaction

            echo json_encode(['status' => 'success', 'message' => 'Tracking Number: ' . $tracking_number2]);
        } else {
            // Tracking failed
            echo json_encode(['status' => 'error', 'message' => 'Tracking failed: ' . htmlspecialchars($stmt->error)]);
        }

        $stmt->close();
    } catch (Exception $e) {
        $conn->rollback(); // Rollback the transaction in case of an exception
        echo json_encode(['status' => 'error', 'message' => 'Transaction failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

function generateTrackingNumber2($conn) {
    // Fetch the last inserted ID from the database
    $sql = "SELECT id FROM aatracker ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_id = $row['id'];
    } else {
        $last_id = 0; // If no records exist, start from 0 or any default value
    }

    // Increment the last ID by 1 and pad with leading zeros
    $next_id = str_pad($last_id + 1, 4, '0', STR_PAD_LEFT);

    $date = date('m-d-y');
    return $date . '-' . $next_id;
}
?>