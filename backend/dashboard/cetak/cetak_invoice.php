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
        $id_pembayaran = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM pembayaran WHERE id_pembayaran = :id");
        $stmt->bindValue(':id', $id_pembayaran, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $sqlJoin = "SELECT p.*, i.judul_iklan, i.durasi_hari, i.total_harga, pl.nama_pelanggan, pl.kode_pelanggan, l.kode_lokasi, l.nama_lokasi, l.alamat
                    FROM pembayaran AS p
                    JOIN iklan AS i ON p.id_iklan = i.id_iklan
                    JOIN pelanggan AS pl ON i.id_pelanggan = pl.id_pelanggan
                    JOIN lokasi_iklan AS l ON i.id_lokasi = l.id_lokasi
                    WHERE p.id_pembayaran = :id;";
        $stmtJoin = $conn->prepare($sqlJoin);
        $stmtJoin->bindParam(':id', $_GET['id']);
        $stmtJoin->execute();
        $joinList = $stmtJoin->fetch(PDO::FETCH_ASSOC);
    }
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
        $sisaTagihan = "Rp " . number_format($sisaTagihan, 0, ',', '.') . " (Pembayaran kelebihan nanti akan dikembalikan ke pelanggan)";
    } else {
        $sisaTagihan = "Rp " . number_format($sisaTagihan, 0, ',', '.');
    }

    $id_pembayaran = $_GET['id'] ?? 0;
    $stmt = $conn->prepare("SELECT * FROM pembayaran WHERE id_pembayaran = :id_pembayaran");
    $stmt->bindParam(':id_pembayaran', $id_pembayaran);
    $stmt->execute();
    $tagihan = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmtDetail = $conn->prepare("SELECT * FROM detail_pembayaran
                                    WHERE id_pembayaran = :id_pembayaran
                                    AND status_bayar = 'lunas'
                                    ORDER BY tanggal_dibuat_tagihan DESC
                                    ");
    $stmtDetail->bindParam(':id_pembayaran', $id_pembayaran);
    $stmtDetail->execute();
    $detailPembayaran = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="">
        <div class="container mx-auto py-8">
            <h2 class="text-xl font-bold mb-4">INVOICE PEMBAYARAN</h2>
        </div>
        <hr>
        <div class="container mx-auto py-8">
            <table class="text-left w-full border-collapse border border-gray-300">
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Kode Invoice</th>
                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($data['kode_invoice']); ?></td>
                </tr>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Nama Pelanggan</th>
                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($joinList['nama_pelanggan']); ?></td>
                </tr>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Pemesanan Iklan</th>
                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($joinList['kode_lokasi']); ?> | <?php echo htmlspecialchars($joinList['nama_lokasi']); ?></td>
                </tr>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Durasi Tayang Iklan</th>
                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($joinList['durasi_hari']); ?> hari</td>
                </tr>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Total Tagihan</th>
                    <td class="border border-gray-300 px-4 py-2">Rp <?php echo number_format($data['total_tagihan'], 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Sisa Tagihan</th>
                    <td class="border border-gray-300 px-4 py-2"><?php echo $sisaTagihan; ?></td>
                </tr>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Status Pembayaran</th>
                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($data['status_pembayaran']); ?></td>
                </tr>
            </table>
        </div>
        <hr>
        <div class="container mx-auto py-8">
            <h2 class="text-xl font-bold mb-4">INVOICE TAGIHAN</h2>
        </div>
        <div class="container mx-auto">
            <table class="text-left w-full border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">No</th>
                        <th class="border border-gray-300 px-4 py-2">Tanggal Tagihan Dibuat</th>
                        <th class="border border-gray-300 px-4 py-2">Nominal Harga Dibayar</th>
                        <th class="border border-gray-300 px-4 py-2">Metode Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($detailPembayaran as $detail) : ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2"><?php echo $no++; ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo date('d-m-Y', strtotime($detail['tanggal_dibuat_tagihan'])); ?></td>
                            <td class="border border-gray-300 px-4 py-2">Rp <?php echo number_format($detail['nominal_bayar'], 0, ',', '.'); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($detail['metode_pembayaran']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
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