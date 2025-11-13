<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 7 hari terakhir
$dates = [];
$done_counts = [];

for($i=6;$i>=0;$i--){
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates[] = $date;

    $stmt = $conn->prepare("SELECT COUNT(*) as done_count 
        FROM habit_logs l 
        JOIN habits h ON l.habit_id = h.id
        WHERE h.user_id=? AND l.log_date=? AND l.status=1");
    $stmt->bind_param("is",$user_id,$date);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $done_counts[] = (int)$res['done_count'];
}

// Moving average 3 hari
$moving_avg = [];
for($i=0;$i<count($done_counts);$i++){
    $start = max(0,$i-2);
    $slice = array_slice($done_counts,$start,$i-$start+1);
    $moving_avg[] = array_sum($slice)/count($slice);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Progress Grafik</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Navbar -->
<nav class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 text-white flex justify-between items-center shadow-md flex-wrap">
    <h1 class="text-2xl font-bold flex items-center gap-2"><i data-lucide="line-chart"></i> Grafik Progress</h1>
    <div class="flex items-center gap-4 mt-2 md:mt-0">
        <a href="home.php" title="Kembali ke Dashboard" class="hover:scale-110 transition"><i data-lucide="home"></i></a>
        <a href="profile.php" title="Profil" class="hover:scale-110 transition"><i data-lucide="user"></i></a>
        <a href="logout.php" title="Keluar" class="hover:scale-110 transition"><i data-lucide="log-out"></i></a>
    </div>
</nav>

<!-- Grafik -->
<main class="max-w-4xl mx-auto mt-10 bg-white p-8 rounded-2xl shadow-lg animate-fadeIn">
    <h2 class="text-xl font-semibold mb-4">Performa Kebiasaanmu (7 Hari Terakhir)</h2>
    <canvas id="progressChart" height="120"></canvas>
</main>

<script>
lucide.createIcons();

const dates = <?= json_encode($dates) ?>;
const doneCounts = <?= json_encode($done_counts) ?>;
const movingAvg = <?= json_encode($moving_avg) ?>;

new Chart(document.getElementById("progressChart"), {
    type: 'line',
    data: {
        labels: dates,
        datasets: [
            {
                label: 'Habit Done',
                data: doneCounts,
                borderColor: 'rgba(59,130,246,1)',
                backgroundColor: 'rgba(59,130,246,0.2)',
                tension: 0.4
            },
            {
                label: '3 Hari Moving Avg',
                data: movingAvg,
                borderColor: 'rgba(16,185,129,1)',
                borderDash: [5,5],
                fill: false,
                tension: 0.4
            }
        ]
    },
    options:{
        responsive:true,
        plugins:{ legend:{display:true}, tooltip:{mode:'index',intersect:false} },
        scales:{ y:{beginAtZero:true,precision:0}, x:{title:{display:true,text:'Tanggal'}} }
    }
});
</script>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(10px);} to {opacity:1; transform:translateY(0);} }
.animate-fadeIn { animation:fadeIn 0.6s ease-in-out; }
</style>

</body>
</html>
