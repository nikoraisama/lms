<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png" type="image/icon type">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/admin_login.css">
    <title>ACLC Library | Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <form class="login" method="POST">
            <div class="login-title">
                <img src="images/logo.png">
                <h1>Admin Login</h1>
            </div>
            <?php
            include('config.php');

            $error_message = '';

            //process admin login if 'login' is posted
            if (isset($_POST['login'])) {
                //escape special characters in username
                $username = mysqli_real_escape_string($connect, $_POST['username']);
                $password = mysqli_real_escape_string($connect, $_POST['password']);
                
                //query the database to check if the username exists
                $login_query = mysqli_query($connect, "SELECT id, username, password, firstname, admin_image FROM admins WHERE username = '$username'");
                
                //check if any rows were returned
                if (mysqli_num_rows($login_query) > 0) {
                    $login_success = false;
                    
                    //iterate through each row in the result set
                    while ($row = mysqli_fetch_assoc($login_query)) {
                        //use password_verify to check hashed password
                        if (password_verify($password, $row['password'])) {
                            session_start();
                            //set session variables for admin ID and admin Firstname
                            $_SESSION['admin_id'] = $row['id'];
                            $_SESSION['admin_firstname'] = $row['firstname'];
                            $_SESSION['admin_image'] = $row['admin_image'];
                            echo "<script>window.location='admin_dashboard.php';</script>";
                            $login_success = true;
                            exit();
                        }
                    }
                    
                    //if no valid password found after checking all records
                    if (!$login_success) {
                        $error_message = "Wrong username or password. Try again.";
                    }
                } else {
                    $error_message = "No user found with the provided username.";
                }
            }
            ?>
            <?php 
            if (!empty($error_message)) {
                echo "<p class='error-message'>$error_message</p>";
            }
            ?>
            <label for="username"></label>
            <input class="login-input" type="text" id="username" name="username" placeholder="Username" required><br><br>

            <label for="password"></label>
            <input class="login-input" type="password" id="password" name="password" placeholder="Password" required><br><br>    
                  
            <button type="submit" name="login"><i class='bx bx-check'></i>Login</button>
        </form>
        <a href="index.php" class="return-home">Return to home</a>
        <a href="password_forgot.php" class="forgot">Forgot password?</a>
    </div>
    <footer>
        <?php include('footer.php');?>
    </footer>
</body>
</html>