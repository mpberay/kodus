<?php
include "config.php";
require "PhpSpreadsheet/vendor/autoload.php";

session_start();

date_default_timezone_set('Asia/Manila');

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: .");
    exit();
}

// Set the last activity time on login if not set
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

// Function to get the count of municipalities for a specific province
function getMunicipalityCount($conn, $province) {
    $sql = "SELECT COUNT(DISTINCT municipality) AS total_municipalities FROM trackdata WHERE province = '$province'";
    $result = mysqli_query($conn, $sql);

    // Check for errors
    if (!$result) {
        die("Error in SQL query for $province: " . mysqli_error($conn));
    }

    // Fetch the result
    $row = mysqli_fetch_assoc($result);

    // Return the count
    return $row['total_municipalities'];
}

// Function to get the count of barangays for a specific province
function getBarangayCount($conn, $province) {
    $sql = "SELECT COUNT(DISTINCT barangay) AS total_barangays FROM trackdata WHERE province = '$province'";
    $result = mysqli_query($conn, $sql);

    // Check for errors
    if (!$result) {
        die("Error in SQL query for $province: " . mysqli_error($conn));
    }

    // Fetch the result
    $row = mysqli_fetch_assoc($result);

    // Return the count
    return $row['total_barangays'];
}

// Function to get the sum of beneficiaries for a specific province
function getBeneficiarySum($conn, $province) {
    $sql = "SELECT SUM(beneficiaries) AS total_beneficiaries FROM trackdata WHERE province = '$province'";
    $result = mysqli_query($conn, $sql);

    // Check for errors
    if (!$result) {
        die("Error in SQL query for $province: " . mysqli_error($conn));
    }

    // Fetch the result
    $row = mysqli_fetch_assoc($result);

    // Return the count
    return $row['total_beneficiaries'];
}

// Function to get the sum of served beneficiaries for a specific province
function getServedSum($conn, $province) {
    $sql = "SELECT SUM(served) AS total_served FROM trackdata WHERE province = '$province'";
    $result = mysqli_query($conn, $sql);

    // Check for errors
    if (!$result) {
        die("Error in SQL query for $province: " . mysqli_error($conn));
    }

    // Fetch the result
    $row = mysqli_fetch_assoc($result);

    // Return the count
    return $row['total_served'];
}

// Function to calculate the variance for a specific province
function getVariance($conn, $province) {
    $totalBeneficiaries = getBeneficiarySum($conn, $province);
    $totalServed = getServedSum($conn, $province);

    // Calculate the variance
    $variance = $totalBeneficiaries - $totalServed;

    return $variance;
}

// Function to determine font color based on variance
function getFontColorA($variance) {
    return ($variance > 0) ? 'darkred !important' : 'inherit';
}

// Function to determine font color based on variance
function getFontColorB($totalVariance) {
    return ($totalVariance > 0) ? '#CC3366 !important' : 'inherit';
}

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: .");
    exit();
}

// Set the last activity time on login if not set
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

$username = $_SESSION['username'];

// Assuming you have a mysqli connection, fetch the user's details
// Replace 'users' with your actual table name and 'password_column' with the column name storing the first name
$query = "SELECT picture FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $picture);

// Fetch the result
mysqli_stmt_fetch($stmt);

mysqli_stmt_close($stmt);

$currentPath = basename($_SERVER['PHP_SELF']); // Get the current file name

// Function to add a class based on the current path
function addActiveClass($path, $currentPage) {
    if ($path === $currentPage) {
        echo 'current'; // This class will be added if the paths match
    }
}

