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
    <title>ACLC Library | Borrow A Book</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/borrow.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('sidebar.php'); ?>
    <div class="main-content">
        <?php
        include('config.php');

        $error_message = '';

        //check if the form is submitted
        if (isset($_POST['usn'])) {
            $usn = $_POST['usn'];

            //fetch data from 'members' table to check if the USN exists
            $usn_query = mysqli_query($connect, "SELECT id, name FROM members WHERE usn = '$usn'");
            if ($usn_query && mysqli_num_rows($usn_query) > 0) {
                $member_data = mysqli_fetch_assoc($usn_query);
                $member_id = $member_data['id'];
                $name = $member_data['name'];

                //set session variables for the member
                $_SESSION['member_usn'] = $usn;
                $_SESSION['member_id'] = $member_id;

                //redirect to the borrow_transaction.php page
                echo "<script>window.location='borrow_transaction.php';</script>";
                exit();
            } else {
                $error_message = "Invalid USN. Please try again.";
            }
        } 
        ?>
        <div class="form-container">
            <img src="images/banners.jpg" class="banner">
            <h1>Book Borrowing Section</h1>
            <?php 
            if (!empty($error_message)) {
                echo "<p class='error-message'>$error_message</p>";
            }
            ?>
            <form id="qrForm" method="POST">
                <input type="text" id="usn" name="usn" placeholder="Enter Member USN" required>
                <button type="submit" name="submit"><i class='bx bx-check'></i> Submit</button>
                <button class="qr-btn" type="button" onclick="scanQRCode()"><i class='bx bx-qr-scan'></i> Scan QR Code</button>
            </form>
        </div>

        <!-- QR Code Scanner Modal -->
        <div id="qrModal" class="modal">
            <div class="scanner-area"></div>
                <video id="preview"></video>
                <div class="scan-buttons">
                    <button onclick="closeModal()">Close</button>
                </div>
            <div class="powered-by">Powered by ACLC Library</div>
        </div>
    </div>
    <?php include('footer.php'); ?>
    <!-- QR Code Scanner JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/3.3.3/adapter.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>
    <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script>
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
        scanner.addListener('scan', function (content) {
            document.getElementById('usn').value = content; //set the scanned content as the USN
            document.getElementById('qrModal').style.display = 'none'; //hide the QR code scanner modal
        });

        Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]); //start scanning using the first available camera
            } else {
                console.error('No cameras found.');
            }
        }).catch(function (error) {
            console.error(error);
        });

        function scanQRCode() {
            document.getElementById('qrModal').classList.add('active'); //show the QR code scanner modal
        }

        function closeModal() {
            document.getElementById('qrModal').classList.remove('active'); //close the modal
        }   
    </script>
</body>
</html>