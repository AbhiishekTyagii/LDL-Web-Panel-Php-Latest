<?php
include "../db.php";

if ($_GET['id']) {
    $member_id = $_GET['id'];
    $deleteQuery = mysqli_query($conn, "DELETE FROM users WHERE id='$member_id'");
    if ($deleteQuery) {
        header("Location: admin_dashboard.php");
    }
}
?>