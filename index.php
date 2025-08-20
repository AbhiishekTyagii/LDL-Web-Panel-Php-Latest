<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass = md5($_POST['password']);

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$pass'");
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        if ($row['role'] == 'admin') {
            $_SESSION['admin_id'] = $row['id'];
            header("Location: admin/dashboard.php");
        } elseif ($row['role'] == 'student') {
            $_SESSION['student_id'] = $row['id'];
            header("Location: members/dashboard.php");
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Attendance Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container form-wrapper">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <h4 class="text-center mb-3">🔐 Login</h4>
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="admin@example.com" required />
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" value="admin123" required />
                    </div>
                    <button class="btn btn-primary w-100">Login</button>
                </form>
                <p class="text-center mt-3 text-muted small">
                    Use your registered email and password.<br>
                  
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>