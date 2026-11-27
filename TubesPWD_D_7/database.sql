DROP DATABASE IF EXISTS db_wisata_halal_sg;
CREATE DATABASE db_wisata_halal_sg;
USE db_wisata_halal_sg;

-- 1. Tabel Users dengan pembeda role ('admin' dan 'user')
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NULL,
    asal_kota VARCHAR(100) NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
);

-- Akun Admin bawaan (Email: admin@halalsg.com | Password: password)
INSERT INTO users (username, email, password, nama, asal_kota, role) VALUES
('admin_halalsg', 'admin@halalsg.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Halal SG', 'Singapura', 'admin');

-- 2. Tabel Konten Tempat (Dikelola oleh Admin, tampil di Homepage)
CREATE TABLE tempat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kategori ENUM('wisata', 'kuliner', 'masjid') NOT NULL,
    area VARCHAR(100) NOT NULL,
    harga_estimasi VARCHAR(50) NOT NULL DEFAULT 'Gratis',
    keterangan VARCHAR(150) NOT NULL,
    deskripsi TEXT NOT NULL,
    alamat VARCHAR(200) NOT NULL,
    jam_buka VARCHAR(100) NOT NULL,
    status VARCHAR(60) NOT NULL,
    foto VARCHAR(100) NOT NULL DEFAULT 'bg-singapore.jpg'
);

-- Data awal katalog (Destinasi Wisata, Kuliner Halal, dan Masjid)
INSERT INTO tempat (nama, kategori, area, harga_estimasi, keterangan, deskripsi, alamat, jam_buka, status, foto) VALUES
('Zam Zam Restaurant', 'kuliner', 'Kampong Glam / Bugis', 'SGD 8 - 18', 'Murtabak Rusa & Nasi Briyani', 'Restoran legendaris di kawasan Kampong Glam yang berdiri sejak 1908 dan terkenal dengan hidangan Murtabak, Nasi Briyani, serta Teh Tarik khas India-Muslim.', '697-699 North Bridge Rd, Singapore 198675', 'Setiap hari, 07.00 - 23.00 SGT', 'Sertifikat Halal MUIS', 'zam-zam.jpg'),
('Hajah Maimunah', 'kuliner', 'Kampong Glam / Jalan Pisang', 'SGD 10 - 20', 'Nasi Padang & Tahu Telur', 'Rumah makan Nasi Padang dan masakan Melayu tradisional peraih penghargaan Michelin Bib Gourmand dengan puluhan pilihan lauk autentik.', '11 & 15 Jalan Pisang, Singapore 199078', 'Senin - Sabtu, 07.00 - 20.00 SGT', 'Sertifikat Halal MUIS', 'hajah-maimunah.jpg'),
('Encik Tan', 'kuliner', 'Orchard / Bugis', 'SGD 5 - 10', 'Laksa & Hainanese Chicken Rice', 'Jaringan kuliner lokal Singapura yang menyajikan hidangan klasik Tionghoa-Singapura versi bersertifikat halal MUIS.', '200 Victoria Street, #B1-K11 Bugis Junction', 'Setiap hari, 10.00 - 22.00 SGT', 'Sertifikat Halal MUIS', 'encik-tan.jpg'),
('Masjid Sultan', 'masjid', 'Kampong Glam / Bugis', 'Gratis', 'Ruang sholat luas, tempat wudhu nyaman', 'Masjid ikonik berkubah emas sekaligus monumen nasional di jantung Kampong Glam dengan aula sholat utama berkapasitas besar.', '3 Muscat Street, Singapore 198833', 'Setiap hari, 05.00 - 22.00 SGT', 'Masjid', 'masjid-sultan.jpg'),
('Masjid Al-Falah', 'masjid', 'Orchard Road', 'Gratis', 'Akses strategis di pusat belanja Orchard', 'Masjid modern yang terletak tepat di kawasan perbelanjaan Orchard Road (Bideford Road) untuk kemudahan sholat wisatawan.', '22 Bideford Road, #01-01 Cairnhill Place', 'Setiap hari, 05.30 - 21.30 SGT', 'Masjid', 'masjid-alfalah.jpg'),
('Masjid Jamae (Chulia)', 'masjid', 'Chinatown', 'Gratis', 'Masjid bersejarah di tengah Chinatown', 'Salah satu masjid tertua di Singapura (dibangun tahun 1826) dengan arsitektur eklektik unik di tengah kawasan Chinatown.', '218 South Bridge Road, Singapore 058767', 'Setiap hari, 05.30 - 21.30 SGT', 'Masjid', 'masjid-jamae.jpg'),
('Merlion Park & Marina Bay', 'wisata', 'Marina Bay', 'Gratis', 'Ikon utama wisata Singapura ramah Muslim', 'Kawasan ikonik tepi teluk dengan patung Merlion dan panorama Marina Bay Sands yang ramah wisatawan Muslim.', '1 Fullerton Rd, Singapore 049213', '24 Jam', 'Destinasi Wisata', 'bg-singapore.jpg');

-- 3. CRUD Itinerary Kuliner (Pribadi milik User - tanpa kolom MRT)
CREATE TABLE rencana_makanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_tempat VARCHAR(100) NOT NULL,
    status_halal VARCHAR(50) NOT NULL,
    catatan VARCHAR(255) NOT NULL,
    CONSTRAINT fk_makanan_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 4. CRUD Itinerary Masjid (Pribadi milik User)
CREATE TABLE jadwal_masjid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_masjid VARCHAR(100) NOT NULL,
    area_singapura VARCHAR(100) NOT NULL,
    waktu_sholat VARCHAR(50) NOT NULL,
    keterangan VARCHAR(255) NOT NULL,
    CONSTRAINT fk_masjid_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);