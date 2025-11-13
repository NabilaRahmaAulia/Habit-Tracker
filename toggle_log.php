<?php
session_start();
header('Content-Type: application/json');
include 'db.php';
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success'=>false, 'message'=>'Not authenticated']);
  exit;
}
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
  echo json_encode(['success'=>false, 'message'=>'Invalid input']);
  exit;
}
$habit_id = intval($input['habit_id'] ?? 0);
$log_date = $input['log_date'] ?? '';
$status = intval($input['status'] ?? 0);
$user_id = $_SESSION['user_id'];

if (!$habit_id || !$log_date) {
  echo json_encode(['success'=>false, 'message'=>'Missing data']);
  exit;
}

// Pastikan habit milik user
$stmt = $conn->prepare("SELECT id FROM habits WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $habit_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
  echo json_encode(['success'=>false, 'message'=>'Habit not found']);
  exit;
}

if ($status === 1) {
  // insert or update
  $stmt = $conn->prepare("INSERT INTO habit_logs (habit_id, log_date, status) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE status=1");
  $stmt->bind_param("is", $habit_id, $log_date);
  $ok = $stmt->execute();
  echo json_encode(['success' => $ok]);
  exit;
} else {
  // delete or set status 0; kita hapus record agar unik constraint tetap rapih
  $stmt = $conn->prepare("DELETE FROM habit_logs WHERE habit_id = ? AND log_date = ?");
  $stmt->bind_param("is", $habit_id, $log_date);
  $ok = $stmt->execute();
  echo json_encode(['success' => $ok]);
  exit;
}
?>
