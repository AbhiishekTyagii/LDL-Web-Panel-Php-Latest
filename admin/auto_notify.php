

<?php
date_default_timezone_set('Asia/Kolkata');
include "../db.php";

$currentDay = date('l');
$todayMembers = [];
$todayQuery = mysqli_query($conn, "SELECT name FROM users WHERE role != 'admin' AND day = '$currentDay'");
while ($row = mysqli_fetch_assoc($todayQuery)) {
    $todayMembers[] = $row['name'];
}

$title = "Scheduled Members for " . $currentDay;
$message = !empty($todayMembers)
    ? "The members scheduled for today ($currentDay) are:\n- " . implode("\n- ", $todayMembers)
    : "There are no members scheduled for today ($currentDay).";

$fields = json_encode([
    'app_id' => "ba7ad111-0192-486c-8610-af34a41fdb2c",
    'included_segments' => ['All'],
    'data' => ["source" => "cron_job"],
    'headings' => ["en" => $title],
    'contents' => ["en" => $message]
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json; charset=utf-8',
    'Authorization: Basic os_v2_app_xj5nceibsjegzbqqv42kih63fr5ksvupnduefd4ymz7thoz5wiiikwdw46wfefd7bypfdh7lz4tpgpsbk4o3vqbj6js6iqfc5m375fy'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
$response = curl_exec($ch);
curl_close($ch);

// Optional: Log response or output for debugging
file_put_contents("auto_notify_log.txt", date('Y-m-d H:i:s') . " Response: " . $response . PHP_EOL, FILE_APPEND);