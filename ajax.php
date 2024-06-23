<?php
include('config.php');

//for monitoring table data
$currentDate = date('Y-m-d');
$monitoring_query = mysqli_query($connect, "SELECT m.usn, m.name, m.course, m.year_level, m.type, mr.in_time, mr.out_time, mr.status
                            FROM members m INNER JOIN member_report mr ON m.id = mr.member_id
                            WHERE DATE(mr.in_time) = '$currentDate' OR DATE(mr.out_time) = '$currentDate'
                            ORDER BY CASE WHEN mr.status = 'Signed-in' THEN mr.in_time ELSE mr.out_time END DESC");
$data = array();
if ($monitoring_query && mysqli_num_rows($monitoring_query) > 0) {
    while ($row = mysqli_fetch_assoc($monitoring_query)) {
        $data[] = $row;
    }
}

//for occupant count
$count_query = mysqli_query($connect, "SELECT COUNT(*) AS total FROM member_report WHERE DATE(in_time) = '$currentDate' OR DATE(out_time) = '$currentDate'");
$count_data = mysqli_fetch_assoc($count_query);

echo json_encode(['data' => $data, 'count' => $count_data['total']]);
?>
