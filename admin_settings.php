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
    <link rel="stylesheet" href="css/settings.css">
    <title>ACLC Library | Admin Settings</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('sidebar.php'); ?>
    <div class="main-content">
        <h1>Admin Settings</h1><br>
        <hr>
        <?php
        //get success message from URL parameter
        $success_message = isset($_GET['success_message']) ? $_GET['success_message'] : '';
        //get error message from URL parameter
        $error_message = isset($_GET['error_message']) ? $_GET['error_message'] : '';

        if (!empty($success_message)) {
            //display success message if not empty
            echo "<p class='success-message'>$success_message</p>";
        }

        if (!empty($error_message)) {
            //display error message if not empty
            echo "<p class='error-message'>$error_message</p>";
        }
        ?>
        <div class="content-column">
            <?php include('penalty.php'); ?>
        </div>
        <div class="content-column">
            <?php include('allowed_days.php'); ?>
        </div>
    </div>
    <?php include('footer.php');?>
</body>
</html>
