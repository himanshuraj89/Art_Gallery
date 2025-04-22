<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/admin_header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

$reservation_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$reservation_id) {
    header("Location: reservations.php");
    exit();
}

// Fetch reservation details with artwork info
$query = "SELECT r.*, a.title as artwork_title, a.artist, a.price, a.image_url,
          DATE_FORMAT(r.reservation_date, '%Y-%m-%d') as formatted_date,
          r.time_slot, r.guests, r.notes 
          FROM reservations r
          JOIN artworks a ON r.artwork_id = a.id
          WHERE r.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $reservation_id);
$stmt->execute();
$reservation = $stmt->get_result()->fetch_assoc();

if (!$reservation) {
    header("Location: reservations.php");
    exit();
}
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Reservation Details</h1>
            <a href="reservations.php" class="text-indigo-600 hover:text-indigo-900">← Back to Reservations</a>
        </div>

        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Reservation #<?php echo substr($reservation_id, 0, 8); ?>
                </h3>
            </div>

            <div class="border-t border-gray-200">
                <dl>
                    <!-- Artwork Information -->
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Artwork</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="flex items-center">
                                <img src="<?php echo $base_url . '/' . ltrim($reservation['image_url'], '/'); ?>" 
                                     alt="<?php echo htmlspecialchars($reservation['artwork_title']); ?>"
                                     class="h-20 w-20 object-cover rounded">
                                <div class="ml-4">
                                    <div class="font-medium"><?php echo htmlspecialchars($reservation['artwork_title']); ?></div>
                                    <div class="text-gray-500">By <?php echo htmlspecialchars($reservation['artist']); ?></div>
                                    <div class="text-gray-500">$<?php echo number_format($reservation['price'], 2); ?></div>
                                </div>
                            </div>
                        </dd>
                    </div>

                    <!-- Customer Information -->
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Customer Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <?php echo htmlspecialchars($reservation['user_name']); ?>
                        </dd>
                    </div>

                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Contact Information</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div>Email: <?php echo htmlspecialchars($reservation['user_email']); ?></div>
                            <div>Phone: <?php echo htmlspecialchars($reservation['phone']); ?></div>
                        </dd>
                    </div>

                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <form method="POST" action="" class="inline-flex">
                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                <input type="hidden" name="update_status" value="1">
                                <select name="status" onchange="this.form.submit()" 
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="pending" <?php echo $reservation['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="approved" <?php echo $reservation['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="rejected" <?php echo $reservation['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                            </form>
                        </dd>
                    </div>

                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Message</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <?php echo nl2br(htmlspecialchars($reservation['message'])); ?>
                        </dd>
                    </div>

                    <!-- Add Visit Details section -->
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Visit Details</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div>Date: <?php echo date('F j, Y', strtotime($reservation['formatted_date'])); ?></div>
                            <div>Time: <?php echo date('g:i A', strtotime($reservation['time_slot'])); ?></div>
                            <div>Number of Guests: <?php echo $reservation['guests']; ?></div>
                        </dd>
                    </div>

                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Additional Notes</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <?php echo nl2br(htmlspecialchars($reservation['notes'])); ?>
                        </dd>
                    </div>

                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <?php echo date('F j, Y g:i A', strtotime($reservation['created_at'])); ?>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
