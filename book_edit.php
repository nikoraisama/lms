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
    <title>ACLC Library | Edit Book</title>
    <link rel="stylesheet" href="css/book_edit.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('sidebar.php'); ?> 
    <div class="main-content">
        <?php
        include('config.php');
        if (isset($_GET['id'])) {

            $book_id = mysqli_real_escape_string($connect, $_GET['id']);

            //query to fetch book details based on ID
            $book_query = mysqli_query($connect, "SELECT b.*, c.category_name FROM books b 
                                                INNER JOIN category c ON b.category_id = c.id 
                                                WHERE b.id = '$book_id'");

            //check if the query was successful and book exists
            if ($book_query && mysqli_num_rows($book_query) > 0) {
                $book = mysqli_fetch_assoc($book_query);
            ?>
            <fieldset>
                <legend>Edit Book Information</legend>
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
                <!--Book edit form-->
                <form method='POST' action='book_update.php' enctype="multipart/form-data">
                    <input type='hidden' name='book_id' value='<?php echo $book['id']; ?>'>
                    <div class="form">
                        <div class="form1">
                            <label for="">Title</label><br>
                            <input type='text' name='title' value='<?php echo $book['title']; ?>'><br>
                            <label for="">Author 1</label><br>
                            <input type='text' name='author1' value='<?php
                                $author_query = mysqli_query($connect, "SELECT a.author_fullname FROM author a
                                                                        INNER JOIN books_author ba ON a.id = ba.author_id
                                                                        WHERE ba.book_id = '$book_id' LIMIT 1");
                                $author_row = mysqli_fetch_assoc($author_query);
                                echo isset($author_row['author_fullname']) ? $author_row['author_fullname'] : '';
                            ?>'><br>

                            <label for="">Author 2</label><br>
                            <input type='text' name='author2' value='<?php
                                $author_query = mysqli_query($connect, "SELECT a.author_fullname FROM author a
                                                                        INNER JOIN books_author ba ON a.id = ba.author_id
                                                                        WHERE ba.book_id = '$book_id' LIMIT 1,1");
                                $author_row = mysqli_fetch_assoc($author_query);
                                echo isset($author_row['author_fullname']) ? $author_row['author_fullname'] : '';
                            ?>'><br>

                            <label for="">Author 3</label><br>
                            <input type='text' name='author3' value='<?php
                                $author_query = mysqli_query($connect, "SELECT a.author_fullname FROM author a
                                                                        INNER JOIN books_author ba ON a.id = ba.author_id
                                                                        WHERE ba.book_id = '$book_id' LIMIT 2,1");
                                $author_row = mysqli_fetch_assoc($author_query);
                                echo isset($author_row['author_fullname']) ? $author_row['author_fullname'] : '';
                            ?>'><br>
                            <label for="">Published Year</label><br>
                            <input type='text' name='publish_year' value='<?php echo $book['publish_year']; ?>'><br>
                        </div>
                        <div class="form2">
                            <label for="">ISBN</label><br>
                            <input type='text' name='isbn' value='<?php echo $book['isbn']; ?>'><br>
                            <label for="">Category</label><br>
                            <input type='text' name='category' value='<?php echo $book['category_name']; ?>'><br>
                            <label for="">Status</label><br>
                            <select name="status" required>
                                <option value="New" <?php if ($book['status'] == 'New') echo 'selected'; ?>>New</option>
                                <option value="Old" <?php if ($book['status'] == 'Old') echo 'selected'; ?>>Old</option>
                                <option value="Lost" <?php if ($book['status'] == 'Lost') echo 'selected'; ?>>Lost</option>
                                <option value="Damaged" <?php if ($book['status'] == 'Damaged') echo 'selected'; ?>>Damaged</option>
                                <option value="Replacement" <?php if ($book['status'] == 'Replacement') echo 'selected'; ?>>Replacement</option>
                                <option value="Hardbound" <?php if ($book['status'] == 'Hardbound') echo 'selected'; ?>>Hardbound</option>
                            </select><br>
                            <label for="">Copies</label><br>
                            <input type='text' name='copies' value='<?php echo $book['copies']; ?>'><br>
                            <label for="">Remarks</label><br>
                            <select name="remarks">
                                <option value="Available" <?php if ($book['remarks'] == 'Available') echo 'selected'; ?>>Available</option>
                                <option value="Not Available" <?php if ($book['remarks'] == 'Not Available') echo 'selected'; ?>>Not Available</option>
                            </select><br>

                        </div>
                    </div>
                    <!-- Image upload -->
                    <div>
                        <label for="file">Upload Image</label><br>
                        <input type="file" name="file" accept="image/*">
                    </div>
                    <div class="button-container">
                        <button type='submit'><i class='bx bx-check'></i>Update</button>
                        <button type="button" class="cancel-button" onclick="window.location.href='directory_book.php'">Cancel</button>
                    </div>
                </form>
            </fieldset> 
            <?php
            } else {
                echo "Book not found.";
            }
        } else {
            echo "Invalid request.";
        }
        ?>
    </div>
    <?php include('footer.php');?>
</body>
</html>
