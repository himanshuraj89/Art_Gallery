<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/admin_header.php';

// Get all artworks
$query = "SELECT * FROM artworks ORDER BY title ASC";
$artworks = $conn->query($query);

// In a real app, you'd have a separate table for featured artworks
// For this demo, we'll assume artworks with is_featured=1 are featured

// Handle featuring/unfeaturing if an ID is provided
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $artwork_id = (int)$_GET['toggle'];
    $featured = isset($_GET['featured']) ? (int)$_GET['featured'] : 0;
    
    // Toggle featured status (in a real app)
    // $stmt = $conn->prepare("UPDATE artworks SET is_featured = ? WHERE id = ?");
    // $stmt->bind_param("ii", $featured, $artwork_id);
    // $stmt->execute();
    
    $_SESSION['success'] = "Featured status updated! (Note: This is a demo)";
    header("Location: featured.php");
    exit();
}
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Featured Artworks</h1>
            <a href="dashboard.php" class="text-indigo-600 hover:text-indigo-900">← Back to Dashboard</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Featured Info -->
        <div class="mt-6 bg-blue-50 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">About Featured Artworks</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Featured artworks appear prominently on the homepage. Select your best artworks to showcase them to visitors.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Artworks Grid -->
        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php while ($artwork = $artworks->fetch_assoc()): ?>
                <?php 
                    // In a real app, this would come from the database
                    $is_featured = isset($artwork['is_featured']) ? $artwork['is_featured'] : rand(0, 1);
                ?>
                <div class="bg-white shadow overflow-hidden rounded-lg divide-y divide-gray-200">
                    <div class="relative">
                        <img src="<?php echo htmlspecialchars($artwork['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($artwork['title']); ?>" 
                             class="w-full h-48 object-cover">
                        <?php if ($is_featured): ?>
                            <div class="absolute top-0 right-0 bg-yellow-400 text-xs font-bold py-1 px-2 m-2 rounded">
                                <i class="fas fa-star mr-1"></i> Featured
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="px-4 py-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            <?php echo htmlspecialchars($artwork['title']); ?>
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            By <?php echo htmlspecialchars($artwork['artist']); ?>
                        </p>
                    </div>
                    <div class="px-4 py-3 flex items-center justify-between">
                        <?php if ($is_featured): ?>
                            <a href="?toggle=<?php echo $artwork['id']; ?>&featured=0" 
                               class="text-sm font-medium text-red-600 hover:text-red-500">
                                <i class="fas fa-times-circle mr-1"></i> Remove from featured
                            </a>
                        <?php else: ?>
                            <a href="?toggle=<?php echo $artwork['id']; ?>&featured=1" 
                               class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                <i class="fas fa-plus-circle mr-1"></i> Add to featured
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php if ($artworks->num_rows === 0): ?>
                <div class="col-span-full text-center py-10">
                    <p class="text-gray-500">No artworks found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
