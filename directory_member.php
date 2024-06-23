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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>ACLC Library | Member Directory</title>
    <link rel="stylesheet" href="css/directory_member.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <?php include('sidebar.php'); ?>
    <div class="main-content">
        <div class="qrcode-container" id="qrcodeContainer" style="display: none;">
            <h2>Library Member QR Code</h2><br>
            <div id="qrcode"></div>
        </div>  
        <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
        <script>
            function generateQRCode(usn) {
                if (usn.trim() !== '') {
                    const qrCodeDiv = document.getElementById('qrcode');
                    qrCodeDiv.innerHTML = '';
                    
                    new QRCode(qrCodeDiv, {
                        text: usn,  
                        width: 200,
                        height: 200
                    });
                    
                    const qrCodeContainer = document.getElementById('qrcodeContainer');
                    qrCodeContainer.style.display = 'block';
                } else {
                    alert('Not a valid USN.');
                }
            }
            function printMemberList() {
                var printContents = document.querySelector('.m-directory-table').innerHTML;
                var originalContents = document.body.innerHTML;

                document.body.innerHTML = printContents;

                window.print();

                document.body.innerHTML = originalContents;
                location.reload();
            }
        </script>
        <div class="directory-top">
            <h1>Members Directory</h1>
            <?php
            //get success message from URL parameter
            $success_message = isset($_GET['success_message']) ? $_GET['success_message'] : '';
            if (!empty($success_message)) {
                //display success message if not empty
                echo "<p class='success-message'>$success_message</p>";
            }

            //get error message from URL parameter
            $error_message = isset($_GET['error_message']) ? $_GET['error_message'] : '';
            if (!empty($error_message)) {
                //display error message if not empty
                echo "<p class='error-message'>$error_message</p>";
            }
            ?>
        <div class="top-button">
            <button class="add" onclick="window.location.href = 'add_member.php'"><i class='bx bx-plus'></i>Add Member</button>
            <button class="print" onclick="printMemberList()"><i class='bx bx-printer'></i>Print Member List</button>
        </div>
        </div>
        <div class='m-directory-table-wrapper'>
            <div class='m-directory-table'>
                <table id='booksTable'>
                    <thead>
                        <tr>
                            <th>Member Image</th>
                            <th>USN</th>
                            <th>Member Fullname</th>
                            <th>Course</th>
                            <th>Level</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include('config.php');

                        $search_member = mysqli_query($connect, "SELECT * FROM members ORDER BY id DESC ");

                        if ($search_member && mysqli_num_rows($search_member) > 0) {
                            while ($row = mysqli_fetch_assoc($search_member)) {
                                echo "<tr>";
                                echo "<td>";
                                if (!empty($row['member_image'])) {
                                    echo "<img src='member-images/" . $row['member_image'] . "' alt='Book Image' style='max-width: 50px; max-height: 50px;'>";
                                } else {
                                    echo "No image available.";
                                }
                                echo "</td>";
                                echo "<td>{$row['usn']}</td>";
                                echo "<td>{$row['name']}</td>";
                                echo "<td>{$row['course']}</td>";
                                echo "<td>{$row['type']}</td>";
                                echo "<td>{$row['contact']}</td>";
                                echo "<td>
                                        <button class='button-qr' name='Generate QR' onclick=\"generateQRCode('{$row['usn']}')\"><i class='bx bx-qr'></i></button>
                                        <button class='button-user' onclick=\"window.location.href='user_view.php?usn={$row['usn']}'\"><i class='bx bx-info-circle'></i></button>
                                        <button class='button-edit' onclick=\"window.location.href='user_edit.php?usn={$row['usn']}'\"><i class='bx bx-edit'></i></button>
                                        <button class='button-del' onclick=\"if (confirm('Are you sure you want to delete this member?')) { window.location.href='user_delete.php?usn={$row['usn']}'; }\"><i class='bx bx-trash'></i></button>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No Member exists.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>                
        </div>
    </div>                   
    <?php include('footer.php');?>
    <script>
        $(document).ready(function() {
            $('#booksTable').DataTable({
                "searching": true,
                "paging": true,
                "info": true,
                "lengthMenu": [10, 25, 50, 100],
                "pageLength": 10,
                "columnDefs": [
                    { "orderable": false, "targets": [5] }
                ]
            });
        });
    </script>
</body>
</html>
