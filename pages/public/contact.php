<?php
session_start();
require_once '../../config/database.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../../includes/header.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Compact Hero Section with Gradient Background -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 mb-8 shadow-lg text-center">
        <h1 class="text-3xl font-bold text-white mb-2">Get In Touch</h1>
        <p class="max-w-xl mx-auto text-base text-indigo-100">We'd love to hear from you about questions, feedback, or collaboration ideas.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <!-- Contact Form - Spans 3 columns -->
        <div class="lg:col-span-3 bg-white rounded-xl shadow-xl p-8 transform transition-all duration-300 hover:shadow-2xl">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">Send Us a Message</h2>
            </div>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-md animate-pulse">
                    <p class="text-red-700"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['contact_success'])): ?>
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-md animate-pulse">
                    <p class="text-green-700"><?php echo $_SESSION['contact_success']; unset($_SESSION['contact_success']); ?></p>
                </div>
            <?php endif; ?>

            <form action="../../actions/submit_contact.php" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="group">
                        <label class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-indigo-600 transition-colors duration-200">Your Name</label>
                        <input type="text" name="name" required
                               class="block w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all duration-200">
                    </div>

                    <div class="group">
                        <label class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-indigo-600 transition-colors duration-200">Email Address</label>
                        <input type="email" name="email" required
                               class="block w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all duration-200">
                    </div>
                </div>

                <div class="group">
                    <label class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-indigo-600 transition-colors duration-200">Subject</label>
                    <input type="text" name="subject" required
                           class="block w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all duration-200">
                </div>

                <div class="group">
                    <label class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-indigo-600 transition-colors duration-200">Your Message</label>
                    <textarea name="message" rows="5" required
                              class="block w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all duration-200"></textarea>
                </div>

                <!-- Simplified reCAPTCHA element -->
                <div class="mt-4">
                    <div class="g-recaptcha" data-sitekey="YOUR_RECAPTCHA_SITE_KEY"></div>
                    <p class="text-xs text-gray-500 mt-1">Security verification</p>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 px-6 rounded-lg font-medium hover:from-indigo-700 hover:to-purple-700 transition duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    Send Message
                </button>
            </form>
        </div>

        <!-- Contact Information - Spans 2 columns -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-gradient-to-br from-white to-indigo-50 rounded-xl shadow-xl p-8 transform transition-all duration-300 hover:shadow-2xl">
                <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 mb-6">Contact Details</h2>
                
                <div class="space-y-6">
                    <div class="flex items-start transform transition-all duration-200 hover:translate-x-2">
                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-indigo-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-900 font-medium">Our Location</p>
                            <p class="text-gray-600">Art Street, Punjab, Phagwara 14414</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start transform transition-all duration-200 hover:translate-x-2">
                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-phone text-indigo-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-900 font-medium">Call Us</p>
                            <p class="text-gray-600">+91 6307254709</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start transform transition-all duration-200 hover:translate-x-2">
                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-envelope text-indigo-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-900 font-medium">Email Us</p>
                            <p class="text-gray-600">ankitvishwa114@gmail.com</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start transform transition-all duration-200 hover:translate-x-2">
                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-indigo-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-900 font-medium">Business Hours</p>
                            <p class="text-gray-600">Mon - Fri: 9AM - 6PM</p>
                            <p class="text-gray-600">Weekends: 10AM - 4PM</p>
                        </div>
                    </div>
                </div>
                
                <!-- Social Media Icons -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-gray-700 font-medium mb-4">Connect With Us</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors duration-300">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Map with styled container -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden transform transition-all duration-300 hover:shadow-2xl">
                <div class="p-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                    <h2 class="text-xl font-bold flex items-center">
                        <i class="fas fa-map-marked-alt mr-2"></i> Find Us
                    </h2>
                </div>
                <div class="h-64">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.30596698947!2d-74.25987025444366!3d40.69714941680757!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY!5e0!3m2!1sen!2sus!4v1689927675197!5m2!1sen!2sus" 
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
    
    <!-- FAQ Section -->
    <div class="mt-16 bg-white rounded-xl shadow-xl p-8">
        <h2 class="text-2xl font-bold text-center text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 mb-8">Frequently Asked Questions</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-indigo-50 rounded-lg p-6 transition-all duration-300 hover:shadow-md">
                <h3 class="text-lg font-semibold text-indigo-700 mb-2">How can I purchase artwork?</h3>
                <p class="text-gray-600">You can browse our gallery and purchase artwork directly through our online store or visit our physical location.</p>
            </div>
            
            <div class="bg-indigo-50 rounded-lg p-6 transition-all duration-300 hover:shadow-md">
                <h3 class="text-lg font-semibold text-indigo-700 mb-2">Do you ship internationally?</h3>
                <p class="text-gray-600">Yes, we offer worldwide shipping with tracking for all purchased artwork.</p>
            </div>
            
            <div class="bg-indigo-50 rounded-lg p-6 transition-all duration-300 hover:shadow-md">
                <h3 class="text-lg font-semibold text-indigo-700 mb-2">Can I commission custom artwork?</h3>
                <p class="text-gray-600">Absolutely! Contact us with your ideas and we'll connect you with artists who can create custom pieces.</p>
            </div>
            
            <div class="bg-indigo-50 rounded-lg p-6 transition-all duration-300 hover:shadow-md">
                <h3 class="text-lg font-semibold text-indigo-700 mb-2">How can artists submit their work?</h3>
                <p class="text-gray-600">Artists can apply through our online submission form or contact our artist relations team directly.</p>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>
