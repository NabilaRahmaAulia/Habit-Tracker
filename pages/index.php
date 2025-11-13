<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  $sql = "SELECT * FROM users WHERE username = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      header("Location: home.php");
      exit;
    } else {
      $error = "Password salah!";
    }
  } else {
    $error = "User tidak ditemukan!";
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Login - Habit Tracker</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-sky-100 to-indigo-100 h-screen flex items-center justify-center">
  <div class="bg-white p-8 rounded-2xl shadow-lg w-96">
    <h1 class="text-2xl font-bold text-center mb-6">Habit Tracker</h1>
    <?php if (!empty($error)) echo "<p class='text-red-500 text-center mb-3'>$error</p>"; ?>
    <form method="POST" class="space-y-3">
      <input name="username" type="text" placeholder="Username" class="w-full p-3 border rounded-lg" required>
      <input name="password" type="password" placeholder="Password" class="w-full p-3 border rounded-lg" required>
      <button type="submit" class="bg-indigo-600 text-white w-full p-3 rounded-lg hover:bg-indigo-700">Login</button>
    </form>
    <p class="text-center text-sm mt-4">Belum punya akun? <a href="register.php" class="text-indigo-600 font-medium">Daftar</a></p>
  </div>
</body>
</html>
