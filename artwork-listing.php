<?php
// Configuration
$uploadsDir = 'uploads/artwork/';
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Function to get artwork metadata - in a real app, you'd fetch this from a database
function getArtworkMetadata($filename) {
    // Artist names for random assignment
    $artists = ['John Doe', 'Jane Smith', 'Alex Rivera', 'Morgan Chen', 'Sara Johnson'];
    
    // Artwork categories
    $categories = ['Abstract', 'Portrait', 'Landscape', 'Still Life', 'Modern', 'Contemporary', 'Surrealism'];
    
    // Random dimensions (inches)
    $width = rand(8, 48);
    $height = rand(8, 60);
    
    // Random artwork name generator
    $adjectives = ['Beautiful', 'Dark', 'Vibrant', 'Melancholic', 'Joyful', 'Serene', 'Chaotic'];
    $nouns = ['Dreams', 'Journey', 'Reflection', 'Nature', 'Soul', 'Cosmos', 'Passage', 'Memory'];
    
    // Get file creation time for "date added"
    $dateAdded = filemtime($GLOBALS['uploadsDir'] . $filename);
    
    return [
        'title' => $adjectives[array_rand($adjectives)] . ' ' . $nouns[array_rand($nouns)],
        'artist' => $artists[array_rand($artists)],
        'dimensions' => $width . '" × ' . $height . '"',
        'category' => $categories[array_rand($categories)],
        'dateAdded' => $dateAdded,
        'medium' => ['Oil on Canvas', 'Acrylic', 'Watercolor', 'Mixed Media', 'Digital'][array_rand(['Oil on Canvas', 'Acrylic', 'Watercolor', 'Mixed Media', 'Digital'])]
    ];
}

// Scan the uploads directory
function getArtworks() {
    global $uploadsDir, $allowedExtensions;
    $artworks = [];
    
    // Check if directory exists
    if (!file_exists($uploadsDir) || !is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
        return $artworks;
    }
    
    // Scan directory for images
    $files = scandir($uploadsDir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($extension, $allowedExtensions)) {
            $artworks[$file] = getArtworkMetadata($file);
        }
    }
    
    // Sort by date added (newest first)
    uasort($artworks, function($a, $b) {
        return $b['dateAdded'] - $a['dateAdded'];
    });
    
    return $artworks;
}

// Get all artwork
$artworks = getArtworks();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artwork Gallery</title>
    <link rel="stylesheet" href="css/artwork-styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Our Artwork Collection</h1>
            <p>Displaying <?= count($artworks) ?> pieces from our collection</p>
        </header>
        
        <?php if (empty($artworks)): ?>
            <div class="no-artwork">
                <p>No artwork found. Please upload images to the <?= $uploadsDir ?> directory.</p>
            </div>
        <?php else: ?>
            <div class="filter-options">
                <!-- You can add filter options here in the future -->
            </div>
            
            <div class="artwork-grid">
                <?php foreach ($artworks as $filename => $metadata): ?>
                <div class="artwork-item">
                    <div class="artwork-image">
                        <img src="<?= $uploadsDir . $filename ?>" alt="<?= htmlspecialchars($metadata['title']) ?>">
                    </div>
                    <div class="artwork-info">
                        <h2><?= htmlspecialchars($metadata['title']) ?></h2>
                        <p class="artist">By <?= htmlspecialchars($metadata['artist']) ?></p>
                        <p class="details"><strong>Medium:</strong> <?= htmlspecialchars($metadata['medium']) ?></p>
                        <p class="details"><strong>Dimensions:</strong> <?= htmlspecialchars($metadata['dimensions']) ?></p>
                        <p class="details"><strong>Category:</strong> <?= htmlspecialchars($metadata['category']) ?></p>
                        <p class="date-added">Added: <?= date('F j, Y', $metadata['dateAdded']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
