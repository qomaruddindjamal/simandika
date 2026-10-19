/* JIBAS Anjungan — menu atas + pemuat seksi tanpa reload, tanpa /anjungan di address bar.
   Pane memakai id warisan (content-pane-a..k) sehingga script tiap seksi anjungan tetap jalan:
   potongan HTML dimuat, <script>-nya dieksekusi berurutan, baru fungsi init-nya dipanggil. */
(function () {
    'use strict';

    var root = document.documentElement;
    var CSS_TEMA = ['/style/jibas-theme.css', '/style/jibas-anjungan.css'];

    function kartu(key) {
        return document.querySelector('.anj-card[data-seksi="' + key + '"]');
    }

    function judul(key) {
        var el = kartu(key);
        var h2 = el ? el.querySelector('.anj-card-title') : null;
        return h2 ? h2.textContent.trim() : '';
    }

    /* ---------- pemuatan seksi ---------- */

    var sudahDimuat = {};

    function muatLuarscript(urlAbs) {
        if (sudahDimuat[urlAbs]) return Promise.resolve();
        sudahDimuat[urlAbs] = true;
        return new Promise(function (resolve) {
            var node = document.createElement('script');
            node.src = urlAbs;
            node.onload = node.onerror = function () { resolve(); };
            document.body.appendChild(node);
        });
    }

    function muatGaya(rel) {
        var urlAbs = new URL(rel, document.baseURI).href;
        if (document.querySelector('link[href="' + urlAbs + '"]')) return;
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = urlAbs;
        document.head.appendChild(link);
    }

    function muatScript(el) {
        return new Promise(function (resolve) {
            if (el.src) {
                muatLuarscript(el.src).then(resolve);
                return;
            }
            try {
                window.eval(el.textContent);
            } catch (e) { /* script warisan yang bergantung pada DOM dialog — abaikan */ }
            resolve();
        });
    }

    function temaIframe(frame) {
        if (frame.dataset.anjTema === '1') return;
        frame.addEventListener('load', function () {
            var doc;
            try { doc = frame.contentDocument; } catch (e) { return; }
            if (!doc || !doc.documentElement) return;
            frame.dataset.anjTema = '1';
            doc.documentElement.setAttribute('data-jibas-phase', root.getAttribute('data-jibas-phase') || 'terang');
            doc.documentElement.setAttribute('data-bs-theme', root.getAttribute('data-bs-theme') || 'light');
            if (doc.body) doc.body.classList.add('anj-iframe');
            if (!doc.querySelector('link[data-anj-tema]')) {
                CSS_TEMA.forEach(function (href) {
                    var link = doc.createElement('link');
                    link.rel = 'stylesheet';
                    link.setAttribute('data-anj-tema', '1');
                    link.href = href;
                    (doc.head || doc.documentElement).appendChild(link);
                });
            }
        });
    }

    function pasangIsi(kartuEl, html) {
        var doc = new DOMParser().parseFromString(html, 'text/html');
        var paneEl = doc.body;
        var scripts = [].slice.call(paneEl.querySelectorAll('script'));
        scripts.forEach(function (s) { s.parentNode.removeChild(s); });

        var target = kartuEl.querySelector('.anj-pane');
        target.textContent = '';

        /* Sebagian script warisan Informasi Siswa menulis ulang ke '#is_main', jadi
           isinya dibungkus elemen dengan id itu bila kartunya meminta (data-wadah). */
        var tujuan = target;
        var wadahId = kartuEl.getAttribute('data-wadah');
        if (wadahId) {
            tujuan = document.createElement('div');
            tujuan.id = wadahId;
            target.appendChild(tujuan);
        }
        while (paneEl.firstChild) tujuan.appendChild(paneEl.firstChild);
        target.querySelectorAll('iframe').forEach(temaIframe);

        var rantai = Promise.resolve();
        scripts.forEach(function (s) {
            rantai = rantai.then(function () {
                var tiruan = document.createElement('script');
                if (s.src) tiruan.src = new URL(s.getAttribute('src'), target.baseURI).href;
                if (s.type) tiruan.type = s.type;
                if (!s.src) tiruan.textContent = s.textContent;
                return muatScript(tiruan);
            });
        });
        return rantai;
    }

    function muatSeksi(key) {
        var el = kartu(key);
        if (!el || el.dataset.anjTermuat === '1') return Promise.resolve();
        el.dataset.anjTermuat = '1';
        var target = el.querySelector('.anj-pane');
        target.innerHTML = '<div class="anj-loading"><span class="anj-spinner" aria-hidden="true"></span>Memuat ' + judul(key) + '…</div>';

        var pustaka = [];
        try { pustaka = JSON.parse(el.dataset.lib || '[]') || []; } catch (e) { /* tanpa pustaka */ }
        try { JSON.parse(el.dataset.css || '[]').forEach(muatGaya); } catch (e) { /* tanpa gaya tambahan */ }
        var rantai = Promise.resolve();
        pustaka.forEach(function (rel) {
            rantai = rantai.then(function () { return muatLuarscript(new URL(rel, document.baseURI).href); });
        });

        return rantai
            .then(function () { return fetch(el.dataset.src, { credentials: 'same-origin' }); })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(function (html) {
                target.textContent = '';
                return pasangIsi(el, html);
            })
            .then(function () {
                var nama = el.dataset.init;
                if (nama && typeof window[nama] === 'function') window[nama]();
                if (el.dataset.dasbor === 'siswa') pasangDasborSiswa(el);
            })
            .catch(function (err) {
                el.dataset.anjTermuat = '';
                target.innerHTML = '<div class="anj-error"><i class="bi bi-exclamation-triangle me-2"></i>'
                    + judul(key) + ' tidak dapat dimuat' + (err && err.message ? ' (' + err.message + ')' : '') + '.</div>';
            });
    }

    /* ---------- rel sidebar & kepala transparan saat digulir ---------- */

    /* Sesi siswa yang sedang hidup: satu-satunya syarat rel laporan ditampilkan. */
    var sesiSiswaAktif = false;

    function rel() {
        return document.querySelector('.anj-rail');
    }

    /* Sidebar laporan tinggal di rel di luar kartu. Selama sesi siswa aktif rel dipakai di
       semua seksi (permintaan user 2026-10-19), bukan hanya di kartu Info Siswa; tanpa
       sidebar di dalamnya — pengujung tanpa sesi — kelas pengatur kolom tidak dipasang
       supaya seksi tetap memakai lebar penuh. */
    function tampilRel(aktif) {
        var utama = document.querySelector('.anj-main');
        var r = rel();
        if (utama) {
            utama.classList.toggle('anj-ber-rail', aktif && !!(r && r.querySelector('.anj-sb')));
        }
    }

    function pastikanRelSiswa() {
        var r = rel();
        if (!r) return;
        if (!r.querySelector('.anj-sb')) r.appendChild(bangunSidebar());
        tampilRel(true);
    }

    function kosongkanRel() {
        var r = rel();
        if (r) r.textContent = '';
        tampilRel(false);
    }

    /* Rel ditahan di bawah kepala yang lengket, jadi tingginya harus terukur. */
    function ukurKepala() {
        var bar = document.getElementById('anjTopbar');
        if (bar) root.style.setProperty('--anj-topbar-tinggi', bar.offsetHeight + 'px');
    }

    function pantauGulir() {
        var bar = document.getElementById('anjTopbar');
        if (!bar) return;
        var cek = function () {
            bar.classList.toggle('anj-gulung', window.scrollY > 12);
        };
        window.addEventListener('scroll', cek, { passive: true });
        window.addEventListener('resize', function () { ukurKepala(); cek(); }, { passive: true });
        cek();
    }

    /* ---------- navigasi menu atas ---------- */

    function aktifkan(key, push) {
        var el = kartu(key);
        if (!el) return;
        [].slice.call(document.querySelectorAll('.anj-card')).forEach(function (c) {
            c.classList.toggle('d-none', c !== el);
        });
        /* Rel laporan ikut tampil di seksi mana pun selama sesi siswa hidup; di luar itu
           hanya kartu Info Siswa yang punya rel. */
        if (sesiSiswaAktif) pastikanRelSiswa(); else tampilRel(key === 'infosiswa');
        [].slice.call(document.querySelectorAll('.anj-nav-link')).forEach(function (a) {
            a.classList.toggle('active', a.getAttribute('data-seksi') === key);
        });
        var h1 = document.getElementById('anjHeroTitle');
        if (h1) h1.textContent = judul(key);
        if (push) history.pushState({ seksi: key }, '', '/' + (key === 'beranda' ? '' : key + '/'));
        /* Kembali ke seksi ini lewat tombol nama: pushState di atas membuang hash, jadi
           alamatnya disamakan lagi. Hanya untuk navigasi menu — perpindahan hash antar
           laporan juga memanggil penangan popstate, dan di sana hash justru tujuannya. */
        if (push && key === 'infosiswa') samakanHash();
        muatSeksi(key);
    }

    document.addEventListener('click', function (ev) {
        var akun = ev.target.closest ? ev.target.closest('.anj-masuk[data-seksi]') : null;
        if (akun) {
            ev.preventDefault();
            aktifkan(akun.getAttribute('data-seksi'), true);
            return;
        }
        var link = ev.target.closest ? ev.target.closest('.anj-nav-link') : null;
        if (link) {
            ev.preventDefault();
            aktifkan(link.getAttribute('data-seksi'), true);
            return;
        }
        var tool = ev.target.closest ? ev.target.closest('.anj-tool') : null;
        if (tool) {
            var grup = tool.parentNode.children;
            for (var i = 0; i < grup.length; i++) grup[i].classList.toggle('active', grup[i] === tool);
        }
    });

    window.addEventListener('popstate', function (ev) {
        var key = ev.state && ev.state.seksi ? ev.state.seksi : jbSeksiDariPath(location.pathname);
        aktifkan(key, false);
    });

    function jbSeksiDariPath(path) {
        var m = /^\/(infosekolah|mading|infosiswa|jadkal|pustaka|pegawai|psb)\/?$/.exec(path || '/');
        return m ? m[1] : 'beranda';
    }

    /* ---------- latar hero ---------- */

    function latar() {
        var daftar = window.b_bgList;
        var layer = document.getElementById('anjBg');
        if (!layer || !Array.isArray(daftar) || daftar.length === 0) return;
        var url = daftar.map(function (img) { return new URL(img, document.baseURI).href; });
        var i = 0;
        layer.style.backgroundImage = 'url("' + url[0] + '")';
        setInterval(function () {
            i = (i + 1) % url.length;
            layer.style.opacity = '0';
            setTimeout(function () {
                layer.style.backgroundImage = 'url("' + url[i] + '")';
                layer.style.opacity = '';
            }, 600);
        }, Math.max(8000, (window.b_bgDelay || 15000)));
    }

    /* ---------- modal Masuk (form login Informasi Siswa) ---------- */

    function tampilkanGalat(pesan) {
        var box = document.getElementById('anjMasukError');
        if (!box) return;
        box.textContent = pesan;
        box.hidden = false;
    }

    function sembunyikanGalat() {
        var box = document.getElementById('anjMasukError');
        if (box) { box.textContent = ''; box.hidden = true; }
    }

    function tutupModal() {
        var el = document.getElementById('anjMasukModal');
        if (el && window.bootstrap) window.bootstrap.Modal.getOrCreateInstance(el).hide();
    }

    /* Isi halaman Informasi Siswa diambil dari infosiswa.content.php, bukan infosiswa.php:
       file itu hanya merender form login (sesi siswa tidak dibuka di sana) dan include
       'infosiswa.main.php' milik warisan tidak ada di disk. */
    function bukaIsiSiswa(nis, tipe) {
        var el = kartu('infosiswa');
        if (!el) return;
        /* Halaman dibuka langsung pada satu laporan — tautan atau muat ulang di
           /infosiswa/#keuangan. Jenisnya ikut diminta ke potongan warisan lewat
           reporttype; memaksa PROFIL di sini membuat laporan itu hilang sebelum
           sidebar sempat dibangun. Tanpa combo di DOM, dataset ini satu-satunya yang
           tahu laporan yang sedang diminta, jadi samakanHash() membacanya. */
        var jenis = tipe || tipeDariHash() || 'PROFIL';
        el.dataset.anjTipe = jenis;
        el.dataset.src = 'infosiswa/infosiswa.content.php?nis=' + encodeURIComponent(nis)
            + '&reporttype=' + encodeURIComponent(jenis);
        el.dataset.anjTermuat = '';
        el.dataset.anjPulih = '';
        aktifkan('infosiswa', true);
    }

    /* Titik masuk login NISN/PIN ada di root (login-siswa.php); path relatif akan resolve ke
       /anjungan/ karena <base>, jadi selalu dipakai absolut. */
    var URL_LOGIN = '/login-siswa.php';

    /* Kabar singkat tanpa Bootstrap Toast: elemen melengkung lalu hilang sendiri. */
    function kabari(pesan) {
        var el = document.createElement('div');
        el.className = 'anj-kabari';
        el.setAttribute('role', 'status');
        el.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>';
        el.appendChild(document.createTextNode(pesan));
        document.body.appendChild(el);
        /* Jeda dulu supaya transisi punya titik awal; fallback timer karena requestAnimationFrame
           tidak berjalan di tab yang tidak digambar. */
        setTimeout(function () { el.classList.add('anj-kabari-tampil'); }, 20);
        setTimeout(function () {
            el.classList.remove('anj-kabari-tampil');
            setTimeout(function () { el.remove(); }, 400);
        }, 3800);
    }

    /* Keluar dari dasbor siswa ditutup di titik masuk login, lalu halaman kembali ke beranda.
       Alur logout warisan tidak dipakai: ia menggambar ulang form login lama ke dalam pane. */
    function keluarSiswa() {
        /* Konfirmasi memakai SweetAlert2 (pustaka + CSS-nya ikut dimuat bersama seksi Info
           Siswa): dialog bawaan browser tampil sebagai milik browser, tanpa tema halaman.
           allowOutsideClick ditutup — sama seperti aturan modal Bootstrap di proyek ini,
           dialog hanya lekat lewat tombolnya sendiri. */
        Swal.fire({
            icon: 'warning',
            title: 'Keluar dari Informasi Siswa?',
            text: 'Anda akan keluar dari halaman Informasi Siswa. Lanjutkan?',
            showCancelButton: true,
            confirmButtonText: 'Ya, keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            allowOutsideClick: false
        }).then(function (hasil) {
            if (hasil.isConfirmed) tutupSesiSiswa();
        });
    }

    function tutupSesiSiswa() {
        fetch(URL_LOGIN + '?aksi=keluar', { method: 'POST', credentials: 'same-origin' })
            .then(function (res) { return res.ok ? res.json() : null; })
            .then(function () {
                var el = kartu('infosiswa');
                if (el) {
                    el.removeAttribute('data-nis');
                    el.removeAttribute('data-nama');
                    el.removeAttribute('data-foto');
                    el.removeAttribute('data-anj-tipe');
                    el.dataset.anjTermuat = '';
                    /* Sesi yang baru ditutup tidak boleh langsung dipulihkan otomatis. */
                    el.dataset.anjPulih = '9';
                    var pane = el.querySelector('.anj-pane');
                    if (pane) pane.textContent = '';
                }
                /* Sidebar laporan bukan bagian kartu lagi, tapi dilepas oleh ubahTombolMasuk(false). */
                ubahTombolMasuk(false);
                aktifkan('beranda', true);
                kabari('Anda telah keluar dari Informasi Siswa.');
            })
            .catch(function () {
                kabari('Pengeluaran tidak dapat dikonfirmasi, silakan muat ulang halaman.');
            });
    }

    function kirimLogin(nisn, pin) {
        var tombol = document.getElementById('anjMasukKirim');
        if (tombol) tombol.disabled = true;
        var body = new URLSearchParams();
        body.append('nisn', nisn);
        body.append('pin', pin);
        fetch(URL_LOGIN, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8' },
            body: body.toString()
        })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                if (!data || !data.sukses) throw new Error((data && data.pesan) || 'NISN atau PIN salah.');
                tutupModal();
                ubahTombolMasuk(true, data.nis, data.nama, data.berfoto);
                bukaIsiSiswa(data.nis);
            })
            .catch(function (err) {
                tampilkanGalat(err && err.message ? err.message : 'Login gagal, silakan coba lagi.');
            })
            .then(function () {
                if (tombol) tombol.disabled = false;
            });
    }

    /* Tombol Masuk berubah wujud menjadi pintu ke dasbor selama sesi siswa masih ada;
       tanpa itu tidak ada jalan kembali ke Info Siswa karena menunya tidak di baris menu.
       Labelnya nama lengkap siswa, avatarnya fotonya — bila blob foto kosong, avatar
       menampilkan inisial nama depan dan nama kedua. */
    function ubahTombolMasuk(sudahLogin, nis, nama, berfoto) {
        var tombol = document.querySelector('.anj-masuk');
        if (!tombol) return;
        /* Sesi aktif = rel laporan dipasang (lagi) walau kartu Info Siswa tidak sedang tampil;
           sesi selesai = rel dibersihkan. Dibaca ulang di sini karena fungsi ini yang
           paling tahu keadaan sesi: dari cek status saat halaman dibuka, login, sampai Keluar. */
        sesiSiswaAktif = !!sudahLogin;
        if (sudahLogin) pastikanRelSiswa(); else kosongkanRel();
        var el = kartu('infosiswa');
        if (el && nama) el.dataset.nama = nama;
        if (el && berfoto !== undefined) el.dataset.foto = berfoto ? '1' : '0';
        var data = el ? el.dataset : {};
        var label = sudahLogin ? (data.nama || 'Info Siswa') : 'Masuk';
        var wujud = (sudahLogin ? 'S' : 'M') + '|' + label + '|' + (sudahLogin && data.foto === '1' ? 'foto' : 'inisial');
        if (tombol.dataset.anjWujud === wujud) return;
        tombol.dataset.anjWujud = wujud;
        if (sudahLogin) {
            tombol.removeAttribute('data-bs-toggle');
            tombol.removeAttribute('data-bs-target');
            tombol.setAttribute('data-seksi', 'infosiswa');
            if (nis && el) el.dataset.nis = nis;
            tombol.setAttribute('title', label);
        } else {
            tombol.removeAttribute('data-seksi');
            tombol.removeAttribute('title');
            tombol.setAttribute('data-bs-toggle', 'modal');
            tombol.setAttribute('data-bs-target', '#anjMasukModal');
        }
        tombol.textContent = '';
        tombol.appendChild(sudahLogin ? avatarSiswa(data.nama, data.foto === '1') : ikonBi('bi-box-arrow-in-right'));
        var s = document.createElement('span');
        s.className = 'anj-masuk-nama';
        s.textContent = label;
        tombol.appendChild(s);
    }

    function ikonBi(nama) {
        var i = document.createElement('i');
        i.className = 'bi ' + nama + ' me-1';
        i.setAttribute('aria-hidden', 'true');
        return i;
    }

    function avatarSiswa(nama, berfoto) {
        var b = document.createElement('span');
        b.className = 'anj-avatar';
        var inisial = document.createElement('span');
        inisial.className = 'anj-avatar-inisial';
        inisial.textContent = inisialNama(nama);
        b.appendChild(inisial);
        if (berfoto) {
            var img = document.createElement('img');
            img.alt = '';
            img.src = '/login-siswa.php?aksi=foto';
            /* Foto hilang di tengah jalan: inisial yang tampil, bukan kotak rusak. */
            img.onerror = function () { img.remove(); };
            b.appendChild(img);
        }
        return b;
    }

    function inisialNama(nama) {
        var kata = String(nama || '').trim().split(/\s+/).filter(function (k) { return k.length; });
        if (!kata.length) return '?';
        var huruf = function (k) { return Array.from(k)[0].toUpperCase(); };
        return (huruf(kata[0]) + (kata[1] ? huruf(kata[1]) : '')).slice(0, 2);
    }

    function cekSesiHalaman() {
        fetch('/login-siswa.php?aksi=status', { credentials: 'same-origin' })
            .then(function (res) { return res.ok ? res.json() : null; })
            .then(function (data) {
                if (data && data.login && data.nis) ubahTombolMasuk(true, data.nis, data.nama, data.berfoto);
            })
            .catch(function () { /* tanpa sesi: tombol tetap Masuk */ });
    }

    function initMasuk() {
        var form = document.getElementById('anjMasukForm');
        if (!form) return;
        form.addEventListener('submit', function (ev) {
            ev.preventDefault();
            sembunyikanGalat();
            var nisn = document.getElementById('anjIsNisn').value.trim();
            var pin = document.getElementById('anjIsPin').value.trim();
            if (nisn === '' || pin === '') { tampilkanGalat('NISN dan PIN harus diisi.'); return; }
            kirimLogin(nisn, pin);
        });
        /* Setiap modal dibuka, isinya dikosongkan lagi: nilai sisa dari browser atau percobaan
           sebelumnya tidak boleh tertinggal di kolom login. */
        document.querySelectorAll('[data-bs-target="#anjMasukModal"]').forEach(function (pemicu) {
            pemicu.addEventListener('click', function () {
                form.reset();
                sembunyikanGalat();
                setTimeout(fokusNisn, 120);
            });
        });
        /* Bootstrap memindahkan fokus ke elemen dialog saat transisi selesai; ambil alih lagi
           supaya kursor langsung di kolom NISN. */
        document.getElementById('anjMasukModal').addEventListener('shown.bs.modal', fokusNisn);
    }

    function fokusNisn() {
        var nisn = document.getElementById('anjIsNisn');
        if (nisn) nisn.focus();
    }

    function bukaModalMasuk() {
        var el = document.getElementById('anjMasukModal');
        var form = document.getElementById('anjMasukForm');
        if (form) form.reset();
        sembunyikanGalat();
        if (el && window.bootstrap) window.bootstrap.Modal.getOrCreateInstance(el).show();
        setTimeout(fokusNisn, 120);
    }

    /* ---------- dasbor Info Siswa: sidebar laporan menggantikan dropdown warisan ---------- */

    var IKON_LAPORAN = {
        PROFIL: 'bi-person-vcard',
        KEUANGAN: 'bi-cash-coin',
        PRESENSIHARIAN: 'bi-calendar-check',
        PRESENSIPELAJARAN: 'bi-journal-text',
        PRESENSIKEGIATAN: 'bi-clipboard2-check',
        NILAI: 'bi-graph-up',
        RAPOR: 'bi-file-earmark-text',
        PERPUSTAKAAN: 'bi-book',
        CBE: 'bi-display'
    };

    /* Alamat laporan: /infosiswa/#datasiswa, #keuangan, dst. Nilai combo warisan tetap
       sumber kebenaran isi laporan; hash hanya membuat tiap laporan bisa ditauti. */
    var SLUG_LAPORAN = {
        PROFIL: 'datasiswa',
        KEUANGAN: 'keuangan',
        PRESENSIHARIAN: 'presensiharian',
        PRESENSIPELAJARAN: 'presensipelajaran',
        PRESENSIKEGIATAN: 'presensikegiatan',
        NILAI: 'nilai',
        RAPOR: 'rapor',
        PERPUSTAKAAN: 'perpustakaan',
        CBE: 'cbe'
    };

    var TIPE_DARI_SLUG = {};
    Object.keys(SLUG_LAPORAN).forEach(function (tipe) {
        TIPE_DARI_SLUG[SLUG_LAPORAN[tipe]] = tipe;
    });

    /* Label sidebar tidak menunggu combo warisan dimuat: rel bisa lebih dulu tampil di
       seksi lain. Isinya sama dengan ShowReportComboBox() di
       anjungan/infosiswa/infosiswa.class.php, urutan diambil dari SLUG_LAPORAN. */
    var LABEL_LAPORAN = {
        PROFIL: 'Data Pribadi',
        KEUANGAN: 'Keuangan',
        PRESENSIHARIAN: 'Presensi Harian',
        PRESENSIPELAJARAN: 'Presensi Pelajaran',
        PRESENSIKEGIATAN: 'Presensi Kegiatan',
        NILAI: 'Nilai',
        RAPOR: 'Rapor',
        PERPUSTAKAAN: 'Perpustakaan',
        CBE: 'Computer Based Exam'
    };

    function tipeDariHash() {
        return TIPE_DARI_SLUG[location.hash.replace(/^#/, '')] || null;
    }

    function comboLaporan(main) {
        return main ? main.querySelector('#reporttype') : null;
    }

    /* Dropdown "Laporan :" serta tombol LOGOUT/REFRESH warisan digantikan sidebar:
       elemennya tetap ada (seluruh navigasi warisan membacanya), hanya disembunyikan. */
    function sembunyikanKontrolLama(main) {
        var combo = comboLaporan(main);
        if (!combo) return;
        var teks = combo.previousSibling;
        if (teks && teks.nodeType === 3 && /laporan/i.test(teks.nodeValue)) teks.nodeValue = '';
        combo.style.display = 'none';
        [].slice.call(main.querySelectorAll('input.but')).forEach(function (tombol) {
            tombol.style.display = 'none';
        });
    }

    function tandaiAktif(nav, tipe) {
        [].slice.call(nav.querySelectorAll('.anj-sb-item[data-tipe]')).forEach(function (b) {
            var aktif = b.dataset.tipe === tipe;
            b.classList.toggle('active', aktif);
            if (aktif) {
                b.setAttribute('aria-current', 'page');
            } else {
                b.removeAttribute('aria-current');
            }
        });
    }

    function item(nav, kelas, tip, ikon, label) {
        var b = document.createElement(tip ? 'a' : 'button');
        if (!tip) b.type = 'button';
        b.className = 'anj-sb-item ' + kelas;
        if (tip) {
            b.dataset.tipe = tip;
            b.href = '#' + (SLUG_LAPORAN[tip] || tip.toLowerCase());
        }
        var i = document.createElement('i');
        i.className = 'bi ' + ikon;
        i.setAttribute('aria-hidden', 'true');
        var s = document.createElement('span');
        s.textContent = label;
        b.appendChild(i);
        b.appendChild(s);
        nav.appendChild(b);
        return b;
    }

    function bangunSidebar() {
        var nav = document.createElement('nav');
        nav.className = 'anj-sb';
        nav.setAttribute('aria-label', 'Laporan siswa');
        var judul = document.createElement('p');
        judul.className = 'anj-sb-title';
        judul.textContent = 'Laporan';
        nav.appendChild(judul);
        Object.keys(SLUG_LAPORAN).forEach(function (tipe) {
            item(nav, '', tipe, IKON_LAPORAN[tipe] || 'bi-dot', LABEL_LAPORAN[tipe] || tipe);
        });
        var pemisah = document.createElement('div');
        pemisah.className = 'anj-sb-sep';
        nav.appendChild(pemisah);
        item(nav, 'anj-sb-muat', null, 'bi-arrow-clockwise', 'Muat ulang');
        item(nav, 'anj-sb-keluar', null, 'bi-box-arrow-right', 'Keluar');
        nav.addEventListener('click', function (ev) {
            var b = ev.target.closest ? ev.target.closest('.anj-sb-item') : null;
            if (!b) return;
            ev.preventDefault();
            if (b.classList.contains('anj-sb-keluar')) {
                keluarSiswa();
                return;
            }
            if (b.classList.contains('anj-sb-muat')) {
                muatUlangSeksi();
                return;
            }
            bukaLaporan(b.dataset.tipe);
        });
        return nav;
    }

    function jalankanLaporan(main, tipe) {
        var combo = comboLaporan(main);
        if (!combo || !tipe) return;
        var nav = rel() ? rel().querySelector('.anj-sb') : null;
        if (nav) tandaiAktif(nav, tipe);
        combo.value = tipe;
        combo.dispatchEvent(new Event('change'));
    }

    function pilihLaporan(main, tipe) {
        var combo = comboLaporan(main);
        if (!combo || !tipe) return;
        var slug = SLUG_LAPORAN[tipe];
        if (slug && location.hash !== '#' + slug) {
            /* Ganti alamat dulu; hashchange yang memanggil laporan yang diminta. */
            location.hash = slug;
            return;
        }
        jalankanLaporan(main, tipe);
    }

    /* Laporan dari sidebar bisa diminta dari seksi lain, saat kartu Info Siswa belum
       pernah dimuat. Jenis laporan dibawa sebagai argumen, bukan lewat hash: pushState
       saat mengaktifkan seksi membuang hash, dan alamatnya disamakan lagi sesudah itu. */
    function bukaLaporan(tipe) {
        var el = kartu('infosiswa');
        if (!el || !tipe) return;
        var main = el.querySelector('#is_main');
        var combo = comboLaporan(main);
        if (!combo && !el.dataset.nis) {
            bukaModalMasuk();
            return;
        }
        if (combo) {
            /* Laporan hanya bisa tampil di kartunya sendiri. */
            if (el.classList.contains('d-none')) aktifkan('infosiswa', true);
            pilihLaporan(main, tipe);
            return;
        }
        bukaIsiSiswa(el.dataset.nis, tipe);
    }

    /* "Muat ulang" mengikuti seksi yang sedang tampil: di dasbor ia mengulang laporan yang
       ada, di seksi lain ia mengambil ulang potongan halamannya. */
    function muatUlangSeksi() {
        var el = document.querySelector('.anj-card:not(.d-none)');
        if (!el) return;
        var main = el.querySelector('#is_main');
        var combo = comboLaporan(main);
        if (combo) {
            jalankanLaporan(main, combo.value);
            return;
        }
        el.dataset.anjTermuat = '';
        muatSeksi(el.getAttribute('data-seksi'));
    }

    /* Tautan /infosiswa/#keuangan dan tombol mundur/ maju browser. */
    function bukaLaporanDariHash() {
        var tipe = tipeDariHash();
        var el = tipe ? kartu('infosiswa') : null;
        var main = el ? el.querySelector('#is_main') : null;
        var combo = comboLaporan(main);
        if (!combo) return;
        if (combo.value === tipe) {
            var nav = rel() ? rel().querySelector('.anj-sb') : null;
            if (nav) tandaiAktif(nav, tipe);
            return;
        }
        jalankanLaporan(main, tipe);
    }

    window.addEventListener('hashchange', bukaLaporanDariHash);

    /* Alamat selalu mengikuti laporan yang tampil, termasuk setelah muat ulang di URL
       tanpa hash dan saat kembali ke seksi ini lewat tombol nama. replaceState dipakai
       supaya sinkronisasi otomatis ini tidak menambah riwayat; URL ditulis absolut
       karena <base href="/anjungan/"> menimpa resolusi URL relatif. */
    function samakanHash() {
        var el = kartu('infosiswa');
        if (!el || el.classList.contains('d-none')) return;
        var combo = comboLaporan(el.querySelector('#is_main'));
        var tipe = combo ? combo.value : el.dataset.anjTipe;
        var slug = tipe && SLUG_LAPORAN[tipe];
        if (!slug || location.hash === '#' + slug) return;
        history.replaceState({ seksi: 'infosiswa' }, '', location.pathname + '#' + slug);
    }

    function pasangDasborSiswa(el) {
        var pane = el.querySelector('.anj-pane');
        var main = el.querySelector('#is_main');
        var r = rel();
        var nav = r ? r.querySelector('.anj-sb') : null;
        var baruDibangun = false;
        var combo = main ? comboLaporan(main) : null;

        if (!combo) {
            /* Bukan laporan lagi: form login lama warisan (pertama kali dibuka) atau hasil
               Keluar. Isi kartu dibersihkan lalu sesi ditanyakan ke titik masuk login.
               Rel hanya dibongkar bila sesi memang tidak aktif — selama sesi hidup sidebar
               laporan tetap dipakai dari seksi mana pun. */
            if (!sesiSiswaAktif) kosongkanRel();
            var sudahCoba = parseInt(el.dataset.anjPulih || '0', 10);
            if (sudahCoba < 2) {
                pane.textContent = '';
                pulihkanSesiSiswa(el, sudahCoba);
            }
            return;
        }

        if (!nav) {
            /* Sidebar biasanya sudah dibangun saat sesi diketahui aktif (rel ikut tampil di
               seksi lain); dibangun di sini hanya bila belum ada. */
            nav = bangunSidebar();
            if (r) r.appendChild(nav);
            baruDibangun = true;
        }
        /* Laporan warisan menulis ulang isi #is_main tiap ganti jenis: pengamat ini membuat
           sidebar tersinkron (item aktif + kontrol lama tersembunyi) lagi. Pengamat ikut
           dipindah bila warisan mengganti simpul #is_main, supaya tidak menempel pada
           simpul yang sudah lepas dari DOM. */
        if (main && el._anjMain !== main) {
            el._anjMain = main;
            if (!el._anjPengamat) {
                el._anjPengamat = new MutationObserver(function () { pasangDasborSiswa(el); });
            } else {
                el._anjPengamat.disconnect();
            }
            el._anjPengamat.observe(main, { childList: true, subtree: true });
        }
        tampilRel(true);

        sembunyikanKontrolLama(main);
        tandaiAktif(nav, combo.value);
        ubahTombolMasuk(true);
        /* Halaman dibuka langsung pada satu laporan: /infosiswa/#rapor. Alamat hanya
           disamakan saat sidebar baru dibangun — di tengah muat laporan warisan combo-nya
           sempat berisi nilai awal lagi, jadi menulis hash di sana akan membatalkan
           laporan yang baru saja dipilih. */
        if (baruDibangun) {
            bukaLaporanDariHash();
            samakanHash();
        }
    }

    function pulihkanSesiSiswa(el, percobaan) {
        el.dataset.anjPulih = String(percobaan + 1);
        var pane = el.querySelector('.anj-pane');
        fetch('/login-siswa.php?aksi=status', { credentials: 'same-origin' })
            .then(function (res) { return res.ok ? res.json() : null; })
            .then(function (data) {
                if (data && data.login && data.nis) {
                    ubahTombolMasuk(true, data.nis, data.nama, data.berfoto);
                    bukaIsiSiswa(data.nis);
                    return;
                }
                ubahTombolMasuk(false);
                var kosong = document.createElement('div');
                kosong.className = 'anj-kosong';
                var i = document.createElement('i');
                i.className = 'bi bi-person-lock me-2';
                i.setAttribute('aria-hidden', 'true');
                kosong.appendChild(i);
                kosong.appendChild(document.createTextNode('Masuk dulu untuk melihat informasi siswa.'));
                pane.appendChild(kosong);
                bukaModalMasuk();
            })
            .catch(function () {
                var gagal = document.createElement('div');
                gagal.className = 'anj-kosong';
                gagal.textContent = 'Informasi siswa tidak dapat dibuka saat ini.';
                pane.appendChild(gagal);
            });
    }


    /* ---------- iframe bertema ---------- */

    function sediaTemaIframeUlang() {
        var fase = root.getAttribute('data-jibas-phase');
        document.querySelectorAll('.anj-pane iframe').forEach(function (f) {
            try {
                if (f.contentDocument && f.contentDocument.documentElement) {
                    f.contentDocument.documentElement.setAttribute('data-jibas-phase', fase);
                    f.contentDocument.documentElement.setAttribute('data-bs-theme', root.getAttribute('data-bs-theme'));
                }
            } catch (e) { /* dokumen lintas sumber */ }
        });
    }

    /* iframe warisan (mading / info sekolah) dokumen terpisah: ikutkan fase tema ke dalamnya */
    new MutationObserver(sediaTemaIframeUlang)
        .observe(root, { attributes: true, attributeFilter: ['data-jibas-phase', 'data-bs-theme'] });

    /* Kabar versi baru service worker sengaja TIDAK dipasang di anjungan: halaman ini
       dipakai siswa tanpa login, pembaruan teknis tidak perlu mereka lihat.
       Toast "versi baru" hanya ada di shell akademik — lihat script/shell.js. */

    function mulai() {
        var aktif = document.body.dataset.anjActive || 'beranda';
        ukurKepala(); pantauGulir();
        window.addEventListener('load', ukurKepala, { passive: true });
        initMasuk(); latar(); aktifkan(aktif, false);
        /* Kartu Info Siswa sudah menanyakan sesi sendiri saat dipulihkan. */
        if (aktif !== 'infosiswa') cekSesiHalaman();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mulai);
    } else {
        mulai();
    }
})();
