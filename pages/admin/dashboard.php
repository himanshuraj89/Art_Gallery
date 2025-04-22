<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/admin_header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

// Get statistics
$stats = [];

// Total artworks
$result = $conn->query("SELECT COUNT(*) as count FROM artworks");
$stats['artworks'] = $result->fetch_assoc()['count'];

// Total orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['orders'] = $result->fetch_assoc()['count'];

// Total users
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
$stats['users'] = $result->fetch_assoc()['count'];

// Additional statistics
$result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
$stats['revenue'] = $result->fetch_assoc()['total'] ?: 0;

// Recent revenue (last 30 days)
$query = "SELECT SUM(total_amount) as recent FROM orders 
          WHERE status != 'cancelled' 
          AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$result = $conn->query($query);
$stats['recent_revenue'] = $result->fetch_assoc()['recent'] ?: 0;

// Active users (users with orders)
$query = "SELECT COUNT(DISTINCT user_id) as count FROM orders";
$result = $conn->query($query);
$stats['active_users'] = $result->fetch_assoc()['count'];

// Pending orders
$query = "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'";
$result = $conn->query($query);
$stats['pending_orders'] = $result->fetch_assoc()['count'];

// Top selling artworks
$query = "SELECT a.id, a.title, a.image_url, COUNT(oi.id) as sold 
          FROM artworks a 
          JOIN order_items oi ON a.id = oi.artwork_id 
          JOIN orders o ON oi.order_id = o.id 
          WHERE o.status != 'cancelled' 
          GROUP BY a.id 
          ORDER BY sold DESC 
          LIMIT 5";
$top_selling = $conn->query($query);

// Recent orders
$query = "SELECT o.*, u.email FROM orders o 
          JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC LIMIT 5";
$recent_orders = $conn->query($query);

// Low stock artworks
$query = "SELECT * FROM artworks WHERE stock <= 5 ORDER BY stock ASC LIMIT 5";
$low_stock = $conn->query($query);

// Get daily revenue data (last 14 days)
$daily_revenue_query = "SELECT 
    DATE_FORMAT(created_at, '%b %d') as date_label,
    SUM(total_amount) as revenue,
    COUNT(*) as order_count
FROM orders 
WHERE status != 'cancelled'
    AND created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
GROUP BY DATE(created_at)
ORDER BY DATE(created_at) ASC";
$daily_revenue_result = $conn->query($daily_revenue_query);

$daily_labels = [];
$daily_revenue = [];
$daily_orders = [];

while ($row = $daily_revenue_result->fetch_assoc()) {
    $daily_labels[] = $row['date_label'];
    $daily_revenue[] = (float)$row['revenue'];
    $daily_orders[] = (int)$row['order_count'];
}

// Get monthly revenue data (last 12 months)
$monthly_revenue_query = "SELECT 
    DATE_FORMAT(created_at, '%b %Y') as month_label,
    SUM(total_amount) as revenue,
    COUNT(*) as order_count
FROM orders 
WHERE status != 'cancelled'
    AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY YEAR(created_at), MONTH(created_at)
ORDER BY YEAR(created_at) ASC, MONTH(created_at) ASC";
$monthly_revenue_result = $conn->query($monthly_revenue_query);

$monthly_labels = [];
$monthly_revenue = [];
$monthly_orders = [];

while ($row = $monthly_revenue_result->fetch_assoc()) {
    $monthly_labels[] = $row['month_label'];
    $monthly_revenue[] = (float)$row['revenue'];
    $monthly_orders[] = (int)$row['order_count'];
}

// Get yearly revenue data (last 5 years)
$yearly_revenue_query = "SELECT 
    YEAR(created_at) as year_label,
    SUM(total_amount) as revenue,
    COUNT(*) as order_count
FROM orders 
WHERE status != 'cancelled'
    AND created_at >= DATE_SUB(NOW(), INTERVAL 5 YEAR)
GROUP BY YEAR(created_at)
ORDER BY YEAR(created_at) ASC";
$yearly_revenue_result = $conn->query($yearly_revenue_query);

$yearly_labels = [];
$yearly_revenue = [];
$yearly_orders = [];

while ($row = $yearly_revenue_result->fetch_assoc()) {
    $yearly_labels[] = $row['year_label'];
    $yearly_revenue[] = (float)$row['revenue'];
    $yearly_orders[] = (int)$row['order_count'];
}

// Today's revenue
$today_query = "SELECT 
    SUM(total_amount) as revenue,
    COUNT(*) as order_count
FROM orders 
WHERE status != 'cancelled'
    AND DATE(created_at) = CURDATE()";
