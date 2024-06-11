<?php
    session_start();
    
    if(isset($_SESSION['username'])) {
        header("location: home");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RRP-LAWA-BINHI</title>
    <link rel="icon" href="assets/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/sweetalert2.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <style>
        #showPassword, #showPassword1, #showPassword2 {
            display: none;
        }
    </style>
</head>
<body>
    <div class="RRP-LAWA-BINHI">
        <center>
            <h1>RRP - LAWA and BINHI</h1>
            <h2><i class="abb">R</i>isk <i class="abb">R</i>esiliency <i class="abb">P</i>rogram <br> through <i class="abb">L</i>ocal <i class="abb">A</i>daptation to <i class="abb">W</i>ater <i class="abb">A</i>ccess <br> and <i class="abb">B</i>reaking <i class="abb">I</i>nsufficiency through <i class="abb">N</i>utritious <i class="abb">H</i>arvest for the <i class="abb">I</i>mpoverished</h2>
        </center>
    </div>
    <section class="getStarted">
        <center>
            <button class="button button1" id="showLoginButton">Get started!</button>
        </center>
    </section>
<button id="showRegisterButton" hidden>Show Register Form</button>
<script src="js/sweetalert2@10.js"></script> <!--https://cdn.jsdelivr.net/npm/sweetalert2@10-->
<script src="script.js"></script>
<button id="logoutButton" hidden></button>
<div id="toggle-container" hidden>
    <i id="dayNightToggle" class="fa fa-moon-o" onclick="toggleMode()"></i>
</div>
</body>
</html>