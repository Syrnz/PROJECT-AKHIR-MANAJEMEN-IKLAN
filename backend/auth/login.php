<?php
session_start();
include_once '../../database/koneksi_db.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {

        $error = "Email dan Password wajib diisi!";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Format email tidak valid!";

    } else {

        try {
            $query = "SELECT * FROM admins WHERE email = :email";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin) {
                if (password_verify($password, $admin['password'])) {
                    $_SESSION['login']   = true;
                    $_SESSION['id']      = $admin['id'];
                    $_SESSION['username'] = $admin['username'];
                    $_SESSION['email']   = $admin['email'];

                    header("Location: ../dashboard/main/dashboard.php");
                    exit;
                } else {
                    $error = "Password salah!";
                }
            } else {
                $error = "Email tidak ditemukan!";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Page</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden grid md:grid-cols-2">
        <div class="hidden md:flex bg-linear-to-br from-blue-600 to-indigo-700 text-white p-12 flex-col justify-center">
            <h1 class="text-5xl font-bold mb-6">
                Welcome Back
            </h1>

            <p class="text-lg text-blue-100 leading-relaxed">
                Masuk ke dashboard admin untuk mengelola data,
                pengguna, dan seluruh aktivitas sistem dengan mudah.
            </p>
            <div class="mt-10">
                <img
                    src="../../assets/gambar-login.png"
                    alt="Login"
                    class="w-72 mx-auto">
            </div>
        </div>
        <div class="p-8 md:p-12">
            <div class="mb-10 text-center">
                <h2 class="text-4xl font-bold text-gray-800">
                    Login
                </h2>
                <p class="text-gray-500 mt-2">
                    Silakan masuk ke akun Anda
                </p>
            </div>
            <?php if ($error != "") : ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-5">
                    <?= $error; ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Masukkan email"
                        value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 outline-none transition">
                </div>

                <!-- Password -->
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 outline-none transition">
                </div>
                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold text-lg shadow-lg transition duration-300">
                    Login
                </button>
            </form>
            <p class="text-center text-gray-500 mt-8">
                Belum punya akun?
                <a href="register.php" class="text-blue-600 font-semibold hover:underline">
                    Daftar
                </a>
            </p>
        </div>
    </div>
</body>

</html>