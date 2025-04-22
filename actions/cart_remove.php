<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/public/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/public/cart.php");
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid form submission";
    header("Location: ../pages/public/cart.php");
    exit();
}

$cart_item_id = $_POST['cart_item_id'];
$user_id = $_SESSION['user_id'];

// Only allow users to remove their own cart items
$stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
$stmt->bind_param("ss", $cart_item_id, $user_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Item removed from cart";
} else {
    $_SESSION['error'] = "Failed to remove item from cart";
}

header("Location: ../pages/public/cart.php");
exit();
