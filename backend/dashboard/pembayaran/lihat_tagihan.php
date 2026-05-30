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
    <title>DATA IKLAN</title>
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
    <!-- ===== Preloader End ===== -->

    <!-- ===== Page Wrapper Start ===== -->
    <div class="flex h-screen overflow-hidden">

        <!-- ===== Sidebar ===== -->
        <?php include('../partials/sidebar.php') ?>

        <!-- ===== Content Area ===== -->
        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">

            <!-- Small Device Overlay -->
            <div
                @click="sidebarToggle = false"
                :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
                class="fixed w-full h-screen z-9 bg-gray-900/50"></div>

            <!-- ===== Header ===== -->
            <?php include('../partials/header.php') ?>

            <!-- ===== Main Content ===== -->
            <main>
                <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

                    <?php
                    if (!isset($_GET['id'])) {
                        die("ID tidak ditemukan!");
                    }
                    $id_pembayaran = $_GET['id'] ?? 0;
                    $stmt = $conn->prepare("SELECT *
                                            FROM pembayaran
                                            WHERE id_pembayaran = :id_pembayaran
                                        ");
                    $stmt->bindParam(':id_pembayaran', $id_pembayaran);
                    $stmt->execute();
                    $tagihan = $stmt->fetch(PDO::FETCH_ASSOC);

                    $stmtDetail = $conn->prepare("SELECT *
                                                    FROM detail_pembayaran
                                                    WHERE id_pembayaran = :id_pembayaran
                                                    ORDER BY tanggal_dibuat_tagihan DESC
                                                ");
                    $stmtDetail->bindParam(':id_pembayaran', $id_pembayaran);
                    $stmtDetail->execute();
                    $detailPembayaran = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);

                    ?>

                    <div class="grid grid-cols-12 gap-4 md:gap-6">
                        <div class="col-span-12 space-y-6">
                            <div class="space-y-5 sm:space-y-6">
                                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                                    <!-- Header Card -->
                                    <div class="flex items-center justify-between px-5 py-4 sm:px-6 sm:py-5">
                                        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                                            Data Tagihan Pelanggan
                                        </h3>
                                    </div>

                                    <!-- Table -->
                                    <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                                        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                                            <div class="max-w-full overflow-x-auto">
                                                <table class="min-w-full">
                                                    <thead>
                                                        <tr class="border-b border-gray-100 dark:border-gray-800">
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">No</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tanggal Tagihan Dibuat</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Nominal Harga</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Metode Pembayaran</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tanggal Bayar</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Bukti Pembayaran</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Konfirmasi</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Cetak Invoice</p>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                                        <?php if (empty($detailPembayaran)) : ?>
                                                            <tr>
                                                                <td colspan="11" class="px-5 pt-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                                                    Belum ada data tagihan.
                                                                </td>
                                                            </tr>
                                                        <?php else : ?>
                                                            <?php foreach ($detailPembayaran as $index => $pembayaran) : ?>
                                                                <tr>

                                                                    <!-- No -->
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                            <?= $index + 1 ?>
                                                                        </p>
                                                                    </td>


                                                                    <!-- Tanggal Mulai -->
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                            <?= date('d M Y', strtotime($pembayaran['tanggal_dibuat_tagihan'])) ?>
                                                                        </p>
                                                                    </td>

                                                                    <!-- Nominal Bayar -->
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400 whitespace-nowrap">
                                                                            Rp <?= number_format($pembayaran['nominal_bayar'], 0, ',', '.') ?>
                                                                        </p>
                                                                    </td>

                                                                    <!-- Metode Pembayaran -->
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                            <?= htmlspecialchars($pembayaran['metode_pembayaran']) ?>
                                                                        </p>
                                                                    </td>

                                                                    <!-- Tanggal Dibayar -->
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <?php if (!empty($pembayaran['tanggal_bayar'])) : ?>
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <?= date('d M Y', strtotime($pembayaran['tanggal_bayar'])) ?>
                                                                            </p>
                                                                        <?php else : ?>
                                                                            <span class="text-gray-400 text-theme-sm">Belum Dibayar</span>
                                                                        <?php endif; ?>
                                                                    </td>

                                                                    <!-- File Pembayaran -->
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <?php if (!empty($pembayaran['bukti_pembayaran'])) : ?>
                                                                            <a href="../uploads/pembayaran/<?= htmlspecialchars($pembayaran['bukti_pembayaran']) ?>"
                                                                                target="_blank"
                                                                                class="text-brand-500 hover:text-brand-600 text-theme-sm dark:text-brand-400 dark:hover:text-brand-300 underline">
                                                                                Lihat File
                                                                            </a>
                                                                        <?php else : ?>
                                                                            <span class="text-gray-400 text-theme-sm">-</span>
                                                                        <?php endif; ?>
                                                                    </td>

                                                                    <!-- Action -->
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center gap-2">
                                                                            <a href="konfirmasi_pembayaran.php?id=<?= $pembayaran['id_detail'] ?>"
                                                                                class="text-success-500 hover:text-brand-600 text-theme-sm dark:text-brand-400 dark:hover:text-brand-300">
                                                                                Konfirmasi Pembayaran
                                                                            </a>
                                                                            <span class="text-gray-300 dark:text-gray-600">|</span>
                                                                            <a href="detail_invoice.php?id=<?= $pembayaran['id_pembayaran'] ?>"
                                                                                class="text-error-500 hover:text-error-600 text-theme-sm dark:text-error-400 dark:hover:text-error-300">
                                                                                Kembali
                                                                            </a>
                                                                        </div>
                                                                    </td>

                                                                    <!-- Cetak -->
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center gap-2">
                                                                            <a href="../cetak/cetak_tagihan.php?id=<?= $pembayaran['id_detail'] ?>" target="_blank"
                                                                                class="text-warning-500 hover:text-warning-600 text-theme-sm dark:text-warning-400 dark:hover:text-warning-300">
                                                                                Cetak Tagihan
                                                                            </a>
                                                                        </div>
                                                                    </td>

                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <!-- ===== Page Wrapper End ===== -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="../src/js/bundle.js"></script>

    <script>
        function konfirmasiHapus(id, judul) {
            Swal.fire({
                title: 'Hapus Iklan?',
                html: `Iklan <strong>${judul}</strong> akan dihapus secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `data_iklan.php?hapus_confirmed=${id}`;
                }
            });
        }

        function konfirmasiPindahArsip(id, judul) {
            Swal.fire({
                title: 'Pindahkan ke Arsip?',
                html: `Iklan <strong>${judul}</strong> akan dipindahkan ke arsip.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Pindahkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `data_iklan.php?pindah_arsip_confirmed=${id}`;
                }
            });
        }
    </script>

</body>

</html>