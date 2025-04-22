<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/header.php';

// Define valid categories
$valid_categories = ["Paintings", "Sculptures", "Photography", "Digital Art", "Mixed Media", "Prints"];

// Filter parameters
$category = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Validate category
if ($category && !in_array($category, $valid_categories)) {
    $category = null;
}

// Build query
$query = "SELECT * FROM artworks WHERE 1=1";
$params = [];
$types = "";

if ($category !== null) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

if ($search) {
    $query .= " AND (title LIKE ? OR description LIKE ? OR artist LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss";
}

if ($min_price !== null) {
    $query .= " AND price >= ?";
    $params[] = $min_price;
    $types .= "d";
}

if ($max_price !== null) {
    $query .= " AND price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

// Add sorting
switch ($sort) {
    case 'price_low':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_high':
        $query .= " ORDER BY price DESC";
        break;
    case 'oldest':
        $query .= " ORDER BY created_at ASC";
        break;
    default:
        $query .= " ORDER BY created_at DESC";
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="lg:grid lg:grid-cols-4 lg:gap-8">
        <!-- Filters -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-6 sticky top-6 shadow-sm border border-gray-100 backdrop-blur-sm">
                <h2 class="text-xl font-medium text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">Refine Results</span>
                </h2>
                <form action="" method="get" class="space-y-6">
                    <!-- Price Range -->
                    <div class="group">
                        <label class="text-sm font-medium text-gray-700 mb-3 flex items-center group-hover:text-indigo-600 transition-colors">
                            <span class="mr-2">Price Range</span>
                            <div class="h-px bg-gradient-to-r from-indigo-100 to-purple-100 flex-1"></div>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-indigo-400">$</span>
                                </div>
                                <input type="number" name="min_price" placeholder="Min" 
                                    value="<?php echo ($min_price !== null) ? $min_price : ''; ?>"
                                    class="w-full pl-7 pr-3 py-3 bg-white border border-gray-200 rounded-lg text-sm transition-all hover:border-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:bg-white">
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-indigo-400">$</span>
                                </div>
                                <input type="number" name="max_price" placeholder="Max" 
                                    value="<?php echo ($max_price !== null) ? $max_price : ''; ?>"
                                    class="w-full pl-7 pr-3 py-3 bg-white border border-gray-200 rounded-lg text-sm transition-all hover:border-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="group">
                        <label class="text-sm font-medium text-gray-700 mb-3 flex items-center group-hover:text-indigo-600 transition-colors">
                            <span class="mr-2">Sort By</span>
                            <div class="h-px bg-gradient-to-r from-indigo-100 to-purple-100 flex-1"></div>
                        </label>
                        <div class="relative">
                            <select name="sort" class="w-full appearance-none px-4 py-3 bg-white border border-gray-200 rounded-lg text-sm transition-all hover:border-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:bg-white">
                                <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Latest Arrivals</option>
                                <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                            </select>
                            <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 space-y-3">
                        <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-medium rounded-lg transition-all hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:transform active:scale-[0.98]">
                            Apply Filters
                        </button>
                        <a href="gallery.php" class="block w-full px-4 py-3 bg-white text-indigo-600 text-sm font-medium rounded-lg transition-all hover:bg-gray-50 text-center border border-gray-200 hover:border-indigo-200">
                            Clear All
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Gallery Grid -->
        <div class="mt-6 lg:mt-0 lg:col-span-3">
            <!-- Search -->
            <div class="mb-6">
                <form class="flex items-center shadow-sm">
                    <input type="text" 
                           name="search" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search artworks..."
                           class="flex-1 px-4 py-3 text-gray-600 bg-white border-t border-b border-l border-gray-300 rounded-l focus:outline-none focus:border-indigo-500">
                    
                    <!-- Keep the hidden inputs for maintaining filter state -->
                    <?php if ($category): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                    <?php endif; ?>
                    
                    <?php if ($min_price): ?>
                    <input type="hidden" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>">
                    <?php endif; ?>
                    
                    <?php if ($max_price): ?>
                    <input type="hidden" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>">
                    <?php endif; ?>
                    
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                    
                    <button type="submit" 
                            class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 border border-indigo-600 rounded-r hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </button>
                </form>
            </div>
            
            <!-- Compact Page Header with Animation -->
            <div class="relative mb-6 overflow-hidden bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-md">
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <defs>
                            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5" />
                            </pattern>
                        </defs>
                        <rect width="100" height="100" fill="url(#grid)" />
                    </svg>
                </div>
                
                <div class="relative py-4 px-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <h1 class="text-xl font-bold text-white">Art Gallery Collection</h1>
                            <p class="text-indigo-100 text-xs">Discover exceptional artwork from talented artists</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 text-center">
                        <?php
                        // Count artworks per category
                        $stmt = $conn->prepare("SELECT category, COUNT(*) as count FROM artworks GROUP BY category");
                        $stmt->execute();
                        $categoryStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        $totalArtworks = 0;
                        $categoryData = [];
                        
                        foreach ($categoryStats as $stat) {
                            $totalArtworks += $stat['count'];
                            $categoryData[$stat['category']] = $stat['count'];
                        }
                        ?>
                        <div class="bg-white/20 backdrop-blur-sm px-3 py-2 rounded-lg text-xs">
                            <span class="text-white font-bold block"><?php echo $totalArtworks; ?></span>
                            <span class="text-indigo-100 text-xs">Artworks</span>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm px-3 py-2 rounded-lg text-xs">
                            <span class="text-white font-bold block"><?php echo count($categoryData); ?></span>
                            <span class="text-indigo-100 text-xs">Categories</span>
                        </div>
                    </div>
                </div>
                
                <!-- Abstract decorative element -->
                <div class="absolute -bottom-3 -right-3 w-12 h-12 bg-purple-500/20 rounded-full blur-xl"></div>
            </div>

            <!-- Category Quick Filters -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-700 to-purple-700">Browse by Category</span>
                </h3>
                <div class="flex flex-wrap gap-2">
                    <a href="gallery.php<?php echo $search ? '?search='.urlencode($search) : ''; ?><?php echo $min_price ? ($search ? '&' : '?').'min_price='.$min_price : ''; ?><?php echo $max_price ? (($search || $min_price) ? '&' : '?').'max_price='.$max_price : ''; ?><?php echo $sort ? (($search || $min_price || $max_price) ? '&' : '?').'sort='.$sort : ''; ?>" 
                       class="px-3 py-1.5 rounded-lg text-xs font-medium inline-flex items-center gap-1.5 
                       <?php echo !$category ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?> 
                       transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span>All</span>
                        <?php if (isset($totalArtworks)): ?>
                        <span class="ml-1 px-1.5 py-0.5 rounded-full bg-white/20 text-xs <?php echo !$category ? 'text-white' : 'text-indigo-600 bg-indigo-100'; ?>">
                            <?php echo $totalArtworks; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    
                    <?php 
                    // Define simplified category icons (smaller)
                    $categoryIcons = [
                        "Paintings" => '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>',
                        "Sculptures" => '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /></svg>',
                        "Photography" => '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /></svg>',
                        "Digital Art" => '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 01-2 2v10a2 2 0 002 2z" /></svg>',
                        "Mixed Media" => '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>',
                        "Prints" => '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>'
                    ];
                    ?>
                    
                    <?php foreach ($valid_categories as $cat): ?>
                    <a href="gallery.php?category=<?php echo urlencode($cat); ?><?php echo $search ? '&search='.urlencode($search) : ''; ?><?php echo $min_price ? '&min_price='.$min_price : ''; ?><?php echo $max_price ? '&max_price='.$max_price : ''; ?><?php echo $sort ? '&sort='.$sort : ''; ?>" 
                       class="px-3 py-1.5 rounded-lg text-xs font-medium inline-flex items-center gap-1.5
                       <?php echo $category === $cat ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?> 
                       transition-all duration-200">
                        <?php echo isset($categoryIcons[$cat]) ? $categoryIcons[$cat] : ''; ?>
                        <span><?php echo htmlspecialchars($cat); ?></span>
                        <?php if (isset($categoryData[$cat])): ?>
                        <span class="ml-1 px-1.5 py-0.5 rounded-full bg-white/20 text-xs <?php echo $category === $cat ? 'text-white' : 'text-indigo-600 bg-indigo-100'; ?>">
                            <?php echo $categoryData[$cat]; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Active Filters Area -->
            <?php if ($category || $search || $min_price || $max_price): ?>
            <div class="mb-8 bg-indigo-50 border border-indigo-100 rounded-xl p-5 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-100 rounded-full opacity-40 -translate-x-10 -translate-y-24"></div>
                <div class="absolute bottom-0 left-0 w-20 h-20 bg-purple-100 rounded-full opacity-40 -translate-x-10 translate-y-10"></div>
                
                <div class="relative">
                    <div class="flex items-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-800">Active Filters</h3>
                        
                        <a href="gallery.php" class="ml-auto text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Clear All Filters
                        </a>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <?php if ($category): ?>
                            <div class="inline-flex items-center bg-indigo-100 text-indigo-800 text-sm font-medium px-3 py-2 rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span>Category: <?php echo htmlspecialchars($category); ?></span>
                                <a href="gallery.php<?php echo $search ? '?search='.urlencode($search) : ''; ?><?php echo $min_price ? ($search ? '&' : '?').'min_price='.$min_price : ''; ?><?php echo $max_price ? (($search || $min_price) ? '&' : '?').'max_price='.$max_price : ''; ?><?php echo $sort ? (($search || $min_price || $max_price) ? '&' : '?').'sort='.$sort : ''; ?>" 
                                   class="ml-2 text-indigo-700 hover:text-indigo-900 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($search): ?>
                            <div class="inline-flex items-center bg-blue-100 text-blue-800 text-sm font-medium px-3 py-2 rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span>Search: "<?php echo htmlspecialchars($search); ?>"</span>
                                <a href="gallery.php<?php echo $category ? '?category='.urlencode($category) : ''; ?><?php echo $min_price ? ($category ? '&' : '?').'min_price='.$min_price : ''; ?><?php echo $max_price ? (($category || $min_price) ? '&' : '?').'max_price='.$max_price : ''; ?><?php echo $sort ? (($category || $min_price || $max_price) ? '&' : '?').'sort='.$sort : ''; ?>" 
                                   class="ml-2 text-blue-700 hover:text-blue-900 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($min_price || $max_price): ?>
                            <div class="inline-flex items-center bg-emerald-100 text-emerald-800 text-sm font-medium px-3 py-2 rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Price: 
                                    <?php if ($min_price && $max_price): ?>
                                        $<?php echo number_format($min_price, 2); ?> - $<?php echo number_format($max_price, 2); ?>
                                    <?php elseif ($min_price): ?>
                                        $<?php echo number_format($min_price, 2); ?>+
                                    <?php else: ?>
                                        Up to $<?php echo number_format($max_price, 2); ?>
                                    <?php endif; ?>
                                </span>
                                <a href="gallery.php<?php echo $category ? '?category='.urlencode($category) : ''; ?><?php echo $search ? ($category ? '&' : '?').'search='.urlencode($search) : ''; ?><?php echo $sort ? (($category || $search) ? '&' : '?').'sort='.$sort : ''; ?>" 
                                   class="ml-2 text-emerald-700 hover:text-emerald-900 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Results summary -->
            <div class="mb-8 bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center">
                        <?php if ($result->num_rows > 0): ?>
                            <div class="flex items-center justify-center bg-indigo-100 text-indigo-600 w-10 h-10 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700 block">
                                    Showing <span class="text-indigo-600 font-semibold"><?php echo $result->num_rows; ?></span> artwork<?php echo $result->num_rows != 1 ? 's' : ''; ?>
                                </span>
                                <?php if ($category || $search || $min_price || $max_price): ?>
                                <span class="text-xs text-gray-500">Filtered results</span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center justify-center bg-amber-100 text-amber-600 w-10 h-10 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-amber-800">No results found</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <?php if ($category): ?>
                            <div class="inline-flex items-center bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-1.5 rounded-md shadow-sm">
                                <span>Category: <?php echo htmlspecialchars($category); ?></span>
                                <a href="gallery.php<?php echo $search ? '?search='.urlencode($search) : ''; ?><?php echo $min_price ? ($search ? '&' : '?').'min_price='.$min_price : ''; ?><?php echo $max_price ? (($search || $min_price) ? '&' : '?').'max_price='.$max_price : ''; ?><?php echo $sort ? (($search || $min_price || $max_price) ? '&' : '?').'sort='.$sort : ''; ?>" 
                                   class="ml-1.5 text-indigo-600 hover:text-indigo-800 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($search): ?>
                            <div class="inline-flex items-center bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-1.5 rounded-md shadow-sm">
                                <span>Search: "<?php echo htmlspecialchars($search); ?>"</span>
                                <a href="gallery.php<?php echo $category ? '?category='.urlencode($category) : ''; ?><?php echo $min_price ? ($category ? '&' : '?').'min_price='.$min_price : ''; ?><?php echo $max_price ? (($category || $min_price) ? '&' : '?').'max_price='.$max_price : ''; ?><?php echo $sort ? (($category || $min_price || $max_price) ? '&' : '?').'sort='.$sort : ''; ?>" 
                                   class="ml-1.5 text-blue-600 hover:text-blue-800 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($min_price || $max_price): ?>
                            <div class="inline-flex items-center bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-1.5 rounded-md shadow-sm">
                                <span>Price: 
                                    <?php if ($min_price && $max_price): ?>
                                        $<?php echo number_format($min_price, 2); ?> - $<?php echo number_format($max_price, 2); ?>
                                    <?php elseif ($min_price): ?>
                                        $<?php echo number_format($min_price, 2); ?>+
                                    <?php else: ?>
                                        Up to $<?php echo number_format($max_price, 2); ?>
                                    <?php endif; ?>
                                </span>
                                <a href="gallery.php<?php echo $category ? '?category='.urlencode($category) : ''; ?><?php echo $search ? ($category ? '&' : '?').'search='.urlencode($search) : ''; ?><?php echo $sort ? (($category || $search) ? '&' : '?').'sort='.$sort : ''; ?>" 
                                   class="ml-1.5 text-emerald-600 hover:text-emerald-800 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($result->num_rows > 0): ?>
                    <div class="flex items-center bg-gray-100 rounded-md px-3 py-2 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                        </svg>
                        <span class="text-xs text-gray-600">
                            Sorted by: <span class="font-semibold text-gray-800">
                                <?php
                                echo $sort === 'price_low' ? 'Price: Low to High' : 
                                    ($sort === 'price_high' ? 'Price: High to Low' : 
                                    ($sort === 'oldest' ? 'Oldest First' : 'Latest Arrivals'));
                                ?>
                            </span>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Results -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($artwork = $result->fetch_assoc()): ?>
                        <div class="group bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 transform">
                            <div class="relative h-60 overflow-hidden">
                                <img src="<?php echo get_image_url($artwork['image_url']); ?>" 
                                    alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                                    class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-110">
                                <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <div class="absolute top-2 right-2 z-10">
                                    <span class="px-2 py-1 text-xs font-semibold bg-indigo-600 text-white rounded-full shadow-sm">
                                        <?php echo htmlspecialchars($artwork['category']); ?>
                                    </span>
                                </div>
                                <?php if($artwork['stock'] <= 3 && $artwork['stock'] > 0): ?>
                                <div class="absolute bottom-2 left-2 z-10">
                                    <span class="px-2 py-1 text-xs font-semibold bg-amber-500 text-white rounded-full shadow-sm animate-pulse">
                                        Only <?php echo $artwork['stock']; ?> left!
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-medium text-gray-900 truncate group-hover:text-indigo-600 transition-colors">
                                    <?php echo htmlspecialchars($artwork['title']); ?>
                                </h3>
                                <p class="text-sm text-gray-500 mb-3">
                                    By <span class="font-medium text-gray-700 hover:text-indigo-600 transition-colors"><?php echo htmlspecialchars($artwork['artist']); ?></span>
                                </p>
                                <div class="flex justify-between items-center">
                                    <p class="text-lg font-bold text-indigo-600">
                                        $<?php echo number_format($artwork['price'], 2); ?>
                                    </p>
                                    <?php if($artwork['stock'] <= 0): ?>
                                    <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded">Out of stock</span>
                                    <?php endif; ?>
                                </div>
                                <a href="artwork.php?id=<?php echo $artwork['id']; ?>" 
                                   class="mt-4 block w-full text-center px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-300 shadow-sm hover:shadow">
                                    View Details
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-full flex flex-col items-center justify-center py-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl shadow-inner border border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-400 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        <p class="text-gray-600 text-xl font-medium mb-4">No artworks found matching your criteria</p>
                        <p class="text-gray-500 mb-6 max-w-md text-center">Try adjusting your filters or search terms to find what you're looking for</p>
                        <a href="gallery.php" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-300 shadow-sm hover:shadow flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Clear Filters
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>
