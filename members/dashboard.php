<?php
session_start();
include "../db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: ../student_login.php");
    exit();
}

$student_id = $_SESSION['student_id']; 

$logMessage = "User ID: $student_id logged in at " . date('Y-m-d H:i:s') . "\n";
file_put_contents('../app.log', $logMessage, FILE_APPEND);

// Fetch attendance data
$totalDaysQuery = mysqli_query($conn, "SELECT COUNT(*) as total_days FROM attendance WHERE member_id='$student_id'");
$totalDays = mysqli_fetch_assoc($totalDaysQuery)['total_days'];

$presentDaysQuery = mysqli_query($conn, "SELECT COUNT(*) as present_days FROM attendance WHERE member_id='$student_id' AND status='present'");
$presentDays = mysqli_fetch_assoc($presentDaysQuery)['present_days'];

$attendancePercentage = ($totalDays > 0) ? ($presentDays / $totalDays) * 100 : 0;

// Fetch Profile Data
$profileQuery = mysqli_query($conn, "SELECT name, enrollment_no, email, phone FROM users WHERE id='$student_id'");
$profileData = mysqli_fetch_assoc($profileQuery);

// Fetch Previous Attendance Records
$attendanceHistoryQuery = mysqli_query($conn, "SELECT * FROM attendance WHERE member_id='$student_id' ORDER BY date DESC");

// Check for errors in the query
if (!$attendanceHistoryQuery) {
    die('Query Failed: ' . mysqli_error($conn));  // Show any query error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Members Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      background: #f7f9fc;
      color: #333;
      font-family: 'Segoe UI', sans-serif;
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
      padding: 20px 15px 60px 15px;
    }

    .card {
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      margin-bottom: 30px;
      overflow: hidden;
    }

    .card:hover {
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    .card-body {
      padding: 25px;
    }

    .card-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: #8692f7;
      margin-bottom: 20px;
    }

    .badge-success {
      background-color: #4caf50;
    }

    .badge-danger {
      background-color: #f44336;
    }

    .badge {
      border-radius: 12px;
      padding: 6px 12px;
      font-size: 0.9rem;
    }

    .btn-outline-danger {
      font-weight: 600;
      border-radius: 20px;
      color: #8692f7;
      border-color: #8692f7;
    }

    .btn-outline-danger:hover {
      background-color: #8692f7;
      color: white;
    }

    .table {
      border-collapse: separate;
      border-spacing: 0;
      width: 100%;
      font-size: 0.875rem;
    }

    .table-striped tbody tr:nth-child(odd) {
      background-color: #ffffff;
    }

    .table-striped tbody tr:nth-child(even) {
      background-color: #f7f9fc;
    }

    .table thead th {
      background-color: #8692f7;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      font-size: 0.9rem;
      border: none;
      padding: 16px 20px;
      position: sticky;
      top: 0;
      z-index: 1;
    }

    .table th, .table td {
      padding: 10px 12px;
      text-align: center;
      border-bottom: 1px solid #e0e0e0;
      vertical-align: middle;
    }

    .table tbody tr:hover {
      background-color: #e3f2fd;
    }

    .d-flex.justify-content-between {
      border-bottom: 2px solid #f0f0f0;
      padding-bottom: 20px;
      margin-bottom: 20px;
    }

    .attendance-summary {
      flex-wrap: wrap;
      gap: 10px;
    }

    .attendance-summary > div {
      margin-bottom: 10px;
    }

    @media (max-width: 576px) {
      .attendance-summary {
        flex-direction: column !important;
        align-items: flex-start !important;
      }

      .card-body {
        padding: 20px 15px;
      }

      .table th, .table td {
        padding: 8px;
      }
    }
  .badge.bg-success,
  .badge.bg-danger {
    font-size: 0.75rem;
    padding: 4px 10px;
  }
  </style>
