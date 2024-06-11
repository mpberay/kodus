<?php
	include "header.php";
	include "sidenav.php";
    include "script.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Data Tracking | KODUS</title>
</head>
<body>
    <div id="loading-overlay">
        <div id="loading-spinner"></div>
    </div><br>
	<div class="content" id="content">
        <h3 class="secondHead">Documents Tracking</h3>
        <div id="sE">
        <div class="search">
            &nbsp; <input type="text" id="searchInput" placeholder="Search..."><i class="clear-button" onclick="clearSearch()">&times;</i>
            <button class="btn" onclick="showSelectForm()">&nbsp;<span class="fa fa-file-pen"></span>Track Document</button>
        </div>
    </div>
        <div class="table-container" id="data-table">
    		<table class="center" id="myTable">
                <thead class="sticky-header">
                    <tr class="head1">
                        <th class="action" style="z-index: 2; left: 0; position:sticky; left: 0; background-color: white !important" rowspan="4">Action</th>
                        <th rowspan="4" onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(2)')" style="cursor: pointer;">Tracking Number</th>
                        <th colspan="4">Profile</th>
                        <th colspan="12">Physical and Financial Target Accomplishment</th>
                        <th colspan="10">Implementation</th>
                        <th colspan="15">Post-Implementation</th>
                        <th rowspan="4">PLGU Endorsement</th>
                    </tr>
                    <tr class="head2">
                    <!-- <th class="province" rowspan="3">Province</th> -->
                        <th rowspan="3" onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(3)')" style="cursor: pointer;">Province</th>
                        <th rowspan="3" onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(4)')" style="cursor: pointer;">PDO</th>
                        <th rowspan="3" onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(5)')" style="cursor: pointer;">Batch</th>
                    <!-- <th class="municipality" rowspan="3">Municipality</th> -->
                        <th rowspan="3" onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(6)')" style="cursor: pointer;">Municipality</th>
                        <th colspan="3">Per Allocation/Obligation</th>
                        <th colspan="3">Accomplishment</th>
                        <th colspan="4">GAP</th>
                        <th colspan="2">Payout Details</th>
                        <th colspan="2">BLGU Orientation</th>
                        <th rowspan="3" onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(21)')" style="cursor: pointer;">2nd Day (1st Training Day)</th>
                        <th rowspan="3">Project Monitoring by RRP-CFW Team</th>
                        <th rowspan="3">Evaluator</th>
                        <th rowspan="3" onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(24)')" style="cursor: pointer;">Last Day</th>
                        <th rowspan="3" onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(25)')" style="cursor: pointer;">Difference</th>
                        <th rowspan="3">Project</th>
                        <th rowspan="3">Findings</th>
                        <th rowspan="3">Key Investment Area</th>
                        <th colspan="7">Payout Documents/Pre-Liquidation Stage</th>
                        <th colspan="8">Post-Payout Documents/Liquidation Stage</th>
                    </tr>
                    <tr class="head3">
                    <!-- <th class="barangay" rowspan="2">Barangay</th> -->
                        <th rowspan="2">Barangay</th>
                        <th rowspan="2" onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(8)')" style="cursor: pointer;">Target No. of Beneficiaries</th>
                        <th rowspan="2">Funds Allocated</th>
                        <th rowspan="2" onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(10)')" style="cursor: pointer;">No. of Served Beneficiaries</th>
                        <th rowspan="2">Amount Disbursed</th>
                        <th rowspan="2">Percentage</th>
                        <th rowspan="2" onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(13)')" style="cursor: pointer;">No. of Unpaid Beneficiaries</th>
                        <th rowspan="2">Undisbursed Amount</th>
                        <th rowspan="2">For Special Payout</th>
                        <th rowspan="2">For NORSA</th>
                        <th rowspan="2" onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(17)')" style="cursor: pointer;">Payout Date</th>
                        <th rowspan="2">Paymasters</th>
                        <th rowspan="2" onclick="w3.sortHTML('#myTable', '.item', 'td:nth-child(19)')" style="cursor: pointer;">Date</th>
                        <th rowspan="2">Speaker</th>
                        <th rowspan="2">Payroll</th>
                        <th rowspan="2">Time Tally Sheet</th>
                        <th rowspan="2">Work Accomplishment Report</th>
                        <th rowspan="2">Certificate of Completion</th>
                        <th colspan="3">Geotagged Photos</th>
                        <th rowspan="2">Certificate of Correct Spelling</th>
                        <th colspan="2">Summary of Replacements</th>
                        <th colspan="2">MEB for Replacements</th>
                        <th colspan="2">Lot Utilization</th>
                        <th rowspan="2">BLGU Minutes w/ Photo Docs</th>
                    </tr>
                    <tr class="head4">
                        <th>Before Pics</th>
                        <th>During Pics</th>
                        <th>After Pics</th>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Brgy. Reso. on Lot Utilization</th>
                        <th>MOA/Cert. on Lot Utilization</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <?php
                    if ($result->num_rows === 0) {
                        echo "<tr><td colspan='44' class='shaking-text' style='text-align: left'>No data available.</td></tr>";
                    } else {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr class='item'>";
                            echo "<td class='action' style='position:sticky; left: 0; background-color: inherit !important'><button class='view-details-btn' data-id='" . htmlspecialchars($row['id']) . "'>Details</button></td>";
                            echo "<td style='white-space: nowrap'>" . $row['tracking_number'] . "</td>";
                        //  echo "<td class='province'>" . $row['province'] . "</td>";
                            echo "<td style='white-space: nowrap'>" . $row['province'] . "</td>";
                            echo "<td style='white-space: nowrap'>" . $row['pdo'] . "</td>";
                            echo "<td>" . htmlspecialchars($row["batchNumber"]) . "</td>";
                        //  echo "<td class='municipality'>" . htmlspecialchars($row["municipality"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["municipality"]) . "</td>";
                        //  echo "<td class='barangay'>" . htmlspecialchars($row["barangay"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["barangay"]) . "</td>";
                            echo "<td>" . number_format(htmlspecialchars($row["beneficiaries"]), 0, '', ',') . "</td>";
                            echo "<td style='text-align: left'>" . number_format(htmlspecialchars($row["fund"]), 2, '.', ',') . "</td>";
                            echo "<td>" . htmlspecialchars($row["served"]) . "</td>";
                            echo "<td style='text-align: left'>" . number_format(htmlspecialchars($row["disbursed"]), 2, '.', ',') . "</td>";
                            echo "<td>" . (htmlspecialchars($row["percent"]) == "" ? "0" : htmlspecialchars($row["percent"])) . '%' . "</td>";
                        //  echo "<td>" . number_format(htmlspecialchars($row["unpaid"]), 0, '', ',') . "</td>";
                            echo "<td>" . (htmlspecialchars($row['unpaid']) == 0 ? number_format(htmlspecialchars($row['beneficiaries']), 0, '', ',') : number_format(htmlspecialchars($row['unpaid']), 0, '', ',')) . "</td>";
                        //  echo "<td style='text-align: left'>" . number_format(htmlspecialchars($row["undisbursed"]), 2, '.', ',') . "</td>";
                            echo "<td style='text-align: left'>" . (htmlspecialchars($row["undisbursed"]) == 0 ? number_format(htmlspecialchars($row['fund']), 2, '.', ',') : number_format(htmlspecialchars($row['undisbursed']), 2, '.', ',')) . "</td>";
                            echo "<td>" . number_format(htmlspecialchars($row["specialPayout"]), 0, '.', ',') . "</td>";
                            echo "<td>" . number_format(htmlspecialchars($row["norsa"]), 0, '.', ',') . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["payout"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["payout"]))) . "</td>";
                            echo "<td style='max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis'>" . htmlspecialchars($row["paymaster"]) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["orientation"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["orientation"]))) . "</td>";
                            echo "<td style='max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis'>" . htmlspecialchars($row["speaker"]) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["secondDay"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["secondDay"]))) . "</td>";
                            echo "<td style='max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis'>" . htmlspecialchars($row["monitoring"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["evaluator"]) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["lastDay"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["lastDay"]))) . "</td>";
                            echo "<td>" . htmlspecialchars($row["difference"]) . "</td>";
                            echo "<td style='white-space: nowrap'>" . htmlspecialchars($row["project"]) . "</td>";
                            echo "<td>" . (htmlspecialchars($row["findings"]) == "" ? "None" : (htmlspecialchars($row["findings"]))) . "</td>";
                            echo "<td>" . htmlspecialchars($row["kia"]) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["payroll"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["payroll"]))) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["tts"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["tts"]))) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["war"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["war"]))) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["coc"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["coc"]))) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["geobefore"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["geobefore"]))) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["geoduring"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["geoduring"]))) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["geoafter"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["geoafter"]))) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["spelling"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["spelling"]))) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["replacementsDate"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["replacementsDate"]))) . "</td>";
                            echo "<td>" . (htmlspecialchars($row["replacements"]) == "0" ? "-" : htmlspecialchars($row["replacements"])) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["mebRDate"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["mebRDate"]))) . "</td>";
                            echo "<td>" . (htmlspecialchars($row["mebR"]) == "0" ? "-" : htmlspecialchars($row["mebR"])) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["brgyReso"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["brgyReso"]))) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["moaCert"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["moaCert"]))) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["minutes"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["minutes"]))) . "</td>";
                            echo "<td style='white-space: nowrap'>" . (htmlspecialchars($row["endorsement"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["endorsement"]))) . "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="tblAct">
            <button class="actBtn">Action <span class="arrowDwn">&#xfe40;</span></button>
            <div class="tblOpt">
                <button class="tblDrpDwn" onclick="exportTable()">Export Table</button>
            </div>
        </div>
        <center>
            <div id="noResultMessage" hidden>No result found.</div>
        </center>
	</div>
