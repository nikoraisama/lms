<?php
session_start();
//check if admin is not in session redirect to admin_login. Making sure you cannot access page unless admin is logged-in
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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>ACLC Library | Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <?php
    include('sidebar.php');
    include('config.php');
    ?>
    <div class="main-content">
        <h1>Library Management System</h1>
        <div class="time-container">
            <h2>Current Date and Time: <span id="current-time"></span></h2>
            <script>
                setInterval(() => {
                    let now = new Date();
                    let year = now.getFullYear().toString();
                    let month = (now.getMonth() + 1).toString().padStart(2, '0'); //months are zero-based, so add 1
                    let day = now.getDate().toString().padStart(2, '0');
                    let hours = now.getHours().toString().padStart(2, '0');
                    let minutes = now.getMinutes().toString().padStart(2, '0');
                    let seconds = now.getSeconds().toString().padStart(2, '0');
                    let currentDateTime = `${month}/${day}/${year} ${hours}:${minutes}:${seconds}`;
                    document.getElementById('current-time').textContent = currentDateTime;
                }, 1000);
            </script>
        </div>
        <div class="list-container">
            <ul class="horizontal-list">
                <li>
                    <?php
                    $result = mysqli_query($connect,"SELECT * FROM books");
                    $rows = mysqli_num_rows($result);
                    ?>
                    <a href="directory_book.php">
                        <span class="count_top"><i class='bx bxs-book'></i> Books</span>
                    </a>
                    <div class="count"><?php echo $rows; ?></div>
                    <span class="count_bottom ">Total Books</span> 
                </li>
                <div class = "vertical"></div>
                <li>
                    <?php
                    
                    //use current date of sign-in and sign-out to filter
                    $currentDate = date('Y-m-d');
                    $result = mysqli_query($connect, "SELECT * FROM member_report WHERE DATE(in_time) = '$currentDate' OR DATE(out_time) = '$currentDate'");
                    $rows = mysqli_num_rows($result);
                    ?>
                    <a href="directory_member.php">
                        <span class="count_top"><i class='bx bxs-user-check'></i> Occupants</span>
                    </a>
                    <div class="count occupant-count"><?php echo $rows; ?></div>
                    <span class="count_bottom ">Total Library Occupants</span> 
                </li>
                <div class = "vertical"></div>
                <li>
                    <?php
                    $result = mysqli_query($connect,"SELECT * FROM borrowed_books");
                    $rows = mysqli_num_rows($result);
                    ?>
                    <a href="borrow_list.php">
                        <span class="count_top"><i class='bx bxs-book-open'></i> Borrowed Books</span>
                    </a>
                    <div class="count"><?php echo $rows; ?></div>
                    <span class="count_bottom ">Total Books Borrowed</span> 
                </li>
                <div class = "vertical"></div>
                <li>
                    <?php
                    $result = mysqli_query($connect,"SELECT * FROM returned_books");
                    $rows = mysqli_num_rows($result);
                    ?>
                    <a href="return_list.php">
                        <span class="count_top"><i class='bx bxs-book-open'></i> Returned Books</span>
                    </a>
                    <div class="count"><?php echo $rows; ?></div>
                    <span class="count_bottom ">Total Books Returned</span> 
                </li>
            </ul>
        </div>
        <div class="container">
            <!--admin list table-->
            <div class="adminlist-container">
            <?php
                $result = mysqli_query($connect, "SELECT id, firstname, lastname FROM admins");
                if ($result && mysqli_num_rows($result)) {
                    echo "<h1>Admins</h1>";
                    //get success message from URL parameter
                    $success_message = isset($_GET['success_message']) ? $_GET['success_message'] : '';
                    //get error message from URL parameter
                    $error_message = isset($_GET['error_message']) ? $_GET['error_message'] : '';

                    if (!empty($success_message)) {
                        //display success message if not empty
                        echo "<p class='success-message'>$success_message</p>";
                    }

                    if (!empty($error_message)) {
                        //display error message if not empty
                        echo "<p class='error-message'>$error_message</p>";
                    }
                    echo "<div class='admin-table-wrapper'>";
                    echo "<div class='admin-table'>";
                    echo "<table>";
                    echo "<tr><th>Firstname</th><th>Lastname</th><th>Action</th></tr>";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>{$row['firstname']}</td>";
                        echo "<td>{$row['lastname']}</td>";
                        echo "<td>
                                <button class='button-view' onclick=\"window.location.href='admin_view.php?id={$row['id']}'\"><i class='bx bx-info-circle'></i></button>
                                <button class='button-edit' onclick=\"window.location.href='admin_edit.php?id={$row['id']}'\"><i class='bx bx-edit'></i></button>
                                <button class='button-del' onclick=\"if (confirm('Are you sure you want to delete this admin?')) { window.location.href='admin_delete.php?id={$row['id']}'; }\"><i class='bx bx-trash'></i></button>
                            </td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</div>";
                    echo "</div>";
                } else {
                    echo "No admins found.";
                }
                ?>
            <button class="add-admin" onclick="window.location.href = 'add_admin.php'"><i class='bx bx-plus'></i> Add New Admin</button>
            </div>
            <!--occupancy report table-->
            <div id="content" class="monitoring-container">
                <?php
                    $result = mysqli_query($connect, "SELECT m.usn, m.name, m.course, m.year_level, m.type, mr.in_time, mr.out_time, mr.status
                                                    FROM members m INNER JOIN member_report mr ON m.id = mr.member_id
                                                    ORDER BY CASE WHEN mr.status = 'Signed-in' THEN mr.in_time ELSE mr.out_time END DESC");

                    echo "<h1>Occupancy Report</h1>";
                    echo "<div class='occupancy-table-wrapper'>";
                    echo "<div class='occupancy-table'>";
                    echo "<table id='monitoring-table'>";
                    echo "<tr><th>USN</th><th>Name</th><th>Course</th><th>Year Level</th><th>Type</th><th>Sign-in Time</th><th>Sign-out Time</th><th>Status</th></tr>";

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>{$row['usn']}</td>";
                            echo "<td>{$row['name']}</td>";
                            echo "<td>{$row['course']}</td>";
                            echo "<td>{$row['year_level']}</td>";
                            echo "<td>{$row['type']}</td>";
                            echo "<td>{$row['in_time']}</td>";
                            echo "<td>";
                            if ($row['status'] == 'Signed-out') {
                                echo date('Y-m-d H:i:s', strtotime($row['out_time']));
                            } else {
                                echo "-";
                            }
                            echo "</td>";
                            echo "<td>{$row['status']}</td>";
                            echo "</tr>"; 
                        }
                    } else {
                        echo "<tr><td colspan='8' style='text-align: center;'>No data available for the current date.</td></tr>";
                    }

                    echo "</table>";
                    echo "</div>";
                    echo "</div>";
                ?>
                <div class="save-button-container">
                    <button id="saveMemberDataBtn"><i class='bx bxs-download'></i>Save Occupancy Data</button>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
                <script src="js/json.js"></script>
                <script>

                    //add event listener for the button click
                    document.getElementById('saveMemberDataBtn').addEventListener('click', () => {

                        //generate Excel file with member data
                        let wb = XLSX.utils.book_new();
                        let ws = XLSX.utils.table_to_sheet(document.querySelector('.monitoring-container table'));
                        XLSX.utils.book_append_sheet(wb, ws, 'Member Data');
                        XLSX.writeFile(wb, 'member_data_' + new Date().toISOString().slice(0, 10) + '.xlsx'); //include date in the filename
                    });
                </script>   
            </div>
        </div>
    </div>
    <?php include('footer.php');?>
</body>
</html>