</head>
<body>
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">🎓 Member's Dashboard</h3>
    <a href="../logout.php" class="btn btn-outline-danger">Logout</a>
  </div>

  <!-- Profile Information -->
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">👤 Profile</h5>
      <p><strong>Name:</strong> <?= htmlspecialchars($profileData['name']) ?></p>
      <p><strong>Enrollment No:</strong> <?= htmlspecialchars($profileData['enrollment_no']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($profileData['email']) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($profileData['phone']) ?></p>
    </div>
  </div>

  <!-- Attendance Percentage -->
  <div class="card mb-4">
    <div class="card-body d-flex flex-sm-row justify-content-between align-items-center attendance-summary">
      <div>
        <h5 class="mb-1">📈 Attendance Percentage</h5>
        <p class="fs-4 fw-semibold mb-0"><?= round($attendancePercentage, 2) ?>%</p>
      </div>
      <div>
        <span class="text-muted small">Total Days: <?= $totalDays ?> | Present: <?= $presentDays ?></span>
      </div>
    </div>
  </div>

  <!-- Notifications Section -->
  <div class="card mb-4">
   <div class="card-body">
  <h5 class="card-title">🔔 Notifications <span id="notifCount" class="badge bg-primary ms-2"></span></h5>
  <div id="notificationArea">
    <p class="text-muted">Loading notifications...</p>
  </div>
</div>
  </div>

    <!-- Export Section -->
<div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title"> Export Reports</h5>
      <!-- Export Buttons -->
<div class="d-flex gap-3 flex-wrap justify-content-end">
  <a href="export_member_report.php?export_type=csv"
     class="btn d-flex align-items-center px-3 py-2 shadow-sm rounded-pill"
     style="border: 2px solid #8692f7; color: #8692f7;"
     onmouseover="this.style.backgroundColor='#8692f7'; this.style.color='white';"
     onmouseout="this.style.backgroundColor='transparent'; this.style.color='#8692f7';"
  >
    <span class="me-2">📄</span> Export CSV
  </a>
  <a href="export_member_report.php?export_type=pdf"
     class="btn d-flex align-items-center px-3 py-2 shadow-sm rounded-pill"
     style="border: 2px solid #8692f7; color: #8692f7;"
     onmouseover="this.style.backgroundColor='#8692f7'; this.style.color='white';"
     onmouseout="this.style.backgroundColor='transparent'; this.style.color='#8692f7';"
  >
    <span class="me-2">📑</span> Export PDF
  </a>
</div>
    </div>
  </div>

    <!-- Progress Section -->
    <div class="card mb-4">
      <div class="card-body text-end">
        <h5 class="card-title text-start">📊 Progress Tracker</h5>
        <a href="progress_view.php"
           class="btn d-flex align-items-center justify-content-center gap-2 px-3 py-2 shadow-sm rounded-pill"
           style="border: 2px solid #8692f7; color: #8692f7;"
           onmouseover="this.style.backgroundColor='#8692f7'; this.style.color='white';"
           onmouseout="this.style.backgroundColor='transparent'; this.style.color='#8692f7';"
        >
          <span>📈</span> View Progress
        </a>
      </div>
    </div>

  <!-- Previous Attendance Section -->
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">📅 Previous Attendances</h5>
      <?php if (mysqli_num_rows($attendanceHistoryQuery) > 0): ?>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($attendanceHistoryQuery)): ?>
                <tr>
                  <td><?= htmlspecialchars($row['date']) ?></td>
                  <td>
                    <span class="badge bg-<?= $row['status'] == 'present' ? 'success' : 'danger' ?>">
                      <?= ucfirst($row['status']) ?>
                    </span>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-muted">You have no previous attendance records.</p>
      <?php endif; ?>
    </div>
  </div>

</div>
<!-- Firebase App (the core Firebase SDK) -->
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-database-compat.js"></script>

<script src="../loadFirebaseConfig.php"></script>
<script>
  firebase.initializeApp(firebaseConfig);
  console.log('Firebase initialized with config:', firebaseConfig);

  const db = firebase.database();

  const studentId = "<?= $student_id ?>";

  db.ref(".info/connected").on("value", (snap) => {
    if (snap.val() === true) {
      console.log("Firebase DB connected successfully.");
    } else {
      console.error("Firebase DB connection lost.");
    }
  });

  db.ref("notifications/" + studentId).on("value", (snapshot) => {
    const notificationArea = document.getElementById("notificationArea");
    const data = snapshot.val();

    if (!data) {
      notificationArea.innerHTML = '<p class="text-muted">No new notifications.</p>';
      return;
    }

    let html = "<ul class='list-group'>";
    Object.entries(data).forEach(([key, notification]) => {
      const message = notification.message || "No message";
      const time = notification.time ? `<br><small class='text-muted'>${new Date(notification.time).toLocaleString()}</small>` : "";
      html += `<li class='list-group-item'>${message}${time}</li>`;
    });
    html += "</ul>";
    notificationArea.innerHTML = html;
  });
</script>
</body>
</html>