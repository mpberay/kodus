// Wait for the DOM to fully load
document.addEventListener('DOMContentLoaded', function () {
    // Delay the execution of showLoginForm by 3 seconds
    setTimeout(function() {
        // Trigger the login form when the page is loaded
        if (window.location.pathname === '/kodus/') {
            showLoginForm();
        }
    }, 1000); // 1 second in milliseconds

    // Set a timer to check for session expiration every minute (adjust as needed)
    //setInterval(checkSessionExpiration, 1 * 60 * 1000); // 1 minute in milliseconds

    // Trigger the registration form when a button is clicked
    document.getElementById('showRegisterButton').addEventListener('click', showRegistrationForm);

    // Trigger the login form when a button is clicked
    document.getElementById('showLoginButton').addEventListener('click', showLoginForm);
});

function showSelectForm() {
    console.log('showSelectForm called');
    Swal.fire({
        title: 'Please select document to track<hr>',
        html: `
            <form id="swal-form">
                <select class="swal2-select" id="documentType">
                    <option value="3">Incoming / Outgoing Documents</option>
                    <option value="2">MEB</option>
                    <option value="1">Post-implementation Documents</option>
                </select>
            </form>`,
        width: 'auto',
        focusConfirm: true,
        showCancelButton: true,
        showCloseButton: true,
        showConfirmButton: true,
        showLoaderOnConfirm: true,
        buttonsStyling: false, // Disable the default button styling
        customClass: {
            closeButton: 'custom-close-button',
        },
        preConfirm: () => {
            const selectedDocumentType = Swal.getPopup().querySelector('#documentType').value;

            // Check the selected document type and call the appropriate function
            if (selectedDocumentType === '1') {
                showTrackForm();
            } else if (selectedDocumentType === '2') {
                showTrackForm2();
            } else if (selectedDocumentType === '3') {
                showTrackForm3();
            }

            // Get today's date in the format YYYY-MM-DD
            function getTodayDate() {
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Set the default value for date input fields, excluding those with the "exempted" class
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                if (!input.classList.contains('exempted')) {
                    input.value = getTodayDate();
                }
            });
        },
    });
}

function showLoginForm() {
    console.log('showLoginForm called');
    Swal.fire({
        title: 'Login',
        html: `
            <form id="swal-form">
                <input type="text" id="username" name="username" class="swal2-input" placeholder="Username" autocomplete="username">
                <div class="password-container">
                    <input type="password" id="password" name="password" class="swal2-input" placeholder="Password" autocomplete="current-password">
                    <span id="showPassword" class="show-password" onclick="togglePasswordVisibility()">
                        <i class="fa fa-eye-slash eye1"></i>
                    </span>
                </div>
            </form>`,
        focusConfirm: true,
        showCancelButton: true,
        showCloseButton: true,
        showConfirmButton: true,
        showLoaderOnConfirm: true,
        buttonsStyling: false,
        customClass: {
            closeButton: 'custom-close-button',
        },
        footer: '<button id="switchToRegistration" class="button button1">Not yet registered?</button>',
        didOpen: () => {
            document.getElementById('switchToRegistration').addEventListener('click', showRegistrationForm);
            const input1 = Swal.getPopup().querySelector('#username');
            const input2 = Swal.getPopup().querySelector('#password');

            input1.addEventListener('keydown', handleEnterKey);
            input2.addEventListener('keydown', handleEnterKey);
        },
        willClose: () => {
            document.getElementById('switchToRegistration').removeEventListener('click', showRegistrationForm);
            const input1 = Swal.getPopup().querySelector('#username');
            const input2 = Swal.getPopup().querySelector('#password');
            input1.removeEventListener('keydown', handleEnterKey);
            input2.removeEventListener('keydown', handleEnterKey);
        },
        preConfirm: () => {
            const username = Swal.getPopup().querySelector('#username').value;
            const password = Swal.getPopup().querySelector('#password').value;

            return performAuthentication('login.php', username, password);
        },
    });

    const passwordInput = document.getElementById('password');
    const showPasswordButton = document.getElementById('showPassword');

    passwordInput.addEventListener('input', () => {
        showPasswordButton.style.display = 'block';
    });
}

function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const showPasswordButton = document.getElementById('showPassword');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        showPasswordButton.innerHTML = '<i class="fa fa-eye eye1"></i>';
    } else {
        passwordInput.type = 'password';
        showPasswordButton.innerHTML = '<i class="fa fa-eye-slash eye1"></i>';
    }
}

function performAuthentication(url, username, password) {
    console.log('performAuthentication called');
    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            username: username,
            password: password,
        }),
    })
    .then(response => response.json())
    .then(data => {
        console.log('Authentication response:', data); // Add this line for logging

        return data; // Return the data to the next `then` block
    })
    .catch(error => {
        console.error('Error:', error);
        // Add an alert for network errors
        Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'An error occurred during authentication. Please check your network connection and try again.',
        });
    })
    .then(data => {
        // Check if the authentication was successful before redirecting
        if (data && data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: data.message,
                showConfirmButton: false,
                timer: 1500 // 1.5 seconds
            }).then(() => {
                // Redirect after successful login
                window.location.href = 'home';
            });
        } else {
            // Add an alert for other cases (e.g., authentication failure)
            Swal.fire({
                icon: 'error',
                title: 'Authentication Failed',
                text: 'Invalid credentials. Please try again.',
            }).then(() => {
                // Show the login form again
                showLoginForm();
            });
        }
    });
}

