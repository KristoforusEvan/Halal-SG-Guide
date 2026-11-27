// ============================================================
// umum.js - fungsi bantu yang dipakai SEMUA halaman .xhtml
// Urutan <script> di tiap halaman: umum.js -> validasi.js -> skrip halaman
// ============================================================

// Memanggil endpoint PHP di folder api/ lewat fetch.
//  - url  : alamat endpoint, contoh 'api/login.php'
//  - data : (opsional) objek {kunci: nilai}. Kalau ada, dikirim sebagai POST.
// Selalu mengembalikan objek hasil: {sukses, pesan, ..., kode}
async function panggilApi(url, data) {
    var opsi = { credentials: 'same-origin' };
    if (data !== undefined) {
        opsi.method = 'POST';
        opsi.headers = { 'Content-Type': 'application/x-www-form-urlencoded' };
        opsi.body = new URLSearchParams(data).toString();
    }

    var respons;
    try {
        respons = await fetch(url, opsi);
    } catch (e) {
        return { sukses: false, pesan: 'Tidak bisa menghubungi server. Pastikan Apache XAMPP menyala.', kode: 0 };
    }

    var hasil;
    try {
        hasil = await respons.json();
    } catch (e) {
        hasil = { sukses: false, pesan: 'Balasan server tidak valid (bukan JSON). Cek file PHP di folder api/.' };
    }
    hasil.kode = respons.status;
    // Detail error teknis (hanya ada kalau MODE_DEBUG = true di api/fungsi.php)
    if (hasil.detail) {
        hasil.pesan = hasil.pesan + ' (' + hasil.detail + ')';
    }
    return hasil;
}

// Kalau server membalas 401 (belum login / session habis), kembalikan ke halaman login.
// Mengembalikan true kalau pengguna dialihkan.
function sesiHabis(hasil) {
    if (hasil.kode === 401) {
        window.location.replace('login.xhtml');
        return true;
    }
    return false;
}

// ---------- Bantuan membuat elemen (tanpa innerHTML, supaya aman dari XSS) ----------

function kosongkan(elemen) {
    while (elemen.firstChild) {
        elemen.removeChild(elemen.firstChild);
    }
}

// Buat elemen <tag class="kelas">teks</tag>. Teks dimasukkan sebagai TEKS, bukan HTML.
function buatElemen(tag, teks, kelas) {
    var el = document.createElement(tag);
    if (teks) {
        el.appendChild(document.createTextNode(teks));
    }
    if (kelas) {
        el.className = kelas;
    }
    return el;
}

// Tampilkan pesan di <div id="pesan">. isi boleh teks tunggal atau array teks.
// tipe: 'error' atau 'sukses' (nama class CSS). isi kosong = hapus pesan.
function tampilPesan(isi, tipe) {
    var wadah = document.getElementById('pesan');
    if (!wadah) {
        return;
    }
    kosongkan(wadah);
    if (!isi || isi.length === 0) {
        return;
    }
    var daftar = Array.isArray(isi) ? isi : [isi];
    var p = buatElemen('p', '', tipe);
    for (var i = 0; i < daftar.length; i++) {
        if (i > 0) {
            p.appendChild(document.createElement('br'));
        }
        p.appendChild(document.createTextNode(daftar[i]));
    }
    wadah.appendChild(p);
}

// ---------- Session & navbar ----------

// Tanya server: siapa yang login? Mengembalikan {nama} atau null kalau belum login.
async function ambilSesi() {
    var hasil = await panggilApi('api/session.php');
    if (hasil.sukses && hasil.login) {
        return hasil;
    }
    return null;
}

function tambahLink(nav, teks, href) {
    var a = buatElemen('a', teks);
    a.setAttribute('href', href);
    nav.appendChild(a);
    return a;
}

// Susun isi <div id="nav"> sesuai status login
function bangunNavbar(sesi) {
    var nav = document.getElementById('nav');
    if (!nav) {
        return;
    }
    kosongkan(nav);
    tambahLink(nav, 'Homepage', 'index.xhtml');

    if (sesi) {
        tambahLink(nav, 'Rencana Kuliner', 'makanan.xhtml');
        tambahLink(nav, 'Jadwal Masjid', 'masjid.xhtml');
        tambahLink(nav, 'Profil Saya', 'profil.xhtml');
        var linkLogout = tambahLink(nav, 'Logout', '#');
        linkLogout.addEventListener('click', async function (e) {
            e.preventDefault();
            await panggilApi('api/logout.php', {});
            window.location.href = 'index.xhtml';
        });
    } else {
        tambahLink(nav, 'Login', 'login.xhtml');
        tambahLink(nav, 'Daftar', 'register.xhtml');
    }
}

// Dipanggil di awal tiap halaman.
//  aturan 'privat' : wajib login (kalau belum -> ke login.xhtml)
//  aturan 'tamu'   : khusus yang belum login (kalau sudah -> ke index.xhtml)
//  aturan 'bebas'  : siapa saja
// Mengembalikan data sesi (atau null kalau belum login / sedang dialihkan).
async function siapkanHalaman(aturan) {
    var sesi = await ambilSesi();
    if (aturan === 'privat' && !sesi) {
        window.location.replace('login.xhtml');
        return null;
    }
    if (aturan === 'tamu' && sesi) {
        window.location.replace('index.xhtml');
        return null;
    }
    bangunNavbar(sesi);
    return sesi;
}
