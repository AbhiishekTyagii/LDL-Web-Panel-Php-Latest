<?php
include "../db.php";

if (!isset($_GET['id'])) {
  die("Member ID is required.");
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) !== 1) {
  die("Member not found.");
}

$member = mysqli_fetch_assoc($result);
$visitQuery = mysqli_query($conn, "SELECT COUNT(*) AS total_visits FROM attendance WHERE member_id = $id");
$visitData = mysqli_fetch_assoc($visitQuery);
$totalVisits = $visitData['total_visits'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">  
  <title>Member Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f7f9fc;
      font-family: 'Segoe UI', sans-serif;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      margin-top: 30px;
    }
    .btn {
      border-radius: 12px;
    }
    .btn-primary {
      background-color: #8692f7;
      border-color: #8692f7;
    }
    .btn-primary:hover {
      background-color: #6e7ae0;
      border-color: #6e7ae0;
    }
  </style>
</head>
<body>
<div class="container mt-4">
  <a href="all_members.php" class="btn btn-secondary mb-3">← Back to Members</a>
  <div class="card p-4">
    <h4 class="mb-4 text-primary">👤 Member Profile Overview</h4>
    <div class="row mb-2">
      <div class="col-sm-4 fw-semibold">Name:</div>
      <div class="col-sm-8"><?= htmlspecialchars($member['name']) ?></div>
    </div>
    <div class="row mb-2">
      <div class="col-sm-4 fw-semibold">Enrollment No:</div>
      <div class="col-sm-8"><?= htmlspecialchars($member['enrollment_no']) ?></div>
    </div>
    <div class="row mb-2">
      <div class="col-sm-4 fw-semibold">Email:</div>
      <div class="col-sm-8"><?= htmlspecialchars($member['email']) ?></div>
    </div>
    <div class="row mb-2">
      <div class="col-sm-4 fw-semibold">Phone:</div>
      <div class="col-sm-8"><?= htmlspecialchars($member['phone']) ?></div>
    </div>
    <div class="row mb-2">
      <div class="col-sm-4 fw-semibold">Role:</div>
      <div class="col-sm-8"><?= htmlspecialchars($member['role']) ?></div>
    </div>
    <div class="row mb-4">
      <div class="col-sm-4 fw-semibold">Total Visits:</div>
      <div class="col-sm-8"><?= $totalVisits ?></div>
    </div>
    <a href="login_as.php?id=<?= $member['id'] ?>" class="btn btn-primary">🔐 Login as this user</a>
  </div>
</div>
</body>
</html>
