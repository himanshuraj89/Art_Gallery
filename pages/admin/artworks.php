<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../includes/admin_header.php';

// Define base URL for images
$base_url = '/newf';  // Adjust this according to your setup

// Fetch all artworks
$query = "SELECT * FROM artworks ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Manage Artworks</h1>
            <div class="flex space-x-4">
                <button id="deleteSelected" 
                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 hidden">
                    Delete Selected
                </button>
                <a href="artwork_form.php" 
                   class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Add New Artwork
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="mt-8 flex flex-col">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <form id="bulkDeleteForm" action="../../actions/bulk_delete_artwork.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Artist</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($artwork = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="artwork_ids[]" 
                                               value="<?php echo $artwork['id']; ?>" 
                                               class="artwork-checkbox rounded border-gray-300">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                        $image_path = $base_url . '/' . ltrim($artwork['image_url'], '/');
                                        // Debug output
                                        echo "<!-- Debug: Image path = " . htmlspecialchars($image_path) . " -->";
                                        ?>
                                        <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                             alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                                             class="h-16 w-16 object-cover rounded"
                                             onerror="console.log('Error loading image:', this.src); this.src='<?php echo $base_url; ?>/assets/images/placeholder.jpg';">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($artwork['title']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($artwork['artist']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        $<?php echo number_format($artwork['price'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            <?php echo $artwork['stock'] <= 5 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                                            <?php echo $artwork['stock']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="artwork_form.php?id=<?php echo $artwork['id']; ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                        <form action="../../actions/delete_artwork.php" method="POST" class="inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this artwork?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="artwork_id" value="<?php echo $artwork['id']; ?>">
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 cursor-pointer">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const artworkCheckboxes = document.querySelectorAll('.artwork-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelected');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');

    function updateDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.artwork-checkbox:checked');
        deleteSelectedBtn.classList.toggle('hidden', checkedBoxes.length === 0);
    }

    selectAll.addEventListener('change', function() {
        artworkCheckboxes.forEach(checkbox => checkbox.checked = this.checked);
        updateDeleteButton();
    });

    artworkCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateDeleteButton);
    });

    deleteSelectedBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete all selected artworks?')) {
            bulkDeleteForm.submit();
        }
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
