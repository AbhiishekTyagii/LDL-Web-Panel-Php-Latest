<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

$selected_student = $_GET['student'] ?? '';
$selected_date = $_GET['date'] ?? date('Y-m-d');
$monthYear = $_GET['month_view'] ?? '';
$selected_history_student = $_GET['history_student'] ?? '';

$students = mysqli_query($conn, "SELECT id, name FROM users WHERE role='student'");
$attendance = null;
if ($selected_student) {
    $attendance_query = mysqli_query($conn, "SELECT * FROM attendance WHERE member_id='$selected_student' AND date='$selected_date'");
    $attendance = mysqli_fetch_assoc($attendance_query);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $member_id = $_POST['member_id'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    $check = mysqli_query($conn, "SELECT * FROM attendance WHERE member_id='$member_id' AND date='$date'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE attendance SET status='$status' WHERE member_id='$member_id' AND date='$date'");
    } else {
        mysqli_query($conn, "INSERT INTO attendance (member_id, date, status) VALUES ('$member_id', '$date', '$status')");
    }
    header("Location: edit_attendance.php?student=$member_id&date=$date&success=1");
    exit();
}

// For Monthly Edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_month'])) {
    foreach ($_POST['month_status'] as $student_id => $dates) {
        foreach ($dates as $date => $status) {
            if (!$status) continue;
            $check = mysqli_query($conn, "SELECT * FROM attendance WHERE member_id='$student_id' AND date='$date'");
            if (mysqli_num_rows($check)) {
                mysqli_query($conn, "UPDATE attendance SET status='$status' WHERE member_id='$student_id' AND date='$date'");
            } else {
                mysqli_query($conn, "INSERT INTO attendance (member_id, date, status) VALUES ('$student_id', '$date', '$status')");
            }
        }
    }
    header("Location: edit_attendance.php?month_view=" . $_POST['year'] . "-" . str_pad($_POST['month'], 2, "0", STR_PAD_LEFT) . "&success=1");
    exit();
}

