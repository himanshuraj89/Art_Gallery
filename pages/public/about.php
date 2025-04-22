<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/header.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Hero Section -->
    <div class="text-center mb-16">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">About Our Project</h1>
        <p class="max-w-2xl mx-auto text-lg text-gray-500">An innovative digital platform connecting artists and art enthusiasts, revolutionizing the way art is discovered, shared, and appreciated.</p>
    </div>

    <!-- Mission Section -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-10 mb-16 shadow-xl">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-white mb-6">Our Vision</h2>
            <p class="text-lg text-white leading-relaxed">
                To create a vibrant digital ecosystem where artists can showcase their creativity and art lovers can explore unique pieces that resonate with them. We aim to democratize art appreciation while empowering creators.
            </p>
        </div>
    </div>

    <!-- Project Highlights -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
        <div class="bg-white p-8 rounded-lg shadow-lg text-center transform transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
            <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 mb-3">Innovation</div>
            <div class="text-gray-700">Cutting-edge technology meets artistic expression</div>
        </div>
        <div class="bg-white p-8 rounded-lg shadow-lg text-center transform transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
            <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 mb-3">Community</div>
            <div class="text-gray-700">Building connections between creators and collectors</div>
        </div>
        <div class="bg-white p-8 rounded-lg shadow-lg text-center transform transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
            <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 mb-3">Accessibility</div>
            <div class="text-gray-700">Making art discovery seamless for everyone</div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="mb-16">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Our Talented Team</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-white to-indigo-50 p-6 rounded-xl shadow-lg text-center transform transition-all duration-300 hover:scale-105 overflow-hidden group">
                <img src="/newf/teams/ab.png" alt="Ankit Vishwakarma" class="w-32 h-32 mx-auto mb-6 rounded-full object-cover border-4 border-indigo-300 group-hover:border-purple-500 transition-all duration-300">
                <h3 class="text-xl font-semibold text-gray-900">Ankit Vishwakarma</h3>
            </div>
            
            <div class="bg-gradient-to-br from-white to-indigo-50 p-6 rounded-xl shadow-lg text-center transform transition-all duration-300 hover:scale-105 overflow-hidden group">
                <img src="/newf/teams/kr.png" alt="Kartik Gour" class="w-32 h-32 mx-auto mb-6 rounded-full object-cover border-4 border-indigo-300 group-hover:border-purple-500 transition-all duration-300">
                <h3 class="text-xl font-semibold text-gray-900">Kartik Gour</h3>
            </div>
            
            <div class="bg-gradient-to-br from-white to-indigo-50 p-6 rounded-xl shadow-lg text-center transform transition-all duration-300 hover:scale-105 overflow-hidden group">
                <img src="/newf/teams/vs.png" alt="Vishal Kumar" class="w-32 h-32 mx-auto mb-6 rounded-full object-cover border-4 border-indigo-300 group-hover:border-purple-500 transition-all duration-300">
                <h3 class="text-xl font-semibold text-gray-900">Vishal Kumar</h3>
            </div>
            
            <div class="bg-gradient-to-br from-white to-indigo-50 p-6 rounded-xl shadow-lg text-center transform transition-all duration-300 hover:scale-105 overflow-hidden group">
                <img src="/newf/teams/hm.png" alt="Himanshu Raj" class="w-32 h-32 mx-auto mb-6 rounded-full object-cover border-4 border-indigo-300 group-hover:border-purple-500 transition-all duration-300">
                <h3 class="text-xl font-semibold text-gray-900">Himanshu Raj</h3>
            </div>
        </div>
    </div>
    
    <!-- Project Journey -->
    <div class="bg-gray-50 rounded-2xl p-8 mb-16">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Our Journey</h2>
        <div class="max-w-3xl mx-auto">
            <div class="relative">
                <!-- Timeline line -->
                <div class="absolute left-0 md:left-1/2 h-full w-0.5 bg-indigo-300 transform md:-translate-x-1/2"></div>
                
                <!-- Timeline items -->
                <div class="space-y-12">
                    <div class="relative flex flex-col md:flex-row items-center">
                        <div class="flex-1 md:text-right md:pr-8 mb-4 md:mb-0">
                            <h3 class="text-xl font-bold text-indigo-600">Project Inception</h3>
                            <p class="text-gray-600">The initial idea that started it all</p>
                        </div>
                        <div class="z-10 w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center">
                            <span class="text-white">1</span>
                        </div>
                        <div class="flex-1 md:pl-8">
                            <p class="text-gray-700">We identified a gap in the digital art marketplace and began planning our solution.</p>
                        </div>
                    </div>
                    
                    <div class="relative flex flex-col md:flex-row items-center">
                        <div class="flex-1 md:text-right md:pr-8 mb-4 md:mb-0">
                            <h3 class="text-xl font-bold text-indigo-600">Research & Design</h3>
                            <p class="text-gray-600">Building our foundation</p>
                        </div>
                        <div class="z-10 w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center">
                            <span class="text-white">2</span>
                        </div>
                        <div class="flex-1 md:pl-8">
                            <p class="text-gray-700">Extensive user research and iterative design processes to create the optimal experience.</p>
                        </div>
                    </div>
                    
                    <div class="relative flex flex-col md:flex-row items-center">
                        <div class="flex-1 md:text-right md:pr-8 mb-4 md:mb-0">
                            <h3 class="text-xl font-bold text-indigo-600">Development</h3>
                            <p class="text-gray-600">Bringing our vision to life</p>
                        </div>
                        <div class="z-10 w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center">
                            <span class="text-white">3</span>
                        </div>
                        <div class="flex-1 md:pl-8">
                            <p class="text-gray-700">Collaborative coding and implementation of features that empower artists and collectors.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>
