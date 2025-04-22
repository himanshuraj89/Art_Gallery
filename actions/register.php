<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/public/register.php");
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid form submission";
    header("Location: ../pages/public/register.php");
    exit();
}

$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Validation
if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
    $_SESSION['error'] = "All fields are required";
    header("Location: ../pages/public/register.php");
    exit();
}

if ($password !== $confirm_password) {
    $_SESSION['error'] = "Passwords do not match";
    header("Location: ../pages/public/register.php");
    exit();
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $_SESSION['error'] = "Email already registered";
    header("Location: ../pages/public/register.php");
    exit();
}

// Create user
$id = gen_uuid();
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = 'user';

$stmt = $conn->prepare("INSERT INTO users (id, name, email, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $id, $name, $email, $hashed_password, $role);

if ($stmt->execute()) {
    $_SESSION['user_id'] = $id;
    $_SESSION['role'] = $role;
    header("Location: ../index.php");
} else {
    $_SESSION['error'] = "Registration failed";
    header("Location: ../pages/public/register.php");
}
exit();
