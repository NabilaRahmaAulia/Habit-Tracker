<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// pastikan habit milik user
$stmt = $conn->prepare("DELETE h FROM habits h WHERE h.id = ? AND h.user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
header("Location: home.php");
exit;
?>
