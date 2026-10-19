<?
/**[N]**
 * JIBAS Education Community
 * Jaringan Informasi Bersama Antar Sekolah
 *
 * Beranda anjungan modern: kisi kartu berita lintas lembaga.
 * Isi kartu (judul, cuplikan, tanggal, penulis, lembaga, foto sampul) diambil dari
 * jbsvcr.notes lewat beranda/berita.data.php; foto sampul memakai lampiran berita
 * bila ada, jika tidak cover galeri lembaga pada kategori yang sama.
 **[N]**/ ?>
<?
require_once("../include/config.php");
require_once("../include/db_functions.php");

OpenDb();

$sql = "SELECT replid, departemen
          FROM jbsakad.departemen
         WHERE aktif = 1
         ORDER BY urutan";
$res = QueryDb($sql);
$daftarLembaga = array();
while ($row = mysqli_fetch_row($res))
{
    $daftarLembaga[] = array((int)$row[0], $row[1]);
}
?>
<div class="anj-berita">
    <div class="anj-berita-bar">
        <div class="anj-berita-filter">
            <label class="anj-berita-label" for="anjBeritaLembaga">Lembaga</label>
            <select id="anjBeritaLembaga" class="form-select form-select-sm">
                <option value="0">Semua lembaga</option>
<? foreach ($daftarLembaga as $l) { ?>
                <option value="<?= (int)$l[0] ?>"><?= htmlspecialchars($l[1], ENT_QUOTES, 'UTF-8') ?></option>
<? } ?>
            </select>
        </div>
        <div class="anj-berita-filter">
            <span class="anj-berita-label">Jenis</span>
            <div class="btn-group btn-group-sm anj-berita-jenis" role="group" aria-label="Saring jenis berita">
                <button type="button" class="btn anj-berita-chip active" data-kategori="">Semua</button>
                <button type="button" class="btn anj-berita-chip" data-kategori="ifse">Berita Sekolah</button>
                <button type="button" class="btn anj-berita-chip" data-kategori="mading">Mading</button>
            </div>
        </div>
        <p class="anj-berita-hitung" id="anjBeritaHitung" aria-live="polite"></p>
    </div>

    <div class="anj-berita-grid" id="anjBeritaGrid"></div>

    <p class="anj-berita-kosong d-none" id="anjBeritaKosong">
        <i class="bi bi-inbox me-2"></i>Belum ada berita untuk pilihan ini.
    </p>

    <div class="anj-berita-pager">
        <button type="button" class="btn btn-sm btn-primary d-none" id="anjBeritaMore">
            <i class="bi bi-arrow-down-circle me-1"></i>Muat berita berikutnya
        </button>
    </div>
</div>

<div class="modal fade" id="anjBeritaModal" tabindex="-1" role="dialog"
     aria-labelledby="anjBeritaModalJudul" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content anj-modal">
            <div class="modal-header">
                <div class="anj-berita-modal-head">
                    <span class="anj-berita-badge anj-berita-badge-modal" id="anjBeritaModalLabel">Berita</span>
                    <h5 class="modal-title" id="anjBeritaModalJudul"></h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <ul class="anj-berita-meta anj-berita-meta-modal">
                    <li><i class="bi bi-building"></i><span id="anjBeritaModalLembaga"></span></li>
                    <li><i class="bi bi-person-circle"></i><span id="anjBeritaModalPenulis"></span></li>
                    <li><i class="bi bi-calendar3"></i><span id="anjBeritaModalTanggal"></span></li>
                </ul>
                <div class="anj-berita-isi" id="anjBeritaIsi"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
var anjBerita = {
    mulai: 0,
    langkah: 9,
    total: 0,
    memuat: false,
    modal: null
};

anjBerita_aman = function (html) {
    /* Isi berita ditulis staf lewat editor HTML. Sebelum ditempel ke halaman,
       skrip, gaya, frame dan atribut on* dibuang supaya beranda tidak jadi celah XSS. */
    var doc = new DOMParser().parseFromString('<div id="akar">' + html + '</div>', 'text/html');
    var akar = doc.getElementById('akar');
    if (!akar) return '';
    var berbahaya = akar.querySelectorAll('script,style,iframe,frame,object,embed,link,meta,base,form,input,button');
    [].forEach.call(berbahaya, function (el) { el.parentNode.removeChild(el); });
    [].forEach.call(akar.querySelectorAll('*'), function (el) {
        [].slice.call(el.attributes).forEach(function (a) {
            var n = a.name.toLowerCase();
            var v = (a.value || '').replace(/\s/g, '').toLowerCase();
            if (n.indexOf('on') === 0 || ((n === 'href' || n === 'src') && v.indexOf('javascript:') === 0))
                el.removeAttribute(a.name);
        });
    });
    var keluaran = document.createElement('div');
    while (akar.firstChild) keluaran.appendChild(akar.firstChild);
    return keluaran.innerHTML;
};

