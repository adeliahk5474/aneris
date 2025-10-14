<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Dashboard</title>

    <!-- Font dan Tailwind -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-purple-50 to-blue-50 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-700">🎨 Artist Dashboard</h1>
        <div class="flex items-center gap-4">
            <span class="text-gray-600">{{ $user->name }}</span>
            <a href="{{ route('logout') }}" class="bg-rose-400 text-white px-4 py-2 rounded-lg hover:bg-rose-500 transition">
                Logout
            </a>
        </div>
    </nav>

    <!-- Main content -->
    <main class="flex-grow p-8">
        <div class="grid md:grid-cols-3 gap-6">

            <!-- Card Profile -->
            <div class="bg-white rounded-2xl shadow-md p-6 border border-pink-100">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Profile</h2>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Rating:</strong> {{ $artist->average_rating ?? 'No rating yet' }}</p>
                <p><strong>Verified:</strong> {{ $artist->is_verified ? '✅ Yes' : '❌ No' }}</p>
            </div>

            <!-- Card Portfolio -->
            <div class="bg-white rounded-2xl shadow-md p-6 border border-purple-100">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Portfolio</h2>
                @if($artist->portfolio_link)
                    <a href="{{ $artist->portfolio_link }}" target="_blank" class="text-indigo-500 underline">
                        View Portfolio
                    </a>
                @else
                    <p class="text-gray-500">No portfolio link added yet.</p>
                @endif
            </div>

            <!-- Card Stats -->
            <div class="bg-white rounded-2xl shadow-md p-6 border border-blue-100">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Statistics</h2>
                <p><strong>Total Reviews:</strong> {{ $artist->total_reviews }}</p>
                <p><strong>Average Rating:</strong> {{ $artist->average_rating ?? '-' }}</p>
            </div>
        </div>

        <!-- Section for commissions -->
        <div class="mt-10 bg-white rounded-2xl shadow-md p-6 border border-pink-100">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Active Commissions</h2>
            <p class="text-gray-500 italic">Coming soon: your commission orders will appear here.</p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white py-4 text-center text-gray-500 text-sm border-t">
        &copy; {{ date('Y') }} Aneris Art Platform. All rights reserved.
    </footer>

</body>
</html>
