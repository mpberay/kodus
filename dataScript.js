$(document).ready(function() {
    var currentTableBody = ''; // Variable to store the current data
    var currentTableBody2 = '';
    var currentTableBody3 = '';
    var currentTableBody4 = '';
    var currentTableBody5 = '';

    function updateTable() {
        $.ajax({
            url: 'http://localhost/kodus/data-tracking.php',
            type: 'GET',
            success: function(data) {
                //console.log(data); // Log the returned data
                // Find the table body within the returned data
                var newTableBody = $(data).find('#table-body').html();

                // Compare current data with new table body
                if (newTableBody !== currentTableBody) {
                    $('#table-body').html(newTableBody);
                    currentTableBody = newTableBody; // Update current table body
                }
            },
            error: function(error) {
                console.log('Error:', error);
            }
        });
    }

    function updateTable2() {
        $.ajax({
            url: 'http://localhost/kodus/data-tracking-meb.php',
            type: 'GET',
            success: function(data) {
                //console.log(data); // Log the returned data
                // Find the table body within the returned data
                var newTableBody = $(data).find('#table-body2').html();

                // Compare current data with new table body
                if (newTableBody !== currentTableBody2) {
                    $('#table-body2').html(newTableBody);
                    currentTableBody2 = newTableBody; // Update current table body
                }
            },
            error: function(error) {
                console.log('Error:', error);
            }
        });
    }

    function updateTable3() {
        $.ajax({
            url: 'http://localhost/kodus/data-tracking-in.php',
            type: 'GET',
            success: function(data) {
                //console.log(data); // Log the returned data
                // Find the table body within the returned data
                var newTableBody = $(data).find('#table-body3').html();

                // Compare current data with new table body
                if (newTableBody !== currentTableBody3) {
                    $('#table-body3').html(newTableBody);
                    currentTableBody3 = newTableBody; // Update current table body
                }
            },
            error: function(error) {
                console.log('Error:', error);
            }
        });
    }

    function updateTable4() {
        $.ajax({
            url: 'http://localhost/kodus/data-tracking-out.php',
            type: 'GET',
            success: function(data) {
                //console.log(data); // Log the returned data
                // Find the table body within the returned data
                var newTableBody = $(data).find('#table-body4').html();

                // Compare current data with new table body
                if (newTableBody !== currentTableBody4) {
                    $('#table-body4').html(newTableBody);
                    currentTableBody4 = newTableBody; // Update current table body
                }
            },
            error: function(error) {
                console.log('Error:', error);
            }
        });
    }

    function updateHomeTable() {
        $.ajax({
            url: 'http://localhost/kodus/home.php',
            type: 'GET',
            success: function(data) {
                //console.log(data); // Log the returned data
                // Find the table body within the returned data
                var newTableBody = $(data).find('#homeTable-body').html();

                // Compare current data with new table body
                if (newTableBody !== currentTableBody5) {
                    $('#homeTable-body').html(newTableBody);
                    currentTableBody5 = newTableBody; // Update current table body
                }
            },
            error: function(error) {
                console.log('Error:', error);
            }
        });
    }

    // Initial data load
    updateTable();
    updateTable2();
    updateTable3();
    updateTable4();
    updateHomeTable();

    // Set interval to update data every 5 seconds (adjust as needed)
    setInterval(function() {
        updateTable();
        updateTable2();
        updateTable3();
        updateTable4();
        updateHomeTable();
    }, 5000);

    // Event listener for the beneficiaries cell
    $(document).on('input', '#beneficiaries[contenteditable=true]', function () {
        var beneficiariesValue = $(this).text();

        // Check if the value is a valid number
        if (!isNaN(beneficiariesValue)) {
            // Multiply the value by 7400 for 'disbursed'
            var fundValue = beneficiariesValue * 7400;

            // Update the content of the 'disbursed' cell
            $('#fund').text(numberWithCommas(fundValue.toFixed(2)));
        } else {
        // Handle invalid input
        alert('Please enter a valid number in the beneficiaries cell')
        }
    });

    // Event listener for 'payout' and 'lastDay' cells
    $(document).on('input', '#payout, #lastDay', function () {
        //console.log('input event triggered');
        var payoutValue = $('#payout').text();
        var lastDayValue = $('#lastDay').text();

        // Create Date objects
        payoutValue = new Date(payoutValue);
        lastDayValue = new Date(lastDayValue);
        //console.log('Payout Date: ', payoutValue);
        //console.log('Last Day: ', lastDayValue);

        // Compute the time difference in milliseconds
        var timeDifference = payoutValue.getTime() - lastDayValue.getTime();
        //console.log('Difference in milliseconds: ', timeDifference);

        // Convert to days
        var differenceValue = timeDifference / (1000 * 60 * 60 *24);
        console.log('Difference in days: ', differenceValue);

        // Update value in the difference cell
        if (isNaN(differenceValue)) {
            $('#difference').text('');
        } else if (differenceValue > 1) {
            $('#difference').text(differenceValue + ' days');
        } else {
            $('#difference').text(differenceValue + ' day');
        }
    });

    // Event listener for the served cell
    $(document).on('input', '#served[contenteditable=true]', function () {
        var servedValue = $(this).text();

        // Check if the value is a valid number
        if (!isNaN(servedValue)) {
            // Multiply the value by 7400 for 'disbursed'
            var disbursedValue = servedValue * 7400;

            // Update the content of the 'disbursed' cell
            $('#disbursed').text(numberWithCommas(disbursedValue.toFixed(2)));

            // Calculate and update the 'percent' cell
            var beneficiaries = parseFloat($('#beneficiaries').text().replace(/,/g, ''));
            var percentValue = (servedValue / beneficiaries) * 100;
            $('#percent').text(percentValue.toFixed(2) + '%');

            // Calculate and update the 'unpaid' cell
            var unpaidValue = beneficiaries - servedValue;
            $('#unpaid').text(unpaidValue.toFixed(0));

            // Calculate and update the 'undisbursed' cell
            var fund = parseFloat($('#fund').text().replace(/,/g, ''));
            var undisbursedValue = fund - disbursedValue;
            $('#undisbursed').text(numberWithCommas(undisbursedValue.toFixed(2)));

            // Calculate and update the 'specialPayout' cell
            var specialPayoutValue = unpaidValue;
            $('#specialPayout').text(specialPayoutValue.toFixed(0));

            // Calculate and update the 'norsa' cell
            var norsaValue = specialPayoutValue;
            $('#norsa').text(norsaValue.toFixed(0));
        } else {
            // Handle invalid input (optional)
            alert('Please enter a valid number in the Number of served cell.');
            // You may also clear the served cell or take other actions based on your requirements
        }
    });

        // Function to add commas for better number formatting
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
});

