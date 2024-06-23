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
    <title>ACLC Library | Member Information</title>
    <link rel="stylesheet" href="css/user_view.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('sidebar.php'); ?>
    <div class="main-content">
        <div class="user-info-wrapper">
            <h2>Member Information</h2>
            <div class="user-info">
                <table>
                    <thead>
                        <tr>
                            <th>Member Image</th>
                            <th>USN</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Year Level</th>
                            <th>Type</th>
                            <th>Gender</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>E-mail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include('config.php');

                        //check if the USN parameter is provided in the URL
                        if (isset($_GET['usn'])) {
                            //sanitize the USN parameter to prevent SQL injection
                            $usn = mysqli_real_escape_string($connect, $_GET['usn']);

                            //query to fetch user details based on USN
                            $usn_query = mysqli_query($connect, "SELECT * FROM members WHERE usn = '$usn'");

                            //check if the query was successful and user exists
                            if ($usn_query && mysqli_num_rows($usn_query) > 0) {
                                $user = mysqli_fetch_assoc($usn_query);
                                echo "<tr>";
                                echo "<td>";
                                if (!empty($user['member_image'])) {
                                    echo "<img src='member-images/" . $user['member_image'] . "' alt='Member Image' style='max-width: 200px; max-height: 200px;'>";
                                } else {
                                    echo "No image available.";
                                }
                                echo "</td>";
                                echo "<td>{$user['usn']}</td>";
                                echo "<td>{$user['name']}</td>";
                                echo "<td>{$user['course']}</td>";
                                echo "<td>{$user['year_level']}</td>";
                                echo "<td>{$user['type']}</td>";
                                echo "<td>{$user['gender']}</td>";
                                echo "<td>{$user['address']}</td>";
                                echo "<td>{$user['contact']}</td>";
                                echo "<td>{$user['email']}</td>";
                                echo "</tr>";
                            } else {
                                echo "User not found.";
                            }
                        } else {
                            echo "Invalid request.";
                        }
                        ?>
                    </tbody>
                </table>
                <a class="back" href="#" onclick="history.go(-1)"><i class='bx bxs-chevron-left'></i>Go back</a>
            </div>
        </div>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>
