CREATE DATABASE IF NOT EXISTS db_wisata_halal_sg;
USE db_wisata_halal_sg;

-- Akun pengguna: untuk daftar dan login cukup nama, email, password.
-- username dan asal_kota opsional (diisi nanti lewat halaman profil).
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    username VARCHAR(50) NULL,
    asal_kota VARCHAR(100) NULL
);

-- CRUD 1: rencana kuliner halal milik tiap user
CREATE TABLE rencana_makanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_tempat VARCHAR(100) NOT NULL,
    lokasi_mrt VARCHAR(100) NOT NULL,
    status_halal VARCHAR(50) NOT NULL,
    catatan VARCHAR(255) NOT NULL
);

-- CRUD 2: jadwal kunjungan masjid milik tiap user
CREATE TABLE jadwal_masjid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_masjid VARCHAR(100) NOT NULL,
    area_singapura VARCHAR(100) NOT NULL,
    waktu_sholat VARCHAR(50) NOT NULL,
    keterangan VARCHAR(255) NOT NULL
);