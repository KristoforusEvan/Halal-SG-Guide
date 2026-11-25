CREATE DATABASE db_wisata_halal_sg;
USE db_wisata_halal_sg;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    asal_kota VARCHAR(100) NOT NULL
);

CREATE TABLE rencana_makanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_tempat VARCHAR(100) NOT NULL,
    lokasi_mrt VARCHAR(100) NOT NULL,
    status_halal VARCHAR(50) NOT NULL,
    catatan VARCHAR(255) NOT NULL
);

CREATE TABLE jadwal_masjid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_masjid VARCHAR(100) NOT NULL,
    area_singapura VARCHAR(100) NOT NULL,
    waktu_sholat VARCHAR(50) NOT NULL,
    keterangan VARCHAR(255) NOT NULL
);


-- Penambahan Epun 

-- Tabel data publik untuk homepage (kuliner, masjid, wisata)
CREATE TABLE tempat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kategori ENUM('kuliner','masjid','wisata') NOT NULL,
    area_mrt VARCHAR(100) NOT NULL,
    keterangan VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL
);

-- Data awal (diambil dari tabel statis di index.php lama)
INSERT INTO tempat (nama, kategori, area_mrt, keterangan, status) VALUES
('Zam Zam Restaurant', 'kuliner', 'Bugis MRT (North Bridge Rd)', 'Murtabak Rusa & Nasi Briyani', 'Sertifikat Halal MUIS'),
('Hajah Maimunah', 'kuliner', 'Bugis / Jalan Pisang', 'Nasi Padang & Tahu Telur', 'Sertifikat Halal MUIS'),
('Encik Tan', 'kuliner', 'Orchard / Bugis Junction', 'Laksa & Hainanese Chicken Rice', 'Sertifikat Halal MUIS'),
('Masjid Sultan', 'masjid', 'Bugis MRT (EW12 / DT14)', 'Ruang sholat luas, tempat wudhu nyaman', 'Masjid'),
('Masjid Al-Falah', 'masjid', 'Somerset MRT (NS23)', 'Akses strategis di pusat belanja Orchard', 'Masjid'),
('Masjid Jamae (Chulia)', 'masjid', 'Chinatown MRT (NE4 / DT19)', 'Masjid bersejarah di tengah Chinatown', 'Masjid');