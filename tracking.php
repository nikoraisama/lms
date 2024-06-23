<link rel="stylesheet" href="css/tracking.css">
<div class="b-search-container">
    <h2 class="tracking-title">Search Books</h2>
    <form class="tracking-form" method="GET">
        <input type="text" name="search" placeholder="Enter keywords">
        <input type="submit" value="Search">
    </form><br>
    <?php
    include('config.php');
    if (isset($_GET['search'])) {
        $search_query = $_GET['search'];
        $search_query = mysqli_real_escape_string($connect, $search_query);
        $search_result = mysqli_query($connect, "SELECT b.*, c.category_name, GROUP_CONCAT(a.author_fullname SEPARATOR ', ') AS authors 
                                                FROM books b
                                                INNER JOIN category c ON b.category_id = c.id
                                                LEFT JOIN books_author ba ON b.id = ba.book_id
                                                LEFT JOIN author a ON ba.author_id = a.id
                                                WHERE b.title LIKE '%$search_query%' 
                                                OR a.author_fullname LIKE '%$search_query%' 
                                                OR b.isbn LIKE '%$search_query%'
                                                OR c.category_name LIKE '%$search_query%'
                                                OR b.status LIKE '%$search_query%'
                                                OR b.copies LIKE '%$search_query%'
                                                OR b.remarks LIKE '%$search_query%'
                                                GROUP BY b.id");
        if ($search_result) {
            if (mysqli_num_rows($search_result) > 0) {
                echo "<h2 id='search-results-title'>Search Results</h2>";
                echo "<div id='search-results' class=b-list1-container>";
                echo "<table border='1'>";
                echo "<tr><th>Title</th><th>Authors</th><th>ISBN</th><th>Category</th><th>Status</th><th>Copies</th><th>Remarks</th></tr>";
                while ($row = mysqli_fetch_assoc($search_result)) {
                    echo "<tr><td>{$row['title']}</td><td>{$row['authors']}</td><td>{$row['isbn']}</td><td>{$row['category_name']}</td><td>{$row['status']}</td><td>{$row['copies']}</td><td>{$row['remarks']}</td></tr>";
                }
                echo "</table><br>";
                echo "</div>";
                echo "<script>";
                echo "setTimeout(function() {";
                echo "document.getElementById('search-results-title').innerHTML = '';";
                echo "document.getElementById('search-results').innerHTML = '';";
                echo "}, 300000);";
                echo "</script>";
            } else {
                echo '<div style="color: white; font-size: 20px;">';
                echo "Book not found.";
                echo '</div>';
            }
        } else {
            echo "Error: " . mysqli_error($connect);
        }
    }
    ?>
</div>
<h2>Book List</h2>
<div class="b-list-container">
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Authors</th>
            <th>ISBN</th>
            <th>Category</th>
            <th>Status</th>
            <th>Copies</th>
            <th>Remarks</th>
        </tr>
        <?php
        include('config.php');
        $books_list = mysqli_query($connect,"SELECT b.*, c.category_name, GROUP_CONCAT(a.author_fullname SEPARATOR ', ') AS authors 
                                            FROM books b 
                                            INNER JOIN category c ON b.category_id = c.id
                                            LEFT JOIN books_author ba ON b.id = ba.book_id
                                            LEFT JOIN author a ON ba.author_id = a.id
                                            GROUP BY b.id");
        while ($row = mysqli_fetch_array($books_list)){
            echo "<tr>";
            echo "<td>" . $row['title'] . "</td>";
            echo "<td>" . $row['authors'] . "</td>";
            echo "<td>" . $row['isbn'] . "</td>";
            echo "<td>" . $row['category_name'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>" . $row['copies'] . "</td>";
            echo "<td>" . $row['remarks'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>