</body>

    <script>
        document.getElementById("searchInput").addEventListener("input", function () {
            var filter = this.value.toLowerCase();
            var table = document.getElementById("myTable");
            var tbodyRows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
            var noResultMessage = document.getElementById("noResultMessage");

            var hasResult = false;

            for (var i = 0; i < tbodyRows.length; i++) {
                var cells = tbodyRows[i].getElementsByTagName("td");
                var display = "none";

                for (var j = 1; j < cells.length; j++) {
                    // Skip the first cell (Action buttons) during the search
                    var cellText = cells[j].textContent || cells[j].innerText;
                    var matchIndex = cellText.toLowerCase().indexOf(filter);

                    if (matchIndex > -1) {
                        display = "";
                        hasResult = true;

                        // Highlight the matched keyword
                        var matchedText = cellText.substr(matchIndex, filter.length);
                        var highlightedText = cellText.replace(new RegExp(matchedText, 'i'), '<span class="highlighted">' + matchedText + '</span>');

                        cells[j].innerHTML = highlightedText;
                    } else {
                        // Reset the cell content if there is no match
                        cells[j].innerHTML = cellText;
                    }
                }

                tbodyRows[i].style.display = display;
            }

            // Display "No result found" message if there are no matching rows
            if (!hasResult) {
                noResultMessage.style.display = "block";
            } else {
                noResultMessage.style.display = "none";
            }
        });

        // JavaScript code for clear button
        function clearSearch() {
            document.getElementById("searchInput").value = "";
            // Trigger the input event to clear and reset the search
            var event = new Event('input');
            document.getElementById("searchInput").dispatchEvent(event);
        }
    </script>

    <script>
        // Export functions

        function exportTable() {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'data-tracking.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.responseType = 'blob';

            xhr.onload = function () {
                var a = document.createElement('a');

                // Use the filename received from the server-side PHP
                var filenameHeader = xhr.getResponseHeader('Content-Disposition');
                var matches = filenameHeader.match(/filename="(.+?)"/);

                if (matches && matches.length > 1) {
                    var filename = matches[1];
                    a.href = window.URL.createObjectURL(xhr.response);
                    a.download = filename;
                    a.style.display = 'none';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                } else {
                    console.error('Failed to extract filename from response headers.');
                }
            };

            xhr.send('export=true');
        }
    </script>

    <script>
        // Event delegation for the "View Details" button
        document.getElementById("table-body").addEventListener("click", function (event) {
            if (event.target.classList.contains("view-details-btn")) {
                var id = event.target.getAttribute("data-id");
                viewDetails(id);
            }
        });
    </script>

    <script>
        // Event delegation for the "Update" button
        document.getElementById("table-body").addEventListener("click", function (event) {
            if (event.target.classList.contains("update-details-btn")) {
                var id = event.target.getAttribute("data-id");
                updateDetails(id);
            }
        });
    </script>
</html>