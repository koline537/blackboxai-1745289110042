<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';

    if (empty($title) || empty($description)) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO concerns (user_id, title, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $description);
        if ($stmt->execute()) {
            $success = "Concern submitted successfully.";
        } else {
            $error = "Failed to submit concern. Please try again.";
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
    <title>Submit Concern - PLMUN Concern Hub</title>
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
        <h2 class="text-xl mb-4">Submit a Concern</h2>
        <?php if ($error): ?>
            <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
        <?php elseif ($success): ?>
            <p class="text-green-500 mb-4"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <form method="POST" class="bg-white bg-opacity-10 p-6 rounded-lg">
            <div class="mb-4">
                <label for="title" class="block mb-1">Title</label>
                <input type="text" id="title" name="title" required class="w-full p-2 rounded text-black" />
            </div>
            <div class="mb-4">
                <label for="description" class="block mb-1">Description</label>
                <textarea id="description" name="description" rows="5" required class="w-full p-2 rounded text-black"></textarea>
            </div>
            <button type="submit" class="bg-darkgreen hover:bg-green-800 text-white py-2 px-4 rounded transition">Submit</button>
        </form>
    </main>
    <footer class="p-4 text-center text-sm bg-white bg-opacity-10">
        &copy; 2024 PLMUN Concern Hub
    </footer>
</body>
</html>
