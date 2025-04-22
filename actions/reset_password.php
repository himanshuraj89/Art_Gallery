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

$token = $_POST['token'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if ($password !== $confirm_password) {
    $_SESSION['error'] = "Passwords do not match";
    header("Location: ../pages/public/new_password.php?token=" . $token);
    exit();
}

try {
    $conn->begin_transaction();

    // Get email from valid token
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW() AND used = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Invalid or expired reset link");
    }
    
    $email = $result->fetch_assoc()['email'];
    
    // Update password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);
    $stmt->execute();
    
    // Mark token as used
    $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    
    $conn->commit();
    $_SESSION['success'] = "Password has been reset successfully";
    header("Location: ../pages/public/login.php");
    
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = $e->getMessage();
    header("Location: ../pages/public/reset_password.php");
}
exit();
