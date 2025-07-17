<?php
$title = "Dashboard";
ob_start(); // bắt đầu ghi nội dung
?>
<div class="grid">
  <div class="card">
    <h3>Total Users</h3>
    <div class="value">1,243</div>
  </div>
  <div class="card">
    <h3>Diagnoses Today</h3>
    <div class="value">78</div>
  </div>
  <div class="card">
    <h3>Top Disease</h3>
    <div class="value">Flu</div>
  </div>
  <div class="card">
    <h3>Feedback Unread</h3>
    <div class="value">5</div>
  </div>
</div>

<canvas id="diagnosisChart" height="80"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('diagnosisChart');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
      datasets: [{
        label: 'Diagnoses',
        data: [12, 19, 7, 15, 10, 22, 18],
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
