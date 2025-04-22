<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in (you may need to adjust this based on your authentication system)
// This is a placeholder - replace with your actual authentication check
if(!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check for POST request with required parameters
if(isset($_POST['id']) && isset($_POST['status'])) {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $status = $_POST['status'] === 'active' ? 1 : 0;
    
    if(!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid subscriber ID']);
        exit();
    }
    
    try {
        // Update the subscriber status
        $update_query = "UPDATE newsletter_subscribers SET is_active = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ii", $status, $id);
        
        if($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Subscriber status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update subscriber status']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred']);
        error_log("Newsletter update error: " . $e->getMessage());
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
}
?>
