<?php
date_default_timezone_set('Asia/Manila');

//create connection
$connect = mysqli_connect('localhost','root','','library');

//check connection
if (!$connect) {
    echo "Connection failed";
}
?>