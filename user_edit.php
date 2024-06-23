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
    <title>ACLC Library | Edit Member</title>
    <link rel="stylesheet" href="css/user_edit.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('sidebar.php'); ?> 
    <div class="main-content">
        <?php
        include('config.php');
        if (isset($_GET['usn'])) {

            $usn = mysqli_real_escape_string($connect, $_GET['usn']);

            $member_query = mysqli_query($connect, "SELECT * FROM members WHERE usn = '$usn'");

            if ($member_query && mysqli_num_rows($member_query) > 0) {
                $user = mysqli_fetch_assoc($member_query);

            ?>
            <fieldset>
                <legend>Edit Member Information</legend>
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
                <!-- Member edit form -->
                <form method='POST' action='user_update.php' enctype="multipart/form-data">
                    <input type='hidden' name='usn' value='<?php echo $user['usn']; ?>'>
                    <div class="form">
                        <div class="form1">
                            <label for="">Name</label><br>
                            <input type='text' name='name' value='<?php echo $user['name']; ?>'><br>
                            <label for="">Course</label><br>
                            <select name="course">
                                <option value="BSIT" <?php if ($user['course'] == 'BSIT') echo 'selected'; ?>>BSIT</option>
                                <option value="BSCS" <?php if ($user['course'] == 'BSCS') echo 'selected'; ?>>BSCS</option>
                                <option value="WAD" <?php if ($user['course'] == 'WAD') echo 'selected'; ?>>WAD</option>
                                <option value="PN" <?php if ($user['course'] == 'PN') echo 'selected'; ?>>PN</option>
                                <option value="BSHM" <?php if ($user['course'] == 'BSHM') echo 'selected'; ?>>BSHM</option>
                                <option value="BSBA" <?php if ($user['course'] == 'BSBA') echo 'selected'; ?>>BSBA</option>
                                <option value="BSA" <?php if ($user['course'] == 'BSA') echo 'selected'; ?>>BSA</option>
                                <option value="OMT" <?php if ($user['course'] == 'OMT') echo 'selected'; ?>>OMT</option>
                                <option value="OAT" <?php if ($user['course'] == 'OAT') echo 'selected'; ?>>OAT</option>
                                <option value="HRT" <?php if ($user['course'] == 'HRT') echo 'selected'; ?>>HRT</option>
                            </select><br>
                            <label for="">Year Level</label><br>
                            <select name="year_level">
                                <option value="1st Year" <?php if ($user['year_level'] == '1st Year') echo 'selected'; ?>>1st Year</option>
                                <option value="2nd Year" <?php if ($user['year_level'] == '2nd Year') echo 'selected'; ?>>2nd Year</option>
                                <option value="3rd Year" <?php if ($user['year_level'] == '3rd Year') echo 'selected'; ?>>3rd Year</option>
                                <option value="4th Year" <?php if ($user['year_level'] == '4th Year') echo 'selected'; ?>>4th Year</option>
                                <option value="5th Year" <?php if ($user['year_level'] == '5th Year') echo 'selected'; ?>>5th Year</option>
                            </select><br>
                            <label for="">Type</label><br>
                            <select name="type">
                                <option value="SH" <?php if ($user['type'] == 'SH') echo 'selected'; ?>>SH</option>
                                <option value="College" <?php if ($user['type'] == 'College') echo 'selected'; ?>>College</option>
                                <option value="Faculty" <?php if ($user['type'] == 'Faculty') echo 'selected'; ?>>Faculty</option>
                                <option value="Staff" <?php if ($user['type'] == 'Staff') echo 'selected'; ?>>Staff</option>
                            </select><br>
                        </div>
                        <div class="form2">
                            <label for="">Contact</label><br>
                            <input type='text' name='contact' value='<?php echo $user['contact']; ?>'><br>
                            <label for="">Email</label><br>
                            <input type='email' name='email' value='<?php echo $user['email']; ?>'><br>
                            <label for="">Gender</label><br>
                            <select name="gender">
                                <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                            </select><br>
                            <label for="">Email</label><br>
                            <input type='text' name='address' value='<?php echo $user['address']; ?>'><br>
                        </div>
                    </div>
                    <!-- Image upload -->
                    <div>
                        <label for="file">Upload Image</label><br>
                        <input type="file" name="file" accept="image/*">
                    </div>
                    <div class="button-container">
                        <button type='submit'><i class='bx bx-check'></i>Update</button>
                        <button type="button" class="cancel-button" onclick="window.location.href='directory_member.php'">Cancel</button>
                    </div>
                </form>
                <?php
                    } else {
                        echo "User not found.";
                    }
                } else {
                    echo "Invalid request.";
                }
                ?>
            </fieldset>
    </div>
    <?php include('footer.php');?>
</body>
</html>