<?php
session_start();

//check if the user is logged in, then destroy the session
if (isset($_SESSION['usn'])) {
    session_unset(); //unset all session variables
    session_destroy(); //destroy the session
}

//redirect the user to back to borrow.php
echo "<script>window.location='borrow.php';</script>";
exit(); //stop further execution
?>