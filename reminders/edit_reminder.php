<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Ambil habit_id dari URL
if (!isset($_GET['habit_id'])) {
    die("Habit ID tidak ditemukan.");
}
$habit_id = intval($_GET['habit_id']);

// Ambil info habit
$stmt = $conn->prepare("SELECT habit_name FROM habits WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $habit_id, $user_id);
$stmt->execute();
$habit_res = $stmt->get_result();
if ($habit_res->num_rows == 0) die("Habit tidak ditemukan.");
$habit = $habit_res->fetch_assoc()['habit_name'];

// Ambil reminder jika ada
$stmt2 = $conn->prepare("SELECT remind_time, repeat_days FROM habit_reminders WHERE habit_id=?");
$stmt2->bind_param("i",$habit_id);
$stmt2->execute();
$reminder_res = $stmt2->get_result();
$remind_time = "";
$repeat_days = [];
if ($reminder_res->num_rows > 0) {
    $row = $reminder_res->fetch_assoc();
    $remind_time = $row['remind_time'];
    $repeat_days = explode(",", $row['repeat_days']);
}

// Proses simpan form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $time = $_POST['remind_time'];
    $days = isset($_POST['repeat_days']) ? implode(",", $_POST['repeat_days']) : '';
    
    if ($reminder_res->num_rows > 0) {
        // update
        $stmt = $conn->prepare("UPDATE habit_reminders SET remind_time=?, repeat_days=? WHERE habit_id=?");
        $stmt->bind_param("ssi", $time, $days, $habit_id);
    } else {
        // insert
        $stmt = $conn->prepare("INSERT INTO habit_reminders (habit_id, remind_time, repeat_days) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $habit_id, $time, $days);
    }
    $stmt->execute();
    header("Location: home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Reminder Habit</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<main class="bg-white p-6 rounded-2xl shadow-lg w-full max-w-md">
  <h2 class="text-xl font-semibold mb-4 flex items-center gap-2"><i data-lucide="clock"></i> Edit Reminder</h2>
  <form method="POST" class="space-y-4">
    <div>
      <label class="block font-medium mb-1">Habit</label>
      <input type="text" value="<?= htmlspecialchars($habit) ?>" disabled class="w-full p-2 border rounded">
    </div>
    <div>
      <label class="block font-medium mb-1">Waktu Reminder</label>
      <input type="time" name="remind_time" value="<?= htmlspecialchars($remind_time) ?>" required class="w-full p-2 border rounded">
    </div>
    <div>
      <label class="block font-medium mb-1">Hari Pengulangan</label>
      <div class="flex flex-wrap gap-2">
        <?php
        $all_days = ['Mon'=>'Senin','Tue'=>'Selasa','Wed'=>'Rabu','Thu'=>'Kamis','Fri'=>'Jumat','Sat'=>'Sabtu','Sun'=>'Minggu'];
        foreach($all_days as $key=>$label): ?>
          <label class="flex items-center gap-1">
            <input type="checkbox" name="repeat_days[]" value="<?= $key ?>" <?= in_array($key,$repeat_days)?'checked':'' ?> class="w-4 h-4">
            <span class="text-gray-700"><?= $label ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition w-full">Simpan</button>
  </form>
</main>

<script>lucide.createIcons();</script>
</body>
</html>
