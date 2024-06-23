<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

//initialize success and error message variables
$success_message = '';
$error_message = '';

include('config.php');
if (isset($_GET['id'])) {

    $admin_id = mysqli_real_escape_string($connect, $_GET['id']);

    //disable foreign key checks
    $queryDisableFK = mysqli_query($connect, "SET FOREIGN_KEY_CHECKS=0");

    $delete_query = mysqli_query($connect, "DELETE FROM admins WHERE id = '$admin_id'");

    //enable foreign key checks
    $queryEnableFK = mysqli_query($connect, "SET FOREIGN_KEY_CHECKS=1");

    if ($delete_query) {
        $success_message = "Admin successfully removed."; 
        header("Location: admin_dashboard.php?success_message=" . urlencode($success_message));
        exit();
    } else {
        $error_message = "Unable remove admin.";
        header("Location: Admin_dashboard.php?error_message=" . urlencode($error_message));
        exit();
}
} else {
    $error_message = "Invalid request.";
}

//redirect with error message if there was an issue
if (!empty($error_message)) {
    header("Location: admin_dashboard.php?error_message={$error_message}");
    exit();
}
?>