function viewDetails(id) {
    // Show loading screen before making the AJAX request
    var loadingScreen = Swal.fire({
        icon: 'info',
        title: 'Loading... Please wait!',
        html: 'Have a break. Don\'t take life seriously.<br><i style="text-align: right;">-JM</i>',
        allowOutsideClick: false,
        showConfirmButton: false,
        onBeforeOpen: () => {
            Swal.showLoading();
        }
    });

    // Set a cookie with the current timestamp as 'lastViewedTime'
    document.cookie = 'lastViewedTime=' + new Date().toUTCString() + '; path=/';

    $.ajax({
        url: 'http://localhost/kodus/get_details.php',
        type: 'POST',
        data: { id: id },
        beforeSend: function () {
            // This function is executed before the AJAX request is sent
            loadingScreen;
        },
        complete: function () {
            // This function is executed after the AJAX request is complete (regardless of success or failure)
            loadingScreen.close(); // Close the loading screen
        },
        success: function (data) {
            //console.log('Raw Response:', data);
            Swal.fire({
                title: 'Row Details',
                width: 'auto',
                html: data,
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Save changes',
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    // Get the updated values
                    var batchNumber = $('#batchNumber').text();
                    var municipality = $('#municipality').text();
                    var barangay = $('#barangay').text();
                    var beneficiaries = $('#beneficiaries').text();
                    var fund = $('#fund').text();
                    var served = $('#served').text();
                    var disbursed = $('#disbursed').text();
                    var percent = parseFloat($('#percent').text().replace('%', ''));
                    var unpaid = $('#unpaid').text();
                    var undisbursed = $('#undisbursed').text();
                    var specialPayout = $('#specialPayout').text();
                    var norsa = $('#norsa').text();
                    var payout = $('#payout').text();
                    var paymaster = $('#paymaster').text();
                    var orientation = $('#orientation').text();
                    var speaker = $('#speaker').text();
                    var secondDay = $('#secondDay').text();
                    var monitoring = $('#monitoring').text();
                    var evaluator = $('#evaluator').text();
                    var lastDay = $('#lastDay').text();
                    var difference = $('#difference').text();
                    var project = $('#project').text();
                    var findings = $('#findings').text();
                    var kia = $('#kia').text();
                    var payroll = $('#payroll').text();
                    var tts = $('#tts').text();
                    var war = $('#war').text();
                    var coc = $('#coc').text();
                    var spelling = $('#spelling').text();
                    var geobefore = $('#geobefore').text();
                    var geoduring = $('#geoduring').text();
                    var geoafter = $('#geoafter').text();
                    var replacementsDate = $('#replacementsDate').text();
                    var replacements = $('#replacements').text();
                    var mebRDate = $('#mebRDate').text();
                    var mebR = $('#mebR').text();
                    var brgyReso = $('#brgyReso').text();
                    var moaCert = $('#moaCert').text();
                    var minutes = $('#minutes').text();
                    var endorsement = $('#endorsement').text();

                    // Send the updated values to the server
                    $.ajax({
                        url: 'http://localhost/kodus/get_details.php',
                        type: 'POST',
                        data: { id: id, batchNumber: batchNumber, municipality: municipality, barangay: barangay, beneficiaries: beneficiaries, fund: fund, served: served, disbursed: disbursed, percent: percent, unpaid: unpaid, undisbursed: undisbursed, specialPayout: specialPayout, norsa: norsa, payout: payout, paymaster: paymaster, orientation: orientation, speaker: speaker, secondDay: secondDay, monitoring: monitoring, evaluator: evaluator, lastDay: lastDay, difference: difference, project: project, findings: findings, kia: kia, payroll: payroll, tts: tts, war: war, coc: coc, spelling: spelling, geobefore: geobefore, geoduring: geoduring, geoafter: geoafter, replacementsDate: replacementsDate, replacements: replacements, mebRDate: mebRDate, mebR: mebR, brgyReso: brgyReso, moaCert: moaCert, minutes: minutes, endorsement: endorsement},
                        beforeSend: function () {
                            // Show loading screen before making the update AJAX request
                            Swal.fire({
                                icon: 'info',
                                title: 'Saving changes...<br>Please wait!',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                onBeforeOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function (response) {
                            let result;

                            try {
                                result = JSON.parse(response);

                                if (result.status === 'success') {
                                    Swal.fire('Success', 'Data updated successfully', 'success');
                                } else {
                                    Swal.fire('Error', 'Failed to update data', 'error');
                                }
                            } catch (e) {
                                // If parsing fails, treat it as a success and display the alert
                                //console.log('Error parsing JSON response:', e);
                                Swal.fire({
                                    icon: 'success',
                                    title: "Data updated successfully!",
                                    timer: 2000,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    },
                                    willClose: () => {
                                        // You can add any custom actions here after the alert is closed
                                    }
                                });
                            }
                        },
                        error: function (error) {
                            console.log('Error:', error);
                            Swal.fire('Error', 'Failed to update data', 'error');
                        }
                    });
                }
            });
        },
        error: function (error) {
            console.log('Error:', error);
        }
    });
}

// Function to get the 'lastViewedTime' cookie value
function getLastViewedTime() {
    var name = 'lastViewedTime=';
    var decodedCookie = decodeURIComponent(document.cookie);
    var cookies = decodedCookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        while (cookie.charAt(0) === ' ') {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(name) === 0) {
            return cookie.substring(name.length, cookie.length);
        }
    }
    return null;
}

