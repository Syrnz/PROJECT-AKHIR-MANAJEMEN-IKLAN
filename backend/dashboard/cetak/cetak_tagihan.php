<?php
include('../../middleware/check_login.php');
include_once('../../../database/koneksi_db.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Invoice</title>
    <script src="https://cloudflare.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body>
    <?php

    if (isset($_GET['id'])) {
        $id_detail = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM detail_pembayaran WHERE id_detail = :id");
        $stmt->bindValue(':id', $id_detail, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $id_pembayaran = $data['id_pembayaran'];

        $sqlJoin = "SELECT p.*, i.judul_iklan, i.durasi_hari, i.total_harga, pl.nama_pelanggan, pl.kode_pelanggan, pl.email, l.kode_lokasi, l.nama_lokasi, l.alamat
                    FROM pembayaran AS p
                    JOIN iklan AS i ON p.id_iklan = i.id_iklan
                    JOIN pelanggan AS pl ON i.id_pelanggan = pl.id_pelanggan
                    JOIN lokasi_iklan AS l ON i.id_lokasi = l.id_lokasi
                    WHERE p.id_pembayaran = :id;";
        $stmtJoin = $conn->prepare($sqlJoin);
        $stmtJoin->bindParam(':id', $id_pembayaran);
        $stmtJoin->execute();
        $joinList = $stmtJoin->fetch(PDO::FETCH_ASSOC);

        $namaBank = 'Bank KBS';
        $nomorRekening = '1234567890';
        $namaPemilikRekening = 'PT. Iklankan';
    }
    ?>
    <?php
    include "../phpqrcode/qrlib.php";
    $penyimpanan = "temp/";
    if (!file_exists($penyimpanan))
        mkdir($penyimpanan);
    $isi = 'https://github.com/Syrnz/PROJECT-AKHIR-MANAJEMEN-IKLAN.git';
    QRcode::png($isi, $penyimpanan . $joinList['kode_invoice'] . ".png");
    ?>
    <div class="">
        <div class="container mx-auto py-8">
            <h2 class="text-xl font-bold mb-4">INVOICE TAGIHAN | <?php echo htmlspecialchars($joinList['kode_invoice']); ?></h2>
            <h4>PT. Iklankan</h4>
            <p>iklankan@gmail.com</p>
            <p>perum gili asri residence blok C 10 no. 6 gili timur, kamal, bangkalan</p>
        </div>
        <hr>
        <div class="container mx-auto py-8">
            <table class="text-left">
                <tr>
                    <th class=" px-4 py-2">DITAGIHKAN KEPADA</th>
                </tr>
                <tr>
                    <td class="px-4 py-2"><?php echo htmlspecialchars($joinList['nama_pelanggan']); ?></td>
                </tr>
                <tr>
                    <td class="px-4 py-2"><?php echo htmlspecialchars($joinList['kode_pelanggan']); ?></td>
                </tr>
                <tr>
                    <td class="px-4 py-2"><?php echo htmlspecialchars($joinList['email']); ?></td>
                </tr>
            </table>
            <hr>
            <table class="text-left">
                <tr>
                    <th class=" px-4 py-2">Tagihan Iklan</th>
                    <td class="px-4 py-2"><?php echo htmlspecialchars($joinList['kode_lokasi']); ?> | <?php echo htmlspecialchars($joinList['nama_lokasi']); ?> </td>
                </tr>
                <tr>
                    <th class="px-4 py-2">Nominal Pembayaran</th>
                    <td class="px-4 py-2">Rp <?php echo number_format($data['nominal_bayar'], 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-2">Metode Pembayaran</th>
                    <td class="px-4 py-2"><?php echo htmlspecialchars($data['metode_pembayaran']); ?></td>
                </tr>
            </table>
            <hr>
            <table class="text-left">
                <tr>
                    <th class="px-4 py-2">HARAP DIBAYAR MELALUI</th>
                </tr>
                <?php if ($data['metode_pembayaran'] === 'transfer bank' || $data['metode_pembayaran'] === 'e-wallet'): ?>
                    <tr>
                        <th class="px-4 py-2">Nama Bank</th>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($namaBank); ?></td>
                    </tr>
                    <tr>
                        <th class="px-4 py-2">Nomor Rekening</th>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($nomorRekening); ?></td>
                    </tr>
                    <tr>
                        <th class="px-4 py-2">Nama Pemilik Rekening</th>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($namaPemilikRekening); ?></td>
                    </tr>
                <?php elseif ($data['metode_pembayaran'] === 'qris'): ?>
                    <tr>
                        <td class="px-4 py-2">
                            <img src="<?php echo $penyimpanan . $joinList['kode_invoice'] . '.png'; ?>" alt="QR Code Pembayaran">
                        </td>
                    </tr>
                <?php elseif ($data['metode_pembayaran'] === 'cash'): ?>
                    <tr>
                        <td class="px-4 py-2">
                            <p>Silakan bayar secara tunai ke kasir kami di kantor PT. Iklankan</p>
                            <p>Alamat: perum gili asri residence blok C 10 no. 6 gili timur, kamal, bangkalan</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>