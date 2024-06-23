<?php
include('config.php');

$success_message = '';
$error_message = '';

//check if a book ID is posted
if (isset($_POST['book_id'])) {
    $book_id = mysqli_real_escape_string($connect, $_POST['book_id']);

    //fetch admin ID and member ID in session
    $admin_id = $_SESSION['admin_id'];
    $member_id = $_SESSION['member_id'];

    //fetch data from 'books' table for the specified book_id
    $books_query = mysqli_query($connect, "SELECT * FROM books WHERE id = '$book_id'");
    if ($books_query && mysqli_num_rows($books_query) > 0) {
        $book_data = mysqli_fetch_assoc($books_query);

        //check if the book remarks is 'Available'
        if ($book_data['remarks'] === 'Available') {
            $borrow_date = date('Y-m-d H:i:s');

            //fetch allowed borrow days from 'allowed_days' table
            $allowed_days_query = mysqli_query($connect, "SELECT allowed_days FROM allowed_days");
            if ($allowed_days_query && mysqli_num_rows($allowed_days_query) > 0) {
                $allowed_days_data = mysqli_fetch_assoc($allowed_days_query);
                $allowed_days = $allowed_days_data['allowed_days'];

                //calculate due date
                $due_date = date('Y-m-d H:i:s', strtotime($borrow_date . " + $allowed_days days"));

                //check if the book is already borrowed by the member
                $existing_borrow_query = mysqli_query($connect, "SELECT * FROM borrowed_books WHERE member_id = '$member_id' AND book_id = '$book_id' AND borrow_status = 'borrowed'");
                if ($existing_borrow_query && mysqli_num_rows($existing_borrow_query) == 0) {

                    //insert due date into 'borrowed_books' table
                    $insert_due_date_query = mysqli_query($connect, "INSERT INTO borrowed_books (admin_id, member_id, book_id, borrow_date, due_date, borrow_status) VALUES ('$admin_id', '$member_id', '$book_id', '$borrow_date', '$due_date', 'borrowed')");
                    if ($insert_due_date_query) {

                        //insert the same data into 'book_report' table
                        $insert_report_query = mysqli_query($connect, "INSERT INTO book_report (admin_id, member_id, book_id, borrow_date, due_date, borrow_status, penalty) VALUES ('$admin_id', '$member_id', '$book_id', '$borrow_date', '$due_date', 'borrowed', 'No Penalty')");
                        if ($insert_report_query) {

                            //update the number of copies in the 'books' table
                            $new_copies_count = $book_data['copies'] - 1;
                            $update_copies_query = mysqli_query($connect, "UPDATE books SET copies = '$new_copies_count' WHERE id = '$book_id'");
                            if ($update_copies_query) {
                                if ($new_copies_count == 0) {
                                    $update_remarks_query = mysqli_query($connect, "UPDATE books SET remarks = 'Not Available' WHERE id = '$book_id'");
                                    if (!$update_remarks_query) {
                                        $error_message = "Error updating remarks: " . mysqli_error($connect);
                                    }
                                }
                                //redirect to borrow_transaction.php on success
                                $success_message = "Book borrowed successfully.";
                            } else {
                                $error_message = "Error updating copies count: " . mysqli_error($connect);
                            }
                        } else {
                            $error_message = "Error inserting data into book_report: " . mysqli_error($connect);
                        }
                    } else {
                        $error_message = "Error inserting data into borrowed_books: " . mysqli_error($connect);
                    }
                } else {
                    $error_message = "You have already borrowed this book.";
                }
            } else {
                $error_message = "Error fetching allowed borrow days: " . mysqli_error($connect);
            }
        } else {
            $error_message = "Book is not available for borrowing.";
        }
    } else {
        $error_message = "No data found for the specified book ID.";
    }
} 
//include penalty_update.php to update penalties for overdue books
include('penalty_update.php');
?>
<?php
//display success or error messages
if (!empty($success_message)) {
    echo "<p class='success-message'>$success_message</p>";
} elseif (!empty($error_message)) {
    echo "<p class='error-message'>$error_message</p>";
}
?>