function showRegistrationForm() {
    console.log('showRegistrationForm called');
    Swal.fire({
        title: 'Register',
        html: `
            <form id="swal-form">
                <input type="text" id="username1" name="username" class="swal2-input" placeholder="Username" autocomplete="username">
                <div>
                    <input type="password" id="password" name="current-password" class="swal2-input" placeholder="Password" autocomplete="new-password">
                    <span id="showPassword1" class="show-password" onclick="showPassword1()">
                        <i class="fa fa-eye-slash eye1"></i>
                    </span>
                </div>
                <div>
                    <input type="password" id="password2" name="repeat-password" class="swal2-input" placeholder="Repeat Password" autocomplete="new-password">
                    <span id="showPassword2" class="show-password" onclick="showPassword2()">
                        <i class="fa fa-eye-slash eye2"></i>
                    </span>
                </div>
            </form>`,
        focusConfirm: true,
        showCancelButton: true,
        showCloseButton: true,
        showConfirmButton: true,
        buttonsStyling: false,
        customClass: {
            closeButton: 'custom-close-button',
        },
        footer: '<button id="switchToLogin" class="button button1">Already have an account?</button>',
        didOpen: () => {
            document.getElementById('switchToLogin').addEventListener('click', showLoginForm);
            const input1 = Swal.getPopup().querySelector('#username1');
            const input2 = Swal.getPopup().querySelector('#password');
            const input3 = Swal.getPopup().querySelector('#password2');

            input1.addEventListener('keydown', handleEnterKey);
            input2.addEventListener('keydown', handleEnterKey);
            input3.addEventListener('keydown', handleEnterKey);
        },
        willClose: () => {
            document.getElementById('switchToLogin').removeEventListener('click', showLoginForm);
            const input1 = Swal.getPopup().querySelector('#username1');
            const input2 = Swal.getPopup().querySelector('#password');
            const input3 = Swal.getPopup().querySelector('#password2');

            input1.removeEventListener('keydown', handleEnterKey);
            input2.removeEventListener('keydown', handleEnterKey);
            input3.removeEventListener('keydown', handleEnterKey);
        },
        preConfirm: async () => {
            const username = Swal.getPopup().querySelector('#username1').value;
            const password = Swal.getPopup().querySelector('#password').value;
            const password2 = Swal.getPopup().querySelector('#password2').value;

            if (password !== password2) {
                Swal.fire({
                    icon: 'error',
                    title: 'Passwords Do Not Match',
                    text: 'Please make sure the passwords match.',
                    allowOutsideClick: false,
                }).then(() => {
                    showRegistrationForm();
                });
                return false;
            }

            const isUsernameUnique = await checkUsernameAvailability(username);

            if (!isUsernameUnique) {
                Swal.fire({
                    icon: 'error',
                    title: 'Username Not Available',
                    text: 'The chosen username is already taken. Please choose another one.',
                    allowOutsideClick: false,
                }).then(() => {
                    showRegistrationForm();
                });
                return false;
            }

            try {
                const response = await fetch('register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        username: username,
                        password: password,
                    }),
                });

                const data = await response.json();

                console.log('Registration response:', data);

                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful',
                        showConfirmButton: false,
                        timer: 1500,
                    }).then(() => {
                        Swal.fire({
                            title: 'Logging In',
                            html: 'Please wait...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            timer: 1500,
                            didOpen: () => {
                                performAuthentication('login.php', username, password)
                                    .then(() => {
                                        setTimeout(() => {
                                            window.location.href = 'home';
                                        }, 1500);
                                    })
                                    .catch(() => {
                                        showLoginForm();
                                    });
                            },
                        });
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: data.message,
                    });
                }
            } catch (error) {
                console.error('Error:', error);
            }
        },
    });

    const password = document.getElementById('password');
    const password2 = document.getElementById('password2');
    const showPassword1 = document.getElementById('showPassword1');
    const showPassword2 = document.getElementById('showPassword2');

    password.addEventListener('input', () => {
        showPassword1.style.display = 'block';
    });

    password2.addEventListener('input', () => {
        showPassword2.style.display = 'block';
    });
}

function showPassword1() {
    const password = document.getElementById('password');
    const showPassword1 = document.getElementById('showPassword1');

    if (password.type === 'password') {
        password.type = 'text';
        showPassword1.innerHTML = '<i class="fa fa-eye eye1"></i>';
    } else {
        password.type = 'password';
        showPassword1.innerHTML = '<i class="fa fa-eye-slash eye1"></i>';
    }
}

function showPassword2() {
    const password2 = document.getElementById('password2');
    const showPassword2 = document.getElementById('showPassword2');

    if (password2.type === 'password') {
        password2.type = 'text';
        showPassword2.innerHTML = '<i class="fa fa-eye eye2"></i>';
    } else {
        password2.type = 'password';
        showPassword2.innerHTML = '<i class="fa fa-eye-slash eye2"></i>';
    }
}

// Function to check if the username is available
async function checkUsernameAvailability(username) {
    try {
            console.log('check_username.php is called');
        const response = await fetch('check_username.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                username: username,
            }),
        });
        const data = await response.json();
        return data.isAvailable; // Assuming the server responds with an object containing a boolean property 'available'
    } catch (error) {
        console.error('Error checking username availability:', error);
        return false; // Assume username is not available in case of an error
    }
}

function handleEnterKey(event) {
    if (event.key === 'Enter') {
        Swal.clickConfirm();
    }
}

//function checkSessionExpiration() {
    // Check if the user is logged in
    //if (isLoggedIn()) {
        // Check if the current page is not index.php
        //if (window.location.pathname !== '/kodus/') {
            // Adjust the timeout duration based on your session expiration time
            //const sessionTimeoutDuration = 15 * 60 * 1000; // 15 minutes in milliseconds

            // Get the last activity time from the session
            //const lastActivityTime = new Date(sessionStorage.getItem('last_activity') || 0).getTime();

            // Calculate the time elapsed since the last activity
            //const elapsedTime = Date.now() - lastActivityTime;

            // Check if the session has expired
            //if (elapsedTime >= sessionTimeoutDuration) {
                // Session expired, show the alert
                //Swal.fire({
                    //icon: 'warning',
                    //title: 'Session Expired',
                    //text: 'Your session has expired. Please log in again.',
                    //willClose: () => {
                        // Make an AJAX request to the server to perform logout
                        //fetch('logout.php', {
                            //method: 'GET',
                        //})
                        //.then(response => response.json())
                        //.then(data => {
                            //if (data.status === 'success') {
                                // Redirect to the login page or perform any other necessary actions
                                //window.location.href = '.';
                            //} else {
                                // Handle error if needed
                                //console.error('Logout failed:', data.message);
                            //}
                        //})
                        //.catch(error => {
                            //console.error('Error:', error);
                        //});
                    //}
                //});
            //}
        //}
    //}
//}

// Function to check if the user is logged in
//async function isLoggedIn() {
//    try {
//        const response = await fetch('check_login_status.php');
//        const data = await response.json();
//        return data.loggedIn;
//    } catch (error) {
//        console.error('Error checking login status:', error);
//        return false;
//    }
//}

// Function to check if the user is logged in
async function isLoggedIn() {
    try {
        // Get the current URL and determine the relative path
        const currentURL = new URL(window.location.href);
        const relativePath = currentURL.pathname.includes('/kodus/provinces/')
            ? '../check_login_status.php'
            : 'check_login_status.php';
        
        // Send AJAX request to check login status on the server
        const response = await fetch(relativePath);
        
        // Parse JSON response
        const data = await response.json();
        
        // Return the loggedIn property
        return data.loggedIn;
    } catch (error) {
        console.error('Error checking login status:', error);
        return false;
    }
}

// Function to handle generic errors
function handleErrors(error) {
    console.error('Error:', error);
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'An unexpected error occurred. Please try again.',
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // Trigger the logout function when the logout button is clicked
    document.getElementById('logoutButton').addEventListener('click', performLogout);
});

