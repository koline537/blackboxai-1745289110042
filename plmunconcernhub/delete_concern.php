<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$concern_id = $_GET['id'] ?? null;

if ($concern_id) {
    $stmt = $conn->prepare("DELETE FROM concerns WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $concern_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: view_concerns.php");
exit();
?>
