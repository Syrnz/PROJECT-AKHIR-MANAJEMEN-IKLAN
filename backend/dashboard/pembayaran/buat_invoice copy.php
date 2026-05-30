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
    <title>BUAT INVOICE</title>
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
                                        Buat Invoice
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

                                $error = "";
                                if (isset($_POST['buatInvoice'])) {
                                    $kode_invoice = htmlspecialchars(trim($_POST['kode_invoice']));
                                    $tanggal_invoice = htmlspecialchars(trim($_POST['tanggal_invoice']));
                                    $jatuh_tempo = htmlspecialchars(trim($_POST['jatuh_tempo']));
                                    $jumlah_bayar = htmlspecialchars(trim($_POST['jumlah_bayar']));
                                    $metode_pembayaran = htmlspecialchars(trim($_POST['metode_pembayaran']));
                                    $status_pembayaran = htmlspecialchars(trim($_POST['status_pembayaran']));
                                    $catatan = htmlspecialchars(trim($_POST['catatan']));
                                    // Validasi field wajib
                                    if ($error === "") {
                                        if (
                                            empty($kode_invoice) || empty($tanggal_invoice) || empty($jatuh_tempo) ||
                                            empty($jumlah_bayar) || empty($metode_pembayaran) || empty($status_pembayaran)
                                        ) {
                                            $error = "Semua field wajib diisi!";
                                        } elseif (strlen($kode_invoice) > 50) {
                                            $error = "Kode invoice tidak boleh melebihi 50 karakter.";
                                        } elseif (strlen($catatan) > 255) {
                                            $error = "Catatan tidak boleh melebihi 255 karakter.";
                                        } elseif (!in_array($status_pembayaran, ['pending', 'dp', 'lunas'])) {
                                            $error = "Status pembayaran tidak valid!";
                                        } elseif (!in_array($metode_pembayaran, ['transfer bank', 'cash', 'qris', 'e-wallet'])) {
                                            $error = "Metode pembayaran tidak valid!";
                                        } elseif (strtotime($jatuh_tempo) < strtotime($tanggal_invoice)) {
                                            $error = "Tanggal jatuh tempo tidak boleh sebelum tanggal invoice.";
                                        } else {

                                            $sql = "INSERT INTO pembayaran (id_iklan, kode_invoice, tanggal_invoice, jatuh_tempo, metode_pembayaran, total_tagihan, jumlah_bayar, status_pembayaran, catatan, created_at)
                                                    VALUES (:id_iklan, :kode_invoice, :tanggal_invoice, :jatuh_tempo, :metode_pembayaran, :total_tagihan, :jumlah_bayar, :status_pembayaran, :catatan, CURRENT_TIMESTAMP)";

                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':id_iklan',        $_GET['id']);
                                            $stmt->bindParam(':kode_invoice',    $kode_invoice);
                                            $stmt->bindParam(':tanggal_invoice', $tanggal_invoice);
                                            $stmt->bindParam(':jatuh_tempo',     $jatuh_tempo);
                                            $stmt->bindParam(':total_tagihan',   $data['total_harga']);
                                            $stmt->bindParam(':jumlah_bayar',    $jumlah_bayar);
                                            $stmt->bindParam(':metode_pembayaran', $metode_pembayaran);
                                            $stmt->bindParam(':status_pembayaran', $status_pembayaran);
                                            $stmt->bindParam(':catatan',         $catatan);

                                            if ($stmt->execute()) {

                                                echo "<script>
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Invoice berhasil dibuat!',
                                                        showConfirmButton: false,
                                                        timer: 1500
                                                    }).then(() => {
                                                        window.location.href = 'daftar_invoice.php';
                                                    });
                                                </script>";
                                            } else {
                                                echo "<script>
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Invoice gagal dibuat!',
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
                                <form action="buat_invoice.php?id=<?= $_GET['id'] ?>" method="POST" enctype="multipart/form-data">
                                    <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">

                                        <!-- Kode Invoice -->
                                        <div>
                                            <label for="kode_invoice" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Kode Invoice
                                            </label>
                                            <input
                                                type="text" name="kode_invoice" id="kode_invoice" maxlength="50"
                                                value="<?= isset($_POST['kode_invoice']) ? htmlspecialchars($_POST['kode_invoice']) : '' ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                                placeholder="Masukkan Kode Invoice | ex: #INV-001" />
                                        </div>

                                        <!-- Tanggal Invoice Dibuat -->
                                        <div>
                                            <label for="tanggal_invoice" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Tanggal Invoice Dibuat
                                            </label>
                                            <!-- buat tanggal menjadi sekarang tidak boleh kemarin atau besok langsung dibuat di value-->
                                            <div class="relative">
                                                <input
                                                    type="date" name="tanggal_invoice" id="tanggal_invoice" min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>"
                                                    placeholder="Select date"
                                                    value="<?= isset($_POST['tanggal_invoice']) ? htmlspecialchars($_POST['tanggal_invoice']) : '' ?>"
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

                                        <!-- Tanggal jatuh tempo -->
                                        <div>
                                            <label for="jatuh_tempo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Tanggal Jatuh Tempo
                                            </label>
                                            <!-- buat tanggal menjadi sekarang tidak boleh kemarin atau besok langsung dibuat di value -->
                                            <div class="relative">
                                                <input
                                                    type="date" name="jatuh_tempo" id="jatuh_tempo" min="<?= date('Y-m-d') ?>" max="<?= htmlspecialchars($data['tanggal_selesai']) ?>"
                                                    placeholder="Select date"
                                                    value="<?= isset($_POST['jatuh_tempo']) ? htmlspecialchars($_POST['jatuh_tempo']) : '' ?>"
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

                                        <!-- Durasi Hari (auto-hitung, readonly) -->
                                        <div>
                                            <label for="durasi_hari" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Durasi Hari <span class="text-xs text-gray-400"></span>
                                            </label>
                                            <input
                                                type="number" name="durasi_hari" id="durasi_hari" readonly
                                                value="<?= htmlspecialchars($data['durasi_hari']) ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed" />
                                        </div>

                                        <!-- Harga -->
                                        <div>
                                            <label for="total_tagihan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Total Tagihan (Rp) <span class="text-xs text-gray-400"></span>
                                            </label>
                                            <input
                                                type="number" name="total_tagihan" id="total_tagihan" min="0" step="0.01" readonly
                                                value="<?= htmlspecialchars($data['total_harga']) ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/50 cursor-not-allowed" />
                                        </div>

                                        <!-- Jumlah Bayar -->
                                        <div>
                                            <label for="jumlah_bayar" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Jumlah Bayar (Rp) <span class="text-xs text-gray-400"></span>
                                            </label>
                                            <input
                                                type="number" name="jumlah_bayar" id="jumlah_bayar" min="0" step="1000" max="<?= htmlspecialchars($data['total_harga']) ?>"
                                                value="<?= isset($_POST['jumlah_bayar']) ? htmlspecialchars($_POST['jumlah_bayar']) : '' ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                        </div>

                                        <!-- Status Pembayaran -->
                                        <div>
                                            <label for="status_pembayaran" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Status Pembayaran
                                            </label>
                                            <select name="status_pembayaran" id="status_pembayaran"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                                <option value="pending" <?= (isset($_POST['status_pembayaran']) && $_POST['status_pembayaran'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                                                <option value="dp" <?= (isset($_POST['status_pembayaran']) && $_POST['status_pembayaran'] === 'dp') ? 'selected' : '' ?>>Down Payment</option>
                                                <option value="lunas" <?= (isset($_POST['status_pembayaran']) && $_POST['status_pembayaran'] === 'lunas') ? 'selected' : '' ?>>Pelunasan</option>
                                            </select>
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

                                        <!-- Catatan -->
                                        <div>
                                            <label for="catatan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Catatan (Opsional)
                                            </label>
                                            <input
                                                type="text" name="catatan" id="catatan" maxlength="255"
                                                value="<?= isset($_POST['catatan']) ? htmlspecialchars($_POST['catatan']) : '' ?>"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                                placeholder="Masukkan Catatan" />
                                        </div>

                                        <!-- Tombol Submit -->
                                        <div class="flex gap-3">
                                            <button type="submit" name="buatInvoice"
                                                class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition-colors">
                                                Tambah Tagihan
                                            </button>
                                            <a href="../iklan/data_iklan.php"
                                                class="bg-error-500 hover:bg-error-600 rounded-lg px-5 py-3 text-sm font-medium text-white transition-colors">
                                                Batal
                                            </a>
                                        </div>

                                    </div>
                                </form>

                                <!-- Script auto-hitung durasi hari -->
                                <script>

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