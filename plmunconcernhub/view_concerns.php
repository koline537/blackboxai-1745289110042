<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch concerns for the logged-in user
$stmt = $conn->prepare("SELECT id, title, description, status, created_at, updated_at FROM concerns WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$concerns = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth" data-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Concerns - PLMUN Concern Hub</title>
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
        <h2 class="text-xl mb-4">Your Concerns</h2>
        <?php if (empty($concerns)): ?>
            <p>No concerns submitted yet.</p>
        <?php else: ?>
            <table class="w-full text-left border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-white bg-opacity-20">
                        <th class="border border-gray-300 p-2">Title</th>
                        <th class="border border-gray-300 p-2">Status</th>
                        <th class="border border-gray-300 p-2">Created At</th>
                        <th class="border border-gray-300 p-2">Updated At</th>
                        <th class="border border-gray-300 p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($concerns as $concern): ?>
                        <tr class="border border-gray-300">
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($concern['title']) ?></td>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($concern['status']) ?></td>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($concern['created_at']) ?></td>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($concern['updated_at']) ?></td>
                            <td class="border border-gray-300 p-2 space-x-2">
                                <a href="edit_concern.php?id=<?= $concern['id'] ?>" class="text-blue-400 underline">Edit</a>
                                <a href="delete_concern.php?id=<?= $concern['id'] ?>" class="text-red-400 underline" onclick="return confirm('Are you sure you want to delete this concern?');">Delete</a>
                                <a href="comments.php?concern_id=<?= $concern['id'] ?>" class="text-green-400 underline">Comments</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
    <footer class="p-4 text-center text-sm bg-white bg-opacity-10">
        &copy; 2024 PLMUN Concern Hub
    </footer>
</body>
</html>
