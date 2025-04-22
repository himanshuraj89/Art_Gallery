<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid form submission";
    header("Location: ../pages/admin/artworks.php");
    exit();
}

$artwork_id = $_POST['artwork_id'];

try {
    // Start transaction
    $conn->begin_transaction();

    // Check if artwork exists and get image URL
    $stmt = $conn->prepare("SELECT image_url FROM artworks WHERE id = ?");
    $stmt->bind_param("s", $artwork_id);
    $stmt->execute();
    $artwork = $stmt->get_result()->fetch_assoc();

    if (!$artwork) {
        throw new Exception("Artwork not found");
    }

    // Delete cart items first
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE artwork_id = ?");
    $stmt->bind_param("s", $artwork_id);
    $stmt->execute();

    // Check for and handle order items
    $stmt = $conn->prepare("UPDATE order_items SET artwork_id = NULL WHERE artwork_id = ?");
    $stmt->bind_param("s", $artwork_id);
    $stmt->execute();

    // Now delete the artwork
    $stmt = $conn->prepare("DELETE FROM artworks WHERE id = ?");
    $stmt->bind_param("s", $artwork_id);
    
    if ($stmt->execute()) {
        // Delete image file if it exists
        $image_path = ".." . parse_url($artwork['image_url'], PHP_URL_PATH);
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        
        $conn->commit();
        $_SESSION['success'] = "Artwork deleted successfully";
    } else {
        throw new Exception("Failed to delete artwork: " . $conn->error);
    }

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Error deleting artwork: " . $e->getMessage();
    error_log("Error deleting artwork ID $artwork_id: " . $e->getMessage());
}

header("Location: ../pages/admin/artworks.php");
exit();
