<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include required files
require_once '../../includes/db_connect.php';
include_once '../../includes/admin_header.php';

// Initialize variables
$subscribers = [];
$error_message = null;
$success_message = null;
$total_pages = 1;
$page = 1;
$search = '';

try {
    // Check if the subscribers table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'subscribers'");
    
    if ($table_check->num_rows == 0) {
        // Create the table if it doesn't exist
        $create_table_sql = "CREATE TABLE subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($create_table_sql)) {
            $success_message = "Subscribers table was created successfully!";
        } else {
            throw new Exception("Error creating subscribers table: " . $conn->error);
        }
    }

    // Delete subscriber if ID is provided
    if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
        $id = intval($_GET['delete_id']);
        $delete_sql = "DELETE FROM subscribers WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $success_message = "Subscriber deleted successfully!";
            } else {
                throw new Exception("Error executing delete: " . $stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception("Error preparing delete statement: " . $conn->error);
        }
    }

    // Get search parameter
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    // Pagination
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $records_per_page = 10;
    $offset = ($page - 1) * $records_per_page;

    // Get subscribers with basic error handling
    if (!empty($search)) {
        $count_sql = "SELECT COUNT(*) as total FROM subscribers WHERE email LIKE ?";
        $select_sql = "SELECT * FROM subscribers WHERE email LIKE ? ORDER BY created_at DESC LIMIT ?, ?";
        
        $search_param = "%$search%";
        
        // Count total records
        $count_stmt = $conn->prepare($count_sql);
        if ($count_stmt) {
            $count_stmt->bind_param("s", $search_param);
            $count_stmt->execute();
            $count_result = $count_stmt->get_result();
            $total_result = $count_result->fetch_assoc();
            $total_records = $total_result['total'];
            $count_stmt->close();
        } else {
            throw new Exception("Error preparing count statement: " . $conn->error);
        }
        
        // Get results
        $stmt = $conn->prepare($select_sql);
        if ($stmt) {
            $stmt->bind_param("sii", $search_param, $offset, $records_per_page);
            $stmt->execute();
            $result = $stmt->get_result();
            $subscribers = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } else {
            throw new Exception("Error preparing select statement: " . $conn->error);
        }
    } else {
        $count_sql = "SELECT COUNT(*) as total FROM subscribers";
        $select_sql = "SELECT * FROM subscribers ORDER BY created_at DESC LIMIT ?, ?";
        
        // Count total records
        $count_result = $conn->query($count_sql);
        if ($count_result) {
            $total_result = $count_result->fetch_assoc();
            $total_records = $total_result['total'];
        } else {
            throw new Exception("Error counting records: " . $conn->error);
        }
        
        // Get results
        $stmt = $conn->prepare($select_sql);
        if ($stmt) {
            $stmt->bind_param("ii", $offset, $records_per_page);
            $stmt->execute();
            $result = $stmt->get_result();
            $subscribers = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } else {
            throw new Exception("Error preparing select statement: " . $conn->error);
        }
    }
    
    // Calculate total pages
    $total_pages = ceil($total_records / $records_per_page);
    
} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage();
    // Log error to file
    error_log("Subscribers page error: " . $e->getMessage(), 0);
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Subscribers Management</h1>
        
        <!-- Search Form -->
        <form method="GET" action="" class="flex">
            <input type="text" name="search" placeholder="Search by email" value="<?php echo htmlspecialchars($search); ?>"
                   class="px-4 py-2 border rounded-l focus:outline-none">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r hover:bg-blue-600">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    
    <?php if (isset($success_message)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Subscribed On</th>
                    <th class="py-3 px-4 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($subscribers)): ?>
                    <?php foreach ($subscribers as $subscriber): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-4 px-4"><?php echo htmlspecialchars($subscriber['id']); ?></td>
                            <td class="py-4 px-4"><?php echo htmlspecialchars($subscriber['email']); ?></td>
                            <td class="py-4 px-4"><?php echo date('F j, Y, g:i a', strtotime($subscriber['created_at'])); ?></td>
                            <td class="py-4 px-4">
                                <a href="?delete_id=<?php echo $subscriber['id']; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this subscriber?')"
                                   class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">No subscribers found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-6 flex justify-center">
            <div class="flex">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                       class="px-4 py-2 mx-1 <?php echo ($page == $i) ? 'bg-blue-500 text-white' : 'bg-white text-blue-500'; ?> border rounded">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include_once '../../includes/admin_footer.php'; ?>
