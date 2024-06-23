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
    <title>ACLC Library | Admin Information</title>
    <link rel="stylesheet" href="css/admin_view.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('sidebar.php'); ?>
    <div class="main-content">
        <div class="admin-info-wrapper">
            <h2>Admin Information</h2>
            <div class="admin-info">
                <table>
                    <tr>
                        <th>Admin Image</th>
                        <th>Username</th>
                        <th>Firstname</th>
                        <th>Middlename</th>
                        <th>Lastname</th>
                        <th>Email</th>
                    </tr>
                    <?php
                    include('config.php');

                    if (isset($_GET['id'])) {
                        $admin_id = mysqli_real_escape_string($connect, $_GET['id']);
                        $admin_query = mysqli_query($connect, "SELECT * FROM admins WHERE id = '$admin_id'");

                        if ($admin_query && mysqli_num_rows($admin_query) > 0) {
                            $admin = mysqli_fetch_assoc($admin_query);
                            echo "<tr>";
                            echo "<td>";
                            if (!empty($admin['admin_image'])) {
                                echo "<img src='admin-images/" . $admin['admin_image'] . "' alt='Admin Image' style='max-width: 200px; max-height: 200px;'>";
                            } else {
                                echo "No image available.";
                            }
                            echo "</td>";
                            echo "<td>{$admin['username']}</td>";
                            echo "<td>{$admin['firstname']}</td>";
                            echo "<td>{$admin['middlename']}</td>";
                            echo "<td>{$admin['lastname']}</td>";
                            echo "<td>{$admin['email']}</td>";
                            echo "</tr>";
                        } else {
                            echo "<tr><td colspan='6'>Admin not found.</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>Invalid request.</td></tr>";
                    }
                    ?>
                </table>
                <a class="back" href="#"onclick="history.go(-1)"><i class='bx bxs-chevron-left'></i>Go back</a>
            </div>
        </div>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>