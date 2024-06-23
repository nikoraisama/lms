<?php
include('config.php');

$success_message = '';
$error_message = '';

//check if the form is submitted
if (isset($_GET['penalty_id'])) {
    //get the penalty ID from the form
    $penalty_id = $_GET['penalty_id'];

    //select the penalty with status 'Not Paid' corresponding to the penalty_id
    $penalty_query = mysqli_query($connect, "SELECT * FROM member_penalty WHERE id = '$penalty_id' AND penalty_status = 'Not Paid'");
    
    if ($penalty_query && mysqli_num_rows($penalty_query) > 0) {
        //update the penalty status to 'Paid' in the database
        $update_penalty_query = mysqli_query($connect, "UPDATE member_penalty SET penalty_status = 'Paid' WHERE id = '$penalty_id'");

        if ($update_penalty_query) {
            $success_message = "Fine successfully paid.";
            header("Location: borrow_transaction.php?success_message=" . urlencode($success_message));
            exit();
        } else {
            $error_message = "Error updating penalty status: " . mysqli_error($connect);
        }
    } else {
        $error_message = "Penalty ID not found or already paid.";
    }
}

//display success or error messages
if (!empty($success_message)) {
    echo "<p class='success-message'>$success_message</p>";
} elseif (!empty($error_message)) {
    echo "<p class='error-message'>$error_message</p>";
}
?>
