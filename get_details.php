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
            echo "<table id='details-table' class='details-table'>";
            echo "<tr><td><strong>Province:</strong></td><td>" . htmlspecialchars($row['province']) . "</td></tr>";
            echo "<tr><td><strong>PDO:</strong></td><td>" . htmlspecialchars($row['pdo']) . "</td></tr>";
            echo "<tr><td><strong>Batch:</strong></td><td contenteditable='true' id='batchNumber' name='batchNumber'>" . htmlspecialchars($row['batchNumber']) . "</td></tr>";
            echo "<tr><td><strong>Municipality:</strong></td><td contenteditable='true' id='municipality' name='municipality'>" . htmlspecialchars($row['municipality']) . "</td></tr>";
            echo "<tr><td><strong>Barangay:</strong></td><td contenteditable='true' id='barangay' name='barangay'>" . htmlspecialchars($row['barangay']) . "</td></tr>";
            echo "<tr><td><strong>Target No. of Beneficiaries:</strong></td><td contenteditable='true' id='beneficiaries' name='beneficiaries'>" . number_format(htmlspecialchars($row['beneficiaries']), 0, '', ',') . "</td></tr>";
            echo "<tr><td><strong>Funds Allocated:</strong></td><td id='fund' name='fund'>" . number_format(htmlspecialchars($row['fund']), 2, '.', ',') . "</td></tr>";
            echo "<tr><td><strong>No. of Served Beneficiaries:</strong></td><td contenteditable='true' id='served' name='served'>" . htmlspecialchars($row['served']) . "</td></tr>";
            echo "<tr><td><strong>Amount Disbursed:</strong></td><td id='disbursed' name='disbursed'>" . number_format(htmlspecialchars($row['disbursed']), 2, '.', ',') . "</td></tr>";
            echo "<tr><td><strong>Percentage:</strong></td><td id='percent' name='percent'>" . (htmlspecialchars($row['percent']) == "" ? "0" : htmlspecialchars($row['percent'])) . '%' . "</td></tr>";
        //  echo "<tr><td><strong>No. of Unpaid Beneficiaries:</strong></td><td id='unpaid' name='unpaid'>" . number_format(htmlspecialchars($row['unpaid']), 0, '', ',') . "</td></tr>";
            echo "<tr><td><strong>No. of Unpaid Beneficiaries:</strong></td><td id='unpaid' name='unpaid'>" . (htmlspecialchars($row['unpaid']) == 0 ? number_format(htmlspecialchars($row['beneficiaries']), 0, '', ',') : number_format(htmlspecialchars($row['unpaid']), 0, '', ',')) . "</td></tr>";
        //  echo "<tr><td><strong>Undisbursed Amount:</strong></td><td id='undisbursed' name='undisbursed'>" . number_format(htmlspecialchars($row['undisbursed']), 2, '.', ',') . "</td></tr>";
            echo "<tr><td><strong>Undisbursed Amount:</strong></td><td id='undisbursed' name='undisbursed'>" . (htmlspecialchars($row['undisbursed']) == 0 ? number_format(htmlspecialchars($row['fund']), 2, '.', ',') : number_format(htmlspecialchars($row['undisbursed']), 2, '.', ',')) . "</td></tr>";
            echo "<tr><td><strong>For Special Payout:</strong></td><td id='specialPayout' name='specialPayout'>" . number_format(htmlspecialchars($row['specialPayout']), 0, '.', ',') . "</td></tr>";
            echo "<tr><td><strong>For NORSA:</strong></td><td id='norsa' name='norsa'>" . number_format(htmlspecialchars($row['norsa']), 0, '.', ',') . "</td></tr>";
            echo "<tr><td><strong>Payout Date:</strong></td><td contenteditable='true' id='payout' name='payout'>" . (htmlspecialchars($row['payout']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['payout']))) . "</td></tr>";
            echo "<tr><td><strong>Paymasters:</strong></td><td contenteditable='true' id='paymaster' name='paymaster'>" . htmlspecialchars($row['paymaster']) . "</td></tr>";
            echo "<tr><td><strong>Date:</strong></td><td class='editable-cell' id='orientation' name='orientation' data-date='" . (htmlspecialchars($row['orientation']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['orientation']))) . "'>" . (htmlspecialchars($row['orientation']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['orientation']))) . "</td></tr>";
            echo "<tr><td><strong>Speaker:</strong></td><td contenteditable='true' id='speaker' name='speaker'>" . htmlspecialchars($row['speaker']) . "</td></tr>";
            echo "<tr><td><strong>2nd Day (1st Training Day):</strong></td><td class='editable-cell' id='secondDay' name='secondDay' data-date='" . (htmlspecialchars($row['secondDay']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['secondDay']))) . "'>" . (htmlspecialchars($row['secondDay']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['secondDay']))) . "</td></tr>";
            echo "<tr><td><strong>Project Monitoring by RRP-CFW Team:</strong></td><td contenteditable='true' id='monitoring' name='monitoring'>" . htmlspecialchars($row['monitoring']) . "</td></tr>";
            echo "<tr><td><strong>Evaluator:</strong></td><td contenteditable='true' id='evaluator' name='evaluator'>" . htmlspecialchars($row['evaluator']) . "</td></tr>";
            echo "<tr><td><strong>Last Day:</strong></td><td contenteditable='true' id='lastDay' name='lastDay'>" . (htmlspecialchars($row['lastDay']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['lastDay']))) . "</td></tr>";
            echo "<tr><td><strong>Difference:</strong></td><td id='difference' name='difference'>" . htmlspecialchars($row['difference']) . "</td></tr>";
            echo "<tr><td><strong>Project:</strong></td><td contenteditable='true' id='project' name='project'>" . htmlspecialchars($row['project']) . "</td></tr>";
            echo "<tr><td><strong>Findings:</strong></td><td contenteditable='true' id='findings' name='findings'>" . (htmlspecialchars($row['findings']) == "" ? "None" : (htmlspecialchars($row['findings']))) . "</td></tr>";
            echo "<tr><td><strong>Key Investment Area:</strong></td><td contenteditable='true' id='kia' name='kia'>" . htmlspecialchars($row['kia']) . "</td></tr>";
            echo "<tr><td><strong>Payroll:</strong></td><td class='editable-cell' id='payroll' name='payroll' data-date='" . (htmlspecialchars($row['payroll']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['payroll']))) . "'>" . (htmlspecialchars($row['payroll']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['payroll']))) . "</td></tr>";
            echo "<tr><td><strong>Time Tally Sheet:</strong></td><td class='editable-cell' id='tts' name='tts' data-date='" . (htmlspecialchars($row['tts']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['tts']))) . "'>" . (htmlspecialchars($row['tts']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['tts']))) . "</td></tr>";
            echo "<tr><td><strong>Work Accomplishment Report:</strong></td><td class='editable-cell' id='war' name='war' data-date='" . (htmlspecialchars($row['war']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['war']))) . "'>" . (htmlspecialchars($row['war']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['war']))) . "</td></tr>";
            echo "<tr><td><strong>Certificate of Completion:</strong></td><td class='editable-cell' id='coc' name='coc' data-date='" . (htmlspecialchars($row['coc']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['coc']))) . "'>" . (htmlspecialchars($row['coc']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['coc']))) . "</td></tr>";
            echo "<tr><td><strong>Certificate of Correct Spelling:</strong></td><td class='editable-cell' id='spelling' name='spelling' data-date='" . (htmlspecialchars($row['spelling']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['spelling']))) . "'>" . (htmlspecialchars($row['spelling']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['spelling']))) . "</td></tr>";
            echo "<tr><td><strong>Before Pics:</strong></td><td class='editable-cell' id='geobefore' name='geobefore' data-date='" . (htmlspecialchars($row['geobefore']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['geobefore']))) . "'>" . (htmlspecialchars($row['geobefore']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['geobefore']))) . "</td></tr>";
            echo "<tr><td><strong>During Pics:</strong></td><td class='editable-cell' id='geoduring' name='geoduring' data-date='" . (htmlspecialchars($row['geoduring']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['geoduring']))) . "'>" . (htmlspecialchars($row['geoduring']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['geoduring']))) . "</td></tr>";
            echo "<tr><td><strong>After Pics:</strong></td><td class='editable-cell' id='geoafter' name='geoafter' data-date='" . (htmlspecialchars($row['geoafter']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['geoafter']))) . "'>" . (htmlspecialchars($row['geoafter']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['geoafter']))) . "</td></tr>";
            echo "<tr><td><strong>Summary of Replacements:</strong></td><td type='date' class='editable-cell' id='replacementsDate' name='replacementsDate' data-date='" . (htmlspecialchars($row['replacementsDate']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['replacementsDate']))) . "'>" . (htmlspecialchars($row['replacementsDate']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['replacementsDate']))) . "</td></tr>";
            echo "<tr><td><strong>Number of Replacements:</strong></td><td contenteditable='true' id='replacements' name='replacements'>" . (htmlspecialchars($row['replacements']) == "0" ? "-" : htmlspecialchars($row['replacements'])) . "</td></tr>";
            echo "<tr><td><strong>MEB for Replacements:</strong></td><td class='editable-cell' id='mebRDate' name='mebRDate' data-date='" . (htmlspecialchars($row['mebRDate']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['mebRDate']))) . "'>" . (htmlspecialchars($row['mebRDate']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['mebRDate']))) . "</td></tr>";
            echo "<tr><td><strong>Number of Replacements:</strong></td><td contenteditable='true' id='mebR' name='mebR'>" . (htmlspecialchars($row['mebR']) == "0" ? "-" : htmlspecialchars($row['mebR'])) . "</td></tr>";
            echo "<tr><td><strong>Brgy. Reso. on Lot Utilization:</strong></td><td class='editable-cell' id='brgyReso' name='brgyReso' data-date='" . (htmlspecialchars($row['brgyReso']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['brgyReso']))) . "'>" . (htmlspecialchars($row['brgyReso']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['brgyReso']))) . "</td></tr>";
            echo "<tr><td><strong>MOA/Cert. on Lot Utilization:</strong></td><td class='editable-cell' id='moaCert' name='moaCert' data-date='" . (htmlspecialchars($row['moaCert']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['moaCert']))) . "'>" . (htmlspecialchars($row['moaCert']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['moaCert']))) . "</td></tr>";
            echo "<tr><td><strong>BLGU Minutes w/ Photo Docs:</strong></td><td class='editable-cell' id='minutes' name='minutes' data-date='" . (htmlspecialchars($row['minutes']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['minutes']))) . "'>" . (htmlspecialchars($row['minutes']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['minutes']))) . "</td></tr>";
            echo "<tr><td><strong>PLGU Endorsement:</strong></td><td class='editable-cell' id='endorsement' name='endorsement' data-date='" . (htmlspecialchars($row['endorsement']) == "0000-00-00" ? "" : date('m-d-Y', strtotime($row['endorsement']))) . "'>" . (htmlspecialchars($row['endorsement']) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row['endorsement']))) . "</td></tr>";

        // Add other details as needed
        echo "</table>";

            // Check for update parameters and perform update
            if (isset($_POST['batchNumber'], $_POST['municipality'], $_POST['barangay'], $_POST['beneficiaries'], $_POST['fund'], $_POST['served'], $_POST['disbursed'], $_POST['percent'], $_POST['unpaid'], $_POST['undisbursed'], $_POST['specialPayout'], $_POST['norsa'], $_POST['payout'], $_POST['paymaster'], $_POST['orientation'], $_POST['speaker'], $_POST['secondDay'], $_POST['monitoring'], $_POST['evaluator'], $_POST['lastDay'], $_POST['difference'], $_POST['project'], $_POST['findings'], $_POST['kia'], $_POST['payroll'], $_POST['tts'], $_POST['war'], $_POST['coc'], $_POST['spelling'], $_POST['geobefore'], $_POST['geoduring'], $_POST['geoafter'], $_POST['replacementsDate'], $_POST['replacements'], $_POST['mebRDate'], $_POST['mebR'], $_POST['brgyReso'], $_POST['moaCert'], $_POST['minutes'], $_POST['endorsement'])) {
                $batchNumber = $_POST['batchNumber'];
                $municipality = $_POST['municipality'];
                $barangay = $_POST['barangay'];
                $beneficiaries = $_POST['beneficiaries'];
                $fund = str_replace(',', "", $_POST['fund']);
                $served = $_POST['served'];
                $disbursed = str_replace(',', '', $_POST['disbursed']);
                $percent = $_POST['percent'];
                $unpaid = $_POST['unpaid'];
                $undisbursed = str_replace(',', '', $_POST['undisbursed']);
                $specialPayout = $_POST['specialPayout'];
                $norsa = $_POST['norsa'];
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
                $spelling = $_POST['spelling'];
                $geobefore = $_POST['geobefore'];
                $geoduring = $_POST['geoduring'];
                $geoafter = $_POST['geoafter'];
                $replacementsDate = $_POST['replacementsDate'];
                $replacements = $_POST['replacements'];
                $mebRDate = $_POST['mebRDate'];
                $mebR = $_POST['mebR'];
                $brgyReso = $_POST['brgyReso'];
                $moaCert = $_POST['moaCert'];
                $minutes = $_POST['minutes'];
                $endorsement = $_POST['endorsement'];

                // Update the database with new values
                $updateSql = "UPDATE trackdata SET batchNumber = ?, municipality = ?, barangay = ?, beneficiaries = ?, fund = ?, served = ?, disbursed = ?, percent = ?, unpaid = ?, undisbursed = ?, specialPayout = ?, norsa = ?, payout = STR_TO_DATE(?, '%m-%d-%Y'), paymaster = ?, orientation = STR_TO_DATE(?, '%m-%d-%Y'), speaker = ?, secondDay = STR_TO_DATE(?, '%m-%d-%Y'), monitoring = ?, evaluator = ?, lastDay = STR_TO_DATE(?, '%m-%d-%Y'), difference = ?, project = ?, findings = ?, kia = ?, payroll = STR_TO_DATE(?, '%m-%d-%Y'), tts = STR_TO_DATE(?, '%m-%d-%Y'), war = STR_TO_DATE(?, '%m-%d-%Y'), coc = STR_TO_DATE(?, '%m-%d-%Y'), spelling = STR_TO_DATE(?, '%m-%d-%Y'), geobefore = STR_TO_DATE(?, '%m-%d-%Y'), geoduring = STR_TO_DATE(?, '%m-%d-%Y'), geoafter = STR_TO_DATE(?, '%m-%d-%Y'), replacementsDate = STR_TO_DATE(?, '%m-%d-%Y'), replacements = ?, mebRDate = STR_TO_DATE(?, '%m-%d-%Y'), mebR = ?, brgyReso = STR_TO_DATE(?, '%m-%d-%Y'), moaCert = STR_TO_DATE(?, '%m-%d-%Y'), minutes = STR_TO_DATE(?, '%m-%d-%Y'), endorsement = STR_TO_DATE(?, '%m-%d-%Y') WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("issiiiisiiiissssssssissssssssssssisissssi", $batchNumber, $municipality, $barangay, $beneficiaries, $fund, $served, $disbursed, $percent, $unpaid, $undisbursed, $specialPayout, $norsa, $payout, $paymaster, $orientation, $speaker, $secondDay, $monitoring, $evaluator, $lastDay, $difference, $project, $findings, $kia, $payroll, $tts, $war, $coc, $spelling, $geobefore, $geoduring, $geoafter, $replacementsDate, $replacements, $mebRDate, $mebR, $brgyReso, $moaCert, $minutes, $endorsement, $id);

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