<!DOCTYPE html>
<html lang="en">
<head>
<title>KliMalasakit</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="assets/logo.ico" type="image/x-icon">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
</head>
<body>
	<div class="sidenav" id="sidenav">
		<img class="logo" src="assets/logo.png">
		<hr>
		<a href="#">Link</a>
		<a href="#">Link</a>
		<button class="dropdown-btn">Link 
			<i id="caret" class="fa fa-caret-down"></i>
		</button>
		<div class="dropdown-container">
			<a href="#">Link 1</a>
			<a href="#">Link 2</a>
			<a href="#">Link 3</a>
		</div>
		<a href="#">Link</a>
	</div>

	<div class="content" id="content">
		<h2>
			<button id="navBtn" onclick="sidenavToggle()">☰</button>
			KliMalasakit
		</h2>
	</div>
		<hr>

<script>
	function sidenavToggle() {
		var sideNav = document.getElementById("sidenav");
		var content = document.getElementById("content");
		var navBtn = document.getElementById("navBtn");

		if (!sideNav || !content) {
			console.error("SideNav, content, or navBtn element not found");
			return;
		}

		var computedWidth = window.getComputedStyle(sideNav).width;
		var isOpen = computedWidth === "150px";

		if (isOpen) {
			sideNav.style.width = "0";
			content.style.marginLeft = "0";
			navBtn.style.transform = "rotate(-360deg)";
		} else {
			sideNav.style.width = "150px";
			content.style.marginLeft = "150px";
			navBtn.style.transform = "rotate(360deg)";
		}
	}

	/* Updated event listener to toggle caret icon */
	var dropdown = document.getElementsByClassName("dropdown-btn");
	var i;

	for (i = 0; i < dropdown.length; i++) {
		dropdown[i].addEventListener("click", function() {
			this.classList.toggle("active");
			var caretIcon = this.querySelector("#caret");
			caretIcon.classList.toggle("fa-caret-down");
			caretIcon.classList.toggle("fa-caret-right");
			var dropdownContent = this.nextElementSibling;
			if (dropdownContent.style.display === "block") {
				dropdownContent.style.display = "none";
			} else {
				dropdownContent.style.display = "block";
			}
		});
	}
</script>
</body>
</html>