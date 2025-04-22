<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mark all notifications as read
if (isset($_GET['mark_read'])) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: notifications.php");
    exit();
}

// Fetch notifications
$stmt = $conn->prepare("SELECT id, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth" data-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Notifications - PLMUN Concern Hub</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="style.css" />
</head>
<body class="min-h-screen flex flex-col">
    <header class="flex items-center justify-between p-4 bg-white bg-opacity-10">
        <h1 class="text-2xl font-bold">PLMUN Concern Hub</h1>
        <div class="flex items-center space-x-4">
            <i class="fas fa-user-circle text-3xl cursor-pointer" title="User Profile"></i>
            <a href="logout.php" class="underline">Logout</a>
        </div>
    </header>
    <main class="flex-grow p-6 max-w-4xl mx-auto">
        <h2 class="text-xl mb-4">Notifications</h2>
        <a href="notifications.php?mark_read=1" class="underline mb-4 inline-block">Mark all as read</a>
        <?php if (empty($notifications)): ?>
            <p>No notifications.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($notifications as $notification): ?>
                    <li class="mb-4 p-4 rounded <?= $notification['is_read'] ? 'bg-white bg-opacity-10' : 'bg-green-700' ?>">
                        <p><?= nl2br(htmlspecialchars($notification['message'])) ?></p>
                        <p class="text-xs text-gray-300"><?= htmlspecialchars($notification['created_at']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a href="profile.php" class="underline mt-6 inline-block">Back to Profile</a>
    </main>
    <footer class="p-4 text-center text-sm bg-white bg-opacity-10">
        &copy; 2024 PLMUN Concern Hub
    </footer>
</body>
</html>
