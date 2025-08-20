<?php
include "../db.php";

$searchTerm = $_GET['search'] ?? '';

$filter = "WHERE role != 'admin'";
if (!empty($searchTerm)) {
  $escaped = mysqli_real_escape_string($conn, $searchTerm);
  $filter .= " AND (name LIKE '%$escaped%' OR enrollment_no LIKE '%$escaped%')";
}

$query = "SELECT * FROM users $filter ORDER BY name ASC";
$result = mysqli_query($conn, $query);
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
      border-collapse: separate;
      border-spacing: 0;
      width: 100%;
      font-size: 0.875rem;
      border-radius: 12px;
      overflow: hidden;
    }

    .table-responsive {
      border-radius: 12px;
      overflow: hidden;
    }

    .table thead th:first-child {
      border-top-left-radius: 12px;
    }

    .table thead th:last-child {
      border-top-right-radius: 12px;
    }

    .btn {
      border-radius: 12px !important;
    }

    .table-striped tbody tr:nth-child(odd) {
      background-color: #ffffff;
    }

    .table-striped tbody tr:nth-child(even) {
      background-color: #f7f9fc;
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

    .table thead th {
      background-color: #8692f7 !important;
      color: white;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      border: none;
      padding: 16px 20px;
      position: sticky;
      top: 0;
      z-index: 1;
    }
    .btn-primary {
      background-color: #8692f7;
      border-color: #8692f7;
    }
    .btn-primary:hover {
      background-color: #6e7ae0;
      border-color: #6e7ae0;
    }
    .btn-info {
      background-color: #a6aefc;
      border-color: #a6aefc;
      color: white;
    }
    .btn-info:hover {
      background-color: #7f87dd;
      border-color: #7f87dd;
    }
  </style>
</head>
<body>
<div class="container mt-4">
  <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
  <h3 class="mb-3">👥 All Members</h3>

  <form method="GET" class="mb-4 d-flex gap-2">
    <input type="text" class="form-control" name="search" placeholder="Search by name or enrollment no" value="<?= htmlspecialchars($searchTerm) ?>" />
    <button type="submit" class="btn btn-primary px-4 py-2">Search</button>
  </form>

  <?php if (mysqli_num_rows($result) > 0): ?>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Name</th>
            <th>Enrollment No</th>
            <th>Email</th>
            <th>Phone</th>
            <th>View</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['enrollment_no']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= htmlspecialchars($row['phone']) ?></td>
              <td>
                <a href="member_details.php?id=<?= $row['id'] ?>" class="btn btn-info px-3 py-2">Details</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-warning">No members found.</div>
  <?php endif; ?>
</div>
</body>
</html>