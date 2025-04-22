    </div> <!-- Closing the flex-grow div from header -->
    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap justify-between items-center">
                <div>
                    <p>&copy; <?php echo date('Y'); ?> Art Gallery Admin Panel</p>
                </div>
                <div>
                    <p>
                        <i class="fas fa-wrench mr-1"></i>
                        Version 1.0
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Any JavaScript you want to include in all admin pages can go here
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-out effect to alert messages after 3 seconds
            const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
            if (alerts.length > 0) {
                setTimeout(function() {
                    alerts.forEach(function(alert) {
                        alert.style.transition = 'opacity 1s';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            alert.style.display = 'none';
                        }, 1000);
                    });
                }, 3000);
            }
        });
    </script>
</body>
</html>
