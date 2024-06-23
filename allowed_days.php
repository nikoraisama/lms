<link rel="stylesheet" href="css/allowed_days.css">
<?php
include('config.php');

$days_query = mysqli_query($connect, "SELECT * FROM allowed_days ORDER BY id DESC");
while ($row = mysqli_fetch_assoc($days_query)) {
    $days_id = $row['id'];
    $allowed_days = $row['allowed_days'];

    echo "<div class='days-table'>";
    echo "<h2>Book Borrowing Duration</h2>";

    //check if a days update was performed and display success/error message accordingly
    if (isset($_POST['days_id']) && isset($_POST['new_days'])) {
        $days_id = $_POST['days_id'];
        $new_days = $_POST['new_days'];

        //update the allowed days in the database
        $update_query = mysqli_query($connect, "UPDATE allowed_days SET allowed_days = '$new_days' WHERE id = '$days_id'");
        if ($update_query) {
            $success_message = "Borrow duration updated successfully.";
            echo "<script>window.location.href = 'admin_settings.php?success_message=" . urlencode($success_message) . "';</script>";
            echo "<p class='success-message'>$success_message</p>";
            exit();
        } else {
            $error_message = "Error updating allowable days.";
            echo "<p class='error-message'>$error_message</p>";
        }
    }

    echo "<table>";
    echo "<tr><th>Day/Days</th><th>Action</th></tr>";
    echo "<tr>";
    echo "<td>$allowed_days</td>";
    echo "<td>";
    echo "<form method='POST'>";
    echo "<input type='hidden' name='days_id' value='$days_id'>";
    echo "<input type='number' name='new_days' value='$allowed_days' required>";
    echo "<input type='submit' value='Update'>";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>";
}
?>
