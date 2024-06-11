<?php

date_default_timezone_set('Asia/Manila');

require 'PhpSpreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include "config.php";

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Add column headers
$columnHeaders = array('Province', 'No. of LGUs', 'No. Barangays', 'Target No. of Beneficiaries', 'Funds Allocated', 'No. of Beneficiaries Served', 'Amount Disbursed', 'Variance', 'Amount Undisbursed');
$sheet->fromArray($columnHeaders, NULL, 'A1');

// Fetch data from the database
$dataRows = array();
$provinces = array('Agusan del Norte', 'Agusan del Sur', 'Dinagat Islands', 'Surigao del Norte', 'Surigao del Sur');
$rowIndex = 2;

// Initialize total values
$totalMunicipalityCount = 0;
$totalBarangayCount = 0;
$totalBeneficiarySum = 0;
$totalFundAllocation = 0;
$totalServedSum = 0;
$totalDisbursedFund = 0;
$totalVariance = 0;
$totalUndisbursedFund = 0;

foreach ($provinces as $province) {
    // If the current province is "", skip database query and use calculated totals
    if ($province !== '') {
        $sql = "SELECT * FROM trackdata WHERE province = '$province'";
        $result = $conn->query($sql);

        if (!$result) {
            die("Error in SQL query for $province: " . $conn->error);
        }

        // Fetch data for the current province
        $municipalityCount = getMunicipalityCount($conn, $province);
        $barangayCount = getBarangayCount($conn, $province);
        $beneficiarySum = getBeneficiarySum($conn, $province);
        $fundAllocation = $beneficiarySum * 7400;
        $servedSum = getServedSum($conn, $province);
        $disbursedFund = $servedSum * 7400;
        $variance = getVariance($conn, $province);
        $undisbursedFund = $variance * 7400;
    }

    // Add data to the array
    $dataRows[] = array($province, $municipalityCount, $barangayCount, $beneficiarySum, $fundAllocation, $servedSum, $disbursedFund, $variance, $undisbursedFund);

    // Update total values
    $totalMunicipalityCount += $municipalityCount;
    $totalBarangayCount += $barangayCount;
    $totalBeneficiarySum += $beneficiarySum;
    $totalFundAllocation += $fundAllocation;
    $totalServedSum += $servedSum;
    $totalDisbursedFund += $disbursedFund;
    $totalVariance += $variance;
    $totalUndisbursedFund += $undisbursedFund;

    $rowIndex++;
}

// Add the "TOTAL" row
$dataRows[] = array('TOTAL', $totalMunicipalityCount, $totalBarangayCount, $totalBeneficiarySum, $totalFundAllocation, $totalServedSum, $totalDisbursedFund, $totalVariance, $totalUndisbursedFund);

// Add data to the spreadsheet
$sheet->fromArray($dataRows, NULL, 'A2');

// Create a writer object
$writer = new Xlsx($spreadsheet);

// Set headers for download
$filename = "summary_table_" . date('Ymd_His') . ".xlsx"; // Include date and time in the filename
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Output the Excel file to the browser
$writer->save('php://output');
exit();

// Close the database connection
$conn->close();

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
?>