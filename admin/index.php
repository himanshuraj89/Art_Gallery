<?php
session_start();
// Include authentication check here

// Include header
$page_title = "Admin Dashboard";
include_once "../includes/admin_header.php";
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Existing dashboard cards -->
        
        <!-- New Newsletter Subscribers Card -->
        <a href="subscribers.php" class="block bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Newsletter Subscribers</h2>
                    <p class="text-sm text-gray-600">Manage your newsletter subscribers</p>
                </div>
            </div>
        </a>
        
        <!-- Additional cards as needed -->
    </div>
</div>

<?php include_once "../includes/admin_footer.php"; ?>
