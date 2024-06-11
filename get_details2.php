<?php
include "config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Fetch details based on the ID from your database using prepared statement
        $sql = "SELECT * FROM trackdata WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Return the HTML content for the details in a table form
            echo "<div>";
            echo "<strong>" . 'Tracking Number: ' . htmlspecialchars($row['tracking_number']) . "</strong>";
            echo "</div>";
            echo "<table id='details-table2' class='details-table'>";
            echo "<tr><td><strong>Date Received by the ADAS:</strong></td><td contenteditable='true' id='adas' name='adas'>" . (htmlspecialchars($row['adas']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['adas']))) . "</td></tr>";
            echo "<tr><td><strong>Tracking Date:</strong></td><td contenteditable='true' id='trackDate' name='trackDate'>" . (htmlspecialchars($row['trackDate']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['trackDate']))) . "</td></tr>";
            echo "<tr><td><strong>Province:</strong></td><td contenteditable='true' id='tProvince' name='tProvince'>" . htmlspecialchars($row['tProvince']) . "</td></tr>";
            echo "<tr><td><strong>Municipality:</strong></td><td contenteditable='true' id='tMunicipality' name='tMunicipality'>" . htmlspecialchars($row['tMunicipality']) . "</td></tr>";
            echo "<tr><td><strong>Barangay:</strong></td><td contenteditable='true' id='tBarangay' name='tBarangay'>" . htmlspecialchars($row['tBarangay']) . "</td></tr>";
            echo "<tr><td><strong>Quantity in MEB</strong></td><td contenteditable='true' id='meb' name='meb'>" . number_format(htmlspecialchars($row['meb']), 0, '', ',') . "</td></tr>";
            echo "<tr><td><strong>Quantity in MER:</strong></td><td contenteditable='true' id='mer' name='mer'>" . number_format(htmlspecialchars($row['mer']), 0, '', ',') . "</td></tr>";
            echo "<tr><td><strong>Findings:</strong></td><td contenteditable='true' id='remarks' name='remarks'>" . (htmlspecialchars($row['remarks']) == "" ? "None" : (htmlspecialchars($row['remarks']))) . "</td></tr>";

        // Add other details as needed
        echo "</table>";

            // Check for update parameters and perform update
            if (isset($_POST['adas'], $_POST['trackDate'], $_POST['tProvince'], $_POST['tMunicipality'], $_POST['tBarangay'], $_POST['meb'], $_POST['mer'], $_POST['remarks'])) {
                $adas = $_POST['adas'];
                $trackDate = $_POST['trackDate'];
                $tProvince = $_POST['tProvince'];
                $tMunicipality = $_POST['tMunicipality'];
                $tBarangay = $_POST['tBarangay'];
                $meb = $_POST['meb'];
                $mer = $_POST['mer'];
                $remarks = $_POST['remarks'];

                // Update the database with new values
                $updateSql = "UPDATE trackdata SET adas = STR_TO_DATE(?, '%m-%d-%Y'), trackDate = STR_TO_DATE(?, '%m-%d-%Y'), tProvince = ?, tMunicipality = ?, tBarangay = ?, meb = ?, mer = ?, remarks = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("sssssiisi", $adas, $trackDate, $tProvince, $tMunicipality, $tBarangay, $meb, $mer, $remarks, $id);

                if ($updateStmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Data updated successfully' . $tracking_number]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update data']);
                }

                $updateStmt->close();
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
?>
<script>
    var tracking_number = <?php echo json_encode($tracking_number) ?>
</script>