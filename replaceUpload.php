<?php

include "config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Check if the form is submitted
        if (isset($_POST['submit'])) {
            // Retrieve form data

            // File upload handling
            $uploadDir = "TrackData-uploads/"; // Specify your upload directory

            // Ensure the 'uploads' directory exists
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = $_FILES['fileToUpload']['name'];
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadFile)) {
                echo "File is valid, and was successfully uploaded.\n";

                // Insert data into the database
                $sql = "UPDATE aatracker SET file_name = '$fileName' WHERE id = $id";

                if ($conn->query($sql) === TRUE) {
                    echo "Data updated successfully";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Upload failed";
            }
        }
    }

    // Close the database connection
    $conn->close();
}
?>