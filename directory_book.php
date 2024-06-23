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
    <title>ACLC Library | Book Directory</title>
    <link rel="stylesheet" href="css/directory_book.css">
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
        function printBookList() {
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
        <div class="direc-b-top">
            <h1>Books Directory</h1>
            <?php
            //get success message from URL parameter
            $success_message = isset($_GET['success_message']) ? $_GET['success_message'] : '';
            if (!empty($success_message)) {
                //display success message if not empty
                echo "<p class='success-message'>$success_message</p>";
            }

            //get error message from URL parameter
            $error_message = isset($_GET['error_message']) ? $_GET['error_message'] : '';
            if (!empty($error_message)) {
                //display error message if not empty
                echo "<p class='error-message'>$error_message</p>";
            }
            ?>
        <div class="top-button">
            <button class="add" onclick="window.location.href = 'add_book.php'"><i class='bx bx-plus'></i>Add Book</button>
            <button class="print" onclick="printBookList()"><i class='bx bx-printer'></i>Print Book List</button>
        </div>
        </div>
        <div class='b-directory-table-wrapper'>
            <div class='b-directory-table'>
                <table id='booksTable'>
                    <thead>
                        <tr>
                            <th>Book Image</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Copies</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    include('config.php');

                    //fetch data from the 'books' table
                    $search_book = mysqli_query($connect, "SELECT b.*, c.category_name FROM books b 
                                                            INNER JOIN category c ON b.category_id = c.id ORDER BY b.id DESC");

                    if ($search_book && mysqli_num_rows($search_book) > 0) {
                        while ($row = mysqli_fetch_assoc($search_book)) {
                            echo "<tr>";
                            echo "<td>";
                            if (!empty($row['book_image'])) {
                                echo "<img src='book-images/" . $row['book_image'] . "' alt='Book Image' style='max-width: 50px; max-height: 50px;'>";
                            } else {
                                echo "No image available.";
                            }
                            echo "</td>";
                            echo "<td>{$row['title']}</td>";
                            echo "<td>";

                            //fetch authors for the current book ID
                            $book_id = $row['id'];
                            $author_query = mysqli_query($connect, "SELECT a.author_fullname FROM author a
                                                                    INNER JOIN books_author ba ON a.id = ba.author_id
                                                                    WHERE ba.book_id = '$book_id'");
                            $authors = [];
                            while ($author_row = mysqli_fetch_assoc($author_query)) {
                                $authors[] = $author_row['author_fullname'];
                            }
                            //display concatenated authors in the same cell
                            echo implode(', ', $authors);
                            echo "</td>";
                            echo "<td>{$row['isbn']}</td>";
                            echo "<td>{$row['category_name']}</td>";
                            echo "<td>{$row['status']}</td>";
                            echo "<td>{$row['copies']}</td>";
                            echo "<td>{$row['remarks']}</td>";
                            echo "<td>
                                    <button class='button-book' onclick=\"window.location.href='book_view.php?id={$row['id']}'\"><i class='bx bx-info-circle'></i></button>
                                    <button class='button-edit' onclick=\"window.location.href='book_edit.php?id={$row['id']}'\"><i class='bx bx-edit'></i></button>
                                    <button class='button-del' onclick=\"if (confirm('Are you sure you want to delete this book?')) { window.location.href='book_delete.php?id={$row['id']}'; }\"><i class='bx bx-trash'></i></button>
                                </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No Book exists.</td></tr>";
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
                "paging": true,
                "searching": true,
                "info": true,
                "lengthMenu": [10, 25, 50, 100],
                "pageLength": 10,
                "columnDefs": [
                    { "orderable": false, "targets": [7] }
                ]
            });
        });
    </script>>
</body>
</html>