<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/public/login.php");
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid form submission";
    header("Location: ../pages/public/login.php");
    exit();
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

if (empty($email) || empty($password)) {
    $_SESSION['error'] = "All fields are required";
    header("Location: ../pages/public/login.php");
    exit();
}

$stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    
    if ($user['role'] === 'admin') {
        header("Location: ../pages/admin/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
} else {
    $_SESSION['error'] = "Invalid email or password";
    header("Location: ../pages/public/login.php");
}
exit();
