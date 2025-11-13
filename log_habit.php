<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$user_id = $_SESSION['user_id'];
$habit_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Pastikan habit milik user
$stmt = $conn->prepare("SELECT * FROM habits WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $habit_id, $user_id);
$stmt->execute();
$habit = $stmt->get_result()->fetch_assoc();
if (!$habit) { header("Location: home.php"); exit; }

// tanggal default = 7 hari terakhir
$days = [];
for ($i=6; $i>=0; $i--) {
    $days[] = date('Y-m-d', strtotime("-$i days"));
}

// Ambil logs
$logs_stmt = $conn->prepare("SELECT log_date, status FROM habit_logs WHERE habit_id = ? AND log_date BETWEEN ? AND ?");
$logs_stmt->bind_param("iss", $habit_id, $days[0], $days[6]);
$logs_stmt->execute();
$res = $logs_stmt->get_result();
$logs = [];
while ($r = $res->fetch_assoc()) $logs[$r['log_date']] = $r['status'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Log Harian - <?= htmlspecialchars($habit['habit_name']) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Navbar -->
<nav class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 text-white flex justify-between items-center shadow-md">
  <h1 class="text-2xl font-bold flex items-center gap-2"><i data-lucide="clipboard-list"></i> <?= htmlspecialchars($habit['habit_name']) ?></h1>
  <a href="home.php" class="hover:scale-110 transition" title="Kembali"><i data-lucide="arrow-left"></i></a>
</nav>

<main class="max-w-2xl mx-auto mt-10 bg-white p-8 rounded-2xl shadow-lg animate-fadeIn">
  <h2 class="text-lg font-semibold mb-4">Checklist 7 Hari Terakhir</h2>
  <ul class="space-y-3">
    <?php foreach ($days as $d): ?>
      <li class="flex items-center justify-between bg-gray-50 p-3 rounded-lg hover:shadow transition-transform hover:scale-[1.01]">
        <span><?= date('D, d M', strtotime($d)) ?></span>
        <label class="inline-flex items-center gap-2 cursor-pointer">
          <input type="checkbox" class="log-toggle h-5 w-5" data-date="<?= $d ?>" <?= isset($logs[$d]) && $logs[$d]==1 ? 'checked' : '' ?>>
          <span><?= isset($logs[$d]) && $logs[$d]==1 ? 'Done' : 'Mark' ?></span>
        </label>
      </li>
    <?php endforeach; ?>
  </ul>
</main>

<script>
lucide.createIcons();

document.querySelectorAll('.log-toggle').forEach(chk => {
  chk.addEventListener('change', function(){
    const date = this.dataset.date;
    const status = this.checked ? 1 : 0;
    const habitId = <?= $habit_id ?>;

    fetch('toggle_log.php', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body: JSON.stringify({habit_id: habitId, log_date: date, status: status})
    }).then(r=>r.json()).then(res=>{
      if(!res.success){ alert('Gagal update log'); this.checked=!this.checked; }
    }).catch(()=>{ alert('Network error'); this.checked=!this.checked; });
  });
});
</script>

<style>
@keyframes fadeIn { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:translateY(0);} }
.animate-fadeIn { animation: fadeIn 0.6s ease-in-out; }
</style>
</body>
</html>
