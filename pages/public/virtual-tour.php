<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/header.php';

// Add this function after the require statements
function getRandomGalleryBackground() {
    $galleryDir = '../../uploads/artworks/';
    $images = glob($galleryDir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
    
    if (empty($images)) {
        return '../../assets/images/default-gallery.jpg';
    }
    
    $randomImage = $images[array_rand($images)];
    return $randomImage;
}

// Fetch all artworks for the virtual tour
$stmt = $conn->prepare("SELECT * FROM artworks ORDER BY created_at DESC LIMIT 8");
$stmt->execute();
$artworks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!-- Add required libraries -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 text-center">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
            Virtual Art Gallery Tour
        </h1>
        <p class="mt-2 text-gray-600">Experience our gallery in an immersive 360° virtual tour</p>
    </div>
    
    <!-- Virtual Tour Viewer with Controls -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="relative">
            <div id="panorama" class="w-full h-[70vh] rounded-lg"></div>
            <!-- Navigation Controls -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-4 bg-black bg-opacity-50 rounded-full px-6 py-3">
                <button onclick="viewer.startAutoRotate()" class="text-white hover:text-blue-400 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                <button onclick="viewer.zoomIn()" class="text-white hover:text-blue-400 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                    </svg>
                </button>
                <button onclick="viewer.zoomOut()" class="text-white hover:text-blue-400 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Instructions Card -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg">
                <h3 class="font-medium text-blue-900 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                    </svg>
                    Navigation
                </h3>
                <p class="text-sm text-gray-700">Click and drag to look around. Double-click to reset view.</p>
            </div>
            <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg">
                <h3 class="font-medium text-purple-900 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Artworks
                </h3>
                <p class="text-sm text-gray-700">Click on artwork markers (🖼️) to view details and information.</p>
            </div>
            <div class="p-4 bg-gradient-to-br from-pink-50 to-red-50 rounded-lg">
                <h3 class="font-medium text-pink-900 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    Controls
                </h3>
                <p class="text-sm text-gray-700">Use mouse wheel or buttons below to zoom in/out.</p>
            </div>
        </div>
    </div>

    <script>
    const artworkHotspots = [
        <?php foreach ($artworks as $index => $artwork): ?>
        {
            id: "artwork-<?php echo $artwork['id']; ?>",
            // Position artworks along virtual walls
            pitch: <?php echo -5; ?>, // Slightly below eye level
            yaw: <?php echo -160 + ($index * (320 / count($artworks))); ?>, // Spread artworks around the gallery
            type: "info",
            text: "<?php echo htmlspecialchars($artwork['title']); ?>",
            URL: "artwork.php?id=<?php echo $artwork['id']; ?>",
            cssClass: "artwork-frame",
            createTooltipFunc: hotspot => {
                const tooltip = document.createElement('div');
                tooltip.classList.add('artwork-display');
                tooltip.innerHTML = `
                    <div class="virtual-frame">
                        <img src="<?php echo get_image_url($artwork['image_url']); ?>" 
                             class="virtual-artwork">
                        <div class="artwork-info">
                            <h4>${hotspot.text}</h4>
                            <p>By <?php echo htmlspecialchars($artwork['artist']); ?></p>
                            <button onclick="window.location.href='${hotspot.URL}'">
                                View Details
                            </button>
                        </div>
                    </div>
                `;
                return tooltip;
            }
        },
        <?php endforeach; ?>
    ];

    viewer = pannellum.viewer('panorama', {
        type: 'equirectangular',
        panorama: '<?php echo getRandomGalleryBackground(); ?>', // Random gallery image
        autoLoad: true,
        autoRotate: -2,
        compass: false,
        hotSpotDebug: false,
        defaultZoom: 100,
        minZoom: 50,
        maxZoom: 120,
        hotSpots: artworkHotspots,
        controls: {
            mouseZoom: true,
            pan: true,
            touchPanAndZoom: true
        },
        sceneFadeDuration: 1000,
        hfov: 120, // Wider field of view
    });
    </script>

    <style>
    .artwork-frame {
        width: 300px;
        height: 200px;
        background: transparent;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .artwork-display {
        position: absolute;
        transform: translate(-50%, -50%);
        z-index: 100;
        width: 300px;
    }

    .virtual-frame {
        background: white;
        padding: 10px;
        border: 8px solid #8B4513;
        box-shadow: 0 0 30px rgba(0,0,0,0.4);
        border-radius: 2px;
    }

    .virtual-artwork {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border: 1px solid #ddd;
    }

    .artwork-info {
        background: rgba(255,255,255,0.95);
        padding: 10px;
        margin-top: 5px;
        text-align: center;
    }

    .artwork-info h4 {
        font-size: 14px;
        font-weight: bold;
        color: #333;
        margin: 0;
    }

    .artwork-info p {
        font-size: 12px;
        color: #666;
        margin: 5px 0;
    }

    .artwork-info button {
        background: #4F46E5;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        margin-top: 5px;
    }

    #panorama {
        height: 80vh !important;
        background: #f0f0f0;
    }
    </style>

    <!-- Featured Artworks -->
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured in this Tour</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($artworks as $artwork): ?>
            <a href="artwork.php?id=<?php echo htmlspecialchars($artwork['id']); ?>" 
               class="group">
                <div class="relative overflow-hidden rounded-lg">
                    <img src="<?php echo get_image_url($artwork['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                         class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity duration-300"></div>
                </div>
                <h3 class="mt-3 text-sm font-medium text-gray-900">
                    <?php echo htmlspecialchars($artwork['title']); ?>
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    By <?php echo htmlspecialchars($artwork['artist']); ?>
                </p>
            </a>
        <?php endforeach; ?>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>
