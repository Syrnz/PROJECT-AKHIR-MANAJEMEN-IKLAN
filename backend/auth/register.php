<?php
session_start();

require '../../database/koneksi_db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama     = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    // Validasi kosong
    if (
        empty($nama) ||
        empty($email) ||
        empty($password) ||
        empty($confirm)
    ) {

        $error = "Semua field wajib diisi!";
    }

    // Validasi email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Format email tidak valid!";
    }

    // Password minimal
    elseif (strlen($password) < 6) {

        $error = "Password minimal 6 karakter!";
    }

    // Konfirmasi password
    elseif ($password !== $confirm) {

        $error = "Konfirmasi password tidak cocok!";
    } else {

        // Cek email sudah ada atau belum
        $check = $conn->prepare("SELECT * FROM admins WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {

            $error = "Email sudah digunakan!";
        } else {

            // Hash password
            $hashPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert data
            $insert = $conn->prepare(
                "INSERT INTO admins(username, email, password) 
                 VALUES(?, ?, ?)"
            );

            $result = $insert->execute([
                $nama,
                $email,
                $hashPassword
            ]);

            if ($result) {
                echo    "<script>
                            alert('Registrasi Berhasil! Silakan Login.');
                            window.location.href='login.php';
                        </script>";
                exit;
                // $success = "Registrasi berhasil!";
            } else {

                $error = "Registrasi gagal!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-5">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 md:p-12">
        <div class="text-center mb-8">
            <h2 class="text-4xl font-bold text-slate-800">
                Create Account
            </h2>
            <p class="text-slate-500 mt-2">
                Silakan isi data untuk registrasi
            </p>
        </div>
        <?php if ($error != "") : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-5">
                <?= $error; ?>
            </div>
        <?php endif; ?>
        <form method="POST" class="space-y-5">
            <div>
                <label class="block mb-2 text-sm font-semibold text-slate-700">
                    Nama Lengkap
                </label>
                <input
                    type="text"
                    name="username"
                    placeholder="Masukkan nama lengkap"
                    value="<?= isset($nama) ? htmlspecialchars($nama) : '' ?>"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 outline-none transition">
            </div>
            <div>
                <label class="block mb-2 text-sm font-semibold text-slate-700">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    placeholder="Masukkan email"
                    value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 outline-none transition">
            </div>
            <div>
                <label class="block mb-2 text-sm font-semibold text-slate-700">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 outline-none transition">
            </div>
            <div>
                <label class="block mb-2 text-sm font-semibold text-slate-700">
                    Konfirmasi Password
                </label>
                <input
                    type="password"
                    name="confirm"
                    placeholder="Konfirmasi password"
                    class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 outline-none transition">
            </div>
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold text-lg shadow-lg transition duration-300">
                Register
            </button>
        </form>
        <p class="text-center text-slate-500 mt-8">
            Sudah punya akun?
            <a href="login.php" class="text-blue-600 font-semibold hover:underline">
                Login
            </a>
        </p>
    </div>
</body>
</html>