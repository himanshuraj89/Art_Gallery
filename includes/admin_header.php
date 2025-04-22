<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

$base_url = '/newf';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Art Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'admin-primary': '#1e293b',
                        'admin-secondary': '#334155',
                        'admin-accent': '#4f46e5',
                        'admin-light': '#f8fafc',
                        'indigo-400': '#818cf8',
                        'indigo-500': '#6366f1',
                        'indigo-600': '#4f46e5',
                    }
                }
            }
        }
    </script>
    <style>
        .admin-nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        .admin-nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #818cf8;
            transition: width 0.3s ease;
        }
        .admin-nav-link:hover:after {
            width: 100%;
        }
        .admin-nav-link.active {
            color: white;
            background-color: rgba(79, 70, 229, 0.2);
            border-left: 3px solid #4f46e5;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Admin notification bar -->
    <div class="bg-indigo-600 text-white text-center py-1.5 text-sm font-medium">
        <p>Admin Dashboard — Manage your art gallery content here</p>
    </div>
    
    <nav class="bg-gradient-to-r from-gray-900 to-indigo-900 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo $base_url; ?>/pages/admin/dashboard.php" class="flex items-center text-white font-bold">
                        <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center mr-3">
                            <i class="fas fa-palette text-white"></i>
                        </div>
                        <div>
                            <span class="text-xl font-serif tracking-wide text-white">ArtSpace</span>
                            <span class="text-xs block text-gray-300">Admin Control Panel</span>
                        </div>
                    </a>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="<?php echo $base_url; ?>/pages/admin/dashboard.php" 
                               class="admin-nav-link text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-tachometer-alt mr-2 text-indigo-400"></i>Dashboard
                            </a>
                            <a href="<?php echo $base_url; ?>/pages/admin/artworks.php" 
                               class="admin-nav-link text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-image mr-2 text-indigo-400"></i>Artworks
                            </a>
                            <a href="<?php echo $base_url; ?>/pages/admin/orders.php" 
                               class="admin-nav-link text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-shopping-cart mr-2 text-indigo-400"></i>Orders
                            </a>
                            <a href="<?php echo $base_url; ?>/pages/admin/reservations.php" 
                               class="admin-nav-link text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-calendar-check mr-2 text-indigo-400"></i>Reservations
                            </a>
                            <a href="<?php echo $base_url; ?>/pages/admin/users.php" 
                               class="admin-nav-link text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-users mr-2 text-indigo-400"></i>Users
                            </a>
                            <a href="<?php echo $base_url; ?>/pages/admin/messages.php" 
                               class="admin-nav-link text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-envelope mr-2 text-indigo-400"></i>Messages
                            </a>
                           
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="<?php echo $base_url; ?>" 
                       class="text-gray-300 hover:text-white bg-gray-800 hover:bg-indigo-600 px-4 py-2 rounded-md text-sm font-medium transition duration-300"
                       target="_blank">
                        <i class="fas fa-external-link-alt mr-2"></i>Visit Site
                    </a>
                    <!-- Admin dropdown -->
                    <div class="relative group">
                        <button class="text-gray-300 hover:text-white px-3 py-2 border border-gray-700 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-300">
                            <i class="fas fa-user-shield mr-1"></i>
                            <span class="ml-1 hidden sm:inline-block">Admin</span>
                        </button>
                        
                        <div class="hidden group-hover:block absolute right-0 w-48 py-2 bg-gray-900 rounded-md shadow-lg z-20 border border-gray-700">
                            <a href="<?php echo $base_url; ?>/pages/public/profile.php" class="block px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
                                <i class="fas fa-user mr-2 text-indigo-400"></i> My Profile
                            </a>
                            <a href="<?php echo $base_url; ?>/pages/admin/settings.php" class="block px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
                                <i class="fas fa-cog mr-2 text-indigo-400"></i> Settings
                            </a>
                            <hr class="my-1 border-gray-700">
                            <a href="<?php echo $base_url; ?>/actions/logout.php" class="block px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
                                <i class="fas fa-sign-out-alt mr-2 text-indigo-400"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile menu button and dropdown (hidden by default) -->
            <div class="md:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="<?php echo $base_url; ?>/pages/admin/dashboard.php" class="admin-nav-link block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-800">
                        <i class="fas fa-tachometer-alt mr-2 text-indigo-400"></i>Dashboard
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/admin/artworks.php" class="admin-nav-link block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-800">
                        <i class="fas fa-image mr-2 text-indigo-400"></i>Artworks
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/admin/orders.php" class="admin-nav-link block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-800">
                        <i class="fas fa-shopping-cart mr-2 text-indigo-400"></i>Orders
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/admin/reservations.php" class="admin-nav-link block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-800">
                        <i class="fas fa-calendar-check mr-2 text-indigo-400"></i>Reservations
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/admin/users.php" class="admin-nav-link block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-800">
                        <i class="fas fa-users mr-2 text-indigo-400"></i>Users
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/admin/messages.php" class="admin-nav-link block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-800">
                        <i class="fas fa-envelope mr-2 text-indigo-400"></i>Messages
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/admin/subscribers.php" class="admin-nav-link block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-800">
                        <i class="fas fa-user-friends mr-2 text-indigo-400"></i>Subscribers
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="flex-grow">
