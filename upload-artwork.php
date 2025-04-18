<?php
$uploadsDir = 'uploads/artwork/';
$message = '';

// Create directory if it doesn't exist
if (!file_exists($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['artwork'])) {
    $files = $_FILES['artwork'];
    
    // Check if it's a single file or multiple files
    $isMultiple = is_array($files['name']);
    
    if ($isMultiple) {
        // Handle multiple file uploads
        $fileCount = count($files['name']);
        $successCount = 0;
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === 0) {
                $fileName = $files['name'][$i];
                $fileTmp = $files['tmp_name'][$i];
                
                // Generate a unique name to avoid overwrites
                $newFileName = time() . '_' . basename($fileName);
                $targetPath = $uploadsDir . $newFileName;
                
                if (move_uploaded_file($fileTmp, $targetPath)) {
                    $successCount++;
                }
            }
        }
        
        $message = "Successfully uploaded $successCount out of $fileCount artwork files.";
    } else {
        // Handle single file upload
        if ($files['error'] === 0) {
            $fileName = $files['name'];
            $fileTmp = $files['tmp_name'];
            
            // Generate a unique name to avoid overwrites
            $newFileName = time() . '_' . basename($fileName);
            $targetPath = $uploadsDir . $newFileName;
            
            if (move_uploaded_file($fileTmp, $targetPath)) {
                $message = "Artwork uploaded successfully!";
            } else {
                $message = "Error uploading file.";
            }
        } else {
            $message = "Error: " . $files['error'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Artwork</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #45a049;
        }
        .message {
            padding: 15px;
            background: #f8f9fa;
            border-left: 5px solid #4CAF50;
            margin-bottom: 20px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Upload New Artwork</h1>
    
    <?php if ($message): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="artwork">Select Artwork Files:</label>
            <input type="file" id="artwork" name="artwork[]" accept="image/*" multiple required>
            <small>You can select multiple files by holding Ctrl (or Cmd on Mac) while selecting.</small>
        </div>
        
        <button type="submit">Upload Artwork</button>
    </form>
    
    <a href="artwork-listing.php" class="back-link">View All Artwork</a>
</body>
</html>
