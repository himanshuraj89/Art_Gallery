<?php
session_start();
require_once '../config/database.php';

if (!isset($_POST['submit_reservation']) || !isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid form submission.";
    header("Location: ../pages/public/reservation.php");
    exit();
}

// Generate UUID for reservation
$reservation_id = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand(0, 0xffff), mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0x0fff) | 0x4000,
    mt_rand(0, 0x3fff) | 0x8000,
    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
);

$name = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$date = mysqli_real_escape_string($conn, $_POST['date']);
$time = mysqli_real_escape_string($conn, $_POST['time']);
$guests = (int)$_POST['guests'];
$notes = mysqli_real_escape_string($conn, isset($_POST['notes']) ? $_POST['notes'] : '');
$status = 'pending';

$stmt = $conn->prepare("INSERT INTO reservations (id, name, email, date, time, guests, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $reservation_id, $name, $email, $date, $time, $guests, $notes, $status);

if ($stmt->execute()) {
    $_SESSION['success'] = "Your reservation has been submitted successfully! We will contact you shortly to confirm.";
} else {
    $_SESSION['error'] = "Error submitting reservation: " . $stmt->error;
}

header("Location: ../pages/public/reservation.php");
exit();
