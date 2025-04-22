<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/public/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/public/gallery.php");
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid form submission";
    header("Location: ../pages/public/gallery.php");
    exit();
}

$artwork_id = $_POST['artwork_id'];
$quantity = (int)$_POST['quantity'];
$user_id = $_SESSION['user_id'];

// Verify artwork exists and has enough stock
$stmt = $conn->prepare("SELECT stock FROM artworks WHERE id = ?");
$stmt->bind_param("s", $artwork_id);
$stmt->execute();
$result = $stmt->get_result();
$artwork = $result->fetch_assoc();

if (!$artwork || $artwork['stock'] < $quantity) {
    $_SESSION['error'] = "Item is out of stock";
    header("Location: ../pages/public/artwork.php?id=" . $artwork_id);
    exit();
}

// Check if item already in cart
$stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND artwork_id = ?");
$stmt->bind_param("ss", $user_id, $artwork_id);
$stmt->execute();
$cart_item = $stmt->get_result()->fetch_assoc();

if ($cart_item) {
    // Update quantity
    $new_quantity = $cart_item['quantity'] + $quantity;
    if ($new_quantity > $artwork['stock']) {
        $_SESSION['error'] = "Cannot add more of this item";
        header("Location: ../pages/public/artwork.php?id=" . $artwork_id);
        exit();
    }

    $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    $stmt->bind_param("is", $new_quantity, $cart_item['id']);
} else {
    // Add new item
    $cart_item_id = gen_uuid();
    $stmt = $conn->prepare("INSERT INTO cart_items (id, user_id, artwork_id, quantity) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $cart_item_id, $user_id, $artwork_id, $quantity);
}

if ($stmt->execute()) {
    header("Location: ../pages/public/cart.php");
} else {
    $_SESSION['error'] = "Failed to add item to cart";
    header("Location: ../pages/public/artwork.php?id=" . $artwork_id);
}
exit();
