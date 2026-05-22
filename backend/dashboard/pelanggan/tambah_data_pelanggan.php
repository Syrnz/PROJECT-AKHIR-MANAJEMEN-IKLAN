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
            Dashboard
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
                                            Tambah Data Pelanggan
                                        </h3>
                                    </div>
                                    <!-- form -->
                                    <form action="tambah_data_pelanggan.php" name="tambahData" id="submit" method="POST">
                                        <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
                                            <div>
                                                <label for="kode_pelanggan"
                                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                    Kode Pelanggan
                                                </label>
                                                <input
                                                    type="number" name="kode_pelanggan" id="kode_pelanggan"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="NIK" />
                                            </div>
                                            <div>
                                                <label for="nama_pelanggan"
                                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                    Nama Pelanggan
                                                </label>
                                                <input
                                                    type="text" name="nama_pelanggan" id="nama_pelanggan"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Nama Pelanggan" />
                                            </div>
                                            <div>
                                                <label for="email"
                                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                    Email Pelanggan
                                                </label>
                                                <input
                                                    type="email" name="email" id="email"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Email" />
                                            </div>
                                            <div>
                                                <label for="no_hp"
                                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                    Nomor Handphone
                                                </label>
                                                <input
                                                    type="text" name="no_hp" id="no_hp"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="No HP" />
                                            </div>
                                            <div>
                                                <label for="alamat"
                                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                    Alamat
                                                </label>
                                                <input
                                                    type="text" name="alamat" id="alamat"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Alamat" />
                                            </div>
                                            <div>
                                                <button type="submit" name="tambahData" id="btnTambah" class="bg-brand-500 hover:bg-brand-600 rounded-lg p-3 text-sm font-medium text-white transition-colors"> Submit </button>
                                            </div>
                                        </div>
                                    </form>
                                    <?php
                                    if (isset($_POST['tambahData'])) {
                                        $kode_pelanggan = htmlspecialchars($_POST['kode_pelanggan']);
                                        $nama_pelanggan = htmlspecialchars($_POST['nama_pelanggan']);
                                        $email = htmlspecialchars($_POST['email']);
                                        $no_hp = htmlspecialchars($_POST['no_hp']);
                                        $alamat = htmlspecialchars($_POST['alamat']);

                                        $sql = "INSERT INTO pelanggan (id_pelanggan, kode_pelanggan, nama_pelanggan, email, no_hp, alamat, created_at)
                                                    VALUES (NULL, :kode_pelanggan, :nama_pelanggan, :email, :no_hp, :alamat, CURRENT_TIMESTAMP)";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bindParam(':kode_pelanggan', $kode_pelanggan);
                                        $stmt->bindParam(':nama_pelanggan', $nama_pelanggan);
                                        $stmt->bindParam(':email', $email);
                                        $stmt->bindParam(':no_hp', $no_hp);
                                        $stmt->bindParam(':alamat', $alamat);

                                        // $stmt->execute([$kode_pelanggan, $nama_pelanggan, $email, $no_hp, $alamat]);

                                        if ($stmt->execute()) {
                                            echo "<script> Swal.fire({
                                                    position: 'top-end',
                                                    icon: 'success',
                                                    title: 'Your work has been saved',
                                                    showConfirmButton: false,
                                                    timer: 1500
                                                });</script>";
                                        } else {
                                            echo "<script>alert('Data gagal ditambahkan!');</script>";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>


                        </div>
                    </div>
                </main>
            </div>
        </div>
        <script>
            document.getElementById('submit').addEventListener('submit', function(e) {
                var form = this;
                e.preventDefault(); // Stop the form from submitting immediately

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Manually submit the form if confirmed
                    }
                });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script defer src="../src/js/bundle.js"></script>
    </body>

    </html>