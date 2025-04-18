<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';
?>

<main class="bg-gray-50">
    <!-- Compact Hero Section -->
    <div class="bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-5 bg-white sm:pb-10 lg:max-w-2xl lg:w-full">
                <div class="mt-6 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-3xl tracking-tight font-extrabold text-gray-900 sm:text-4xl md:text-5xl">
                            <span class="inline">Welcome to </span>
                            <span class="text-indigo-600 inline">Art Gallery</span>
                        </h1>
                        <p class="mt-2 text-sm text-gray-500 sm:text-base md:text-lg max-w-xl">
                            Discover unique artworks from talented artists around the world
                        </p>
                        <div class="mt-5 sm:flex sm:justify-center lg:justify-start">
                            <a href="pages/public/gallery.php" 
                               class="group relative inline-flex items-center justify-center overflow-hidden rounded-lg bg-gradient-to-br from-purple-600 to-indigo-500 p-0.5 text-sm font-medium text-white hover:text-white focus:outline-none focus:ring-4 focus:ring-indigo-300 group-hover:from-purple-600 group-hover:to-blue-500 transition-all duration-500 ease-in-out shadow-lg hover:shadow-xl">
                                <span class="relative flex items-center rounded-md bg-indigo-600 px-6 py-2.5 transition-all duration-300 ease-in-out group-hover:bg-opacity-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transform group-hover:rotate-12 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Browse Gallery
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Featured Artworks section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Featured Artworks
                    <span class="block mt-1 text-sm font-normal text-gray-500">Curated selection of our finest pieces</span>
                </h2>
            </div>
            <div class="flex space-x-4">
                <div class="text-center">
                    <span class="block text-xl font-bold text-indigo-600">200+</span>
                    <span class="text-xs text-gray-500">Artworks</span>
                </div>
                <div class="text-center">
                    <span class="block text-xl font-bold text-indigo-600">50+</span>
                    <span class="text-xs text-gray-500">Artists</span>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $query = "SELECT * FROM artworks ORDER BY created_at DESC LIMIT 6";
            $result = $conn->query($query);
            
            while($artwork = $result->fetch_assoc()):
            ?>
                <div class="bg-white rounded-lg shadow overflow-hidden transition duration-200 hover:translate-y-[-4px] hover:shadow-md">
                    <div class="relative">
                        <img src="<?php echo get_image_url($artwork['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                             class="w-full h-56 object-cover">
                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-1 text-xs font-semibold bg-indigo-600 text-white rounded-full shadow-sm">
                                <?php echo htmlspecialchars($artwork['category']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 transition duration-200 hover:text-indigo-600">
                            <?php echo htmlspecialchars($artwork['title']); ?>
                        </h3>
                        <p class="text-sm text-gray-500">
                            By <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($artwork['artist']); ?></span>
                        </p>
                        <p class="mt-1 text-xl font-bold text-indigo-600">
                            $<?php echo number_format($artwork['price'], 2); ?>
                        </p>
                        <div class="mt-3 flex space-x-2">
                            <a href="pages/public/artwork.php?id=<?php echo $artwork['id']; ?>" 
                               class="flex-1 text-center px-3 py-1.5 border border-transparent rounded text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition duration-200">
                                View Details
                            </a>
                            <?php if ($artwork['stock'] > 0): ?>
                                <form action="actions/cart_add.php" method="POST" class="flex-1">
                                    <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
                                    <input type="hidden" name="artwork_id" value="<?php echo $artwork['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" 
                                            class="w-full px-3 py-1.5 border border-transparent rounded text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 transition duration-200">
                                        <i class="fas fa-cart-plus mr-1"></i> Add to Cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <button disabled 
                                        class="flex-1 px-3 py-1.5 border border-transparent rounded text-sm font-medium text-white bg-gray-300 cursor-not-allowed">
                                    Out of Stock
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Paintings Category Section -->
    <div class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-purple-500 to-indigo-600">Paintings</span>
                    <span class="block mt-1 text-sm font-normal text-gray-500">Vibrant colors and expressive brushwork</span>
                </h2>
                <a href="pages/public/gallery.php?category=paintings" 
                   class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                    View all paintings
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                $query = "SELECT * FROM artworks WHERE category = 'Paintings' ORDER BY created_at DESC LIMIT 4";
                $result = $conn->query($query);
                
                while($artwork = $result->fetch_assoc()):
                ?>
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden transition duration-200 hover:translate-y-[-4px] hover:shadow-md border border-gray-100">
                        <div class="relative">
                            <img src="<?php echo get_image_url($artwork['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                                 class="w-full h-48 object-cover">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                                <h3 class="text-white font-medium truncate"><?php echo htmlspecialchars($artwork['title']); ?></h3>
                                <p class="text-white/80 text-sm">By <?php echo htmlspecialchars($artwork['artist']); ?></p>
                            </div>
                        </div>
                        <div class="p-3 flex justify-between items-center">
                            <span class="font-bold text-indigo-600">$<?php echo number_format($artwork['price'], 2); ?></span>
                            <a href="pages/public/artwork.php?id=<?php echo $artwork['id']; ?>" 
                               class="px-3 py-1 text-xs font-medium text-white bg-indigo-600 rounded-full hover:bg-indigo-700 transition duration-200">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    

    <!-- Random Artwork Cards Section -->
    <div class="bg-white py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-left mb-10">
                <h2 class="text-2xl font-bold text-gray-900">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-pink-500 to-violet-500">Discover Something New</span>
                </h2>
                <p class="mt-2 text-gray-600 max-w-2xl">
                    Each visit shows different artworks from our collection. Refresh to explore more!
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                // Query to get random artworks
                $query = "SELECT * FROM artworks ORDER BY RAND() LIMIT 4";
                $result = $conn->query($query);
                
                while($artwork = $result->fetch_assoc()):
                ?>
                    <div class="group perspective">
                        <div class="relative transform transition-all duration-500 preserve-3d group-hover:rotate-y-12">
                            <!-- Card front -->
                            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                                <div class="relative">
                                    <img src="<?php echo get_image_url($artwork['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                                         class="w-full h-52 object-cover">
                                    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2 py-1 text-xs font-medium bg-white/90 text-gray-800 rounded-full shadow-sm">
                                            <?php echo htmlspecialchars($artwork['category']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-900 truncate group-hover:text-indigo-600 transition duration-200">
                                        <?php echo htmlspecialchars($artwork['title']); ?>
                                    </h3>
                                    <div class="flex justify-between items-center mt-2">
                                        <p class="text-sm text-gray-500">
                                            By <?php echo htmlspecialchars($artwork['artist']); ?>
                                        </p>
                                        <span class="font-bold text-indigo-600">
                                            $<?php echo number_format($artwork['price'], 2); ?>
                                        </span>
                                    </div>
                                    <div class="mt-3 flex space-x-2">
                                        <a href="pages/public/artwork.php?id=<?php echo $artwork['id']; ?>" 
                                            class="flex-1 text-center px-2 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700 transition-colors">
                                            View Details
                                        </a>
                                        <?php if ($artwork['stock'] > 0): ?>
                                            <form action="actions/cart_add.php" method="POST" class="flex-1">
                                                <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
                                                <input type="hidden" name="artwork_id" value="<?php echo $artwork['id']; ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" 
                                                        class="w-full px-2 py-1.5 text-xs font-medium text-white bg-blue-500 rounded hover:bg-blue-600 transition-colors">
                                                    <i class="fas fa-cart-plus mr-1"></i> Add
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button disabled 
                                                    class="flex-1 px-2 py-1.5 text-xs font-medium text-white bg-gray-300 rounded cursor-not-allowed">
                                                Out of Stock
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <div class="mt-8 text-center">
                <a href="pages/public/gallery.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-pink-500 to-violet-500 hover:from-pink-600 hover:to-violet-600 shadow-sm transition duration-300">
                    <i class="fas fa-images mr-2"></i> Explore All Artworks
                </a>
            </div>
        </div>
    </div>

    <!-- Enhanced Categories Section with Modern Styling -->
    <div class="bg-gradient-to-b from-gray-50 to-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-center mb-3 bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-blue-500">
                Explore Art Categories
            </h2>
            <p class="text-gray-600 text-center mb-8 max-w-2xl mx-auto">Discover our diverse collection of artworks across various mediums and styles</p>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php
                $categories = [
                    ['name' => 'Paintings', 'icon' => 'fa-palette', 'count' => '120+', 'color' => 'from-purple-500 to-indigo-600'],
                    ['name' => 'Sculptures', 'icon' => 'fa-monument', 'count' => '45+', 'color' => 'from-blue-500 to-teal-400'],
                    ['name' => 'Photography', 'icon' => 'fa-camera', 'count' => '80+', 'color' => 'from-amber-500 to-pink-500'],
                    ['name' => 'Digital Art', 'icon' => 'fa-desktop', 'count' => '60+', 'color' => 'from-emerald-500 to-blue-500']
                ];
                foreach ($categories as $category):
                ?>
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                        <div class="bg-gradient-to-r <?php echo $category['color']; ?> p-4 flex justify-center items-center">
                            <span class="w-14 h-14 flex items-center justify-center bg-white bg-opacity-20 rounded-full backdrop-blur-sm">
                                <i class="fas <?php echo $category['icon']; ?> text-2xl text-white"></i>
                            </span>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900"><?php echo $category['name']; ?></h3>
                            <p class="text-indigo-600 font-medium text-sm mt-1"><?php echo $category['count']; ?> pieces</p>
                            <a href="pages/public/gallery.php?category=<?php echo strtolower(str_replace(' ', '-', $category['name'])); ?>" 
                               class="mt-3 inline-flex items-center text-sm font-medium text-indigo-600 group-hover:text-indigo-800">
                                Explore 
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    
</main>

<?php require_once 'includes/footer.php'; ?>
