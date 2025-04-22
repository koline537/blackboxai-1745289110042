<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$concern_id = $_GET['concern_id'] ?? null;

if (!$concern_id) {
    header("Location: view_concerns.php");
    exit();
}

$error = '';
$success = '';

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $_POST['comment'] ?? '';
    if (empty($comment)) {
        $error = "Comment cannot be empty.";
    } else {
        $stmt = $conn->prepare("INSERT INTO comments (concern_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $concern_id, $user_id, $comment);
        if ($stmt->execute()) {
            $success = "Comment added successfully.";
        } else {
            $error = "Failed to add comment. Please try again.";
        }
        $stmt->close();
    }
}

// Fetch concern details
$stmt = $conn->prepare("SELECT title, description FROM concerns WHERE id = ?");
$stmt->bind_param("i", $concern_id);
$stmt->execute();
$stmt->bind_result($title, $description);
$stmt->fetch();
$stmt->close();

// Fetch comments
$stmt = $conn->prepare("SELECT c.comment, u.username, c.created_at FROM comments c JOIN users u ON c.user_id = u.id WHERE c.concern_id = ? ORDER BY c.created_at ASC");
$stmt->bind_param("i", $concern_id);
$stmt->execute();
$result = $stmt->get_result();
$comments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth" data-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Comments - PLMUN Concern Hub</title>
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
        <h2 class="text-xl mb-2">Comments for: <?= htmlspecialchars($title) ?></h2>
        <p class="mb-6"><?= nl2br(htmlspecialchars($description)) ?></p>
        <?php if ($error): ?>
            <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
        <?php elseif ($success): ?>
            <p class="text-green-500 mb-4"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <form method="POST" class="mb-6 bg-white bg-opacity-10 p-4 rounded-lg">
            <textarea name="comment" rows="3" required class="w-full p-2 rounded text-black" placeholder="Add a comment..."></textarea>
            <button type="submit" class="mt-2 bg-darkgreen hover:bg-green-800 text-white py-2 px-4 rounded transition">Submit Comment</button>
        </form>
        <div>
            <?php if (empty($comments)): ?>
                <p>No comments yet.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($comments as $c): ?>
                        <li class="mb-4 border-b border-white border-opacity-20 pb-2">
                            <p class="font-semibold"><?= htmlspecialchars($c['username']) ?> <span class="text-xs text-gray-300"><?= htmlspecialchars($c['created_at']) ?></span></p>
                            <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <a href="view_concerns.php" class="underline mt-6 inline-block">Back to Concerns</a>
    </main>
    <footer class="p-4 text-center text-sm bg-white bg-opacity-10">
        &copy; 2024 PLMUN Concern Hub
    </footer>
</body>
</html>
