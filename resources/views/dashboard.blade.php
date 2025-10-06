@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r p-6 flex flex-col justify-between">
        <div>
            <h2 class="text-lg font-semibold mb-6">Artcommissions</h2>
            <nav class="space-y-3">
                <a href="#" class="block text-gray-900 font-medium bg-gray-100 p-2 rounded-lg">Dashboard</a>
                <a href="#" class="block text-gray-600 hover:text-gray-900 p-2 rounded-lg">Settings</a>
            </nav>
        </div>
        <footer class="text-xs text-gray-500">
            © 2024 Artcommission. All rights reserved.
        </footer>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
        <!-- Navbar -->
        <header class="flex items-center justify-between bg-white border-b px-8 py-4">
            <nav class="flex space-x-6 font-medium text-gray-700">
                <a href="#" class="hover:text-black">Artwork</a>
                <a href="#" class="hover:text-black">Commission</a>
            </nav>
            <div class="flex items-center space-x-4">
                <button class="text-gray-500 hover:text-black">
                    <i class="fas fa-bell"></i>
                </button>
                <div class="w-8 h-8 bg-gray-300 rounded-full"></div>
            </div>
        </header>

        <!-- Dashboard Body -->
        <main class="flex-1 overflow-y-auto p-8">
            <h1 class="text-2xl font-semibold mb-6">Artist Dashboard</h1>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <p class="text-gray-500 text-sm mb-1">Total Commissions</p>
                    <p class="text-3xl font-semibold">24</p>
                    <p class="text-xs text-gray-400 mt-1">+3 this month</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <p class="text-gray-500 text-sm mb-1">Monthly Earnings</p>
                    <p class="text-3xl font-semibold">$2,840</p>
                    <p class="text-xs text-gray-400 mt-1">+12% from last month</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <p class="text-gray-500 text-sm mb-1">Average Rating</p>
                    <p class="text-3xl font-semibold">4.8</p>
                    <p class="text-xs text-gray-400 mt-1">Based on 18 reviews</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <div class="bg-white border rounded-xl shadow-sm p-8 text-center">
                    <div class="mx-auto mb-3 w-12 h-12 bg-gray-200 rounded"></div>
                    <h3 class="text-lg font-semibold mb-1">Upload New Work</h3>
                    <p class="text-sm text-gray-500 mb-4">Drag & drop files here or click to browse</p>
                    <button class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">Choose Files</button>
                </div>
                <div class="bg-white border rounded-xl shadow-sm p-8 text-center">
                    <div class="mx-auto mb-3 w-12 h-12 bg-gray-200 rounded"></div>
                    <h3 class="text-lg font-semibold mb-1">Create New Commission</h3>
                    <p class="text-sm text-gray-500 mb-4">Set up a new commission listing</p>
                    <button class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">Create Commission</button>
                </div>
            </div>

            <!-- Active Commissions -->
            <section class="mb-10">
                <h2 class="text-xl font-semibold mb-4">Active Commissions</h2>
                <div class="space-y-3">
                    <div class="bg-white border rounded-xl shadow-sm p-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium">Custom Portrait Commission</p>
                            <p class="text-sm text-gray-500">Client: John Smith<br>Deadline: March 15, 2024</p>
                        </div>
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full">In Progress</span>
                    </div>
                    <div class="bg-white border rounded-xl shadow-sm p-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium">Logo Design Project</p>
                            <p class="text-sm text-gray-500">Client: Creative Agency<br>Deadline: March 20, 2024</p>
                        </div>
                        <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full">Waiting Payment</span>
                    </div>
                    <div class="bg-white border rounded-xl shadow-sm p-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium">Character Illustration</p>
                            <p class="text-sm text-gray-500">Client: Game Studio<br>Deadline: March 25, 2024</p>
                        </div>
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full">In Progress</span>
                    </div>
                </div>
            </section>

            <!-- Portfolio -->
            <section class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Portfolio</h2>
                    <a href="#" class="text-blue-600 text-sm hover:underline">View All</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
                        <div class="bg-gray-200 h-40"></div>
                        <div class="p-4">
                            <p class="text-sm text-gray-500 mb-1">Digital Art</p>
                            <h3 class="font-medium">Cyberpunk Cityscape</h3>
                            <p class="text-xs text-gray-400 mt-1">1.2k views • 89 likes</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
                        <div class="bg-gray-200 h-40"></div>
                        <div class="p-4">
                            <p class="text-sm text-gray-500 mb-1">Photography</p>
                            <h3 class="font-medium">Urban Portrait Series</h3>
                            <p class="text-xs text-gray-400 mt-1">856 views • 67 likes</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
                        <div class="bg-gray-200 h-40"></div>
                        <div class="p-4">
                            <p class="text-sm text-gray-500 mb-1">3D Model</p>
                            <h3 class="font-medium">Sci-Fi Weapon Design</h3>
                            <p class="text-xs text-gray-400 mt-1">2.1k views • 156 likes</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-500 border-t pt-6">
                <div>
                    <h4 class="font-semibold mb-2 text-gray-700">About</h4>
                    <ul class="space-y-1">
                        <li><a href="#" class="hover:underline">Our Story</a></li>
                        <li><a href="#" class="hover:underline">Careers</a></li>
                        <li><a href="#" class="hover:underline">Press</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-2 text-gray-700">Support</h4>
                    <ul class="space-y-1">
                        <li><a href="#" class="hover:underline">Help Center</a></li>
                        <li><a href="#" class="hover:underline">Contact Us</a></li>
                        <li><a href="#" class="hover:underline">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-2 text-gray-700">Community</h4>
                    <ul class="space-y-1">
                        <li><a href="#" class="hover:underline">Forums</a></li>
                        <li><a href="#" class="hover:underline">Blog</a></li>
                        <li><a href="#" class="hover:underline">Events</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-2 text-gray-700">Follow Us</h4>
                    <div class="flex space-x-3">
                        <div class="w-5 h-5 bg-gray-300 rounded"></div>
                        <div class="w-5 h-5 bg-gray-300 rounded"></div>
                        <div class="w-5 h-5 bg-gray-300 rounded"></div>
                        <div class="w-5 h-5 bg-gray-300 rounded"></div>
                    </div>
                </div>
            </footer>
        </main>
    </div>
</div>
@endsection
