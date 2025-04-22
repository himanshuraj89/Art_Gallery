<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/header.php';

// Helper function to get featured artists
function getFeaturedArtists($conn, $limit = 3) {
    $query = "SELECT DISTINCT artist, COUNT(*) as artwork_count FROM artworks 
              GROUP BY artist ORDER BY artwork_count DESC LIMIT $limit";
    return $conn->query($query);
}

// Helper function to get artist info (in a real app, you'd have an artists table)
function getArtistInfo($artist) {
    // Array of different artist bios for variety
    $artistBios = [
        'A visionary artist known for creative expression and innovative techniques.',
        'Renowned for captivating compositions that blend traditional and modern styles.',
        'An avant-garde creator whose work challenges conventional artistic boundaries.',
        'Celebrated for masterful use of color and emotion in every piece.',
        'A groundbreaking talent with a unique perspective on form and function.',
        'Known for thought-provoking works that tell stories of human experience.',
        'An exceptional artist whose detailed craftsmanship reveals years of dedication.',
        'Recognized for bold experimentation with materials and artistic processes.',
        'A pioneer in contemporary art with influences from global cultural movements.',
        'Distinguished by a signature style that balances complexity with elegant simplicity.'
    ];
    
    // Use a hash of the artist name to select a consistent bio for each artist
    $bioIndex = crc32($artist) % count($artistBios);
    
    // Placeholder data - in a real app, this would come from database
    $artistInfo = [
        'bio' => $artistBios[$bioIndex],
        'specialty' => 'Contemporary, Abstract',
        'experience' => '10+ years',
        'profile_image' => 'https://randomuser.me/api/portraits/men/' . rand(1, 99) . '.jpg'
    ];
    
    return $artistInfo;
}

// Helper function to get artworks by artist
function getArtworksByArtist($conn, $artist, $limit = 4) {
    $artist = $conn->real_escape_string($artist);
    $query = "SELECT * FROM artworks WHERE artist = '$artist' LIMIT $limit";
    return $conn->query($query);
}

// Get all unique artists
$allArtistsQuery = "SELECT DISTINCT artist FROM artworks ORDER BY artist ASC";
$allArtistsResult = $conn->query($allArtistsQuery);
$artistsList = [];
while ($row = $allArtistsResult->fetch_assoc()) {
    $artistsList[] = $row['artist'];
}
?>

