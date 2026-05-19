<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <!-- Container -->
    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden grid md:grid-cols-2">

        <!-- Left Side -->
        <div class="hidden md:flex bg-gradient-to-br from-blue-600 to-indigo-700 text-white p-12 flex-col justify-center">
            <h1 class="text-5xl font-bold mb-6">
                Welcome Back
            </h1>

            <p class="text-lg text-blue-100 leading-relaxed">
                Masuk ke dashboard admin untuk mengelola data, pengguna,
                dan seluruh aktivitas sistem dengan mudah.
            </p>

            <div class="mt-10">
                <img
                    src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                    alt="Login Illustration"
                    class="w-72 mx-auto">
            </div>
        </div>

        <!-- Right Side -->
        <div class="p-8 md:p-12">

            <div class="mb-10 text-center">
                <h2 class="text-4xl font-bold text-gray-800">
                    Login
                </h2>

                <p class="text-gray-500 mt-2">
                    Silakan masuk ke akun Anda
                </p>
            </div>

            <!-- Form -->
            <form class="space-y-6">

                <!-- Email -->
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">
                        Email
                    </label>

                    <input
                        type="email"
                        placeholder="Masukkan email"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 outline-none transition">
                </div>

                <!-- Password -->
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">
                        Password
                    </label>

                    <input
                        type="password"
                        placeholder="Masukkan password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 outline-none transition">
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-gray-600">
                        <input type="checkbox" class="rounded text-blue-600">
                        Remember me
                    </label>

                    <a href="#" class="text-blue-600 hover:underline">
                        Lupa Password?
                    </a>
                </div>

                <!-- Button -->
                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold text-lg shadow-lg transition duration-300">
                    Login
                </button>
            </form>
            <!-- Register -->
            <p class="text-center text-gray-500 mt-8">
                Belum punya akun?
                <a href="#" class="text-blue-600 font-semibold hover:underline">
                    Daftar
                </a>
            </p>
        </div>
    </div>
</body>
</html>