<?php
$servername = "localhost";
$username = "root";        // default user XAMPP
$password = "";            // default kosong
$database = "habit_tracking";
$port = 3307;              // tambahkan ini

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}
?>
