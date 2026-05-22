# PROJECT-AKHIR-MANAJEMEN-IKLAN

SQL
CREATE TABLE admins (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50),
email VARCHAR(100),
password VARCHAR(255),
role ENUM('super','staff') DEFAULT 'staff'
);

CREATE TABLE pelanggan (
id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
kode_pelanggan CHAR(16) UNIQUE NOT NULL,
nama_pelanggan VARCHAR(100) NOT NULL,
email VARCHAR(100) NOT NULL,
no_hp CHAR(13) NOT NULL,
alamat TEXT NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE TABLE iklan (
id_iklan INT AUTO_INCREMENT PRIMARY KEY,
id_pelanggan INT NOT NULL,
judul_iklan VARCHAR(150) NOT NULL,
jenis_iklan ENUM('banner', 'billboard', 'videotron') NOT NULL,
deskripsi TEXT,
file_iklan VARCHAR(255),
tanggal_mulai DATE NOT NULL,
tanggal_selesai DATE NOT NULL,
durasi_hari INT,
harga DECIMAL(12,2) NOT NULL,
status_iklan ENUM('belum_tayang', 'aktif', 'selesai') DEFAULT 'belum_tayang',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_pelanggan)
    REFERENCES pelanggan(id_pelanggan)
    ON DELETE RESTRICT

);
