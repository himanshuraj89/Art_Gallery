<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/admin_header.php';

// Debug logging function
function debug_log($message) {
    error_log("[Messages Debug] " . $message);
}

// Verify database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update message status if action is set
if (isset($_POST['action']) && isset($_POST['message_id'])) {
    try {
        $message_id = filter_var($_POST['message_id'], FILTER_VALIDATE_INT);
        if ($message_id === false) {
            throw new Exception("Invalid message ID");
        }

        // Verify the message exists
        $check = $conn->prepare("SELECT id FROM contact_messages WHERE id = ?");
        $check->bind_param("i", $message_id);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Message not found");
        }

        if ($_POST['action'] === 'delete') {
            // Delete the message
            $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("i", $message_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            if ($stmt->affected_rows > 0) {
                $_SESSION['success_message'] = "Message deleted successfully!";
                debug_log("Message deleted ID: " . $message_id);
            } else {
                throw new Exception("No message was deleted");
            }
        } else {
            $status = $_POST['action'] === 'mark_read' ? 'read' : 'responded';
            
            // Update the message status
            $stmt = $conn->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("si", $status, $message_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            if ($stmt->affected_rows > 0) {
                $_SESSION['success_message'] = "Message status updated successfully!";
                debug_log("Status updated for message ID: " . $message_id);
            } else {
                throw new Exception("No rows were updated");
            }
        }
        
    } catch (Exception $e) {
        debug_log("Error: " . $e->getMessage());
        $_SESSION['error_message'] = "Operation failed: " . $e->getMessage();
    }

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Filter status if provided
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$valid_statuses = ['new', 'read', 'responded', ''];

if (!in_array($status_filter, $valid_statuses)) {
    $status_filter = '';
}

// Fetch messages with optional filter
$query = "SELECT * FROM contact_messages";
if (!empty($status_filter)) {
    $query .= " WHERE status = '" . $conn->real_escape_string($status_filter) . "'";
}
$query .= " ORDER BY created_at DESC";
$messages = $conn->query($query);

// Count messages by status
$counts = [
    'total' => 0,
    'new' => 0,
    'read' => 0,
    'responded' => 0
];

$count_query = "SELECT status, COUNT(*) as count FROM contact_messages GROUP BY status";
$count_result = $conn->query($count_query);
if ($count_result && $count_result->num_rows > 0) {
    while ($row = $count_result->fetch_assoc()) {
        $counts[$row['status']] = $row['count'];
        $counts['total'] += $row['count'];
    }
}

// Display notifications
if (isset($_SESSION['success_message']) || isset($_SESSION['error_message'])) {
    $notif_type = isset($_SESSION['success_message']) ? 'success' : 'error';
    $notif_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : $_SESSION['error_message'];
    $notif_bg = $notif_type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
    
    echo '<div id="notification" class="fixed top-4 right-4 ' . $notif_bg . ' px-4 py-3 rounded shadow-lg z-50 transition-opacity duration-500" role="alert">';
    echo '<div class="flex items-center">';
    echo '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">';
    if ($notif_type === 'success') {
        echo '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>';
    } else {
        echo '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>';
    }
    echo '</svg>';
    echo '<span class="font-medium">' . $notif_message . '</span>';
    echo '</div>';
    echo '</div>';
    
    unset($_SESSION['success_message'], $_SESSION['error_message']);
}
?>

<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center p-4 md:p-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900 mb-3 md:mb-0">Contact Messages</h1>
                <div class="flex flex-wrap gap-2">
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="px-3 py-2 text-sm font-medium rounded-md <?php echo empty($status_filter) ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        All (<?php echo $counts['total']; ?>)
                    </a>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?status=new" class="px-3 py-2 text-sm font-medium rounded-md <?php echo $status_filter === 'new' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        New (<?php echo $counts['new']; ?>)
                    </a>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?status=read" class="px-3 py-2 text-sm font-medium rounded-md <?php echo $status_filter === 'read' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        Read (<?php echo $counts['read']; ?>)
                    </a>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?status=responded" class="px-3 py-2 text-sm font-medium rounded-md <?php echo $status_filter === 'responded' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        Responded (<?php echo $counts['responded']; ?>)
                    </a>
                </div>
            </div>

            <?php if ($messages->num_rows > 0): ?>
                <div class="divide-y divide-gray-100">
                    <?php while ($message = $messages->fetch_assoc()): 
                        // Define status-specific styling
                        $status_bg_class = '';
                        $status_color = '';
                        $status_icon_color = '';
                        
                        switch($message['status']) {
                            case 'new':
                                $status_bg_class = 'border-l-4 border-yellow-400';
                                $status_color = 'bg-yellow-100 text-yellow-800';
                                $status_icon_color = 'text-yellow-400';
                                break;
                            case 'read':
                                $status_bg_class = 'border-l-4 border-blue-400';
                                $status_color = 'bg-blue-100 text-blue-800';
                                $status_icon_color = 'text-blue-400';
                                break;
                            case 'responded':
                                $status_bg_class = 'border-l-4 border-green-400';
                                $status_color = 'bg-green-100 text-green-800';
                                $status_icon_color = 'text-green-400';
                                break;
                        }
                    ?>
                        <div class="p-4 sm:p-5 hover:bg-gray-50 transition-colors duration-150 <?php echo $status_bg_class; ?>">
                            <div class="flex flex-col sm:flex-row justify-between">
                                <!-- Header with name, email, status -->
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-base font-semibold text-gray-600"><?php echo strtoupper(substr($message['name'], 0, 1)); ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($message['name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($message['email']); ?></div>
                                        <div class="mt-1 flex items-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?php echo $status_color; ?>">
                                                <svg class="mr-1 h-2 w-2 <?php echo $status_icon_color; ?>" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3" />
                                                </svg>
                                                <?php echo ucfirst($message['status']); ?>
                                            </span>
                                            <span class="ml-2 text-xs text-gray-500">
                                                <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action buttons for desktop -->
                                <div class="hidden sm:flex items-center space-x-2 mt-2 sm:mt-0">
                                    <?php if ($message['status'] === 'new'): ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                            <input type="hidden" name="action" value="mark_read">
                                            <button type="submit" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 rounded text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Mark Read
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" 
                                       onclick="event.preventDefault(); document.getElementById('respond-form-<?php echo $message['id']; ?>').submit();"
                                       class="inline-flex items-center px-2.5 py-1.5 border border-transparent rounded text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                        <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Reply
                                    </a>
                                    <form method="POST" class="inline" id="respond-form-<?php echo $message['id']; ?>">
                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                        <input type="hidden" name="action" value="mark_responded">
                                    </form>
                                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 border border-transparent rounded text-xs font-medium text-white bg-red-600 hover:bg-red-700">
                                            <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Message content with toggle -->
                            <div class="mt-3">
                                <button class="w-full text-left focus:outline-none" id="toggle-msg-<?php echo $message['id']; ?>" onclick="toggleMessage(<?php echo $message['id']; ?>)">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-base font-medium text-gray-900 truncate" style="max-width: 90%;">
                                            <?php echo htmlspecialchars($message['subject']); ?>
                                        </h3>
                                        <svg class="h-4 w-4 text-gray-500 transform transition-transform" id="chevron-<?php echo $message['id']; ?>" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600 truncate hidden sm:block" style="max-width: 95%;">
                                        <?php echo htmlspecialchars(substr($message['message'], 0, 100)) . (strlen($message['message']) > 100 ? '...' : ''); ?>
                                    </p>
                                </button>
                                <div class="mt-2 hidden bg-gray-50 rounded p-3 text-gray-700 whitespace-pre-line text-sm" id="message-<?php echo $message['id']; ?>">
                                    <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                </div>
                            </div>

                            <!-- Action buttons for mobile -->
                            <div class="sm:hidden flex justify-between mt-3 pt-2 border-t border-gray-100">
                                <div class="flex space-x-2">
                                    <?php if ($message['status'] === 'new'): ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                            <input type="hidden" name="action" value="mark_read">
                                            <button type="submit" class="inline-flex items-center px-2 py-1 border border-gray-300 rounded text-xs text-gray-700 bg-white">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" 
                                       onclick="event.preventDefault(); document.getElementById('respond-form-mobile-<?php echo $message['id']; ?>').submit();"
                                       class="inline-flex items-center px-2 py-1 border border-transparent rounded text-xs text-white bg-indigo-600">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST" class="inline" id="respond-form-mobile-<?php echo $message['id']; ?>">
                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                        <input type="hidden" name="action" value="mark_responded">
                                    </form>
                                </div>
                                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="inline-flex items-center px-2 py-1 border border-transparent rounded text-xs text-white bg-red-600">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="mt-4 text-lg font-medium text-gray-900">No messages found</p>
                    <p class="mt-2 text-sm text-gray-500">
                        <?php echo empty($status_filter) ? 'When you receive messages, they will appear here.' : 'No messages with status "' . $status_filter . '" found.'; ?>
                    </p>
                    <?php if (!empty($status_filter)): ?>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="mt-4 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            View all messages
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Toggle message content visibility
function toggleMessage(id) {
    const messageElement = document.getElementById('message-' + id);
    const chevronElement = document.getElementById('chevron-' + id);
    
    if (messageElement.classList.contains('hidden')) {
        messageElement.classList.remove('hidden');
        chevronElement.classList.add('rotate-180');
    } else {
        messageElement.classList.add('hidden');
        chevronElement.classList.remove('rotate-180');
    }
}

// Auto-hide notifications after 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    const notification = document.getElementById('notification');
    if (notification) {
        setTimeout(function() {
            notification.classList.add('opacity-0');
            setTimeout(function() {
                notification.remove();
            }, 500);
        }, 3000);
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>
