<?php
session_start(); //start the session
session_destroy(); //destroy the session data

//redirect to index.php after logout
echo "<script>window.location='admin_login.php';</script>";
exit();
?>