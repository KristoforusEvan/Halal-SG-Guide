// ============================================================
// index.js - homepage publik: pencarian + kartu tempat
// Klik kartu: jika belum login -> login.xhtml; jika sudah -> detail.xhtml?id=...
// ============================================================

var sesiAktif = null; // menyimpan objek sesi jika sudah login, null jika tamu

// Gambar kartu-kartu tempat ke dalam <div id="gridTempat">
function tampilKartu(daftar) {
    var grid = document.getElementById('gridTempat');
    kosongkan(grid);

    for (var i = 0; i < daftar.length; i++) {
        (function (t) {
            var kartu = buatElemen('div', '', 'kartu-tempat');
            kartu.setAttribute('title', sesiAktif ? 'Klik untuk melihat deskripsi detail' : 'Login terlebih dahulu untuk melihat detail');

            // Kotak gambar
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

            var teksCta = sesiAktif ? 'Lihat detail tempat \u2192' : 'Login untuk lihat detail \u2192';
            isi.appendChild(buatElemen('p', teksCta, 'kartu-cta'));

            kartu.appendChild(isi);

            // Aksi saat kartu tempat diklik
            kartu.addEventListener('click', function () {
                if (!sesiAktif) {
                    window.location.href = 'login.xhtml';
                    return;
                }
                window.location.href = 'detail.xhtml?id=' + encodeURIComponent(t.id);
            });

            grid.appendChild(kartu);
        })(daftar[i]);
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
    sesiAktif = await siapkanHalaman('bebas');

    // Sapaan hanya muncul kalau sudah login
    if (sesiAktif) {
        var sapaan = document.getElementById('sapaan');
        sapaan.appendChild(document.createTextNode('Halo, '));
        sapaan.appendChild(buatElemen('strong', sesiAktif.nama));
        sapaan.appendChild(document.createTextNode('!'));
    }

    document.getElementById('formCari').addEventListener('submit', function (e) {
        e.preventDefault();
        cariTempat();
    });

    cariTempat();
});