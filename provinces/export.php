<?php
date_default_timezone_set('Asia/Manila');

require '../PhpSpreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include "../config.php";

// Check if the province parameter is set
if (!isset($_GET['province'])) {
    die('Province parameter not set.');
}

$province = $_GET['province'];

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Add column headers
$columnHeaders = array('LGU', 'Progress', 'Barangay', 'No. of Target Beneficiaries', 'No. of Served Beneficiaries', 'Orientation', 'Payout', 'Remark', 'Timeliness');
$sheet->fromArray($columnHeaders, NULL, 'A1');

// Fetch data from the database for the specified province
$dataRows = array();
$municipalities = getMunicipalities($conn, $province);
$rowIndex = 2;

foreach ($municipalities as $municipality) {
    $barangays = getBarangaysWithBeneficiaries($conn, $municipality);

    foreach ($barangays as $index => $barangay) {
        if ($index > 0) {
            $rowIndex++;
        }

        $dataRows[] = array(
            $municipality,
            $barangay['percent'].'%',
            $barangay['barangay'],
            $barangay['beneficiaries'],
            $barangay['served'],
            formatDate($barangay['orientation']),
            formatDate($barangay['payout']),
            ($barangay['beneficiaries'] - $barangay['served'] == 0) ? '100% Disbursed' : "{$barangay['unpaid']} unpaid",
            ($barangay['difference'] < 2) ? "Paid {$barangay['difference']} Day from the last working day" : "Paid {$barangay['difference']} Days from the last working day"
        );
    }
}

// Add data to the spreadsheet
$sheet->fromArray($dataRows, NULL, 'A2');

// Create a writer object
$writer = new Xlsx($spreadsheet);

// Set headers for download
$filename = strtolower(str_replace(' ', '', $province)) . "_summary_" . date('Ymd_His') . ".xlsx"; // Include date and time in the filename
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Output the Excel file to the browser
$writer->save('php://output');
exit();

// Close the database connection
$conn->close();

function getMunicipalities($conn, $province) {
    $sql = "SELECT DISTINCT municipality FROM trackdata WHERE province = '$province'";
    $result = mysqli_query($conn, $sql);
    $municipalities = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $municipalities[] = $row['municipality'];
    }

    return $municipalities;
}

function getBarangaysWithBeneficiaries($conn, $municipality) {
    $sql = "SELECT barangay, beneficiaries, served, orientation, payout, percent, unpaid, difference, lastDay AS barangayGroup FROM trackdata WHERE municipality = '$municipality' GROUP BY barangay";
    $result = mysqli_query($conn, $sql);
    $barangays = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $barangays[] = array(
            'barangay' => $row['barangay'],
            'beneficiaries' => $row['beneficiaries'],
            'served' => $row['served'],
            'percent' => $row['percent'],
            'unpaid' => $row['unpaid'],
            'orientation' => formatDate($row['orientation']),
            'payout' => formatDate($row['payout']),
            'difference' => $row['difference']
        );
    }

    return $barangays;
}

function formatDate($dateString) {
    // Assuming the date format is Y-m-d, change accordingly if it's different
    $timestamp = strtotime($dateString);
    return date('F j, Y', $timestamp);
}

function getColorBasedOnPercent($percent) {
    if ($percent >= 100) {
        return 'skyblue';
    } elseif ($percent >= 75) {
        return 'green';
    } elseif ($percent >= 50) {
        return 'orange';
    } elseif ($percent >= 25) {
        return 'pink';
    } else {
        return 'red';
    }
}
?>