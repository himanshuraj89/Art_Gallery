<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$base_url = '/newf';
 
// Helper function to generate correct image URLs
function get_image_url($relative_path) {
    global $base_url;
    if (!$relative_path) {
        return $base_url . '/assets/images/placeholder.jpg';
    }
    
    // Handle different path formats
    if (strpos($relative_path, 'http://') === 0 || strpos($relative_path, 'https://') === 0) {
        return $relative_path;
    }
    
    // Remove any leading slashes
    $relative_path = ltrim($relative_path, '/');
    
    // If path starts with uploads or assets, add base_url
    if (strpos($relative_path, 'uploads/') === 0 || strpos($relative_path, 'assets/') === 0) {
        return $base_url . '/' . $relative_path;
    }
    
    // For all other cases, assume it's a relative path from base_url
    return $base_url . '/' . $relative_path;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'art-primary': '#4A3D69',
                        'art-secondary': '#A17C6B',
                        'art-accent': '#E5B299',
                        'art-light': '#F9F4F0',
                        'indigo-400': '#818cf8',
                        'indigo-500': '#6366f1',
                        'indigo-600': '#4f46e5',
                    }
                }
            }
        }

        // Add menu toggle function
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.querySelector('[data-menu-toggle]');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (menuButton && mobileMenu) {
                menuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
    </script>
    <style>
        .nav-link {
            position: relative;
        }
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #818cf8;
            transition: width 0.3s ease;
        }
        .nav-link:hover:after {
            width: 100%;
        }
    </style>
