<?php
require_once 'config.php';

header('Content-Type: application/json'); // Set the response content type to JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure that the tracking_number is passed from the frontend
    $tracking_data = generateTrackingNumber($conn);

    if ($tracking_data['status'] === 'success') {
        $tracking_number = $tracking_data['tracking_number'];

    $province = $_POST['province'];
    $pdo = $_POST['pdo'];
    $batchNumber = $_POST['batchNumber'];
    $municipality = str_replace("`", "", $_POST['municipality']);
    $barangay = $_POST['barangay'];
    $beneficiaries = $_POST['beneficiaries'];
    $fund = str_replace(",", "", $_POST['fund']);
    $unpaid = $_POST['unpaid'];
    $undisbursed = str_replace(",", "", $_POST['undisbursed']);
    $payout = $_POST['payout'];
    $paymaster = $_POST['paymaster'];
    $orientation = $_POST['orientation'];
    $speaker = $_POST['speaker'];
    $secondDay = $_POST['secondDay'];
    $monitoring = $_POST['monitoring'];
    $evaluator = $_POST['evaluator'];
    $lastDay = $_POST['lastDay'];
    $difference = $_POST['difference'];
    $project = $_POST['project'];
    $findings = $_POST['findings'];
    $kia = $_POST['kia'];
    $payroll = $_POST['payroll'];
    $tts = $_POST['tts'];
    $war = $_POST['war'];
    $coc = $_POST['coc'];
    $geobefore = $_POST['geobefore'];
    $geoduring = $_POST['geoduring'];
    $geoafter = $_POST['geoafter'];
    $spelling = $_POST['spelling'];
    $replacementsDate = $_POST['replacementsDate'];
    $replacements = $_POST['replacements'];
    $mebRDate = $_POST['mebRDate'];
    $mebR = $_POST['mebR'];
    $brgyReso = $_POST['brgyReso'];
    $moaCert = $_POST['moaCert'];
    $minutes = $_POST['minutes'];
    $endorsement = $_POST['endorsement'];
    $track_type = $_POST['track_type'];

    $stmt = $conn->prepare('INSERT INTO trackdata (tracking_number, province, pdo, batchNumber, municipality, barangay, beneficiaries, fund, unpaid, undisbursed, payout, paymaster, orientation, speaker, secondDay, monitoring, evaluator, lastDay, difference, project, findings, kia, payroll, tts, war, coc, geobefore, geoduring, geoafter, spelling, replacementsDate, replacements, mebRDate, mebR, brgyReso, moaCert, minutes, endorsement, track_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare statement error: ' . $conn->error]);
    } else {
        $stmt->bind_param('sssssssssssssssssssssssssssssssssssssss', $tracking_number, $province, $pdo, $batchNumber, $municipality, $barangay, $beneficiaries, $fund, $unpaid, $undisbursed, $payout, $paymaster, $orientation, $speaker, $secondDay, $monitoring, $evaluator, $lastDay, $difference, $project, $findings, $kia, $payroll, $tts, $war, $coc, $geobefore, $geoduring, $geoafter, $spelling, $replacementsDate, $replacements, $mebRDate, $mebR, $brgyReso, $moaCert, $minutes, $endorsement, $track_type);

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