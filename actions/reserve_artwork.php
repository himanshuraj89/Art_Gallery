<?php
session_start();
require_once '../config/database.php';

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid form submission";
    header("Location: ../pages/public/gallery.php");
    exit();
}

$artwork_id = $_POST['artwork_id'];
$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
$message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

try {
    // Generate UUID for reservation
    $reservation_id = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );

    $stmt = $conn->prepare("INSERT INTO reservations (id, artwork_id, user_name, user_email, phone, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $reservation_id, $artwork_id, $name, $email, $phone, $message);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Your reservation has been submitted successfully! We will contact you soon.";
    } else {
        throw new Exception("Failed to submit reservation");
    }

} catch (Exception $e) {
    $_SESSION['error'] = "Error submitting reservation: " . $e->getMessage();
}

header("Location: ../pages/public/artwork.php?id=" . $artwork_id);
exit();
