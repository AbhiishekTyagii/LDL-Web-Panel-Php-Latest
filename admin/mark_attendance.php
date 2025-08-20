<link rel="stylesheet" href="style.css">
<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];  // Get the date from the form
    $statuses = $_POST['status'];  // Get the status (present or absent)
    $member_ids = $_POST['member_id'];  // Get member IDs for all students

    // Check if date is empty
    if (empty($date)) {
        echo "<script>alert('Date is missing. Please select a valid date.');window.location.href='dashboard.php';</script>";
        exit();
    }
    
    // Loop through all students
    foreach ($member_ids as $id) {
        // Check if status is set for the student
        $status = isset($statuses[$id]) ? $statuses[$id] : null;

        if ($status) {
            // Check if attendance is already marked for the student and date
            $check = mysqli_query($conn, "SELECT * FROM attendance WHERE member_id='$id' AND date='$date'");
            if (mysqli_num_rows($check) == 0) {
                // Insert attendance if it's not already marked
                $insert = mysqli_query($conn, "INSERT INTO attendance (member_id, date, status) VALUES ('$id', '$date', '$status')");
                if (!$insert) {
                    echo "Error: " . mysqli_error($conn); // Debugging if insertion fails
                }
            }
        }
    }

    // Successful insertion
    echo "<script>alert('Attendance saved successfully!');window.location.href='dashboard.php';</script>";
}
?>