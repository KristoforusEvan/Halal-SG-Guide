var POLA_NAMA = /^[a-zA-Z ]+$/;
var POLA_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
var POLA_USERNAME = /^[a-zA-Z0-9_]{3,30}$/;
var MIN_PASSWORD = 8;

function nilai(id) {
    var el = document.getElementById(id);
    return el ? el.value : '';
}

document.addEventListener('DOMContentLoaded', function () {
    var kolom = document.querySelectorAll('input, select, textarea');
    for (var i = 0; i < kolom.length; i++) {
        kolom[i].addEventListener('input', function () {
            tampilPesan('');
        });
    }
});

function fn_ValRegister() {
    var daftarError = [];
    var username = nilai('username').trim();
    var email = nilai('email').trim().toLowerCase();
    var password = nilai('password');

    if (username === '') {
        daftarError.push('Username wajib diisi.');
    } else if (!POLA_USERNAME.test(username)) {
        daftarError.push('Username 3-30 karakter, hanya huruf, angka, dan underscore (_).');
    }

    if (email === '') {
        daftarError.push('Email wajib diisi.');
    } else if (!POLA_EMAIL.test(email)) {
        daftarError.push('Format email tidak valid (contoh: nama@gmail.com).');
    }

    if (password === '') {
        daftarError.push('Password wajib diisi.');
    } else if (password.length < MIN_PASSWORD) {
        daftarError.push('Password minimal ' + MIN_PASSWORD + ' karakter.');
    }
    return daftarError;
}

function fn_ValLogin() {
    var daftarError = [];
    var email = nilai('email').trim().toLowerCase();
    var password = nilai('password');

    if (email === '') {
        daftarError.push('Email belum diisi.');
    } else if (!POLA_EMAIL.test(email)) {
        daftarError.push('Format email tidak valid (contoh: nama@gmail.com).');
    }

    if (password === '') {
        daftarError.push('Password belum diisi.');
    }
    return daftarError;
}

function fn_ValProfil() {
    var daftarError = [];
    var username = nilai('username').trim();
    var email = nilai('email').trim().toLowerCase();
    var nama = nilai('nama').trim();

    if (username === '') {
        daftarError.push('Username wajib diisi.');
    } else if (!POLA_USERNAME.test(username)) {
        daftarError.push('Username 3-30 karakter, hanya huruf, angka, dan underscore (_).');
    }

    if (email === '') {
        daftarError.push('Email wajib diisi.');
    } else if (!POLA_EMAIL.test(email)) {
        daftarError.push('Format email tidak valid (contoh: nama@gmail.com).');
    }

    if (nama !== '' && !POLA_NAMA.test(nama)) {
        daftarError.push('Nama lengkap hanya boleh berisi huruf dan spasi.');
    }
    return daftarError;
}

function fn_ValMakanan() {
    var daftarError = [];
    if (nilai('nama_tempat').trim() === '') {
        daftarError.push('Nama restoran/kedai wajib diisi.');
    }
    if (nilai('catatan').trim() === '') {
        daftarError.push('Catatan menu/sumber verifikasi wajib diisi.');
    }
    return daftarError;
}

function fn_ValMasjid() {
    var daftarError = [];
    if (nilai('nama_masjid').trim() === '') {
        daftarError.push('Nama masjid wajib diisi.');
    }
    return daftarError;
}

function fn_ValKonten() {
    var daftarError = [];
    var nama = nilai('nama').trim();
    var area = nilai('area').trim();
    var harga = nilai('harga_estimasi').trim();
    var lat = nilai('lat').trim();
    var lng = nilai('lng').trim();
    var status = nilai('status').trim();
    var jamBuka = nilai('jam_buka').trim();
    var alamat = nilai('alamat').trim();
    var keterangan = nilai('keterangan').trim();
    var deskripsi = nilai('deskripsi').trim();

    if (nama === '') {
        daftarError.push('Nama tempat wajib diisi.');
    }
    if (area === '') {
        daftarError.push('Area / kawasan Singapura wajib diisi.');
    }
    if (harga === '') {
        daftarError.push('Estimasi biaya wajib diisi (tulis "Gratis" jika tidak berbayar).');
    }
    if (lat !== '' && isNaN(Number(lat))) {
        daftarError.push('Koordinat Latitude harus berupa angka desimal (contoh: 1.3023).');
    }
    if (lng !== '' && isNaN(Number(lng))) {
        daftarError.push('Koordinat Longitude harus berupa angka desimal (contoh: 103.8589).');
    }
    if (status === '') {
        daftarError.push('Badge status wajib diisi.');
    }
    if (jamBuka === '') {
        daftarError.push('Jam operasional wajib diisi.');
    }
    if (alamat === '') {
        daftarError.push('Alamat lengkap wajib diisi.');
    }
    if (keterangan === '') {
        daftarError.push('Ringkasan singkat wajib diisi.');
    }
    if (deskripsi === '') {
        daftarError.push('Deskripsi lengkap wajib diisi.');
    }

    var tipeValid = ['image/jpeg', 'image/png', 'image/webp'];
    var batasUkuran = 2 * 1024 * 1024;

    var inputFoto = document.getElementById('foto_file');
    if (inputFoto && inputFoto.files && inputFoto.files[0]) {
        var fileUtama = inputFoto.files[0];
        if (tipeValid.indexOf(fileUtama.type) === -1) {
            daftarError.push('Foto utama harus berformat JPG, PNG, atau WEBP.');
        } else if (fileUtama.size > batasUkuran) {
            daftarError.push('Ukuran foto utama maksimal 2 MB.');
        }
    }

    var inputGaleri = document.getElementById('foto_galeri');
    if (inputGaleri && inputGaleri.files) {
        for (var i = 0; i < inputGaleri.files.length; i++) {
            var fileSlide = inputGaleri.files[i];
            if (tipeValid.indexOf(fileSlide.type) === -1) {
                daftarError.push('Semua foto galeri harus berformat JPG, PNG, atau WEBP.');
                break;
            } else if (fileSlide.size > batasUkuran) {
                daftarError.push('Ukuran setiap foto galeri maksimal 2 MB.');
                break;
            }
        }
    }

    return daftarError;
}