<?php
/**
 * Functions to retrieve and display artwork
 */

// Get database connection
function getArtworkDbConnection() {
    // Update these with your actual database credentials
    $dbHost = 'localhost';
    $dbUsername = 'root';
    $dbPassword = '';
    $dbName = 'art_gallery';
    
    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        return null;
    }
}

// Get all artwork, with optional limit and category filter
function getArtworks($limit = null, $category = null) {
    $pdo = getArtworkDbConnection();
    if (!$pdo) return [];
    
    try {
        $sql = "SELECT * FROM artworks";
        $params = [];
        
        if ($category) {
            $sql .= " WHERE category = ?";
            $params[] = $category;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if ($limit && is_numeric($limit)) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching artwork: " . $e->getMessage());
        return [];
    }
}

// Get a single artwork by ID
function getArtworkById($id) {
    $pdo = getArtworkDbConnection();
    if (!$pdo) return null;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM artworks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching artwork by ID: " . $e->getMessage());
        return null;
    }
}

// Get all available artwork categories
function getArtworkCategories() {
    $pdo = getArtworkDbConnection();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->query("SELECT DISTINCT category FROM artworks ORDER BY category");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch(PDOException $e) {
        error_log("Error fetching artwork categories: " . $e->getMessage());
        return [];
    }
}

// Display artwork grid
function displayArtworkGrid($artworks, $columns = 3) {
    if (empty($artworks)) {
        echo '<div class="empty-gallery">No artwork found.</div>';
        return;
    }
    
    echo '<div class="artwork-grid columns-' . $columns . '">';
    
    foreach ($artworks as $artwork) {
        ?>
        <div class="artwork-item">
            <div class="artwork-image">
                <img src="<?= htmlspecialchars($artwork['image_path']) ?>" alt="<?= htmlspecialchars($artwork['title']) ?>">
                <?php if ($artwork['status'] === 'Sold'): ?>
                    <div class="artwork-sold-tag">Sold</div>
                <?php endif; ?>
            </div>
            <div class="artwork-info">
                <h3><?= htmlspecialchars($artwork['title']) ?></h3>
                <p class="artist">By <?= htmlspecialchars($artwork['artist']) ?></p>
                <p class="medium"><?= htmlspecialchars($artwork['medium']) ?></p>
                <p class="dimensions"><?= htmlspecialchars($artwork['dimensions']) ?></p>
                <p class="price">$<?= number_format($artwork['price'], 2) ?></p>
                <a href="artwork-detail.php?id=<?= $artwork['id'] ?>" class="view-details">View Details</a>
            </div>
        </div>
        <?php
    }
    
    echo '</div>';
}

// Display featured artwork (for homepage)
function displayFeaturedArtwork($limit = 4) {
    $featuredArtworks = getArtworks($limit);
    
    if (empty($featuredArtworks)) {
        return;
    }
    
    echo '<div class="featured-artwork-section">';
    echo '<h2>Featured Artwork</h2>';
    echo '<div class="featured-artwork-container">';
    
    foreach ($featuredArtworks as $artwork) {
        ?>
        <div class="featured-artwork">
            <div class="artwork-image">
                <img src="<?= htmlspecialchars($artwork['image_path']) ?>" alt="<?= htmlspecialchars($artwork['title']) ?>">
            </div>
            <div class="artwork-info">
                <h3><?= htmlspecialchars($artwork['title']) ?></h3>
                <p class="artist">By <?= htmlspecialchars($artwork['artist']) ?></p>
                <p class="price">$<?= number_format($artwork['price'], 2) ?></p>
                <a href="artwork-detail.php?id=<?= $artwork['id'] ?>" class="view-details">View Details</a>
            </div>
        </div>
        <?php
    }
    
    echo '</div>';
    echo '<div class="view-all-link"><a href="gallery.php">View All Artwork</a></div>';
    echo '</div>';
}
?>
