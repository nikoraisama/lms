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
    <title>ACLC Library | Member Borrow Dashboard</title>
    <link rel="stylesheet" href="css/borrow_transaction.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <?php include('sidebar.php'); ?>
    <div class="main-content">
        <?php
        include('config.php');

        //check if member USN and member ID are set in the session
        if (isset($_SESSION['member_usn'], $_SESSION['member_id'])) {
            //fetch member USN and member ID from the session
            $usn = $_SESSION['member_usn'];
            $member_id = $_SESSION['member_id'];

            //fetch the user's details from the 'members' table
            $member_query = mysqli_query($connect, "SELECT id, name, member_image FROM members WHERE usn = '$usn'");
            if ($member_query) {
                if (mysqli_num_rows($member_query) > 0) {
                    $member_data = mysqli_fetch_assoc($member_query);
                    $name = $member_data['name'];
                    $image = $member_data['member_image'];
                } else {
                    //handle the case where user details are not found
                    $name = "Unknown";
                }
            }
        }      
        ?>            
        <!--display the user's details-->
        <h1>Member Details</h1>
        <div class="member-container">
            <div class="img-container">
            <?php
                if (!empty($image)) {
                    echo "<img src='member-images/" . $image . "' alt='Member Image'>";
                } else {
                    echo "No image available.";
                }
            ?>
            </div>
            <div class='details'>
                <p>USN: <?php echo "$usn"?></p>
                <p>Name: <?php echo "$name"?></p>

                <!--logout button-->
                <form class="logout-form" action='borrow_logout.php' method='POST'>
                    <button class="logoutBtn" type="submit" name="logout">Exit Member</button>
                </form>
            </div>
        </div>
        <hr> 
        <h2>Borrow Book</h2>
        <div class='borrow-table-wrapper'>
            <div class='search-form'>
                <form class="searchbar" method='GET'>
                    <input type='text' name='search' placeholder='Search for books...'>
                    <button class="search-btn" type='submit' name="submit">
                        <i class='bx bx-search-alt'></i> Search
                    </button>
                </form>
            </div>
            <div class='borrow-table'>
                <table id='booksTable'>
                    <thead>
                        <tr>
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
                        <!--display book results to borrow-->
                        <?php
                        if (isset($_GET['search'])) {
                            $search = mysqli_real_escape_string($connect, $_GET['search']);
                            $borrow_book_query = mysqli_query($connect, "SELECT b.*, c.category_name FROM books b
                                                                        INNER JOIN category c ON b.category_id = c.id
                                                                        WHERE b.title LIKE '%$search%' OR b.isbn LIKE '%$search%' OR c.category_name LIKE '%$search%'");

                            if ($borrow_book_query && mysqli_num_rows($borrow_book_query) > 0) {
                                while ($row = mysqli_fetch_assoc($borrow_book_query)) {
                                    echo "<tr>";
                                    echo "<td>{$row['title']}</td>";
                                    echo "<td>";
                                    //fetch authors for the current book ID
                                    $book_id = $row['id'];
                                    $author_query = mysqli_query($connect, "SELECT author_fullname FROM author
                                                                            INNER JOIN books_author ba ON author.id = ba.author_id
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
                                    echo "<td>";
                                    echo "<form method='POST'>";
                                    echo "<input type='hidden' name='book_id' value='{$row['id']}'>";
                                    echo "<button class='borrowBtn' type='submit'><i class='bx bx-check' ></i> Borrow</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            }
                        } 
                        include('borrow_book.php');
                        ?>
                    </tbody>
                </table>
            </div>         
        </div>
        <?php include('overdue_book.php'); ?> 
        <?php include('member_borrow.php'); ?>         
    </div>
    <?php include('footer.php'); ?>
    <script>
        $(document).ready(function() {
            $('#booksTable').DataTable({
                "searching": false,
                "paging": true,
                "info": true,
                "lengthMenu": [10, 25, 50, 100],
                "pageLength": 10,
                "columnDefs": [
                    { "orderable": false, "targets": [7] }
                ]
            });
        });
    </script>
</body>
</html>
