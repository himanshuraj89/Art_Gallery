<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/header.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-blue-50 to-indigo-50">
    <div class="max-w-md w-full m-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 transform transition-all duration-300 hover:shadow-2xl">
            <!-- Logo/Icon -->
            <div class="text-center mb-8">
                <div class="mx-auto h-16 w-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center transform transition-transform duration-300 hover:scale-110">
                    <i class="fas fa-user-plus text-3xl text-white"></i>
                </div>
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Create Account</h2>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                    <p class="text-sm text-red-600"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
                </div>
            <?php endif; ?>

            <form action="../../actions/register.php" method="POST" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <div class="relative">
                        <input type="text" name="name" required 
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                               placeholder="John Doe">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>

                <div class="group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <div class="relative">
                        <input type="email" name="email" required 
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                               placeholder="your@email.com">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                </div>

                <div class="group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" name="password" required 
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                               placeholder="••••••••">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                </div>

                <div class="group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <input type="password" name="confirm_password" required 
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                               placeholder="••••••••">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                </div>

                <div class="group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Type</label>
                    <div class="relative">
                        <select name="account_type" required 
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                            <option value="user">Regular User</option>
                            <option value="contributor">Artist/Contributor</option>
                        </select>
                    </div>
                </div>
                
                <!-- Add contributor specific fields -->
                <div id="contributorFields" class="hidden space-y-5">
                    <div class="group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Artist Bio</label>
                        <textarea name="artist_bio" 
                                  class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                  rows="3"></textarea>
                    </div>
                </div>

                <script>
                    document.querySelector('select[name="account_type"]').addEventListener('change', function() {
                        document.getElementById('contributorFields').classList.toggle('hidden', this.value !== 'contributor');
                    });
                </script>

                <button type="submit" 
                        class="w-full py-2 px-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:opacity-90 transform transition-all duration-300 hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Account
                </button>

                <div class="mt-6 text-center text-sm">
                    <a href="login.php" class="text-blue-600 hover:text-blue-700 hover:underline">
                        Already have an account? Sign in
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
