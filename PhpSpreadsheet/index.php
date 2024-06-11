<?php

require 'vendor/autoload.php'; // Include PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

// Function to import data from Excel file
function importPayrollData($excelFile)
{
    try {
        $spreadsheet = IOFactory::load($excelFile);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);

        // Assume the Excel file has columns like 'EmployeeName', 'Salary', 'Bonus', etc.
        $payrollData = [];

        // Skip the first row (headers)
        array_shift($data);

        foreach ($data as $row) {
            // Process data or save to database
            // Here, we're just storing it in an array for demonstration purposes
            $payrollData[] = [
                'employee_name' => $row['A'],
                'salary' => $row['B'],
                'bonus' => $row['C'],
            ];
        }

        return $payrollData;
    } catch (\Exception $e) {
        // Handle errors, e.g., file not found, invalid format, etc.
        die('Error loading Excel file: ' . $e->getMessage());
    }
}

// Example usage:
$excelFile = 'excel/payrollData.xlsx';
$payrollData = importPayrollData($excelFile);

// Output the final result as an HTML table with border and collapse
echo '<table style="border: 1px solid black; border-collapse: collapse;">';
echo '<tr><th style="border: 1px solid black;">Employee Name</th><th style="border: 1px solid black;">Salary</th><th style="border: 1px solid black;">Bonus</th></tr>';

foreach ($payrollData as $employee) {
    echo '<tr>';
    echo '<td style="border: 1px solid black;">' . $employee['employee_name'] . '</td>';
    echo '<td style="border: 1px solid black;">' . $employee['salary'] . '</td>';
    echo '<td style="border: 1px solid black;">' . $employee['bonus'] . '</td>';
    echo '</tr>';
}

echo '</table>';
