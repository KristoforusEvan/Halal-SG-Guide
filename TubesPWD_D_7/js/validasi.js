// ============================================================
// validasi.js - validasi form di sisi browser
// ============================================================

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
    if (nilai('area_singapura').trim() === '') {
        daftarError.push('Area Singapura wajib diisi.');
    }
    if (nilai('keterangan').trim() === '') {
        daftarError.push('Keterangan jadwal wajib diisi.');
    }
    return daftarError;
}