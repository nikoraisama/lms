<?php
session_start();
include('config.php');

$success_message = '';
$error_message = '';

if (isset($_GET['return_id'])) {
    $return_id = $_GET['return_id'];
    $member_id = $_SESSION['member_id'];
    $admin_id = $_SESSION['admin_id'];

    $books_result = mysqli_query($connect, "SELECT * FROM books WHERE id = '$return_id'");
    if ($books_result && mysqli_num_rows($books_result) > 0) {
        $return_date = date('Y-m-d H:i:s');

        $due_date_query = mysqli_query($connect, "SELECT id, due_date FROM borrowed_books WHERE book_id = '$return_id' AND member_id = '$member_id' AND borrow_status = 'borrowed' ORDER BY due_date DESC LIMIT 1");

        if ($due_date_query && mysqli_num_rows($due_date_query) > 0) {
            $due_date_data = mysqli_fetch_assoc($due_date_query);
            $due_date = $due_date_data['due_date'];
            $borrow_id = $due_date_data['id'];

            $update_borrowed_result = mysqli_query($connect, "UPDATE borrowed_books SET borrow_status = 'returned' WHERE id = '$borrow_id'");
            if ($update_borrowed_result) {
                $insert_returned_result = mysqli_query($connect, "INSERT INTO returned_books (admin_id, member_id, book_id, borrow_status, return_date, due_date) VALUES ('$admin_id', '$member_id', '$return_id', 'returned', '$return_date', '$due_date')");
                if ($insert_returned_result) {
                    //update the number of copies in the 'books' table
                    $books_data = mysqli_fetch_assoc($books_result);
                    $new_copies_count = $books_data['copies'] + 1;
                    $update_copies_query = mysqli_query($connect, "UPDATE books SET copies = '$new_copies_count' WHERE id = '$return_id'");
                    if ($update_copies_query) {
                        if ($new_copies_count > 0) {
                            $update_remarks_query = mysqli_query($connect, "UPDATE books SET remarks = 'Available' WHERE id = '$return_id'");
                            if (!$update_remarks_query) {
                                $error_message = "Error updating remarks: " . mysqli_error($connect);
                            }
                        }
                        $update_report_query = mysqli_query($connect, "UPDATE book_report SET borrow_status = 'returned', return_date = '$return_date' WHERE id = '$borrow_id'");
                        if ($update_report_query) {
                            $success_message = "Book returned successfully.";
                            header("Location: borrow_transaction.php?success_message=" . urlencode($success_message));
                            exit();
                        } else {
                            $error_message = "Error updating data in book_report: " . mysqli_error($connect);
                        }
                    } else {
                        $error_message = "Error updating copies count: " . mysqli_error($connect);
                    }
                } else {
                    $error_message = "Error inserting data into returned_books: " . mysqli_error($connect);
                }
            } else {
                $error_message = "Error updating data in borrowed_books: " . mysqli_error($connect);
            }
        } else {
            $error_message = "Error fetching due date from borrowed_books: " . mysqli_error($connect);
        }
    } else {
        $error_message = "No data found for the specified book ID.";
    }
}

//display success or error messages
if (!empty($success_message)) {
    echo "<p class='success-message'>$success_message</p>";
} elseif (!empty($error_message)) {
    echo "<p class='error-message'>$error_message</p>";
}
?>
