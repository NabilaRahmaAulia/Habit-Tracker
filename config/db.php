<?php
$host = "sql205.infinityfree.com"; // MySQL Hostname 
$user = "if0_40408592";            // MySQL Username
$pass = "66H9EGndoRLcjQe";         // MySQL Password
$db   = "if0_40408592_habittracking"; // Database name 
$port = 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>

