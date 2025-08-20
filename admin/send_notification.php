<?php
date_default_timezone_set('Asia/Kolkata');
include "../db.php"; // Your DB connection

// Get current day name like Monday, Tuesday
$currentDay = date('l');

// Fetch members scheduled for current day
$todayMembers = [];
$todayQuery = mysqli_query($conn, "SELECT name FROM users WHERE role != 'admin' AND day = '$currentDay'");

if (!$todayQuery) {
    die("Error fetching today's members: " . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($todayQuery)) {
    $todayMembers[] = $row['name'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_schedule'])) {
        $title = "Scheduled Members for " . $currentDay;
        if (!empty($todayMembers)) {
            $message = "The members scheduled for today ($currentDay) are:\n- " . implode("\n- ", $todayMembers);
        } else {
            $message = "There are no members scheduled for today ($currentDay).";
        }

        $content = ["en" => $message];
        $headings = ["en" => $title];

        $fields = [
            'app_id' => "ba7ad111-0192-486c-8610-af34a41fdb2c",
            'included_segments' => ['All'],
            'data' => ["source" => "admin_panel"],
            'headings' => $headings,
            'contents' => $content
        ];

        $fields = json_encode($fields);

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

        $result = json_decode($response, true);
        $success = isset($result['id']);
    } elseif (!empty($_POST['title']) && !empty($_POST['message'])) {
        $title = $_POST['title'];
        $message = $_POST['message'];

        $content = ["en" => $message];
        $headings = ["en" => $title];

        $fields = [
            'app_id' => "ba7ad111-0192-486c-8610-af34a41fdb2c",
            'included_segments' => ['All'],
            'data' => ["source" => "admin_panel"],
            'headings' => $headings,
            'contents' => $content
        ];

        $fields = json_encode($fields);

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

        error_log("OneSignal payload: " . $fields);
        $response = curl_exec($ch);
        error_log("OneSignal response: " . $response);
        curl_close($ch);

        $result = json_decode($response, true);
        $success = isset($result['id']);

        if ($success) {
            $firebaseDbUrl = "https://ldlattendencebarcode-default-rtdb.firebaseio.com/";
            $membersQuery = mysqli_query($conn, "SELECT id FROM users WHERE role != 'admin'");
            while ($member = mysqli_fetch_assoc($membersQuery)) {
                $memberId = $member['id'];
                $notificationData = [
                    'message' => $title . ': ' . $message,
                    'time' => round(microtime(true) * 1000)
                ];
                $firebaseUrl = "$firebaseDbUrl/$memberId.json";
                $chFirebase = curl_init();
                curl_setopt($chFirebase, CURLOPT_URL, $firebaseUrl);
                curl_setopt($chFirebase, CURLOPT_POST, 1);
                curl_setopt($chFirebase, CURLOPT_POSTFIELDS, json_encode($notificationData));
                curl_setopt($chFirebase, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($chFirebase, CURLOPT_RETURNTRANSFER, true);
                curl_exec($chFirebase);
                curl_close($chFirebase);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Send Notification</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f7f9fc;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      max-width: 700px;
      margin-top: 50px;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .btn-primary {
      background-color: #8692f7;
      border-color: #8692f7;
    }
    .btn-primary:hover {
      background-color: #6e7ae0;
      border-color: #6e7ae0;
    }
    .notification-preview {
      animation: fadeSlideIn 0.5s ease-out;
      border-left: 4px solid #8692f7;
    }

    @keyframes fadeSlideIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="card p-4">
    <h3 class="mb-4">📨 Send Notification</h3>
    <?php if (isset($success)): ?>
      <div class="alert alert-<?= $success ? 'success' : 'danger' ?>">
        <?= $success ? 'Notification sent successfully!' : 'Failed to send notification.' ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label for="title" class="form-label">Notification Title</label>
        <input type="text" name="title" id="title" class="form-control" />
      </div>
      <div class="mb-3">
        <label for="message" class="form-label">Message</label>
        <textarea name="message" id="message" class="form-control" rows="4"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Templates</label><br />
        <div class="d-flex flex-wrap gap-2">
          <button type="button" class="btn btn-outline-secondary btn-sm template-btn" data-title="Holiday Notice" data-message="Today is a holiday. Classes are suspended.">🎉 Holiday Notice</button>
          <button type="button" class="btn btn-outline-secondary btn-sm template-btn" data-title="Event Today" data-message="An event is scheduled today. Check the notice board for details.">📅 Event Today</button>
          <button type="button" class="btn btn-outline-secondary btn-sm template-btn" data-title="Class Cancelled" data-message="Your class has been cancelled for today.">❌ Class Cancelled</button>
          <button type="button" class="btn btn-outline-secondary btn-sm template-btn" data-title="Special Lecture" data-message="A special lecture is arranged today at 2:00 PM in Hall A.">📚 Special Lecture</button>
          <button type="button" class="btn btn-outline-secondary btn-sm template-btn" data-title="Exam Reminder" data-message="Reminder: Your exam is scheduled for tomorrow. Please be prepared.">📝 Exam Reminder</button>
        </div>
      </div>

      <hr class="my-4" />
      <h5 class="mb-3">📅 Members Scheduled for <?= $currentDay ?></h5>
      <?php if (count($todayMembers) > 0): ?>
        <ul class="list-group mb-4">
          <?php foreach ($todayMembers as $memberName): ?>
            <li class="list-group-item"><?= htmlspecialchars($memberName) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted mb-4">No members scheduled for today.</p>
      <?php endif; ?>

      <a href="scheduled_members.php" class="btn btn-outline-primary">🗓️ Manage Scheduled Members</a>

      <button type="submit" class="btn btn-primary mt-3">Send Notification</button>
      <a href="dashboard.php" class="btn btn-secondary ms-2 mt-3">Back to Dashboard</a>
      <button type="submit" name="send_schedule" class="btn btn-warning mt-3 ms-2">📬 Send Scheduled Members</button>
    </form>

    <hr class="my-4" />
    <h5 class="mb-3">📢 Notification Preview</h5>
    <div class="notification-preview border rounded bg-light shadow-sm p-3 position-relative" style="max-width: 100%;">
      <span class="badge bg-primary position-absolute top-0 end-0 m-2">Preview</span>
      <div class="d-flex align-items-center mb-2">
        <img src="https://cdn-icons-png.flaticon.com/512/1827/1827301.png" alt="bell" style="width: 24px; height: 24px;" class="me-2">
        <h6 class="mb-0 fw-bold text-dark" id="preview-title">[Title will appear here]</h6>
      </div>
      <p class="mb-1 text-dark" id="preview-message">[Message content will appear here]</p>
      <small class="text-muted">Sent via Admin Panel</small>
    </div>
  </div>
</div>

<script>
  const titleInput = document.getElementById('title');
  const messageInput = document.getElementById('message');
  const previewTitle = document.getElementById('preview-title');
  const previewMessage = document.getElementById('preview-message');

  titleInput.addEventListener('input', () => {
    previewTitle.textContent = titleInput.value || '[Title will appear here]';
  });

  messageInput.addEventListener('input', () => {
    previewMessage.textContent = messageInput.value || '[Message content will appear here]';
  });

  document.querySelectorAll('.template-btn').forEach(button => {
    button.addEventListener('click', () => {
      const title = button.getAttribute('data-title');
      const message = button.getAttribute('data-message');
      titleInput.value = title;
      messageInput.value = message;
      previewTitle.textContent = title;
      previewMessage.textContent = message;
    });
  });
</script>
</body>
</html>