// Function to delete the 'lastViewedTime' cookie
function deleteLastViewedTime() {
    document.cookie = 'lastViewedTime=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
}

function viewDetails2(id) {
    // Show loading screen before making the AJAX request
    var loadingScreen = Swal.fire({
        icon: 'info',
        title: 'Loading... Please wait!',
        html: 'Have a break. Don\'t take life seriously.<br><i style="text-align: right;">-JM</i>',
        allowOutsideClick: false,
        showConfirmButton: false,
        onBeforeOpen: () => {
            Swal.showLoading();
        }
    });

    // Set a cookie with the current timestamp as 'lastViewedTime'
    document.cookie = 'lastViewedTime=' + new Date().toUTCString() + '; path=/';

    $.ajax({
        url: 'http://localhost/kodus/get_details2.php',
        type: 'POST',
        data: { id: id },
        beforeSend: function () {
            // This function is executed before the AJAX request is sent
            loadingScreen;
        },
        complete: function () {
            // This function is executed after the AJAX request is complete (regardless of success or failure)
            loadingScreen.close(); // Close the loading screen
        },
        success: function (data) {
            //console.log('Raw Response:', data);
            Swal.fire({
                title: 'Row Details',
                width: 'auto',
                html: data,
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Save changes',
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    // Get the updated values
                    var adas = $('#adas').text();
                    var trackDate = $('#trackDate').text();
                    var tProvince = $('#tProvince').text();
                    var tMunicipality = $('#tMunicipality').text();
                    var tBarangay = $('#tBarangay').text();
                    var meb = $('#meb').text();
                    var mer = $('#mer').text();
                    var remarks = $('#remarks').text();

                    // Send the updated values to the server
                    $.ajax({
                        url: 'http://localhost/kodus/get_details2.php',
                        type: 'POST',
                        data: { id: id, adas: adas, trackDate: trackDate, tProvince: tProvince, tMunicipality: tMunicipality, tBarangay: tBarangay, meb: meb, mer: mer, remarks: remarks},
                        beforeSend: function () {
                            // Show loading screen before making the update AJAX request
                            Swal.fire({
                                icon: 'info',
                                title: 'Saving changes...<br>Please wait!',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                onBeforeOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function (response) {
                            let result;

                            try {
                                result = JSON.parse(response);

                                if (result.status === 'success') {
                                    Swal.fire('Success', 'Data updated successfully', 'success');
                                } else {
                                    Swal.fire('Error', 'Failed to update data', 'error');
                                }
                            } catch (e) {
                                // If parsing fails, treat it as a success and display the alert
                                //console.log('Error parsing JSON response:', e);
                                Swal.fire({
                                    icon: 'success',
                                    title: "Data updated successfully!",
                                    timer: 2000,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    },
                                    willClose: () => {
                                        // You can add any custom actions here after the alert is closed
                                    }
                                });
                            }
                        },
                        error: function (error) {
                            console.log('Error:', error);
                            Swal.fire('Error', 'Failed to update data', 'error');
                        }
                    });
                }
            });
        },
        error: function (error) {
            console.log('Error:', error);
        }
    });
}

function viewDetails3(id) {
    // Show loading screen before making the AJAX request
    var loadingScreen = Swal.fire({
        icon: 'info',
        title: 'Loading... Please wait!',
        html: 'Have a break. Don\'t take life seriously.<br><i style="text-align: right;">-JM</i>',
        allowOutsideClick: false,
        showConfirmButton: false,
        onBeforeOpen: () => {
            Swal.showLoading();
        }
    });

    // Set a cookie with the current timestamp as 'lastViewedTime'
    document.cookie = 'lastViewedTime=' + new Date().toUTCString() + '; path=/';

    $.ajax({
        url: 'http://localhost/kodus/get_details3.php',
        type: 'POST',
        data: { id: id },
        beforeSend: function () {
            // This function is executed before the AJAX request is sent
            loadingScreen;
        },
        complete: function () {
            // This function is executed after the AJAX request is complete (regardless of success or failure)
            loadingScreen.close(); // Close the loading screen
        },
        success: function (data) {
            //console.log('Raw Response:', data);
            Swal.fire({
                title: 'Row Details',
                width: 'auto',
                html: data,
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Save changes',
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    // Get the updated values
                    var aaDate = $('#aaDate').text();
                    var description = $('#description').text();
                    var jmGwapo = $('#jmGwapo').text();
                    var focal = $('#focal').text();
                    var remarks2 = $('#remarks2').text();
                    var sHead = $('#sHead').text();

    // Add this code to handle file upload
    var formData = new FormData();
    formData.append('id', id);
    formData.append('aaDate', aaDate);
    formData.append('description', description);
    formData.append('jmGwapo', jmGwapo);
    formData.append('focal', focal);
    formData.append('remarks2', remarks2);
    formData.append('sHead', sHead);
    formData.append('file_name', $('#file_name')[0].files[0]);

                    // Send the updated values to the server
                    $.ajax({
                        url: 'http://localhost/kodus/get_details3.php',
                        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
                            // Show loading screen before making the update AJAX request
                            Swal.fire({
                                icon: 'info',
                                title: 'Saving changes...<br>Please wait!',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                onBeforeOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function (response) {
                            let result;

                            try {
                                result = JSON.parse(response);

                                if (result.status === 'success') {
                                    Swal.fire('Success', 'Data updated successfully', 'success');
                                } else {
                                    Swal.fire('Error', 'Failed to update data', 'error');
                                }
                            } catch (e) {
                                // If parsing fails, treat it as a success and display the alert
                                //console.log('Error parsing JSON response:', e);
                                Swal.fire({
                                    icon: 'success',
                                    title: "Data updated successfully!",
                                    timer: 2000,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    },
                                    willClose: () => {
                                        // You can add any custom actions here after the alert is closed
                                    }
                                });
                            }
                        },
                        error: function (error) {
                            console.log('Error:', error);
                            Swal.fire('Error', 'Failed to update data', 'error');
                        }
                    });
                }
            });
        },
        error: function (error) {
            console.log('Error:', error);
        }
    });
}

function viewDetails4(id) {
    // Show loading screen before making the AJAX request
    var loadingScreen = Swal.fire({
        icon: 'info',
        title: 'Loading... Please wait!',
        html: 'Have a break. Don\'t take life seriously.<br><i style="text-align: right;">-JM</i>',
        allowOutsideClick: false,
        showConfirmButton: false,
        onBeforeOpen: () => {
            Swal.showLoading();
        }
    });

    // Set a cookie with the current timestamp as 'lastViewedTime'
    document.cookie = 'lastViewedTime=' + new Date().toUTCString() + '; path=/';

    $.ajax({
        url: 'http://localhost/kodus/get_details4.php',
        type: 'POST',
        data: { id: id },
        beforeSend: function () {
            // This function is executed before the AJAX request is sent
            loadingScreen;
        },
        complete: function () {
            // This function is executed after the AJAX request is complete (regardless of success or failure)
            loadingScreen.close(); // Close the loading screen
        },
        success: function (data) {
            //console.log('Raw Response:', data);
            Swal.fire({
                title: 'Row Details',
                width: 'auto',
                html: data,
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Save changes',
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    // Get the updated values
                    var outDate = $('#outDate').text();
                    var description = $('#description').text();
                    var personnel = $('#personnel').text();
                    var dateReceived = $('#dateReceived').text();
                    var remarks2 = $('#remarks2').text();

    // Add this code to handle file upload
    var formData = new FormData();
    formData.append('id', id);
    formData.append('outDate', outDate);
    formData.append('description', description);
    formData.append('personnel', personnel);
    formData.append('dateReceived', dateReceived);
    formData.append('remarks2', remarks2);
    formData.append('file_name', $('#file_name')[0].files[0]);

                    // Send the updated values to the server
                    $.ajax({
                        url: 'http://localhost/kodus/get_details4.php',
                        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
                            // Show loading screen before making the update AJAX request
                            Swal.fire({
                                icon: 'info',
                                title: 'Saving changes...<br>Please wait!',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                onBeforeOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function (response) {
                            let result;

                            try {
                                result = JSON.parse(response);

                                if (result.status === 'success') {
                                    Swal.fire('Success', 'Data updated successfully', 'success');
                                } else {
                                    Swal.fire('Error', 'Failed to update data', 'error');
                                }
                            } catch (e) {
                                // If parsing fails, treat it as a success and display the alert
                                //console.log('Error parsing JSON response:', e);
                                Swal.fire({
                                    icon: 'success',
                                    title: "Data updated successfully!",
                                    timer: 2000,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    },
                                    willClose: () => {
                                        // You can add any custom actions here after the alert is closed
                                    }
                                });
                            }
                        },
                        error: function (error) {
                            console.log('Error:', error);
                            Swal.fire('Error', 'Failed to update data', 'error');
                        }
                    });
                }
            });
        },
        error: function (error) {
            console.log('Error:', error);
        }
    });
}