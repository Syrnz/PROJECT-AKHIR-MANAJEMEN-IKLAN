<?php
include('../../middleware/check_login.php');
include_once('../../../database/koneksi_db.php');
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta
        name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>TAMBAH DATA IKLAN</title>
    <link rel="icon" href="../favicon.ico">
    <link href="../src/css/style.css" rel="stylesheet">
</head>

<body
    x-data="{ page: 'ecommerce', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
    x-init="
        darkMode = JSON.parse(localStorage.getItem('darkMode'));
        $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark bg-gray-900': darkMode === true}">

    <!-- ===== Preloader Start ===== -->
    <div
        x-show="loaded"
        x-init="window.addEventListener('DOMContentLoaded', () => {setTimeout(() => loaded = false, 500)})"
        class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
        <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent"></div>
    </div>

    <!-- ===== Page Wrapper Start ===== -->
    <div class="flex h-screen overflow-hidden">
        <!-- ===== Sidebar ===== -->
        <?php include('../partials/sidebar.php') ?>

        <!-- ===== Content Area Start ===== -->
        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
            <!-- Small Device Overlay -->
            <div
                @click="sidebarToggle = false"
                :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
                class="fixed w-full h-screen z-9 bg-gray-900/50"></div>

            <!-- ===== Header ===== -->
            <?php include('../partials/header.php') ?>

            <!-- ===== Main Content Start ===== -->
            <main>
                <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                    <div class="grid grid-cols-12 gap-4 md:gap-6">
                        <div class="col-span-12 space-y-6">
                            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                                <div class="px-5 py-4 sm:px-6 sm:py-5">
                                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                                        Tambah Data Iklan
                                    </h3>
                                </div>

                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                                <?php
                                $error = "";

                                // Ambil daftar pelanggan untuk dropdown id_pelanggan
                                $pelangganList = [];
                                $stmtPelanggan = $conn->query("SELECT id_pelanggan, nama_pelanggan FROM pelanggan ORDER BY nama_pelanggan ASC");
                                $pelangganList = $stmtPelanggan->fetchAll(PDO::FETCH_ASSOC);

                                if (isset($_POST['tambahData'])) {
                                    // Sanitasi input
                                    $id_pelanggan   = htmlspecialchars(trim($_POST['id_pelanggan']));
                                    $judul_iklan    = htmlspecialchars(trim($_POST['judul_iklan']));
                                    $jenis_iklan    = htmlspecialchars(trim($_POST['jenis_iklan']));
                                    $deskripsi      = htmlspecialchars(trim($_POST['deskripsi']));
                                    $tanggal_mulai  = htmlspecialchars(trim($_POST['tanggal_mulai']));
                                    $tanggal_selesai = htmlspecialchars(trim($_POST['tanggal_selesai']));
                                    $harga          = htmlspecialchars(trim($_POST['harga']));
                                    $status_iklan   = htmlspecialchars(trim($_POST['status_iklan']));

                                    // Upload file iklan
                                    $file_iklan = null;
                                    if (isset($_FILES['file_iklan']) && $_FILES['file_iklan']['error'] === UPLOAD_ERR_OK) {
                                        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'application/pdf'];
                                        $fileType = mime_content_type($_FILES['file_iklan']['tmp_name']);
                                        $maxSize = 10 * 1024 * 1024; // 10 MB

                                        if (!in_array($fileType, $allowedTypes)) {
                                            $error = "Tipe file tidak diizinkan. Gunakan JPG, PNG, GIF, MP4, atau PDF.";
                                        } elseif ($_FILES['file_iklan']['size'] > $maxSize) {
                                            $error = "Ukuran file tidak boleh melebihi 10 MB.";
                                        } else {
                                            $uploadDir = '../uploads/iklan/';
                                            if (!is_dir($uploadDir)) {
                                                mkdir($uploadDir, 0755, true);
                                            }
                                            $fileName = time() . '_' . basename($_FILES['file_iklan']['name']);
                                            $uploadPath = $uploadDir . $fileName;
                                            if (move_uploaded_file($_FILES['file_iklan']['tmp_name'], $uploadPath)) {
                                                $file_iklan = $fileName;
                                            } else {
                                                $error = "Gagal mengupload file iklan.";
                                            }
                                        }
                                    }

                                    // Validasi field wajib
                                    if ($error === "") {
                                        if (
                                            empty($id_pelanggan) || empty($judul_iklan) || empty($jenis_iklan) ||
                                            empty($tanggal_mulai) || empty($tanggal_selesai) || empty($harga) || empty($status_iklan)
                                        ) {
                                            $error = "Semua field wajib diisi!";
                                        } elseif (!in_array($jenis_iklan, ['banner', 'billboard', 'videotron'])) {
                                            $error = "Jenis iklan tidak valid!";
                                        } elseif (!in_array($status_iklan, ['belum_tayang', 'aktif', 'selesai'])) {
                                            $error = "Status iklan tidak valid!";
                                        } elseif (strlen($judul_iklan) > 150) {
                                            $error = "Judul iklan tidak boleh melebihi 150 karakter.";
                                        } elseif (!is_numeric($harga) || $harga < 0) {
                                            $error = "Harga harus berupa angka positif.";
                                        } elseif (strtotime($tanggal_selesai) < strtotime($tanggal_mulai)) {
                                            $error = "Tanggal selesai tidak boleh sebelum tanggal mulai.";
                                        } else {
                                            // Hitung durasi hari otomatis
                                            $tgl_mulai_obj   = new DateTime($tanggal_mulai);
                                            $tgl_selesai_obj = new DateTime($tanggal_selesai);
                                            $durasi_hari     = $tgl_mulai_obj->diff($tgl_selesai_obj)->days;

                                            $sql = "INSERT INTO iklan 
                                                        (id_pelanggan, judul_iklan, jenis_iklan, deskripsi, file_iklan, tanggal_mulai, tanggal_selesai, durasi_hari, harga, status_iklan, created_at)
                                                    VALUES 
                                                        (:id_pelanggan, :judul_iklan, :jenis_iklan, :deskripsi, :file_iklan, :tanggal_mulai, :tanggal_selesai, :durasi_hari, :harga, :status_iklan, CURRENT_TIMESTAMP)";

                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':id_pelanggan',    $id_pelanggan);
                                            $stmt->bindParam(':judul_iklan',     $judul_iklan);
                                            $stmt->bindParam(':jenis_iklan',     $jenis_iklan);
                                            $stmt->bindParam(':deskripsi',       $deskripsi);
                                            $stmt->bindParam(':file_iklan',      $file_iklan);
                                            $stmt->bindParam(':tanggal_mulai',   $tanggal_mulai);
                                            $stmt->bindParam(':tanggal_selesai', $tanggal_selesai);
                                            $stmt->bindParam(':durasi_hari',     $durasi_hari, PDO::PARAM_INT);
                                            $stmt->bindParam(':harga',           $harga);
                                            $stmt->bindParam(':status_iklan',    $status_iklan);

                                            if ($stmt->execute()) {
                                                echo "<script>
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Data berhasil ditambahkan!',
                                                        showConfirmButton: false,
                                                        timer: 1500
                                                    }).then(() => {
                                                        window.location.href = 'data_iklan.php';
                                                    });
                                                </script>";
                                            } else {
                                                echo "<script>
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Data gagal ditambahkan!',
                                                        showConfirmButton: false,
                                                        timer: 1500
                                                    });
                                                </script>";
                                            }
                                        }
                                    }
                                }
                                ?>

                                <!-- Error Alert -->
                                <?php if ($error != "") : ?>
                                    <div class="rounded-xl border border-error-500 bg-error-50 p-4 dark:border-error-500/30 dark:bg-error-500/15 mb-4">
                                        <div class="flex items-start gap-3">
                                            <div class="-mt-0.5 text-error-500">
                                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M20.3499 12.0004C20.3499 16.612 16.6115 20.3504 11.9999 20.3504C7.38832 20.3504 3.6499 16.612 3.6499 12.0004C3.6499 7.38881 7.38833 3.65039 11.9999 3.65039C16.6115 3.65039 20.3499 7.38881 20.3499 12.0004ZM11.9999 22.1504C17.6056 22.1504 22.1499 17.6061 22.1499 12.0004C22.1499 6.3947 17.6056 1.85039 11.9999 1.85039C6.39421 1.85039 1.8499 6.3947 1.8499 12.0004C1.8499 17.6061 6.39421 22.1504 11.9999 22.1504ZM13.0008 16.4753C13.0008 15.923 12.5531 15.4753 12.0008 15.4753L11.9998 15.4753C11.4475 15.4753 10.9998 15.923 10.9998 16.4753C10.9998 17.0276 11.4475 17.4753 11.9998 17.4753L12.0008 17.4753C12.5531 17.4753 13.0008 17.0276 13.0008 16.4753ZM11.9998 6.62898C12.414 6.62898 12.7498 6.96476 12.7498 7.37898L12.7498 13.0555C12.7498 13.4697 12.414 13.8055 11.9998 13.8055C11.5856 13.8055 11.2498 13.4697 11.2498 13.0555L11.2498 7.37898C11.2498 6.96476 11.5856 6.62898 11.9998 6.62898Z" fill="#F04438" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">Error</h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400"><?= $error ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Form -->
                                <form action="tambah_data_iklan.php" method="POST" enctype="multipart/form-data">
                                    <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">

                                        <!-- Pelanggan -->
                                        <div>
                                            <label for="id_pelanggan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Pelanggan 
                                            </label>
                                            <select name="id_pelanggan" id="id_pelanggan"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                                <option value="">-- Pilih Pelanggan --</option>
                                                <?php foreach ($pelangganList as $p) : ?>
                                                    <option value="<?= $p['id_pelanggan'] ?>"
                                                        <?= (isset($_POST['id_pelanggan']) && $_POST['id_pelanggan'] == $p['id_pelanggan']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($p['nama_pelanggan']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- Judul Iklan -->
                                        <div>
                                            <label for="judul_iklan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Judul Iklan
                                            </label>
                                            <input
                                                type="text" name="judul_iklan" id="judul_iklan" maxlength="150"
                                                value="<?= isset($_POST['judul_iklan']) ? htmlspecialchars($_POST['judul_iklan']) : '' ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                                placeholder="Masukkan Judul Iklan" />
                                        </div>

                                        <!-- Jenis Iklan -->
                                        <div>
                                            <label for="jenis_iklan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Jenis 
                                            </label>
                                            <select name="jenis_iklan" id="jenis_iklan"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                                <option value="">-- Pilih Jenis Iklan --</option>
                                                <option value="banner" <?= (isset($_POST['jenis_iklan']) && $_POST['jenis_iklan'] === 'banner')     ? 'selected' : '' ?>>Banner</option>
                                                <option value="billboard" <?= (isset($_POST['jenis_iklan']) && $_POST['jenis_iklan'] === 'billboard')  ? 'selected' : '' ?>>Billboard</option>
                                                <option value="videotron" <?= (isset($_POST['jenis_iklan']) && $_POST['jenis_iklan'] === 'videotron')  ? 'selected' : '' ?>>Videotron</option>
                                            </select>
                                        </div>

                                        <!-- Deskripsi -->
                                        <div>
                                            <label for="deskripsi" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Deskripsi
                                            </label>
                                            <textarea name="deskripsi" id="deskripsi" rows="4"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                                placeholder="Masukkan Deskripsi Iklan (opsional)"><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
                                        </div>

                                        <!-- File Iklan -->
                                        <div>
                                            <label for="file_iklan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                File Iklan <span class="text-xs text-gray-400">(JPG, PNG, GIF, MP4, PDF – maks. 10 MB)</span>
                                            </label>
                                            <input
                                                type="file" name="file_iklan" id="file_iklan"
                                                accept=".jpg,.jpeg,.png,.gif,.mp4,.pdf"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>

                                        <!-- Tanggal Mulai -->
                                        <div>
                                            <label for="tanggal_mulai" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Tanggal Mulai 
                                            </label>
                                            <input
                                                type="date" name="tanggal_mulai" id="tanggal_mulai"
                                                value="<?= isset($_POST['tanggal_mulai']) ? htmlspecialchars($_POST['tanggal_mulai']) : '' ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>

                                        <!-- Tanggal Selesai -->
                                        <div>
                                            <label for="tanggal_selesai" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Tanggal Selesai 
                                            </label>
                                            <input
                                                type="date" name="tanggal_selesai" id="tanggal_selesai"
                                                value="<?= isset($_POST['tanggal_selesai']) ? htmlspecialchars($_POST['tanggal_selesai']) : '' ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>

                                        <!-- Durasi Hari (auto-hitung, readonly) -->
                                        <div>
                                            <label for="durasi_hari" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Durasi Hari <span class="text-xs text-gray-400">(otomatis dihitung)</span>
                                            </label>
                                            <input
                                                type="number" name="durasi_hari" id="durasi_hari" readonly
                                                placeholder="Otomatis terisi setelah memilih tanggal"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed" />
                                        </div>

                                        <!-- Harga -->
                                        <div>
                                            <label for="harga" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Harga 
                                            </label>
                                            <input
                                                type="number" name="harga" id="harga" min="0" step="0.01"
                                                value="<?= isset($_POST['harga']) ? htmlspecialchars($_POST['harga']) : '' ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                                placeholder="Masukkan Harga" />
                                        </div>

                                        <!-- Status Iklan -->
                                        <div>
                                            <label for="status_iklan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Status Iklan 
                                            </label>
                                            <select name="status_iklan" id="status_iklan"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                                <option value="belum_tayang" <?= (!isset($_POST['status_iklan']) || $_POST['status_iklan'] === 'belum_tayang') ? 'selected' : '' ?>>Belum Tayang</option>
                                                <option value="aktif" <?= (isset($_POST['status_iklan']) && $_POST['status_iklan'] === 'aktif')        ? 'selected' : '' ?>>Aktif</option>
                                                <option value="selesai" <?= (isset($_POST['status_iklan']) && $_POST['status_iklan'] === 'selesai')      ? 'selected' : '' ?>>Selesai</option>
                                            </select>
                                        </div>

                                        <!-- Tombol Submit -->
                                        <div class="flex gap-3">
                                            <button type="submit" name="tambahData"
                                                class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition-colors">
                                                Submit
                                            </button>
                                        </div>

                                    </div>
                                </form>

                                <!-- Script auto-hitung durasi hari -->
                                <script>
                                    const tglMulai = document.getElementById('tanggal_mulai');
                                    const tglSelesai = document.getElementById('tanggal_selesai');
                                    const durasiInput = document.getElementById('durasi_hari');

                                    function hitungDurasi() {
                                        const mulai = new Date(tglMulai.value);
                                        const selesai = new Date(tglSelesai.value);
                                        if (tglMulai.value && tglSelesai.value && selesai >= mulai) {
                                            const selisih = Math.round((selesai - mulai) / (1000 * 60 * 60 * 24));
                                            durasiInput.value = selisih;
                                        } else {
                                            durasiInput.value = '';
                                        }
                                    }

                                    tglMulai.addEventListener('change', hitungDurasi);
                                    tglSelesai.addEventListener('change', hitungDurasi);
                                </script>

                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script defer src="../src/js/bundle.js"></script>
</body>

</html>