anjBerita_kartu = function (item) {
    var kartu = document.createElement('article');
    kartu.className = 'anj-berita-kartu';
    kartu.tabIndex = 0;
    kartu.setAttribute('role', 'button');
    kartu.dataset.id = item.id;

    var media = document.createElement('div');
    media.className = 'anj-berita-media';
    var badge = document.createElement('span');
    badge.className = 'anj-berita-badge';
    badge.innerHTML = '<i class="bi bi-newspaper me-1"></i>';
    badge.appendChild(document.createTextNode(item.label));
    media.appendChild(badge);

    if (item.gambar) {
        var img = document.createElement('img');
        img.className = 'anj-berita-foto';
        img.loading = 'lazy';
        img.alt = item.judul;
        img.src = item.gambar;
        img.onerror = function () { img.parentNode.removeChild(img); media.classList.add('anj-berita-media-tanpa-foto'); };
        media.appendChild(img);
    } else {
        media.classList.add('anj-berita-media-tanpa-foto');
        var inisial = document.createElement('span');
        inisial.className = 'anj-berita-inisial';
        inisial.textContent = item.lembaga;
        media.appendChild(inisial);
    }
    kartu.appendChild(media);

    var tubuh = document.createElement('div');
    tubuh.className = 'anj-berita-tubuh';
    var judul = document.createElement('h3');
    judul.className = 'anj-berita-judul';
    judul.textContent = item.judul;
    var kutip = document.createElement('p');
    kutip.className = 'anj-berita-kutip';
    kutip.textContent = item.cuplikan;
    tubuh.appendChild(judul);
    tubuh.appendChild(kutip);
    kartu.appendChild(tubuh);

    var kaki = document.createElement('ul');
    kaki.className = 'anj-berita-meta';
    [['bi-building', item.lembaga], ['bi-person-circle', item.penulis], ['bi-calendar3', item.tanggal]]
        .forEach(function (bagian) {
            var li = document.createElement('li');
            li.innerHTML = '<i class="bi ' + bagian[0] + '"></i>';
            var teks = document.createElement('span');
            teks.textContent = bagian[1];
            li.appendChild(teks);
            kaki.appendChild(li);
        });
    kartu.appendChild(kaki);

    var buka = function () { anjBerita_buka(item.id); };
    kartu.addEventListener('click', buka);
    kartu.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); buka(); }
    });
    return kartu;
};

anjBerita_isi = function (data, tambah) {
    var grid = document.getElementById('anjBeritaGrid');
    if (!tambah) grid.innerHTML = '';
    data.items.forEach(function (item) { grid.appendChild(anjBerita_kartu(item)); });

    anjBerita.total = data.total;
    anjBerita.mulai = data.mulai + data.items.length;

    var sisa = anjBerita.total - anjBerita.mulai;
    document.getElementById('anjBeritaHitung').textContent =
        data.total + ' berita' + (data.total ? ' · ' + anjBerita.mulai + ' ditampilkan' : '');
    document.getElementById('anjBeritaKosong').classList.toggle('d-none', anjBerita.total > 0);
    document.getElementById('anjBeritaMore').classList.toggle('d-none', sisa <= 0);
};

anjBerita_muat = function (tambah) {
    if (anjBerita.memuat) return;
    anjBerita.memuat = true;
    var tombol = document.getElementById('anjBeritaMore');
    tombol.disabled = true;

    var url = 'beranda/berita.data.php?lembaga=' + encodeURIComponent(document.getElementById('anjBeritaLembaga').value)
        + '&kategori=' + encodeURIComponent(anjBerita_kategori())
        + '&mulai=' + (tambah ? anjBerita.mulai : 0) + '&jumlah=' + anjBerita.langkah;

    fetch(url, { credentials: 'same-origin' })
        .then(function (res) { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
        .then(function (data) {
            if (data.error) throw new Error(data.error);
            anjBerita_isi(data, !!tambah);
        })
        .catch(function (err) {
            document.getElementById('anjBeritaGrid').innerHTML =
                '<div class="anj-error">Berita tidak dapat dimuat' + (err && err.message ? ' (' + err.message + ')' : '') + '.</div>';
        })
        .then(function () {
            anjBerita.memuat = false;
            tombol.disabled = false;
        });
};

anjBerita_kategori = function () {
    var aktif = document.querySelector('.anj-berita-jenis .active');
    return aktif ? aktif.getAttribute('data-kategori') : '';
};

anjBerita_buka = function (id) {
    fetch('beranda/berita.data.php?detail=' + encodeURIComponent(id), { credentials: 'same-origin' })
        .then(function (res) { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
        .then(function (data) {
            if (data.error) throw new Error(data.error);
            var item = data.item;
            document.getElementById('anjBeritaModalLabel').textContent = item.label;
            document.getElementById('anjBeritaModalJudul').textContent = item.judul;
            document.getElementById('anjBeritaModalLembaga').textContent = item.lembaga;
            document.getElementById('anjBeritaModalPenulis').textContent = item.penulis;
            document.getElementById('anjBeritaModalTanggal').textContent = item.tanggal;
            var wadah = document.getElementById('anjBeritaIsi');
            wadah.innerHTML = anjBerita_aman(item.isi);
            if (!anjBerita.modal) anjBerita.modal = new bootstrap.Modal(document.getElementById('anjBeritaModal'));
            anjBerita.modal.show();
        })
        .catch(function (err) {
            if (window.Swal) Swal.fire({ icon: 'error', title: 'Berita tidak dapat dibuka',
                text: err && err.message ? err.message : '', confirmButtonText: 'Tutup' });
        });
};

anjBerita_siap = function () {
    /* Modal dipindah ke <body> agar tidak ikut terpotong gaya kartu induknya. */
    var modalEl = document.getElementById('anjBeritaModal');
    if (modalEl && modalEl.parentNode !== document.body) document.body.appendChild(modalEl);

    document.getElementById('anjBeritaLembaga').addEventListener('change', function () { anjBerita_muat(false); });
    document.getElementById('anjBeritaMore').addEventListener('click', function () { anjBerita_muat(true); });
    [].forEach.call(document.querySelectorAll('.anj-berita-jenis .anj-berita-chip'), function (tombol) {
        tombol.addEventListener('click', function () {
            document.querySelectorAll('.anj-berita-jenis .anj-berita-chip').forEach(function (lain) {
                lain.classList.remove('active');
            });
            tombol.classList.add('active');
            anjBerita_muat(false);
        });
    });
    anjBerita_muat(false);
};

if (document.getElementById('anjBeritaGrid')) anjBerita_siap();
</script>
