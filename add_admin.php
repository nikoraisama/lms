<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="css/add_admin.css">
    <title>ACLC Library | Add Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('sidebar.php'); ?>
    <div class="main-content">
        <?php
        include('config.php');

        // Initialize error and success message variables
        $error_message = '';
        $success_message = '';

        // Define variables to store form data and errors
        $username = $password = $confirm_password = $firstname = $middlename = $lastname = $email = "";
        $username_err = $password_err = $confirm_password_err = $firstname_err = $lastname_err = $email_err = "";

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addAdmin'])) {

            // Validate username
            if (empty($_POST['username'])) {
                $username_err = "<br>Please enter a username.";
            } else {
                $username = mysqli_real_escape_string($connect, $_POST['username']);
            }

            // Validate password
            if (empty($_POST['password'])) {
                $password_err = "<br>Please enter a password.";
            } else {
                $password = mysqli_real_escape_string($connect, $_POST['password']);
            }

            // Validate confirm password
            if (empty($_POST['confirm_password'])) {
                $confirm_password_err = "<br>Please confirm password.";
            } else {
                $confirm_password = mysqli_real_escape_string($connect, $_POST['confirm_password']);
                if ($password != $confirm_password) {
                    $confirm_password_err = "<br>Password did not match.";
                }
            }

            // Validate first name
            if (empty($_POST['firstname'])) {
                $firstname_err = "<br>Please enter a first name.";
            } else {
                $firstname = mysqli_real_escape_string($connect, $_POST['firstname']);
            }

            // Validate last name
            if (empty($_POST['lastname'])) {
                $lastname_err = "<br>Please enter a last name.";
            } else {
                $lastname = mysqli_real_escape_string($connect, $_POST['lastname']);
            }

            // Extract middle name if provided
            if (isset($_POST['middlename'])) {
                $middlename = mysqli_real_escape_string($connect, $_POST['middlename']);
            }

            // Validate email
            if (empty($_POST['email'])) {
                $email_err = "<br>Please enter your email.";
            } else {
                $email = mysqli_real_escape_string($connect, $_POST['email']);
            }

            // If no input errors, proceed with adding the admin user
            if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($firstname_err) && empty($lastname_err) && empty($email_err)) {
                
                // Hash the password using password_hash() function
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

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
                            $new_file_name = uniqid('admin_image_') . '.' . $file_extension;
                            $upload_path = 'admin-images/' . $new_file_name;

                            // Move uploaded file to destination folder
                            if (move_uploaded_file($file_tmp, $upload_path)) {
                                // Insert admin data into admins' table
                                $add_admin_query = mysqli_query($connect, "INSERT INTO admins (username, password, firstname, middlename, lastname, email, admin_image) 
                                                                            VALUES ('$username', '$hashed_password', '$firstname', '$middlename', '$lastname', '$email', '$new_file_name')");
                                
                                if ($add_admin_query) {
                                    $success_message = "Admin added successfully.";
                                    header("Location: add_admin.php?success_message=" . urlencode($success_message));
                                    exit();
                                } else {
                                    $error_message = "Unable to add admin. SQL Error: " . mysqli_error($connect);
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
        }
        ?>
        <fieldset style="width: 500px;">
            <legend>Admin Registration</legend>
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
            <form method="POST" class="addAdmin" enctype="multipart/form-data">
                <div class="form">
                    <div class="form1">
                        <input class="addAdmin-input" type="text" id="username" name="username" placeholder="Username" value="<?php echo $username; ?>">
                        <span style="color: red; font-size: 13px;"><?php echo $username_err; ?></span><br>

                        <input class="addAdmin-input" type="password" id="password" name="password" placeholder="Password">
                        <span style="color: red; font-size: 13px;"><?php echo $password_err; ?></span><br> 

                        <input class="addAdmin-input" type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password">
                        <span style="color: red; font-size: 13px;"><?php echo $confirm_password_err; ?></span><br>
                    </div>
                    <div class="form2">
                        <input class="addAdmin-input" type="text" id="firstname" name="firstname" placeholder="First Name" value="<?php echo $firstname; ?>">
                        <span style="color: red; font-size: 13px;"><?php echo $firstname_err; ?></span><br>

                        <input class="addAdmin-input" type="text" id="middlename" name="middlename" placeholder="Middle Name" value="<?php echo $middlename; ?>">
                        <br>

                        <input class="addAdmin-input" type="text" id="lastname" name="lastname" placeholder="Last Name" value="<?php echo $lastname; ?>">
                        <span style="color: red; font-size: 13px;"><?php echo $lastname_err; ?></span><br>

                        <input class="addAdmin-input" type="email" name="email" placeholder="Enter Email" value="<?php echo $email; ?>">
                        <span style="color: red; font-size: 13px;"><?php echo $email_err; ?></span><br>
                    </div>
                </div>
                <div>
                    <label for="file">Upload Image</label><br>
                    <input type="file" name="file" accept="image/*"><br>
                </div>
                <div class="button-container">
                    <button type="submit" name="addAdmin"><i class='bx bx-plus' style="margin-right: 5px;"></i>Add Admin</button>
                    <button type="button" class="cancel-button" onclick="window.location.href='admin_dashboard.php'">Cancel</button>
                </div>
            </form>
        </fieldset>
    </div>
    <?php include('footer.php');?>
</body>
</html>
