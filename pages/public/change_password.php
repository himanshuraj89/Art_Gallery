<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-100 via-purple-50 to-pink-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 transform transition duration-500 hover:scale-105">
        <div class="bg-white p-8 rounded-xl shadow-2xl">
            <div class="text-center">
                <h2 class="mt-2 text-3xl font-extrabold text-gray-900 select-none">
                    Change Password
                </h2>
                <p class="mt-2 text-sm text-gray-600 select-none">
                    Update your account password
                </p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mt-4 bg-red-50 border-l-4 border-red-400 p-4">
                    <p class="text-sm text-red-700">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="mt-4 bg-green-50 border-l-4 border-green-400 p-4">
                    <p class="text-sm text-green-700">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </p>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" action="../../actions/update_password.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Current Password
                    </label>
                    <input type="password" name="current_password" required
                           class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        New Password
                    </label>
                    <input type="password" name="new_password" required
                           class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Confirm New Password
                    </label>
                    <input type="password" name="confirm_password" required
                           class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Password
                </button>

                <div class="text-center">
                    <a href="profile.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Back to Profile
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
