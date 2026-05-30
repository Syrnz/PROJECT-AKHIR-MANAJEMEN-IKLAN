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
    <title>UBAH DATA IKLAN</title>
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
                                        Ubah Data Iklan
                                    </h3>
                                </div>

                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                                <?php
                                if (!isset($_GET['id'])) {
                                    die("ID tidak ditemukan!");
                                }

                                $sql = "SELECT * FROM iklan WHERE id_iklan = :id_iklan";
                                $stmt = $conn->prepare($sql);
                                $stmt->bindParam(':id_iklan', $_GET['id']);
                                $stmt->execute();
                                $data = $stmt->fetch(PDO::FETCH_ASSOC);

                                $sqlJoin = "SELECT i.*, p.nama_pelanggan, p.kode_pelanggan, l.nama_lokasi, l.alamat
                                                    FROM iklan AS i
                                                    JOIN pelanggan AS p ON i.id_pelanggan = p.id_pelanggan
                                                    JOIN lokasi_iklan AS l ON i.id_lokasi = l.id_lokasi";
                                $stmtJoin = $conn->prepare($sqlJoin . " WHERE i.id_iklan = :id_iklan");
                                $stmtJoin->bindParam(':id_iklan', $_GET['id']);
                                $stmtJoin->execute();
                                $joinList = $stmtJoin->fetch(PDO::FETCH_ASSOC);
                                // Ambil daftar pelanggan untuk dropdown id_pelanggan
                                // $pelangganList = [];
                                // $stmtPelanggan = $conn->query("SELECT id_pelanggan, nama_pelanggan, kode_pelanggan FROM pelanggan ORDER BY nama_pelanggan ASC");
                                // $pelangganList = $stmtPelanggan->fetchAll(PDO::FETCH_ASSOC);

                                // // Ambil daftar lokasi untuk dropdown id_lokasi yang tersedia
                                // $lokasiList = [];
                                // $stmtLokasi = $conn->prepare("SELECT id_lokasi, nama_lokasi, alamat, harga
                                //                             FROM lokasi_iklan
                                //                             WHERE id_lokasi = :id_lokasi
                                //                             ");

                                // $stmtLokasi->bindParam(':id_lokasi', $data['id_lokasi']);
                                // $stmtLokasi->execute();
                                // $lokasiList = $stmtLokasi->fetchAll(PDO::FETCH_ASSOC);

                                $error = "";
                                if (isset($_POST['ubahData'])) {
                                    $id_pelanggan   = htmlspecialchars(trim($_POST['id_pelanggan']));
                                    $lokasi_iklan    = htmlspecialchars(trim($_POST['id_lokasi']));
                                    $judul_iklan    = htmlspecialchars(trim($_POST['judul_iklan']));
                                    $tanggal_mulai  = htmlspecialchars(trim($_POST['tanggal_mulai']));
                                    $tanggal_selesai = htmlspecialchars(trim($_POST['tanggal_selesai']));
                                    $status_iklan   = htmlspecialchars(trim($_POST['status_iklan']));

                                    // Upload file iklan
                                    $file_iklan = $data['file_iklan'];
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
                                            if (!empty($_FILES['file_iklan']['name'])) {

                                                // hapus file lama
                                                if ($data['file_iklan'] != null) {
                                                    unlink('../uploads/iklan/' . $data['file_iklan']);
                                                }

                                                // upload baru
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
                                            empty($id_pelanggan) || empty($judul_iklan) || empty($lokasi_iklan) ||
                                            empty($tanggal_mulai) || empty($tanggal_selesai) || empty($status_iklan)
                                        ) {
                                            $error = "Semua field wajib diisi!";
                                        } elseif (!in_array($status_iklan, ['belum_tayang', 'aktif', 'selesai'])) {
                                            $error = "Status iklan tidak valid!";
                                        } elseif (strlen($judul_iklan) > 150) {
                                            $error = "Judul iklan tidak boleh melebihi 150 karakter.";
                                        } elseif (strtotime($tanggal_selesai) < strtotime($tanggal_mulai)) {
                                            $error = "Tanggal selesai tidak boleh sebelum tanggal mulai.";
                                        } else {

                                            // Hitung durasi hari otomatis
                                            $tgl_mulai_obj   = new DateTime($tanggal_mulai);
                                            $tgl_selesai_obj = new DateTime($tanggal_selesai);
                                            $durasi_hari     = $tgl_mulai_obj->diff($tgl_selesai_obj)->days;

                                            // Ambil harga lokasi dari database
                                            $stmtHarga = $conn->prepare(" SELECT harga 
                                                                        FROM lokasi_iklan 
                                                                        WHERE id_lokasi = :id_lokasi
                                                                    ");
                                            $stmtHarga->bindParam(':id_lokasi', $lokasi_iklan);
                                            $stmtHarga->execute();
                                            $dataHarga = $stmtHarga->fetch(PDO::FETCH_ASSOC);
                                            if (!$dataHarga) {
                                                $error = "Lokasi iklan tidak ditemukan!";
                                            } else {
                                                $harga_per_hari = $dataHarga['harga'];
                                            }
                                            $total_harga = $durasi_hari * $harga_per_hari;


                                            $sql = "UPDATE iklan SET
                                                        id_pelanggan = :id_pelanggan,
                                                        id_lokasi = :id_lokasi,
                                                        judul_iklan = :judul_iklan,
                                                        file_iklan = :file_iklan,
                                                        tanggal_mulai = :tanggal_mulai,
                                                        tanggal_selesai = :tanggal_selesai,
                                                        durasi_hari = :durasi_hari,
                                                        total_harga = :total_harga,
                                                        status_iklan = :status_iklan,
                                                        updated_at = CURRENT_TIMESTAMP
                                                    WHERE id_iklan = :id_iklan
                                                    ";

                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':id_pelanggan',    $id_pelanggan);
                                            $stmt->bindParam(':id_lokasi',       $lokasi_iklan);
                                            $stmt->bindParam(':judul_iklan',     $judul_iklan);
                                            $stmt->bindParam(':file_iklan',      $file_iklan);
                                            $stmt->bindParam(':tanggal_mulai',   $tanggal_mulai);
                                            $stmt->bindParam(':tanggal_selesai', $tanggal_selesai);
                                            $stmt->bindParam(':durasi_hari',     $durasi_hari, PDO::PARAM_INT);
                                            $stmt->bindParam(':total_harga',     $total_harga);
                                            $stmt->bindParam(':status_iklan',    $status_iklan);
                                            $stmt->bindParam(':id_iklan',        $_GET['id']);

                                            if ($stmt->execute()) {

                                                echo "<script>
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Data berhasil diubah!',
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
                                                        title: 'Data gagal diubah!',
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
                                <form action="edit_data_iklan.php?id=<?= $data['id_iklan'] ?>" name="ubahData" method="POST" enctype="multipart/form-data">
                                    <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">

                                        <!-- Pelanggan -->
                                        <div>
                                            <label for="id_pelanggan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Pelanggan
                                            </label>
                                            <input type="text" name="id_iklan" value="<?= $data['id_iklan'] ?>" hidden>

                                            <input type="hidden" name="id_pelanggan" value="<?= $data['id_pelanggan'] ?>">
                                            <input
                                                type="text"
                                                readonly
                                                value="<?= htmlspecialchars($joinList['nama_pelanggan']) ?> | <?= htmlspecialchars($joinList['kode_pelanggan']) ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed" />
                                        </div>

                                        <!-- Jenis Iklan -->
                                        <div>
                                            <label for="id_lokasi" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Jenis & Lokasi Iklan
                                            </label>
                                            <input type="hidden" name="id_lokasi" value="<?= $data['id_lokasi'] ?>">
                                            <input
                                                type="text"
                                                readonly
                                                value="<?= htmlspecialchars($joinList['nama_lokasi']) ?> | <?= htmlspecialchars($joinList['alamat']) ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed" />
                                        </div>

                                        <!-- Judul Iklan -->
                                        <div>
                                            <label for="judul_iklan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Judul Iklan
                                            </label>
                                            <input
                                                type="text" name="judul_iklan" id="judul_iklan" maxlength="150"
                                                value="<?= $data['judul_iklan'] ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                                placeholder="Masukkan Judul Iklan" />
                                        </div>

                                        <!-- File Iklan -->
                                        <div>
                                            <label for="file_iklan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                File Iklan <span class="text-xs text-gray-400">(JPG, PNG, GIF, MP4, PDF – maks. 10 MB)</span>
                                            </label>
                                            <?php if (!empty($data['file_iklan'])) : ?>
                                                <div class="mb-2 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    File saat ini:
                                                    <a href="../uploads/iklan/<?= htmlspecialchars($data['file_iklan']) ?>"
                                                        target="_blank"
                                                        class="text-brand-500 hover:underline dark:text-brand-400">
                                                        <?= htmlspecialchars($data['file_iklan']) ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
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
                                            <div class="relative">
                                                <input
                                                    type="date" name="tanggal_mulai" id="tanggal_mulai" min="<?= date('Y-m-d') ?>"
                                                    value="<?= htmlspecialchars($data['tanggal_mulai']) ?>"
                                                    placeholder="Select date"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                                    onclick="this.showPicker()" />
                                                <span
                                                    class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                                    <svg
                                                        class="fill-current"
                                                        width="20"
                                                        height="20"
                                                        viewBox="0 0 20 20"
                                                        fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            fill-rule="evenodd"
                                                            clip-rule="evenodd"
                                                            d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                                                            fill="" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Tanggal Selesai -->
                                        <div>
                                            <label for="tanggal_selesai" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Tanggal Selesai
                                            </label>
                                            <div class="relative">
                                                <input
                                                    type="date" type="date" name="tanggal_selesai" id="tanggal_selesai"
                                                    value="<?= htmlspecialchars($data['tanggal_selesai']) ?>"
                                                    placeholder="Select date"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                                    onclick="this.showPicker()" />
                                                <span
                                                    class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                                    <svg
                                                        class="fill-current"
                                                        width="20"
                                                        height="20"
                                                        viewBox="0 0 20 20"
                                                        fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            fill-rule="evenodd"
                                                            clip-rule="evenodd"
                                                            d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                                                            fill="" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Durasi Hari (auto-hitung, readonly) -->
                                        <div>
                                            <label for="durasi_hari" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Durasi Hari <span class="text-xs text-gray-400">(otomatis dihitung)</span>
                                            </label>
                                            <input
                                                type="number" name="durasi_hari" id="durasi_hari" readonly
                                                value="<?= htmlspecialchars($data['durasi_hari']) ?>"
                                                placeholder="Otomatis terisi setelah memilih tanggal"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed" />
                                        </div>

                                        <!-- Harga -->
                                        <div>
                                            <label for="harga" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Total Harga
                                            </label>
                                            <input
                                                type="number" name="total_harga" id="harga" min="0" step="0.01" readonly
                                                value="<?= htmlspecialchars($data['total_harga']) ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed"
                                                placeholder="Otomatis terisi setelah memilih lokasi dan durasi ditentukan" />
                                        </div>

                                        <!-- Status Iklan -->
                                        <div>
                                            <label for="status_iklan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Status Iklan
                                            </label>
                                            <select name="status_iklan" id="status_iklan"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                                <option value="belum_tayang" <?= ($data['status_iklan'] === 'belum_tayang') ? 'selected' : '' ?>>Belum Tayang</option>
                                                <option value="aktif" <?= ($data['status_iklan'] === 'aktif') ? 'selected' : '' ?>>Aktif</option>
                                                <option value="selesai" <?= ($data['status_iklan'] === 'selesai') ? 'selected' : '' ?>>Selesai</option>
                                            </select>
                                        </div>

                                        <!-- Tombol Submit -->
                                        <div class="flex gap-3">
                                            <button type="submit" name="ubahData"
                                                class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition-colors">
                                                Ubah Data
                                            </button>
                                            <a href="data_iklan.php"
                                                class="bg-error-500 hover:bg-error-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition-colors">
                                                Batal
                                            </a>
                                        </div>

                                    </div>
                                </form>

                                <!-- Script auto-hitung durasi hari -->
                                <script>
                                    const tglMulai = document.getElementById('tanggal_mulai');
                                    const tglSelesai = document.getElementById('tanggal_selesai');
                                    const durasiInput = document.getElementById('durasi_hari');
                                    const lokasiSelect = document.getElementById('id_lokasi');
                                    const hargaInput = document.getElementById('harga');

                                    tglMulai.addEventListener('change', function() {
                                        tglSelesai.min = this.value;
                                        if (tglSelesai.value < this.value) {
                                            tglSelesai.value = '';
                                        }

                                    });


                                    function hitungDurasi() {
                                        const mulai = new Date(tglMulai.value);
                                        const selesai = new Date(tglSelesai.value);

                                        // ambil option lokasi yang dipilih
                                        const selectedOption =
                                            lokasiSelect.options[lokasiSelect.selectedIndex];

                                        // ambil harga dari data-harga
                                        const hargaPerHari =
                                            parseFloat(selectedOption.getAttribute('data-harga')) || 0;

                                        if (
                                            tglMulai.value && tglSelesai.value && selesai >= mulai
                                        ) {

                                            // hitung selisih hari
                                            const selisih =
                                                Math.round((selesai - mulai) / (1000 * 60 * 60 * 24));

                                            // isi durasi
                                            durasiInput.value = selisih;

                                            // hitung total harga
                                            const totalHarga =
                                                selisih * hargaPerHari;

                                            // isi input harga
                                            hargaInput.value = totalHarga;

                                        } else {

                                            durasiInput.value = '';
                                            hargaInput.value = '';

                                        }
                                    }

                                    tglMulai.addEventListener('change', hitungDurasi);
                                    tglSelesai.addEventListener('change', hitungDurasi);
                                    lokasiSelect.addEventListener('change', hitungDurasi);
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