function performLogout() {
    // Show loading screen
    Swal.fire({
        icon: 'info',
        title: 'Logging out...',
        showConfirmButton: false,
        allowOutsideClick: false, // Prevent user from clicking outside the modal
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Get the current URL and determine the relative path
    const currentURL = new URL(window.location.href);
    const relativePath = currentURL.pathname.includes('kodus/provinces/')
        ? '../logout.php'
        : 'logout.php';

    // Send AJAX request to update last activity on the server
    fetch(relativePath, {
        method: 'GET',
    })
        .then(response => response.json())
        .then(data => {
            // Close loading screen
            Swal.close();

            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1500, // 1.5 seconds
                    willClose: () => {
                        window.location.href = '/kodus/'; // Redirect to the desired page after logout
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Logout Failed',
                    text: data.message,
                });
            }
        })
        .catch(error => {
            // Close loading screen
            Swal.close();

            console.error('Error:', error);
            // Add an alert for network errors
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'An error occurred during logout. Please check your network connection and try again.',
            });
        });
}

function toggleMode() {
    const body = document.body;

    // Toggle the 'dark-mode' class on the body
    body.classList.toggle('dark-mode');

    // Save the user's preference to localStorage
    const isDarkMode = body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDarkMode);
}

// Check for user's preference in localStorage on page load
document.addEventListener('DOMContentLoaded', () => {
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    if (isDarkMode) {
        document.body.classList.add('dark-mode');
    }
});

document.addEventListener('DOMContentLoaded', function () {
  var toggleIcon = document.getElementById('dayNightToggle');

  toggleIcon.addEventListener('click', function () {
    toggleDayNight();
  });

  function toggleDayNight() {
    var currentClass = toggleIcon.classList.contains('fa-moon-o') ? 'fa-moon-o' : 'fa-sun-o';
    var newClass = currentClass === 'fa-moon-o' ? 'fa-sun-o' : 'fa-moon-o';

    toggleIcon.classList.remove(currentClass);
    toggleIcon.classList.add(newClass);
  }
});

//document.addEventListener('DOMContentLoaded', function () {
    // Add event listeners for user interactions
    //document.addEventListener('mousemove', updateLastActivity);
    //document.addEventListener('keypress', updateLastActivity);
//});

//function updateLastActivity() {
    // Check if the current page is not index.php
    //if (window.location.pathname !== '/kodus/') {
        //console.log("updateLastActivity() is called");
        // Update the last activity time in session storage
        //sessionStorage.setItem('last_activity', new Date().toISOString());

        // Get the current URL and determine the relative path
        //const currentURL = new URL(window.location.href);
        //const relativePath = currentURL.pathname.includes('/kodus/provinces/')
            //? '../update_last_activity.php'
            //: 'update_last_activity.php';

        // Send AJAX request to update last activity on the server
        //fetch(relativePath, {
            //method: 'POST',
        //})
        //.then(response => response.json())
        //.then(data => {
            //console.log('Server response:', data);
        //)
        //.catch(error => {
            //console.error('Error updating last activity:', error);
        //});
    //}
//}

// Function to format number as "1,000,000.00"
function numberFormat(value) {
   return value.toLocaleString('en-US', {
     minimumFractionDigits: 2,
     maximumFractionDigits: 2,
   });
}

// Function to format number as "1000000"
// function numberFormat(value) {
//  return value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, '');
//}

// Function to format number as "100.00%"
function percentFormat(value) {
  return value.toLocaleString('en-US', {
    style: 'percent',
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  });
}

function generateTrackingNumber() {
    // You can implement your own logic to generate a tracking number,
    // for example, a combination of date, province code, and a random number.
    const date = new Date();
    const formattedDate = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
    const randomNumber = Math.floor(Math.random() * (999 - 100 + 1)) + 100;
    return formattedDate + '-' + randomNumber;
}

function generateTrackingNumber2() {
    // You can implement your own logic to generate a tracking number,
    // for example, a combination of date, province code, and a random number.
    const date = new Date();
    const formattedDate = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
    const randomNumber = Math.floor(Math.random() * (999 - 100 + 1)) + 100;
    return formattedDate + '-' + randomNumber;
}

