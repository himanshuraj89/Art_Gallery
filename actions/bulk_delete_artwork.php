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

if (!isset($_POST['artwork_ids']) || !is_array($_POST['artwork_ids'])) {
    $_SESSION['error'] = "No artworks selected";
    header("Location: ../pages/admin/artworks.php");
    exit();
}

$artwork_ids = $_POST['artwork_ids'];

try {
    $conn->begin_transaction();

    // Get image URLs for selected artworks
    $placeholders = str_repeat('?,', count($artwork_ids) - 1) . '?';
    $stmt = $conn->prepare("SELECT id, image_url FROM artworks WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('s', count($artwork_ids)), ...$artwork_ids);
    $stmt->execute();
    $artworks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Delete cart items
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE artwork_id IN ($placeholders)");
    $stmt->bind_param(str_repeat('s', count($artwork_ids)), ...$artwork_ids);
    $stmt->execute();

    // Update order items (set artwork_id to NULL)
    $stmt = $conn->prepare("UPDATE order_items SET artwork_id = NULL WHERE artwork_id IN ($placeholders)");
    $stmt->bind_param(str_repeat('s', count($artwork_ids)), ...$artwork_ids);
    $stmt->execute();

    // Delete artworks
    $stmt = $conn->prepare("DELETE FROM artworks WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('s', count($artwork_ids)), ...$artwork_ids);
    
    if ($stmt->execute()) {
        // Delete image files
        foreach ($artworks as $artwork) {
            $image_path = ".." . parse_url($artwork['image_url'], PHP_URL_PATH);
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $conn->commit();
        $_SESSION['success'] = "Selected artworks deleted successfully";
    } else {
        throw new Exception("Failed to delete artworks");
    }

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Error deleting artworks: " . $e->getMessage();
    error_log("Error bulk deleting artworks: " . $e->getMessage());
}

header("Location: ../pages/admin/artworks.php");
exit();
