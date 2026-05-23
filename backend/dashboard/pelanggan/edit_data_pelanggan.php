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
            EDIT DATA PELANGGAN
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

        <!-- ===== Page Wrapper Start ===== -->
        <div class="flex h-screen overflow-hidden">

            <!-- ===== Sidebar ===== -->
            <?php include('../partials/sidebar.php') ?>

            <!-- ===== Content Area Start ===== -->
            <div
                class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
                <!-- Small Device Overlay Start -->
                <div
                    @click="sidebarToggle = false"
                    :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
                    class="fixed w-full h-screen z-9 bg-gray-900/50"></div>
                <!-- Small Device Overlay End -->

                <!-- ===== Header ===== -->
                <?php include('../partials/header.php') ?>

                <!-- ===== Main Content Start ===== -->
                <main>
                    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                        <div class="grid grid-cols-12 gap-4 md:gap-6">
                            <div class="col-span-12 space-y-6">
                                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                                    <div class="px-5 py-4 sm:px-6 sm:py-5">
                                        <h3
                                            class="text-base font-medium text-gray-800 dark:text-white/90">
                                            Edit Data Pelanggan
                                        </h3>
                                    </div>

                                    <!-- form -->
                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                    <?php
                                    if (!isset($_GET['id'])) {
                                        die("ID tidak ditemukan!");
                                    }
                                    $sql = "SELECT * FROM pelanggan WHERE id_pelanggan = :id_pelanggan";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':id_pelanggan', $_GET['id']);
                                    $stmt->execute();
                                    $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

                                    $error = "";
                                    if (isset($_POST['ubahData'])) {
                                        $id_pelanggan = htmlspecialchars($_POST['id_pelanggan']);
                                        $kode_pelanggan = htmlspecialchars($_POST['kode_pelanggan']);
                                        $nama_pelanggan = htmlspecialchars($_POST['nama_pelanggan']);
                                        $email = htmlspecialchars($_POST['email']);
                                        $no_hp = htmlspecialchars($_POST['no_hp']);
                                        $alamat = htmlspecialchars($_POST['alamat']);

                                        if (
                                            empty($kode_pelanggan) || empty($nama_pelanggan) || empty($email) || empty($no_hp) || empty($alamat)
                                        ) {
                                            $error = "Semua field wajib diisi!";
                                        } elseif (!is_numeric($kode_pelanggan)) {
                                            $error = "NIK hanya boleh berisi angka";
                                        } elseif (intval($kode_pelanggan) < 0) {
                                            $error = "NIK tidak boleh negatif";
                                        } elseif (strlen($kode_pelanggan) > 16) {
                                            $error = "NIK tidak boleh melebihi 16 karakter";
                                        } elseif (!is_numeric($no_hp)) {
                                            $error = "Nomor Telepon hanya boleh berisi angka";
                                        } elseif (strlen($no_hp) < 12) {
                                            $error = "Nomor Telepon minimal 12 digit";
                                        } elseif (strlen($no_hp) > 13) {
                                            $error = "Nomor Telepon tidak boleh melebihi 13 digit";
                                        } else {

                                            $sql = "UPDATE pelanggan SET kode_pelanggan = :kode_pelanggan, nama_pelanggan = :nama_pelanggan, email = :email, no_hp = :no_hp, alamat = :alamat WHERE id_pelanggan = :id_pelanggan";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':kode_pelanggan', $kode_pelanggan);
                                            $stmt->bindParam(':nama_pelanggan', $nama_pelanggan);
                                            $stmt->bindParam(':email', $email);
                                            $stmt->bindParam(':no_hp', $no_hp);
                                            $stmt->bindParam(':alamat', $alamat);
                                            $stmt->bindParam(':id_pelanggan', $_GET['id']);

                                            // $stmt->execute([$kode_pelanggan, $nama_pelanggan, $email, $no_hp, $alamat]);

                                            if ($stmt->execute()) {
                                                echo "<script>
                                                        Swal.fire({
                                                            icon: 'success',
                                                            title: 'Data berhasil diubah!',
                                                            showConfirmButton: false,
                                                            timer: 1500
                                                        }).then(() => {
                                                            window.location.href = 'data_pelanggan.php';
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
                                    ?>
                                    <?php if ($error != "") : ?>
                                        <div
                                            class="rounded-xl border border-error-500 bg-error-50 p-4 dark:border-error-500/30 dark:bg-error-500/15">
                                            <div class="flex items-start gap-3">
                                                <div class="-mt-0.5 text-error-500">
                                                    <svg
                                                        class="fill-current"
                                                        width="24"
                                                        height="24"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            fill-rule="evenodd"
                                                            clip-rule="evenodd"
                                                            d="M20.3499 12.0004C20.3499 16.612 16.6115 20.3504 11.9999 20.3504C7.38832 20.3504 3.6499 16.612 3.6499 12.0004C3.6499 7.38881 7.38833 3.65039 11.9999 3.65039C16.6115 3.65039 20.3499 7.38881 20.3499 12.0004ZM11.9999 22.1504C17.6056 22.1504 22.1499 17.6061 22.1499 12.0004C22.1499 6.3947 17.6056 1.85039 11.9999 1.85039C6.39421 1.85039 1.8499 6.3947 1.8499 12.0004C1.8499 17.6061 6.39421 22.1504 11.9999 22.1504ZM13.0008 16.4753C13.0008 15.923 12.5531 15.4753 12.0008 15.4753L11.9998 15.4753C11.4475 15.4753 10.9998 15.923 10.9998 16.4753C10.9998 17.0276 11.4475 17.4753 11.9998 17.4753L12.0008 17.4753C12.5531 17.4753 13.0008 17.0276 13.0008 16.4753ZM11.9998 6.62898C12.414 6.62898 12.7498 6.96476 12.7498 7.37898L12.7498 13.0555C12.7498 13.4697 12.414 13.8055 11.9998 13.8055C11.5856 13.8055 11.2498 13.4697 11.2498 13.0555L11.2498 7.37898C11.2498 6.96476 11.5856 6.62898 11.9998 6.62898Z"
                                                            fill="#F04438" />
                                                    </svg>
                                                </div>

                                                <div>
                                                    <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                                                        Error Message
                                                    </h4>

                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        <?= $error ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <form action="edit_data_pelanggan.php?id=<?php echo $pelanggan['id_pelanggan'] ?>" name="ubahData" id="submit" method="POST">
                                        <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
                                            <div>
                                                <label for="kode_pelanggan"
                                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                    Kode Pelanggan
                                                </label>
                                                <input type="text" name="id_pelanggan" id="id_pelanggan" value="<?php echo $pelanggan['id_pelanggan']; ?>" hidden>
                                                <input
                                                    type="number" name="kode_pelanggan" id="kode_pelanggan" value="<?php echo $pelanggan['kode_pelanggan']; ?>"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="NIK | maksimal 16 karakter" />
                                            </div>
                                            <div>
                                                <label for="nama_pelanggan"
                                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                    Nama Pelanggan
                                                </label>
                                                <input
                                                    type="text" name="nama_pelanggan" id="nama_pelanggan" value="<?php echo $pelanggan['nama_pelanggan']; ?>"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Nama Pelanggan" />
                                            </div>
                                            <div>
                                                <label for="email"
                                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                    Email Pelanggan
                                                </label>
                                                <input
                                                    type="email" name="email" id="email" value="<?php echo $pelanggan['email']; ?>"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Email" />
                                            </div>
                                            <div>
                                                <label for="no_hp"
                                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                    Nomor Handphone
                                                </label>
                                                <input
                                                    type="text" name="no_hp" id="no_hp" value="<?php echo $pelanggan['no_hp']; ?>"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="No HP | max 13 Digit" />
                                            </div>
                                            <div>
                                                <label for="alamat"
                                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                    Alamat
                                                </label>
                                                <input
                                                    type="text" name="alamat" id="alamat" value="<?php echo $pelanggan['alamat']; ?>"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Alamat" />
                                            </div>
                                            <div class="flex gap-x-6">
                                                <button type="submit" name="ubahData" id="btnEdit" class="bg-brand-500 hover:bg-brand-600 rounded-lg p-3 text-sm font-medium text-white transition-colors"> Ubah </button>
                                                <a href="data_pelanggan.php" class="bg-gray-500 hover:bg-gray-600 rounded-lg p-3 text-sm font-medium text-white transition-colors"> Batal </a>
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