// Function to show the tracking form
window.showTrackForm = function () {
  Swal.fire({
    position: 'top',
    width: 'auto',
    title: 'Track Documents<hr>',
    showCloseButton: true,
    allowOutsideClick: false,
    html: `
    <div style="max-height: 78vh; overflow-y: auto;">
      <form class="input-container" id="swal-form">
        <input type="text" name="tracking_number" id="tracking_number" hidden>
        <section class="section">
            <div>
                <label for="province">Province*</label><br>
                <select class="swal2-select province" id="province" name="province">
                  <option value="" selected disabled>Province</option>
                </select>
            </div>
            <div>
                <label for="pdo">PDO*</label><br>
                <select class="swal2-select pdo" id="pdo" name="pdo">
                  <option value="" selected disabled>PDO</option>
                </select>
            </div>
            <div>
                <label for="batchNumber">Batch*</label><br>
                <input class="swal2-input custom-input batchNumber" type="number" id="batchNumber" name="batchNumber" placeholder="Batch">
            </div>
            <div>
                <label for="municipality">Municipality*</label><br>
                <select class="swal2-select municipality" id="municipality" name="municipality">
                  <option value="" selected disabled>Municipality</option>
                </select>
            </div>
        </section>
        <hr>
        <section class="section">
            <div>
                <label for="barangay">Barangay*</label><br>
                <select class="swal2-select barangay" id="barangay" name="barangay">
                  <option value="" selected disabled>Barangay</option>
                </select>
            </div>
            <div>
                <label for="beneficiaries">Target No. of Beneficiaires*</label><br>
                <input class="swal2-input custom-input beneficiaries" type="number" id="beneficiaries" name="beneficiaries" placeholder="Target # of Beneficiaires">
            </div>
            <div>
                <label for="fund">Fund Allocated*</label><br>
                <input class="swal2-input custom-input fund" type="text" id="fund" name="fund" placeholder="Funds Allocated" tabindex="-1"  readonly>
            </div>
        </section>
        <hr>
        <div hidden>
            <section class="section">
                <div>
                    <label for="served">No. of Served Beneficiaires</label><br>
                    <input class="swal2-input custom-input served" type="number" id="served" name="served" placeholder="# of Served Beneficiaires"> 
                </div>
                <div>
                    <label for="disbursed">Amount Disbursed</label><br>
                    <input class="swal2-input custom-input disbursed" type="text" id="disbursed" name="disbursed" placeholder="Amount Disbursed" tabindex="-1"  readonly>
                </div>
                <div>
                    <label for="percent">Percentage</label><br>
                    <input class="swal2-input custom-input percent" type="text" id="percent" name="percent" placeholder="Percentage" tabindex="-1"  readonly>
                </div>
            </section>
            <hr>
            <section class="section">
                <div>
                    <label for="unpaid">No. of Unpaid Beneficiaires</label><br>
                    <input class="swal2-input custom-input unpaid" type="text" id="unpaid" name="unpaid" placeholder="# of Unpaid Beneficiaires" tabindex="-1"  readonly>
                </div>
                <div>
                    <label for="undisbursed">Undisbursed Amount</label><br>
                    <input class="swal2-input custom-input undisbursed" type="text" id="undisbursed" name="undisbursed" placeholder="Undisbursed Amount" tabindex="-1"  readonly>
                </div>
                <div>
                    <label for="specialPayout">For Special Payout</label><br>
                    <input class="swal2-input custom-input specialPayout" type="text" id="specialPayout" name="specialPayout" placeholder="For Special Payout" tabindex="-1"  readonly>
                </div>
                <div>
                    <label for="norsa">For NORSA</label><br>
                    <input class="swal2-input custom-input norsa" type="text" id="norsa" name="norsa" placeholder="For NORSA" tabindex="-1"  readonly>
                </div>
            </section>
            <hr>
        </div>
        <section class="section">
            <div>
                <label for="payout">Payout Date*</label><br>
                <input class="swal2-input custom-input payout exempted" type="date" id="payout" name="payout" placeholder="Payout Date">
            </div>
            <div>
                <label for="paymaster">Paymasters</label><br>
                <textarea class="swal2-input custom-input paymaster" type="text" id="paymaster" name="paymaster" placeholder="Paymasters"></textarea>
            </div>
        </section>
        <hr>
        <section class="section">
            <div>
                <label for="orientation">BLGU Orientation Date*</label><br>
                <input class="swal2-input custom-input orientation exempted" type="date" id="orientation" name="orientation" placeholder="BLGU Orientation Date">
            </div>
            <div>
                <label for="speaker">Resource Speaker*</label><br>
                <textarea class="swal2-input custom-input speaker" type="text" id="speaker" name="speaker" placeholder="Resource Speaker"></textarea>
            </div>
        </section>
        <hr>
        <section class="section">
            <div>
                <label for="secondDay">2nd Day (1st Training Day)</label><br>
                <input class="swal2-input custom-input secondDay exempted" type="date" id="secondDay" name="secondDay" placeholder="1st Training Day">
            </div>
            <div>
                <label for="monitoring">Project Monitoring by RRP-CFW/T Team</label><br>
                <textarea class="swal2-input custom-input monitoring" type="text" id="monitoring" name="monitoring" placeholder="Monitoring"></textarea>
            </div>
            <div>
                <label for="evaluator">Evaluator</label><br>
                <textarea class="swal2-input custom-input evaluator" type="text" id="evaluator" name="evaluator" placeholder="Evaluator"></textarea>
            </div>
            <div>
                <label for="lastDay">Last Day*</label><br>
                <input class="swal2-input custom-input lastDay exempted" type="date" id="lastDay" name="lastDay" placeholder="Last Day">
            </div>
            <div>
                <label for="difference">Difference*</label><br>
                <input class="swal2-input custom-input difference" type="text" id="difference" name="difference" placeholder="Difference" tabindex="-1" readonly>
            </div>
        </section>
        <hr>
        <section class="section">
            <div>
                <label for="project">Project*</label><br>
                <textarea class="swal2-input custom-input project" type="text" id="project" name="project" placeholder="Project"></textarea>
            </div>
            <div>
                <label for="findings">Findings*</label><br>
                <textarea class="swal2-input custom-input findings" id="findings" name="findings" placeholder="Findings"></textarea>
            </div>
            <div>
                <label for="kia">Key Investment Area*</label><br>
                <textarea class="swal2-input custom-input kia" id="kia" name="kia" placeholder="Key Investment Area"></textarea>
            </div>
        </section>
        <hr>
        <section class="section">
            <div>
                <label for="payroll">Payroll</label><br>
                <input class="swal2-input custom-input payroll exempted" type="date" id="payroll" name="payroll" placeholder="Payroll">
            </div>
            <div>
                <label for="tts">Time Tally Sheet</label><br>
                <input class="swal2-input custom-input tts" type="date" id="tts" name="tts" placeholder="Time Tally Sheet">
            </div>
            <div>
                <label for="war">Work Accomplishment Report</label><br>
                <input class="swal2-input custom-input war" type="date" id="war" name="war" placeholder="Work Accomplishment Report">
            </div>
            <div>
                <label for="coc">Certificate of Completion</label><br>
                <input class="swal2-input custom-input coc" type="date" id="coc" name="coc" placeholder="Certificate of Completion">
            </div>
        </section>
        <hr>
        <section class="section">
            <div>
                <label for="geobefore">Before Pics</label><br>
                <input class="swal2-input custom-input geobefore" type="date" id="geobefore" name="geobefore" placeholder="Before Pics">
            </div>
            <div>
                <label for="geoduring">During Pics</label><br>
                <input class="swal2-input custom-input geoduring" type="date" id="geoduring" name="geoduring" placeholder="During Pics">
            </div>
            <div>
                <label for="geoafter">After Pics</label><br>
                <input class="swal2-input custom-input geoafter" type="date" id="geoafter" name="geoafter" placeholder="After Pics">
            </div>
        </section>
        <hr>
        <section class="section">
            <div>
                <label for="spelling">Certificate of Correct Spelling</label><br>
                <input class="swal2-input custom-input spelling" type="date" id="spelling" name="spelling" placeholder="Certificate of Correct Spelling">
            </div>
            <div>
                <label for="replacementsDate">Summary of Replacements</label><br>
                <input class="swal2-input custom-input replacementsDate" type="date" id="replacementsDate" name="replacementsDate" placeholder="Summary of Replacements (Date)">
            </div>
            <div>
                <label for="replacements">Number of Replacements</label><br>
                <input class="swal2-input custom-input replacements" type="number" id="replacements" name="replacements" placeholder="Summary of Replacements (#)">
            </div>
            <div>
                <label for="mebRDate">MEB for Replacements</label><br>
                <input class="swal2-input custom-input mebRDate" type="date" id="mebRDate" name="mebRDate" placeholder="MEB for Replacements (Date)">
            </div>
            <div>
                <label for="mebR">Number of Replacements</label><br>
                <input class="swal2-input custom-input mebR" type="number" id="mebR" name="mebR" placeholder="MEB for Replacements (#)">
            </div>
        </section>
        <hr>
        <section class="section">
            <div>
                <label for="brgyReso">Brgy. Reso on Lot Utilization</label><br>
                <input class="swal2-input custom-input brgyReso" type="date" id="brgyReso" name="brgyReso" placeholder="Brgy. Reso on Lot Utilization">
            </div>
            <div>
                <label for="moaCert">MOA/Cert. on Lot Utilization</label><br>
                <input class="swal2-input custom-input moaCert exempted" type="date" id="moaCert" name="moaCert" placeholder="MOA/Cert. on Lot Utilization">
            </div>
            <div>
                <label for="minutes">BLGU Minutes w/ Photo Docs</label><br>
                <input class="swal2-input custom-input minutes" type="date" id="minutes" name="minutes" placeholder="BLGU Minutes w/ Photo Docs">
            </div>
        </section>
        <hr>
        <section class="section">
            <div>
                <label for="endorsement">PLGU Endorsement</label><br>
                <input class="swal2-input custom-input endorsement exempted" type="date" id="endorsement" name="endorsement" placeholder="PLGU Endorsement">
            </div>
            <div hidden>
                <label for="track_type">Track Type</label><br>
                <input class="swal2-input custom-input track_type" type="number" id="track_type" name="track_type" value="1" placeholder="Track Type">
            </div>
        </section>
        <hr>
      </form>
    </div>
    `,
    focusConfirm: true,
    showCancelButton: true,
    confirmButtonText: 'Track',
    didOpen: function () {
      const provinceSelect = Swal.getPopup().querySelector('#province');
      const pdoSelect = Swal.getPopup().querySelector('#pdo');
      const municipalitySelect = Swal.getPopup().querySelector('#municipality');
      const barangaySelect = Swal.getPopup().querySelector('#barangay');

      // Fetch provinces from the database
fetch('fetch_data.php?table=provinces')
  .then(response => response.json())
  .then(provinceOptions => {
    provinceOptions.forEach(option => {
      provinceSelect.innerHTML += `<option value="${option.province_name}">${option.province_name}</option>`;
    });

    provinceSelect.addEventListener('change', function () {
        console.log("Selected Province: ", provinceSelect.value);
      const selectedProvinceName = provinceSelect.value;

      // Fetch PDOs based on the selected province from the database
      fetch(`fetch_data.php?table=pdos&province_id=${encodeURIComponent(selectedProvinceName)}`)
        .then(response => response.json())
        .then(pdoOptions => {
          pdoSelect.innerHTML = '';

          pdoOptions.forEach(pdoOption => {
            pdoSelect.innerHTML += `<option value="${pdoOption.pdo}">${pdoOption.pdo}</option>`;
          });

          // Automatically set the first PDO option as the default
          pdoSelect.selectedIndex = 0;
        });

      // Fetch municipalities based on the selected province from the database
      fetch(`fetch_data.php?table=municipality&province_id=${encodeURIComponent(selectedProvinceName)}`)
        .then(response => response.json())
        .then(municipalityOptions => {
          municipalitySelect.innerHTML = '<option value="" selected disabled>Municipality</option>';

          municipalityOptions.forEach(municipalityOption => {
            municipalitySelect.innerHTML += `<option value="${municipalityOption.municipality_name}">${municipalityOption.municipality_name}</option>`;
            
            // Log each municipality
            console.log("Municipality: ", municipalityOption.municipality_name);
          });

          // Trigger the change event for the municipality select
          municipalitySelect.dispatchEvent(new Event('change'));
        });
    });
  });

// Municipality selection logic
municipalitySelect.addEventListener('change', function () {
        console.log("Selected Municipality: ", municipalitySelect.value);
  const selectedMunicipalityName = municipalitySelect.value;

  // Fetch barangays based on the selected municipality from the database
  fetch(`fetch_data.php?table=barangay&municipality_id=${encodeURIComponent(selectedMunicipalityName)}`)
    .then(response => response.json())
    .then(barangayOptions => {
      barangaySelect.innerHTML = '<option value="" selected disabled>Barangay</option>';

      barangayOptions.forEach(barangayOption => {
        barangaySelect.innerHTML += `<option value="${barangayOption.brgy_name}">${barangayOption.brgy_name}</option>`;
            
            // Log each barangay
            console.log("Barangay: ", barangayOption.brgy_name);
      });
    });
});

// Trigger the change event for the initial setup
provinceSelect.dispatchEvent(new Event('change'));

        const beneficiariesInput = Swal.getPopup().querySelector('#beneficiaries');
        const fundInput = Swal.getPopup().querySelector('#fund');
        const servedInput = Swal.getPopup().querySelector('#served');
        const disbursedInput = Swal.getPopup().querySelector('#disbursed');
        const percentInput = Swal.getPopup().querySelector('#percent');
        const unpaidInput = Swal.getPopup().querySelector('#unpaid');
        const specialPayoutInput = Swal.getPopup().querySelector('#specialPayout');
        const norsaInput = Swal.getPopup().querySelector('#norsa');
        const undisbursedInput = Swal.getPopup().querySelector('#undisbursed');
        const payoutInput = Swal.getPopup().querySelector('#payout');
        const lastdayInput = Swal.getPopup().querySelector('#lastDay');
        const differenceInput = Swal.getPopup().querySelector('#difference');

        beneficiariesInput.addEventListener('input', calculateFund);
        servedInput.addEventListener('input', calculateDisbursed);
        servedInput.addEventListener('input', calculatePercent);
        servedInput.addEventListener('input', calculateUnpaid);
        servedInput.addEventListener('input', calculateSpecialPayout);
        servedInput.addEventListener('input', calculateNORSA);
        servedInput.addEventListener('input', calculateUndisbursed);
        payoutInput.addEventListener('input', calculateDifference);
        lastdayInput.addEventListener('input', calculateDifference);

        function calculateFund() {
            const beneficiariesValue = parseFloat(beneficiariesInput.value);
            if (!isNaN(beneficiariesValue)) {
                const calculatedFund = beneficiariesValue * 370 * 20;
                const calculatedUnpaid = beneficiariesValue;
                const calculatedUndisbursed = beneficiariesValue * 370 * 20;
                fundInput.value = numberFormat(calculatedFund);
                unpaidInput.value = calculatedUnpaid;
                undisbursedInput.value = numberFormat(calculatedUndisbursed);
            } else {
                fundInput.value = '';
                unpaidInput.value = '';
                undisbursedInput.value = '';
            }
        }

        function calculateDisbursed() {
            const servedValue = parseFloat(servedInput.value);
            if (!isNaN(servedValue)) {
                const calculatedDisbursed = servedValue * 370 * 20;
                disbursedInput.value = numberFormat(calculatedDisbursed);
            } else {
                disbursedInput.value = '';
            }
        }

        function calculatePercent() {
            const beneficiariesValue = parseFloat(beneficiariesInput.value);
            const servedValue = parseFloat(servedInput.value);
            if (!isNaN(servedValue) && !isNaN(beneficiariesValue)) {
                const calculatedPercent = (servedValue / beneficiariesValue);
                percentInput.value = percentFormat(calculatedPercent);
            } else {
                percentInput.value = '';
            }
        }

        function calculateUnpaid() {
            const beneficiariesValue = parseFloat(beneficiariesInput.value);
            const servedValue = parseFloat(servedInput.value);
            if (!isNaN(beneficiariesValue) && !isNaN(servedValue)) {
                const calculatedUnpaid = beneficiariesValue - servedValue;
                unpaidInput.value = calculatedUnpaid;
            } else {
                unpaidInput.value = '';
            }
        }

        function calculateSpecialPayout() {
            const beneficiariesValue = parseFloat(beneficiariesInput.value);
            const servedValue = parseFloat(servedInput.value);
            if (!isNaN(beneficiariesValue) && !isNaN(servedValue)) {
                const calculatedSpecialPayout = beneficiariesValue - servedValue;
                specialPayoutInput.value = calculatedSpecialPayout;
            } else {
                specialPayoutInput.value = '';
            }
        }

        function calculateNORSA() {
            const beneficiariesValue = parseFloat(beneficiariesInput.value);
            const servedValue = parseFloat(servedInput.value);
            if (!isNaN(beneficiariesValue) && !isNaN(servedValue)) {
                const calculatedNORSA = beneficiariesValue - servedValue;
                norsaInput.value = calculatedNORSA;
            } else {
                norsaInput.value = '';
            }
        }

        function calculateUndisbursed() {
            const beneficiariesValue = parseFloat(beneficiariesInput.value);
            const servedValue = parseFloat(servedInput.value);
            if (!isNaN(beneficiariesValue) && !isNaN(servedValue)) {
                const calculatedUndisbursed = (beneficiariesValue - servedValue) * 370 * 20;
                undisbursedInput.value = numberFormat(calculatedUndisbursed);
            } else {
                undisbursedInput.value = '';
            }
        }

        function calculateDifference() {
            const payoutValue = new Date(payoutInput.value);
            const lastdayValue = new Date(lastdayInput.value);
            if (!isNaN(payoutValue.getTime()) && !isNaN(lastdayValue.getTime())) {
                const timeDifference = Math.abs(payoutValue.getTime() - lastdayValue.getTime());
                const differenceInDays = Math.ceil(timeDifference / (1000 * 3600 * 24));
                differenceInput.value = differenceInDays;
            } else {
                differenceInput.value = '';
            }
        }
    },
    preConfirm: async () => {
            const province = Swal.getPopup().querySelector('#province').value;
            const pdo = Swal.getPopup().querySelector('#pdo').value;
            const batchNumber = Swal.getPopup().querySelector('#batchNumber').value;
            const municipality = Swal.getPopup().querySelector('#municipality').value;
            const barangay = Swal.getPopup().querySelector('#barangay').value;
            const beneficiaries = Swal.getPopup().querySelector('#beneficiaries').value;
            const fund = Swal.getPopup().querySelector('#fund').value;
            const unpaid = Swal.getPopup().querySelector('#unpaid').value;
            const undisbursed = Swal.getPopup().querySelector('#undisbursed').value;
            const payout = Swal.getPopup().querySelector('#payout').value;
            const paymaster = Swal.getPopup().querySelector('#paymaster').value;
            const orientation = Swal.getPopup().querySelector('#orientation').value;
            const speaker = Swal.getPopup().querySelector('#speaker').value;
            const secondDay = Swal.getPopup().querySelector('#secondDay').value;
            const monitoring = Swal.getPopup().querySelector('#monitoring').value;
            const evaluator = Swal.getPopup().querySelector('#evaluator').value;
            const lastDay = Swal.getPopup().querySelector('#lastDay').value;
            const difference = Swal.getPopup().querySelector('#difference').value;
            const project = Swal.getPopup().querySelector('#project').value;
            const findings = Swal.getPopup().querySelector('#findings').value;
            const kia = Swal.getPopup().querySelector('#kia').value;
            const payroll = Swal.getPopup().querySelector('#payroll').value;
            const tts = Swal.getPopup().querySelector('#tts').value;
            const war = Swal.getPopup().querySelector('#war').value;
            const coc = Swal.getPopup().querySelector('#coc').value;
            const geobefore = Swal.getPopup().querySelector('#geobefore').value;
            const geoduring = Swal.getPopup().querySelector('#geoduring').value;
            const geoafter = Swal.getPopup().querySelector('#geoafter').value;
            const spelling = Swal.getPopup().querySelector('#spelling').value;
            const replacementsDate = Swal.getPopup().querySelector('#replacementsDate').value;
            const replacements = Swal.getPopup().querySelector('#replacements').value;
            const mebRDate = Swal.getPopup().querySelector('#mebRDate').value;
            const mebR = Swal.getPopup().querySelector('#mebR').value;
            const brgyReso = Swal.getPopup().querySelector('#brgyReso').value;
            const moaCert = Swal.getPopup().querySelector('#moaCert').value;
            const minutes = Swal.getPopup().querySelector('#minutes').value;
            const endorsement = Swal.getPopup().querySelector('#endorsement').value;
            const track_type = Swal.getPopup().querySelector('#track_type').value;

            try {
                // Generate a dynamically generated tracking number
                const trackingNumber = generateTrackingNumber();

                // Make an AJAX request to the server for tracking
                const response = await fetch('track.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        province: province,
                        tracking_number: trackingNumber, // Pass the generated tracking number
                        pdo: pdo,
                        batchNumber: batchNumber,
                        municipality: municipality,
                        barangay: barangay,
                        beneficiaries: beneficiaries,
                        fund: fund,
                        unpaid: unpaid,
                        undisbursed: undisbursed,
                        payout: payout,
                        paymaster: paymaster,
                        orientation: orientation,
                        speaker: speaker,
                        secondDay: secondDay,
                        monitoring: monitoring,
                        evaluator: evaluator,
                        lastDay: lastDay,
                        difference: difference,
                        project: project,
                        findings: findings,
                        kia: kia,
                        payroll: payroll,
                        tts: tts,
                        war: war,
                        coc: coc,
                        geobefore: geobefore,
                        geoduring: geoduring,
                        geoafter: geoafter,
                        spelling: spelling,
                        replacementsDate: replacementsDate,
                        replacements: replacements,
                        mebRDate: mebRDate,
                        mebR: mebR,
                        brgyReso: brgyReso,
                        moaCert: moaCert,
                        minutes: minutes,
                        endorsement: endorsement,
                        track_type: track_type,
                    }),
                });

                const data = await response.json();

                console.log('Tracking response:', data);

                if (data.status === 'success') {
                    // Tracking successful, show success alert with longer timer
                    Swal.fire({
                        icon: 'success',
                        title: 'Tracking Successful',
                        text: data.message,
                        showConfirmButton: true,
                        //timer: 1500, // Display the success alert for 1.5 seconds
                    })
                } else {
                    // Tracking failed, show an error alert
                    Swal.fire({
                        icon: 'error',
                        title: 'Tracking Failed',
                        text: data.message,
                    });
                }
            } catch (error) {
                console.error('Error:', error);
            }
        },
  });
};

