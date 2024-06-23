<?php
include('config.php');

//get the current date in the format 'YYYY-MM-DD'
$current_date = date('Y-m-d');

//fetch all borrowed books that are overdue
$overdue_books_query = mysqli_query($connect, "SELECT * FROM book_report WHERE due_date < '$current_date' AND borrow_status = 'borrowed'");

if ($overdue_books_query && mysqli_num_rows($overdue_books_query) > 0) {
    while ($overdue_book = mysqli_fetch_assoc($overdue_books_query)) {
        $member_id = $overdue_book['member_id'];
        $book_id = $overdue_book['book_id'];
        $due_date = $overdue_book['due_date'];

        //fetch penalty amount from the 'penalty' table
        $penalty_query = mysqli_query($connect, "SELECT penalty_amount FROM penalty");
        if ($penalty_query && mysqli_num_rows($penalty_query) > 0) {
            $penalty_data = mysqli_fetch_assoc($penalty_query);
            $penalty_amount = $penalty_data['penalty_amount'];

            //calculate penalty
            $days_overdue = round((strtotime($current_date) - strtotime($due_date)) / (60 * 60 * 24));
            $penalty = $days_overdue * $penalty_amount;

            //update 'book_report' table with the calculated penalty
            $update_report_query = mysqli_query($connect, "UPDATE book_report SET penalty = '$penalty' WHERE book_id = '$book_id' AND borrow_status = 'borrowed'");
            if ($update_report_query) {

                //check if the entry already exists in 'member_penalty' table
                $check_existing_query = mysqli_query($connect, "SELECT * FROM member_penalty WHERE member_id = '$member_id' AND book_id = '$book_id'");
                if ($check_existing_query && mysqli_num_rows($check_existing_query) == 0) {
                    //insert data into 'member_penalty' table if there is no existing entry
                    if ($penalty > 0) {
                        $insert_penalty_query = mysqli_query($connect, "INSERT INTO member_penalty (member_id, book_id, penalty, penalty_status) VALUES ('$member_id', '$book_id', '$penalty', 'Not Paid')");

                        if (!$insert_penalty_query) {
                            echo "Error inserting data into member_penalty: " . mysqli_error($connect);
                        }
                    }
                }
            } else {
                echo "Error updating penalty in book_report: " . mysqli_error($connect);
            }
        } else {
            echo "Error fetching penalty amount: " . mysqli_error($connect);
        }
    }
}
?>
