<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Tambah habit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['habit_name'])) {
  $habit = $_POST['habit_name'];
  $stmt = $conn->prepare("INSERT INTO habits (user_id, habit_name) VALUES (?, ?)");
  $stmt->bind_param("is", $user_id, $habit);
  $stmt->execute();
}

// Ambil habit user
$result = $conn->query("SELECT * FROM habits WHERE user_id=$user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Habit Tracker</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 min-h-screen transition-all">

  <!-- Navbar -->
  <nav class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 text-white flex justify-between items-center shadow-md flex-wrap">
    <h1 class="text-2xl font-bold flex items-center gap-2">
      <i data-lucide="book"></i> Habit Tracker
    </h1>
    <div class="flex items-center gap-4 mt-2 md:mt-0">
      <a href="progress.php" title="Lihat grafik" class="hover:scale-110 transition"><i data-lucide="line-chart"></i></a>
      <a href="schedule.php" title="Jadwal & Reminder" class="hover:scale-110 transition"><i data-lucide="calendar"></i></a>
      <a href="profile.php" title="Profil" class="hover:scale-110 transition"><i data-lucide="user"></i></a>
      <a href="logout.php" title="Keluar" class="hover:scale-110 transition"><i data-lucide="log-out"></i></a>
    </div>
  </nav>

  <!-- Konten -->
  <main class="max-w-3xl mx-auto mt-6 md:mt-10 bg-white p-6 md:p-8 rounded-2xl shadow-lg animate-fadeIn">

    <!-- Form tambah habit -->
    <form method="POST" class="flex flex-col sm:flex-row mb-4">
      <input name="habit_name" type="text" placeholder="Tambah kebiasaan baru..." class="flex-grow p-3 border rounded-t-lg sm:rounded-l-lg sm:rounded-tr-none focus:ring focus:ring-blue-200 mb-2 sm:mb-0" required>
      <button type="submit" class="bg-blue-500 text-white px-6 py-3 rounded-b-lg sm:rounded-r-lg sm:rounded-bl-none hover:bg-blue-600 transition">Tambah</button>
    </form>

    <!-- Tombol Export CSV + PDF -->
    <div class="flex justify-end gap-2 mb-4 flex-wrap">
      <a href="export_csv.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition flex items-center gap-2">
        <i data-lucide="download"></i> Export CSV
      </a>
      <button id="exportPDF" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition flex items-center gap-2">
        <i data-lucide="file-text"></i> Export PDF
      </button>
    </div>

    <h2 class="text-xl font-semibold mb-4">Daftar Kebiasaanmu</h2>
    <ul class="space-y-3 md:space-y-4 overflow-x-auto">
      <?php while ($row = $result->fetch_assoc()): ?>
        <li class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-50 p-4 rounded-lg hover:shadow transition-transform hover:scale-[1.01]">
          <div class="flex items-center gap-3 mb-2 sm:mb-0 w-full sm:w-auto flex-wrap">
            <i data-lucide="book-open" class="text-blue-500"></i>
            <span class="truncate"><?= htmlspecialchars($row['habit_name']) ?></span>
          </div>
          <div class="flex items-center gap-3 flex-wrap">
            <a href="log_habit.php?id=<?= $row['id'] ?>" title="Lihat catatan harian" class="text-green-600 hover:scale-110"><i data-lucide="calendar-check"></i></a>
            <a href="edit_habit.php?id=<?= $row['id'] ?>" title="Edit kebiasaan" class="text-blue-600 hover:scale-110"><i data-lucide="edit"></i></a>
            <a href="delete_habit.php?id=<?= $row['id'] ?>" title="Hapus kebiasaan" class="text-red-600 hover:scale-110"><i data-lucide="trash-2"></i></a>
          </div>
        </li>
      <?php endwhile; ?>
    </ul>
  </main>

  <script>lucide.createIcons();</script>

  <!-- Notifikasi Reminder Berdasarkan Jadwal -->
  <script>
if ("Notification" in window) {
  if (Notification.permission !== "granted") Notification.requestPermission();

  const now = new Date();
  const today = now.toLocaleDateString('en-US', { weekday: 'short' }); // Mon, Tue, ...
  const currentTime = now.toTimeString().substr(0,5); // HH:MM

  <?php
  $reminders = $conn->query("SELECT h.habit_name,r.remind_time,r.repeat_days 
                             FROM habits h 
                             JOIN habit_reminders r ON h.id=r.habit_id 
                             WHERE h.user_id=$user_id");
  while($row = $reminders->fetch_assoc()){
    $days = explode(",", $row['repeat_days']);
    $habit = addslashes($row['habit_name']);
    $time = $row['remind_time'];
    echo "if (".json_encode($days).".includes(today) && currentTime == '$time') {\n";
    echo "  new Notification('Habit Reminder!', { body: 'Jangan lupa lakukan habit: $habit', icon: 'https://img.icons8.com/fluency/48/checkmark.png' });\n";
    echo "}\n";
  }
  ?>
}
</script>

<!-- Export PDF Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
document.getElementById('exportPDF').addEventListener('click', ()=>{
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  let y=10;
  doc.setFontSize(14);
  doc.text("Habit Log", 10, y); y+=10;

  <?php
  $stmt=$conn->prepare("SELECT h.habit_name,l.log_date,l.status FROM habits h LEFT JOIN habit_logs l ON h.id=l.habit_id WHERE h.user_id=? ORDER BY l.log_date ASC");
  $stmt->bind_param("i",$user_id); $stmt->execute(); $res=$stmt->get_result();
  while($row=$res->fetch_assoc()){
    $status = $row['status']==1?'Done':'Not Done';
    $habit = addslashes($row['habit_name']); $date=$row['log_date'];
    echo "doc.text('{$habit} - {$date} - {$status}', 10, y); y+=7;\n";
  }
  ?>
  doc.save("habit_log.pdf");
});
</script>

<style>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn { animation: fadeIn 0.6s ease-in-out; }
</style>

</body>
</html>
