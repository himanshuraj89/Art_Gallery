<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/admin/orders.php");
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid form submission";
    header("Location: ../pages/admin/orders.php");
    exit();
}

$order_id = $_POST['order_id'];
$status = $_POST['status'];

// Validate status
$valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
if (!in_array($status, $valid_statuses)) {
    $_SESSION['error'] = "Invalid status";
    header("Location: ../pages/admin/order_details.php?id=" . $order_id);
    exit();
}

// Update order status
$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("ss", $status, $order_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Order status updated successfully";
} else {
    $_SESSION['error'] = "Failed to update order status";
}

header("Location: ../pages/admin/order_details.php?id=" . $order_id);
exit();
