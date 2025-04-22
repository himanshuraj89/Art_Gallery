<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../../config/database.php';
require_once '../../includes/header.php';

// Function to check if user is admin
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

$artwork_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$artwork_id) {
    header("Location: gallery.php");
    exit();
}

// If delete action is requested and user is admin
if (isset($_POST['delete']) && is_admin()) {
    // First get the image path
    $image_stmt = $conn->prepare("SELECT image_url FROM artworks WHERE id = ?");
    $image_stmt->bind_param("s", $artwork_id);
    $image_stmt->execute();
    $image_result = $image_stmt->get_result()->fetch_assoc();
    
    if ($image_result) {
        // Delete the physical image file
        $image_path = $_SERVER['DOCUMENT_ROOT'] . '/newf/' . $image_result['image_url'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        
        // Delete the database record
        $delete_stmt = $conn->prepare("DELETE FROM artworks WHERE id = ?");
        $delete_stmt->bind_param("s", $artwork_id);
        if ($delete_stmt->execute()) {
            header("Location: gallery.php");
            exit();
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM artworks WHERE id = ?");
$stmt->bind_param("s", $artwork_id);
$stmt->execute();
$artwork = $stmt->get_result()->fetch_assoc();

if (!$artwork) {
    header("Location: gallery.php");
    exit();
}

// Update the popular artworks query
$popular_stmt = $conn->prepare("
    SELECT * FROM artworks 
    WHERE id != ? 
    ORDER BY RAND() 
    LIMIT 8
");

if (!$popular_stmt) {
    echo "Prepare failed: " . $conn->error;
    exit();
}

$popular_stmt->bind_param("s", $artwork_id);
if (!$popular_stmt->execute()) {
    echo "Execute failed: " . $popular_stmt->error;
    exit();
}

$result = $popular_stmt->get_result();
if (!$result) {
    echo "Get result failed: " . $popular_stmt->error;
    exit();
}

$popular_artworks = $result->fetch_all(MYSQLI_ASSOC);

// Create a standardized description paragraph for the artwork
$artwork_description = "This stunning artwork \"" . htmlspecialchars($artwork['title']) . 
                      "\" belongs to the " . htmlspecialchars($artwork['category']) . 
                      " category. With dimensions of " . htmlspecialchars($artwork['dimensions']) . 
                      ", this piece is available for $" . number_format($artwork['price'], 2) . 
                      ". A perfect addition to any art collection that values quality and aesthetic appeal.";
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="lg:grid lg:grid-cols-2 lg:gap-12">
        <!-- Image -->
        <div class="relative aspect-[4/3] rounded-2xl shadow-2xl overflow-hidden">
            <img src="<?php echo get_image_url($artwork['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                 class="absolute inset-0 w-full h-full object-cover">
        </div>

        <!-- Details -->
        <div class="mt-10 lg:mt-0 p-8 bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100">
            <div class="space-y-8">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                        <?php echo htmlspecialchars($artwork['title']); ?>
                    </h1>
                    <p class="mt-2 text-xl font-medium text-gray-700">By <?php echo htmlspecialchars($artwork['artist']); ?></p>
                    <p class="mt-4 text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                        $<?php echo number_format($artwork['price'], 2); ?>
                    </p>
                </div>

                <div class="prose prose-lg text-gray-600 bg-white/50 p-6 rounded-xl backdrop-blur-sm border border-gray-100">
                    <p class="italic"><?php echo $artwork_description; ?></p>
                    <div class="mt-4"><?php echo nl2br(htmlspecialchars($artwork['description'])); ?></div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                        Artwork Details
                    </h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div class="bg-white/50 p-4 rounded-xl backdrop-blur-sm border border-gray-100 transition-all duration-300 hover:shadow-md">
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="mt-1 text-lg font-medium text-gray-900"><?php echo htmlspecialchars($artwork['category']); ?></dd>
                        </div>
                        <div class="bg-white/50 p-4 rounded-xl backdrop-blur-sm border border-gray-100 transition-all duration-300 hover:shadow-md">
                            <dt class="text-sm font-medium text-gray-500">Dimensions</dt>
                            <dd class="mt-1 text-lg font-medium text-gray-900"><?php echo htmlspecialchars($artwork['dimensions']); ?></dd>
                        </div>
                        <div class="bg-white/50 p-4 rounded-xl backdrop-blur-sm border border-gray-100 transition-all duration-300 hover:shadow-md col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Availability</dt>
                            <dd class="mt-1 text-lg font-medium <?php echo $artwork['stock'] > 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo $artwork['stock'] > 0 ? $artwork['stock'] . ' in stock' : 'Out of Stock'; ?>
                            </dd>
                        </div>
                    </dl>
                </div>

                <?php if ($artwork['stock'] > 0): ?>
                    <form action="../../actions/cart_add.php" method="POST" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="artwork_id" value="<?php echo $artwork['id']; ?>">
                        
                        <div class="flex items-center gap-4">
                            <select id="quantity" name="quantity" 
                                class="rounded-xl border-2 border-gray-200 text-lg text-gray-700 px-4 py-3
                                hover:border-blue-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                transition-all duration-300 cursor-pointer bg-white/50 backdrop-blur-sm">
                                <?php for($i = 1; $i <= min(5, $artwork['stock']); $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                            <button type="submit" 
                                class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl px-8 py-3
                                hover:from-blue-700 hover:to-purple-700 active:from-blue-800 active:to-purple-800
                                transform transition-all duration-300 hover:-translate-y-0.5 active:translate-y-0
                                focus:outline-none focus:ring-2 focus:ring-purple-500/50 shadow-lg hover:shadow-xl
                                text-lg font-medium">
                                Add to Cart
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="mt-8">
                        <button disabled 
                            class="w-full bg-gray-100 text-gray-400 rounded-xl px-8 py-3
                            cursor-not-allowed transition-all duration-300 border-2 border-gray-200
                            text-lg font-medium">
                            Out of Stock
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (is_admin()): ?>
                    <div class="mt-4">
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this artwork?');">
                            <button type="submit" name="delete" 
                                class="w-full bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-xl px-8 py-3
                                hover:from-red-600 hover:to-pink-600 active:from-red-700 active:to-pink-700
                                transform transition-all duration-300 hover:-translate-y-0.5 active:translate-y-0
                                focus:outline-none focus:ring-2 focus:ring-red-500/50 shadow-lg hover:shadow-xl
                                text-lg font-medium">
                                Delete Artwork
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Popular Artworks Section -->
    <div class="mt-16 border-t border-gray-200 pt-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">More Artworks You Might Like</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($popular_artworks as $popular): ?>
                <a href="artwork.php?id=<?php echo htmlspecialchars($popular['id']); ?>" 
                   class="group">
                    <div class="relative overflow-hidden rounded-lg">
                        <img src="<?php echo get_image_url($popular['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($popular['title']); ?>"
                             class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity duration-300"></div>
                    </div>
                    <h3 class="mt-3 text-sm font-medium text-gray-900">
                        <?php echo htmlspecialchars($popular['title']); ?>
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        $<?php echo number_format($popular['price'], 2); ?>
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>
