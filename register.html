<?php
include 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

  $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $username, $password);

  if ($stmt->execute()) {
    header("Location: index.php");
    exit;
  } else {
    $error = "Gagal register, mungkin username sudah dipakai!";
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Register - Habit Tracker</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-100 to-emerald-100 h-screen flex items-center justify-center">
  <div class="bg-white p-8 rounded-2xl shadow-lg w-96">
    <h1 class="text-2xl font-bold text-center mb-6">Daftar Habit Tracker</h1>
    <?php if (!empty($error)) echo "<p class='text-red-500 text-center mb-3'>$error</p>"; ?>
    <form method="POST" class="space-y-3">
      <input name="username" type="text" placeholder="Username" class="w-full p-3 border rounded-lg" required>
      <input name="password" type="password" placeholder="Password" class="w-full p-3 border rounded-lg" required>
      <button type="submit" class="bg-green-600 text-white w-full p-3 rounded-lg hover:bg-green-700">Daftar</button>
    </form>
    <p class="text-center text-sm mt-4">Sudah punya akun? <a href="index.php" class="text-green-600 font-medium">Login</a></p>
  </div>
</body>
</html>
