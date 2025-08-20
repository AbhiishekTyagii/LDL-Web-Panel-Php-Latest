<?php
include "../db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $phone = $_POST['phone'];
    $enrollment_no = $_POST['enrollment_no'];
    $role = $_POST['role'];

    $query = "INSERT INTO users (name, email, password, phone, enrollment_no, role) VALUES ('$name', '$email', '$password', '$phone', '$enrollment_no', '$role')";
    if (mysqli_query($conn, $query)) {
        // Redirect after successfully inserting the new member
        echo "<script>
                alert('Member added successfully!');
                window.location.href = 'dashboard.php';  // Redirect to the dashboard
              </script>";
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
    <title>Add Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css"> <!-- Link the CSS file -->
</head>
<body class="bg-light">
<div class="container mt-5">
    <form method="POST" class="card p-4">
        <h4 class="mb-4 text-center">Add New Member</h4>
        <input type="text" name="name" class="form-control mb-3" placeholder="Full Name" required>
        <input type="email" name="email" class="form-control mb-3" placeholder="Email Address" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
        <input type="text" name="phone" class="form-control mb-3" placeholder="Phone Number" required>
        <input type="text" name="enrollment_no" class="form-control mb-3" placeholder="Enrollment ID" required>
        <select name="role" class="form-control mb-4" required>
            <option value="student">Member</option>
           
        </select>
        <button type="submit" class="btn btn-primary w-100">Add Member</button>
    </form>
</div>

<!-- Toast notification (Bootstrap 5) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>