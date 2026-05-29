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
        <title>
            DATA Periklanan Videotron
        </title>
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
            <div
                class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent"></div>
        </div>

        <!-- ===== Preloader End ===== -->

        <!-- ===== Page Wrapper Start ===== -->
        <div class="flex h-screen overflow-hidden">
            <!-- ===== Sidebar Start ===== -->
            <?php include('../partials/sidebar.php') ?>

            <!-- ===== Sidebar End ===== -->

            <!-- ===== Content Area Start ===== -->
            <div
                class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
                <!-- Small Device Overlay Start -->
                <div
                    @click="sidebarToggle = false"
                    :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
                    class="fixed w-full h-screen z-9 bg-gray-900/50"></div>
                <!-- Small Device Overlay End -->

                <!-- ===== Header Start ===== -->
                <?php include('../partials/header.php') ?>
                <!-- ===== Header End ===== -->

                <!-- ===== Main Content Start ===== -->
                <main>
                    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                        <div class="grid grid-cols-12 gap-4 md:gap-6">
                            <div class="col-span-12 space-y-6">
                                <div class="space-y-5 sm:space-y-6">
                                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                                        <div class="px-5 py-4 sm:px-6 sm:py-5">
                                            <h3
                                                class="text-base font-medium text-gray-800 dark:text-white/90">
                                                Data Periklanan Videotron
                                            </h3>
                                        </div>
                                        <div
                                            class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                                            <!-- ====== Table -->
                                            <div
                                                class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                                                <div class="max-w-full overflow-x-auto">
                                                    <?php
                                                    $sql = "SELECT * FROM lokasi_iklan
                                                            JOIN jenis_iklan
                                                            ON lokasi_iklan.id_jenis = jenis_iklan.id_jenis
                                                            WHERE jenis_iklan.nama_jenis = 'Videotron'";
                                                    $stmt = $conn->query($sql);

                                                    $stmt->execute();
                                                    $lokasi_iklan = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <table class="min-w-full">
                                                        <thead>
                                                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                                                <th class="px-5 py-3 sm:px-6">
                                                                    <div class="flex items-center">
                                                                        <p
                                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                            No
                                                                        </p>
                                                                    </div>
                                                                </th>
                                                                <th class="px-5 py-3 sm:px-6">
                                                                    <div class="flex items-center">
                                                                        <p
                                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                            Kode Lokasi
                                                                        </p>
                                                                    </div>
                                                                </th>
                                                                <th class="px-5 py-3 sm:px-6">
                                                                    <div class="flex items-center">
                                                                        <p
                                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                            Nama Lokasi
                                                                        </p>
                                                                    </div>
                                                                </th>
                                                                <th class="px-5 py-3 sm:px-6">
                                                                    <div class="flex items-center">
                                                                        <p
                                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                            Alamat
                                                                        </p>
                                                                    </div>
                                                                </th>
                                                                <th class="px-5 py-3 sm:px-6">
                                                                    <div class="flex items-center">
                                                                        <p
                                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                            Harga Sewa
                                                                        </p>
                                                                    </div>
                                                                </th>
                                                                <th class="px-5 py-3 sm:px-6">
                                                                    <div class="flex items-center">
                                                                        <p
                                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                            Status
                                                                        </p>
                                                                    </div>
                                                                </th>
                                                                <th class="px-5 py-3 sm:px-6">
                                                                    <div class="flex items-center">
                                                                        <p
                                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                            Action
                                                                        </p>
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                            <?php foreach ($lokasi_iklan as $index => $lokasi): ?>
                                                                <tr>
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <!-- make this no++ -->
                                                                                <?php echo $index + 1; ?>
                                                                            </p>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <?php echo htmlspecialchars($lokasi['kode_lokasi']); ?>
                                                                            </p>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <?php echo htmlspecialchars($lokasi['nama_lokasi']); ?>
                                                                            </p>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <?php echo htmlspecialchars($lokasi['alamat']); ?>
                                                                            </p>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <?php echo htmlspecialchars($lokasi['harga']); ?>
                                                                            </p>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center">
                                                                            <?php
                                                                            $statusBadge = [
                                                                                'disewa' => 'rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-700 dark:bg-warning-500/15 dark:text-warning-400',
                                                                                'tersedia'        => 'rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500',
                                                                                'maintenance'      => 'rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-700 dark:bg-error-500/15 dark:text-error-500',
                                                                            ];
                                                                            $statusLabel = [
                                                                                'tersedia' => 'Tersedia',
                                                                                'disewa'        => 'Disewa',
                                                                                'maintenance'      => 'Maintenance',
                                                                            ];
                                                                            $status = $lokasi['status'];
                                                                            $sBadge = $statusBadge[$status] ?? 'bg-gray-100 text-gray-600';
                                                                            $sLabel = $statusLabel[$status] ?? $status;
                                                                            ?>
                                                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $sBadge ?>">
                                                                                <?= $sLabel ?>
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <!-- <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center">
                                                                            <a href="edit_data_lokasi.php?id=<?php echo $lokasi['id_lokasi']; ?>" class="text-brand-500 hover:text-brand-600 text-theme-sm dark:text-brand-400 dark:hover:text-brand-300">
                                                                                Edit
                                                                            </a>
                                                                            <span class="mx-2 dark:text-gray-400">|</span>

                                                                            <a href="?hapus=<?= $lokasi['id_lokasi']; ?>" class="text-error-500 hover:text-error-600 text-theme-sm dark:text-error-400 dark:hover:text-error-300">
                                                                                Delete
                                                                            </a>
                                                                        </div>
                                                                    </td> -->
                                                                </tr>
                                                            <?php endforeach; ?>
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
        <?php
        if (isset($_GET["hapus"])) {

            $id = $_GET["hapus"];
            echo " <script>
                Swal.fire({
                    title: 'Apakah kamu yakin hapus data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6b7280'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'data_pelanggan.php?hapus_confirmed=$id';
                    }
                });
                </script>
                ";
        }

        // if (isset($_GET["hapus_confirmed"])) {
        //     $id = $_GET["hapus_confirmed"];
        //     $sql = "DELETE FROM";
        //     $stmt = $conn->prepare($sql);
        //     $stmt->bindParam(':id', $id);

        //     if ($stmt->execute()) {
        //         echo "<script>
        //             Swal.fire({
        //                 title: 'Berhasil!',
        //                 text: 'Data berhasil dihapus',
        //                 icon: 'success',
        //                 showConfirmButton: false,
        //                 timer: 1500
        //             }).then(() => {
        //                 window.location.href = 'data_pelanggan.php';
        //             });
        //             </script>
        //             ";
        //     } else {
        //         echo "<script>
        //             Swal.fire({
        //                 title: 'Gagal!',
        //                 text: 'Data gagal dihapus',
        //                 icon: 'error',
        //                 showConfirmButton: false,
        //                 timer: 1500
        //             });
        //             </script>
        //             ";
        //     }
        // }
        ?>
        <!-- ===== Page Wrapper End ===== -->
        <script defer src="../src/js/bundle.js"></script>
    </body>

    </html>