</head>
<body class="bg-art-light min-h-screen flex flex-col">
    <header class="bg-gradient-to-r from-gray-900 to-indigo-900 text-white shadow-md">
        <!-- Announcement bar -->
        <div class="bg-indigo-600 text-white text-center py-2 text-sm">
            <p>Discover new artwork arrivals every week — <a href="<?php echo $base_url; ?>/pages/public/gallery.php" class="underline hover:text-gray-200">Browse latest collections</a></p>
        </div>
        
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center">
                    <!-- Logo -->
                    <a href="<?php echo $base_url; ?>" class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center mr-3">
                            <i class="fas fa-paint-brush text-white"></i>
                        </div>
                        <div>
                            <span class="text-2xl font-serif tracking-wide text-white">ArtSpace</span>
                            <span class="text-xs block text-gray-300">Fine Art Gallery</span>
                        </div>
                    </a>
                    
                    <!-- Main Navigation -->
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
                        <a href="<?php echo $base_url; ?>/pages/public/gallery.php" class="text-gray-300 hover:text-white flex items-center group px-3 py-2 font-medium">
                            <span class="group-hover:underline decoration-indigo-400 underline-offset-2">Gallery</span>
                        </a>
                        <a href="<?php echo $base_url; ?>/pages/public/artists.php" class="text-gray-300 hover:text-white flex items-center group px-3 py-2 font-medium">
                            <span class="group-hover:underline decoration-indigo-400 underline-offset-2">Artists</span>
                        </a>
                        <a href="<?php echo $base_url; ?>/pages/public/about.php" class="text-gray-300 hover:text-white flex items-center group px-3 py-2 font-medium">
                            <span class="group-hover:underline decoration-indigo-400 underline-offset-2">About</span>
                        </a>
                        <a href="<?php echo $base_url; ?>/pages/public/contact.php" class="text-gray-300 hover:text-white flex items-center group px-3 py-2 font-medium">
                            <span class="group-hover:underline decoration-indigo-400 underline-offset-2">Contact</span>
                        </a>
                        <a href="<?php echo $base_url; ?>/pages/public/reservation.php" class="text-gray-300 hover:text-white flex items-center group px-3 py-2 font-medium">
                            <span class="group-hover:underline decoration-indigo-400 underline-offset-2">
                                <i class="fas fa-calendar-check mr-1"></i>Reservation
                            </span>
                        </a>
                        <a href="<?php echo $base_url; ?>/pages/public/virtual-tour.php" class="text-gray-300 hover:text-white flex items-center group px-3 py-2 font-medium">
                            <span class="group-hover:underline decoration-indigo-400 underline-offset-2">Virtual Tour</span>
                        </a>
                    </div>
                </div>
                
                <!-- Search, Cart, and User Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Search removed as requested -->
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Cart with item count -->
                        <a href="<?php echo $base_url; ?>/pages/public/cart.php" class="text-gray-300 hover:text-white px-3 py-2 relative transition hover:scale-110 transform">
                            <i class="fas fa-shopping-cart"></i>
                            <?php
                            $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                            if ($cart_count > 0): 
                            ?>
                            <span class="absolute -top-1 -right-1 bg-indigo-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                <?php echo $cart_count; ?>
                            </span>
                            <?php endif; ?>
                        </a>
                        
                        <!-- User dropdown -->
                        <div class="ml-3 relative group">
                            <button class="text-gray-300 hover:text-white px-3 py-2 border border-gray-700 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-300">
                                <i class="fas fa-user"></i>
                                <span class="ml-2 hidden sm:inline-block"><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Account'; ?></span>
                            </button>
                            
                            <div class="hidden group-hover:block absolute right-0 w-48 py-2 bg-gray-900 rounded-md shadow-lg z-20 border border-gray-700">
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <a href="<?php echo $base_url; ?>/pages/admin/dashboard.php" class="block px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
                                        <i class="fas fa-tachometer-alt mr-2 text-indigo-400"></i> Admin Dashboard
                                    </a>
                                <?php elseif ($_SESSION['role'] === 'contributor'): ?>
                                    <a href="<?php echo $base_url; ?>/pages/contributor/dashboard.php" class="block px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
                                        <i class="fas fa-palette mr-2 text-indigo-400"></i> Artist Dashboard
                                    </a>
                                    <a href="<?php echo $base_url; ?>/pages/contributor/submit_artwork.php" class="block px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
                                        <i class="fas fa-upload mr-2 text-indigo-400"></i> Submit Artwork
                                    </a>
                                    <a href="<?php echo $base_url; ?>/pages/contributor/my_submissions.php" class="block px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
                                        <i class="fas fa-images mr-2 text-indigo-400"></i> My Submissions
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo $base_url; ?>/pages/public/profile.php" class="block px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
                                    <i class="fas fa-user mr-2 text-indigo-400"></i> My Profile
                                </a>
                                <hr class="my-1 border-gray-700">
                                <a href="<?php echo $base_url; ?>/actions/logout.php" class="block px-4 py-2 text-gray-300 hover:bg-gray-800 hover:text-white">
                                    <i class="fas fa-sign-out-alt mr-2 text-indigo-400"></i> Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo $base_url; ?>/pages/public/login.php" class="text-gray-300 hover:text-white transition-all duration-300 hover:underline decoration-indigo-400 underline-offset-2 px-3 py-2">Login</a>
                        <a href="<?php echo $base_url; ?>/pages/public/register.php" class="ml-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-500 transition duration-300 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Register
                        </a>
                    <?php endif; ?>
                    
                    <!-- Mobile menu button -->
                    <button data-menu-toggle class="md:hidden p-2 rounded-md text-gray-300 hover:text-white hover:bg-gray-800 focus:outline-none transition duration-300">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile navigation (collapsed by default) -->
            <div class="md:hidden hidden" id="mobile-menu">
                <!-- Mobile search removed as requested -->
                <div class="px-2 pt-2 pb-3">
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'contributor'): ?>
                        <a href="<?php echo $base_url; ?>/pages/contributor/dashboard.php" class="block py-2 text-base font-medium text-gray-300 hover:text-white">Artist Dashboard</a>
                        <a href="<?php echo $base_url; ?>/pages/contributor/submit_artwork.php" class="block py-2 text-base font-medium text-gray-300 hover:text-white">Submit Artwork</a>
                        <a href="<?php echo $base_url; ?>/pages/contributor/my_submissions.php" class="block py-2 text-base font-medium text-gray-300 hover:text-white">My Submissions</a>
                    <?php endif; ?>
                    <a href="<?php echo $base_url; ?>/pages/public/gallery.php" class="block py-2 text-base font-medium text-gray-300 hover:text-white flex items-center group">
                        <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">Gallery</span>
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/public/artists.php" class="block py-2 text-base font-medium text-gray-300 hover:text-white flex items-center group">
                        <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">Artists</span>
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/public/exhibitions.php" class="block py-2 text-base font-medium text-gray-300 hover:text-white flex items-center group">
                        <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">Exhibitions</span>
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/public/about.php" class="block py-2 text-base font-medium text-gray-300 hover:text-white flex items-center group">
                        <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">About</span>
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/public/contact.php" class="block py-2 text-base font-medium text-gray-300 hover:text-white flex items-center group">
                        <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">Contact</span>
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/public/reservation.php" class="block py-2 text-base font-medium text-gray-300 hover:text-white flex items-center group">
                        <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i>
                        <i class="fas fa-calendar-check mr-1"></i>
                        <span class="group-hover:underline">Reservation</span>
                    </a>
                    <a href="<?php echo $base_url; ?>/pages/public/virtual-tour.php" class="block py-2 text-base font-medium text-gray-300 hover:text-white flex items-center group">
                        <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">Virtual Tour</span>
                    </a>
                </div>
            </div>
        </nav>
    </header>
    <div class="flex-grow">
