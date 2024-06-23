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
    <title>ACLC Library | Book Registration</title>
    <link rel="stylesheet" href="css/add_book.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('sidebar.php'); ?>
    <div class="main-content">
    <?php

        include('config.php');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //initialize error and success message variables
            $error_message = '';
            $success_message = '';

            //retrieve form data and sanitize
            $title = mysqli_real_escape_string($connect, $_POST['title']);
            $authors = $_POST['author']; // Array of authors
            $publish_year = mysqli_real_escape_string($connect, $_POST['publish_year']);
            $isbn = mysqli_real_escape_string($connect, $_POST['isbn']);
            $category = mysqli_real_escape_string($connect, $_POST['category']);
            $status = mysqli_real_escape_string($connect, $_POST['status']);
            $copies = mysqli_real_escape_string($connect, $_POST['copies']);
            $remarks = mysqli_real_escape_string($connect, $_POST['remarks']);

            //handle image upload
            $file_name = $_FILES['file']['name'];
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_error = $_FILES['file']['error'];

            if ($file_error === UPLOAD_ERR_OK) {
                $file_size = $_FILES['file']['size'];
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

                if ($file_size <= 5242880) { // 5MB limit
                    $allowed_extensions = ['jpg', 'jpeg', 'png'];
                    if (in_array(strtolower($file_extension), $allowed_extensions)) {
                        $new_file_name = uniqid('book_image_') . '.' . $file_extension;
                        $upload_path = 'book-images/' . $new_file_name;
                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            //insert the book details including the image filename into the database
                            $insert_category_query = "INSERT INTO category (category_name) VALUES ('$category')";
                            if (mysqli_query($connect, $insert_category_query)) {
                                $category_id = mysqli_insert_id($connect);
                            } else {
                                $error_message = "Unable to add category.";
                                exit;
                            }

                            $insert_book_query = "INSERT INTO books (title, publish_year, isbn, category_id, status, copies, remarks, book_image) 
                                                VALUES ('$title', '$publish_year', '$isbn', '$category_id', '$status', '$copies', '$remarks', '$new_file_name')";
                            if (mysqli_query($connect, $insert_book_query)) {
                                $book_id = mysqli_insert_id($connect);

                                //insert authors into the 'author' table if they don't exist and get their IDs
                                $author_ids = [];
                                foreach ($authors as $author_name) {
                                    $author_name = mysqli_real_escape_string($connect, $author_name);

                                    //check if the author name is not empty
                                    if (!empty($author_name)) {
                                        $insert_author_query = "INSERT INTO author (author_fullname) VALUES ('$author_name')";
                                        if (mysqli_query($connect, $insert_author_query)) {
                                            $author_id = mysqli_insert_id($connect);
                                        } else {
                                            $error_message = "Unable to add author: $author_name.";
                                            continue; //skip this author and proceed with the next one
                                        }
                                        $author_ids[] = $author_id;
                                    }
                                }

                                //insert book_id and author_id into 'books_author' table for each author
                                foreach ($author_ids as $author_id) {
                                    $insert_books_author_query = "INSERT INTO books_author (book_id, author_id) VALUES ('$book_id', '$author_id')";
                                    if (!mysqli_query($connect, $insert_books_author_query)) {
                                        $error_message = "Unable to add book-author association.";
                                    }
                                }

                                $success_message = "Book added successfully.";
                                header("Location: add_book.php?success_message=" . urlencode($success_message));
                                exit();
                            } else {
                                $error_message = "Unable to add book.";
                            }
                        } else {
                            $error_message = "Error moving uploaded file.";
                        }
                    } else {
                        $error_message = "Only JPG, JPEG, and PNG files are allowed.";
                    }
                } else {
                    $error_message = "File size exceeds the limit.";
                }
            } else {
                $error_message = "Error uploading file.";
            }

            // Redirect with success or error message
            if (isset($success_message)) {
                header("Location: add_book.php?success_message=" . urlencode($success_message));
                exit();
            } elseif (isset($error_message)) {
                header("Location: add_book.php?error_message=" . urlencode($error_message));
                exit();
            }
        }
    ?>

        <!-- Add book form -->
        <fieldset style="width: 500px;">
            <legend>Book Registration</legend>
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
            <form method="POST" enctype="multipart/form-data">
                <div class="form">
                    <div class="form1">
                        <input type="text" name="title" placeholder="Enter Book Title" required><br>
                        <input type="text" name="author[]" placeholder="Enter Author 1" required><br>
                        <input type="text" name="author[]" placeholder="Enter Author 2"><br>
                        <input type="text" name="author[]" placeholder="Enter Author 3"><br>
                        <input type="text" name="publish_year" placeholder="Enter Published Year" required><br>
                    </div>
                    <div class="form2">
                        <input type="text" name="isbn" placeholder="Enter Book ISBN" required><br>
                        <select name="category" required>
                            <option value="No Category">-- Select Category --</option>
                            <option value="000-Computer Science, Information & General Works">000-Computer Science, Information & General Works</option>
                            <option value="001-Knowledge">001-Knowledge</option>
                            <option value="002-The Book">002-The Book</option>
                            <option value="003-Systems">003-Systems</option>
                            <option value="004-Computer Science">004-Computer Science</option>
                            <option value="005-Computer Programming, Program & Data">005-Computer Programming, Program & Data</option>
                            <option value="006-Special Computer Methods">006-Special Computer Methods</option>
                            <option value="100-Philosophy & Psychology">100-Philosophy & Psychology</option>
                            <option value="110-Metaphysics">110-Metaphysics</option>
                            <option value="120-Epistemology">120-Epistemology</option>
                            <option value="130-Parapsychology">130-Parapsychology</option>
                            <option value="140-Philosophical Schools of Thought">140-Philosophical Schools of Thought</option>
                            <option value="150-Psychology">150-Psychology</option>
                            <option value="160-Philosophical Logic">160-Philosophical Logic</option>
                            <option value="170-Ethics">170-Ethics</option>
                            <option value="180-Ancient, Medieval & Eastern Philosophy">180-Ancient, Medieval & Eastern Philosophy</option>
                            <option value="190-Modern Western Philosophy">190-Modern Western Philosophy</option>
                            <option value="200-Religion">200-Religion</option>
                            <option value="210-Philosophy & Theory">210-Philosophy & Theory</option>
                            <option value="220-The Bible">220-The Bible</option>
                            <option value="230-Christianity">230-Christianity</option>
                            <option value="240-Christian Practice & Observance">240-Christian Practice & Observance</option>
                            <option value="250-Christian Pastoral Practice & Observance">250-Christian Pastoral Practice & Observance</option>
                            <option value="260-Christian Organization, Social Work & Worship">260-Christian Organization, Social Work & Worship</option>
                            <option value="270-History of Christianity">270-History of Christianity</option>
                            <option value="280-Christian Denominations">280-Christian Denominations</option>
                            <option value="290-Other Religions">290-Other Religions</option>
                            <option value="300-Social Science, Sociology & Anthropology">300-Social Science, Sociology & Anthropology</option>
                            <option value="310-Statistics">310-Statistics</option>
                            <option value="320-Political Science">320-Political Science</option>
                            <option value="330-Economics">330-Economics</option>
                            <option value="340-Law">340-Law</option>
                            <option value="350-Public Administration & Military Science">350-Public Administration & Military Science</option>
                            <option value="360-Social Problems & Social Services">360-Social Problems & Social Services</option>
                            <option value="370-Education">370-Education</option>
                            <option value="380-Commerce, Communications & Transportation">380-Commerce, Communications & Transportation</option>
                            <option value="390-Customs, Etiquette & Folklore">390-Customs, Etiquette & Folklore</option>
                            <option value="400-Language">400-Language</option>
                            <option value="410-Linguistics">410-Linguistics</option>
                            <option value="420-English & Old English Languages">420-English & Old English Languages</option>
                            <option value="430-Germanic Languages">430-Germanic Languages</option>
                            <option value="440-Romance Languages">440-Romance Languages</option>
                            <option value="450-Italian, Romanian Languages">450-Italian, Romanian Languages</option>
                            <option value="460-Spanish & Portuguese Languages">460-Spanish & Portuguese Languages</option>
                            <option value="470-Latin & Italic Languages">470-Latin & Italic Languages</option>
                            <option value="480-Hellenic Languages">480-Hellenic Languages</option>
                            <option value="490-Other Languages">490-Other Languages</option>
                            <option value="500-Science">500-Science</option>
                            <option value="510-Mathematics">510-Mathematics</option>
                            <option value="520-Astronomy">520-Astronomy</option>
                            <option value="530-Physics">530-Physics</option>
                            <option value="540-Chemistry">540-Chemistry</option>
                            <option value="550-Earth Sciences & Geology">550-Earth Sciences & Geology</option>
                            <option value="560-Fossils & Prehistoric Life">560-Fossils & Prehistoric Life</option>
                            <option value="570-Life Sciences">570-Life Sciences</option>
                            <option value="580-Plants (Botany)">580-Plants (Botany)</option>
                            <option value="590-Animals (Zoology)">590-Animals (Zoology)</option>
                            <option value="600-Technology">600-Technology</option>
                            <option value="610-Medical Sciences">610-Medical Sciences</option>
                            <option value="620-Engineering & Applied Operations">620-Engineering & Applied Operations</option>
                            <option value="630-Agriculture">630-Agriculture</option>
                            <option value="640-Home & Family Management">640-Home & Family Management</option>
                            <option value="650-Management & Public Relations">650-Management & Public Relations</option>
                            <option value="660-Chemical Engineering">660-Chemical Engineering</option>
                            <option value="670-Manufacturing">670-Manufacturing</option>
                            <option value="680-Manufacture for Specific Uses">680-Manufacture for Specific Uses</option>
                            <option value="690-Buildings">690-Buildings</option>
                            <option value="700-Arts">700-Arts</option>
                            <option value="710-Civic & Landscape Art">710-Civic & Landscape Art</option>
                            <option value="720-Architecture">720-Architecture</option>
                            <option value="730-Plastic Arts, Sculpture">730-Plastic Arts, Sculpture</option>
                            <option value="740-Drawing & Decorative Arts">740-Drawing & Decorative Arts</option>
                            <option value="750-Painting">750-Painting</option>
                            <option value="760-Graphic Arts, Printmaking">760-Graphic Arts, Printmaking</option>
                            <option value="770-Photography, Computer Art">770-Photography, Computer Art</option>
                            <option value="780-Music">780-Music</option>
                            <option value="790-Recreational & Performing Arts">790-Recreational & Performing Arts</option>
                            <option value="800-Literature, Rhetoric & Criticism">800-Literature, Rhetoric & Criticism</option>
                            <option value="810-American Literature">810-American Literature</option>
                            <option value="820-English & Old English Literatures">820-English & Old English Literatures</option>
                            <option value="830-Germanic Literatures">830-Germanic Literatures</option>
                            <option value="840-Romance Literatures">840-Romance Literatures</option>
                            <option value="850-Italian, Romanian Literatures">850-Italian, Romanian Literatures</option>
                            <option value="860-Spanish & Portuguese Literatures">860-Spanish & Portuguese Literatures</option>
                            <option value="870-Latin & Italic Literatures">870-Latin & Italic Literatures</option>
                            <option value="880-Hellenic Literatures">880-Hellenic Literatures</option>
                            <option value="890-Other Literatures">890-Other Literatures</option>
                            <option value="900-History">900-History</option>
                            <option value="910-Geography & Travel">910-Geography & Travel</option>
                            <option value="920-Biography, Genealogy, Insignia">920-Biography, Genealogy, Insignia</option>
                            <option value="930-History of Ancient World">930-History of Ancient World</option>
                            <option value="940-History of Europe">940-History of Europe</option>
                            <option value="950-History of Asia">950-History of Asia</option>
                            <option value="960-History of Africa">960-History of Africa</option>
                            <option value="970-History of North America">970-History of North America</option>
                            <option value="980-History of South America">980-History of South America</option>
                            <option value="990-History of Other Areas">990-History of Other Areas</option>
                        </select><br>
                        <select name="status" required>
                            <option value="No Status">-- Select Status --</option>
                            <option value="New">New</option>
                            <option value="Old">Old</option>
                            <option value="Lost">Lost</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Replacement">Replacement</option>
                            <option value="Hardbound">Hardbound</option>
                        </select><br>
                        <input type="number" name="copies" min="1" placeholder="Enter Book Copies" required><br>
                        <select name="remarks" required>
                            <option value="No Remarks">-- Select Remarks --</option>
                            <option value="Available">Available</option>
                            <option value="Not Available">Not Available</option>
                        </select><br>
                    </div>
                </div>
                <!-- Image upload -->
                <div>
                    <label for="file">Upload Image</label><br>
                    <input type="file" name="file" accept="image/*">
                </div>
                <div class="button-container">
                    <button type="submit" name="addBook"><i class='bx bx-plus'></i> Add Book</button>
                    <button type="button" class="cancel-button" onclick="window.location.href='directory_book.php'">Cancel</button>
                </div>
            </form>
        </fieldset><br>
    </div>
    <?php include('footer.php');?>
</body>
</html>