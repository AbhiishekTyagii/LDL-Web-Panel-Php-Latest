<?php
include "../db.php";

if (isset($_POST['update_single'])) {
    $memberId = (int)$_POST['update_single'];
    $dayField = "day_" . $memberId;
    $day = $_POST[$dayField] ?? '';

    if ($day !== '') {
        $updateQuery = "UPDATE users SET day = '".mysqli_real_escape_string($conn, $day)."' WHERE id = $memberId";
        if (mysqli_query($conn, $updateQuery)) {
            echo "<div class='alert alert-success'>Day updated successfully for member ID {$memberId}.</div>";
        } else {
            echo "<div class='alert alert-danger'>Failed to update day for member ID {$memberId}.</div>";
        }
    }
}

$members = mysqli_query($conn, "SELECT id, name, enrollment_no, email, phone, day FROM users WHERE role != 'admin'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Members</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f7f9fc;
      font-family: 'Segoe UI', sans-serif;
    }
    .table {
      border-radius: 12px;
      overflow: hidden;
    }
    .table thead th {
      background-color: #8692f7;
      color: white;
    }
    .table td, .table th {
      text-align: center;
      vertical-align: middle;
    }
    .table-responsive {
      border-radius: 12px;
      overflow-x: auto;
      background-color: #fff;
      padding: 10px;
    }
  </style>
</head>
<body>
<div class="container mt-4">
  <h3 class="mb-4 fw-bold">👥 All Members</h3>
  <div class="table-responsive">
    <table class="table table-bordered table-striped shadow-sm">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Enrollment No</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Day</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($members) > 0): $i = 1; ?>
          <?php while ($row = mysqli_fetch_assoc($members)): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['enrollment_no']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= htmlspecialchars($row['phone']) ?></td>
              <td><?= htmlspecialchars($row['day']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-muted">No members found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<hr class="my-5">

<h4>Edit Member Days</h4>
<form method="POST" action="scheduled_members.php">
  <div class="table-responsive">
    <table class="table table-bordered table-striped shadow-sm">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Enrollment No</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Day</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $allMembers = mysqli_query($conn, "SELECT id, name, enrollment_no, email, phone, day FROM users WHERE role != 'admin'");
          if (mysqli_num_rows($allMembers) > 0): $count = 1;
          while ($member = mysqli_fetch_assoc($allMembers)):
        ?>
        <tr>
          <td><?= $count++ ?></td>
          <td><?= htmlspecialchars($member['name']) ?></td>
          <td><?= htmlspecialchars($member['enrollment_no']) ?></td>
          <td><?= htmlspecialchars($member['email']) ?></td>
          <td><?= htmlspecialchars($member['phone']) ?></td>
          <td>
            <select name="day_<?= $member['id'] ?>" class="form-select form-select-sm">
              <option value="" disabled <?= empty($member['day']) ? 'selected' : '' ?>>Select day</option>
              <?php
                $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                foreach ($days as $day) {
                  $selected = ($member['day'] === $day) ? 'selected' : '';
                  echo "<option value='{$day}' {$selected}>{$day}</option>";
                }
              ?>
            </select>
          </td>
          <td>
            <button type="submit" name="update_single" value="<?= $member['id'] ?>" class="btn btn-sm btn-primary">Save</button>
          </td>
        </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="7" class="text-muted text-center">No members found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</form>
</div>
</body>
</html>
