<?php 
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Ambil habit dan reminder
$sql = "SELECT h.id AS habit_id, h.habit_name, r.remind_time, r.repeat_days
        FROM habits h
        LEFT JOIN habit_reminders r ON h.id = r.habit_id
        WHERE h.user_id=?
        ORDER BY h.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Jadwal & Reminder Habit</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Navbar -->
<nav class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 text-white flex justify-between items-center shadow-md flex-wrap">
  <h1 class="text-2xl font-bold flex items-center gap-2"><i data-lucide="calendar"></i> Jadwal & Reminder</h1>
  <div class="flex items-center gap-4 mt-2 md:mt-0">
    <a href="home.php" class="hover:scale-110 transition" title="Dashboard"><i data-lucide="home"></i></a>
    <a href="logout.php" class="hover:scale-110 transition" title="Keluar"><i data-lucide="log-out"></i></a>
  </div>
</nav>

<!-- Tabel Jadwal -->
<main class="max-w-4xl mx-auto mt-8 bg-white p-6 rounded-2xl shadow-lg animate-fadeIn">
  <h2 class="text-xl font-semibold mb-4">Daftar Jadwal Habit</h2>
  <table class="min-w-full table-auto border-collapse border border-gray-300">
    <thead>
      <tr class="bg-gray-100">
        <th class="border border-gray-300 px-4 py-2 text-left">Habit</th>
        <th class="border border-gray-300 px-4 py-2 text-left">Waktu Reminder</th>
        <th class="border border-gray-300 px-4 py-2 text-left">Hari</th>
        <th class="border border-gray-300 px-4 py-2">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr class="hover:bg-gray-50">
        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['habit_name']) ?></td>
        <td class="border border-gray-300 px-4 py-2"><?= $row['remind_time'] ?? '-' ?></td>
        <td class="border border-gray-300 px-4 py-2"><?= $row['repeat_days'] ?? '-' ?></td>
        <td class="border border-gray-300 px-4 py-2 text-center">
          <a href="edit_reminder.php?habit_id=<?= $row['habit_id'] ?>" class="text-blue-600 hover:underline">Edit</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</main>

<script>lucide.createIcons();</script>
<style>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn { animation: fadeIn 0.6s ease-in-out; }
</style>
</body>
</html>
