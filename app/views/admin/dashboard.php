<?php
$title = "Dashboard";
ob_start();
?>

<div class="grid">
  <div class="card">
    <h3>Total Users</h3>
    <div class="value"><?= $totalUsers ?? 0 ?></div>
  </div>
  <div class="card">
    <h3>Diagnoses Today</h3>
    <div class="value"><?= $diagnosesToday ?? 0 ?></div>
  </div>
  <div class="card">
    <h3>Top Disease</h3>
    <div class="value"><?= htmlspecialchars($topDisease ?? 'N/A') ?></div>
  </div>
  <div class="card">
    <h3>Feedback Unread</h3>
    <div class="value"><?= $unreadFeedback ?? 0 ?></div>
  </div>
</div>

<canvas id="diagnosisChart" height="80"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('diagnosisChart');

  const labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
  const dataFromPHP = <?= json_encode($diagnosesPerDay ?? []) ?>;
  const today = new Date();
  const monday = new Date(today.setDate(today.getDate() - today.getDay() + 1));

  const chartData = labels.map((label, i) => {
    const day = new Date(monday);
    day.setDate(day.getDate() + i);
    const dateStr = day.toISOString().slice(0, 10);
    return dataFromPHP[dateStr] ?? 0;
  });

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Diagnoses',
        data: chartData,
        borderColor: '#1e3a8a',
        backgroundColor: 'rgba(30,58,138,0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });
</script>
