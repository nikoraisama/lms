<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
                    echo "File uploaded successfully.";
                } else {
                    echo "Error moving uploaded file.";
                }
            } else {
                echo "Only JPG, JPEG, and PNG files are allowed.";
            }
        } else {
            echo "File size exceeds the limit.";
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "Invalid request.";
}
?>
