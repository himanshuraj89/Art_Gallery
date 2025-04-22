<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/admin_header.php';

// Update reservation status if requested
if (isset($_POST['update_status']) && isset($_POST['reservation_id']) && isset($_POST['status'])) {
    $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt->bind_param("ss", $_POST['status'], $_POST['reservation_id']);
    $stmt->execute();
}

// Add delete handler
if (isset($_POST['delete_reservation']) && isset($_POST['reservation_id'])) {
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $_POST['reservation_id']);
    $stmt->execute();
}

// Get all reservations
$query = "SELECT r.*, DATE_FORMAT(r.date, '%Y-%m-%d') as formatted_date,
          TIME_FORMAT(r.time, '%h:%i %p') as formatted_time
          FROM reservations r 
          ORDER BY r.created_at DESC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<div class="py-8">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Manage Reservations</h1>

        <div class="mt-4 flex flex-col">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Contact</th>
                            <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Visit Details</th>
                            <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <?php while ($reservation = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-8 py-6">
                                    <div class="text-base font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($reservation['name']); ?>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($reservation['email']); ?></div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="space-y-1">
                                        <div class="text-sm font-medium text-gray-900">
                                            <span class="inline-block w-16 text-gray-500">Date:</span>
                                            <?php echo date('F j, Y', strtotime($reservation['formatted_date'])); ?>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <span class="inline-block w-16 text-gray-500">Time:</span>
                                            <?php echo $reservation['formatted_time']; ?>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <span class="inline-block w-16 text-gray-500">Guests:</span>
                                            <?php echo $reservation['guests']; ?>
                                        </div>
                                        <?php if ($reservation['notes']): ?>
                                        <div class="text-sm text-gray-600 mt-2">
                                            <span class="block text-gray-500">Notes:</span>
                                            <p class="mt-1 italic"><?php echo nl2br(htmlspecialchars($reservation['notes'])); ?></p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                        <?php 
                                        echo match($reservation['status']) {
                                            'confirmed' => 'bg-green-100 text-green-800 border border-green-200',
                                            'cancelled' => 'bg-red-100 text-red-800 border border-red-200',
                                            default => 'bg-yellow-100 text-yellow-800 border border-yellow-200'
                                        };
                                        ?>">
                                        <?php echo ucfirst($reservation['status']); ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center space-x-4">
                                        <form method="POST" class="flex space-x-2">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                            <input type="hidden" name="update_status" value="1">
                                            <select name="status" onchange="this.form.submit()"
                                                    class="text-sm border border-gray-300 rounded-lg py-1.5 px-3 bg-white shadow-sm hover:border-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                                                <option value="pending" <?php echo $reservation['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="confirmed" <?php echo $reservation['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirm</option>
                                                <option value="cancelled" <?php echo $reservation['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancel</option>
                                            </select>
                                        </form>
                                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this reservation?');">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                            <input type="hidden" name="delete_reservation" value="1">
                                            <button type="submit" class="text-red-600 hover:text-red-800 transition-colors duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
