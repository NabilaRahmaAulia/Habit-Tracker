<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
$user_id = $_SESSION['user_id'];

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// pastikan habit milik user
$stmt = $conn->prepare("SELECT * FROM habits WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) { header("Location: home.php"); exit; }
$habit = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $new_name = trim($_POST['habit_name']);
  $up = $conn->prepare("UPDATE habits SET habit_name = ? WHERE id = ? AND user_id = ?");
  $up->bind_param("sii", $new_name, $id, $user_id);
  $up->execute();
  header("Location: home.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Edit Habit</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center h-screen">
  <div class="bg-white p-6 rounded-2xl shadow w-96">
    <h1 class="text-xl font-bold mb-4 text-center">Edit Habit</h1>
    <form method="POST" class="space-y-3">
      <input name="habit_name" value="<?= htmlspecialchars($habit['habit_name']) ?>" class="w-full p-2 border rounded" required>
      <div class="flex gap-2">
        <button class="bg-indigo-600 text-white px-4 py-2 rounded w-full">Simpan</button>
        <a href="home.php" class="bg-slate-200 px-4 py-2 rounded w-full text-center">Batal</a>
      </div>
    </form>
  </div>
</body>
</html>
