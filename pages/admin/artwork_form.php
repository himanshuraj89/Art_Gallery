<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/admin_header.php';

$artwork = ['id' => '', 'title' => '', 'price' => '', 
            'category' => '', 'stock' => '', 'artist' => '', 'dimensions' => ''];

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM artworks WHERE id = ?");
    $stmt->bind_param("s", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $artwork = $result->fetch_assoc();
}
?>

<div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">
                <?php echo $artwork['id'] ? 'Edit Artwork' : 'Add New Artwork'; ?>
            </h1>
            <a href="artworks.php" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        <form action="../../actions/upload_artwork.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="artwork_id" value="<?php echo $artwork['id']; ?>">

            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-8">
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                        <div class="space-y-6 sm:col-span-2">
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center" id="imageUploadArea">
                                <input type="file" name="image" id="imageInput" class="hidden" <?php echo $artwork['id'] ? '' : 'required'; ?> accept="image/*" onchange="previewImage(event)">
                                <div id="imagePreview" class="mb-4 hidden">
                                    <img src="" alt="Preview" class="mx-auto max-h-64 object-contain">
                                </div>
                                <label for="imageInput" class="cursor-pointer">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-1 text-sm text-gray-600">Click to upload artwork image</p>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" value="<?php echo htmlspecialchars($artwork['title']); ?>" required
                                   class="mt-1 block w-full px-4 py-3 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Artist</label>
                            <input type="text" name="artist" value="<?php echo htmlspecialchars($artwork['artist']); ?>" required
                                   class="mt-1 block w-full px-4 py-3 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Price ($)</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" name="price" value="<?php echo $artwork['price']; ?>" required min="0" step="0.01"
                                       class="block w-full pl-7 px-4 py-3 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stock</label>
                            <input type="number" name="stock" value="<?php echo $artwork['stock']; ?>" required min="0"
                                   class="mt-1 block w-full px-4 py-3 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" required 
                                    class="mt-1 block w-full px-4 py-3 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <?php
                                $categories = ["Paintings", "Sculptures", "Photography", "Digital Art", "Mixed Media", "Prints"];
                                foreach($categories as $cat) {
                                    $selected = ($artwork['category'] === $cat) ? 'selected' : '';
                                    echo "<option value=\"$cat\" $selected>$cat</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dimensions</label>
                            <input type="text" name="dimensions" value="<?php echo htmlspecialchars($artwork['dimensions']); ?>"
                                   class="mt-1 block w-full px-4 py-3 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="e.g., 24 x 36 inches">
                        </div>
                    </div>
                </div>

                <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-4">
                    <a href="artworks.php" 
                       class="px-6 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <?php echo $artwork['id'] ? 'Update Artwork' : 'Create Artwork'; ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    const image = preview.querySelector('img');
    preview.classList.remove('hidden');
    image.src = URL.createObjectURL(event.target.files[0]);
}
</script>

<?php require_once '../../includes/footer.php'; ?>
