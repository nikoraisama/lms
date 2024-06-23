<?php
session_start(); //start the session to manage user sessions across pages
if (!isset($_SESSION['admin_id'])) { //check if the admin is logged in
    header('Location: admin_login.php'); //redirect to admin login page if not logged in
    exit(); //exit to stop further execution
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png" type="image/icon type">
    <title>ACLC Library | Member Registration</title>
    <link rel="stylesheet" href="css/add_member.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
<?php include('sidebar.php'); ?>
    <div class="main-content">
        <?php
        include('config.php');

        $error_message = ''; // Initialize error message variable
        $success_message = ''; // Initialize success message variable

        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Sanitize user input to prevent SQL injection
            $usn = mysqli_real_escape_string($connect, $_POST['usn']);
            $name = mysqli_real_escape_string($connect, $_POST['name']);
            $course = mysqli_real_escape_string($connect, $_POST['course']);
            $year_level = mysqli_real_escape_string($connect, $_POST['year_level']);
            $type = mysqli_real_escape_string($connect, $_POST['type']);
            $contact = mysqli_real_escape_string($connect, $_POST['contact']);
            $email = mysqli_real_escape_string($connect, $_POST['email']);
            $gender = mysqli_real_escape_string($connect, $_POST['gender']);
            $address = mysqli_real_escape_string($connect, $_POST['address']);

            // File upload handling
            $file_name = $_FILES['file']['name'];
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_error = $_FILES['file']['error'];

            if ($file_error === UPLOAD_ERR_OK) {
                $file_size = $_FILES['file']['size'];
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

                if ($file_size <= 5242880) { // 5MB limit
                    $allowed_extensions = ['jpg', 'jpeg', 'png'];
                    if (in_array(strtolower($file_extension), $allowed_extensions)) {
                        // Generate a unique file name
                        $new_file_name = uniqid('member_image_') . '.' . $file_extension;
                        $upload_path = 'member-images/' . $new_file_name;

                        // Move uploaded file to destination folder
                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            // Insert member data into the 'members' table
                            $add_member = mysqli_query($connect, "INSERT INTO members (usn, name, course, year_level, type, contact, email, gender, address, member_image) 
                                                                    VALUES ('$usn', '$name', '$course', '$year_level', '$type', '$contact', '$email', '$gender', '$address', '$new_file_name')");

                            if ($add_member) {
                                $success_message = "Member added successfully."; 
                                header("Location: add_member.php?success_message=" . urlencode($success_message));
                                exit();
                            } else {
                                $error_message = "Unable to add member.";
                            }
                        } else {
                            $error_message = "Error moving uploaded file.";
                        }
                    } else {
                        $error_message = "Only JPG, JPEG, and PNG files are allowed.";
                    }
                } else {
                    $error_message = "File size exceeds the limit.";
                }
            } else {
                $error_message = "Error uploading file.";
            }
        }
        ?>
        <fieldset style="width: 500px">
            <legend>Member Registration</legend>
            <?php
            //get success message from URL parameter
            $success_message = isset($_GET['success_message']) ? $_GET['success_message'] : '';
            if (!empty($success_message)) {
                //display success message if not empty
                echo "<p class='success-message'>$success_message</p>";
            }

            //get error message from URL parameter
            $error_message = isset($_GET['error_message']) ? $_GET['error_message'] : '';
            if (!empty($error_message)) {
                //display error message if not empty
                echo "<p class='error-message'>$error_message</p>";
            }
            ?>
            <!-- Member registration form -->
            <form method="POST" enctype="multipart/form-data">
                <div class="form">
                    <div class="form1">
                        <input type="text" name="usn" placeholder="Enter Member USN" required><br>
                        <input type="text" name="name" placeholder="Enter Member Fullname" required><br>
                        <select name="course">
                            <option value="No Course">-- Select Course --</option>
                            <option value="BSIT">BSIT</option>
                            <option value="BSCS">BSCS</option>
                            <option value="WAD">WAD</option>
                            <option value="PN">PN</option>
                            <option value="BSHM">BSHM</option>
                            <option value="BSBA">BSBA</option>
                            <option value="BSA">BSA</option>
                            <option value="OMT">OMT</option>
                            <option value="OAT">OAT</option>
                            <option value="HRT">HRT</option>
                        </select><br>
                        <select name="year_level">
                            <option value="No Year Level">-- Select Year Level --</option>
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                            <option value="5th Year">5th Year</option>
                        </select><br>
                        <select name="type" required>
                            <option value="No Type">-- Select Type --</option>
                            <option value="SH">SH</option>
                            <option value="College">College</option>
                            <option value="Faculty">Faculty</option>
                            <option value="Staff">Staff</option>
                        </select><br>
                    </div>
                    <div class="form2">
                        <input type="text" name="contact" placeholder="Enter Contact Number" required><br>
                        <input type="email" name="email" placeholder="Enter Email" required><br>
                        <select name="gender">
                            <option value="No Gender">-- Select Gender --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select><br>
                        <input type="text" name="address" placeholder="Enter Address" required><br>
                        <label for="file">Upload Image</label><br>
                        <input type="file" name="file" accept="image/*">
                    </div>
                </div>
                <div class="button-container">
                    <button type="submit" name="addMember"><i class='bx bx-plus'></i> Add Member</button>
                    <button type="button" class="cancel-button" onclick="window.location.href='directory_member.php'">Cancel</button>
                </div>
            </form>
        </fieldset><br>
    </div>
    <?php include('footer.php');?>
</body> 
</html> 