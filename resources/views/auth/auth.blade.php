<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .slide-container {
            transition: transform 0.6s ease;
            width: 200%;
        }
        .slide-left { transform: translateX(-50%); }
    </style>
</head>
<body class="bg-pink-50 flex items-center justify-center h-screen">

    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden w-[850px] h-[500px] relative">
        <div id="formWrapper" class="slide-container flex w-full h-full">

            <!-- LOGIN FORM -->
            <div class="w-1/2 flex flex-col justify-center items-center p-10">
                <h2 class="text-2xl font-semibold mb-6 text-pink-600">Welcome Back!</h2>
                <form action="{{ route('login') }}" method="POST" class="w-full max-w-sm">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-600 text-sm mb-1">Email</label>
                        <input type="email" name="email" required
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-600 text-sm mb-1">Password</label>
                        <input type="password" name="password" required
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                    <button type="submit"
                            class="w-full bg-pink-400 hover:bg-pink-500 text-white font-semibold py-2 rounded-lg transition">
                        Login
                    </button>
                </form>
                <p class="text-sm mt-6 text-gray-600">Belum punya akun?
                    <button onclick="toggleSlide()" class="text-pink-500 font-semibold">Daftar Sekarang</button>
                </p>
            </div>

            <!-- REGISTER FORM -->
            <div class="w-1/2 flex flex-col justify-center items-center p-10 bg-pink-100">
                <h2 class="text-2xl font-semibold mb-6 text-pink-600">Create Account</h2>
                <form action="{{ route('register') }}" method="POST" class="w-full max-w-sm">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-600 text-sm mb-1">Name</label>
                        <input type="text" name="name" required
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-600 text-sm mb-1">Email</label>
                        <input type="email" name="email" required
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-600 text-sm mb-1">Password</label>
                        <input type="password" name="password" required
                               class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-300">
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-600 text-sm mb-1">Daftar Sebagai</label>
                        <select name="role" required
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option value="">Pilih Role</option>
                            <option value="client">Client</option>
                            <option value="artist">Artist</option>
                        </select>
                    </div>
                    <button type="submit"
                            class="w-full bg-pink-400 hover:bg-pink-500 text-white font-semibold py-2 rounded-lg transition">
                        Register
                    </button>
                </form>
                <p class="text-sm mt-6 text-gray-600">Sudah punya akun?
                    <button onclick="toggleSlide()" class="text-pink-500 font-semibold">Login Sekarang</button>
                </p>
            </div>

        </div>
    </div>

    <script>
        const wrapper = document.getElementById('formWrapper');
        function toggleSlide() {
            wrapper.classList.toggle('slide-left');
        }
    </script>

</body>
</html>
