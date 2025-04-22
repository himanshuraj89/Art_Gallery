<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../config/database.php';
require_once '../../includes/header.php';

// Define base URL for images
$base_url = '/newf';  // Adjust this according to your setup

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch cart items
$query = "SELECT c.*, a.title, a.price, a.image_url, a.stock 
          FROM cart_items c
          JOIN artworks a ON c.artwork_id = a.id
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$cart_items = $stmt->get_result();

$total = 0;
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if ($cart_items->num_rows > 0): ?>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php while ($item = $cart_items->fetch_assoc()): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <?php 
                                    $image_path = $base_url . '/' . ltrim($item['image_url'], '/');
                                    // Debug output
                                    echo "<!-- Debug: Image path = " . htmlspecialchars($image_path) . " -->";
                                    ?>
                                    <img class="h-16 w-16 object-cover rounded" 
                                         src="<?php echo htmlspecialchars($image_path); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                         onerror="console.log('Error loading image:', this.src); this.src='<?php echo $base_url; ?>/assets/images/placeholder.jpg';">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($item['title']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                $<?php echo number_format($item['price'], 2); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="../../actions/cart_update.php" method="POST" class="flex items-center">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                    <select name="quantity" onchange="this.form.submit()" 
                                            class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <?php for ($i = 1; $i <= min(5, $item['stock']); $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php echo $item['quantity'] == $i ? 'selected' : ''; ?>>
                                                <?php echo $i; ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                $<?php echo number_format($subtotal, 2); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="../../actions/cart_remove.php" method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="px-6 py-4 bg-gray-50">
                <div class="flex justify-between items-center">
                    <div class="text-lg font-medium text-gray-900">
                        Total: $<?php echo number_format($total, 2); ?>
                    </div>
                    <a href="checkout.php" class="bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <p class="text-gray-500 mb-4">Your cart is empty</p>
            <a href="gallery.php" class="text-indigo-600 hover:text-indigo-800">Continue Shopping</a>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../../includes/footer.php'; ?>
