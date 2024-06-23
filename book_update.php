<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = mysqli_real_escape_string($connect, $_POST['book_id']);
    $title = mysqli_real_escape_string($connect, $_POST['title']);
    $publish_year = mysqli_real_escape_string($connect, $_POST['publish_year']);
    $isbn = mysqli_real_escape_string($connect, $_POST['isbn']);
    $status = mysqli_real_escape_string($connect, $_POST['status']);
    $copies = mysqli_real_escape_string($connect, $_POST['copies']);
    $remarks = mysqli_real_escape_string($connect, $_POST['remarks']);
    $category = mysqli_real_escape_string($connect, $_POST['category']);
    $author1 = mysqli_real_escape_string($connect, $_POST['author1']);
    $author2 = mysqli_real_escape_string($connect, $_POST['author2']);
    $author3 = mysqli_real_escape_string($connect, $_POST['author3']);

    // Handle image upload
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_error = $_FILES['file']['error'];

    if ($file_error === UPLOAD_ERR_OK) {
        $file_size = $_FILES['file']['size'];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

        if ($file_size <= 5242880) { // 5MB limit
            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            if (in_array(strtolower($file_extension), $allowed_extensions)) {
                $new_file_name = uniqid('book_image_') . '.' . $file_extension;
                $upload_path = 'book-images/' . $new_file_name;
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // Update the book image filename in the database
                    $update_image_query = mysqli_query($connect, "UPDATE books SET book_image = '$new_file_name' WHERE id = '$book_id'");
                    if ($update_image_query) {
                        $success_message = "Book image uploaded successfully.";
                    } else {
                        $error_message = "Error updating book image in the database.";
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

    // Update the rest of the book details if image upload was successful
    if (!isset($error_message)) {
        $update_book_query = mysqli_query($connect, "UPDATE books SET title = '$title', publish_year = '$publish_year', isbn = '$isbn', status = '$status',
            copies = '$copies', remarks = '$remarks' WHERE id = '$book_id'");

        if ($update_book_query) {
            $update_category_query = mysqli_query($connect, "UPDATE category SET category_name = '$category' WHERE id = (SELECT category_id FROM books WHERE id = '$book_id')");

            if ($update_category_query) {
                $authors = [$author1, $author2, $author3];
                foreach ($authors as $index => $author_fullname) {
                    if (!empty($author_fullname)) {
                        $author_index = $index + 1;

                        $check_author_query = mysqli_query($connect, "SELECT id FROM author WHERE author_fullname = '$author_fullname'");
                        if (mysqli_num_rows($check_author_query) > 0) {
                            $author_row = mysqli_fetch_assoc($check_author_query);
                            $author_id = $author_row['id'];
                        } else {
                            mysqli_query($connect, "INSERT INTO author (author_fullname) VALUES ('$author_fullname')");
                            $author_id = mysqli_insert_id($connect);
                        }

                        $check_books_author_query = mysqli_query($connect, "SELECT * FROM books_author WHERE book_id = '$book_id' AND author_id = '$author_id'");
                        if (mysqli_num_rows($check_books_author_query) == 0) {
                            mysqli_query($connect, "INSERT INTO books_author (book_id, author_id) VALUES ('$book_id', '$author_id')");
                        } else {
                            mysqli_query($connect, "UPDATE books_author SET author_id = '$author_id' WHERE book_id = '$book_id' AND author_id = (SELECT author_id FROM books_author WHERE book_id = '$book_id' LIMIT $index,1)");
                        }
                    }
                }
                $success_message = "Book updated successfully.";
            } else {
                $error_message = "Unable to update book category.";
            }
        } else {
            $error_message = "Error updating book.";
        }
    }

    // Redirect with success or error message
    if (isset($success_message)) {
        header("Location: book_edit.php?id={$book_id}&success_message=" . urlencode($success_message));
        exit();
    } elseif (isset($error_message)) {
        header("Location: book_edit.php?id={$book_id}&error_message=" . urlencode($error_message));
        exit();
    }
} else {
    header("Location: book_edit.php?id={$book_id}&error_message=Invalid request.");
    exit();
}
?>
