<?php
require 'phpmailer/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

include('config.php');

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //sanitize and validate email input
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    } else {
        $mail = new PHPMailer(TRUE);

        $mail->isSMTP();
        $mail->SMTPAuth = TRUE;

        //configure your SMTP settings
        $mail->Host = 'smtp.gmail.com'; //your SMTP server address
        $mail->Username = 'nick.evo25@gmail.com'; //SMTP username
        $mail->Password = 'weqg pkad rrxi bdiz'; //SMTP password
        $mail->SMTPSecure = 'tls'; //enable TLS encryption
        $mail->Port = 587; //SMTP port (use 587 for TLS/STARTTLS or 465 for SSL)

        //check if the email exists in your admins table and fetch the corresponding admin_id
        $sql_check_email = mysqli_query($connect, "SELECT id FROM admins WHERE email = '$email'");
        if ($sql_check_email && mysqli_num_rows($sql_check_email) > 0) {
            $row = mysqli_fetch_assoc($sql_check_email);
            $admin_id = $row['id'];

            //generate a unique token for password reset
            $token = bin2hex(random_bytes(32));

            //store the token, admin_id, and timestamp in the password_reset table
            $sql_insert_reset = mysqli_query($connect, "INSERT INTO password_reset (admin_id, email, token, created_at) VALUES ('$admin_id', '$email', '$token', NOW())");
            if ($sql_insert_reset) {
                //send an email to the user with a link to reset password
                $reset_link = "http://localhost/library/password_reset.php?token=$token";
                $email_body = '
                <html>
                <head>
                <style>
                    body {
                        color: #333;
                        font-family:Arial, Helvetica, sans-serif
                    }
                    .container {
                        padding: 20px;
                        max-width: 600px;
                        margin: 0 auto;
                        border-radius: 8px;
            
                    }
                    .p1{
                        font-size: 23px;
                    }
                    footer {
                        text-align: center;
                        font-size: 12px;
                        color: #333;
                    }
                    .dev {
                        color: #333;
                        text-decoration: none;
                        margin-top: -10px;
                    }
                    .dev:hover {
                        color: #007bff;
                    }
                </style>
                </head>
                <body>
                    <div class="container">
                        <p class="p1">Hey there!</p>
                        <p>Someone requested to change your password. If this is you, click the link below to reset your password.</p>
                        <a href="'.$reset_link.'" class="button">Click here to reset your password.</a>
                    </div>
                </body>
                <footer>
                    <div>
                        <br><br>
                        <p>&copy; ACLC College of Tacloban - Library</p>
                    </div>
                </footer>
                </html>';

                $mail->setFrom('nick.evo25@gmail.com', 'Niko (ACLC Library-Developer)'); //your email and name
                $mail->addAddress($email); //recipient email

                $mail->isHTML(true);
                $mail->Subject = "Password Reset Request";
                $mail->Body = $email_body;

                if ($mail->send()) {
                    $success_message = "Password reset link sent to your email.";
                } else {
                    $error_message = "Failed to send reset link. Please try again later.";
                }
            } else {
                $error_message = "Error inserting reset data: " . mysqli_error($connect);
            }
        } else {
            $error_message = "Email not found. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png" type="image/icon type">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>ACLC Library | Forgot Password</title>
    <link rel="stylesheet" href="css/password_forgot.css">
</head>
<body>
    <div class="container">
        <div class="container-title">
            <img src="images/lock.png">
            <h2>Oops! Forgot Password?</h2>
            <p>Enter your email and we'll send you a link <br> to reset your password</p>
        </div>
        <?php
        if (!empty($error_message)) {
            echo "<p class='error-message'>$error_message</p>";
        }
        if (!empty($success_message)) {
            echo "<p class='success-message'>$success_message</p>";
        }
        ?>
        <form method="POST">
            <label for="email"></label>
            <input class="email-input" type="email" id="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="submit"><i class='bx bx-mail-send'></i>Send me the link</button>
        </form>
        <a href="admin_login.php" class="return-home">Return to login</a>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>
