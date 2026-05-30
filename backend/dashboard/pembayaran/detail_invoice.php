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
    <title>DETAIL INVOICE</title>
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
                                        Detail Invoice
                                    </h3>
                                </div>

                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                                <?php
                                if (!isset($_GET['id'])) {
                                    die("ID tidak ditemukan!");
                                }

                                $sql = "SELECT * FROM pembayaran WHERE id_pembayaran = :id_pembayaran";
                                $stmt = $conn->prepare($sql);
                                $stmt->bindParam(':id_pembayaran', $_GET['id']);
                                $stmt->execute();
                                $data = $stmt->fetch(PDO::FETCH_ASSOC);

                                $sqlJoin = "SELECT p.*, i.judul_iklan, i.durasi_hari, i.total_harga, pl.nama_pelanggan, pl.kode_pelanggan, l.nama_lokasi, l.alamat
                                            FROM pembayaran AS p
                                            JOIN iklan AS i ON p.id_iklan = i.id_iklan
                                            JOIN pelanggan AS pl ON i.id_pelanggan = pl.id_pelanggan
                                            JOIN lokasi_iklan AS l ON i.id_lokasi = l.id_lokasi
                                            WHERE p.id_pembayaran = :id;";
                                $stmtJoin = $conn->prepare($sqlJoin);
                                $stmtJoin->bindParam(':id', $_GET['id']);
                                $stmtJoin->execute();
                                $joinList = $stmtJoin->fetch(PDO::FETCH_ASSOC);

                                // ambil nominal bayar
                                $stmt = $conn->prepare("SELECT COALESCE(SUM(nominal_bayar),0) AS total_bayar
                                                        FROM detail_pembayaran
                                                        WHERE id_pembayaran = :id_pembayaran
                                                        AND status_bayar = 'lunas'
                                                    ");
                                $stmt->bindParam(':id_pembayaran', $_GET['id']);
                                $stmt->execute();
                                $totalBayar = $stmt->fetch(PDO::FETCH_ASSOC)['total_bayar'];

                                $sisaTagihan = $data['total_tagihan'] - $totalBayar;
                                if ($sisaTagihan < 0) {
                                    $sisaTagihan = $sisaTagihan . " (Kelebihan nanti akan dikembalikan ke pelanggan)";
                                }
                                if ($totalBayar >= $data['total_tagihan']) {
                                    $status = 'lunas';
                                } elseif ($totalBayar > 0) {
                                    $status = 'sebagian';
                                } else {
                                    $status = 'pending';
                                }
                                $updateStatus = $conn->prepare("UPDATE pembayaran
                                                            SET status_pembayaran = :status
                                                            WHERE id_pembayaran = :id_pembayaran
                                                        ");

                                $updateStatus->bindParam(':status', $status);
                                $updateStatus->bindParam(':id_pembayaran', $_GET['id']);
                                $updateStatus->execute();
                                if ($status === 'lunas') {
                                    $updateIklan = $conn->prepare("UPDATE iklan
                                                                SET status_pembayaran = 'lunas'
                                                                WHERE id_iklan = :id_iklan
                                                            ");
                                    $updateIklan->bindParam(':id_iklan', $joinList['id_iklan']);
                                    $updateIklan->execute();
                                }
                                ?>


                                <!-- Form -->
                                <form action="buat_invoice.php?id=<?= $_GET['id'] ?>" method="POST" enctype="multipart/form-data">
                                    <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">

                                        <!-- Kode Invoice -->
                                        <div>
                                            <label for="kode_invoice" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Kode Invoice
                                            </label>
                                            <input
                                                type="text" name="kode_invoice" id="kode_invoice" maxlength="50" readonly
                                                value="<?= htmlspecialchars($data['kode_invoice']) ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed"
                                                placeholder="Masukkan Kode Invoice | ex: #INV-001" />
                                        </div>

                                        <!-- Pelanggan -->
                                        <div>
                                            <label for="id_pelanggan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Pelanggan
                                            </label>
                                            <input type="text" name="id_iklan" value="<?= $joinList['id_iklan'] ?>" hidden>
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
                                            <input
                                                type="text"
                                                readonly
                                                value="<?= htmlspecialchars($joinList['nama_lokasi']) ?> | <?= htmlspecialchars($joinList['alamat']) ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed" />
                                        </div>

                                        <!-- Durasi Hari -->
                                        <div>
                                            <label for="durasi_hari" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Durasi Hari <span class="text-xs text-gray-400"></span>
                                            </label>
                                            <input
                                                type="number" name="durasi_hari" id="durasi_hari" readonly
                                                value="<?= htmlspecialchars($joinList['durasi_hari']) ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed" />
                                        </div>

                                        <!-- Harga -->
                                        <div>
                                            <label for="total_tagihan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Total Tagihan (Rp) <span class="text-xs text-gray-400"></span>
                                            </label>
                                            <input
                                                type="number" name="total_tagihan" id="total_tagihan" min="0" step="0.01" readonly
                                                value="<?= htmlspecialchars($data['total_tagihan']) ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed" />
                                        </div>

                                        <!-- Status Pembayaran -->
                                        <div>
                                            <label for="status_pembayaran" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Status Pembayaran
                                            </label>
                                            <input
                                                type="text" name="status_pembayaran" id="status_pembayaran" maxlength="50" readonly
                                                value="<?= ($data['status_pembayaran'] === 'pending') ? 'Pending' : (($data['status_pembayaran'] === 'lunas') ? 'Sudah Lunas' : (($data['status_pembayaran'] === 'sebagian') ? 'Sebagian Dibayar' : '')) ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed"
                                                placeholder="Masukkan Status Pembayaran" />
                                        </div>

                                        <!-- sisa tagihan -->
                                        <div>
                                            <label for="total_tagihan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Total tagihan yang harus dibayar <span class="text-xs text-gray-400"></span>
                                            </label>
                                            <input
                                                type="text" name="total_tagihan" id="total_tagihan" min="0" step="0.01" readonly
                                                value="<?= $sisaTagihan ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed" />
                                        </div>

                                        <!-- Tombol Submit -->
                                        <div class="flex gap-3">
                                            <?php if ($data['status_pembayaran'] !== 'lunas'): ?>
                                                <a href="buat_tagihan.php?id=<?= $_GET['id'] ?>"
                                                    class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition-colors">
                                                    Buat Tagihan
                                                </a>
                                            <?php endif; ?>
                                            <a href="lihat_tagihan.php?id=<?= $data['id_pembayaran'] ?>"
                                                class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition-colors">
                                                Lihat Tagihan
                                            </a>
                                            <a href="../iklan/data_iklan.php"
                                                class="bg-error-500 hover:bg-error-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition-colors">
                                                Kembali
                                            </a>
                                            <a href="../cetak/cetak_invoice.php?id=<?= $data['id_pembayaran'] ?>" target="_blank"
                                                class="bg-warning-500 hover:bg-warning-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition-colors">
                                                Cetak Invoice
                                            </a>
                                        </div>

                                    </div>
                                </form>

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