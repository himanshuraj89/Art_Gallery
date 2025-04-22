<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/admin_header.php';

// In a real application, these would come from a settings table in the database
$site_settings = [
    'site_name' => 'Art Gallery',
    'site_tagline' => 'Discover and buy unique artwork',
    'contact_email' => 'info@artgallery.com',
    'homepage_featured_count' => 6,
    'currency' => 'USD',
    'enable_user_registration' => true,
    'enable_reviews' => true,
    'maintenance_mode' => false
];

// Process settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a real app, you'd validate and save these settings to the database
    $_SESSION['success'] = "Settings updated successfully! (Note: This is a demo)";
    header("Location: settings.php");
    exit();
}
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Site Settings</h1>
            <a href="dashboard.php" class="text-indigo-600 hover:text-indigo-900">← Back to Dashboard</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Settings Form -->
        <form method="POST" action="" class="mt-8 space-y-8">
            <!-- General Settings -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        General Settings
                    </h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="site_name" class="block text-sm font-medium text-gray-700">
                                Site Name
                            </label>
                            <div class="mt-1">
                                <input type="text" name="site_name" id="site_name" 
                                    value="<?php echo htmlspecialchars($site_settings['site_name']); ?>"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="site_tagline" class="block text-sm font-medium text-gray-700">
                                Site Tagline
                            </label>
                            <div class="mt-1">
                                <input type="text" name="site_tagline" id="site_tagline" 
                                    value="<?php echo htmlspecialchars($site_settings['site_tagline']); ?>"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="contact_email" class="block text-sm font-medium text-gray-700">
                                Contact Email
                            </label>
                            <div class="mt-1">
                                <input type="email" name="contact_email" id="contact_email" 
                                    value="<?php echo htmlspecialchars($site_settings['contact_email']); ?>"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="currency" class="block text-sm font-medium text-gray-700">
                                Currency
                            </label>
                            <div class="mt-1">
                                <select id="currency" name="currency" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="USD" <?php echo $site_settings['currency'] === 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                                    <option value="EUR" <?php echo $site_settings['currency'] === 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                                    <option value="GBP" <?php echo $site_settings['currency'] === 'GBP' ? 'selected' : ''; ?>>GBP (£)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="homepage_featured_count" class="block text-sm font-medium text-gray-700">
                                Homepage Featured Artworks
                            </label>
                            <div class="mt-1">
                                <input type="number" name="homepage_featured_count" id="homepage_featured_count" 
                                    value="<?php echo htmlspecialchars($site_settings['homepage_featured_count']); ?>"
                                    min="0" max="20"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature Toggles -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Feature Toggles
                    </h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="enable_user_registration" name="enable_user_registration" type="checkbox"
                                    <?php echo $site_settings['enable_user_registration'] ? 'checked' : ''; ?>
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="enable_user_registration" class="font-medium text-gray-700">Enable User Registration</label>
                                <p class="text-gray-500">Allow new users to register on the site</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="enable_reviews" name="enable_reviews" type="checkbox"
                                    <?php echo $site_settings['enable_reviews'] ? 'checked' : ''; ?>
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="enable_reviews" class="font-medium text-gray-700">Enable Product Reviews</label>
                                <p class="text-gray-500">Allow customers to leave reviews on artworks</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="maintenance_mode" name="maintenance_mode" type="checkbox"
                                    <?php echo $site_settings['maintenance_mode'] ? 'checked' : ''; ?>
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="maintenance_mode" class="font-medium text-gray-700">Maintenance Mode</label>
                                <p class="text-gray-500">Put the site into maintenance mode (only admins can access)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
