<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

include('config.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //initialize success and error messages
    $success_message = '';
    $error_message = '';

    $admin_id = mysqli_real_escape_string($connect, $_POST['id']);
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $firstname = mysqli_real_escape_string($connect, $_POST['firstname']);
    $middlename = mysqli_real_escape_string($connect, $_POST['middlename']);
    $lastname = mysqli_real_escape_string($connect, $_POST['lastname']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);

    $update_query = mysqli_query($connect, "UPDATE admins SET username = '$username', firstname = '$firstname', middlename = '$middlename', lastname = '$lastname', email = '$email' WHERE id = '$admin_id'");

    if ($update_query) {
    // File upload handling
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png'];

        if (in_array(strtolower($file_extension), $allowed_extensions) && $file_size <= 5242880) { // 5MB limit
            $new_file_name = uniqid('admin_image_') . '.' . $file_extension;
            $upload_path = 'admin-images/' . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Update admin's profile picture path in the database
                $update_picture_query = mysqli_query($connect, "UPDATE admins SET admin_image = '$new_file_name' WHERE id = '$admin_id'");

                if ($update_picture_query) {
                    $success_message .= " Admin image updated successfully.";
                    header("Location: admin_edit.php?id={$admin_id}&success_message=" . urlencode($success_message));
                    exit();
                } else {
                    $error_message .= " Unable to update admin image.";
                }
            } else {
                $error_message .= " Error moving uploaded file.";
            }
        } else {
            $error_message .= " Invalid file format or size exceeds the limit.";
        }
    } else {
        $error_message .= " Error uploading file.";
    }
        $success_message .= " Admin updated successfully without changing the image.";
        header("Location: admin_edit.php?id={$admin_id}&success_message=" . urlencode($success_message));
        exit();
    } else {
        $error_message .= " Unable to update admin details.";
        header("Location: admin_edit.php?id={$admin_id}&error_message=" . urlencode($error_message));
        exit();
    }
} else {
    echo "Invalid request.";
}
?>
