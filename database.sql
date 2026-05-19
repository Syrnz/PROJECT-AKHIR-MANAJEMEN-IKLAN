CREATE DATABASE db_iklan;

USE db_iklan;

CREATE TABLE iklan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pengiklan VARCHAR(100),
    nama_iklan VARCHAR(100),
    harga DECIMAL(10,2),
    tanggal_mulai DATE,
    tanggal_selesai DATE,
    status ENUM('Belum Tayang','Aktif','Selesai'),
    pembayaran ENUM('Pending','Lunas')
);

INSERT INTO iklan 
(nama_pengiklan, nama_iklan, harga, tanggal_mulai, tanggal_selesai, status, pembayaran)
VALUES
('PT Maju Jaya', 'Iklan Produk A', 500000, '2026-05-01', '2026-05-10', 'Aktif', 'Lunas'),
('CV Media', 'Promo Event', 300000, '2026-04-01', '2026-04-05', 'Selesai', 'Lunas'),
('Toko Berkah', 'Banner Diskon', 200000, '2026-06-01', '2026-06-07', 'Belum Tayang', 'Pending');