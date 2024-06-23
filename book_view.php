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
    <title>ACLC Library | Book Information</title>
    <link rel="stylesheet" href="css/book_view.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('sidebar.php'); ?>
    <div class="main-content">
        <div class="book-info-wrapper">
            <h2>Book Information</h2>
            <div class="book-info">
                <table>
                    <tr>
                        <th>Book Image</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Published Year</th>
                        <th>ISBN</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Copies</th>
                        <th>Remarks</th>
                    </tr>
                    <?php
                    include('config.php');

                    if (isset($_GET['id'])) {
                        $book_id = mysqli_real_escape_string($connect, $_GET['id']);
                        $book_query = mysqli_query($connect, "SELECT b.*, c.category_name FROM books b 
                                                              INNER JOIN category c ON b.category_id = c.id 
                                                              WHERE b.id = '$book_id'");
                        if ($book_query && mysqli_num_rows($book_query) > 0) {
                            $book = mysqli_fetch_assoc($book_query);
                            echo "<tr>";
                            echo "<td>";
                            if (!empty($book['book_image'])) {
                                echo "<img src='book-images/" . $book['book_image'] . "' alt='Book Image' style='max-width: 200px; max-height: 200px;'>";
                            } else {
                                echo "No image available.";
                            }
                            echo "</td>";
                            echo "<td>{$book['title']}</td>";
                            echo "<td>";
                            $author_query = mysqli_query($connect, "SELECT a.author_fullname FROM author a
                                                                INNER JOIN books_author ba ON a.id = ba.author_id
                                                                WHERE ba.book_id = '$book_id'");
                            $authors = [];
                            while ($author_row = mysqli_fetch_assoc($author_query)) {
                                $authors[] = $author_row['author_fullname'];
                            }
                            echo implode(', ', $authors);
                            echo "</td>";
                            echo "<td>{$book['publish_year']}</td>";
                            echo "<td>{$book['isbn']}</td>";
                            echo "<td>{$book['category_name']}</td>";
                            echo "<td>{$book['status']}</td>";
                            echo "<td>{$book['copies']}</td>";
                            echo "<td>{$book['remarks']}</td>";
                            echo "</tr>";
                        } else {
                            echo "<tr><td colspan='9'>Book not found.</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>Invalid request.</td></tr>";
                    }
                    ?>
                </table>
                <a class="back" href="#" onclick="history.go(-1)"><i class='bx bxs-chevron-left'></i>Go back</a>
            </div>
        </div>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>

