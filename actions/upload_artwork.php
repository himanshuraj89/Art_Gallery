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

$artwork_id = $_POST['artwork_id'] ?: gen_uuid();
$title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
$description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
$price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
$category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
$stock = filter_var($_POST['stock'], FILTER_VALIDATE_INT);
$artist = filter_var($_POST['artist'], FILTER_SANITIZE_STRING);
$dimensions = filter_var($_POST['dimensions'], FILTER_SANITIZE_STRING);

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = "../uploads/artworks/";
    $absolute_dir = realpath(__DIR__ . '/../uploads/artworks') ?: __DIR__ . '/../uploads/artworks';
    
    // Debug directory permissions
    error_log("Upload directory path: " . $absolute_dir);
    error_log("Directory exists: " . (file_exists($absolute_dir) ? 'yes' : 'no'));
    error_log("Directory writable: " . (is_writable($absolute_dir) ? 'yes' : 'no'));
    error_log("Directory permissions: " . substr(sprintf('%o', fileperms($absolute_dir)), -4));
    
    // Create directory with proper permissions if it doesn't exist
    if (!file_exists($absolute_dir)) {
        if (!mkdir($absolute_dir, 0777, true)) {
            error_log("Failed to create directory: " . $absolute_dir);
            $_SESSION['error'] = "Upload directory could not be created. Check permissions.";
            header("Location: ../pages/admin/artworks.php");
            exit();
        }
        // Ensure proper permissions after creation
        chmod($absolute_dir, 0777);
    }

    // Verify directory is writable
    if (!is_writable($absolute_dir)) {
        error_log("Directory not writable: " . $absolute_dir);
        $_SESSION['error'] = "Upload directory is not writable. Current permissions: " . 
                            substr(sprintf('%o', fileperms($absolute_dir)), -4);
        header("Location: ../pages/admin/artworks.php");
        exit();
    }

    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $filename = $artwork_id . '.' . $file_extension;
    $upload_path = $absolute_dir . '/' . $filename;
    $image_url = 'uploads/artworks/' . $filename;
    
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
        $error = error_get_last();
        error_log("Failed to move uploaded file. Error: " . ($error ? $error['message'] : 'Unknown error'));
        error_log("From: " . $_FILES['image']['tmp_name'] . " To: " . $upload_path);
        $_SESSION['error'] = "Failed to upload image. Check server logs for details.";
        header("Location: ../pages/admin/artworks.php");
        exit();
    }
}

try {
    if ($_POST['artwork_id']) {
        // Update existing artwork
        $query = "UPDATE artworks SET 
                    title = ?, description = ?, price = ?, category = ?,
                    stock = ?, artist = ?, dimensions = ?
                    " . (isset($image_url) ? ", image_url = ?" : "") . "
                 WHERE id = ?";
        
        $params = [$title, $description, $price, $category, $stock, $artist, $dimensions];
        if (isset($image_url)) {
            $params[] = $image_url;
        }
        $params[] = $artwork_id;
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    } else {
        // Insert new artwork
        $query = "INSERT INTO artworks (id, title, description, price, image_url, 
                    category, stock, artist, dimensions) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssdsssss", $artwork_id, $title, $description, $price, 
                         $image_url, $category, $stock, $artist, $dimensions);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Artwork saved successfully";
    } else {
        throw new Exception("Failed to save artwork");
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header("Location: ../pages/admin/artworks.php");
exit();