window.showTrackForm2 = function () {
  Swal.fire({
    position: 'top',
    width: 'auto',
    title: 'MEB Tracking<hr>',
    showCloseButton: true,
    allowOutsideClick: false,
    html: `
    <div style="max-height: 78vh; overflow-y: auto;">
      <form class="input-container" id="swal-form">
        <input type="text" name="tracking_number" id="tracking_number" hidden>
        <section class="section">
            <div>
                <label for="adas">Date Received by the ADAS*</label><br>
                <input class="swal2-input custom-input adas" type="date" id="adas" name="adas" placeholder="Date Received by the ADAS">
            </div>
            <div>
                <label for="trackDate">Tracking Date*</label><br>
                <input class="swal2-input custom-input trackDate" type="date" id="trackDate" name="trackDate" placeholder="Tracking Date">
            </div>
        </section>
        <hr>
        <section class="section">
            <div>
                <label for="tProvince">Province*</label><br>
                <select class="swal2-select tProvince" id="tProvince" name="tProvince">
                  <option value="" selected disabled>Province</option>
                </select>
            </div>
            <div>
                <label for="tMunicipality">Municipality*</label><br>
                <select class="swal2-select tMunicipality" id="tMunicipality" name="tMunicipality">
                  <option value="" selected disabled>Municipality</option>
                </select>
            </div>
            <div>
                <label for="tBarangay">Barangay*</label><br>
                <select class="swal2-select tBarangay" id="tBarangay" name="tBarangay">
                  <option value="" selected disabled>Barangay</option>
                </select>
            </div>
        </section>
        <hr>
        <section class="section">
            <div>
                <label for="meb">Quantity in MEB*</label><br>
                <input class="swal2-input custom-input meb" type="number" id="meb" name="meb" placeholder="Quantity in MEB">
            </div>
            <div>
                <label for="mer">Quantity in MER*</label><br>
                <input class="swal2-input custom-input mer" type="number" id="mer" name="mer" placeholder="Quantity in MER">
            </div>
        </section>
        <hr>
        <section class="section">
            <div>
                <label for="remarks">Remarks*</label><br>
                <textarea class="swal2-input custom-input remarks" id="remarks" name="remarks" placeholder="Remarks"></textarea>
            </div>
        </section>
        <hr>
        <section class="section">
            <div hidden>
                <label for="track_type">Track Type</label><br>
                <input class="swal2-input custom-input track_type" type="number" id="track_type" name="track_type" value="2" placeholder="Track Type">
            </div>
        </section>
      </form>
    </div>
    `,
    focusConfirm: true,
    showCancelButton: true,
    confirmButtonText: 'Track',
    didOpen: function () {
      const provinceSelect = Swal.getPopup().querySelector('#tProvince');
      const municipalitySelect = Swal.getPopup().querySelector('#tMunicipality');
      const barangaySelect = Swal.getPopup().querySelector('#tBarangay');

      // Fetch provinces from the database
fetch('fetch_data.php?table=provinces')
  .then(response => response.json())
  .then(provinceOptions => {
    provinceOptions.forEach(option => {
      provinceSelect.innerHTML += `<option value="${option.province_name}">${option.province_name}</option>`;
    });

    provinceSelect.addEventListener('change', function () {
      const selectedProvinceName = provinceSelect.value;

      // Fetch municipalities based on the selected province from the database
      fetch(`fetch_data.php?table=municipality&province_id=${encodeURIComponent(selectedProvinceName)}`)
        .then(response => response.json())
        .then(municipalityOptions => {
          municipalitySelect.innerHTML = '<option value="" selected disabled>Municipality</option>';

          municipalityOptions.forEach(municipalityOption => {
            municipalitySelect.innerHTML += `<option value="${municipalityOption.municipality_name}">${municipalityOption.municipality_name}</option>`;
          });

          // Trigger the change event for the municipality select
          municipalitySelect.dispatchEvent(new Event('change'));
        });
    });
  });

// Municipality selection logic
municipalitySelect.addEventListener('change', function () {
  const selectedMunicipalityName = municipalitySelect.value;

  // Fetch barangays based on the selected municipality from the database
  fetch(`fetch_data.php?table=barangay&municipality_id=${encodeURIComponent(selectedMunicipalityName)}`)
    .then(response => response.json())
    .then(barangayOptions => {
      barangaySelect.innerHTML = '<option value="" selected disabled>Barangay</option>';

      barangayOptions.forEach(barangayOption => {
        barangaySelect.innerHTML += `<option value="${barangayOption.brgy_name}">${barangayOption.brgy_name}</option>`;
      });
    });
});
    },
    preConfirm: async () => {
            const adas = Swal.getPopup().querySelector('#adas').value;
            const trackDate = Swal.getPopup().querySelector('#trackDate').value;
            const tProvince = Swal.getPopup().querySelector('#tProvince option:checked').textContent;
            const tMunicipality = Swal.getPopup().querySelector('#tMunicipality option:checked').textContent;
            const tBarangay = Swal.getPopup().querySelector('#tBarangay option:checked').textContent;
            const meb = Swal.getPopup().querySelector('#meb').value;
            const mer = Swal.getPopup().querySelector('#mer').value;
            const remarks = Swal.getPopup().querySelector('#remarks').value;
            const track_type = Swal.getPopup().querySelector('#track_type').value;

            try {
                // Generate a dynamically generated tracking number
                const trackingNumber = generateTrackingNumber();

                // Make an AJAX request to the server for tracking
                const response = await fetch('track2.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        adas: adas,
                        trackDate: trackDate,
                        tProvince: tProvince,
                        tracking_number: trackingNumber, // Pass the generated tracking number
                        tMunicipality: tMunicipality,
                        tBarangay: tBarangay,
                        meb: meb,
                        mer: mer,
                        remarks: remarks,
                        track_type: track_type,
                    }),
                });

                const data = await response.json();

                console.log('Tracking response:', data);

                if (data.status === 'success') {
                    // Tracking successful, show success alert with longer timer
                    Swal.fire({
                        icon: 'success',
                        title: 'Tracking Successful',
                        text: data.message,
                        showConfirmButton: true,
                        //timer: 1500, // Display the success alert for 1.5 seconds
                    })
                } else {
                    // Tracking failed, show an error alert
                    Swal.fire({
                        icon: 'error',
                        title: 'Tracking Failed',
                        text: data.message,
                    });
                }
            } catch (error) {
                console.error('Error:', error);
            }
        },
  });
};

