<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/public/reset_password.php");
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid form submission";
    header("Location: ../pages/public/reset_password.php");
    exit();
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

// Verify email exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $_SESSION['error'] = "If this email exists, you will receive a password reset link";
    header("Location: ../pages/public/reset_password.php");
    exit();
}

// Generate reset token
$token = bin2hex(random_bytes(32));
$expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Store reset token
$stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $token, $expires_at);
$stmt->execute();

// Send email (implement your email sending logic here)
$reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/newf/pages/public/new_password.php?token=" . $token;

// For development, just show the link
$_SESSION['success'] = "Password reset link has been sent to your email. For development: " . $reset_link;
header("Location: ../pages/public/reset_password.php");
exit();