<main class="bg-gray-50">
    <!-- Artists Hero Section -->
    <div class="relative bg-gradient-to-r from-indigo-600 to-purple-600 overflow-hidden">
        <div class="absolute inset-0 bg-pattern opacity-10"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="text-center">
                <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
                    Our Featured Artists
                </h1>
                <p class="mt-6 max-w-2xl mx-auto text-xl text-indigo-100">
                    Discover the creative minds behind our stunning collection of artworks
                </p>
                <div class="mt-8 flex justify-center">
                    <div class="inline-flex rounded-full shadow">
                        <a href="#artists-directory" 
                           class="view-all-artists inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-full text-indigo-700 bg-white hover:bg-indigo-50 transition-colors duration-300">
                            View All Artists
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-8 bg-white" style="clip-path: polygon(0 100%, 100% 100%, 100% 0, 50% 100%, 0 0);"></div>
    </div>

    <!-- Featured Artists Section -->
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-12">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-purple-500 to-indigo-600">
                    Meet Our Featured Artists
                </span>
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-16">
                <?php
                $featuredArtists = getFeaturedArtists($conn);
                while ($artist = $featuredArtists->fetch_assoc()):
                    $artistInfo = getArtistInfo($artist['artist']);
                ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition-transform duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="flex flex-col h-full">
                        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-1">
                            <div class="bg-white">
                                <div class="flex items-center p-4 space-x-4">
                                    <div class="flex-shrink-0">
                                        <img class="h-16 w-16 rounded-full object-cover ring-2 ring-indigo-500" 
                                             src="<?php echo $artistInfo['profile_image']; ?>" 
                                             alt="<?php echo htmlspecialchars($artist['artist']); ?>">
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($artist['artist']); ?></h3>
                                        <p class="text-indigo-600 text-sm font-medium"><?php echo $artistInfo['specialty']; ?></p>
                                        <p class="text-gray-500 text-xs mt-1"><?php echo $artistInfo['experience']; ?> • <?php echo $artist['artwork_count']; ?> artworks</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 flex-grow">
                            <p class="text-gray-600 text-sm mb-4">
                                <?php echo $artistInfo['bio']; ?>
                            </p>
                            
                            <div class="mt-2 grid grid-cols-2 gap-2">
                                <?php
                                $artistArtworks = getArtworksByArtist($conn, $artist['artist'], 4);
                                while ($artwork = $artistArtworks->fetch_assoc()):
                                ?>
                                <a href="../public/artwork.php?id=<?php echo $artwork['id']; ?>" 
                                   class="relative block rounded-md overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    <img src="<?php echo get_image_url($artwork['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                                         class="w-full h-24 object-cover">
                                    <div class="absolute inset-0 bg-black bg-opacity-20 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <span class="text-white text-xs font-medium px-2 py-1 bg-indigo-600 rounded-full">View</span>
                                    </div>
                                </a>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                            <a href="#artist-<?php echo urlencode($artist['artist']); ?>" 
                               class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                View all artworks
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    
    <!-- Artists Directory Section -->
    <section id="artists-directory" class="py-16 bg-gradient-to-b from-white to-gray-50 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="anchor-indicator bg-indigo-100 px-4 py-2 rounded-lg text-indigo-700 mb-6 hidden">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>You've reached the Artists Directory</span>
                </div>
            </div>
            
            <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-4">
                Artists Directory
            </h2>
            <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto">
                Browse our complete collection of talented artists and their masterpieces
            </p>
            
            <div class="space-y-16">
                <?php foreach ($artistsList as $index => $artistName): 
                    $artistInfo = getArtistInfo($artistName);
                    $isEven = $index % 2 === 0;
                ?>
                <div id="artist-<?php echo urlencode($artistName); ?>" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <!-- Artist header -->
                    <div class="bg-gradient-to-r <?php echo $isEven ? 'from-indigo-600 to-purple-600' : 'from-pink-500 to-rose-500'; ?> px-6 py-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div class="flex items-center space-x-4">
                                <img class="h-16 w-16 rounded-full object-cover ring-2 ring-white" 
                                     src="<?php echo $artistInfo['profile_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($artistName); ?>">
                                <div>
                                    <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($artistName); ?></h3>
                                    <p class="text-white/80"><?php echo $artistInfo['specialty']; ?></p>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0 flex space-x-3">
                                <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-white/20 hover:bg-white/30 backdrop-blur-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    Follow Artist
                                </a>
                                <a href="../public/contact.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-<?php echo $isEven ? 'indigo' : 'pink'; ?>-700 bg-white hover:bg-gray-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Contact
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Artist bio -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <p class="text-gray-600"><?php echo $artistInfo['bio']; ?></p>
                    </div>
                    
                    <!-- Artist works -->
                    <div class="px-6 py-4">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Featured Works</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <?php
                            $artistArtworks = getArtworksByArtist($conn, $artistName, 8);
                            while ($artwork = $artistArtworks->fetch_assoc()):
                            ?>
                            <a href="../public/artwork.php?id=<?php echo $artwork['id']; ?>" 
                               class="group">
                                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 transition duration-200 group-hover:shadow-md group-hover:-translate-y-1 transform">
                                    <div class="relative">
                                        <img src="<?php echo get_image_url($artwork['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                                             class="w-full h-48 object-cover">
                                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                                            <h3 class="text-white font-medium truncate text-sm">
                                                <?php echo htmlspecialchars($artwork['title']); ?>
                                            </h3>
                                            <p class="text-white/70 text-xs">
                                                $<?php echo number_format($artwork['price'], 2); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<style>
html {
    scroll-behavior: smooth;
}

.bg-pattern {
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.15'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.anchor-indicator {
    animation: fadeOut 5s forwards;
    animation-delay: 3s;
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; visibility: hidden; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the button and target element
    const viewAllBtn = document.querySelector('.view-all-artists');
    const targetSection = document.getElementById('artists-directory');
    const anchorIndicator = targetSection.querySelector('.anchor-indicator');
    
    if(viewAllBtn && targetSection) {
        viewAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Scroll to the target section with some offset
            const yOffset = -20; // Adjust this value if needed
            const y = targetSection.getBoundingClientRect().top + window.pageYOffset + yOffset;
            
            window.scrollTo({
                top: y,
                behavior: 'smooth'
            });
            
            // Show the indicator
            setTimeout(() => {
                anchorIndicator.classList.remove('hidden');
            }, 800);
            
            // Update URL with the hash
            history.pushState(null, null, '#artists-directory');
        });
    }
    
    // Check if URL contains hash on page load and scroll to it
    if(window.location.hash === '#artists-directory') {
        setTimeout(() => {
            const yOffset = -20;
            const y = targetSection.getBoundingClientRect().top + window.pageYOffset + yOffset;
            
            window.scrollTo({
                top: y,
                behavior: 'smooth'
            });
            
            anchorIndicator.classList.remove('hidden');
        }, 500);
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>
