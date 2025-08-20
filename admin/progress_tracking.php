<?php
session_start();
include "../db.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch attendance data for all students
$attendanceQuery = mysqli_query($conn, "SELECT u.name, a.date, a.status FROM attendance a 
                                        JOIN users u ON a.member_id = u.id 
                                        WHERE u.role='student' 
                                        ORDER BY a.date DESC");

// Fetch the last marked attendance
$lastAttendanceQuery = mysqli_query($conn, "SELECT u.name, u.enrollment_no, a.date, a.status 
                                            FROM attendance a 
                                            JOIN users u ON a.member_id = u.id
                                            WHERE a.member_id IN (SELECT id FROM users WHERE role='student') 
                                            ORDER BY a.date DESC LIMIT 1");

// Fetch monthly attendance stats
$monthlyStatsQuery = mysqli_query($conn, "SELECT MONTH(date) as month, COUNT(*) as total, 
                                                SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present 
                                              FROM attendance 
                                            GROUP BY MONTH(date) ORDER BY MONTH(date)");

if (!$attendanceQuery || !$lastAttendanceQuery || !$monthlyStatsQuery) {
    die('Query Failed: ' . mysqli_error($conn));
}

// Prepare data for Chart.js
$attendanceData = [];
$labels = [];
while ($row = mysqli_fetch_assoc($attendanceQuery)) {
    $attendanceData[] = $row['status'] == 'present' ? 1 : 0; // 1 for present, 0 for absent
    $labels[] = date("d M Y", strtotime($row['date'])); // Convert date to a readable format
}

// Fetch the last marked attendance
$lastAttendance = mysqli_fetch_assoc($lastAttendanceQuery);

// Monthly Attendance Stats
$monthlyData = [];
$monthlyLabels = [];
while ($row = mysqli_fetch_assoc($monthlyStatsQuery)) {
    $monthlyLabels[] = date("M", mktime(0, 0, 0, $row['month'], 10)); // Convert month number to month name
    $monthlyData[] = round(($row['present'] / $row['total']) * 100, 2); // Percentage of present attendance for each month
}

// Fetch total attendance data for pie chart
$totalAttendanceQuery = mysqli_query($conn, "SELECT COUNT(*) as total, 
                                                SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present 
                                            FROM attendance");

$totalAttendance = mysqli_fetch_assoc($totalAttendanceQuery);
$totalPresent = $totalAttendance['present'];
$totalAbsent = $totalAttendance['total'] - $totalPresent;

// Additional data for new charts

// For Bar and Line charts (monthly attendance)
$barLabels = $monthlyLabels;
$barData = $monthlyData;
$lineLabels = $monthlyLabels;
$lineData = $monthlyData;

// For Doughnut, Pie, Polar Area - attendance count by status
$statusCountQuery = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM attendance GROUP BY status");
$statusLabels = [];
$statusCounts = [];
while($row = mysqli_fetch_assoc($statusCountQuery)) {
    $statusLabels[] = ucfirst($row['status']);
    $statusCounts[] = (int)$row['count'];
}

// For Radar - attendance percentage per weekday
$weekdayQuery = mysqli_query($conn, "SELECT DAYNAME(date) as weekday, COUNT(*) as total, SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present FROM attendance GROUP BY weekday ORDER BY FIELD(weekday, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')");
$radarLabels = [];
$radarData = [];
while ($row = mysqli_fetch_assoc($weekdayQuery)) {
    $radarLabels[] = $row['weekday'];
    $radarData[] = $row['total'] > 0 ? round(($row['present'] / $row['total']) * 100, 2) : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Progress Tracking - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background: #f7f9fc;
      color: #333;
      font-family: 'Segoe UI', sans-serif;
      margin-top: 40px;
      padding-top: 20px;
    }
    .card-body {
      padding: 20px;
    }
    canvas {
      max-width: 100% !important;
      max-height: 250px !important;
      margin-bottom: 20px;
    }
    .card-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: #333;
    }
  </style>
</head>
<body>
<div class="container">
  <h3>📊 Attendance Progress Tracking</h3>

  <!-- Last Marked Attendance -->
  <!-- <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">📝 Last Marked Attendance</h5>
      <p><strong>Name:</strong> <?= htmlspecialchars($lastAttendance['name']) ?></p>
      <p><strong>Enrollment No:</strong> <?= htmlspecialchars($lastAttendance['enrollment_no']) ?></p>
      <p><strong>Date:</strong> <?= date("d M Y", strtotime($lastAttendance['date'])) ?></p>
      <p><strong>Status:</strong> <?= ucfirst($lastAttendance['status']) ?></p>
    </div>
  </div> -->

  <div class="row">
    <!-- Original 3 charts -->
    <div class="col-md-6 col-lg-4">
      <div class="card mb-4"><div class="card-body">
        <h5 class="card-title">📈 Attendance Progress (Over Time)</h5>
        <canvas id="attendanceChart" height="250"></canvas>
      </div></div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="card mb-4"><div class="card-body">
        <h5 class="card-title">📅 Monthly Attendance Stats</h5>
        <canvas id="monthlyChart" height="250"></canvas>
      </div></div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="card mb-4"><div class="card-body">
        <h5 class="card-title">📊 Total Present vs Absent</h5>
        <canvas id="totalAttendanceChart" height="250"></canvas>
      </div></div>
    </div>

    <!-- 5 New Charts -->
    <div class="col-md-6 col-lg-4">
      <div class="card mb-4"><div class="card-body">
        <h5 class="card-title">Bar Chart Example</h5>
        <canvas id="barChart" height="250"></canvas>
      </div></div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="card mb-4"><div class="card-body">
        <h5 class="card-title">Doughnut Chart Example</h5>
        <canvas id="doughnutChart" height="250"></canvas>
      </div></div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="card mb-4"><div class="card-body">
        <h5 class="card-title">Radar Chart Example</h5>
        <canvas id="radarChart" height="250"></canvas>
      </div></div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="card mb-4"><div class="card-body">
        <h5 class="card-title">Polar Area Chart Example</h5>
        <canvas id="polarAreaChart" height="250"></canvas>
      </div></div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="card mb-4"><div class="card-body">
        <h5 class="card-title">Pie Chart Example</h5>
        <canvas id="pieChart" height="250"></canvas>
      </div></div>
    </div>
  </div>
</div>

<script>
  // Existing charts data and options
  var attendanceChart = new Chart(document.getElementById('attendanceChart').getContext('2d'), {
    type: 'line',
    data: {
      labels: <?= json_encode($labels); ?>,
      datasets: [{
        label: 'Attendance Percentage',
        data: <?= json_encode($attendanceData); ?>,
        fill: false,
        borderColor: '#007bff',
        tension: 0.1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: { title: { display: true, text: 'Date' } },
        y: {
          min: 0, max: 1,
          ticks: { stepSize: 0.1, callback: v => (v * 100) + '%' },
          title: { display: true, text: 'Attendance Percentage' }
        }
      }
    }
  });

  var monthlyChart = new Chart(document.getElementById('monthlyChart').getContext('2d'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($monthlyLabels); ?>,
      datasets: [{
        label: 'Attendance Percentage',
        data: <?= json_encode($monthlyData); ?>,
        backgroundColor: '#007bff',
        borderRadius: 5
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: { title: { display: true, text: 'Month' } },
        y: {
          min: 0, max: 100,
          ticks: { stepSize: 10, callback: v => v + '%' },
          title: { display: true, text: 'Attendance Percentage' }
        }
      }
    }
  });

  var totalAttendanceChart = new Chart(document.getElementById('totalAttendanceChart').getContext('2d'), {
    type: 'pie',
    data: {
      labels: ['Present', 'Absent'],
      datasets: [{
        data: [<?= $totalPresent ?>, <?= $totalAbsent ?>],
        backgroundColor: ['#4caf50', '#f44336'],
        borderColor: ['#4caf50', '#f44336'],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'top' } }
    }
  });

  // Bar Chart
  new Chart(document.getElementById('barChart').getContext('2d'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($barLabels); ?>,
      datasets: [{ label: 'Sample Bar', data: <?= json_encode($barData); ?>, backgroundColor: '#8692f7' }]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });

  // Doughnut Chart
  new Chart(document.getElementById('doughnutChart').getContext('2d'), {
    type: 'doughnut',
    data: {
      labels: <?= json_encode($statusLabels); ?>,
      datasets: [{
        data: <?= json_encode($statusCounts); ?>,
        backgroundColor: ['#4caf50', '#f44336', '#2196f3', '#ff9800', '#9c27b0', '#03a9f4']
      }]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });

  // Radar Chart
  new Chart(document.getElementById('radarChart').getContext('2d'), {
    type: 'radar',
    data: {
      labels: <?= json_encode($radarLabels); ?>,
      datasets: [{
        label: 'Sample Radar',
        data: <?= json_encode($radarData); ?>,
        backgroundColor: 'rgba(134,146,247,0.4)',
        borderColor: '#8692f7',
        pointBackgroundColor: '#8692f7'
      }]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });

  // Polar Area Chart
  new Chart(document.getElementById('polarAreaChart').getContext('2d'), {
    type: 'polarArea',
    data: {
      labels: <?= json_encode($statusLabels); ?>,
      datasets: [{
        data: <?= json_encode($statusCounts); ?>,
        backgroundColor: ['#4caf50', '#f44336', '#2196f3', '#ff9800', '#9c27b0', '#03a9f4']
      }]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });

  // Pie Chart
  new Chart(document.getElementById('pieChart').getContext('2d'), {
    type: 'pie',
    data: {
      labels: <?= json_encode($statusLabels); ?>,
      datasets: [{
        data: <?= json_encode($statusCounts); ?>,
        backgroundColor: ['#4caf50', '#f44336', '#2196f3', '#ff9800', '#9c27b0', '#03a9f4']
      }]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });
</script>
</body>
</html>