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
            DATA PELANGGAN
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
                                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
                                        <div class="flex justify-between px-5 py-4 sm:px-6 sm:py-5">
                                            <h3
                                                class="text-base font-medium text-gray-800 dark:text-white/90">
                                                Data Pelanggan
                                            </h3>
                                            <div class="hidden lg:block">
                                                <form action="" method="POST">
                                                    <div class="relative">
                                                        <span class="absolute top-1/2 left-4 -translate-y-1/2">
                                                            <svg
                                                                class="fill-gray-500 dark:fill-gray-400"
                                                                width="20"
                                                                height="20"
                                                                viewBox="0 0 20 20"
                                                                fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    fill-rule="evenodd"
                                                                    clip-rule="evenodd"
                                                                    d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z"
                                                                    fill="" />
                                                            </svg>
                                                        </span>
                                                        <input
                                                            type="text" placeholder="Cari Data Pelanggan..."
                                                            id="cariDataPelanggan" name="keyword"
                                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pr-14 pl-12 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden xl:w-[430px] dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30" />
                                                        <button
                                                            id="cariDataPelanggan" name="cariDataPelanggan"
                                                            class="absolute top-1/2 right-2.5 inline-flex -translate-y-1/2 items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50 px-[7px] py-[4.5px] text-xs -tracking-[0.2px] text-gray-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400">
                                                            <span> Cari </span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div
                                            class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                                            <!-- ====== Table -->
                                            <div
                                                class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                                                <div class="max-w-full overflow-x-auto">
                                                    <?php
                                                    $sql = "SELECT * FROM pelanggan";
                                                    $stmt = $conn->query($sql);
                                                    $stmt->execute();
                                                    $pelanggan = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                    // buat fitur cari
                                                    if (isset($_POST['cariDataPelanggan'])) {
                                                        $keyword = $_POST['keyword'];
                                                        $sql = "SELECT * FROM pelanggan WHERE kode_pelanggan LIKE :keyword OR nama_pelanggan LIKE :keyword OR email LIKE :keyword OR no_hp LIKE :keyword OR alamat LIKE :keyword";
                                                        $stmt = $conn->prepare($sql);
                                                        $stmt->bindValue(':keyword', '%' . $keyword . '%');
                                                        $stmt->execute();
                                                        $pelanggan = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    }
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
                                                                            Kode Pelanggan
                                                                        </p>
                                                                    </div>
                                                                </th>
                                                                <th class="px-5 py-3 sm:px-6">
                                                                    <div class="flex items-center">
                                                                        <p
                                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                            Nama Pelanggan
                                                                        </p>
                                                                    </div>
                                                                </th>
                                                                <th class="px-5 py-3 sm:px-6">
                                                                    <div class="flex items-center">
                                                                        <p
                                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                            Email
                                                                        </p>
                                                                    </div>
                                                                </th>
                                                                <th class="px-5 py-3 sm:px-6">
                                                                    <div class="flex items-center">
                                                                        <p
                                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                            No HP
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
                                                                            Action
                                                                        </p>
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                            <?php foreach ($pelanggan as $index => $pel): ?>
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
                                                                                <?php echo htmlspecialchars($pel['kode_pelanggan']); ?>
                                                                            </p>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <?php echo htmlspecialchars($pel['nama_pelanggan']); ?>
                                                                            </p>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <?php echo htmlspecialchars($pel['email']); ?>
                                                                            </p>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <?php echo htmlspecialchars($pel['no_hp']); ?>
                                                                            </p>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center">
                                                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                                                <?php echo htmlspecialchars($pel['alamat']); ?>
                                                                            </p>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-5 py-4 sm:px-6">
                                                                        <div class="flex items-center">
                                                                            <a href="edit_data_pelanggan.php?id=<?php echo $pel['id_pelanggan']; ?>" class="text-brand-500 hover:text-brand-600 text-theme-sm dark:text-brand-400 dark:hover:text-brand-300">
                                                                                Edit
                                                                            </a>
                                                                            <span class="mx-2 dark:text-gray-400">|</span>

                                                                            <a href="?hapus=<?= $pel['id_pelanggan']; ?>" class="text-error-500 hover:text-error-600 text-theme-sm dark:text-error-400 dark:hover:text-error-300">
                                                                                Delete
                                                                            </a>
                                                                        </div>
                                                                    </td>
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

        if (isset($_GET["hapus_confirmed"])) {
            $id = $_GET["hapus_confirmed"];
            $sql = "DELETE FROM pelanggan WHERE id_pelanggan = :id_pelanggan";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_pelanggan', $id);

            if ($stmt->execute()) {
                echo "<script>
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data berhasil dihapus',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'data_pelanggan.php';
                    });
                    </script>
                    ";
            } else {
                echo "<script>
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Data gagal dihapus',
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    </script>
                    ";
            }
        }
        ?>
        <!-- ===== Page Wrapper End ===== -->
        <script defer src="../src/js/bundle.js"></script>
    </body>

    </html>