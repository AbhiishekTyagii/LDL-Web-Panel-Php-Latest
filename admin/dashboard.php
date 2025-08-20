<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

$searchQuery = "";
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_POST['search']);
    $searchQuery = "AND (name LIKE '%$searchTerm%' OR enrollment_no LIKE '%$searchTerm%')";
}

$query = "SELECT * FROM users WHERE role='student'";
if (!empty($searchQuery)) {
    $query .= " $searchQuery";
}
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container mt-5">
    <div class="card p-3 mb-4 shadow-sm">
        <h4 class="fw-bold mb-3">📋 Admin Dashboard</h4>
        <div class="d-flex flex-wrap gap-2">
            <a href="add_member.php" class="btn btn-success flex-fill">➕ Add Member</a>
            <a href="report_page.php" class="btn btn-info flex-fill">📊 View Reports</a>
            <a href="progress_tracking.php" class="btn btn-primary flex-fill">📈 Progress</a> 
            <a href="edit_attendance.php" class="btn btn-secondary flex-fill">✏️ Edit Att</a>
            <a href="all_members.php" class="btn btn-dark flex-fill">👥 All Members</a>
            <a href="send_notification.php" class="btn btn-info flex-fill">🔔 Send Notifications</a>
            <a href="../logout.php" class="btn btn-danger flex-fill">Logout</a>
        </div>
    </div>


    <form method="POST" class="mb-4 d-flex gap-2 flex-wrap">
        <input type="text" class="form-control flex-grow-1" name="search" placeholder="Search by name or enrollment no" value="<?= htmlspecialchars($_POST['search'] ?? '') ?>" />
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <!-- Bulk Date Picker -->
    <div class="card mb-4">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <h5 class="mb-0">📅 Select Date to Mark Attendance:</h5>
            <input type="date" id="bulk_date" class="form-control" style="max-width: 250px;" value="<?= date('Y-m-d') ?>" required />
            <span class="text-muted">This date will be applied to each student's attendance below.</span>
        </div>
    </div>

    <!-- Attendance Success Alert -->
    <div id="attendanceAlert" class="alert alert-success d-none" role="alert">
      ✅ Attendance marked successfully!
    </div>

    <form method="POST" action="mark_attendance.php">
        <div class="table-responsive">
            <table class="table table-striped bg-white">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Enrollment No</th>
                        <th>Attendance %</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                        $totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM attendance WHERE member_id='{$row['id']}'");
                        $total = mysqli_fetch_assoc($totalQuery)['total'];

                        $presentQuery = mysqli_query($conn, "SELECT COUNT(*) as present FROM attendance WHERE member_id='{$row['id']}' AND status='present'");
                        $present = mysqli_fetch_assoc($presentQuery)['present'];

                        $percent = ($total > 0) ? round(($present / $total) * 100, 2) : 0;
                    ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['enrollment_no'] ?></td>
                        <td><span class="badge bg-info text-dark"><?= $percent ?>%</span></td>
                        <td>
                            <select name="status[<?= $row['id'] ?>]" class="form-select">
                                <option value="">--Select--</option>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="member_id[<?= $row['id'] ?>]" value="<?= $row['id'] ?>" />
                            <input type="hidden" name="date" value="<?= date('Y-m-d') ?>" class="selected-date" />
                            <button type="submit" class="btn btn-success" onclick="return attachDate(this)">Mark</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <hr class="my-4">
        <h4 class="mb-3">🎓 Manage Members</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Name</th>
                        <th>Enrollment No</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $members = mysqli_query($conn, "SELECT * FROM users WHERE role='student'");
                    while ($row = mysqli_fetch_assoc($members)): ?>
                        <tr>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['enrollment_no'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= ucfirst($row['role']) ?></td>
                            <td><a href="update_member.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Update</a></td>
                            <td><a href="delete_member.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this member?');">Delete</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

</script>
<script>
function attachDate(button) {
    const bulkDate = document.getElementById("bulk_date").value;
    if (!bulkDate) {
        alert("Please select a date first.");
        return false;
    }
    const form = button.closest("form");
    form.querySelectorAll(".selected-date").forEach(function(input) {
        input.value = bulkDate;
    });
    return true;
}
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("attendance") === "success") {
      const alertBox = document.getElementById("attendanceAlert");
      if (alertBox) {
        alertBox.classList.remove("d-none");
        alertBox.classList.add("show");
        setTimeout(() => {
          alertBox.classList.add("d-none");
          window.history.replaceState({}, document.title, window.location.pathname);
        }, 4000);
      }
    }
  });
</script>
</html>