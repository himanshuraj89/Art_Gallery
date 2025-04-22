</div>
    <footer class="bg-gradient-to-r from-gray-900 to-indigo-900 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <!-- Newsletter Section -->
            <div class="mb-12 pb-12 border-b border-gray-700/50">
                <div class="max-w-3xl mx-auto text-center">
                    <h3 class="text-2xl font-bold mb-4">Join Our Art Community</h3>
                    <p class="text-gray-300 mb-6">Subscribe to receive updates on new artworks, exhibitions, and special offers</p>
                    
                    <?php if (isset($_SESSION['subscription_status'])): ?>
                        <div class="mb-4 max-w-md mx-auto px-4 py-3 rounded-lg <?php echo $_SESSION['subscription_status'] === 'success' ? 'bg-green-600/30 text-green-100' : 'bg-red-600/30 text-red-100'; ?>">
                            <?php echo $_SESSION['subscription_message']; ?>
                        </div>
                        <?php 
                        // Clear the session variables after displaying
                        unset($_SESSION['subscription_status']);
                        unset($_SESSION['subscription_message']);
                        ?>
                    <?php endif; ?>
                    
                    <form action="includes/subscribe.php" method="POST" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                        <input type="email" name="email" placeholder="Your email address" required
                               class="flex-1 px-4 py-2 bg-gray-800/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-white">
                        <button type="submit" name="subscribe" 
                                class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 transition duration-300 text-white rounded-lg focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Main Footer Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <h3 class="text-xl font-semibold mb-2 flex items-center">
                        <span class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center mr-2">
                            <i class="fas fa-palette text-white text-xs"></i>
                        </span>
                        Art Gallery
                    </h3>
                    <p class="text-gray-300 mb-3 text-sm">Discover unique artworks from talented artists around the world.</p>
                    <div class="flex space-x-3">
                        <a href="#" class="text-gray-400 hover:text-white transition hover:scale-110 transform">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition hover:scale-110 transform">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition hover:scale-110 transform">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition hover:scale-110 transform">
                            <i class="fab fa-pinterest"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-xl font-semibold mb-2">Quick Links</h3>
                    <ul class="space-y-0.5 text-sm">
                        <li>
                            <a href="/newf/pages/public/gallery.php" class="text-gray-300 hover:text-white flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">Gallery</span>
                            </a>
                        </li>
                        <li>
                            <a href="/newf/pages/public/about.php" class="text-gray-300 hover:text-white flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">About Us</span>
                            </a>
                        </li>
                        <li>
                            <a href="/newf/pages/public/contact.php" class="text-gray-300 hover:text-white flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">Contact</span>
                            </a>
                        </li>
                        <li>
                            <a href="/newf/pages/public/artists.php" class="text-gray-300 hover:text-white flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">Artists</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-xl font-semibold mb-2">Art Categories</h3>
                    <ul class="space-y-0.5 text-sm">
                        <li>
                            <a href="/newf/pages/public/gallery.php?category=paintings" class="text-gray-300 hover:text-white flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">Paintings</span>
                            </a>
                        </li>
                        <li>
                            <a href="/newf/pages/public/gallery.php?category=sculptures" class="text-gray-300 hover:text-white flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">Sculptures</span>
                            </a>
                        </li>
                        <li>
                            <a href="/newf/pages/public/gallery.php?category=photography" class="text-gray-300 hover:text-white flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">Photography</span>
                            </a>
                        </li>
                        <li>
                            <a href="/newf/pages/public/gallery.php?category=digital-art" class="text-gray-300 hover:text-white flex items-center group">
                                <i class="fas fa-chevron-right text-xs text-indigo-400 mr-1 group-hover:translate-x-0.5 transition-transform"></i><span class="group-hover:underline">Digital Art</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-xl font-semibold mb-2">Contact Us</h3>
                    <ul class="space-y-2 text-gray-300 text-sm">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-indigo-400"></i>
                            <span></span>Art Street<br>Punjab, Phagwara 14414</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-indigo-400"></i>
                            <a href="mailto:ankitvishwa114@gmail.com" class="hover:text-white transition-all duration-300 hover:underline decoration-indigo-400 underline-offset-2">ankitvishwa114@gmail.com</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3 text-indigo-400"></i>
                            <a href="tel:+916307254709" class="hover:text-white transition-all duration-300 hover:underline decoration-indigo-400 underline-offset-2">
                                +91 6307254709
                            </a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3 text-indigo-400"></i>
                            <span>Mon-Fri: 9AM-6PM<br>Sat-Sun: 10AM-4PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700/50 mt-8 pt-4 text-center">
                <p class="text-gray-400 text-sm">&copy; <?php echo date('Y'); ?> Art Gallery. All rights reserved.</p>
                <div class="mt-2 space-x-3 text-xs text-gray-500">
                    <a href="/newf/pages/public/privacy.php" class="hover:text-gray-300 transition-all duration-300 hover:underline decoration-indigo-400 underline-offset-2">Privacy Policy</a>
                    <span>|</span>
                    <a href="/newf/pages/public/terms.php" class="hover:text-gray-300 transition-all duration-300 hover:underline decoration-indigo-400 underline-offset-2">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
