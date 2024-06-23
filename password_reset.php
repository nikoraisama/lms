<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png" type="image/icon type">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/password_reset.css">
    <title>ACLC Library | Reset Password</title>
</head>
<body>
    <?php
    include('config.php');

    $token = '';
    $error_message = '';

    if (isset($_GET['token'])) {
        $token = mysqli_real_escape_string($connect, $_GET['token']);

        //validate the token from the database
        $token_query = mysqli_query($connect, "SELECT * FROM password_reset WHERE token = '$token'");

        if ($token_query && mysqli_num_rows($token_query) > 0) {
            //token is valid, display password reset form
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $password = mysqli_real_escape_string($connect, $_POST["password"]);
                $confirm_password = mysqli_real_escape_string($connect, $_POST["confirm_password"]);

                if ($password !== $confirm_password) {
                    $error_message = "Passwords do not match.";
                } else {
                    $row = mysqli_fetch_assoc($token_query);
                    $email = $row["email"];

                    //hash the password before storing it
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    $sql_update = mysqli_query($connect, "UPDATE admins SET password = '$hashed_password' WHERE email = '$email'");
                    if ($sql_update) {
                        //delete the used token from the password_reset table
                        $sql_delete = mysqli_query($connect, "DELETE FROM password_reset WHERE token = '$token'");
                        if ($sql_delete) {
                            echo "<script>alert('Password reset successfully.'); window.location.href = 'admin_login.php';</script>";
                            exit;
                        } else {
                            $error_message = "Error deleting token: " . mysqli_error($connect);
                        }
                    } else {
                        $error_message = "Error updating password: " . mysqli_error($connect);
                    }
                }
            }
        } else {
            $error_message = "Invalid reset link.";
        }
    } else {
        $error_message = "Invalid reset link.";
    }
    ?>
    <!--display the form with any error messages-->
    <div class='container'>
        <div class='container-title'>
            <img src='images/reset.png' alt='Reset Password'>
            <h2>Reset Password</h2>
            <p>Enter your new password below <br> to change your password</p>
        </div>
        <?php
        if ($error_message != '') {
            echo "<p class='error-message'>$error_message</p>";
        }
        ?>
        <form method='POST'>
            <label for='password'></label>
            <input class='pass-input' type='password' id='password' name='password' placeholder='New password' required>

            <label for='confirm_password'></label>
            <input class='pass-input' type='password' id='confirm_password' name='confirm_password' placeholder='Confirm new password' required>

            <input type='hidden' name='token' value='<?php echo $token; ?>'>

            <button type='submit' name='submit'><i class='bx bx-reset'></i>Reset my password</button>
        </form>
        <a href='admin_login.php' class='return-home'>Return to login</a>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>