window.showTrackForm3 = function () {
  Swal.fire({
    position: 'top',
    width: 'auto',
    title: 'Administrative Documents Tracking<hr>',
    showCloseButton: true,
    allowOutsideClick: false,
    html: `
    <div style="max-height: 78vh; overflow-y: auto;">
      <form class="input-container" id="swal-form" enctype="multipart/form-data">

        <input type="text" name="tracking_number2" id="tracking_number2" hidden>

        <section class="section">
            <div>
                <label for='aaType'>Select Type:</label><br>
                <select class="swal2-select" id="aaType">
                    <option selected disabled></option>
                    <option value='1'>Incoming Documents</option>
                    <option value='2'>Outgoing Documents</option>
                </select>
            </div>
        </section>

        <hr>

        <section class="section">
            <div id="sect1">
                <label for="aaDate">Date Received:</label><br>
                <input class="swal2-input custom-input aaDate" type="date" id="aaDate" name="aaDate" placeholder="Date">
            </div>
        </section>

        <hr id="hr1">

        <section class="section">
            <div id="sect2">
                <label for="outDate">Outgoing date:</label><br>
                <input class="swal2-input custom-input outDate" type="date" id="outDate" name="outDate" placeholder="Date">
            </div>
        </section>

        <hr id="hr2">

        <section class="section">
            <div id="sect3">
                <label for="description">Description:</label><br>
                <textarea class="swal2-textarea custom-input description" id="description" name="description" placeholder="Description..." style="width: 400px"></textarea>
            </div>
        </section>

        <hr id="hr3">

        <section class="section">
            <div id="sect4">
                <label for="fileToUpload">Upload file:</label><br><br>
                <input class="swal2-input" style="width: 400px !important" type="file" id="fileToUpload" name="fileToUpload">
            </div>
        </section>

        <hr id="hr4">

        <section class="section">
            <div id="sect5">
                <label for="personnel">Receiving Office / Personnel:</label><br>
                <input class="swal2-input custom-input personnel" id="personnel" name="personnel" placeholder="Receiving Office / Personnel" style="width: 20em !important" required>
            </div>
        </section>

        <hr id="hr5">

        <section class="section">
            <div id="sect6">
                <label for="dateReceived">Date Received:</label><br>
                <input class="swal2-input custom-input dateReceived" type="date" id="dateReceived" name="dateReceived" placeholder="Date">
            </div>
        </section>

        <hr id="hr6">

        <section class="section">
            <div id="sect7">
                <label for="remarks2">Remarks:</label><br>
                <textarea class="swal2-textarea custom-input remarks2" id="remarks2" name="remarks2" placeholder="Remarks..." style="width: 400px"></textarea>
            </div>
        </section>

        <hr id="hr7">

      </form>
    </div>
    `,
    focusConfirm: true,
    showCancelButton: true,
    confirmButtonText: 'Track',
    preConfirm: async () => {
      const aaDate = Swal.getPopup().querySelector('#aaDate').value;
      const description = Swal.getPopup().querySelector('#description').value;
      const remarks2 = Swal.getPopup().querySelector('#remarks2').value;
      const fileInput = Swal.getPopup().querySelector('#fileToUpload');
      const aaType = Swal.getPopup().querySelector('#aaType').value;
      const outDate = Swal.getPopup().querySelector('#outDate').value;
      const personnel = Swal.getPopup().querySelector('#personnel').value;
      const dateReceived = Swal.getPopup().querySelector('#dateReceived').value;

      try {
        const formData = new FormData();
        formData.append('aaDate', aaDate);
        formData.append('description', description);
        formData.append('remarks2', remarks2);
        formData.append('tracking_number2', generateTrackingNumber2());
        formData.append('fileToUpload', fileInput.files[0]); // Append the file to FormData
        formData.append('aaType', aaType);
        formData.append('outDate', outDate);
        formData.append('personnel', personnel);
        formData.append('dateReceived', dateReceived);

        const response = await fetch('track3.php', {
          method: 'POST',
          body: formData, // Use FormData for file uploads
        });

        const data = await response.json();

                console.log('Tracking response:', data);

                if (data.status === 'success') {
                    // Tracking successful, show success alert with longer timer
                    Swal.fire({
                        icon: 'success',
                        title: 'Tracking Successful',
                        text: data.message,
                        showConfirmButton: true,
                        //timer: 1500, // Display the success alert for 1.5 seconds
                    })
                } else {
                    // Tracking failed, show an error alert
                    Swal.fire({
                        icon: 'error',
                        title: 'Tracking Failed',
                        text: data.message,
                    });
                }
            } catch (error) {
                console.error('Error:', error);
            }
        },
  });

  const select = document.getElementById('aaType');
  const sect1 = document.getElementById('sect1');
  const sect2 = document.getElementById('sect2');
  const sect3 = document.getElementById('sect3');
  const sect4 = document.getElementById('sect4');
  const sect5 = document.getElementById('sect5');
  const sect6 = document.getElementById('sect6');
  const sect7 = document.getElementById('sect7');
  const hr1 = document.getElementById('hr1');
  const hr2 = document.getElementById('hr2');
  const hr3 = document.getElementById('hr3');
  const hr4 = document.getElementById('hr4');
  const hr5 = document.getElementById('hr5');
  const hr6 = document.getElementById('hr6');
  const hr7 = document.getElementById('hr7');

  select.addEventListener("change", function () {
    const selectValue = this.value;

    if (selectValue === "1") {
      sect1.style.display = "block";
      hr1.style.display = "block";
      sect2.style.display = "none";
      hr2.style.display = "none";
      sect3.style.display = "block";
      hr3.style.display = "block";
      sect4.style.display = "block";
      hr4.style.display = "block";
      sect5.style.display = "none";
      hr5.style.display = "none";
      sect6.style.display = "none";
      hr6.style.display = "none";
      sect7.style.display = "block";
      hr7.style.display = "block";
    } else {
      sect1.style.display = "none";
      hr1.style.display = "none";
      sect2.style.display = "block";
      hr2.style.display = "block";
      sect3.style.display = "block";
      hr3.style.display = "block";
      sect4.style.display = "block";
      hr4.style.display = "block";
      sect5.style.display = "block";
      hr5.style.display = "block";
      sect6.style.display = "block";
      hr6.style.display = "block";
      sect7.style.display = "block";
      hr7.style.display = "block";
    }
  });
};

