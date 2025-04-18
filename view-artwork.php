<?php
// Database connection parameters - update with your actual credentials
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'your_database_name';

// Connect to database
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Get all artwork, sorted by newest first
try {
    $stmt = $pdo->query("SELECT * FROM artworks ORDER BY created_at DESC");
    $artworks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("ERROR: Could not fetch artwork. " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artwork Gallery</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f8f8;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #333;
        }
        
        header p {
            font-size: 1.1rem;
            color: #666;
        }
        
        .artwork-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .artwork-item {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .artwork-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .artwork-image {
            height: 250px;
            overflow: hidden;
            position: relative;
        }
        
        .artwork-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .artwork-item:hover .artwork-image img {
            transform: scale(1.05);
        }
        
        .artwork-info {
            padding: 20px;
        }
        
        .artwork-info h2 {
            font-size: 1.3rem;
            margin-bottom: 8px;
            color: #222;
        }
        
        .artist {
            font-style: italic;
            color: #555;
            margin-bottom: 12px;
        }
        
        .details {
            font-size: 0.95rem;
            margin-bottom: 5px;
            color: #666;
        }
        
        .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2a5885;
            margin: 10px 0;
        }
        
        .description {
            margin-top: 15px;
            font-size: 0.95rem;
            color: #555;
            line-height: 1.4;
        }
        
        .date-added {
            margin-top: 12px;
            font-size: 0.9rem;
            color: #888;
        }
        
        .no-artwork {
            text-align: center;
            padding: 50px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .tools {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .tools a {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            margin: 0 10px;
        }
        
        .tools a:hover {
            background: #45a049;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .artwork-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .artwork-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 15px;
            }
            
            header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Our Artwork Collection</h1>
            <p>Displaying <?= count($artworks) ?> pieces from our collection</p>
        </header>
        
        <div class="tools">
            <a href="auto-populate-artwork.php">Auto-Populate More Artwork</a>
        </div>
        
        <?php if (empty($artworks)): ?>
            <div class="no-artwork">
                <p>No artwork found in the database. Run the auto-populate script first.</p>
            </div>
        <?php else: ?>
            <div class="artwork-grid">
                <?php foreach ($artworks as $artwork): ?>
                <div class="artwork-item">
                    <div class="artwork-image">
                        <img src="<?= htmlspecialchars($artwork['image_path']) ?>" alt="<?= htmlspecialchars($artwork['title']) ?>">
                    </div>
                    <div class="artwork-info">
                        <h2><?= htmlspecialchars($artwork['title']) ?></h2>
                        <p class="artist">By <?= htmlspecialchars($artwork['artist']) ?></p>
                        <p class="details"><strong>Medium:</strong> <?= htmlspecialchars($artwork['medium']) ?></p>
                        <p class="details"><strong>Dimensions:</strong> <?= htmlspecialchars($artwork['dimensions']) ?></p>
                        <p class="details"><strong>Category:</strong> <?= htmlspecialchars($artwork['category']) ?></p>
                        <p class="price">$<?= number_format($artwork['price'], 2) ?></p>
                        <p class="description"><?= htmlspecialchars($artwork['description']) ?></p>
                        <p class="date-added">Added: <?= date('F j, Y', strtotime($artwork['created_at'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
