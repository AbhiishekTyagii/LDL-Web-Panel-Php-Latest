<?php
$conn = mysqli_connect("localhost", "root", "", "ldl");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>