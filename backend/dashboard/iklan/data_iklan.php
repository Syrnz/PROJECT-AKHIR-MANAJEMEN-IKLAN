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
                    // PROSES HAPUS
                    if (isset($_GET['hapus_confirmed'])) {
                        $id = (int) $_GET['hapus_confirmed'];
                        $stmt = $conn->prepare("SELECT iklan.*, pelanggan.nama_pelanggan, lokasi_iklan.nama_lokasi
                                                FROM iklan
                                                JOIN pelanggan 
                                                    ON iklan.id_pelanggan = pelanggan.id_pelanggan
                                                JOIN lokasi_iklan 
                                                    ON iklan.id_lokasi = lokasi_iklan.id_lokasi
                                                WHERE id_iklan = :id");
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();
                        $data = $stmt->fetch(PDO::FETCH_ASSOC);

                        $insertRiwayat = $conn->prepare("INSERT INTO riwayat_order (id_iklan_lama, id_pelanggan, id_lokasi, nama_pelanggan, nama_lokasi, judul_iklan, file_iklan, tanggal_mulai, tanggal_selesai,
                                                            durasi_hari, total_harga, status_iklan, alasan
                                                        ) VALUES (
                                                            :id_iklan_lama,
                                                            :id_pelanggan,
                                                            :id_lokasi,
                                                            :nama_pelanggan,
                                                            :nama_lokasi,
                                                            :judul_iklan,
                                                            :file_iklan,
                                                            :tanggal_mulai,
                                                            :tanggal_selesai,
                                                            :durasi_hari,
                                                            :total_harga,
                                                            'dihapus',
                                                            'Dihapus oleh admin, kesalahan data/atau pembatalan iklan'
                                                        )
                                                    ");

                        $insertRiwayat->execute([
                            ':id_iklan_lama' => $data['id_iklan'],
                            ':id_pelanggan' => $data['id_pelanggan'],
                            ':id_lokasi' => $data['id_lokasi'],
                            ':nama_pelanggan' => $data['nama_pelanggan'],
                            ':nama_lokasi' => $data['nama_lokasi'],
                            ':judul_iklan' => $data['judul_iklan'],
                            ':file_iklan' => $data['file_iklan'],
                            ':tanggal_mulai' => $data['tanggal_mulai'],
                            ':tanggal_selesai' => $data['tanggal_selesai'],
                            ':durasi_hari' => $data['durasi_hari'],
                            ':total_harga' => $data['total_harga']
                        ]);

                        $updateLokasi = $conn->prepare("UPDATE lokasi_iklan 
                                                        SET status = 'tersedia'
                                                        WHERE id_lokasi = :id_lokasi
                                                    ");
                        $updateLokasi->execute([
                            ':id_lokasi' => $data['id_lokasi']
                        ]);
                        $updateIklan = $conn->prepare("
                            UPDATE iklan
                            SET status_data = 'dibatalkan'
                            WHERE id_iklan = :id
                        ");
                        $updateIklan->execute([
                            ':id' => $id
                        ]);
                        if ($updateIklan->execute()) {
                            echo "<script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Data iklan berhasil dihapus.',
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        window.location.href = 'data_iklan.php';
                                    });
                                });
                            </script>";
                        } else {
                            echo "<script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: 'Data iklan gagal dihapus.',
                                        icon: 'error',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                });
                            </script>";
                        }
                    }

                    // PROSES PINDAH KE ARSIP
                    if (isset($_GET['pindah_arsip_confirmed'])) {
                        $id = (int) $_GET['pindah_arsip_confirmed'];
                        $stmt = $conn->prepare("SELECT iklan.*, pelanggan.nama_pelanggan, lokasi_iklan.nama_lokasi
                                                FROM iklan
                                                JOIN pelanggan 
                                                    ON iklan.id_pelanggan = pelanggan.id_pelanggan
                                                JOIN lokasi_iklan 
                                                    ON iklan.id_lokasi = lokasi_iklan.id_lokasi
                                                WHERE id_iklan = :id");
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();
                        $data = $stmt->fetch(PDO::FETCH_ASSOC);

                        $insertRiwayat = $conn->prepare("INSERT INTO riwayat_order (id_iklan_lama, id_pelanggan, id_lokasi, nama_pelanggan, nama_lokasi, judul_iklan, file_iklan, tanggal_mulai, tanggal_selesai,
                                                            durasi_hari, total_harga, status_iklan, alasan
                                                        ) VALUES (
                                                            :id_iklan_lama,
                                                            :id_pelanggan,
                                                            :id_lokasi,
                                                            :nama_pelanggan,
                                                            :nama_lokasi,
                                                            :judul_iklan,
                                                            :file_iklan,
                                                            :tanggal_mulai,
                                                            :tanggal_selesai,
                                                            :durasi_hari,
                                                            :total_harga,
                                                            'selesai',
                                                            'Pembayaran lunas, iklan dipindahkan ke arsip'
                                                        )
                                                    ");

                        $insertRiwayat->execute([
                            ':id_iklan_lama' => $data['id_iklan'],
                            ':id_pelanggan' => $data['id_pelanggan'],
                            ':id_lokasi' => $data['id_lokasi'],
                            ':nama_pelanggan' => $data['nama_pelanggan'],
                            ':nama_lokasi' => $data['nama_lokasi'],
                            ':judul_iklan' => $data['judul_iklan'],
                            ':file_iklan' => $data['file_iklan'],
                            ':tanggal_mulai' => $data['tanggal_mulai'],
                            ':tanggal_selesai' => $data['tanggal_selesai'],
                            ':durasi_hari' => $data['durasi_hari'],
                            ':total_harga' => $data['total_harga']
                        ]);

                        $updateLokasi = $conn->prepare("UPDATE lokasi_iklan 
                                                        SET status = 'tersedia'
                                                        WHERE id_lokasi = :id_lokasi
                                                    ");
                        $updateLokasi->execute([
                            ':id_lokasi' => $data['id_lokasi']
                        ]);
                        $updateIklan = $conn->prepare("
                            UPDATE iklan
                            SET status_data = 'selesai'
                            WHERE id_iklan = :id
                        ");
                        $updateIklan->execute([
                            ':id' => $id
                        ]);
                        if ($updateIklan->execute()) {
                            echo "<script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Data iklan berhasil dipindahkan ke arsip.',
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        window.location.href = 'data_iklan.php';
                                    });
                                });
                            </script>";
                        } else {
                            echo "<script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: 'Data iklan gagal dipindahkan ke arsip.',
                                        icon: 'error',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                });
                            </script>";
                        }
                    }

                    // AMBIL DATA IKLAN + NAMA PELANGGAN
                    $sql  = "SELECT i.*, p.nama_pelanggan, p.kode_pelanggan, l.nama_lokasi, l.alamat, pb.id_pembayaran
                                FROM iklan AS i
                                JOIN pelanggan AS p ON i.id_pelanggan = p.id_pelanggan
                                JOIN lokasi_iklan AS l ON i.id_lokasi = l.id_lokasi
                                LEFT JOIN pembayaran AS pb ON i.id_iklan = pb.id_iklan
                                WHERE i.status_data = 'aktif'
                                ORDER BY i.created_at DESC";
                    $stmt = $conn->query($sql);
                    $iklanList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <div class="grid grid-cols-12 gap-4 md:gap-6">
                        <div class="col-span-12 space-y-6">
                            <div class="space-y-5 sm:space-y-6">
                                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                                    <!-- Header Card -->
                                    <div class="flex items-center justify-between px-5 py-4 sm:px-6 sm:py-5">
                                        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                                            Data Iklan
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
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Pelanggan | Kode</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Jenis Periklanan</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Lokasi Pengiklanan</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Judul Iklan</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tanggal Mulai</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tanggal Selesai</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Durasi</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Harga</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">File</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status Iklan</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status Pembayaran</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Action</p>
                                                            </th>
                                                            <th class="px-5 py-3 sm:px-6 text-left">
                                                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Invoice</p>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                                        <?php if (empty($iklanList)) : ?>
                                                            <tr>
                                                                <td colspan="11" class="px-5 pt-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                                                    Belum ada data iklan.
                                                                </td>
                                                            </tr>
                                                        <?php else : ?>
                                                            <?php foreach ($iklanList as $index => $iklan) : ?>
                                                                    <tr>

                                                                        <!-- No -->
                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <?= $index + 1 ?>
                                                                            </p>
                                                                        </td>

                                                                        <!-- Nama Pelanggan -->
                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <p class="text-gray-800 text-theme-sm dark:text-white/90 font-medium">
                                                                                <?= htmlspecialchars($iklan['nama_pelanggan'] ?? '-') ?> |
                                                                                <?= htmlspecialchars($iklan['kode_pelanggan'] ?? '-') ?>
                                                                            </p>
                                                                        </td>

                                                                        <!-- Jenis Iklan -->
                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400 max-w-[180px] truncate" title="<?= htmlspecialchars($iklan['judul_iklan']) ?>">
                                                                                <?= htmlspecialchars($iklan['nama_lokasi']) ?>
                                                                            </p>
                                                                        </td>

                                                                        <!-- Alamat Iklan -->
                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400 max-w-[180px] truncate" title="<?= htmlspecialchars($iklan['alamat']) ?>">
                                                                                <?= htmlspecialchars($iklan['alamat']) ?>
                                                                            </p>
                                                                        </td>

                                                                        <!-- Judul Iklan -->
                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400 max-w-[180px] truncate" title="<?= htmlspecialchars($iklan['judul_iklan']) ?>">
                                                                                <?= htmlspecialchars($iklan['judul_iklan']) ?>
                                                                            </p>
                                                                        </td>

                                                                        <!-- Tanggal Mulai -->
                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <?= date('d M Y', strtotime($iklan['tanggal_mulai'])) ?>
                                                                            </p>

                                                                        </td>

                                                                        <!-- Tanggal Selesai -->
                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <?= date('d M Y', strtotime($iklan['tanggal_selesai'])) ?>
                                                                            </p>
                                                                        </td>

                                                                        <!-- Durasi Hari -->
                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <?= $iklan['durasi_hari'] !== null ? $iklan['durasi_hari'] . ' hari' : '-' ?>
                                                                            </p>
                                                                        </td>

                                                                        <!-- Harga -->
                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400 whitespace-nowrap">
                                                                                Rp <?= number_format($iklan['total_harga'], 0, ',', '.') ?>
                                                                            </p>
                                                                        </td>

                                                                        <!-- File Iklan -->
                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <?php if (!empty($iklan['file_iklan'])) : ?>
                                                                                <a href="../uploads/iklan/<?= htmlspecialchars($iklan['file_iklan']) ?>"
                                                                                    target="_blank"
                                                                                    class="text-brand-500 hover:text-brand-600 text-theme-sm dark:text-brand-400 dark:hover:text-brand-300 underline">
                                                                                    Lihat File
                                                                                </a>
                                                                            <?php else : ?>
                                                                                <span class="text-gray-400 text-theme-sm">-</span>
                                                                            <?php endif; ?>
                                                                        </td>

                                                                        <!-- Status Iklan -->

                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <?php
                                                                            $statusBadge = [
                                                                                'belum_tayang' => 'rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-700 dark:bg-warning-500/15 dark:text-warning-400',
                                                                                'aktif'        => 'rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500',
                                                                                'selesai'      => 'rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-700 dark:bg-error-500/15 dark:text-error-500',
                                                                            ];
                                                                            $statusLabel = [
                                                                                'belum_tayang' => 'Belum Tayang',
                                                                                'aktif'        => 'Aktif',
                                                                                'selesai'      => 'Selesai',
                                                                            ];
                                                                            $status = $iklan['status_iklan'];
                                                                            $sBadge = $statusBadge[$status] ?? 'bg-gray-100 text-gray-600';
                                                                            $sLabel = $statusLabel[$status] ?? $status;
                                                                            ?>
                                                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $sBadge ?>">
                                                                                <?= $sLabel ?>
                                                                            </span>
                                                                        </td>

                                                                        <!-- Status Pembayaran -->
                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <?php
                                                                            $statusBadge = [
                                                                                'pending' => 'rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-700 dark:bg-warning-500/15 dark:text-warning-400',
                                                                                'lunas'   => 'rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500',
                                                                            ];
                                                                            $statusLabel = [
                                                                                'pending' => 'Pending',
                                                                                'lunas'   => 'Lunas',
                                                                            ];
                                                                            $status = $iklan['status_pembayaran'];
                                                                            $sBadge = $statusBadge[$status] ?? 'bg-gray-100 text-gray-600';
                                                                            $sLabel = $statusLabel[$status] ?? $status;
                                                                            ?>
                                                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $sBadge ?>">
                                                                                <?= $sLabel ?>
                                                                            </span>
                                                                        </td>

                                                                        <!-- Action -->
                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <div class="flex items-center gap-2">
                                                                                <a href="edit_data_iklan.php?id=<?= $iklan['id_iklan'] ?>"
                                                                                    class="text-brand-500 hover:text-brand-600 text-theme-sm dark:text-brand-400 dark:hover:text-brand-300">
                                                                                    Edit
                                                                                </a>
                                                                                <span class="text-gray-300 dark:text-gray-600">|</span>
                                                                                <button
                                                                                    onclick="konfirmasiHapus(<?= $iklan['id_iklan'] ?>, '<?= htmlspecialchars(addslashes($iklan['judul_iklan'])) ?>')"
                                                                                    class="text-error-500 hover:text-error-600 text-theme-sm dark:text-error-400 dark:hover:text-error-300 bg-transparent border-none cursor-pointer p-0">
                                                                                    Batal / Hapus
                                                                                </button>
                                                                                <span class="text-gray-300 dark:text-gray-600">|</span>
                                                                                <button
                                                                                    onclick="konfirmasiPindahArsip(<?= $iklan['id_iklan'] ?>, '<?= htmlspecialchars(addslashes($iklan['judul_iklan'])) ?>')"
                                                                                    class="text-warning-500 hover:text-warning-600 text-theme-sm dark:text-warning-400 dark:hover:text-warning-300 bg-transparent border-none cursor-pointer p-0">
                                                                                    Pindahkan Arsip
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                        <!-- Invoice -->
                                                                        <td class="px-5 py-4 sm:px-6">
                                                                            <div class="flex items-center gap-2">
                                                                                <?php if ($iklan['id_pembayaran']) : ?>
                                                                                    <a href="../pembayaran/detail_invoice.php?id=<?= $iklan['id_pembayaran'] ?>"
                                                                                        class="text-success-500 hover:text-success-600 text-theme-sm">
                                                                                        Lihat Invoice
                                                                                    </a>
                                                                                <?php else : ?>
                                                                                    <a href="../pembayaran/buat_invoice.php?id=<?= $iklan['id_iklan'] ?>"
                                                                                        class="text-brand-500 hover:text-brand-600 text-theme-sm dark:text-brand-400 dark:hover:text-brand-300">
                                                                                        Buat Invoice
                                                                                    </a>
                                                                                <?php endif; ?>
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