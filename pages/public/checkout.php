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
$items = [];
while ($item = $cart_items->fetch_assoc()) {
    $item['subtotal'] = $item['price'] * $item['quantity'];
    $total += $item['subtotal'];
    $items[] = $item;
}

if (empty($items)) {
    header("Location: cart.php");
    exit();
}
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

    <div class="lg:grid lg:grid-cols-2 lg:gap-8">
        <!-- Order Summary -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>
            </div>
            <div class="border-t border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="px-6 py-4">
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    $<?php echo number_format($item['price'], 2); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $item['quantity']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    $<?php echo number_format($item['subtotal'], 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="px-6 py-4 bg-gray-50">
                    <div class="text-lg font-medium text-gray-900">
                        Total: $<?php echo number_format($total, 2); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="mt-8 lg:mt-0">
            <form action="../../actions/process_order.php" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                    <div class="space-y-6">
                        <button type="submit" 
                                class="w-full bg-indigo-600 border border-transparent rounded-md py-3 px-4 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Place Order
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>
