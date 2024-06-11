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
        <h3 class="fourthHead">Outgoing Documents</h3>
        <div id="sE4">
        <div class="search">
            &nbsp; <input type="text" id="searchInput4" placeholder="Search..."><i class="clear-button" onclick="clearSearch4()">&times;</i>
            <button class="btn" onclick="showSelectForm()">&nbsp;<span class="fa fa-file-pen"></span>Track Document</button>
        </div>
    </div>
    <div class="table-container" id="data-table4">
        <table class="center" id="myFourthTable">
            <thead style="white-space: nowrap;">
                <tr>
                    <th class='action' style='position:sticky; left: 0; background-color: white !important'>Action</th>
                    <th>Outgoing Date</th>
                    <th>DTN / DRN</th>
                    <th>Description</th>
                    <th>Receiving Office / Personnel</th>
                    <th>Date Received</th>
                    <th>Remarks</th>
                    <th>File</th>
                </tr>
            </thead>
            <tbody id="table-body4" style="white-space: nowrap;">
                <?php
                if ($result4->num_rows === 0) {
                    echo "<tr><td colspan='8' class='shaking-text'>No data available.</td></tr>";
                }
                while ($row = $result4->fetch_assoc()) {
                    echo "<tr class='item4'>";
                    echo "<td class='action' style='position:sticky; left: 0; background-color: white !important'><button class='view-details-btn4' data4-id='" . htmlspecialchars($row['id']) . "'>Details</button></td>";
                    echo "<td>" . (htmlspecialchars($row["outDate"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["outDate"]))) . "</td>";
                    echo "<td>" . $row['tracking_number2'] . "</td>";
                    echo "<td style='max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis'>" . (htmlspecialchars($row["description"]) == "" ? "None" : (htmlspecialchars($row["description"]))) . "</td>";
                    echo "<td>" . (htmlspecialchars($row["personnel"]) == "" ? "<i class='shaking-text'>Can't be blank.</i>" : htmlspecialchars($row["personnel"])) . "</td>";
                    echo "<td>" . (htmlspecialchars($row["dateReceived"]) == "0000-00-00" ? "-" : date('m-d-Y', strtotime($row["dateReceived"]))) . "</td>";
                    echo "<td style='max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis'>" . (htmlspecialchars($row["remarks2"]) == "" ? "None" : (htmlspecialchars($row["remarks2"]))) . "</td>";
                    echo "<td style='max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis'>" . (htmlspecialchars($row["file_name"]) == "" ? "None" : (htmlspecialchars($row["file_name"]))) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
        <div class="tblAct">
            <button class="actBtn">Action <span class="arrowDwn">&#xfe40;</span></button>
            <div class="tblOpt">
                <button class="tblDrpDwn" onclick="exportTable4()">Export Table</button>
            </div>
        </div>
        <center>
            <div id="noResultMessage4" hidden>No result found.</div>
        </center>

	</div>
</body>

    <script>
        document.getElementById("searchInput4").addEventListener("input", function () {
            var filter = this.value.toLowerCase();
            var table = document.getElementById("myFourthTable");
            var tbodyRows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
            var noResultMessage4 = document.getElementById("noResultMessage4");

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
                noResultMessage4.style.display = "block";
            } else {
                noResultMessage4.style.display = "none";
            }
        });

        // JavaScript code for clear button
        function clearSearch4() {
            document.getElementById("searchInput4").value = "";
            // Trigger the input event to clear and reset the search
            var event = new Event('input');
            document.getElementById("searchInput4").dispatchEvent(event);
        }
    </script>

    <script>
        // Export functions
        function exportTable4() {
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

            xhr.send('export4=true');
        }
    </script>

    <script>
        // Event delegation for the second "View Details" button
        document.getElementById("table-body4").addEventListener("click", function (event) {
            if (event.target.classList.contains("view-details-btn4")) {
                var id = event.target.getAttribute("data4-id");
                viewDetails4(id);
            }
        });
    </script>

    <script>
        // Event delegation for the second "Update" button
        document.getElementById("table-body4").addEventListener("click", function (event) {
            if (event.target.classList.contains("update-details-btn4")) {
                var id = event.target.getAttribute("data4-id");
                updateDetails4(id);
            }
        });
    </script>
</html>