// For Full History Edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_history'])) {
    $sid = $_POST['student_id'];
    foreach ($_POST['history_status'] as $date => $status) {
        mysqli_query($conn, "UPDATE attendance SET status='$status' WHERE member_id='$sid' AND date='$date'");
    }
    header("Location: edit_attendance.php?history_student=$sid&success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Attendance</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container mt-5">
  <div class="d-flex justify-content-between mb-4 align-items-center">
    <h3 class="fw-bold">✏️ Edit Attendance</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary">← Back to Dashboard</a>
  </div>

  <!-- Form to select student and date -->
  <form method="GET" class="row g-3 align-items-end mb-4">
    <div class="col-md-6">
      <label for="student">Select Student</label>
      <select name="student" class="form-select" required>
        <option value="">-- Select Student --</option>
        <?php while ($s = mysqli_fetch_assoc($students)): ?>
          <option value="<?= $s['id'] ?>" <?= ($s['id'] == $selected_student) ? 'selected' : '' ?>>
            <?= $s['name'] ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label for="date">Select Date</label>
      <input type="date" name="date" class="form-control" value="<?= $selected_date ?>" required>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">View</button>
    </div>
  </form>

  <?php if ($selected_student && $attendance): ?>
    <!-- Update individual attendance -->
    <form method="POST" class="card p-4 mb-4">
      <input type="hidden" name="member_id" value="<?= $selected_student ?>">
      <input type="hidden" name="date" value="<?= $selected_date ?>">
      <h5>Status for <?= date("d M Y", strtotime($selected_date)) ?></h5>
      <select name="status" class="form-select mb-3" required>
        <option value="present" <?= $attendance['status'] == 'present' ? 'selected' : '' ?>>Present</option>
        <option value="absent" <?= $attendance['status'] == 'absent' ? 'selected' : '' ?>>Absent</option>
      </select>
      <button type="submit" name="update" class="btn btn-success">Update Attendance</button>
    </form>
  <?php elseif ($selected_student): ?>
    <!-- Mark attendance -->
    <form method="POST" class="card p-4 mb-4">
      <input type="hidden" name="member_id" value="<?= $selected_student ?>">
      <input type="hidden" name="date" value="<?= $selected_date ?>">
      <h5>No attendance found for <?= date("d M Y", strtotime($selected_date)) ?>. Mark now:</h5>
      <select name="status" class="form-select mb-3" required>
        <option value="present">Present</option>
        <option value="absent">Absent</option>
      </select>
      <button type="submit" name="update" class="btn btn-success">Mark Attendance</button>
    </form>
  <?php endif; ?>

<hr class="my-5">
<h4 class="fw-bold">🗓️ Edit Attendance for All Students by Date</h4>
<form method="GET" class="row g-3 align-items-end mb-4">
  <div class="col-md-4">
    <label>Select Date</label>
    <input type="date" name="bulk_date" class="form-control" value="<?= $_GET['bulk_date'] ?? '' ?>" required />
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-primary w-100">Load</button>
  </div>
</form>

<?php
if (isset($_GET['bulk_date'])):
    $bulk_date = $_GET['bulk_date'];
    $students_all = mysqli_query($conn, "SELECT id, name FROM users WHERE role='student'");
    $attendances = [];
    $att_result = mysqli_query($conn, "SELECT * FROM attendance WHERE date='$bulk_date'");
    while ($att_row = mysqli_fetch_assoc($att_result)) {
        $attendances[$att_row['member_id']] = $att_row['status'];
    }
?>
<form method="POST" class="card p-4 mb-4">
  <input type="hidden" name="bulk_update" value="1" />
  <input type="hidden" name="date" value="<?= $bulk_date ?>" />
  <h5 class="mb-4">Attendance on <?= date("d M Y", strtotime($bulk_date)) ?></h5>
  <?php while ($student = mysqli_fetch_assoc($students_all)): ?>
    <div class="mb-3 row align-items-center">
      <label class="col-sm-6 col-form-label"><?= $student['name'] ?></label>
      <div class="col-sm-6">
        <select name="statuses[<?= $student['id'] ?>]" class="form-select">
          <option value="">-- No Change --</option>
          <option value="present" <?= (isset($attendances[$student['id']]) && $attendances[$student['id']] == 'present') ? 'selected' : '' ?>>Present</option>
          <option value="absent" <?= (isset($attendances[$student['id']]) && $attendances[$student['id']] == 'absent') ? 'selected' : '' ?>>Absent</option>
        </select>
      </div>
    </div>
  <?php endwhile; ?>
  <button type="submit" name="bulk_submit" class="btn btn-success">Update All</button>
</form>
<?php endif; ?>

  <hr class="my-5">
  <h4 class="fw-bold">📅 Edit by Month</h4>
  <form method="GET" class="row g-3 align-items-end mb-4">
    <div class="col-md-4">
      <label>Select Month</label>
      <input type="month" name="month_view" class="form-control" value="<?= $_GET['month_view'] ?? '' ?>" required />
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">Load</button>
    </div>
  </form>

  <?php
  if (isset($_GET['month_view'])):
      $monthYear = $_GET['month_view'];
      list($year, $month) = explode("-", $monthYear);
      $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
      $all_students = mysqli_query($conn, "SELECT id, name FROM users WHERE role='student'");
      $attendance_data = [];
      $res = mysqli_query($conn, "SELECT * FROM attendance WHERE MONTH(date)='$month' AND YEAR(date)='$year'");
      while ($a = mysqli_fetch_assoc($res)) {
          $attendance_data[$a['member_id']][$a['date']] = $a['status'];
      }
  ?>
  <form method="POST">
    <input type="hidden" name="month_edit" value="1" />
    <input type="hidden" name="month" value="<?= $month ?>" />
    <input type="hidden" name="year" value="<?= $year ?>" />
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Student</th>
            <?php for ($d = 1; $d <= $daysInMonth; $d++): ?>
              <th><?= $d ?></th>
            <?php endfor; ?>
          </tr>
        </thead>
        <tbody>
          <?php while ($stu = mysqli_fetch_assoc($all_students)): ?>
            <tr>
              <td><?= $stu['name'] ?></td>
              <?php for ($d = 1; $d <= $daysInMonth; $d++):
                $date = "$year-$month-" . str_pad($d, 2, "0", STR_PAD_LEFT);
                $status = $attendance_data[$stu['id']][$date] ?? '';
              ?>
                <td>
                  <select name="month_status[<?= $stu['id'] ?>][<?= $date ?>]" class="form-select form-select-sm">
                    <option value=""></option>
                    <option value="present" <?= $status == 'present' ? 'selected' : '' ?>>P</option>
                    <option value="absent" <?= $status == 'absent' ? 'selected' : '' ?>>A</option>
                  </select>
                </td>
              <?php endfor; ?>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <button type="submit" name="save_month" class="btn btn-success mt-3">Save All</button>
  </form>
  <?php endif; ?>

  <hr class="my-5">
  <h4 class="fw-bold">📜 Edit Full Attendance History</h4>
  <form method="GET" class="row g-3 align-items-end mb-4">
    <div class="col-md-6">
      <label>Select Student</label>
      <select name="history_student" class="form-select" required>
        <option value="">-- Select Student --</option>
        <?php
        $res = mysqli_query($conn, "SELECT id, name FROM users WHERE role='student'");
        while ($r = mysqli_fetch_assoc($res)): ?>
          <option value="<?= $r['id'] ?>" <?= ($_GET['history_student'] ?? '') == $r['id'] ? 'selected' : '' ?>>
            <?= $r['name'] ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">Load</button>
    </div>
  </form>

  <?php
  if (isset($_GET['history_student'])):
      $sid = $_GET['history_student'];
      $history = mysqli_query($conn, "SELECT * FROM attendance WHERE member_id='$sid' ORDER BY date DESC");
  ?>
  <form method="POST">
    <input type="hidden" name="history_edit" value="1" />
    <input type="hidden" name="student_id" value="<?= $sid ?>" />
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($r = mysqli_fetch_assoc($history)): ?>
          <tr>
            <td><?= $r['date'] ?></td>
            <td>
              <select name="history_status[<?= $r['date'] ?>]" class="form-select form-select-sm">
                <option value="present" <?= $r['status'] == 'present' ? 'selected' : '' ?>>Present</option>
                <option value="absent" <?= $r['status'] == 'absent' ? 'selected' : '' ?>>Absent</option>
              </select>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <button type="submit" name="save_history" class="btn btn-success">Update History</button>
  </form>
  <?php endif; ?>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">✅ Attendance updated successfully!</div>
  <?php endif; ?>
</div>
</body>
</html>
