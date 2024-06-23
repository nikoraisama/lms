<link rel="stylesheet" href="css/monitoring.css">
<div class="input-container" >
    <h2 class="monitoring-title">Member Sign-in/Sign-out</h2>
    <form class="monitoring-form" method="POST">
        <input type="text" id="usn" name="usn" placeholder="Enter USN" required>
        <input type="submit" name="in" value="Sign-in">
        <input type="submit" name="out" value="Sign-out">
    </form><br>
</div>
<?php
include('config.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['usn'])) {
        $usn = $_POST['usn'];
        if (isset($_POST['in'])) {
            processIn($usn, $connect);
        } elseif (isset($_POST['out'])) {
            processOut($usn, $connect);
        }    
    }
}
function processIn($usn, $connect) {
    $in_time = date('Y-m-d H:i:s');
    $in_query = mysqli_query($connect, "SELECT id, name, course, year_level FROM members WHERE usn = '$usn'");
    if ($in_query && mysqli_num_rows($in_query) > 0) {
        $row = mysqli_fetch_assoc($in_query);
        $id = $row['id'];
        $name = $row['name'];

        //check if member_report already has an entry for this member_id
        $existing_query = mysqli_query($connect, "SELECT * FROM member_report WHERE member_id = '$id'");
        if ($existing_query) {
            if (mysqli_num_rows($existing_query) > 0) {

                //update sign-in time and status for existing entry
                mysqli_query($connect, "UPDATE member_report SET in_time = '$in_time', status = 'Signed-in' WHERE member_id = '$id'");
            } else {

                //insert new entry for member_id
                mysqli_query($connect, "INSERT INTO member_report (member_id, in_time, status) VALUES ('$id', '$in_time', 'Signed-in')");
            }
            echo '<div style="color: white; font-size: 20px;">';
            echo "Signed in successfully, $name ($usn)";
            echo '</div>';
        } else {
            echo "Error: Invalid USN.";
        }
    }
}
function processOut($usn, $connect) {
    $out_time = date('Y-m-d H:i:s');
    $out_query = mysqli_query($connect, "SELECT id FROM members WHERE usn = '$usn'");
    if ($out_query && mysqli_num_rows($out_query) > 0) {
        $row = mysqli_fetch_assoc($out_query);
        $id = $row['id'];
        
        //update logout time and status in member_report
        mysqli_query($connect, "UPDATE member_report SET out_time = '$out_time', status = 'Signed-out' WHERE member_id = '$id'");
        echo "<div style='color: white; font-size: 20px;'>";
        echo "Signed out successfully.<br>";
        echo "</div>";
    } else {
        echo "Error: Invalid USN.";
    }
}
?>
<h2 class="member-title">Member Information</h2>
<div class="list-container">
    <table>
        <thead>
            <tr>
                <th>USN</th>
                <th>Name</th>
                <th>Course</th>
                <th>Year Level</th>
                <th>Type</th>
                <th>Sign-in Time</th>
                <th>Sign-out Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include('config.php');

            //set the current date in YYYY-MM-DD format
            $currentDate = date('Y-m-d');
                        
            //modify the SQL query to filter out entries from the previous day
            $member_query = mysqli_query($connect, "SELECT m.usn, m.name, m.course, m.year_level, m.type, mr.in_time, mr.out_time, mr.status
                                                    FROM members m
                                                    INNER JOIN member_report mr ON m.id = mr.member_id
                                                    WHERE DATE(mr.in_time) = '$currentDate' OR DATE(mr.out_time) = '$currentDate'
                                                    ORDER BY CASE WHEN mr.status = 'Signed-in' THEN mr.in_time ELSE mr.out_time END DESC");
            while ($row = mysqli_fetch_assoc($member_query)) {
                echo "<tr>";
                echo "<td>{$row['usn']}</td>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['course']}</td>";
                echo "<td>{$row['year_level']}</td>";
                echo "<td>{$row['type']}</td>";
                echo "<td>".date('Y-m-d H:i:s', strtotime($row['in_time']))."</td>"; //format sign-in time with date and time
                echo "<td>";
                if ($row['status'] == 'Signed-out') {
                    echo date('Y-m-d H:i:s', strtotime($row['out_time'])); //format sign-out time with date and time
                } else {
                    echo "-";
                }
                echo "</td>";
                echo "<td>{$row['status']}</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>