// Export Table functionality
if (isset($_POST['export'])) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add table header
    $header = array(
        'Tracking Number', 'Province', 'PDO', 'Batch', 'Municipality', 'Barangay', 'Target No. of Beneficiaries', 'Funds Allocated', 'No. of Served Beneficiaries', 'Amount Disbursed', 'Percentage', 'No. of Unpaid Beneficiaries', 'Undisbursed Amount', 'For Special Payout', 'For NORSA', 'Payout Date', 'Paymasters', 'Orientation Date', 'Speaker', 'Second Day', 'Project Monitoring by RRP-CFW Team', 'Evaluator', 'Last Day', 'Difference', 'Project', 'Findings', 'Key Investment Area', 'Payroll', 'Time Tally Sheet', 'Work Accomplishment Report', 'Certificate of Completion', 'Before Pics', 'During Pics', 'After Pics', 'Certificate of Correct Spelling', 'Summary of Replacements', 'Number of Replacements', 'MEB for Replacements', 'Number of Replacements', 'Brgy. Reso. on Lot Utilization', 'MOA/Cert. on Lot Utilization', 'BLGU Minutes w/ Photo Docs', 'PLGU Endorsement'
    );

    $column = 'A';
    foreach ($header as $value) {
        $sheet->setCellValue($column . '1', $value);
        $column++;
    }

    // Fetch data from database
    $sql = "SELECT tracking_number, province, pdo, batchNumber, municipality, barangay, beneficiaries, fund, served, disbursed, percent, unpaid, undisbursed, specialPayout, norsa, payout, paymaster, orientation, speaker, secondDay, monitoring, evaluator, lastDay, difference, project, findings, kia, payroll, tts, war, coc, geobefore, geoduring, geoafter, spelling, replacementsDate, replacements, mebRDate, mebR, brgyReso, moaCert, minutes, endorsement FROM trackdata WHERE track_type = 1";
    $result = $conn->query($sql);

    $row = 2; // Start from row 2 to avoid overwriting headers
while ($data = $result->fetch_assoc()) {
    $column = 'A';
    foreach ($data as $key => $value) {
        if ($key == 'percent') {
            // Append '%' to the percentage value
            $value .= '%';
        }
        $sheet->setCellValue($column . $row, $value);
        $column++;
    }
    $row++;
}

    // Set header for download
    $filename = "document_tracking_" . date('Ymd_His') . ".xlsx"; // Include date and time in the filename
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit();
}

// Export2 Table functionality
if (isset($_POST['export2'])) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add table header
    $header = array(
        'Tracking Number', 'Date Received by the ADAS', 'Tracking Date', 'Province', 'Municipality', 'Barangay', 'Quantity in MEB', 'Quantity in MEB', 'Remarks'
    );

    $column = 'A';
    foreach ($header as $value) {
        $sheet->setCellValue($column . '1', $value);
        $column++;
    }

    // Fetch data from database
    $sql = "SELECT tracking_number, adas, trackDate, tProvince, tMunicipality, tBarangay, meb, mer, remarks FROM trackdata WHERE tProvince != ''";
    $result = $conn->query($sql);

    $row = 2; // Start from row 2 to avoid overwriting headers
while ($data = $result->fetch_assoc()) {
    $column = 'A';
    foreach ($data as $key => $value) {
        if ($key == 'percent') {
            // Append '%' to the percentage value
            $value .= '%';
        }
        $sheet->setCellValue($column . $row, $value);
        $column++;
    }
    $row++;
}

    // Set header for download
    $filename = "meb_tracking_" . date('Ymd_His') . ".xlsx"; // Include date and time in the filename
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit();
}

// Export3 Table functionality
if (isset($_POST['export3'])) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add table header
    $header = array(
        'Date', 'Tracking Number', 'Description', 'JM Gwapo', 'RRP Focal', 'Remarks', 'DRRS Head', 'File'
    );

    $column = 'A';
    foreach ($header as $value) {
        $sheet->setCellValue($column . '1', $value);
        $column++;
    }

    // Fetch data from database
    $sql = "SELECT aaDate, tracking_number2, description, jmGwapo, focal, remarks2, sHead, file_name FROM aatracker WHERE aaType = 1";
    $result = $conn->query($sql);

    $row = 2; // Start from row 2 to avoid overwriting headers
while ($data = $result->fetch_assoc()) {
    $column = 'A';
    foreach ($data as $key => $value) {
        if ($key == 'percent') {
            // Append '%' to the percentage value
            $value .= '%';
        }
        $sheet->setCellValue($column . $row, $value);
        $column++;
    }
    $row++;
}

    // Set header for download
    $filename = "adas_incoming_tracking_" . date('Ymd_His') . ".xlsx"; // Include date and time in the filename
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit();
}

// Export4 Table functionality
if (isset($_POST['export4'])) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add table header
    $header = array(
        'Outgoing Date', 'Tracking Number', 'Description', 'Receiving Office / Personnel', 'Date Received'
    );

    $column = 'A';
    foreach ($header as $value) {
        $sheet->setCellValue($column . '1', $value);
        $column++;
    }

    // Fetch data from database
    $sql = "SELECT outDate, tracking_number2, description, personnel, dateReceived FROM aatracker WHERE aaType = 2";
    $result = $conn->query($sql);

    $row = 2; // Start from row 2 to avoid overwriting headers
