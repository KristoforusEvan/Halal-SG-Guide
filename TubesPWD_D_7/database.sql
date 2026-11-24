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