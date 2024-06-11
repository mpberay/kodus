<?php
include "header.php";
include "sidenav.php";
include "script.php";

// Check if the form for changing display photo is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitPicture'])) {
    // Check if a file is selected for upload
    if ($_FILES['picture']['size'] > 0) {
        // Validate file type and size
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        if (in_array($_FILES['picture']['type'], $allowedTypes) && $_FILES['picture']['size'] <= $maxFileSize) {
            $uploadDir = 'pictures/';
            // Ensure upload directory exists and is writable
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            // Generate a unique file name to prevent overwriting
            $fileName = uniqid('image_') . '.' . pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
            $uploadFile = $uploadDir . $fileName;
            // Move uploaded file to destination
            if (move_uploaded_file($_FILES['picture']['tmp_name'], $uploadFile)) {
                // Update the database with the file name
                if (isset($username)) { // Ensure username is set
                    $escapedFileName = $conn->real_escape_string($fileName); // Escape file name
                    $updatePictureSql = "UPDATE users SET picture = '$escapedFileName' WHERE username = '$username'";
                    if ($conn->query($updatePictureSql) === TRUE) {
                        echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "Success!",
                            text: "Display photo changed successfully.",
                        }).then(() => {
                        	window.location.href = "settings";
                        });
                        </script>';
                    } else {
                        echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Error updating display photo: ' . $conn->error . '",
                        }).then(() => {
                        	window.location.href = "settings";
                        });
                        </script>';
                    }
                } else {
                    echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "User not found.",
                    }).then(() => {
                    	window.location.href = "settings";
                    });
                    </script>';
                }
            } else {
                echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Error uploading file.",
                }).then(() => {
                	window.location.href = "settings";
                });
                </script>';
            }
        } else {
            echo '<script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Invalid file type or size. Allowed types: JPEG, PNG, GIF, WEBP (Max size: 5MB).",
            }).then(() => {
            	window.location.href = "settings";
            });
            </script>';
        }
    } else {
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Please select a file to upload.",
        }).then(() => {
        	window.location.href = "settings";
        });
        </script>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile Settings | KODUS</title>
</head>
<body>
    <div id="loading-overlay">
        <div id="loading-spinner"></div>
    </div><br>
    <div class="content" id="content">
        <h1>Profile Settings</h1>
        <?php
        if (isset($error)) {
            echo '<div class="alert alert-danger">' . $error . '</div>';
        }
        if (isset($success)) {
            echo '<div class="alert alert-success">' . $success . '</div>';
        }
        ?>
        <br><br>
        <button class="infoBtn" style="position: absolute; left: 170px" id="changeUserBtn" type="button" onclick="toggleChangeUser()">Change Username?</button>

        <button class="infoBtn" style="position: absolute; left: 450px; top: 180px;" id="changePassBtn" type="button" onclick="toggleChangePass()">Change Password?</button>

        <button class="infoBtn" style="position: absolute; left: 720px; top: 180px;" id="changeDPBtn" type="button" style="margin-left: 25px;" onclick="toggleChangeDP()">Change Display Photo?</button>

        <div id="changeUser" style="margin-left: 20px">
            <h3>Change Username:</h3>
            <form method="post" action="userUpdate">
                <label for="username">New Username:</label><br>
                <input type="text" id="new_username" name="new_username" autocomplete="false"><br><br>
                <input class="submitBtn" type="submit" name="Submit">
                <button class="cancelBtn" id="cancelBtn3" type="button" onclick="cancelChangeUser()">Cancel</button>
            </form>
        </div>

        <div id="changePass" style="margin-left: 280px;">
            <h3>Change Password:</h3>
            <form method="post">
                <label for="old_password">Old Password:</label><br>
                <input type="password" id="old_password" name="old_password" autocomplete="false" required><br><br>
                <label for="new_password">New Password:</label><br>
                <input type="password" id="new_password" name="new_password" autocomplete="false"  required><br><br>
                <label for="confirm_password">Confirm Password:</label><br>
                <input type="password" id="confirm_password" name="confirm_password" autocomplete="false"  required><br><br>
                <input class="submitBtn" type="submit" value="Submit">
                <button class="cancelBtn" id="cancelBtn" type="button" onclick="cancelChangePass()">Cancel</button>
            </form>
        </div>

        <div id="changeDP" style="margin-left: 550px;">
            <h3>Change Display Photo:</h3>
            <form method="post" enctype="multipart/form-data" action="dpUpdate">
                <label for="new_picture">Select file:</label><br><br>
                <img id="imagePreview" src="#" alt="Image Preview" style="border-radius: 100%; width: 200px; height: 200px; object-fit: cover; display: none;"><br>
                <input type="file" id="new_picture" name="picture" autocomplete="false" required onchange="previewImage(event)"><br>
                <input class="submitBtn" type="submit" name="submitPicture" value="Submit">
                <button class="cancelBtn" id="cancelBtn2" type="button" onclick="cancelChangeDP()">Cancel</button>
            </form>
        </div>

    </div>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var img = document.getElementById('imagePreview');
                img.src = reader.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>