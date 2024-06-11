<?php
	include "header.php";
	include "sidenav.php";
    include "script.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Home | KODUS</title>
</head>
<body>
    <div id="loading-overlay">
        <div id="loading-spinner"></div>
    </div><br>
	<div class="content" id="content">
        <h3 class="secondHead">Physical and Financial Targets and Accomplishments Summary</h3>
		<table class="center" id="myTable">
            <thead style="white-space: nowrap;">
                <tr class="head4">
                    <th>Province</th>
                    <th>No. of LGUs</th>
                    <th>No. Barangays</th>
                    <th>Target No. of Beneficiaries</th>
                    <th>Funds Allocated</th>
                    <th>No. of Beneficiaries Served</th>
                    <th>Amount Disbursed</th>
                    <th>Variance</th>
                    <th>Amount Undisbursed</th>
                </tr>
            </thead>
            <tbody class="through" id="homeTable-body" style="white-space: nowrap;">
                <?php
                // Variable to store the total municipality count
                $totalMunicipalityCount = 0;
                $totalBarangayCount = 0;
                $totalBeneficiarySum = 0;
                $totalFundAllocation =0;
                $totalServedSum = 0;
                $totalDisbursedFund =0;
                $totalVariance = 0;
                $totalUndisbursedFund = 0;


                // Loop through provinces
                $provinces = array('Agusan del Norte', 'Agusan del Sur', 'Dinagat Islands', 'Surigao Del Norte', 'Surigao Del Sur');
                foreach ($provinces as $province) {
                    $municipalityCount = getMunicipalityCount($conn, $province);
                    $totalMunicipalityCount += $municipalityCount;
                    $barangayCount = getBarangayCount($conn, $province);
                    $totalBarangayCount += $barangayCount;
                    $beneficiarySum = getBeneficiarySum($conn, $province);
                    $totalBeneficiarySum += $beneficiarySum;
                    $fundAllocation = $beneficiarySum * 7400;
                    $totalFundAllocation += $fundAllocation;
                    $servedSum = getServedSum($conn, $province);
                    $totalServedSum += $servedSum;
                    $disbursedFund = $servedSum * 7400;
                    $totalDisbursedFund += $disbursedFund;
                    $variance = getVariance($conn, $province);
                    $totalVariance = $totalBeneficiarySum - $totalServedSum;
                    $undisbursedFund = $variance * 7400;
                    $totalUndisbursedFund += $undisbursedFund;
                    $fontColorA = getFontColorA($variance);
                    $fontColorB = getFontColorB($totalVariance);

                    echo "<tr onclick=\"window.location.href='provinces/" . str_replace(" ", "", strtolower($province)) . "'\">";
                    echo "<td>$province</td>";
                    echo "<td>$municipalityCount</td>";
                    echo "<td>$barangayCount</td>";
                    echo "<td>" . ($beneficiarySum == 0 ? 0 : $beneficiarySum) . "</td>";
                    echo "<td>" . number_format($fundAllocation, 2, '.', ',') . "</td>";
                    echo "<td>" . ($servedSum == 0 ? 0 : $servedSum) . "</td>";
                    echo "<td>" . number_format($disbursedFund, 2, '.', ',') . "</td>";
                    echo "<td style='color: $fontColorA;'>$variance</td>";
                    echo "<td>" . number_format($undisbursedFund, 2, '.', ',') . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <?php
                echo "<tr>";
                    echo "<td><strong>TOTAL</strong></td>";
                    echo "<td><strong>$totalMunicipalityCount</strong></td>";
                    echo "<td><strong>$totalBarangayCount</strong></td>";
                    echo "<td><strong>$totalBeneficiarySum</strong></td>";
                    echo "<td><strong>" . number_format($totalFundAllocation, 2, '.', ',') . "</strong></td>";
                    echo "<td><strong>$totalServedSum</strong></td>";
                    echo "<td><strong>" . number_format($totalDisbursedFund, 2, '.', ',') . "</strong></td>";
                    echo "<td style='color: $fontColorB'><strong>$totalVariance</strong></td>";
                    echo "<td><strong>" . number_format($totalUndisbursedFund, 2, '.', ',') . "</strong></td>";
                echo "</tr>";
                ?>
            </tfoot>
        </table>
        <div class="tblAct">
            <button class="actBtn">Action <span class="arrowDwn">&#xfe40;</span></button>
            <div class="tblOpt">
                <button class="tblDrpDwn" onclick="exportToExcel()">Export Table</button>
            </div>
        </div>
        <br><br>
        <br><br>
        <!--<div class="video-container">
            <video controls autoplay muted>
                <source src="assets/orientation360p.mp4" type="video/mp4">
            </video>
        </div>-->
	</div>
    <script>
        function exportToExcel() {
            // Redirect to a new PHP file for processing the export
            window.location.href = 'export.php';
        }
    </script>
</body>
</html>