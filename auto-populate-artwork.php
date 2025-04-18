<?php
// Database connection parameters
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'art_gallery';

// Artwork directory
$artworkDir = 'uploads/artworks/';
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Connect to database
try {
    // Connect directly to the art_gallery database
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>Database connection successful.</p>";
} catch(PDOException $e) {
    // If connection fails, try creating the database
    try {
        $pdo = new PDO("mysql:host=$dbHost", $dbUsername, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        $pdo->exec("USE `$dbName`");
        echo "<p>Created and connected to database: $dbName</p>";
    } catch(PDOException $e2) {
        die("ERROR: Could not connect or create database. " . $e2->getMessage());
    }
}

// Function to get valid artist IDs from the database
function getValidArtistIds($pdo) {
    try {
        $stmt = $pdo->query("SELECT id FROM artists");
        $artistIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($artistIds)) {
            echo "<p>Warning: No artists found in the database. Will use NULL for artist_id.</p>";
            return [];
        }
        
        return $artistIds;
    } catch(PDOException $e) {
        echo "<p>Error getting artist IDs: " . $e->getMessage() . "</p>";
        return [];
    }
}

// Function to generate random artwork metadata
function generateArtworkMetadata($validArtistIds = []) {
    // Artist names
    $artists = ['John Doe', 'Jane Smith', 'Alex Rivera', 'Morgan Chen', 'Sara Johnson', 'David Wilson', 'Emma Thompson'];
    
    // Artwork categories
    $categories = ['Abstract', 'Portrait', 'Landscape', 'Still Life', 'Modern', 'Contemporary', 'Surrealism', 'Expressionism'];
    
    // Random dimensions (inches)
    $width = rand(8, 48);
    $height = rand(8, 60);
    
    // Random artwork name generator
    $adjectives = ['Beautiful', 'Dark', 'Vibrant', 'Melancholic', 'Joyful', 'Serene', 'Chaotic', 'Ethereal', 'Mysterious'];
    $nouns = ['Dreams', 'Journey', 'Reflection', 'Nature', 'Soul', 'Cosmos', 'Passage', 'Memory', 'Symphony', 'Horizon'];
    
    // Random price generation
    $price = rand(200, 5000);
    
    // Create a description
    $description = 'This artwork captures the essence of ' . $nouns[array_rand($nouns)] . ' through a ' . 
                  $adjectives[array_rand($adjectives)] . ' perspective. The artist created this piece using ' . 
                  ['bold strokes', 'delicate techniques', 'layered composition', 'vibrant colors', 'subtle textures']
                  [array_rand(['bold strokes', 'delicate techniques', 'layered composition', 'vibrant colors', 'subtle textures'])] . '.';
    
    // Get a valid artist ID from the database if available, otherwise null
    $artistId = !empty($validArtistIds) ? $validArtistIds[array_rand($validArtistIds)] : null;
    
    return [
        'title' => $adjectives[array_rand($adjectives)] . ' ' . $nouns[array_rand($nouns)],
        'artist' => $artists[array_rand($artists)],
        'artist_name' => $artists[array_rand($artists)], // Include both artist and artist_name
        'artist_id' => $artistId, // Use a valid artist_id or null
        'dimensions' => $width . '" × ' . $height . '"',
        'category' => $categories[array_rand($categories)],
        'description' => $description,
        'price' => $price,
        'stock' => rand(0, 10), // Random stock value
    ];
}

// Function to check if an image is already in the database
function isImageInDatabase($pdo, $filename) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM artworks WHERE image_url = ?");
        $stmt->execute([$filename]);
        return $stmt->fetchColumn() > 0;
    } catch(PDOException $e) {
        echo "<p>Error checking database: " . $e->getMessage() . "</p>";
        return false;
    }
}

// Get the next available ID for insertion
function getNextId($pdo) {
    try {
        $stmt = $pdo->query("SELECT MAX(id) FROM artworks");
        $maxId = $stmt->fetchColumn();
        return ($maxId ? $maxId + 1 : 1); // If no records, start with 1
    } catch(PDOException $e) {
        echo "<p>Error getting next ID: " . $e->getMessage() . "</p>";
        return 1; // Default to 1 if there's an error
    }
}

