<?php
include 'db.php';

if ($conn) {
  echo "<h3 style='color:green;'>Koneksi ke database berhasil!</h3>";
  echo "Server info: " . $conn->server_info;
} else {
  echo "<h3 style='color:red;'>Koneksi gagal!</h3>";
}
?>
