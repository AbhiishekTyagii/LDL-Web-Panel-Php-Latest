

<?php
session_start();
include "../db.php";

if (!isset($_GET['id'])) {
  die("No user ID specified.");
}

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
if (!$query || mysqli_num_rows($query) !== 1) {
  die("User not found.");
}

$_SESSION['student_id'] = $id;
header("Location: ../members/dashboard.php");
exit();