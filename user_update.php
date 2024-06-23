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

    
    //handle form data first
    $usn = mysqli_real_escape_string($connect, $_POST['usn']);
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $course = mysqli_real_escape_string($connect, $_POST['course']);
    $year_level = mysqli_real_escape_string($connect, $_POST['year_level']);
    $type = mysqli_real_escape_string($connect, $_POST['type']);
    $gender = mysqli_real_escape_string($connect, $_POST['gender']);
    $address = mysqli_real_escape_string($connect, $_POST['address']);
    $contact = mysqli_real_escape_string($connect, $_POST['contact']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);

    $update_query = mysqli_query($connect, "UPDATE members SET name = '$name', course = '$course', year_level = '$year_level', type = '$type', gender = '$gender',
              address = '$address', contact = '$contact', email = '$email' WHERE usn = '$usn'");

    if ($update_query) {
        //file upload handling
        if (!empty($_FILES['file']['name'])) {
            $file_name = $_FILES['file']['name'];
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_error = $_FILES['file']['error'];

            if ($file_error === UPLOAD_ERR_OK) {
                $file_size = $_FILES['file']['size'];
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

                if ($file_size <= 5242880) { // 5MB limit
                    $allowed_extensions = ['jpg', 'jpeg', 'png'];
                    if (in_array(strtolower($file_extension), $allowed_extensions)) {
                        $new_file_name = uniqid('member_image_') . '.' . $file_extension;
                        $upload_path = 'member-images/' . $new_file_name;
                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            $update_image_query = mysqli_query($connect, "UPDATE members SET member_image = '$new_file_name' WHERE usn = '$usn'");
                            if ($update_image_query) {
                                $success_message = "Member image updated successfully.";
                                header("Location: user_edit.php?usn={$usn}&success_message=" . urlencode($success_message));
                                exit();
                            } else {
                                $error_message = "Error updating member image.";
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
        } else {
            $success_message = "Member updated successfully without changing the image.";
            header("Location: user_edit.php?usn={$usn}&success_message=" . urlencode($success_message));
            exit();
        }
    } else {
        $error_message = "Unable to update member details.";
        header("Location: user_edit.php?usn={$usn}&error_message=" . urlencode($error_message));
        exit();
    }
} else {
    echo "Invalid request.";
}
?>