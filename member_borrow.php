<link rel="stylesheet" href="css/member_borrow.css">
<?php
    // Fetch the current member's ID from the session
    $member_id = $_SESSION['member_id'];

    // Fetch data from the 'borrowed_books' table
    $borrow_query = mysqli_query($connect, "SELECT bb.borrow_date, bb.due_date, m.usn, m.name, b.title, b.isbn, b.id AS book_id
                                            FROM borrowed_books bb
                                            INNER JOIN members m ON bb.member_id = m.id
                                            INNER JOIN books b ON bb.book_id = b.id
                                            WHERE bb.member_id = '$member_id'
                                            AND bb.borrow_status = 'borrowed'");

    // Display borrowed books with member and book details in a table
    if ($borrow_query && mysqli_num_rows($borrow_query) > 0) {
        echo "<h2>Borrowed Books</h2>";

        // Get success message from URL parameter
        $success_message = isset($_GET['success_message']) ? $_GET['success_message'] : '';
        // Get error message from URL parameter
        $error_message = isset($_GET['error_message']) ? $_GET['error_message'] : '';

        if (!empty($success_message)) {
            // Display success message if not empty
            echo "<p class='success-message'>$success_message</p>";
        }

        if (!empty($error_message)) {
            // Display error message if not empty
            echo "<p class='error-message'>$error_message</p>";
        }

        echo "<div class='member-borrowed-table-wrapper'>";
        echo "<div class='member-borrowed-table'>";
        echo "<table>";
        echo "<tr><th>Title</th><th>ISBN</th><th>Date Borrowed</th><th>Due Date</th><th>Penalty (Php)</th><th>Action</th></tr>";
        while ($row = mysqli_fetch_assoc($borrow_query)) {
            echo "<tr>";
            echo "<td>{$row['title']}</td>";
            echo "<td>{$row['isbn']}</td>";
            echo "<td>{$row['borrow_date']}</td>";
            echo "<td>{$row['due_date']}</td>";

            // Calculate penalty if the due date has passed
            $penalty = 0;
            if (new DateTime() > new DateTime($row['due_date'])) {
                // You should define how the penalty is calculated here
                // Example: $penalty = some_penalty_calculation_function($row['due_date']);
                $penalty_query = mysqli_query($connect, "SELECT penalty FROM book_report WHERE member_id = '$member_id' AND book_id = '{$row['book_id']}'");
                if ($penalty_query && mysqli_num_rows($penalty_query) > 0) {
                    $penalty_data = mysqli_fetch_assoc($penalty_query);
                    $penalty = $penalty_data['penalty'];
                }
            }

            echo "<td>$penalty</td>";
            echo "<td>";
            echo "<form class='return-form' method='GET' action='return_book.php'>";
            echo "<input type='hidden' name='return_id' value='{$row['book_id']}'>";
            echo "<button class='returnBtn' type='submit'><i class='bx bx-x'></i> Return</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
        echo "</div>";
    }
?>
