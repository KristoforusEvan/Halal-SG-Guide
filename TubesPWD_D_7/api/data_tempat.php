<?php
// Daftar tempat untuk homepage (dibaca oleh api/tempat.php).
// Mau tambah konten? Salin satu blok array(...) di bawah, tempel di bawahnya, lalu ganti isinya.
// kategori hanya boleh: 'kuliner', 'masjid', atau 'wisata'
// foto = NAMA FILE di folder img/ (kosongkan '' kalau belum ada foto)
// Jangan lupa tanda koma di akhir tiap blok. Kalau teks ada tanda petik ('), tulis \'
$daftarTempat = array(
    array(
        'nama'       => 'Zam Zam Restaurant',
        'kategori'   => 'kuliner',
        'area_mrt'   => 'Bugis MRT (North Bridge Rd)',
        'keterangan' => 'Murtabak Rusa & Nasi Briyani',
        'status'     => 'Sertifikat Halal MUIS',
        'foto'       => 'zam-zam.jpg'
    ),
    array(
        'nama'       => 'Hajah Maimunah',
        'kategori'   => 'kuliner',
        'area_mrt'   => 'Bugis / Jalan Pisang',
        'keterangan' => 'Nasi Padang & Tahu Telur',
        'status'     => 'Sertifikat Halal MUIS',
        'foto'       => 'hajah-maimunah.jpg'
    ),
    array(
        'nama'       => 'Encik Tan',
        'kategori'   => 'kuliner',
        'area_mrt'   => 'Orchard / Bugis Junction',
        'keterangan' => 'Laksa & Hainanese Chicken Rice',
        'status'     => 'Sertifikat Halal MUIS',
        'foto'       => 'encik-tan.jpg'
    ),
    array(
        'nama'       => 'Masjid Sultan',
        'kategori'   => 'masjid',
        'area_mrt'   => 'Bugis MRT (EW12 / DT14)',
        'keterangan' => 'Ruang sholat luas, tempat wudhu nyaman',
        'status'     => 'Masjid',
        'foto'       => 'masjid-sultan.jpg'
    ),
    array(
        'nama'       => 'Masjid Al-Falah',
        'kategori'   => 'masjid',
        'area_mrt'   => 'Somerset MRT (NS23)',
        'keterangan' => 'Akses strategis di pusat belanja Orchard',
        'status'     => 'Masjid',
        'foto'       => 'masjid-alfalah.jpg'
    ),
    array(
        'nama'       => 'Masjid Jamae (Chulia)',
        'kategori'   => 'masjid',
        'area_mrt'   => 'Chinatown MRT (NE4 / DT19)',
        'keterangan' => 'Masjid bersejarah di tengah Chinatown',
        'status'     => 'Masjid',
        'foto'       => 'masjid-jamae.jpg'
    )
);
