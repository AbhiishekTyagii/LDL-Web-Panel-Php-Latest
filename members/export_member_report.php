<?php
session_start();
include "../db.php";

if (!isset($_SESSION['student_id']) || !isset($_GET['export_type'])) {
    die("Unauthorized or missing parameters.");
}

$student_id = $_SESSION['student_id'];
$type = $_GET['export_type'];

// Fetch student data
$userQuery = mysqli_query($conn, "SELECT name, enrollment_no FROM users WHERE id='$student_id'");
$user = mysqli_fetch_assoc($userQuery);

// Fetch attendance
$attendanceQuery = mysqli_query($conn, "SELECT date, status FROM attendance WHERE member_id='$student_id' ORDER BY date DESC");

if ($type === "csv") {
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=My_Attendance_Report.csv");

    $output = fopen("php://output", "w");
    fputcsv($output, ["Name", $user['name']]);
    fputcsv($output, ["Enrollment No", $user['enrollment_no']]);
    fputcsv($output, []);
    fputcsv($output, ["Date", "Status"]);

    while ($row = mysqli_fetch_assoc($attendanceQuery)) {
        fputcsv($output, [$row['date'], ucfirst($row['status'])]);
    }

    fclose($output);
    exit();
// } elseif ($type === "pdf") {
//     require_once('../admin/tcpdf/tcpdf.php');

//     $pdf = new TCPDF();
//     $pdf->AddPage();
//     $pdf->SetFont('helvetica', '', 12);

//     $html = "<h2>My Attendance Report</h2>";
//     $html .= "<strong>Name:</strong> " . htmlspecialchars($user['name']) . "<br>";
//     $html .= "<strong>Enrollment No:</strong> " . $user['enrollment_no'] . "<br><br>";
//     $html .= "<table border=\"1\" cellpadding=\"6\">
//                 <thead>
//                     <tr><th>Date</th><th>Status</th></tr>
//                 </thead><tbody>";

//     while ($row = mysqli_fetch_assoc($attendanceQuery)) {
//         $html .= "<tr><td>{$row['date']}</td><td>" . ucfirst($row['status']) . "</td></tr>";
//     }

//     $html .= "</tbody></table>";

//     $pdf->writeHTML($html, true, false, true, false, '');
//     $pdf->Output("My_Attendance_Report.pdf", 'D');
//     exit();
// } else {
//     die("Invalid export type.");
// }
}
elseif ($type === "pdf") {
    require_once('../admin/tcpdf/tcpdf.php'); // make sure path is correct

    // Calculate attendance stats
    $totalDaysQuery = mysqli_query($conn, "SELECT COUNT(*) as total_days FROM attendance WHERE member_id='$student_id'");
    $totalDays = mysqli_fetch_assoc($totalDaysQuery)['total_days'] ?? 0;

    $presentDaysQuery = mysqli_query($conn, "SELECT COUNT(*) as present_days FROM attendance WHERE member_id='$student_id' AND status='present'");
    $presentDays = mysqli_fetch_assoc($presentDaysQuery)['present_days'] ?? 0;

    $attendancePercentage = ($totalDays > 0) ? round(($presentDays / $totalDays) * 100, 2) : 0;

    // Fetch full user info including email and phone
    $fullUserQuery = mysqli_query($conn, "SELECT name, enrollment_no, email, phone FROM users WHERE id='$student_id'");
    $fullUser = mysqli_fetch_assoc($fullUserQuery);

    class MYPDF extends TCPDF {
        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 10);
            $this->Cell(0, 10, '© Light De Literacy', 0, false, 'C');
        }
    }

    $pdf = new MYPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    // Current date & time for export
    date_default_timezone_set('Asia/Kolkata');
    $exportDateTime = date('Y-m-d H:i:s');

    $html = "<h2>My Attendance Report</h2>";
    $html .= "<strong>Exported On:</strong> {$exportDateTime}<br><br>";

    $html .= "<strong>Name:</strong> " . htmlspecialchars($fullUser['name']) . "<br>";
    $html .= "<strong>Enrollment No:</strong> " . $fullUser['enrollment_no'] . "<br>";
    $html .= "<strong>Email:</strong> " . htmlspecialchars($fullUser['email']) . "<br>";
    $html .= "<strong>Phone:</strong> " . htmlspecialchars($fullUser['phone']) . "<br><br>";

    $html .= "<strong>Total Days:</strong> {$totalDays} | <strong>Present Days:</strong> {$presentDays} | <strong>Attendance Percentage:</strong> {$attendancePercentage}%<br><br>";

    $html .= "<table border=\"1\" cellpadding=\"6\">
                <thead>
                    <tr><th>Date</th><th>Status</th></tr>
                </thead><tbody>";

//     while ($row = mysqli_fetch_assoc($attendanceQuery)) {
//     $status = ucfirst($row['status']);
//     $color = ($row['status'] === 'present') ? 'green' : 'red';

//     $html .= "<tr>
//                 <td>{$row['date']}</td>
//                 <td style='color: {$color}; font-weight: bold;'>{$status}</td>
//               </tr>";
// }

while ($row = mysqli_fetch_assoc($attendanceQuery)) {
    $status = ucfirst($row['status']);

    if ($row['status'] === 'present') {
        $html .= "<tr>
                    <td style='background-color: #e6f4ea; color: #2e7d32; font-weight: bold;'>{$row['date']}</td>
                    <td style='background-color: #4CAF50; color: white; font-weight: bold;'>{$status}</td>
                  </tr>";
    } else {
        $html .= "<tr>
                    <td style='background-color: #fdecea; color: #b71c1c; font-weight: bold;'>{$row['date']}</td>
                    <td style='background-color: #f44336; color: white; font-weight: bold;'>{$status}</td>
                  </tr>";
    }

}
    $html .= "</tbody></table>";

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output("My_Attendance_Report.pdf", 'D');
    exit();

}