<?php
session_start();
include "../db.php";

if (!isset($_SESSION['student_id'])) {
  header("Location: ../student_login.php");
  exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details
$userQuery = mysqli_query($conn, "SELECT name FROM users WHERE id='$student_id'");
$user = mysqli_fetch_assoc($userQuery);
$name = $user['name'] ?? 'Unknown';

// Prepare data for chart
$progressQuery = mysqli_query($conn, "SELECT date, status FROM attendance WHERE member_id='$student_id' ORDER BY date ASC");
$dates = [];
$data = [];
$total = 0;
$present = 0;

while ($row = mysqli_fetch_assoc($progressQuery)) {
  $dates[] = $row['date'];
  $total++;
  if ($row['status'] === 'present') $present++;
  $data[] = ($present / $total);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Progress Chart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-4">
  <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
  <h3>📈 Attendance Progress - <?= htmlspecialchars($name) ?></h3>
  <div class="row">
    <div class="col-md-6">
      <div class="card mb-4 p-3">
        <h5 class="mb-3">📈 Cumulative Attendance %</h5>
        <div style="height: 220px;">
          <canvas id="progressChart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card mb-4 p-3">
        <h5 class="mb-3">📅 Weekly Attendance</h5>
        <div style="height: 220px;">
          <canvas id="weeklyChart"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card mb-4 p-3">
        <h5 class="mb-3">📊 Monthly Attendance %</h5>
        <div style="height: 220px;">
          <canvas id="monthlyChart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card mb-4 p-3">
        <h5 class="mb-3">🔄 Present vs Absent</h5>
        <div style="height: 220px;">
          <canvas id="statusChart"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card mb-4 p-3">
        <h5 class="mb-3">📉 Weekly Trends</h5>
        <div style="height: 220px;">
          <canvas id="trendChart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card mb-4 p-3">
        <h5 class="mb-3">📋 Combined (Expected vs Attended)</h5>
        <div style="height: 220px;">
          <canvas id="combinedChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const ctx = document.getElementById('progressChart').getContext('2d');
const progressChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?= json_encode($dates); ?>,
    datasets: [{
      label: 'Cumulative Attendance %',
      data: <?= json_encode(array_map(fn($v) => round($v * 100, 2), $data)); ?>,
      borderColor: '#8692f7',
      fill: false,
      tension: 0.2
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
        max: 100,
        ticks: {
          callback: value => value + '%'
        }
      }
    }
  }
});

new Chart(document.getElementById('weeklyChart').getContext('2d'), {
  type: 'bar',
  data: {
    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    datasets: [{
      label: 'Weekly Attendance',
      data: [1, 1, 0, 1, 1, 0],
      backgroundColor: '#4caf50'
    }]
  }
});

new Chart(document.getElementById('monthlyChart').getContext('2d'), {
  type: 'bar',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr'],
    datasets: [{
      label: 'Monthly %',
      data: [85, 90, 88, 92],
      backgroundColor: '#2196f3'
    }]
  }
});

new Chart(document.getElementById('statusChart').getContext('2d'), {
  type: 'pie',
  data: {
    labels: ['Present', 'Absent'],
    datasets: [{
      data: [80, 20],
      backgroundColor: ['#4caf50', '#f44336']
    }]
  }
});

new Chart(document.getElementById('trendChart').getContext('2d'), {
  type: 'line',
  data: {
    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
    datasets: [{
      label: 'Trend',
      data: [75, 80, 85, 90],
      borderColor: '#ff9800',
      tension: 0.4
    }]
  }
});

new Chart(document.getElementById('combinedChart').getContext('2d'), {
  type: 'bar',
  data: {
    labels: ['Day 1', 'Day 2', 'Day 3'],
    datasets: [
      {
        label: 'Expected',
        data: [1, 1, 1],
        backgroundColor: '#9c27b0'
      },
      {
        label: 'Attended',
        data: [1, 0, 1],
        backgroundColor: '#4caf50'
      }
    ]
  }
});
</script>
</body>
</html>