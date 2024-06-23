<link rel="stylesheet" href="css/penalty.css">
<?php
include('config.php');

$penalty_query = mysqli_query($connect, "SELECT * FROM penalty ORDER BY id DESC");
while ($row = mysqli_fetch_assoc($penalty_query)) {
    $penalty_id = $row['id'];
    $penalty_amount = $row['penalty_amount'];
    $decimal_amount = number_format($penalty_amount, 2);

    echo "<div class='penalty-table'>";
    echo "<h2>Book Overdue Fine</h2>";
    //check if a penalty update was performed and display success/error message accordingly
    if (isset($_POST['penalty_id']) && isset($_POST['new_amount'])) {
        $penalty_id = $_POST['penalty_id'];
        $new_amount = $_POST['new_amount'];

        //update the penalty amount in the database
        $update_query = mysqli_query($connect, "UPDATE penalty SET penalty_amount = '$new_amount' WHERE id = '$penalty_id'");
        if ($update_query) {
            $success_message = "Overdue fine updated successfully.";
            echo "<script>window.location.href = 'admin_settings.php?success_message=" . urlencode($success_message) . "';</script>";
            exit();
        } else {
            $error_message = "Error updating penalty amount.";
        }
    }

    echo "<table>";
    echo "<tr><th>Amount (Php)</th><th>Action</th></tr>";
    echo "<tr>";
    echo "<td>$decimal_amount</td>";
    echo "<td>";
    echo "<form method='POST'>";
    echo "<input type='hidden' name='penalty_id' value='$penalty_id'>";
    echo "<input type='number' name='new_amount' value='$penalty_amount' required>";
    echo "<input type='submit' value='Update'>";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>";
}
?>
