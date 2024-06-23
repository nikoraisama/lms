<link rel="stylesheet" href="css/overdue_book.css">
<?php
    include('config.php');

    // Fetch the current member's ID from the session
    $member_id = $_SESSION['member_id'];

    // Updated SQL query to filter by the current member's ID
    $book_penalty_query = mysqli_query($connect, "SELECT DISTINCT br.borrow_date, br.due_date, br.id, mp.penalty, mp.penalty_status, mp.member_id, m.usn, m.name, b.title, b.isbn, b.id AS book_id
                                                FROM book_report br
                                                INNER JOIN members m ON br.member_id = m.id
                                                INNER JOIN books b ON br.book_id = b.id
                                                INNER JOIN member_penalty mp ON br.id = mp.id 
                                                WHERE mp.member_id = '$member_id'
                                                AND mp.penalty_status = 'Not Paid'");

    if ($book_penalty_query && mysqli_num_rows($book_penalty_query) > 0) {
        echo "<h2>Overdue Books</h2>";

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

        echo "<div class='penalty-table-wrapper'>";
        echo "<div class='penalty-table'>";
        echo "<table>";

        echo "<tr><th>Title</th><th>ISBN</th><th>Date Borrowed</th><th>Due Date</th><th>Penalty (Php)</th><th>Status</th><th>Action</th></tr>";
        while ($row = mysqli_fetch_assoc($book_penalty_query)) {
            echo "<tr>";
            echo "<td>{$row['title']}</td>";
            echo "<td>{$row['isbn']}</td>";
            echo "<td>{$row['borrow_date']}</td>";
            echo "<td>{$row['due_date']}</td>";
            echo "<td>{$row['penalty']}</td>";
            echo "<td>{$row['penalty_status']}</td>";
            echo "<td>";
            echo "<form method='GET' action='pay_book.php'>";
            echo "<input type='hidden' name='penalty_id' value='{$row['id']}'>";
            echo "<button class='payBtn' type='submit'><i class='bx bxs-coin-stack'></i> Pay Penalty</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
        echo "</div><br>";
    }
?>
