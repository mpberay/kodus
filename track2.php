<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure that the tracking_number is passed from the frontend
    $tracking_data = generateTrackingNumber($conn);

    if ($tracking_data['status'] === 'success') {
        $tracking_number = $tracking_data['tracking_number'];

        $adas = $_POST['adas'];
        $trackDate = $_POST['trackDate'];
        $tProvince = $_POST['tProvince'];
        $tMunicipality = str_replace("`", "", $_POST['tMunicipality']);
        $tBarangay = $_POST['tBarangay'];
        $meb = $_POST['meb'];
        $mer = $_POST['mer'];
        $remarks = $_POST['remarks'];
        $track_type = $_POST['track_type'];

        $stmt = $conn->prepare('INSERT INTO trackdata (tracking_number, adas, trackDate, tProvince, tMunicipality, tBarangay, meb, mer, remarks, track_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

        if ($stmt === false) {
            echo json_encode(['status' => 'error', 'message' => 'Prepare statement error: ' . $conn->error]);
        } else {
            $stmt->bind_param('ssssssssss', $tracking_number, $adas, $trackDate, $tProvince, $tMunicipality, $tBarangay, $meb, $mer, $remarks, $track_type);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Tracking Number: ' . $tracking_number]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Tracking failed: ' . $stmt->error]);
            }

            $stmt->close();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error generating tracking number']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

function generateTrackingNumber($conn) {
    $affix = 'RRP';
    $date = date('Y-m-d');

    // Fetch the last inserted ID from the database
    $sql = "SELECT id FROM trackdata ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_id = $row['id'];
    } else {
        $last_id = 0; // If no records exist, start from 0 or any default value
    }

    // Increment the last ID by 1 and pad with leading zeros
    $next_id = str_pad($last_id + 1, 4, '0', STR_PAD_LEFT);

    $tracking_number = $affix . '-' . $date . '-' . $next_id;

    return ['status' => 'success', 'tracking_number' => $tracking_number];
}
?>