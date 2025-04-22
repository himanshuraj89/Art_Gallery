<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/public/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/public/change_password.php");
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid form submission";
    header("Location: ../pages/public/change_password.php");
    exit();
}

$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Verify passwords match
if ($new_password !== $confirm_password) {
    $_SESSION['error'] = "New passwords do not match";
    header("Location: ../pages/public/change_password.php");
    exit();
}

// Get user's current password
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Verify current password
if (!password_verify($current_password, $user['password'])) {
    $_SESSION['error'] = "Current password is incorrect";
    header("Location: ../pages/public/change_password.php");
    exit();
}

// Update password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("ss", $hashed_password, $_SESSION['user_id']);

if ($stmt->execute()) {
    $_SESSION['success'] = "Password updated successfully";
} else {
    $_SESSION['error'] = "Failed to update password";
}

header("Location: ../pages/public/change_password.php");
exit();
