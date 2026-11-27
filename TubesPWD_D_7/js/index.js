// ============================================================
// index.js - homepage publik: pencarian + kartu tempat
// Data diambil dari api/tempat.php (tidak perlu login).
// ============================================================

// Gambar kartu-kartu tempat ke dalam <div id="gridTempat">
function tampilKartu(daftar) {
    var grid = document.getElementById('gridTempat');
    kosongkan(grid);

    for (var i = 0; i < daftar.length; i++) {
        var t = daftar[i];

        var kartu = buatElemen('div', '', 'kartu-tempat');

        // Kotak gambar: kalau foto kosong, tetap tampil sebagai kotak warna sesuai kategori
        var gambar = buatElemen('div', '', 'kartu-gambar kat-' + t.kategori);
        if (t.foto) {
            var img = document.createElement('img');
            img.setAttribute('src', 'img/' + t.foto);
            img.setAttribute('alt', t.nama);
            gambar.appendChild(img);
        }
        kartu.appendChild(gambar);

        var isi = buatElemen('div', '', 'kartu-isi');
        isi.appendChild(buatElemen('span', t.status, 'badge-halal'));
        isi.appendChild(buatElemen('h3', t.nama));
        isi.appendChild(buatElemen('p', t.area_mrt, 'kartu-mrt'));
        isi.appendChild(buatElemen('p', t.keterangan));
        kartu.appendChild(isi);

        grid.appendChild(kartu);
    }
}

// Ambil hasil pencarian dari server lalu tampilkan
async function cariTempat() {
    var q = document.getElementById('q').value.trim();
    var kategori = document.getElementById('kategori').value;
    var url = 'api/tempat.php?q=' + encodeURIComponent(q) + '&kategori=' + encodeURIComponent(kategori);

    var hasil = await panggilApi(url);
    if (!hasil.sukses) {
        tampilPesan(hasil.pesan, 'error');
        return;
    }

    document.getElementById('jumlah').textContent = hasil.jumlah;
    tampilKartu(hasil.data);

    if (hasil.jumlah === 0) {
        tampilPesan('Tidak ada tempat yang cocok. Coba kata kunci lain.', 'error');
    } else {
        tampilPesan('');
    }
}

document.addEventListener('DOMContentLoaded', async function () {
    var sesi = await siapkanHalaman('bebas');

    // Sapaan hanya muncul kalau sudah login
    if (sesi) {
        var sapaan = document.getElementById('sapaan');
        sapaan.appendChild(document.createTextNode('Halo, '));
        sapaan.appendChild(buatElemen('strong', sesi.nama));
        sapaan.appendChild(document.createTextNode('!'));
    }

    document.getElementById('formCari').addEventListener('submit', function (e) {
        e.preventDefault(); // cegah halaman reload, pencarian lewat fetch
        cariTempat();
    });

    cariTempat(); // tampilkan semua tempat saat halaman pertama dibuka
});
