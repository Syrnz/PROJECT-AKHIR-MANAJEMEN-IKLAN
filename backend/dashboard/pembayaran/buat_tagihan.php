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
    <title>BUAT TAGIHAN</title>
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
                                        Buat Tagihan
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

                                $error = "";
                                if (isset($_POST['buatTagihan'])) {
                                    $nominal_bayar = htmlspecialchars(trim($_POST['nominal_bayar']));
                                    $metode_pembayaran = htmlspecialchars(trim($_POST['metode_pembayaran']));

                                    // Validasi field wajib
                                    if ($error === "") {
                                        if (
                                            empty($nominal_bayar) || empty($metode_pembayaran)
                                        ) {
                                            $error = "Semua field wajib diisi!";
                                        } elseif (!in_array($metode_pembayaran, ['transfer bank', 'cash', 'qris', 'e-wallet'])) {
                                            $error = "Metode pembayaran tidak valid!";
                                        } else {

                                            $sql = "INSERT INTO detail_pembayaran (id_pembayaran, tanggal_dibuat_tagihan, metode_pembayaran, nominal_bayar)
                                                    VALUES (:id_pembayaran, CURRENT_TIMESTAMP, :metode_pembayaran, :nominal_bayar)";

                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':id_pembayaran',   $_GET['id']);
                                            $stmt->bindParam(':metode_pembayaran', $metode_pembayaran);
                                            $stmt->bindParam(':nominal_bayar', $nominal_bayar);

                                            if ($stmt->execute()) {
                                                $id_pembayaran = $_GET['id'];

                                                echo "<script>
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Tagihan berhasil dibuat!',
                                                        showConfirmButton: false,
                                                        timer: 1500
                                                    }).then(() => {
                                                        window.location.href = 'detail_invoice.php?id=$id_pembayaran';
                                                    });
                                                </script>";
                                            } else {
                                                echo "<script>
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Tagihan gagal dibuat!',
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
                                <form action="buat_tagihan.php?id=<?= $_GET['id'] ?>" method="POST" enctype="multipart/form-data">
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

                                        <!-- Harga -->
                                        <div>
                                            <label for="total_tagihan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Total Tagihan (Rp) <span class="text-xs text-gray-400"></span>
                                            </label>
                                            <input
                                                type="number" name="total_tagihan" id="total_tagihan" min="0" step="0.01" readonly
                                                value="<?= htmlspecialchars($joinList['total_harga']) ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed" />
                                        </div>

                                        <!-- Jumlah Bayar -->
                                        <div>
                                            <label for="nominal_bayar" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Nominal Bayar (Rp) <span class="text-xs text-gray-400"></span>
                                            </label>
                                            <input
                                                type="number" name="nominal_bayar" id="nominal_bayar" min="0" step="1000" max="<?= htmlspecialchars($joinList['total_harga']) ?>"
                                                value="<?= isset($_POST['nominal_bayar']) ? htmlspecialchars($_POST['nominal_bayar']) : '' ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                        </div>

                                        <!-- Metode Pembayaran -->
                                        <div>
                                            <label for="metode_pembayaran" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Pilih Metode Pembayaran
                                            </label>
                                            <select name="metode_pembayaran" id="metode_pembayaran"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                                <option value="transfer bank" <?= (isset($_POST['metode_pembayaran']) && $_POST['metode_pembayaran'] === 'transfer bank')        ? 'selected' : '' ?>>Transfer Bank</option>
                                                <option value="cash" <?= (isset($_POST['metode_pembayaran']) && $_POST['metode_pembayaran'] === 'cash') ? 'selected' : '' ?>>Cash</option>
                                                <option value="qris" <?= (isset($_POST['metode_pembayaran']) && $_POST['metode_pembayaran'] === 'qris')      ? 'selected' : '' ?>>QRIS</option>
                                                <option value="e-wallet" <?= (isset($_POST['metode_pembayaran']) && $_POST['metode_pembayaran'] === 'e-wallet')      ? 'selected' : '' ?>>E-Wallet</option>
                                            </select>
                                        </div>

                                        <!-- Tombol Submit -->
                                        <div class="flex gap-3">
                                            <button type="submit" name="buatTagihan"
                                                class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition-colors">
                                                Tambah Tagihan
                                            </button>
                                            <a href="detail_invoice.php?id=<?= $_GET['id'] ?>"
                                                class="bg-error-500 hover:bg-error-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition-colors">
                                                Batal
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