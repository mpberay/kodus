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
        <h3 class="secondHead">Master List of Eligible Beneficiaries Tracking</h3>
        <div id="sE2">
        <div class="search">
            &nbsp; <input type="text" id="searchInput2" placeholder="Search..."><i class="clear-button" onclick="clearSearch2()">&times;</i>
            <button class="btn" onclick="showSelectForm()">&nbsp;<span class="fa fa-file-pen"></span>Track Document</button>
        </div>
    </div>
    <div class="table-container" id="data-table2">
        <table class="center" id="mySecondTable">
            <thead style="white-space: nowrap;">
                <tr>
                    <th class='action' style='position:sticky; left: 0; background-color: white !important'>Action</th>
                    <th onclick="w3.sortHTML('#mySecondTable', '.item2', 'td:nth-child(2)')" style="cursor: pointer;">Tracking Number</th>
                    <th onclick="w3.sortHTML('#mySecondTable', '.item2', 'td:nth-child(3)')" style="cursor: pointer;">Date Received by the ADAS</th>
                    <th onclick="w3.sortHTML('#mySecondTable', '.item2', 'td:nth-child(4)')" style="cursor: pointer;">Tracking Date</th>
                    <th onclick="w3.sortHTML('#mySecondTable', '.item2', 'td:nth-child(5)')" style="cursor: pointer;">Province</th>
                    <th onclick="w3.sortHTML('#mySecondTable', '.item2', 'td:nth-child(6)')" style="cursor: pointer;">LGU</th>
                    <th onclick="w3.sortHTML('#mySecondTable', '.item2', 'td:nth-child(7)')" style="cursor: pointer;">Barangay</th>
                    <th onclick="w3.sortHTML('#mySecondTable', '.item2', 'td:nth-child(8)')" style="cursor: pointer;">Quantity in MEB</th>
                    <th onclick="w3.sortHTML('#mySecondTable', '.item2', 'td:nth-child(9)')" style="cursor: pointer;">Quantity in MER</th>
                    <th onclick="w3.sortHTML('#mySecondTable', '.item2', 'td:nth-child(10)')" style="cursor: pointer;">Remarks</th>
                </tr>
            </thead>
            <tbody id="table-body2" style="white-space: nowrap;">
                <?php
                if ($result2->num_rows === 0) {
                    echo "<tr><td colspan='10' class='shaking-text'>No data available.</td></tr>";
                } else {
                    while ($row = $result2->fetch_assoc()) {
                        echo "<tr class='item2'>";
                        echo "<td class='action' style='position:sticky; left: 0; background-color: white !important'><button class='view-details-btn2' data2-id='" . htmlspecialchars($row['id']) . "'>Details</button></td>";
                        echo "<td>" . $row['tracking_number'] . "</td>";
                        echo "<td>" . (htmlspecialchars($row["adas"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["adas"]))) . "</td>";
                        echo "<td>" . (htmlspecialchars($row["trackDate"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["trackDate"]))) . "</td>";
                        echo "<td>" . $row['tProvince'] . "</td>";
                        echo "<td>" . htmlspecialchars($row["tMunicipality"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["tBarangay"]) . "</td>";
                        echo "<td>" . number_format(htmlspecialchars($row["meb"]), 0, '', ',') . "</td>";
                        echo "<td>" . number_format(htmlspecialchars($row["mer"]), 0, '', ',') . "</td>";
                        echo "<td style='max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis'>" . (htmlspecialchars($row["remarks"]) == "" ? "None" : (htmlspecialchars($row["remarks"]))) . "</td>";
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
                <button class="tblDrpDwn" onclick="exportTable2()">Export Table</button>
            </div>
        </div>
        <center>
            <div id="noResultMessage2" hidden>No result found.</div>
        </center>

	</div>
</body>

    <script>
        document.getElementById("searchInput2").addEventListener("input", function () {
            var filter = this.value.toLowerCase();
            var table = document.getElementById("mySecondTable");
            var tbodyRows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
            var noResultMessage2 = document.getElementById("noResultMessage2");

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
                noResultMessage2.style.display = "block";
            } else {
                noResultMessage2.style.display = "none";
            }
        });

        // JavaScript code for clear button
        function clearSearch2() {
            document.getElementById("searchInput2").value = "";
            // Trigger the input event to clear and reset the search
            var event = new Event('input');
            document.getElementById("searchInput2").dispatchEvent(event);
        }
    </script>

    <script>
        // Export functions
        function exportTable2() {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'data-tracking-meb.php', true);
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

            xhr.send('export2=true');
        }
    </script>

    <script>
        // Event delegation for the second "View Details" button
        document.getElementById("table-body2").addEventListener("click", function (event) {
            if (event.target.classList.contains("view-details-btn2")) {
                var id = event.target.getAttribute("data2-id");
                viewDetails2(id);
            }
        });
    </script>

    <script>
        // Event delegation for the second "Update" button
        document.getElementById("table-body2").addEventListener("click", function (event) {
            if (event.target.classList.contains("update-details-btn2")) {
                var id = event.target.getAttribute("data2-id");
                updateDetails2(id);
            }
        });
    </script>
</html>