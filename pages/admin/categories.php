<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/admin_header.php';

// Get existing categories
$query = "SELECT * FROM artworks GROUP BY category ORDER BY category ASC";
$categories = $conn->query($query);

// Handle category addition if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_category'])) {
    $new_category = trim($_POST['new_category']);
    
    if (!empty($new_category)) {
        // In a real application, you would update a categories table
        // For now, we'll just display a success message
        $_SESSION['success'] = "Category added successfully! (Note: This is a demo)";
        header("Location: categories.php");
        exit();
    } else {
        $_SESSION['error'] = "Category name cannot be empty";
    }
}
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Manage Categories</h1>
            <a href="dashboard.php" class="text-indigo-600 hover:text-indigo-900">← Back to Dashboard</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Add New Category Form -->
        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Add New Category
                </h3>
                <div class="mt-4 max-w-xl">
                    <form method="POST" action="">
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" name="new_category" 
                                   class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300" 
                                   placeholder="New category name">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Add
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Current Categories List -->
        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Current Categories
                </h3>
                <div class="mt-4">
                    <ul role="list" class="divide-y divide-gray-200">
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <li class="py-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="ml-3 text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($category['category']); ?>
                                    </span>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <button type="button" 
                                            class="bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Edit
                                    </button>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100">
                                        Used in artworks
                                    </span>
                                </div>
                            </li>
                        <?php endwhile; ?>
                        <?php if ($categories->num_rows === 0): ?>
                            <li class="py-4 text-center text-gray-500">
                                No categories found
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
