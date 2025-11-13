<?php
session_start();
include 'db.php';
if(!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="habit_log.csv"');

$output = fopen('php://output','w');
fputcsv($output, ['Habit', 'Tanggal', 'Status']);

// ambil data
$sql = "SELECT h.habit_name, l.log_date, l.status FROM habits h 
        LEFT JOIN habit_logs l ON h.id=l.habit_id
        WHERE h.user_id=? ORDER BY l.log_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$user_id);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()){
    fputcsv($output, [$row['habit_name'], $row['log_date'], $row['status']]);
}
fclose($output);
exit;