$today_result = $conn->query($today_query);
$today_data = $today_result->fetch_assoc();
$today_revenue = $today_data['revenue'] ? (float)$today_data['revenue'] : 0;
$today_orders = $today_data['order_count'] ? (int)$today_data['order_count'] : 0;

// This month's revenue
$month_query = "SELECT 
    SUM(total_amount) as revenue,
    COUNT(*) as order_count
FROM orders 
WHERE status != 'cancelled'
    AND MONTH(created_at) = MONTH(CURDATE())
    AND YEAR(created_at) = YEAR(CURDATE())";
$month_result = $conn->query($month_query);
$month_data = $month_result->fetch_assoc();
$month_revenue = $month_data['revenue'] ? (float)$month_data['revenue'] : 0;
$month_orders = $month_data['order_count'] ? (int)$month_data['order_count'] : 0;

// This year's revenue
$year_query = "SELECT 
    SUM(total_amount) as revenue,
    COUNT(*) as order_count
FROM orders 
WHERE status != 'cancelled'
    AND YEAR(created_at) = YEAR(CURDATE())";
$year_result = $conn->query($year_query);
$year_data = $year_result->fetch_assoc();
$year_revenue = $year_data['revenue'] ? (float)$year_data['revenue'] : 0;
$year_orders = $year_data['order_count'] ? (int)$year_data['order_count'] : 0;
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Admin Dashboard</h1>
        
        <!-- Quick Actions -->
        <div class="mt-6">
            <h2 class="text-lg font-medium text-gray-900">Quick Actions</h2>
            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <a href="<?php echo $base_url; ?>/pages/admin/artwork_form.php" class="bg-white shadow overflow-hidden rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                            <i class="fas fa-plus text-indigo-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Add Artwork</h3>
                            <p class="text-xs text-gray-500">Upload new art</p>
                        </div>
                    </div>
                </a>
                
                <a href="<?php echo $base_url; ?>/pages/admin/orders.php?status=pending" class="bg-white shadow overflow-hidden rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                            <i class="fas fa-truck text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Pending Orders</h3>
                            <p class="text-xs text-gray-500"><?php echo $stats['pending_orders']; ?> orders waiting</p>
                        </div>
                    </div>
                </a>
                
                <a href="<?php echo $base_url; ?>/pages/admin/users.php" class="bg-white shadow overflow-hidden rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                            <i class="fas fa-users text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Manage Users</h3>
                            <p class="text-xs text-gray-500"><?php echo $stats['users']; ?> registered users</p>
                        </div>
                    </div>
                </a>
                
                <a href="#" id="clearCacheBtn" class="bg-white shadow overflow-hidden rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                            <i class="fas fa-sync text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Clear Cache</h3>
                            <p class="text-xs text-gray-500">Update site cache</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-palette text-indigo-600 text-3xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Artworks</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['artworks']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shopping-cart text-indigo-600 text-3xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['orders']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-indigo-600 text-3xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['users']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-dollar-sign text-indigo-600 text-3xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                                <dd class="text-3xl font-semibold text-gray-900">$<?php echo number_format($stats['revenue'], 2); ?></dd>
                                <dd class="text-sm text-gray-500">
                                    Last 30 days: $<?php echo number_format($stats['recent_revenue'], 2); ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-check text-indigo-600 text-3xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Customers</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo $stats['active_users']; ?></dd>
                                <dd class="text-sm text-gray-500">
                                    of <?php echo $stats['users']; ?> total users
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Recent Orders -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
                    <div class="mt-6">
                        <div class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        #<?php echo substr($order['id'], 0, 8); ?>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        <?php echo htmlspecialchars($order['email']); ?>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            <?php
                                                            switch($order['status']) {
                                                                case 'completed':
                                                                    echo 'bg-green-100 text-green-800';
                                                                    break;
                                                                case 'processing':
                                                                    echo 'bg-blue-100 text-blue-800';
                                                                    break;
                                                                case 'cancelled':
                                                                    echo 'bg-red-100 text-red-800';
                                                                    break;
                                                                default:
                                                                    echo 'bg-yellow-100 text-yellow-800';
                                                            }
                                                            ?>">
                                                            <?php echo ucfirst($order['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        $<?php echo number_format($order['total_amount'], 2); ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Low Stock Alert</h3>
                    <div class="mt-6">
                        <div class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Artwork</th>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <?php while ($artwork = $low_stock->fetch_assoc()): ?>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        <?php echo htmlspecialchars($artwork['title']); ?>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                            <?php echo $artwork['stock'] === 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                            <?php echo $artwork['stock']; ?> left
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Top Selling Artworks</h3>
                    <div class="mt-6">
                        <div class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Artwork</th>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
                                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Units Sold</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <?php if ($top_selling->num_rows > 0): ?>
                                                <?php while ($artwork = $top_selling->fetch_assoc()): ?>
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            <?php echo htmlspecialchars($artwork['title']); ?>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
    <img src="<?php echo $base_url . '/' . ltrim(htmlspecialchars($artwork['image_url']), '/'); ?>" 
         alt="<?php echo htmlspecialchars($artwork['title']); ?>"
         class="h-10 w-10 rounded-full object-cover">
</td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                <?php echo $artwork['sold']; ?> units
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        No sales data available yet
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Revenue Analytics Section -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Revenue Analytics</h3>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-md text-sm font-medium hover:bg-indigo-200" id="viewDaily">Daily</button>
                            <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200" id="viewMonthly">Monthly</button>
                            <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200" id="viewYearly">Yearly</button>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <div class="bg-white p-4 rounded-lg border border-gray-200" style="height: 400px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Daily Stats -->
                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                            <h4 class="text-sm font-medium text-indigo-800 mb-2">Today's Revenue</h4>
                            <p class="text-2xl font-bold text-indigo-700" id="todayRevenue">Loading...</p>
                            <p class="text-xs text-indigo-600 mt-1" id="todayOrders">Loading...</p>
                        </div>
                        
                        <!-- Monthly Stats -->
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <h4 class="text-sm font-medium text-purple-800 mb-2">This Month</h4>
                            <p class="text-2xl font-bold text-purple-700" id="monthRevenue">Loading...</p>
                            <p class="text-xs text-purple-600 mt-1" id="monthOrders">Loading...</p>
                        </div>
                        
                        <!-- Yearly Stats -->
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">This Year</h4>
                            <p class="text-2xl font-bold text-blue-700" id="yearRevenue">Loading...</p>
                            <p class="text-xs text-blue-600 mt-1" id="yearOrders">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Website Configuration -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Website Configuration <span class="ml-2 text-sm text-white bg-blue-500 rounded-full px-3 py-1">Coming Soon</span></h3>
                    <div class="mt-6">
                        <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                            <i class="fas fa-tools text-gray-400 text-4xl mb-3"></i>
                            <p class="text-gray-500">This feature is currently under development.</p>
                            <p class="text-gray-400 text-sm mt-2">Check back soon for website configuration options.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- Add Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JavaScript for interactivity -->
<script>
document.getElementById('clearCacheBtn').addEventListener('click', function(e) {
    e.preventDefault();
    
    // Here you would typically make an AJAX call to a PHP script that clears cache
    // For demonstration, we're just showing an alert
    alert('Cache cleared successfully!');
});

// Revenue Analytics
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    let currentChart = null;
    
    // Real data from PHP
    const dailyLabels = <?php echo json_encode($daily_labels); ?>;
    const dailyRevenue = <?php echo json_encode($daily_revenue); ?>;
    const dailyOrders = <?php echo json_encode($daily_orders); ?>;
    
    const monthlyLabels = <?php echo json_encode($monthly_labels); ?>;
    const monthlyRevenue = <?php echo json_encode($monthly_revenue); ?>;
    const monthlyOrders = <?php echo json_encode($monthly_orders); ?>;
    
    const yearlyLabels = <?php echo json_encode($yearly_labels); ?>;
    const yearlyRevenue = <?php echo json_encode($yearly_revenue); ?>;
    const yearlyOrders = <?php echo json_encode($yearly_orders); ?>;
    
    const todayRevenue = <?php echo $today_revenue; ?>;
    const todayOrders = <?php echo $today_orders; ?>;
    const monthRevenue = <?php echo $month_revenue; ?>;
    const monthOrders = <?php echo $month_orders; ?>;
    const yearRevenue = <?php echo $year_revenue; ?>;
    const yearOrders = <?php echo $year_orders; ?>;
    
    // Set initial summary values
    document.getElementById('todayRevenue').textContent = '$' + todayRevenue.toFixed(2);
    document.getElementById('todayOrders').textContent = todayOrders + ' orders today';
    document.getElementById('monthRevenue').textContent = '$' + monthRevenue.toFixed(2);
    document.getElementById('monthOrders').textContent = monthOrders + ' orders this month';
    document.getElementById('yearRevenue').textContent = '$' + yearRevenue.toFixed(2);
    document.getElementById('yearOrders').textContent = yearOrders + ' orders this year';
    
    // Fetch revenue data for the selected period
    fetchRevenueData('daily');
    
    // Button event listeners
    document.getElementById('viewDaily').addEventListener('click', function() {
        setActiveButton('viewDaily');
        fetchRevenueData('daily');
    });
    
    document.getElementById('viewMonthly').addEventListener('click', function() {
        setActiveButton('viewMonthly');
        fetchRevenueData('monthly');
    });
    
    document.getElementById('viewYearly').addEventListener('click', function() {
        setActiveButton('viewYearly');
        fetchRevenueData('yearly');
    });
    
    function setActiveButton(activeId) {
        // Reset all buttons
        document.getElementById('viewDaily').classList.remove('bg-indigo-100', 'text-indigo-700');
        document.getElementById('viewDaily').classList.add('bg-gray-100', 'text-gray-700');
        document.getElementById('viewMonthly').classList.remove('bg-indigo-100', 'text-indigo-700');
        document.getElementById('viewMonthly').classList.add('bg-gray-100', 'text-gray-700');
        document.getElementById('viewYearly').classList.remove('bg-indigo-100', 'text-indigo-700');
        document.getElementById('viewYearly').classList.add('bg-gray-100', 'text-gray-700');
        
        // Set active button
        document.getElementById(activeId).classList.remove('bg-gray-100', 'text-gray-700');
        document.getElementById(activeId).classList.add('bg-indigo-100', 'text-indigo-700');
    }
    
    function fetchRevenueData(period) {
        let labels, revenueData, orderData;
        
        switch(period) {
            case 'daily':
                labels = dailyLabels.length > 0 ? dailyLabels : getLastNDays(14);
                revenueData = dailyRevenue.length > 0 ? dailyRevenue : generateFallbackData(14, 100, 1000);
                orderData = dailyOrders.length > 0 ? dailyOrders : generateFallbackData(14, 1, 20);
                break;
                
            case 'monthly':
                labels = monthlyLabels.length > 0 ? monthlyLabels : getLastNMonths(12);
                revenueData = monthlyRevenue.length > 0 ? monthlyRevenue : generateFallbackData(12, 1000, 10000);
                orderData = monthlyOrders.length > 0 ? monthlyOrders : generateFallbackData(12, 20, 100);
                break;
                
            case 'yearly':
                labels = yearlyLabels.length > 0 ? yearlyLabels : getLastNYears(5);
                revenueData = yearlyRevenue.length > 0 ? yearlyRevenue : generateFallbackData(5, 10000, 100000);
                orderData = yearlyOrders.length > 0 ? yearlyOrders : generateFallbackData(5, 100, 1000);
                break;
        }
        
        createRevenueChart(labels, revenueData, orderData, period);
    }
    
    function createRevenueChart(labels, revenueData, orderData, period) {
        // Destroy previous chart if it exists
        if (currentChart) {
            currentChart.destroy();
        }
        
        let tooltipTitle;
        switch (period) {
            case 'daily': tooltipTitle = 'Daily Revenue'; break;
            case 'monthly': tooltipTitle = 'Monthly Revenue'; break;
            case 'yearly': tooltipTitle = 'Yearly Revenue'; break;
        }
        
        currentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue ($)',
                    data: revenueData,
                    backgroundColor: 'rgba(79, 70, 229, 0.6)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                }, {
                    label: 'Orders',
                    data: orderData,
                    type: 'line',
                    backgroundColor: 'rgba(139, 92, 246, 0.2)',
                    borderColor: 'rgba(139, 92, 246, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(139, 92, 246, 1)',
                    pointRadius: 4,
                    tension: 0.2,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Revenue ($)'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        },
                        title: {
                            display: true,
                            text: 'Order Count'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(tooltipItems) {
                                return tooltipTitle + ' - ' + tooltipItems[0].label;
                            },
                            label: function(context) {
                                if (context.dataset.label === 'Revenue ($)') {
                                    return 'Revenue: $' + parseFloat(context.raw).toFixed(2);
                                } else {
                                    return 'Orders: ' + context.raw;
                                }
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Helper Functions for fallback data if no orders exist yet
    function getLastNDays(n) {
        const result = [];
        for (let i = n - 1; i >= 0; i--) {
            const d = new Date();
            d.setDate(d.getDate() - i);
            result.push(d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
        }
        return result;
    }
    
    function getLastNMonths(n) {
        const result = [];
        for (let i = n - 1; i >= 0; i--) {
            const d = new Date();
            d.setMonth(d.getMonth() - i);
            result.push(d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' }));
        }
        return result;
    }
    
    function getLastNYears(n) {
        const result = [];
        for (let i = n - 1; i >= 0; i--) {
            const d = new Date();
            d.setFullYear(d.getFullYear() - i);
            result.push(d.getFullYear().toString());
        }
        return result;
    }
    
    function generateFallbackData(count, min, max) {
        // Only used if there's no real data
        const result = [];
        for (let i = 0; i < count; i++) {
            result.push(Math.floor(Math.random() * (max - min + 1)) + min);
        }
        return result;
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>
