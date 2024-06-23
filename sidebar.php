<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//fetch admin firstname in session
$firstname = $_SESSION['admin_firstname'];
$admin_image = $_SESSION['admin_image'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png" type="image/icon type">
    <title>Library Management System</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <div class="top">
            <div class="logo">
                <img class="sidebar-logo" src="images/logo.png" alt="ACLC logo">
                <span>ACLC Tacloban Library</span>
            </div>
            <i class='bx bx-menu' id="btn" ></i>
        </div>
        <div class="user">
            <a href="admin_dashboard.php">
                <?php
                if (!empty($admin_image)) {
                    echo "<img class='admin-img' src='admin-images/" . $admin_image . "' alt='Admin Image'>";
                } else {
                    echo "No image available.";
                }
                ?>
            </a>
            <div>
                <p class="bold" >Welcome!</p>
                <p class="bold2" >Admin, <?php echo "$firstname"?></p>
            </div>
        </div>
        <hr class="hr-line">
        <ul>
            <li>
                <a href="admin_dashboard.php">
                    <i class="bx bxs-dashboard"></i>
                    <span class="nav-item">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="directory_member.php">
                    <i class='bx bxs-user-circle'></i>
                    <span class="nav-item">Members</span>
                </a>
            </li>
            <li>
                <a href="directory_book.php">
                    <i class='bx bxs-book'></i>
                    <span class="nav-item">Books</span>
                </a>
            </li>
            <hr class="hr-line">
            <li>
                <a href="borrow.php">
                    <i class='bx bxs-edit'></i>
                    <span class="nav-item">Borrow</span>
                </a>
            </li>
            <li>
                <a href="borrow_list.php">
                    <i class='bx bxs-book-content'></i>
                    <span class="nav-item">Borrow List</span>
                </a>
            </li>
            <li>
                <a href="return_list.php">
                    <i class='bx bx-book-content' ></i>
                    <span class="nav-item">Return List</span>
                </a>
            </li>
            <hr class="hr-line">
            <li>
                <a href="admin_settings.php">
                    <i class='bx bxs-cog'></i>
                    <span class="nav-item">Settings</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class='bx bx-log-out-circle'></i>
                    <span class="nav-item">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</body>
<script>
    let btn = document.querySelector('.bx-menu');
    let sidebar = document.querySelector('.sidebar');

    btn.onclick = function () {
        sidebar.classList.toggle('active');
    };
</script>
</html>