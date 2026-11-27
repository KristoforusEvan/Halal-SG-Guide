// ============================================================
// index.js - homepage publik: pencarian, Geolocation + Haversine (Bonus 2),
// dan pengalihan login saat kartu diklik untuk melihat detail.
// ============================================================

var sesiAktif = null;
var daftarTempatSaatIni = [];
var lokasiUser = null; // { lat: number, lng: number, label: string }

// Titik koordinat referensi stasiun MRT / area populer Singapura untuk simulasi manual
var TITIK_MRT_SINGAPURA = {
    bugis:       { lat: 1.3008, lng: 103.8561, label: 'Bugis MRT (EW12 / DT14)' },
    orchard:     { lat: 1.3048, lng: 103.8318, label: 'Orchard MRT (NS22)' },
    chinatown:   { lat: 1.2848, lng: 103.8439, label: 'Chinatown MRT (NE4 / DT19)' },
    marinabay:   { lat: 1.2764, lng: 103.8546, label: 'Marina Bay MRT (NS27 / CE2)' },
    changi:      { lat: 1.3574, lng: 103.9885, label: 'Changi Airport MRT (CG2)' },
    harbourfront:{ lat: 1.2654, lng: 103.8214, label: 'HarbourFront / Sentosa (NE1 / CC29)' }
};

// Rumus Haversine murni (menghitung jarak garis lurus antar 2 titik koordinat dalam kilometer)
function hitungJarakKm(lat1, lon1, lat2, lon2) {
    var R = 6371;
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLon = (lon2 - lon1) * Math.PI / 180;
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function urutkanBerdasarkanJarak(daftar) {
    var salinan = [];
    for (var i = 0; i < daftar.length; i++) {
        var item = Object.assign({}, daftar[i]);
        if (lokasiUser && item.lat !== null && item.lng !== null && item.lat !== '' && item.lng !== '') {
            item.jarakKm = hitungJarakKm(
                lokasiUser.lat,
                lokasiUser.lng,
                parseFloat(item.lat),
                parseFloat(item.lng)
            );
        } else {
            item.jarakKm = null;
        }
        salinan.push(item);
    }

    if (lokasiUser) {
        salinan.sort(function (a, b) {
            if (a.jarakKm === null && b.jarakKm === null) {
                return 0;
            }
            if (a.jarakKm === null) {
                return 1;
            }
            if (b.jarakKm === null) {
                return -1;
            }
            return a.jarakKm - b.jarakKm;
        });
    }
    return salinan;
}

function tampilKartu(daftar) {
    var grid = document.getElementById('gridTempat');
    kosongkan(grid);

    var daftarTampil = urutkanBerdasarkanJarak(daftar);

    for (var i = 0; i < daftarTampil.length; i++) {
        (function (t) {
            var kartu = buatElemen('div', '', 'kartu-tempat');
            kartu.setAttribute('title', sesiAktif ? 'Klik untuk melihat deskripsi detail' : 'Login terlebih dahulu untuk melihat selengkapnya');

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
            isi.appendChild(buatElemen('p', t.area + ' • ' + t.harga_estimasi, 'kartu-area'));

            if (typeof t.jarakKm === 'number' && !isNaN(t.jarakKm)) {
                isi.appendChild(buatElemen('p', 'Jarak: ~' + t.jarakKm.toFixed(2) + ' km dari ' + lokasiUser.label, 'kartu-jarak'));
            }

            isi.appendChild(buatElemen('p', t.keterangan));

            var teksCta = sesiAktif ? 'Lihat selengkapnya \u2192' : 'Login untuk lihat selengkapnya \u2192';
            isi.appendChild(buatElemen('p', teksCta, 'kartu-cta'));

            kartu.appendChild(isi);

            // Tamu diarahkan ke login dengan parameter redirect menuju halaman detail tempat ini
            kartu.addEventListener('click', function () {
                var targetDetail = 'detail.xhtml?id=' + encodeURIComponent(t.id);
                if (!sesiAktif) {
                    window.location.href = 'login.xhtml?redirect=' + encodeURIComponent(targetDetail);
                    return;
                }
                window.location.href = targetDetail;
            });

            grid.appendChild(kartu);
        })(daftarTampil[i]);
    }
}

function setStatusLokasi(teks) {
    var info = document.getElementById('infoLokasi');
    if (info) {
        info.textContent = teks;
    }
}

function deteksiLokasiGps() {
    if (!navigator.geolocation) {
        setStatusLokasi('Browser tidak mendukung GPS. Silakan pilih titik area MRT manual.');
        return;
    }

    setStatusLokasi('Mendeteksi titik koordinat GPS Anda...');
    navigator.geolocation.getCurrentPosition(
        function (posisi) {
            lokasiUser = {
                lat: posisi.coords.latitude,
                lng: posisi.coords.longitude,
                label: 'Lokasi GPS Anda'
            };
            document.getElementById('lokasiManual').value = '';
            setStatusLokasi('Aktif: Lokasi GPS (' + lokasiUser.lat.toFixed(4) + ', ' + lokasiUser.lng.toFixed(4) + ') - Diurutkan dari terdekat.');
            tampilKartu(daftarTempatSaatIni);
        },
        function () {
            setStatusLokasi('Izin GPS ditolak/tidak tersedia. Gunakan pilihan Lokasi Manual MRT di samping.');
        },
        { enableHighAccuracy: true, timeout: 8000 }
    );
}

async function cariTempat() {
    var q = document.getElementById('q').value.trim();
    var kategori = document.getElementById('kategori').value;
    var harga = document.getElementById('harga').value;
    var url = 'api/tempat.php?q=' + encodeURIComponent(q) + '&kategori=' + encodeURIComponent(kategori) + '&harga=' + encodeURIComponent(harga);

    var hasil = await panggilApi(url);
    if (!hasil.sukses) {
        tampilPesan(hasil.pesan, 'error');
        return;
    }

    daftarTempatSaatIni = hasil.data;
    document.getElementById('jumlah').textContent = hasil.jumlah;
    tampilKartu(daftarTempatSaatIni);

    if (hasil.jumlah === 0) {
        tampilPesan('Tidak ada tempat yang cocok. Coba kata kunci lain.', 'error');
    } else {
        tampilPesan('');
    }
}

document.addEventListener('DOMContentLoaded', async function () {
    sesiAktif = await siapkanHalaman('bebas');

    if (sesiAktif) {
        var sapaan = document.getElementById('sapaan');
        sapaan.appendChild(document.createTextNode('Halo, '));
        sapaan.appendChild(buatElemen('strong', sesiAktif.nama));
        sapaan.appendChild(document.createTextNode('! Selamat merencanakan perjalanan halal Anda di Singapura.'));
    }

    document.getElementById('formCari').addEventListener('submit', function (e) {
        e.preventDefault();
        cariTempat();
    });

    document.getElementById('btnLokasiGps').addEventListener('click', deteksiLokasiGps);

    document.getElementById('lokasiManual').addEventListener('change', function () {
        var kunci = this.value;
        if (kunci === '') {
            lokasiUser = null;
            setStatusLokasi('Urutan default katalog (belum memilih titik lokasi).');
        } else if (TITIK_MRT_SINGAPURA[kunci]) {
            lokasiUser = TITIK_MRT_SINGAPURA[kunci];
            setStatusLokasi('Aktif: ' + lokasiUser.label + ' - Diurutkan dari terdekat.');
        }
        tampilKartu(daftarTempatSaatIni);
    });

    cariTempat();
});