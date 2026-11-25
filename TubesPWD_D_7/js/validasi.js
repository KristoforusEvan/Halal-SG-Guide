// Hapus pesan error JS saat pengguna mulai mengetik ulang
function hapusPesanError() {
    var wadahError = document.getElementById("pesanError");
    if (wadahError) {
        wadahError.innerHTML = "";
    }
}

// Pasang listener "input" ke field yang ada di halaman
document.addEventListener("DOMContentLoaded", function () {
    var daftarInput = ["username", "email", "password"];
    for (var i = 0; i < daftarInput.length; i++) {
        var elemen = document.getElementById(daftarInput[i]);
        if (elemen) {
            elemen.addEventListener("input", hapusPesanError);
        }
    }
});

// Validasi form register: cukup username, email, dan password (min 8 karakter)
function fn_ValRegister() {
    var daftarError = [];
    var username = document.getElementById("username").value.trim();
    var email = document.getElementById("email").value.trim();
    var password = document.getElementById("password").value;
    var wadahError = document.getElementById("pesanError");

    var polaUsername = /^[a-zA-Z0-9_]+$/;
    var polaEmail = /^[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9]@[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9][\.][a-z0-9]{2,4}$/;

    if (username === "") {
        daftarError.push("Username wajib diisi.");
    } else if (!polaUsername.test(username)) {
        daftarError.push("Username hanya boleh berisi huruf, angka, dan underscore (_).");
    }

    if (email === "") {
        daftarError.push("Email wajib diisi.");
    } else if (!polaEmail.test(email)) {
        daftarError.push("Format email harus huruf kecil dan valid (contoh: nama@gmail.com).");
    }

    if (password === "") {
        daftarError.push("Password wajib diisi.");
    } else if (password.length < 8) {
        daftarError.push("Password minimal 8 karakter.");
    }

    if (daftarError.length > 0) {
        wadahError.innerHTML = "<p class=\"error\">" + daftarError.join("<br />") + "</p>";
        return false;
    }

    wadahError.innerHTML = "";
    return true;
}

// Validasi form login: cukup email dan password
function fn_ValLogin() {
    var daftarError = [];
    var email = document.getElementById("email").value.trim();
    var password = document.getElementById("password").value;
    var wadahError = document.getElementById("pesanError");
    var polaEmail = /^[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9]@[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9][\.][a-z0-9]{2,4}$/;

    if (email === "") {
        daftarError.push("Email belum diisi.");
    } else if (!polaEmail.test(email)) {
        daftarError.push("Format email harus huruf kecil dan valid (contoh: nama@gmail.com).");
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