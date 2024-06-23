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
    <link rel="icon" href="images/logo.png" type="image/icon type">
    <title>ACLC Library | Edit Admin</title>
    <link rel="stylesheet" href="css/admin_edit.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('sidebar.php'); ?> 
    <div class="main-content">
        <?php
        include('config.php');
        if (isset($_GET['id'])) {

            $admin_id = mysqli_real_escape_string($connect, $_GET['id']);

            $admin_query = mysqli_query($connect, "SELECT * FROM admins WHERE id = '$admin_id'");

            if ($admin_query && mysqli_num_rows($admin_query) > 0) {
                $admin = mysqli_fetch_assoc($admin_query);

            ?>
            <fieldset>
                <legend>Edit Admin Information</legend>
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
                <!-- Admin edit form -->
                <form method='POST' action='admin_update.php' enctype="multipart/form-data">
                    <input type='hidden' name='id' value='<?php echo $admin['id']; ?>'>
                    <label class="admin-label">Username</label><br>
                    <input type='text' name='username' value='<?php echo $admin['username']; ?>'><br>
                    <label class="admin-label">Firstname</label><br>
                    <input type='text' name='firstname' value='<?php echo $admin['firstname']; ?>'><br>
                    <label class="admin-label">Middlename</label><br>
                    <input type='text' name='middlename' value='<?php echo $admin['middlename']; ?>'><br>
                    <label class="admin-label">Lastname</label><br>
                    <input type='text' name='lastname' value='<?php echo $admin['lastname']; ?>'><br>
                    <label class="admin-label">Email</label><br>
                    <input type='email' name='email' value='<?php echo $admin['email']; ?>'><br>
                    <!-- Image upload -->
                    <div>
                        <label for="file">Upload Image</label><br>
                        <input type="file" name="file" accept="image/*">
                    </div>
                    <div class="button-container">
                        <button type='submit'><i class='bx bx-check'></i>Update</button>
                        <button type="button" class="cancel-button" onclick="window.location.href='admin_dashboard.php'">Cancel</button>
                    </div>
                </form>
                <?php
                    } else {
                        echo "Admin not found.";
                    }
                } else {
                    echo "Invalid request.";
                }
                ?>
            </fieldset>
        </div>
    <?php include('footer.php');?>
</body>
</html>