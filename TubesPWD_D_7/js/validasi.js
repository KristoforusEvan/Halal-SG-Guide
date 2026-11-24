function fn_ValRegister() {
    var sMsg = "";
    var nama = document.getElementById("nama").value;
    var username = document.getElementById("username").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var asal = document.getElementById("asal_kota").value;
    var polaEmail = /^[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9]@[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9][\.][a-z0-9]{2,4}$/;

    if (nama == "") {
        sMsg += "\n* Nama lengkap wajib diisi";
    }
    if (username == "") {
        sMsg += "\n* Username wajib diisi";
    }
    if (email == "") {
        sMsg += "\n* Email wajib diisi";
    } else if (!polaEmail.test(email)) {
        sMsg += "\n* Format email harus huruf kecil dan valid";
    }
    if (password == "") {
        sMsg += "\n* Password wajib diisi";
    }
    if (asal == "") {
        sMsg += "\n* Asal kota wajib diisi";
    }

    if (sMsg != "") {
        alert("Peringatan:\n" + sMsg);
        return false;
    } else {
        return true;
    }
}

function fn_ValLogin() {
    var sMsg = "";
    if (document.getElementById("username").value == "") {
        sMsg += "\n* Username belum diisi";
    }
    if (document.getElementById("password").value == "") {
        sMsg += "\n* Password belum diisi";
    }

    if (sMsg != "") {
        alert("Peringatan:\n" + sMsg);
        return false;
    } else {
        return true;
    }
}

function fn_ValMakanan() {
    var sMsg = "";
    if (document.getElementById("nama_tempat").value == "") {
        sMsg += "\n* Nama restoran/kedai wajib diisi";
    }
    if (document.getElementById("lokasi_mrt").value == "") {
        sMsg += "\n* Lokasi / Stasiun MRT terdekat wajib diisi";
    }
    if (document.getElementById("catatan").value == "") {
        sMsg += "\n* Catatan menu/sumber verifikasi wajib diisi";
    }

    if (sMsg != "") {
        alert("Peringatan:\n" + sMsg);
        return false;
    } else {
        return true;
    }
}

function fn_ValMasjid() {
    var sMsg = "";
    if (document.getElementById("nama_masjid").value == "") {
        sMsg += "\n* Nama masjid wajib diisi";
    }
    if (document.getElementById("area_singapura").value == "") {
        sMsg += "\n* Area Singapura wajib diisi";
    }
    if (document.getElementById("keterangan").value == "") {
        sMsg += "\n* Keterangan jadwal wajib diisi";
    }

    if (sMsg != "") {
        alert("Peringatan:\n" + sMsg);
        return false;
    } else {
        return true;
    }
}