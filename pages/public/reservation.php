<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/header.php';
?>

<div class="bg-gradient-to-b from-gray-50 to-white min-h-screen">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12 relative">
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="h-16 w-16 transform rotate-45 border-4 border-indigo-100 opacity-20"></div>
            </div>
            <h1 class="text-5xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4 relative">Make a Reservation</h1>
            <p class="text-xl text-gray-600">Experience Art in Person - Schedule Your Visit</p>
        </div>

        <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100 backdrop-blur-sm backdrop-filter">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-2"></div>
            <form action="../../actions/process_reservation.php" method="POST" class="p-8 space-y-8">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div class="relative group">
                            <label for="name" class="block text-sm font-semibold text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">Full Name</label>
                            <input type="text" name="name" id="name" required
                                   class="mt-1 block w-full rounded-lg border-2 border-gray-200 bg-gray-50 px-4 py-3 
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:bg-white
                                   hover:border-indigo-300 transition-all duration-200">
                            <div class="absolute bottom-0 left-0 h-0.5 w-0 bg-indigo-500 transition-all duration-200 group-focus-within:w-full"></div>
                        </div>

                        <div class="relative group">
                            <label for="email" class="block text-sm font-semibold text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">Email Address</label>
                            <input type="email" name="email" id="email" required
                                   class="mt-1 block w-full rounded-lg border-2 border-gray-200 bg-gray-50 px-4 py-3 
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:bg-white
                                   hover:border-indigo-300 transition-all duration-200">
                            <div class="absolute bottom-0 left-0 h-0.5 w-0 bg-indigo-500 transition-all duration-200 group-focus-within:w-full"></div>
                        </div>

                        <div class="relative group">
                            <label for="guests" class="block text-sm font-semibold text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">Number of Guests</label>
                            <input type="number" name="guests" id="guests" min="1" max="10" required
                                   class="mt-1 block w-full rounded-lg border-2 border-gray-200 bg-gray-50 px-4 py-3 
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:bg-white
                                   hover:border-indigo-300 transition-all duration-200">
                            <p class="mt-1 text-sm text-gray-500">Maximum 10 guests per reservation</p>
                            <div class="absolute bottom-0 left-0 h-0.5 w-0 bg-indigo-500 transition-all duration-200 group-focus-within:w-full"></div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="relative group">
                            <label for="date" class="block text-sm font-semibold text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">Preferred Date</label>
                            <input type="date" name="date" id="date" required min="<?php echo date('Y-m-d'); ?>"
                                   class="mt-1 block w-full rounded-lg border-2 border-gray-200 bg-gray-50 px-4 py-3
                                   focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:bg-white
                                   hover:border-indigo-300 transition-all duration-200">
                        </div>

                        <div class="relative group">
                            <label for="time" class="block text-sm font-semibold text-gray-700 after:content-['*'] after:ml-0.5 after:text-red-500">Preferred Time</label>
                            <select name="time" id="time" required
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-200 bg-gray-50 px-4 py-3
                                    focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:bg-white
                                    hover:border-indigo-300 transition-all duration-200">
                                <option value="">Select a time slot</option>
                                <?php
                                $times = [
                                    '10:00' => '10:00 AM - Morning Visit',
                                    '11:00' => '11:00 AM - Late Morning Visit',
                                    '12:00' => '12:00 PM - Lunch Hour Visit',
                                    '13:00' => '1:00 PM - Early Afternoon Visit',
                                    '14:00' => '2:00 PM - Afternoon Visit',
                                    '15:00' => '3:00 PM - Late Afternoon Visit',
                                    '16:00' => '4:00 PM - Evening Visit'
                                ];
                                foreach ($times as $value => $label) {
                                    echo "<option value=\"{$value}\">{$label}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="relative group">
                            <label for="notes" class="block text-sm font-semibold text-gray-700">Special Notes</label>
                            <textarea name="notes" id="notes" rows="4"
                                      class="mt-1 block w-full rounded-lg border-2 border-gray-200 bg-gray-50 px-4 py-3
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:bg-white
                                      hover:border-indigo-300 transition-all duration-200"
                                      placeholder="Any special requirements or requests..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" name="submit_reservation"
                            class="group w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-lg
                            text-base font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 
                            hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-200
                            transform transition-all duration-300 hover:scale-[0.99] hover:shadow-lg">
                        <span class="mr-2">Confirm Reservation</span>
                        <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </button>
                    <p class="mt-3 text-center text-sm text-gray-500">
                        You will receive a confirmation email once your reservation is processed
                    </p>
                </div>
            </form>
        </div>
    </main>
</div>

<?php require_once '../../includes/footer.php'; ?>
