<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/header.php';

$artwork_id = isset($_GET['artwork_id']) ? $_GET['artwork_id'] : null;

if (!$artwork_id) {
    header("Location: gallery.php");
    exit();
}

// Get artwork details
$stmt = $conn->prepare("SELECT * FROM artworks WHERE id = ?");
$stmt->bind_param("s", $artwork_id);
$stmt->execute();
$artwork = $stmt->get_result()->fetch_assoc();

if (!$artwork) {
    header("Location: gallery.php");
    exit();
}
?>

<div class="py-12 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Reserve Artwork</h2>
            </div>
            
            <div class="p-6">
                <div class="mb-8 flex items-center space-x-4">
                    <img src="<?php echo get_image_url($artwork['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                         class="w-24 h-24 object-cover rounded">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($artwork['title']); ?></h3>
                        <p class="text-gray-600">By <?php echo htmlspecialchars($artwork['artist']); ?></p>
                        <p class="text-indigo-600 font-medium">$<?php echo number_format($artwork['price'], 2); ?></p>
                    </div>
                </div>

                <form action="../../actions/reserve_artwork.php" method="POST" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="artwork_id" value="<?php echo $artwork_id; ?>">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="name" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" name="phone" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Message (Optional)</label>
                        <textarea name="message" rows="4"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="../public/artwork.php?id=<?php echo $artwork_id; ?>" 
                           class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            Submit Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
