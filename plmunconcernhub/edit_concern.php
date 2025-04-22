<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$concern_id = $_GET['id'] ?? null;

if (!$concern_id) {
    header("Location: view_concerns.php");
    exit();
}

$error = '';
$success = '';

// Fetch concern details
$stmt = $conn->prepare("SELECT title, description, status FROM concerns WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $concern_id, $user_id);
$stmt->execute();
$stmt->bind_result($title, $description, $status);
if (!$stmt->fetch()) {
    $stmt->close();
    header("Location: view_concerns.php");
    exit();
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_title = $_POST['title'] ?? '';
    $new_description = $_POST['description'] ?? '';
    $new_status = $_POST['status'] ?? 'Pending';

    if (empty($new_title) || empty($new_description)) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("UPDATE concerns SET title = ?, description = ?, status = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sssii", $new_title, $new_description, $new_status, $concern_id, $user_id);
        if ($stmt->execute()) {
            $success = "Concern updated successfully.";
            $title = $new_title;
            $description = $new_description;
            $status = $new_status;
        } else {
            $error = "Failed to update concern. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth" data-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Concern - PLMUN Concern Hub</title>
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
    <main class="flex-grow p-6 max-w-3xl mx-auto">
        <h2 class="text-xl mb-4">Edit Concern</h2>
        <?php if ($error): ?>
            <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
        <?php elseif ($success): ?>
            <p class="text-green-500 mb-4"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <form method="POST" class="bg-white bg-opacity-10 p-6 rounded-lg">
            <div class="mb-4">
                <label for="title" class="block mb-1">Title</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required class="w-full p-2 rounded text-black" />
            </div>
            <div class="mb-4">
                <label for="description" class="block mb-1">Description</label>
                <textarea id="description" name="description" rows="5" required class="w-full p-2 rounded text-black"><?= htmlspecialchars($description) ?></textarea>
            </div>
            <div class="mb-4">
                <label for="status" class="block mb-1">Status</label>
                <select id="status" name="status" class="w-full p-2 rounded text-black">
                    <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="In Progress" <?= $status === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Resolved" <?= $status === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                    <option value="Closed" <?= $status === 'Closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            <button type="submit" class="bg-darkgreen hover:bg-green-800 text-white py-2 px-4 rounded transition">Update</button>
            <a href="view_concerns.php" class="ml-4 underline">Back to Concerns</a>
        </form>
    </main>
    <footer class="p-4 text-center text-sm bg-white bg-opacity-10">
        &copy; 2024 PLMUN Concern Hub
    </footer>
</body>
</html>
