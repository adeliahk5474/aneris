<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-white shadow-md p-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-rose-600">Aneris Client</h1>
        <div>
            <span class="text-gray-700 mr-4">{{ $user->name }}</span>
            <a href="{{ route('logout') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2 rounded-md transition-all">Logout</a>
        </div>
    </nav>

    <!-- Content -->
    <main class="flex-1 flex justify-center items-start mt-16">
        <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl p-10">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Selamat Datang, {{ $user->name }}!</h2>
            <p class="text-gray-600 mb-10">Ini adalah dashboard kamu sebagai client. Di sini nanti akan tampil data pesanan, riwayat transaksi, dan lainnya.</p>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <div class="bg-rose-100 p-6 rounded-xl text-center shadow-sm">
                    <h3 class="text-2xl font-bold text-rose-700">{{ $client->total_orders ?? 0 }}</h3>
                    <p class="text-gray-700 mt-2">Total Pesanan</p>
                </div>

                <div class="bg-pink-100 p-6 rounded-xl text-center shadow-sm">
                    <h3 class="text-2xl font-bold text-pink-700">
                        Rp{{ number_format($client->total_spent ?? 0, 0, ',', '.') }}
                    </h3>
                    <p class="text-gray-700 mt-2">Total Pengeluaran</p>
                </div>

                <div class="bg-purple-100 p-6 rounded-xl text-center shadow-sm">
                    <h3 class="text-2xl font-bold text-purple-700">⭐</h3>
                    <p class="text-gray-700 mt-2">Tingkat Kepuasan</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center text-gray-500 py-4 text-sm mt-10">
        © {{ date('Y') }} Aneris Platform — Client Dashboard
    </footer>
</body>
</html>