// Function to insert artwork into database, based on the existing column structure
function insertArtwork($pdo, $data) {
    try {
        // Get current timestamp for created_at and updated_at
        $timestamp = date('Y-m-d H:i:s');
        
        // Get the next ID to use
        $nextId = getNextId($pdo);
        
        // First check what columns actually exist in the table
        $columnsQuery = $pdo->query("SHOW COLUMNS FROM artworks");
        $columns = [];
        while($col = $columnsQuery->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = $col['Field'];
        }
        
        // Build a dynamic query based on existing columns
        $fieldsList = [];
        $placeholders = [];
        $values = [];
        
        // Include the ID field first
        if (in_array('id', $columns)) {
            $fieldsList[] = 'id';
            $placeholders[] = '?';
            $values[] = $nextId;
        }
        
        $columnMap = [
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'image_url' => $data['image_path'],
            'category' => $data['category'],
            'artist' => $data['artist'],
            'artist_name' => $data['artist_name'],
            'dimensions' => $data['dimensions'],
            'stock' => $data['stock'],
            'artist_id' => $data['artist_id'],
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ];
        
        foreach ($columnMap as $column => $value) {
            if (in_array($column, $columns)) {
                $fieldsList[] = $column;
                $placeholders[] = $value === null ? 'NULL' : '?';
                
                if ($value !== null) {
                    $values[] = $value;
                }
            }
        }
        
        $sql = "INSERT INTO artworks (" . implode(', ', $fieldsList) . ") VALUES (" . implode(', ', $placeholders) . ")";
        echo "<p>SQL Query: $sql</p>"; // Debug info
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        
        return true;
    } catch(PDOException $e) {
        echo "<p>Error inserting artwork: " . $e->getMessage() . "</p>";
        
        // Try inserting without artist_id if foreign key constraint fails
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            try {
                echo "<p>Trying again without artist_id...</p>";
                
                // Build query without artist_id
                $fieldsList = [];
                $placeholders = [];
                $values = [];
                
                // Include the ID field first
                if (in_array('id', $columns)) {
                    $fieldsList[] = 'id';
                    $placeholders[] = '?';
                    $values[] = $nextId;
                }
                
                foreach ($columnMap as $column => $value) {
                    if (in_array($column, $columns) && $column !== 'artist_id') {
                        $fieldsList[] = $column;
                        $placeholders[] = '?';
                        $values[] = $value;
                    }
                }
                
                $sql = "INSERT INTO artworks (" . implode(', ', $fieldsList) . ") VALUES (" . implode(', ', $placeholders) . ")";
                echo "<p>New SQL Query: $sql</p>";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($values);
                
                return true;
            } catch(PDOException $e2) {
                echo "<p>Second error: " . $e2->getMessage() . "</p>";
                return false;
            }
        }
        
        return false;
    }
}

// Get existing columns in the artworks table
try {
    $columnsQuery = $pdo->query("SHOW COLUMNS FROM artworks");
    $columns = [];
    $columnInfo = [];
    
    while($col = $columnsQuery->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $col['Field'];
        $columnInfo[$col['Field']] = [
            'type' => $col['Type'],
            'null' => $col['Null'],
            'key' => $col['Key'],
            'default' => $col['Default'],
            'extra' => $col['Extra']
        ];
    }
    
    echo "<p>Existing table found with columns: " . implode(", ", $columns) . "</p>";
    
    // Check if ID column is properly set as AUTO_INCREMENT
    if (isset($columnInfo['id']) && $columnInfo['id']['extra'] != 'auto_increment') {
        echo "<p>Warning: The 'id' column is not set as AUTO_INCREMENT. Will manually assign IDs.</p>";
        
        // Try to modify the table to add AUTO_INCREMENT
        try {
            $idType = $columnInfo['id']['type'];
            $pdo->exec("ALTER TABLE artworks MODIFY id $idType AUTO_INCREMENT");
            echo "<p>Successfully updated 'id' column to AUTO_INCREMENT.</p>";
        } catch(PDOException $e) {
            echo "<p>Could not modify the 'id' column: " . $e->getMessage() . "</p>";
        }
    }
    
} catch(PDOException $e) {
    // Table doesn't exist, create it
    try {
        $pdo->exec("
            CREATE TABLE artworks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                price DECIMAL(10,2),
                image_url VARCHAR(255) NOT NULL, 
                category VARCHAR(100),
                artist_name VARCHAR(255),
                stock INT DEFAULT 1,
                artist VARCHAR(255),
                dimensions VARCHAR(100),
                artist_id INT,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        echo "<p>Created 'artworks' table with the necessary columns.</p>";
    } catch(PDOException $e2) {
        echo "<p>Error creating table: " . $e2->getMessage() . "</p>";
    }
}

// Get valid artist IDs from the database
$validArtistIds = getValidArtistIds($pdo);
echo "<p>Found " . count($validArtistIds) . " valid artist IDs in the database.</p>";

// Create directory if it doesn't exist
if (!file_exists($artworkDir)) {
    if (mkdir($artworkDir, 0755, true)) {
        echo "<p>Created artwork directory at: $artworkDir</p>";
    } else {
        echo "<p>Failed to create artwork directory. Please check permissions.</p>";
    }
}

// Process the images in the directory
$files = scandir($artworkDir);
$imageCount = 0;
$addedCount = 0;

foreach ($files as $file) {
    // Skip . and .. directory entries
    if ($file === '.' || $file === '..') continue;
    
    // Check if file is an allowed image type
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (in_array($extension, $allowedExtensions)) {
        $imageCount++;
        
        // Full path to the image
        $imagePath = $artworkDir . $file;
        
        // Check if this image is already in the database
        if (isImageInDatabase($pdo, $imagePath)) {
            echo "<p>Skipping $file - already in database.</p>";
            continue;
        }
        
        // Generate metadata for this artwork with valid artist IDs
        $metadata = generateArtworkMetadata($validArtistIds);
        $metadata['image_path'] = $imagePath;
        
        // Insert into database
        if (insertArtwork($pdo, $metadata)) {
            $addedCount++;
            echo "<p>Added: {$metadata['title']} by {$metadata['artist']} ($file)</p>";
        } else {
            echo "<p>Failed to add: $file</p>";
        }
    }
}

// Display summary
if ($imageCount === 0) {
    echo "<p>No images found in $artworkDir. Please add some artwork images to this folder.</p>";
} else {
    echo "<h2>Summary:</h2>";
    echo "<p>Found $imageCount images</p>";
    echo "<p>Added $addedCount new artwork entries to the database</p>";
    echo "<p>Skipped " . ($imageCount - $addedCount) . " images (already in database)</p>";
}

// Navigation links
echo "<p><a href='index.php'>Go to Home Page</a> | <a href='pages/public/gallery.php'>Go to Gallery Page</a></p>";
?>