var tooltipTimeout;

function showTooltip() {
    tooltipTimeout = setTimeout(function() {
      document.getElementById("myTooltip").style.visibility = "visible";
      document.getElementById("myTooltip2").style.visibility = "visible";
    }, 1000); // Set the delay time in milliseconds (e.g., 1000 for 1 second)
}

function hideTooltip() {
    clearTimeout(tooltipTimeout);
    document.getElementById("myTooltip").style.visibility = "hidden";
    document.getElementById("myTooltip2").style.visibility = "hidden";
}

function toggleUpload() {
    var showUpload = document.getElementById("uploadBox");
    
    if (showUpload.style.display === "block") {
        showUpload.style.display = "none";
    } else {
        showUpload.style.display = "block";
    }
}

// JavaScript function to open file in popup with non-editable address bar and print button
function openFilePopup(url) {
    // Calculate the center position
    var screenWidth = window.screen.width;
    var screenHeight = window.screen.height;
    var windowWidth = 800;
    var windowHeight = screenHeight * 0.85;
    var leftPosition = (screenWidth - windowWidth) / 2;
    var topPosition = (screenHeight - windowHeight) / 2;

    // Open the popup window at the center
    var popupWindow = window.open(url, '_blank', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=' + windowWidth + ',height=' + windowHeight + ',left=' + leftPosition + ',top=' + topPosition);
    popupWindow.document.write('<html><head><title>File Preview</title></head><body><iframe src="' + url + '" width="100%" height="100%"></iframe><button onclick="printFile()">Print</button></body></html>');
}

// JavaScript function to print the file in the popup
function printFile() {
    window.print();
}