<?php
include "../db.php";

if (!isset($_GET['enrollment_no']) || !isset($_GET['start_date']) || !isset($_GET['end_date']) || !isset($_GET['export_type'])) {
    die("Missing parameters.");
}

$enrollment = $_GET['enrollment_no'];
$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];
$type = $_GET['export_type'];

// Fetch student info
$studentQuery = mysqli_query($conn, "SELECT id, name FROM users WHERE enrollment_no='$enrollment'");
$student = mysqli_fetch_assoc($studentQuery);
$student_id = $student['id'] ?? null;

if (!$student_id) {
    die("Student not found.");
}

// Fetch attendance data
$attendanceQuery = mysqli_query($conn, "SELECT date, status FROM attendance WHERE member_id='$student_id' AND date BETWEEN '$startDate' AND '$endDate'");

if ($type === "csv") {
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename={$enrollment}_attendance.csv");

    $output = fopen("php://output", "w");
    fputcsv($output, ["Student Name", $student['name']]);
    fputcsv($output, []); // empty line
    fputcsv($output, ["Date", "Status"]);

    while ($row = mysqli_fetch_assoc($attendanceQuery)) {
        fputcsv($output, [$row['date'], ucfirst($row['status'])]);
    }

    fclose($output);
    exit();
}  elseif ($type === "pdf") {
    require_once('tcpdf/tcpdf.php'); // make sure TCPDF is installed in 'tcpdf/' folder

    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Attendance System');
    $pdf->SetTitle("Attendance Report - $enrollment");
    $pdf->AddPage();

    $pdf->SetFont('helvetica', '', 12);

    $html = "<h2>Attendance Report</h2>";
    $html .= "<strong>Name:</strong> " . htmlspecialchars($student['name']). "<br>";
    $html .= "<strong>Enrollment No:</strong> $enrollment<br>";
    $html .= "<strong>From:</strong> $startDate <strong>To:</strong> $endDate<br><br>";

    $html .= "<table border=\"1\" cellpadding=\"6\">
                <thead>
                    <tr>
                        <th><strong>Date</strong></th>
                        <th><strong>Status</strong></th>
                    </tr>
                </thead><tbody>";

    while ($row = mysqli_fetch_assoc($attendanceQuery)) {
        $html .= "<tr>
                    <td>{$row['date']}</td>
                    <td>" . ucfirst($row['status']) . "</td>
                  </tr>";
    }

    $html .= "</tbody></table>";

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output("attendance_report_$enrollment.pdf", 'D');
    exit();
}