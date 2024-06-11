<?php
include "../header.php";
include "../sidenav.php";
include "../script.php";

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

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: .");
    exit();
}

// Set the last activity time on login if not set
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

// Assuming your database connection is stored in $conn
$municipalities = getMunicipalities($conn, 'Dinagat Islands');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>KODUS | Dinagat Islands</title>
</head>
<body>
    <div id="loading-overlay">
        <div id="loading-spinner"></div>
    </div><br>
    <div class="content" id="content">
        <h1 class="thirdHead">Dinagat Islands</h1>
    <div class="table-container">
        <table class="center" id="myTable">
            <thead>
                <tr>
                    <th>LGU</th>
                    <th>Progress</th>
                    <th>Barangay</th>
                    <th>No. of Target Beneficiaries</th>
                    <th>No. of Served Beneficiaries</th>
                    <th>Orientation</th>
                    <th>Payout</th>
                    <th>Remark</th>
                    <th>Timeliness</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($municipalities)) {
                    echo "<tr><td colspan='9'class='shaking-text'>No data available.</td></tr>";
                } else {
                    foreach ($municipalities as $municipality) {
                        $barangays = getBarangaysWithBeneficiaries($conn, $municipality);
                        $barangayCount = count($barangays);

                        echo "<tr>";
                        echo "<td rowspan='$barangayCount' style='max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis'>$municipality</td>";

                        foreach ($barangays as $index => $barangay) {
                            if ($index > 0) {
                                echo "<tr>";
                            }
                            $color = getColorBasedOnPercent($barangay['percent']);
                            echo "<td>";
                            echo "<div class='progress-bar'>";
                            echo "<div class='progress-bar-fill' style='width: {$barangay['percent']}%; background: {$color};'></div><div class='percent'>{$barangay['percent']}%</div>";
                            echo "</div>";
                            echo "</td>";
                            echo "<td style='max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis'>{$barangay['barangay']}</td>";
                            echo "<td>{$barangay['beneficiaries']}</td>";
                            echo "<td>{$barangay['served']}</td>";
                            echo "<td style='max-width: 100px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis'>{$barangay['orientation']}</td>";
                            echo "<td style='max-width: 100px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis'>{$barangay['payout']}</td>";
                            echo "<td style='max-width: 100px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis'>";
                            if ($barangay['beneficiaries'] - $barangay['served'] == 0) {
                                echo "100% Disbursed";
                            } else {
                                echo "{$barangay['unpaid']} unpaid";
                            }
                            echo "</td>";
                            echo "<td style='max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis'>";
                            if ($barangay['difference'] <2) {
                                echo "Paid {$barangay['difference']} Day from the last working day";
                            } else {
                                echo "Paid {$barangay['difference']} Days from the last working day";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
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
            </tbody>
        </table>
    </div>
        <div class="tblAct">
            <button class="actBtn">Action <span class="arrowDwn">&#xfe40;</span></button>
            <div class="tblOpt">
                <button class="tblDrpDwn" onclick="exportToExcel()">Export Table</button>
            </div>
        </div>
    </div>
    <script>
        function exportToExcel() {
            window.location.href = 'export.php?province=Dinagat Islands';
        }
    </script>
            <button id="showLoginButton" hidden></button>
            <button id="showRegisterButton" hidden></button>
            <div id="myTooltip2" hidden></div>
</body>
</html>