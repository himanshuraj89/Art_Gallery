<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in (you may need to adjust this based on your authentication system)
// This is a placeholder - replace with your actual authentication check
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the newsletter_subscribers table exists, and if the is_active column exists
try {
    // Check for table
    $table_check = $conn->query("SHOW TABLES LIKE 'newsletter_subscribers'");
    
    if($table_check->num_rows == 0) {
        // Create the table with the is_active column
        $create_table = "CREATE TABLE newsletter_subscribers (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            subscription_date DATETIME NOT NULL,
            is_active TINYINT(1) DEFAULT 1
        )";
        $conn->query($create_table);
    } else {
        // Check if the is_active column exists
        $column_check = $conn->query("SHOW COLUMNS FROM newsletter_subscribers LIKE 'is_active'");
        
        if($column_check->num_rows == 0) {
            // Add the is_active column
            $add_column = "ALTER TABLE newsletter_subscribers ADD COLUMN is_active TINYINT(1) DEFAULT 1";
            $conn->query($add_column);
        }
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 20;
$offset = ($page - 1) * $records_per_page;

// Filter variables
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Build the query based on filters
$where_clause = "";
$params = [];
$types = "";

if($status_filter === 'active') {
    $where_clause = " WHERE is_active = 1";
} elseif($status_filter === 'inactive') {
    $where_clause = " WHERE is_active = 0";
}

if(!empty($search)) {
    if(!empty($where_clause)) {
        $where_clause .= " AND email LIKE ?";
    } else {
        $where_clause = " WHERE email LIKE ?";
    }
    $params[] = "%$search%";
    $types .= "s";
}

// Get total records for pagination
$count_sql = "SELECT COUNT(*) as total FROM newsletter_subscribers" . $where_clause;
$count_stmt = $conn->prepare($count_sql);

if(!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$total_records = $count_row['total'];

$total_pages = ceil($total_records / $records_per_page);

// Get the subscribers
$sql = "SELECT * FROM newsletter_subscribers" . $where_clause . " ORDER BY subscription_date DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

if(!empty($params)) {
    $types .= "ii";
    $params[] = $records_per_page;
    $params[] = $offset;
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("ii", $records_per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

// Include header
$page_title = "Newsletter Subscribers";
include_once "../includes/admin_header.php";
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Newsletter Subscribers</h1>
    
    <!-- Filters and Search -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form action="" method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All</option>
                    <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search by Email</label>
                <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                       placeholder="Search emails...">
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Filter
                </button>
            </div>
        </form>
    </div>
    
    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Subscription Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($row['email']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    <?= date('M j, Y H:i', strtotime($row['subscription_date'])) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                      <?= $row['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="toggle-status px-3 py-1 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 transition" 
                                        data-id="<?= $row['id'] ?>" 
                                        data-status="<?= $row['is_active'] ? 'active' : 'inactive' ?>">
                                    <?= $row['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            No subscribers found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if($total_pages > 1): ?>
        <div class="flex justify-center mt-6">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <!-- Previous Page Link -->
                <?php if($page > 1): ?>
                    <a href="?page=<?= $page-1 ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>" 
                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                <?php endif; ?>
                
                <!-- Page Number Links -->
                <?php
                $start_page = max(1, min($page - 2, $total_pages - 4));
                $end_page = min($total_pages, max($page + 2, 5));
                
                for($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <a href="?page=<?= $i ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium 
                             <?= $i === $page ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <!-- Next Page Link -->
                <?php if($page < $total_pages): ?>
                    <a href="?page=<?= $page+1 ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>" 
                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status toggling
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const currentStatus = this.getAttribute('data-status');
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            
            // Disable button while processing
            this.disabled = true;
            this.textContent = 'Processing...';
            
            // Send AJAX request to update status
            fetch('../includes/update_newsletter_subscriber.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}&status=${newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button text and data attributes
                    this.textContent = currentStatus === 'active' ? 'Activate' : 'Deactivate';
                    this.setAttribute('data-status', newStatus);
                    
                    // Update status badge
                    const row = this.closest('tr');
                    const statusBadge = row.querySelector('.status-badge');
                    
                    if (newStatus === 'active') {
                        statusBadge.textContent = 'Active';
                        statusBadge.classList.remove('bg-red-100', 'text-red-800');
                        statusBadge.classList.add('bg-green-100', 'text-green-800');
                    } else {
                        statusBadge.textContent = 'Inactive';
                        statusBadge.classList.remove('bg-green-100', 'text-green-800');
                        statusBadge.classList.add('bg-red-100', 'text-red-800');
                    }
                } else {
                    // Show error and reset button
                    alert('Error updating subscriber status: ' + data.message);
                    this.textContent = currentStatus === 'active' ? 'Deactivate' : 'Activate';
                }
                this.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the subscriber status.');
                this.textContent = currentStatus === 'active' ? 'Deactivate' : 'Activate';
                this.disabled = false;
            });
        });
    });
});
</script>

<?php include_once "../includes/admin_footer.php"; ?>
