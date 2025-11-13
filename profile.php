<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$user_id = $_SESSION['user_id'];
$msg = '';

// Update username/password (sama seperti sebelumnya)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['update_username'])) {
    $new = trim($_POST['username']);
    if ($new) {
      $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
      $stmt->bind_param("si",$new,$user_id);
      if($stmt->execute()){ $_SESSION['username']=$new; $msg="Username berhasil diperbarui."; }
      else $msg="Gagal memperbarui username.";
    }
  } elseif(isset($_POST['update_password'])) {
    $old=$_POST['old_password']; $newpw=$_POST['new_password'];
    $stmt=$conn->prepare("SELECT password FROM users WHERE id=?"); $stmt->bind_param("i",$user_id);
    $stmt->execute(); $res=$stmt->get_result()->fetch_assoc();
    if($res && password_verify($old,$res['password'])){
      $hash=password_hash($newpw,PASSWORD_BCRYPT);
      $up=$conn->prepare("UPDATE users SET password=? WHERE id=?"); $up->bind_param("si",$hash,$user_id);
      if($up->execute()) $msg="Password berhasil diubah."; else $msg="Gagal mengubah password.";
    } else { $msg="Password lama tidak cocok."; }
  }
}

// Statistik total habit & done
$total_stmt=$conn->prepare("SELECT COUNT(*) as total FROM habits WHERE user_id=?");
$total_stmt->bind_param("i",$user_id); $total_stmt->execute(); $total=$total_stmt->get_result()->fetch_assoc()['total'];

$done_stmt=$conn->prepare("SELECT COUNT(*) as done FROM habit_logs hl JOIN habits h ON hl.habit_id=h.id WHERE h.user_id=? AND hl.status=1");
$done_stmt->bind_param("i",$user_id); $done_stmt->execute(); $done=$done_stmt->get_result()->fetch_assoc()['done'];

$percent=$total>0?round(($done/$total)*100,1):0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil - Habit Tracker</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 text-white flex justify-between items-center shadow-md">
  <h1 class="text-2xl font-bold flex items-center gap-2"><i data-lucide="user"></i> Profil</h1>
  <a href="home.php" title="Dashboard" class="hover:scale-110 transition"><i data-lucide="home"></i></a>
</nav>

<main class="max-w-3xl mx-auto mt-10 space-y-6">

  <div class="bg-white p-6 rounded-2xl shadow-lg animate-fadeIn">
    <h2 class="text-lg font-semibold mb-4">Statistik</h2>
    <div class="flex justify-between items-center">
      <div>Total Kebiasaan: <span class="font-bold"><?= $total ?></span></div>
      <div>Done: <span class="font-bold"><?= $done ?></span> (<span class="font-bold"><?= $percent ?>%</span>)</div>
    </div>
  </div>

  <div class="bg-white p-6 rounded-2xl shadow-lg animate-fadeIn">
    <h2 class="text-lg font-semibold mb-3">Profil</h2>
    <?php if($msg) echo "<div class='mb-3 p-3 bg-green-50 text-green-700 rounded'>$msg</div>"; ?>
    <form method="POST" class="space-y-4">
      <div>
        <label class="text-sm block mb-1">Username</label>
        <input name="username" value="<?= htmlspecialchars($_SESSION['username']) ?>" class="w-full p-2 border rounded">
      </div>
      <div><button name="update_username" class="bg-indigo-600 text-white px-4 py-2 rounded">Simpan Username</button></div>
    </form>
  </div>

  <div class="bg-white p-6 rounded-2xl shadow-lg animate-fadeIn">
    <h2 class="text-lg font-semibold mb-3">Ubah Password</h2>
    <form method="POST" class="space-y-3">
      <input type="password" name="old_password" placeholder="Password lama" class="w-full p-2 border rounded" required>
      <input type="password" name="new_password" placeholder="Password baru" class="w-full p-2 border rounded" required>
      <button name="update_password" class="bg-green-600 text-white px-4 py-2 rounded">Ubah Password</button>
    </form>
  </div>

</main>

<script>lucide.createIcons();</script>
<style>
@keyframes fadeIn { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:translateY(0);} }
.animate-fadeIn { animation: fadeIn 0.6s ease-in-out; }
</style>
</body>
</html>
