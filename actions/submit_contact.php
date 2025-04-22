<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method';
    header('Location: ../pages/public/contact.php');
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = 'Invalid request';
    header('Location: ../pages/public/contact.php');
    exit();
}

// Store form data in session for form recovery
$_SESSION['form_data'] = $_POST;

// Validate inputs
$name = trim($_POST['name'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || !$email || empty($subject) || empty($message)) {
    $_SESSION['error'] = 'All fields are required and must be valid';
    header('Location: ../pages/public/contact.php');
    exit();
}

try {
    // Insert into database
    $query = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Database preparation failed: " . $conn->error);
    }
    
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    
    if ($stmt->execute()) {
        $_SESSION['contact_success'] = 'Thank you for your message. We will get back to you soon!';
        unset($_SESSION['form_data']);
    } else {
        throw new Exception("Database error: " . $stmt->error);
    }
    
    $stmt->close();
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

$conn->close();
header('Location: ../pages/public/contact.php');
exit();
