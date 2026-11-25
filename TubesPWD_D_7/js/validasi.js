// Hapus pesan error JS saat pengguna mulai mengetik ulang
function hapusPesanError() {
    var wadahError = document.getElementById("pesanError");
    if (wadahError) {
        wadahError.innerHTML = "";
    }
}

// Pasang listener "input" ke field yang ada di halaman
document.addEventListener("DOMContentLoaded", function () {
    var daftarInput = ["nama", "username", "email", "password", "asal_kota"];
    for (var i = 0; i < daftarInput.length; i++) {
        var elemen = document.getElementById(daftarInput[i]);
        if (elemen) {
            elemen.addEventListener("input", hapusPesanError);
        }
    }
});

// Validasi form register (disamakan dengan aturan di register.php)
function fn_ValRegister() {
    var daftarError = [];
    var nama = document.getElementById("nama").value.trim();
    var username = document.getElementById("username").value.trim();
    var email = document.getElementById("email").value.trim();
    var password = document.getElementById("password").value;
    var asalKota = document.getElementById("asal_kota").value.trim();
    var wadahError = document.getElementById("pesanError");

    // Nama: hanya huruf dan spasi (sama persis dengan preg_match di PHP)
    var polaNama = /^[a-zA-Z ]+$/;
    // Email: huruf kecil dan format valid
    var polaEmail = /^[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9]@[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9][\.][a-z0-9]{2,4}$/;

    if (nama === "") {
        daftarError.push("Nama lengkap wajib diisi.");
    } else if (!polaNama.test(nama)) {
        daftarError.push("Nama lengkap hanya boleh berisi huruf dan spasi.");
    }

    if (username === "") {
        daftarError.push("Username wajib diisi.");
    }

    if (email === "") {
        daftarError.push("Email wajib diisi.");
    } else if (!polaEmail.test(email)) {
        daftarError.push("Format email harus huruf kecil dan valid (contoh: nama@gmail.com).");
    }

    if (password === "") {
        daftarError.push("Password wajib diisi.");
    } else if (password.length < 6) {
        daftarError.push("Password minimal 6 karakter.");
    }

    if (asalKota === "") {
        daftarError.push("Asal kota wajib diisi.");
    }

    // Kalau ada error, tampilkan dan batalkan pengiriman form
    if (daftarError.length > 0) {
        wadahError.innerHTML = "<p class=\"error\">" + daftarError.join("<br />") + "</p>";
        return false;
    }

    wadahError.innerHTML = "";
    return true;
}

// Validasi form login: pakai username (bukan email), sesuai form login.php
function fn_ValLogin() {
    var daftarError = [];
    var username = document.getElementById("username").value.trim();
    var password = document.getElementById("password").value;
    var wadahError = document.getElementById("pesanError");

    if (username === "") {
        daftarError.push("Username belum diisi.");
    }
    if (password === "") {
        daftarError.push("Password belum diisi.");
    }

    if (daftarError.length > 0) {
        wadahError.innerHTML = "<p class=\"error\">" + daftarError.join("<br />") + "</p>";
        return false;
    }

    wadahError.innerHTML = "";
    return true;
}

// Validasi form rencana makanan (makanan.php)
function fn_ValMakanan() {
    var sMsg = "";
    if (document.getElementById("nama_tempat").value.trim() === "") {
        sMsg += "\n* Nama restoran/kedai wajib diisi";
    }
    if (document.getElementById("lokasi_mrt").value.trim() === "") {
        sMsg += "\n* Lokasi / Stasiun MRT terdekat wajib diisi";
    }
    if (document.getElementById("catatan").value.trim() === "") {
        sMsg += "\n* Catatan menu/sumber verifikasi wajib diisi";
    }

    if (sMsg !== "") {
        alert("Peringatan:\n" + sMsg);
        return false;
    }
    return true;
}

// Validasi form jadwal masjid (masjid.php)
function fn_ValMasjid() {
    var sMsg = "";
    if (document.getElementById("nama_masjid").value.trim() === "") {
        sMsg += "\n* Nama masjid wajib diisi";
    }
    if (document.getElementById("area_singapura").value.trim() === "") {
        sMsg += "\n* Area Singapura wajib diisi";
    }
    if (document.getElementById("keterangan").value.trim() === "") {
        sMsg += "\n* Keterangan jadwal wajib diisi";
    }

    if (sMsg !== "") {
        alert("Peringatan:\n" + sMsg);
        return false;
    }
    return true;
}