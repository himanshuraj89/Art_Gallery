<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/public/login.php");
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid form submission";
    header("Location: ../pages/public/checkout.php");
    exit();
}

try {
    $conn->begin_transaction();
    
    $user_id = $_SESSION['user_id'];
    $order_id = gen_uuid();
    
    // Get cart items
    $query = "SELECT c.*, a.price, a.stock FROM cart_items c 
              JOIN artworks a ON c.artwork_id = a.id 
              WHERE c.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $cart_items = $stmt->get_result();
    
    if ($cart_items->num_rows === 0) {
        throw new Exception("Cart is empty");
    }
    
    $total_amount = 0;
    $order_items = [];
    
    // Validate stock and calculate total
    while ($item = $cart_items->fetch_assoc()) {
        if ($item['quantity'] > $item['stock']) {
            throw new Exception("Not enough stock for some items");
        }
        $total_amount += $item['price'] * $item['quantity'];
        $order_items[] = $item;
    }
    
    // Create order
    $query = "INSERT INTO orders (id, user_id, total_amount, status) VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssd", $order_id, $user_id, $total_amount);
    $stmt->execute();
    
    // Create order items and update stock
    foreach ($order_items as $item) {
        $item_id = gen_uuid();
        
        // Add order item
        $query = "INSERT INTO order_items (id, order_id, artwork_id, quantity, price_at_time) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssid", $item_id, $order_id, $item['artwork_id'], 
                         $item['quantity'], $item['price']);
        $stmt->execute();
        
        // Update stock
        $new_stock = $item['stock'] - $item['quantity'];
        $query = "UPDATE artworks SET stock = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $new_stock, $item['artwork_id']);
        $stmt->execute();
    }
    
    // Clear cart
    $query = "DELETE FROM cart_items WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    
    $conn->commit();
    $_SESSION['success'] = "Order placed successfully!";
    header("Location: ../pages/public/order_details.php?id=" . $order_id);
    
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Error processing order: " . $e->getMessage();
    header("Location: ../pages/public/checkout.php");
}
exit();