while ($data = $result->fetch_assoc()) {
    $column = 'A';
    foreach ($data as $key => $value) {
        if ($key == 'percent') {
            // Append '%' to the percentage value
            $value .= '%';
        }
        $sheet->setCellValue($column . $row, $value);
        $column++;
    }
    $row++;
}

    // Set header for download
    $filename = "adas_outgoing_tracking_" . date('Ymd_His') . ".xlsx"; // Include date and time in the filename
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit();
}

// Fetch data from your database
$sql = "SELECT id, tracking_number, province, pdo, batchNumber, municipality, barangay, beneficiaries, fund, served, disbursed, percent, unpaid, undisbursed, specialPayout, norsa, payout, paymaster, orientation, speaker, secondDay, monitoring, evaluator, lastDay, difference, project, findings, kia, payroll, tts, war, coc, geobefore, geoduring, geoafter, spelling, replacementsDate, replacements, mebRDate, mebR, brgyReso, moaCert, minutes, endorsement FROM trackdata WHERE track_type = 1";
$result = $conn->query($sql);

$sql2 = "SELECT id, tracking_number, adas, trackDate, tProvince, tMunicipality, tBarangay, meb, mer, remarks FROM trackdata WHERE track_type = 2";
$result2 = $conn->query($sql2);

$sql3 = "SELECT id, aaDate, tracking_number2, description, jmGwapo, focal, remarks2, sHead, file_name, file_size, file_type, upload_time FROM aatracker WHERE aaType = 1";
$result3 = $conn->query($sql3);

$sql4 = "SELECT id, outDate, tracking_number2, description, personnel, dateReceived, remarks2, file_name FROM aatracker WHERE aaType = 2";
$result4 = $conn->query($sql4);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="http://localhost/kodus/js/jquery-3.6.4.min.js"></script>
    <script src="http://localhost/kodus/js/sweetalert2@10.js"></script>
    <script src="http://localhost/kodus/js/7cec42d8be.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="http://localhost/kodus/script.js"></script>
    <script type="text/javascript" src="http://localhost/kodus/dataScript.js"></script>
    <script type="text/javascript" src="http://localhost/kodus/js/w3.js"></script>
	<link rel="icon" href="http://localhost/kodus/assets/logo.ico" type="image/x-icon">
	<link rel="stylesheet" type="text/css" href="http://localhost/kodus/css/style.css">
    <link rel="stylesheet" href="http://localhost/kodus/css/sweetalert2.min.css">
    <link rel="stylesheet" href="http://localhost/kodus/css/font-awesome.min.css">
</head>
<body>
    <div id="loading-overlay">
        <div id="loading-spinner"></div>
    </div>
	<div class="header" id="header">
	<div class="fixed-header"></div>
		<h2 class="head">
			<button id="navBtn" onclick="sidenavToggle()">☰</button>
            <span id="text" class="smooth-transition">KliMalasakit Online Document Updating System</span>
		</h2>
		<div class="profile-container">
			<img class="profile" src="http://localhost/kodus/pictures/<?php echo htmlspecialchars($picture, ENT_QUOTES, 'UTF-8'); ?>">
			<div class="dropdown-content">
				<img class="big-profile" src="http://localhost/kodus/pictures/<?php echo htmlspecialchars($picture, ENT_QUOTES, 'UTF-8'); ?>">
				<!-- <div class="desc">Logo</div> -->
			</div>
		</div>
		<div class="username">
			<i class="fa fa-caret-down"></i><button class="usrDrpDwn"><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></button>
			<div class="usrOpt">
				<a class="drpdwn" href="http://localhost/kodus/settings"><span class="fa fa-gear">&nbsp;</span>Settings</a>
				<hr>
				<a class="drpdwn" id="logoutButton"><span class="fa fa-sign-out">&nbsp;</span>Logout</a>
			</div>
		</div>
	</div>
	<i id="dayNightToggle" class="fa fa-moon-o" onclick="toggleMode()" hidden></i>
	<button id="showLoginButton" hidden></button>
    <button id="showRegisterButton" hidden></button>
</body>
</html>