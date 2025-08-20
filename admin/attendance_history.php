<?php
include "../db.php";

$searchTerm = $_GET['search'] ?? '';
$enrollment_no = $_GET['enrollment_no'] ?? '';

// Fetch member based on search or enrollment number
if (!empty($searchTerm)) {
    $searchTermEscaped = mysqli_real_escape_string($conn, $searchTerm);
    $memberQuery = mysqli_query($conn, "SELECT * FROM users WHERE role='student' AND (name LIKE '%$searchTermEscaped%' OR enrollment_no LIKE '%$searchTermEscaped%') LIMIT 1");
    $member = mysqli_fetch_assoc($memberQuery);
} elseif (!empty($enrollment_no)) {
    $enrollment_noEscaped = mysqli_real_escape_string($conn, $enrollment_no);
    $memberQuery = mysqli_query($conn, "SELECT * FROM users WHERE role='student' AND enrollment_no='$enrollment_noEscaped' LIMIT 1");
    $member = mysqli_fetch_assoc($memberQuery);
} else {
    $member = null;
}

if ($member) {
    $memberId = $member['id'];
    $totalDaysQuery = mysqli_query($conn, "SELECT COUNT(*) as total_days FROM attendance WHERE member_id='$memberId'");
    $totalDays = mysqli_fetch_assoc($totalDaysQuery)['total_days'] ?? 0;

    $presentDaysQuery = mysqli_query($conn, "SELECT COUNT(*) as present_days FROM attendance WHERE member_id='$memberId' AND status='present'");
    $presentDays = mysqli_fetch_assoc($presentDaysQuery)['present_days'] ?? 0;

    $attendancePercentage = ($totalDays > 0) ? round(($presentDays / $totalDays) * 100, 2) : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Attendance History</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
  <a href="report_page.php" class="btn btn-secondary mb-3">← Back to Reports</a>
  <h3>Attendance History</h3>

  <!-- Search Form -->
  <form method="GET" class="mb-4 d-flex gap-2 flex-wrap align-items-center">
    <input type="text" name="search" class="form-control w-auto" placeholder="Search by name or enrollment no" value="<?= htmlspecialchars($searchTerm) ?>" />
    <button type="submit" class="btn btn-primary">Search</button>
  </form>

  <?php if (!$member): ?>
    <div class="alert alert-warning">No member selected or found.</div>
  <?php else: ?>
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">👤 Profile</h5>
        <p><strong>Name:</strong> <?= htmlspecialchars($member['name']) ?></p>
        <p><strong>Enrollment No:</strong> <?= htmlspecialchars($member['enrollment_no']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($member['email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($member['phone']) ?></p>
        <p><strong>Attendance Percentage:</strong> <?= $attendancePercentage ?? 0 ?>%</p>
      </div>
    </div>

    <?php
      $memberId = $member['id'];
      $attendanceQuery = mysqli_query($conn, "SELECT date, status FROM attendance WHERE member_id='$memberId' ORDER BY date DESC");
    ?>

    <h5>📅 Attendance Records</h5>
    <?php if (mysqli_num_rows($attendanceQuery) > 0): ?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($attendanceQuery)): ?>
              <tr>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td>
                  <span class="badge bg-<?= $row['status'] === 'present' ? 'success' : 'danger' ?>">
                    <?= ucfirst($row['status']) ?>
                  </span>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p>No attendance records found for this member.</p>
    <?php endif; ?>
  <?php endif; ?>
</div>
</body>
</html>