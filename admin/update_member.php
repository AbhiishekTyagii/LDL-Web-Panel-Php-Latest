<?php
include "../db.php";

if ($_GET['id']) {
    $member_id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id='$member_id'");
    $member = mysqli_fetch_assoc($result);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $enrollment_no = $_POST['enrollment_no'];
    $role = $_POST['role'];
    $query = "UPDATE users SET name='$name', email='$email', phone='$phone', enrollment_no='$enrollment_no', role='$role' WHERE id='$member_id'";
    if (mysqli_query($conn, $query)) {
        header("Location: dashboard.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <form method="POST" class="card p-4">
        <h4 class="mb-4 text-center">Update Member</h4>
        <input type="text" name="name" class="form-control mb-3" value="<?= $member['name'] ?>" placeholder="Full Name" required>
        <input type="email" name="email" class="form-control mb-3" value="<?= $member['email'] ?>" placeholder="Email Address" required>
        <input type="text" name="phone" class="form-control mb-3" value="<?= $member['phone'] ?>" placeholder="Phone Number" required>
        <input type="text" name="enrollment_no" class="form-control mb-3" value="<?= $member['enrollment_no'] ?>" placeholder="Enrollment ID" required>
        <select name="role" class="form-control mb-4" required>
            <option value="student" <?= $member['role'] == 'student' ? 'selected' : '' ?>>Student</option>
            <option value="admin" <?= $member['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
        <button type="submit" class="btn btn-primary w-100">Update Member</button>
    </form>
</div>
</body>
</html>