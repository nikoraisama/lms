<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png" type="image/icon type">
    <title>ACLC Library | Borrowed Books</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/borrow_list.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        function printBorrowList() {
            var table = $('#booksTable').DataTable();
            var originalLength = table.page.len();

            table.page.len(-1).draw(); // Show all entries

            setTimeout(function() {
                window.print();
                table.page.len(originalLength).draw(); // Revert to original length
            }, 500); // Wait for the table to redraw with all entries before printing
        }
    </script>
</head>
<body>
    <?php include('sidebar.php'); ?>
    <div class="main-content">
        <div class="borrow-direct-top">
            <h1>Borrowed Books List</h1>
            <button class="print" onclick="printBorrowList()"><i class='bx bx-printer'></i>Print Borrow List</button>
        </div>
        <div class='borrow-container-wrapper'>
            <div class='borrow-container'>
                <table id='booksTable'>
                    <thead>
                        <tr>
                            <th>Borrower USN</th>
                            <th>Borrower Name</th>
                            <th>Type</th>
                            <th>Book Title</th>
                            <th>Date Borrowed</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include('config.php');
                        //fetch data from 'book_report' table for borrowed books based on the search query
                        $borrowed_query = mysqli_query($connect, "SELECT bb.borrow_date, bb.due_date, m.usn, m.name, m.type, b.title FROM borrowed_books bb
                                                                INNER JOIN members m ON bb.member_id = m.id INNER JOIN books b ON bb.book_id = b.id ORDER BY bb.borrow_date DESC");
                        if ($borrowed_query && mysqli_num_rows($borrowed_query) > 0) {
                            while ($row = mysqli_fetch_assoc($borrowed_query)) {
                                echo "<tr>";
                                echo "<td>{$row['usn']}</td>";
                                echo "<td>{$row['name']}</td>";
                                echo "<td>{$row['type']}</td>";
                                echo "<td>{$row['title']}</td>";
                                echo "<td>{$row['borrow_date']}</td>";
                                echo "<td>{$row['due_date']}</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include('footer.php');?>
    <script>
        $(document).ready(function() {
            $('#booksTable').DataTable({
                "searching": true,
                "paging": true,
                "info": true,
                "lengthMenu": [10, 25, 50, 100],
                "pageLength": 10
            });
        });
    </script>
</body>
</html>